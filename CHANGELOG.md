# 0.4b0 (2016-09-30)

* version bump for integrity-checking feature publication on github

# 0.3b1 (2016-08-29)

* moved inline JavaScript code to separate .js files
* introduced subresource integrity for link and style elements
* added asmCrypto.js.map for better debugging support
* switched from server-defined PBKDF2 salt to JavaScript-generated salt
* test implementation of new features within chroot environment

# 0.3b0 (2016-08-25)

* version bump for password-protection feature publication on github

# 0.2b2 (2016-08-25)

* improve length and handling of server-defined PBKDF2 salt
* fix escaping of password-protected secrets
* harmonize variable names of encryption and decryption code
* test implementation of password-protection feature within chroot environment

# 0.2b1 (2016-08-24)

* implemented the password-protection feature

# 0.2b0 (2016-08-22)

* version bump for initial publication on github

# 0.1b5 (2016-08-19)

* introduce support for GPG passphrase via passphrase file
* simplified index file structure
* fixed message unstripping which produced undecryptable results for certain lengths
* test implementation of shared-secrets service within chroot environment

# 0.1b4 (2016-08-16)

* optimize copy-to-clipboard JavaScript integration
* fix error message handling
* add changelog file

# 0.1b3 (2016-08-15)

* prepare publication on github
* introduce config parameters for customization
* update "how" page to be customizable
* add license file
* add readme file

# 0.1b2 (2016-08-15)

* introduce copy-to-clipboard feature
* disable auto-form-fill of browsers

# 0.1b1 (2016-08-15)

* introduce cleaned-up code structure
* introduce action handling code
* introduce page handling code
* introduce template handling code
* introduce separate config.php file

# 0.1a2 (2016-08-11)

* allow URL-encoded and URL-unencoded secret URIs (Apple Mail bug)
* publish to internal git versioning

# 0.1a1 (2016-08-11)

* initial PoC release
* testing with first customer
