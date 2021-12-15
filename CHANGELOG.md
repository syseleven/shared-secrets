# 0.29b0 (2021-12-14)

* introduce support for configuration via environment variables
* introduce support for configuration via .env file
* updated README to document environment variables

# 0.28b0 (2021-06-07)

* updated jQuery to version 3.6.0
* fixed typo on howto page
* updated README to introduced maintenance information
* updated README to opt out of Google FLoC by default

# 0.27b0 (2020-07-16)

* introduced `IMPRINT_TEXT` to change the default text of the imprint link
* HTML-escape `IMPRINT_TEXT` to prevent the admin from breaking the HTML output
* updated jQuery to version 3.5.1

# 0.26b0 (2020-05-07)

* introduced `JUMBO_SECRETS` to allow secrets of up to 16kb
* **Beware:** Jumbo secret sharing links that exceed 2048 bytes (2k) in size will most likely be incompatible with older Internet Explorer versions.

# 0.25b1 (2019-12-12)

* fixed selection of keyid for key rollovers

# 0.25b0 (2019-12-12)

* introduced proper key rollover support by adding the corresponding keyid to the database primary key
* now you do not have to purge the database when switching to a new key as fingerprints will not collide for different keys
* renamed `$checksum` to `$fingerprint` to match with the database fields
* updated README to provide a best-practice logging configuration
* updated README to reflect the key rollover support
* updated README to address security limitations of this solution

# 0.24b0 (2019-11-15)

* introduced helper functions `is_privkey()` and `is_pubkey()`
* as a **share-only** instance doesn't need the private key, `RSA_PRIVATE_KEYS` can now also hold RSA public keys
* creating secret sharing links and downloading the public key now also works when RSA public keys are set as `RSA_PRIVATE_KEYS`
* updated README to reflect the new feature

# 0.23b0 (2019-11-14)

* fixed read-only mode and introduced share-only mode
* introduced human-readable page for downloading the public key under `/pub`
* changed the download of the plain public key to `/pub?plain`
* on errors the application now returns `403 Forbidden` response codes instead of `200 OK` response codes
* updated README to reflect the new features

# 0.22b0 (2019-11-13)

* improved URL parsing
* added support for `plain` GET parameter
* updated README to reflect the required configuration change

# 0.21b1 (2019-11-08)

* added length check to local decryption as has been done for local encryption

# 0.21b0 (2019-11-07)

* introduced `router.php` for local debugging
* changed default value of `DEBUG_MODE` from `true` to `false`
* prevent the local encryption with empty secrets and empty passwords
* prevent the encryption of secrets that are too long to be shared
* introduced a JavaScript-based resubmission prevention for supporting browsers
* introduced support for multi-line secrets and added a byte counter

# 0.20b2 (2019-10-31)

* simplified URL-safe Base64 encoding and decoding

# 0.20b1 (2019-10-30)

* correct the minimum length of v00 messages from 50 (without the MAC) to 82 (with the MAC) in `lib/shared-secrets.exec.php`

# 0.20b0 (2019-10-29)

