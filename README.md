# Shared-Secrets

Shared-Secrets is an application that helps you to simply share one-time secrets over the web. Typically when you do not have the possibility to open an encrypted communication channel (e.g. GPG-encrypted mail) to transfer a sensitive information you have to resort to unencrypted means of communication - e.g. SMS, unencrypted e-mail, telephone, etc.

Using the Shared-Secrets service allows you to transfer the actual secret in an encrypted form. Retrieving the secret is as simple as following a link. In contrast to other secret sharing services, Shared-Secrets does not store the secret on the server, but puts the encrypted secret into the link that you share with the desired recipient. That means that the compromise of a Shared-Secrets server does not automatically compromise all of the shared secrets.

Secrets can only be retrieved once. Further retrievals are rejected by matching the encrypted secret against the fingerprints of the secrets that have been retrieved before. By disallowing repeating retrievals of a secret, it is at least possible to detect when the confidentiality of a secret sharing link has been compromised.

To protect your secret from getting known by the server or an attacker, you can additionally protect the secret with a password before sharing it. The secret will be encrypted and decrypted locally without an interaction with the server. You can provide the chosen password to the recipient through a second communication channel to prevent an attacker that is able to control one communication channel from compromising the confidentiallity of your secret.

## Usage

### Share a Secret

Simply enter your secret on the default page of the Shared-Secrets service. You can decide to password-protect the entered secret before sending it to the server by checking the "Password-protected:" box, entering your password and pressing the "Protect!" button. After that, press the "Share the Secret!" button. The secret will be encrypted and converted into a secret sharing link. In cases where you need the plain secret sharing link to be returned by the web  page you can append the GET parameter `?plain` to the URL of the default page.

Secret sharing links can also be created by using a simple POST request:

```
curl -X POST -d "plain&secret=<secret>" https://example.com/

# OR #

curl -X POST -d "secret=<secret>" https://example.com/?plain
```

### Read a Secret

To retrieve the secret, simply open the secret sharing link and press the "Read the Secret!" button. Should your secret be password-protected, check the "Password-protected:" box, enter your password and read your actual secret by pressing the "Unprotect!" button. In cases where you need the plain secret to be returned by the web page you can append the GET parameter `?plain` to the secret sharing link **but be aware** that returning the plain secret does not support the Browser-based decryption.

Secrets can also be retrieved using a simple POST request:

```
curl -X POST -d "plain" <secret-sharing-link>

# OR #

curl -X POST <secret-sharing-link>?plain
```

### Download the Public Key

To download the public key of a Shared-Secrets instance in order to manually generate secret sharing links, simply visit the `/pub` page. In cases where you need the plain public key to be returned by the web page you can append the GET parameter `?plain` to the URL.

The public key can also be downloaded using a simple GET request:

```
curl -X GET https://example.com/pub?plain
```

## Installation

### Requirements

Shared-Secrets is based on MariaDB 10.0, Nginx 1.10 and PHP 7.0, but should also work with MySQL and Apache. Encryption is done via the OpenSSL integration of PHP.

### Nginx Setup

