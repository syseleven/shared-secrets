# 0.7b0 (2016-09-08)

* version bump for url-safe Base64 encoding publication on github

# 0.6b1 (2016-09-07)

* implemented so-called url-safe Base64 encoding of secrets to reduce URL-encoding junk
* checked backward-compatibility with previous standard URL-encoded URLs
* improved line-break handling in GPG message unstripping
* tested url-safe Base64 encoding feature within chroot environment

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