* rewrote the application to use OpenSSL instead of GPG fixing indirect integrity vulnerabilities
* rewrote the client-side encryption to be based on the [Web Cryptography API](https://www.w3.org/TR/WebCryptoAPI/)
* added `encryption.md` to describe the details of the newly implemented encryption scheme
* moved configuration file into the subfolder `config/`
* renamed folder `libs/` to `lib/`
* introduced the possibility to have key roll-overs (encrypt with new key but decrypt with new and old key)
* updated Bootstrap to version 3.4.1
* updated jQuery to version 3.4.1
* **Beware:** Decrypting old GPG-based URLs is not possible with this version. Due to the new encryption scheme v01 the first 48 bytes of a URL will be the same for all generated URLs therefore it is possible to distinguish with high certainty between old and new URLs so that it is possible for you to redirect old URLs to a read-only instance.

# 0.14b0 (2019-10-28)

* introduced a read-only mode to simplify the migration to the upcoming release

# 0.13b0 (2018-11-02)

* introduced `apache_bugfix_encode()` and `apache_bugfix_decode()` to prevent "(36) File name too long" errors
* **SECURITY**: prevent whitespace from allowing an attacker to retrieve a secret more than once

# 0.12b0 (2018-06-22)

* decryption of non-MDC-protected messages is now prevented for older versions of GnuPG that set the return code to 0
* force GnuPG to produce English output as we have to check it against a predefined string

# 0.11b0 (2017-08-10)

* version bump for legacy-less publication on github
* **Beware:** Due to security concerns the previously introduced GnuPG PECL interface has been forcecfully deactivated. This change may break installations that currently rely on the functionality of the GnuPG PECL interface.

# 0.10b2 (2017-08-10)

* activated `ENABLE_PASSWORD_PROTECTION` option by default as the feature has proven to be stable
* deactivated `LOG_IP_ADDRESS` option by default to promote data privacy
* removed `SUPPORT_LEGACY_LINKS` option because the codebase does not generate legacy links for almost a year now
* removed code that handled legacy links and has become obsolete with the removal of the `SUPPORT_LEGACY_LINKS` option
* forcefully deactivated the GnuPG PECL interface because https://github.com/php-gnupg/php-gnupg/issues/9 is not handled properly
* updated README to reflect the forcful deactivation of the GnuPG PECL interface which may break installations

# 0.10b1 (2016-12-19)

* enforced strict base64 decoding
* added info to `config.php.default` and to the README that the GnuPG PECL should currently not be used (thanks to Nikolas Lotz)

# 0.10b0 (2016-12-19)

* fixed a security bug that allowed to retrieve a secret several times by appending query parameters to the secret (thanks to Nikolas Lotz)

# 0.9b0 (2016-11-08)

* version bump for interface improvements publication on github

# 0.8b5 (2016-10-21)

* introduced dynamic indentation for shell command on how page
* tested interface improvements within chroot environment

# 0.8b4 (2016-10-20)

* introduced dummy parameters to fix cached-subresource-checksum-mismatch problem when changing CSS/JS files

# 0.8b3 (2016-10-20)

* removed copy-to-clipboard functionality as it proves to be unreliable
* improved style to simplify manual copying of generated shared secret link
* updated readme accordingly

# 0.8b2 (2016-10-19)

* fixed secret-already-retrieved error message

# 0.8b1 (2016-10-07)

* introduced the parameter `plain` for the share action to just return the link without surrounding HTML
* introduced the parameter `plain` for the read action to just return the secret without surrounding HTML
* introduced some minor changes to make parameter constant naming more consistent
* introduced `.htaccess` to simplify installation using Apache HTTPD
* updated included libraries to newer releases

# 0.8b0 (2016-09-11)

* version bump for GnuPG PECL package support publication on github
* **Beware:** With version 0.8b0 the structure of the secret sharing links has slightly changed. You have to set the `SUPPORT_LEGACY_LINKS` configuration value to `true` if you want to support secret sharing links that have been generated for older versions of Shared-Secrets. Failure to do so will break these legacy links.

# 0.7b2 (2016-09-10)

* rewrote non-PECL encryption to not use ASCII-armoring anymore
* enhanced non-PECL link generation so that PECL and non-PECL links look the same
* cleaned up PECL and non-PECL encryption/decryption code
* simplified and fixed code for PECL or non-PECL call selection
* introduced new configuration variable `SUPPORT_LEGACY_LINKS`
* introduced code that provides backward-compatibility for legacy links
* tested PECL implementation in chroot environment and failed
* updated readme to reflect observations made in chroot environment

# 0.7b1 (2016-09-08)

* implemented support for the newly released GnuPG PECL version 1.4.0
* introduced homedir support for non-PECL encryption and decryption
* introduced new configuration variable `GPG_HOME_DIR`
* implemented handling of equation signs for the URL-safe Base64 encoding and decoding
* tested backward-compatibility so that non-PECL URL don't break
* updated howto website which automatically adjusts when PECL is active
* updated readme to decribe how to install the GnuPG PECL
* fixed some typos in the documentation and in comments

# 0.7b0 (2016-09-08)

* version bump for url-safe Base64 encoding publication on github

# 0.6b1 (2016-09-07)

* implemented so-called url-safe Base64 encoding of secrets to reduce URL-encoding junk
* checked backward-compatibility with previous standard URL-encoded URLs
* improved line-break handling in GPG message unstripping
* tested URL-safe Base64 encoding feature within chroot environment

# 0.6b0 (2016-09-02)

* version bump for increased readability publication on github

# 0.5b1 (2016-09-02)

* fixed copy-to-clipboard feature when password-protection feature is disabled
* increased readability of optional code that is added for password-protection feature

# 0.5b0 (2016-09-02)

* version bump for improved Mozilla Observatory rating publication on github

# 0.4b1 (2016-09-01)

* improved code so that A+ rating in Mozilla Observatory can be achieved
* added HTTP header configuration to readme
* modified the changelog so that it is more consistent
* updated the readme to describe how an A+ rating can be achieved
* tested improved code within chroot environment

# 0.4b0 (2016-08-30)

* version bump for integrity-checking feature publication on github

# 0.3b1 (2016-08-29)

* moved inline JavaScript code to separate .js files
* introduced subresource integrity for link and style elements
* added `asmCrypto.js.map` for better debugging support
* switched from server-defined PBKDF2 salt to JavaScript-generated salt
* tested implementation of new features within chroot environment

# 0.3b0 (2016-08-25)

* version bump for password-protection feature publication on github

# 0.2b2 (2016-08-25)

* improved length and handling of server-defined PBKDF2 salt
* fixed escaping of password-protected secrets
* harmonized variable names of encryption and decryption code
* tested implementation of password-protection feature within chroot environment

# 0.2b1 (2016-08-24)

* implemented the password-protection feature

# 0.2b0 (2016-08-22)

* version bump for initial publication on github

# 0.1b5 (2016-08-19)

* introduce support for GPG passphrase via passphrase file
* simplified index file structure
* fixed message unstripping which produced undecryptable results for certain lengths
* tested implementation of shared-secrets service within chroot environment

# 0.1b4 (2016-08-16)

* optimized copy-to-clipboard JavaScript integration
* fixed error message handling
* added changelog file

# 0.1b3 (2016-08-15)

* prepared publication on github
* introduced config parameters for customization
* updated "how" page to be customizable
* added license file
* added readme file

# 0.1b2 (2016-08-15)

* introduced copy-to-clipboard feature
* disabled auto-form-fill of browsers

# 0.1b1 (2016-08-15)

* introduced cleaned-up code structure
* introduced action handling code
* introduced page handling code
* introduced template handling code
* introduced separate config.php file

# 0.1a2 (2016-08-11)

* allowed URL-encoded and URL-unencoded secret URIs (Apple Mail bug)
* published to internal git versioning

# 0.1a1 (2016-08-11)

* initial PoC release
* tested with first customer