Shared-Secrets is designed to yield an A+ rating at the [Mozilla Observatory](https://observatory.mozilla.org) website check. Releases are checked against the Mozilla Observatory to make sure that a good rating can be achieved.

To achieve an A+ rating with your instance, you have to implement TLS and non-TLS calls have to be redirected to the TLS-protected website. You also have to set some security headers. Furthermore, Shared-Secrets uses a single entry point to control the dataflow. See this NGINX configuration as an example:

```
server {
  listen      80 default_server;
  listen [::]:80 default_server;

  # has to be changed to your domain
  server_name example.com;

  return 301 https://$host$request_uri;
}

server {
  listen      443 ssl http2 default_server;
  listen [::]:443 ssl http2 default_server;

  # has to be changed to your domain
  server_name example.com;

  # has to be changed to your certificate files
  ssl_certificate     /etc/letsencrypt/live/example.com/fullchain.pem;
  ssl_certificate_key /etc/letsencrypt/live/example.com/privkey.pem;
  
  # generate your own dhparam to protect against WeakDH attack:
  # > openssl dhparam -out dhparam.pem 2048
  ssl_dhparam /etc/ssl/certs/dhparam.pem;

  # default locations
  root  /var/www/html;
  index index.html index.htm index.php;

  ssl_ciphers               "ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-SHA384:ECDHE-RSA-AES256-SHA384:ECDHE-ECDSA-AES256-SHA:ECDHE-RSA-AES256-SHA:AES128-SHA";
  ssl_ecdh_curve            secp384r1;
  ssl_prefer_server_ciphers on;
  ssl_protocols             TLSv1.2;
  ssl_session_cache         shared:SSL:10m;
  ssl_session_tickets       off;
  ssl_stapling              on;
  ssl_stapling_verify       on;

  resolver         8.8.8.8 8.8.4.4 valid=300s;
  resolver_timeout 5s;

  # set security headers
  add_header Content-Security-Policy   "base-uri 'self'; default-src 'self'; form-action 'self'; frame-ancestors 'self'; require-sri-for script style";
  add_header Referrer-Policy           "same-origin";
  add_header Strict-Transport-Security "max-age=15768000; includeSubDomains; preload";
  add_header X-Content-Security-Policy "base-uri 'self'; default-src 'self'; form-action 'self'; frame-ancestors 'self'; require-sri-for script style";
  add_header X-Content-Type-Options    "nosniff";
  add_header X-Frame-Options           "SAMEORIGIN";
  add_header X-Webkit-CSP              "base-uri 'self'; default-src 'self'; form-action 'self'; frame-ancestors 'self'; require-sri-for script style";
  add_header X-XSS-Protection          "1; mode=block";

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
  location ~ ^\/router\.php$     { return 404; }
  location ~ ^\/template(\/.*)?$ { return 404; }

  # Your configuration comes here:
  # ...

  # single entrypoint
  location / {
    try_files $uri $uri/ /index.php?$query_string;
  }

  # example PHP-FPM usage
  location ~ \.php$ {
    include      snippets/fastcgi-php.conf;
    fastcgi_pass unix:/run/php/php7.0-fpm.sock;
  }
}
```

### MariaDB Setup

Shared-Secrets uses a single-table database to store which secret has been retrieved at what point in time. No actual secret content is stored:

```
CREATE DATABASE secrets CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE secrets;

CREATE TABLE secrets (
  fingerprint VARCHAR(64) PRIMARY KEY,
  time       TIMESTAMP
);

GRANT ALL ON secrets.* TO 'secrets'@'%'         IDENTIFIED BY '5TR0NGP455W0RD!';
GRANT ALL ON secrets.* TO 'secrets'@'localhost' IDENTIFIED BY '5TR0NGP455W0RD!';
GRANT ALL ON secrets.* TO 'secrets'@'127.0.0.1' IDENTIFIED BY '5TR0NGP455W0RD!';
GRANT ALL ON secrets.* TO 'secrets'@'::1'       IDENTIFIED BY '5TR0NGP455W0RD!';

FLUSH PRIVILEGES;

EXIT;
```

### Encryption Setup

You should generate a fresh RSA key pair with a minimum key size of 2048 bits:

```
openssl genrsa -out ./rsa.key 2048
```

**Beware:** You should place this file in a location so that it is not accessible through the webserver. The recommended protection is to directly insert the RSA private keys as strings into the `RSA_PRIVATE_KEYS` array within `config/config.php`. 

### Service Setup

Copy the `config/config.php.default` file to `config/config.php` and set the necessary configuration items.

### Read-Only and Share-Only Instances

The configuration allows you to set your instances into read-only and/or share-only mode. This can be useful if want to use a private **share-only** instance or custom software to create secret sharing sharing links but provide a public **read-only** instance to retrieve the generated secret sharing links. There are two more things to consider:

* A **share-only** instance does not need access to the RSA private key as it will not decrypt secret sharing links. Therefore, it is possible to configure the RSA public key of the corresponding **read-only** instance into the `RSA_PRIVATE_KEYS` array of a **share-only** instance.
* The basis for the creation of secret sharing link is the `SECRET_SHARING_URL` configuration value. In order for a **share-only** instance to generate correct secret sharing links you have to set the URL of the corresponding **read-only** instance as the `SECRET_SHARING_URL` configuration value of the **share-only** instance.

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
