# Shared-Secrets

Shared-Secrets is an application that helps you to simply share one-time secrets over the web. Typically when you do not have the possibility to open an encrypted communication channel (e.g. GPG-encrypted mail) to transfer an important information you have to resort to unencrypted means of communication - e.g. SMS, unencrypted e-mail, telephone, etc.

Using the Shared-Secrets service allows you to transfer the actual secret in an encrypted form. Retrieving the secret is as simple as following a link. In contrast to other secret sharing services, Shared-Secrets does not store the secret on the server, but puts the encrypted secret into the link that you share with the desired recipient. That means that the comprise of a Shared-Secrets server does not automatically compromise all of the shared secrets.

Secrets can only be retrieved once. Further retrievals are rejected by matching the secret against the fingerprints of secrets that have been retrieved before. By disallowing repeating retrievals of a secret, it is at least possible to detect when the confidentiality of a secret has been compromised.

To protect your secret from getting known by the server or an attacker, you can additionally protect the secret with a password before sharing it. The secret will be encrypted and decrypted locally without an interaction with the server. You can provide the chosen password to the recipient through a second communication channel to prevent an attacker that is able to control one communication channel from compromising the confidentiallity of your secret.

## Usage

### Share a Secret

Simply enter your secret on the default page of the Shared-Secrets service. You can decide to password-protect the entered secret before sending it to the server by checking the "Password-protected:" box, entering your password and pressing the "Protect!" button. After that, press the "Share the Secret!" button. The secret will be GPG-encrypted and converted into a secret sharing link.

Secret sharing links can also be created by using a simple POST request:
```
curl -X POST -d "secret=<secret>&plain" https://example.com/
```

### Read a Secret

To retrieve the secret, simply open the secret sharing link and press the "Read the Secret!" button. Should your secret be password-protected, check the "Password-protected:" box, enter your password and read your actual secret by pressing the "Unprotect!" button.

Secrets can also be retrieved using a simple POST request:

```
curl -X POST -d "plain" <secret sharing link>
```

## Installation

### Requirements

Shared-Secrets is based on MariaDB 10.0, Nginx 1.10 and PHP 7.0, but should also work with MySQL, Apache and earlier versions of PHP. GPG encryption is supported through the [GnuPG PECL package](https://pecl.php.net/package/gnupg) as well as through directly calling the gpg binary. Using the GnuPG PECL package is the prefered mechanism due to its cleaner interface. If you want to use the GnuPG PECL package support of the Shared-Secrets service in a chroot environment you have to set up your chroot environment properly - this does not seem to be easy.

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

  # Your configuration comes here:
  # ...
}
```

Furthermore the following HTTP headers have to be set (Nginx example):
```
add_header Content-Security-Policy   "default-src 'self'; frame-ancestors 'self'";
add_header Strict-Transport-Security "max-age=15768000; includeSubDomains; preload";
add_header X-Content-Security-Policy "default-src 'self'; frame-ancestors 'self'";
add_header X-Content-Type-Options    "nosniff";
add_header X-Frame-Options           "SAMEORIGIN";
add_header X-Webkit-CSP              "default-src 'self'; frame-ancestors 'self'";
add_header X-XSS-Protection          "1; mode=block";
```

### MariaDB Setup

Shared-Secrets uses a single-table database to store who did retrieve which secret at what point in time. No actual secret content is stored. (The logging of IP addresses can be disabled through the configuration parameter LOG_IP_ADDRESS.):
```
CREATE TABLE secrets ( fingerprint VARCHAR(64) PRIMARY KEY, ip VARCHAR(46), time TIMESTAMP );
```

### GPG Setup

You have to have a somewhat recent GPG version installed on the server. Furthermore you have to generate and import the private and public key of the service. It is recommended to generate the GPG keypair locally and only upload the key material that is really necessary. You can use a so-called notebook keyring (which does not include the certifying/primary private key) to reduce the risk of a full compromise of the used GPG keypair:
```
# generate a GPG keypair, set a passphrase so that you can export the private encryption subkey later
gpg --gen-key

# read the fingerprint of the generated key, replace <email> with the e-mail address that has been used for the key,
# the fingerprint will be shown with spaces for better readability
gpg --with-fingerprint --list-keys <email>

# export the private encryption subkey and the public key
gpg --export-secret-subkeys --armor <email> >./private.asc
gpg --export --armor <email> >./public.asc

# import the GPG keys on the server with the user that will execute GPG
sudo -u www-data -H gpg --import ./private.asc
sudo -u www-data -H gpg --import ./public.asc
```

### PHP Setup

**Beware:** Due to a serious bug in the GnuPG PECL package we ask to use the non-PECL setup.

To use the [GnuPG PECL package](https://pecl.php.net/package/gnupg) it has to be installed and activated on the server. The following steps are based on Ubuntu 16.04 LTS and only serve as an example for the installation and activation of the GnuPG PECL package:
```
# install PHP PEAR/PECL
sudo apt-get install php-pear

# install the PHP development tools
sudo apt-get install php7.0-dev

# install the GPGME development package, see https://www.gnupg.org/related_software/gpgme/index.html
sudo apt-get install libgpgme11-dev

# install the GnuPG PECL package
sudo pecl install gnupg

# register the GnuPG PECL package as an available module
sudo sh -c 'echo "extension=gnupg.so" > /etc/php/7.0/mods-available/gnupg.ini'

# activate the GnuPG PECL package in PHP CLI and PHP-FPM
sudo ln -s /etc/php/7.0/mods-available/gnupg.ini /etc/php/7.0/cli/conf.d/20-gnupg.ini
sudo ln -s /etc/php/7.0/mods-available/gnupg.ini /etc/php/7.0/fpm/conf.d/20-gnupg.ini

# restart PHP-FPM to load the GnuPG PECL package
sudo /etc/init.d/php7.0-fpm restart
```

### Service Setup

Rename the "config.php.default" to "config.php" and set the necessary configuration items.

**Beware:** With version 0.8b0 the structure of the secret sharing links has slightly changed. You have to set the *SUPPORT_LEGACY_LINKS* configuration value to *true* if you want to support secret sharing links that have been generated for older versions of Shared-Secrets. Failure to do so will break these legacy links.

### TLS Recommendation

It is strongly recommended to use TLS to protect the connection between the server and the clients.

## Attributions

* [asmCrypto](https://github.com/vibornoff/asmcrypto.js): for providing PBKDF2 and AES functions 
* [Bootstrap](https://getbootstrap.com): for providing an easy-to-use framework to build nice-looking applications
* [buffer](https://github.com/feross/buffer): for providing Base64 encoding and array conversion functions
* [GnuPG](https://www.gnupg.org): for providing a reliable tool for secure communication
* [GnuPG PECL package](https://pecl.php.net/package/gnupg): for providing a clean interface to GnuPG
* [html5shiv](https://github.com/aFarkas/html5shiv): for handling Internet Explorer compatibility stuff
* [jQuery](https://jquery.com): for just existing
* [Katharina Franz](https://www.katharinafranz.com): for suggesting Bootstrap as an easy-to-use framework to build nice-looking applications
* [Respond.js](https://github.com/scottjehl/Respond): for handling even more Internet Explorer compatibility stuff

## ToDo

* make PECL method work in a chroot environment to get rid of the direct call method
* switch to a more personalized design (current design is taken from [here](https://github.com/twbs/bootstrap/tree/master/docs/examples/starter-template))
* implement an alternative encryption scheme based on AES instead of GPG (fewer dependencies)
* implement an expiry date functionality

## License

This application is released under the BSD license. See the [LICENSE](LICENSE) file for further information.
