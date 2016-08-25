# Shared-Secrets

Shared-Secrets is an application that helps you to simply share one-time secrets over the web. Typically when you do not have the possibility to open an encrypted communication channel (e.g. GPG-encrypted mail) to transfer an important information you have to resort to unencrypted means of communication - e.g. SMS, unencrypted e-mail, telephone, etc.

Using the Shared-Secrets service allows you to transfer the actual secret in an encrypted form. Retrieving the secret is as simple as following a link. In contrast to other secret sharing services, Shared-Secrets does not store the secret on the server, but puts the encrypted secret into the link that you share with the desired recipient. That means that the comprise of a Shared-Secrets server does not automatically compromise all of the shared secrets.

Secrets can only be retrieved once. Further retrievals are rejected by matching the secret against the fingerprints of secrets that have been retrieved before. By disallowing repeating retrievals of a secret, it is at least possible to detect when the confidentiality of a secret has been compromised.

To protect your secret from getting known by the server or an attacker, you can additionally protect the secret with a password before sharing it. The secret will be encrypted and decrypted locally without an interaction with the server. You can provide the chosen password to the recipient through a second communication channel to prevent an attacker that is able to control one communication channel from compromising the confidentiallity of your secret.

## Usage

### Share a Secret

Simply enter your secret on the default page of the Shared-Secrets service. You can decide to password-protect the entered secret before sending it to the server by checking the "Password-protected:" box, entering your password and pressing the "Protect!" button. After that, press the "Share the Secret!" button. The secret will be GPG-encrypted and converted into a secret sharing link.

### Read a Secret

To retrieve the secret, simply open the secret sharing link and press the "Read the Secret!" button. Should your secret be password-protected, check the "Password-protected:" box, enter your password and read your actual secret by pressing the "Unprotect!" button.

## Installation

### Requirements

Shared-Secrets is based on MariaDB 10.0, Nginx 1.10 and PHP 7.0, but should also work with MySQL, Apache and earlier versions of PHP.

### Nginx Setup

Shared-Secrets uses a single entry point to control the dataflow. Therefore the following rewrite rule is required (Nginx example):
```
if (!-f $request_filename) {
  rewrite ^.*$ /index.php last;
}
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

### Service Setup

Rename the "config.php.default" to "config.php" and set the necessary configuration items.

### TLS Recommendation

It is strongly recommended to use TLS to protect the connection between the server and the clients.

## Attributions

* asmCrypto (https://github.com/vibornoff/asmcrypto.js/): for providing PBKDF2 and AES functions 
* Bootstrap (https://getbootstrap.com): for providing an easy-to-use framework to build nice-looking applications
* buffer (https://github.com/feross/buffer): for providing Base64-encoding and array-conversion functions
* clipboard.js (https://clipboardjs.com): for simplifying the copy-to-clipboard use-case a lot
* html5shiv (https://github.com/aFarkas/html5shiv): for handling Internet Explorer compatibility stuff
* jQuery (https://jquery.com): for just existing
* Katharina Franz (https://www.katharinafranz.com): for suggesting Bootstrap as an easy-to-use framework to build nice-looking applications
* Respond.js (https://github.com/scottjehl/Respond): for handling even more Internet Explorer compatibility stuff

## ToDo

* switch to the GnuPG PECL (https://pecl.php.net/package/gnupg) once the PHP 7 support is stable
* switch to a more personalized design (current design is taken from https://github.com/twbs/bootstrap/tree/master/docs/examples/starter-template)
* implement an expiry date functionality

## License

This application is released under the BSD license. See the LICENSE file for further information.
