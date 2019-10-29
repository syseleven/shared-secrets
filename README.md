# Shared-Secrets

Shared-Secrets is an application that helps you to simply share one-time secrets over the web. Typically when you do not have the possibility to open an encrypted communication channel (e.g. GPG-encrypted mail) to transfer a sensitive information you have to resort to unencrypted means of communication - e.g. SMS, unencrypted e-mail, telephone, etc.

Using the Shared-Secrets service allows you to transfer the actual secret in an encrypted form. Retrieving the secret is as simple as following a link. In contrast to other secret sharing services, Shared-Secrets does not store the secret on the server, but puts the encrypted secret into the link that you share with the desired recipient. That means that the compromise of a Shared-Secrets server does not automatically compromise all of the shared secrets.

Secrets can only be retrieved once. Further retrievals are rejected by matching the encrypted secret against the fingerprints of the secrets that have been retrieved before. By disallowing repeating retrievals of a secret, it is at least possible to detect when the confidentiality of a secret sharing link has been compromised.

To protect your secret from getting known by the server or an attacker, you can additionally protect the secret with a password before sharing it. The secret will be encrypted and decrypted locally without an interaction with the server. You can provide the chosen password to the recipient through a second communication channel to prevent an attacker that is able to control one communication channel from compromising the confidentiallity of your secret.

## Usage

### Share a Secret

Simply enter your secret on the default page of the Shared-Secrets service. You can decide to password-protect the entered secret before sending it to the server by checking the "Password-protected:" box, entering your password and pressing the "Protect!" button. After that, press the "Share the Secret!" button. The secret will be encrypted and converted into a secret sharing link.

Secret sharing links can also be created by using a simple POST request:
```
curl -X POST -d "plain&secret=<secret>" https://example.com/
```

### Read a Secret

To retrieve the secret, simply open the secret sharing link and press the "Read the Secret!" button. Should your secret be password-protected, check the "Password-protected:" box, enter your password and read your actual secret by pressing the "Unprotect!" button.

Secrets can also be retrieved using a simple POST request:

```
curl -X POST -d "plain" <secret sharing link>
```

## Installation

### Requirements

Shared-Secrets is based on MariaDB 10.0, Nginx 1.10 and PHP 7.0, but should also work with MySQL, Apache and earlier versions of PHP. Encrypted is done via the OpenSSL integration of PHP.

### Nginx Setup

Shared-Secrets uses a single entry point to control the dataflow. Therefore the following rewrite rule is required (Nginx example):
```
if (!-f $request_filename) {
  rewrite ^.*$ /index.php last;
}
```

Shared-Secrets is designed to yield an A+ rating at the [Mozilla Observatory](https://observatory.mozilla.org) website check. Releases are checked against the Mozilla Observatory to make sure that a good rating can be achieved.

To achieve an A+ rating with your instance, you have to implement TLS and non-TLS calls have to be redirected to the TLS-protected website (Nginx example):
```
server {
  listen 80 default_server;
  listen [::]:80 default_server;

  server_name _;

  return 301 https://$host$request_uri;
}

server {
  listen 443 ssl http2 default_server;
  listen [::]:443 ssl http2 default_server;

  # prevent access to certain locations
  location ~ ^\/\.git(\/.*)?$    { return 404; }
  location ~ ^\/\.gitattributes$ { return 404; }
  location ~ ^\/\.gitignore$     { return 404; }
  location ~ ^\/\.htaccess$      { return 404; }
  location ~ ^\/actions(\/.*)?$  { return 404; }
  location ~ ^\/CHANGELOG\.md$   { return 404; }
  location ~ ^\/config(\/.*)?$   { return 404; }
  location ~ ^\/ENCRYPTION\.md$  { return 404; }
  location ~ ^\/lib(\/.*)?$      { return 404; }
  location ~ ^\/LICENSE$         { return 404; }
  location ~ ^\/pages(\/.*)?$    { return 404; }
  location ~ ^\/README\.md$      { return 404; }
  location ~ ^\/template(\/.*)?$ { return 404; }

  # Your configuration comes here:
  # ...
}
```

Furthermore the following HTTP headers have to be set (Nginx example):
```
add_header Content-Security-Policy   "base-uri 'self'; default-src 'self'; form-action 'self'; frame-ancestors 'self'; require-sri-for script style";
add_header Referrer-Policy           "same-origin";
add_header Strict-Transport-Security "max-age=15768000; includeSubDomains; preload";
add_header X-Content-Security-Policy "base-uri 'self'; default-src 'self'; form-action 'self'; frame-ancestors 'self'; require-sri-for script style";
add_header X-Content-Type-Options    "nosniff";
add_header X-Frame-Options           "SAMEORIGIN";
add_header X-Webkit-CSP              "base-uri 'self'; default-src 'self'; form-action 'self'; frame-ancestors 'self'; require-sri-for script style";
add_header X-XSS-Protection          "1; mode=block";
```

### MariaDB Setup

Shared-Secrets uses a single-table database to store who did retrieve which secret at what point in time. No actual secret content is stored. (The logging of IP addresses is disabled through the configuration parameter LOG_IP_ADDRESS by default.):
```
CREATE TABLE secrets ( fingerprint VARCHAR(64) PRIMARY KEY, time TIMESTAMP );
```

### Encryption Setup

You should generate a fresh RSA key pair with a minimum key size of 2048 bits:

```
openssl genrsa -out ./rsa.key 2048
```

**Beware:** You should place this file in a location so that it is not accessible through the webserver.

### Service Setup

Rename the "config.php.default" to "config.php" and set the necessary configuration items.

### TLS Recommendation

It is strongly recommended to use TLS to protect the connection between the server and the clients.

## Attributions

* [Bootstrap](https://getbootstrap.com): for providing an easy-to-use framework to build nice-looking applications
* [html5shiv](https://github.com/aFarkas/html5shiv): for handling Internet Explorer compatibility stuff
* [jQuery](https://jquery.com): for just existing
* [Katharina Franz](https://www.katharinafranz.com): for suggesting Bootstrap as an easy-to-use framework to build nice-looking applications
* [Respond.js](https://github.com/scottjehl/Respond): for handling even more Internet Explorer compatibility stuff

## ToDo

* switch to a more personalized design (current design is taken from [here](https://github.com/twbs/bootstrap/tree/master/docs/examples/starter-template))
* implement an expiry date functionality

## License

This application is released under the BSD license. See the [LICENSE](LICENSE) file for further information.
