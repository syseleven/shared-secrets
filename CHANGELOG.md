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

* introduced the parameter "plain" for the share action to just return the link without surrounding HTML
* introduced the parameter "plain" for the read action to just return the secret without surrounding HTML
* introduced some minor changes to make parameter constant naming more consistent
* introduced .htaccess to simplify installation using Apache HTTPD
* updated included libraries to newer releases

# 0.8b0 (2016-09-11)

* version bump for GnuPG PECL package support publication on github
* **Beware:** With version 0.8b0 the structure of the secret sharing links has slightly changed. You have to set the *SUPPORT_LEGACY_LINKS* configuration value to *true* if you want to support secret sharing links that have been generated for older versions of Shared-Secrets. Failure to do so will break these legacy links.

# 0.7b2 (2016-09-10)

* rewrote non-PECL encryption to not use ASCII-armoring anymore
* enhanced non-PECL link generation so that PECL and non-PECL links look the same
* cleaned up PECL and non-PECL encryption/decryption code
* simplified and fixed code for PECL or non-PECL call selection
* introduced new configuration variable SUPPORT_LEGACY_LINKS
* introduced code that provides backward-compatibility for legacy links
* tested PECL implementation in chroot environment and failed
* updated readme to reflect observations made in chroot environment

# 0.7b1 (2016-09-08)

* implemented support for the newly released GnuPG PECL version 1.4.0
* introduced homedir support for non-PECL encryption and decryption
* introduced new configuration variable GPG_HOME_DIR
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
* added asmCrypto.js.map for better debugging support
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
