# Shared-Secrets

Shared-Secrets is an application that helps you to simply share one-time secrets over the web. Typically when you do not have the possibility to open an encrypted communication channel (e.g. GPG-encrypted mail) to transfer sensitive information you have to resort to unencrypted means of communication - e.g. SMS, unencrypted e-mail, telephone, etc.

Using the Shared-Secrets service allows you to transfer the actual secret in an encrypted form. Retrieving the secret is as simple as following a link. In contrast to other secret sharing services, Shared-Secrets does not store the secret on the server, but puts the encrypted secret into the link that you share with the desired recipient. That means that the compromise of a Shared-Secrets server does not automatically compromise all of the shared secrets.

Secrets can only be retrieved once. Further retrievals are rejected by matching the encrypted secret against the fingerprints of the secrets that have been retrieved before. By disallowing repeated retrievals of a secret, it is at least possible to detect when the confidentiality of a secret sharing link has been compromised.

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

To retrieve the secret, simply open the secret sharing link and press the "Read the Secret!" button. Should your secret be password-protected, check the "Password-protected:" box, enter your password and read your actual secret by pressing the "Unprotect!" button. In cases where you need the plain secret to be returned by the web page you can append the GET parameter `?plain` to the secret sharing link **but be aware** that returning the plain secret does not support the browser-based decryption.

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

  # do not write logs
  access_log off;
  error_log  /dev/null emerg;

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
  add_header Permissions-Policy        "interest-cohort=()";
  add_header Referrer-Policy           "same-origin";
  add_header Strict-Transport-Security "max-age=15768000; includeSubDomains; preload";
  add_header X-Content-Security-Policy "base-uri 'self'; default-src 'self'; form-action 'self'; frame-ancestors 'self'; require-sri-for script style";
  add_header X-Content-Type-Options    "nosniff";
  add_header X-Frame-Options           "SAMEORIGIN";
  add_header X-Webkit-CSP              "base-uri 'self'; default-src 'self'; form-action 'self'; frame-ancestors 'self'; require-sri-for script style";
  add_header X-XSS-Protection          "1; mode=block";

  # prevent access to certain locations
  location ~ ^\/\.env$           { return 404; }
  location ~ ^\/\.env\.default$  { return 404; }
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
  keyid       VARCHAR(64),
  fingerprint VARCHAR(64),
  time        TIMESTAMP,
  PRIMARY KEY (keyid, fingerprint)
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

**Beware:** You should place this file in a location so that it is not accessible through the webserver. The recommended protection is to directly insert the RSA private keys as strings into the `RSA_PRIVATE_KEYS` array within the configuration file.

### Service Setup

#### Configuration via config.php

Copy the `config/config.php.default` file to `config/config.php` and set the necessary configuration values. When a `config/config.php` file exists then it is used as the **only** configuration source for the entire Shared-Secrets instance.

#### Configuration via .env

Copy the `.env.default` file to `.env` and set the necessary configuration values. When a `config/config.php` file exists then the configuration values in the `.env` file will **not** be used. Configuration values in the `.env` file can be overwritten by setting environment variables.

#### Configuration via environment variables

Configuration values can also be set by defining corresponding environment variables. When a `config/config.php` file exists then the configuration values set via environment variables will **not** be used. Configuration values in the `.env` file can be overwritten by setting environment variables.

### Read-Only and Share-Only Instances

The configuration allows you to set your instances into read-only and/or share-only mode. This can be useful if you want to use a private **share-only** instance or custom software to create secret sharing sharing links but provide a public **read-only** instance to retrieve the generated secret sharing links. There are two more things to consider:

* A **share-only** instance does not need access to the RSA private key as it will not decrypt secret sharing links. Therefore, it is possible to configure the RSA public key of the corresponding **read-only** instance into the `RSA_PRIVATE_KEYS` array of a **share-only** instance.
* The basis for the creation of secret sharing link is the `SECRET_SHARING_URL` configuration value. In order for a **share-only** instance to generate correct secret sharing links you have to set the URL of the corresponding **read-only** instance as the `SECRET_SHARING_URL` configuration value of the **share-only** instance.

### TLS Recommendation

It is strongly recommended to use TLS to protect the connection between the server and the clients.

## Maintenance

### Database Backup

It is essential for Shared-Secrets to know which secrets have already been retrieved in order to implement the read-once functionality. Therefore, you should regularly backup your database to prevent messages from being read more than once. A command to create a backup of all databases may look like this:

```
sudo mysqldump --all-databases --result-file="./backup_$(date +'%Y%m%d').sql"
```

**Hint:** To recover from a loss of your database it is important to change the used key pair. Make sure to **not** use the [key rollover](#key-rollover) feature to prevent old secrets from being retrieved more than once.

### Database Optimization

While Shared-Secrets is designed to store a minimal amount of data (keyid, fingerprint of the retrieved message, timestamp) it might become necessary to clean-up the database when a lot of secrets have been retrieved. One approach is as follows:

* Use the [key rollover](#key-rollover) feature to add a new key that is used for all newly shared secrets. (_Users will still be able to retrieve old secrets._)
* Provide a grace period where secrets for the old **and** new key can be retrieved.
* Remove the old key from the list of valid keys. (_Users will **not** be able to retrieve old secrets anymore._)
* Delete the database entries of messages that belong to the old key.

The following commands can be used to delete the database entries of messages that belong to the old key:

```
USE secrets;

DELETE FROM secrets WHERE keyid = "<keyid of the old key>";

OPTIMIZE TABLE secrets;

EXIT;
```

### Key Rollover

Shared-Secrets supports key rollovers in the configuration and in the database. Key rollovers can be useful when you want to switch from an old key to a new key without service interruptions. They allow you to introduce a new key for sharing secrets while still allowing users to retrieve secrets of old keys.

To execute a key rollover you can add more than one RSA private key in the `RSA_PRIVATE_KEYS` configuration value, which happens to be an array. The last element in the array is the new key that is used to create new secret sharing links while all configured keys are used when trying to retrieve secrets. If you do not want to allow the retrieval of secrets created for old keys then you have to remove these specific keys from the `RSA_PRIVATE_KEYS` configuration value.

Therefore, the `RSA_PRIVATE_KEYS` configuration value can look like this:

```
define("RSA_PRIVATE_KEYS", ["-----BEGIN RSA PRIVATE KEY-----\n".
                            "...\n".
                            "...\n".
                            "...\n".
                            "-----END RSA PRIVATE KEY-----",
                            "-----BEGIN RSA PRIVATE KEY-----\n".
                            "...\n".
                            "...\n".
                            "...\n".
                            "-----END RSA PRIVATE KEY-----"]);
```

**Hint:** Key rollovers can be helpful when your database grows too big and needs [to be optimized](#database-optimization).

## Limitations

Using Shared-Secrets is **not** a 100% solution to achieve a perfectly secure communication channel, but it can be an improvement in situations where no better communication channel is available. You should always consider to switch to more secure channels like authenticated e-mail encryption (using GnuPG or S/MIME) or end-to-end encrypted instant messengers (like Signal or Threema).

### Storage Compromise

An attacker gaining access to storage containing secret sharing links could read the stored secret sharing links and try to retrieve the secrets. If properly implemented and used then Shared-Secrets can protect against such an attacker in the following ways:

1. From the secret sharing link itself the attacker will not learn about the contents of the actual secret.
2. When the secret has already been retrieved then the attacker will not be able to retrieve the secret again using the same secret sharing link as Shared-Secrets prevents secrets from being retrieved more than once.
3. When the secret has not already been retrieved and the attacker retrieved the secret instead, then you will be able to notice the attack by not being able to retrieve the secret yourself. Furthermore, the database will contain the information when the secret has been retrieved, providing the possibility to find out when the compromise took place.

### Passive Man-in-the-Middle Attack

A passive man-in-the-middle attacker could read the transmitted secret sharing links and try to retrieve the secrets. If properly implemented and used then Shared-Secrets can protect against such an attacker in the following ways:

1. From the secret sharing link itself the attacker will not learn about the contents of the actual secret.
2. When the secret is retrieved by the attacker, then you will be able to notice the attack by not being able to retrieve the secret yourself. Furthermore, the database will contain the information when the secret has been retrieved, providing the possibility to find out when the compromise took place.

### Active Man-in-the-Middle Attack (Scenario A)

An active man-in-the-middle attacker could change the transmitted secret sharing links in a way that they point to a malicious server that acts as a proxy between you and the actual Shared-Secrets server. By calling the modified secret sharing links you would provide the URLs to the malicious server which would then transparently direct the requests to the actual Shared-Secrets server and return the retrieved secrets while also storing them for the attacker. In such a scenario you would not easily notice that the secrets have been compromised. If properly implemented and used then Shared-Secrets can protect against such an attacker in the following way:

Shared-Secrets provides a browser-based encryption and decryption that is executed locally. Using this additional layer of encryption would prevent the malicious server from reading the decrypted secret. However, an active man-in-the-middle attacker would also be able to compromise the browser-based decryption. In order to mitigate the compromise of the local decryption in cases where you cannot find out if the Shared-Secret server is legitimate, the following strategy might be helpful:

1. Open a fresh **private** browsing window (also known as _"incognito mode"_).
3. Retrieve the secret.
4. Go offline with your computer. Do **not** forget to disable wireless connections or to unplug wired connections.
5. Locally decrypt the retrieved secret.
6. Take note of the locally decrypted secret.
7. Close the private browsing window.
8. Now you can go online with your computer again.

However, the better solution to this problem would be to decrypt the retrieved secret outside of the browser. Unfortunately, this would require the usage of additional tooling.

### Active Man-in-the-Middle Attack (Scenario B)

An active man-in-the-middle attacker could change the transmitted secret sharing links in a way that they retrieve the secrets and then create new secret sharing links containing the retrieved secrets using the same Shared-Secrets server. In such a scenario you would not easily notice that the secrets have been compromised. If properly implemented and used then Shared-Secrets can protect against such an attacker in the following ways:

1. Shared-Secrets provides a browser-based encryption and decryption that is executed locally. Using this additional layer of encryption would prevent the attacker from reading the decrypted secret.
2. Shared-Secrets provides the possibility to create separate **share-only** and **read-only** instances. By having a **share-only** instance that is **not** publicly available and a **read-only** instance that is publicly available the attacker would be able to retrieve the secret but would not be able to create a new secret sharing link.

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
