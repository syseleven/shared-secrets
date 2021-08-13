# Shared-Secrets Encryption Schemes

Shared-Secrets uses two encryption schemes - one for the client-side encryption within the browser, one for the server-side encryption. The client-side encryption prevents the server from learning the actual secret while the server-side encryption prevents anyone else from being able to decrypt the secret. This way the read-once property of the secret sharing links can be enforced.

The encryption schemes are named after their version field (which is the first byte of every encrypted message). Currently schemes **v00** (client-side encryption) and **v01** (server-side encryption) are defined.

## Encryption Scheme v00

Encryption scheme v00 is a password-based Encrypt-then-MAC (EtM) scheme which uses AES-256-CTR as the encryption algorithm and HMAC-SHA-256 as the MAC algorithm. The key to derive the encryption key and the message authentication key is derived via a 10.000 rounds PBKDF2-SHA-256 on the password and a 256 bits long random salt.

### Message Format

Messages in the v00 format have the following format:

```
[version:01][salt:32][nonce:16][message:nn][mac:32]
```

### Message Fields

Messages in the v00 format have the following fields:

* **version** is 1 byte in size and **MUST** have the value `00h`
* **salt** is 32 bytes in size and **SHOULD** contain a cryptographically strong random number
* **nonce** is 16 bytes in size and **SHOULD** contain the UNIX timestamp as the first 8 bytes and `00h` bytes as the second 8 bytes
* **message** is the AES-256-CTR encrypted message
* **mac** is 32 bytes in size and **MUST** contain the HMAC-SHA-256 MAC of all previous fields in their given order

### Key Derivation

Messages in the v00 format use the following keys:

* **salt** is a cryptographically strong random number
* **key** is derived from the given password and **salt** using a 10.000 rounds PBKDF2-SHA-256
* **enckey** is derived from **key** as the key and the string `enc` as the message using HMAC-SHA-256
* **mackey** is derived from **key** as the key and the string `mac` as the message using HMAC-SHA-256

### Key Usage

Keys in the v00 format have the following purposes:

* **enckey** in combination with **nonce** are used to encrypt the message using AES-256-CTR
* **mackey** is used as the key to calculate the MAC of the message `[version:01][salt:32][nonce:16][message:nn]` using HMAC-SHA-256

### Example

The following Bash command encrypts a message with a given password using the above encryption scheme v00. A current version of OpenSSL/LibreSSL and the tool [nettle-pbkdf2](http://manpages.ubuntu.com/manpages/en/man1/nettle-pbkdf2.1.html) are needed:

```
# version 00 symmetric encryption
MESSAGE="message to encrypt" &&
PASSWORD="password" &&
VERSION="00" &&
NONCE=$(printf "%016x0000000000000000" "$(date +%s)") &&
SALT=$(openssl rand -hex 32) &&
KEY=$(echo -n "$PASSWORD" | nettle-pbkdf2 -i 10000 -l 32 --raw --hex-salt "$SALT" | xxd -p | tr -d "\n") &&
ENCKEY=$(echo -n "enc" | openssl dgst -sha256 -mac "HMAC" -macopt "hexkey:$KEY" -binary | xxd -p | tr -d "\n") &&
MACKEY=$(echo -n "mac" | openssl dgst -sha256 -mac "HMAC" -macopt "hexkey:$KEY" -binary | xxd -p | tr -d "\n") &&
ENCMESSAGE=$(echo -n "$MESSAGE" | openssl enc -aes-256-ctr -K "$ENCKEY" -iv "$NONCE" -nopad | xxd -p | tr -d "\n") &&
MACMESSAGE="$VERSION$SALT$NONCE$ENCMESSAGE" &&
MAC=$(echo -n "$MACMESSAGE" | xxd -r -p | openssl dgst -sha256 -mac "HMAC" -macopt "hexkey:$MACKEY" -binary | xxd -p | tr -d "\n") &&
FULLMESSAGE="$MACMESSAGE$MAC" &&
echo "$FULLMESSAGE"
```

## Encryption Scheme v01

Encryption scheme v01 is a RSA-key-based Encrypt-then-MAC (EtM) scheme which uses AES-256-CTR as the encryption algorithm and HMAC-SHA-256 as the MAC algorithm. The key to derive the encryption key and the message authentication key is randomly generated.

### Message Format

Messages in the v01 format have the following format:

```
[version:01][rsakeycount:02][rsakeyid:32][rsakeylength:02][rsakey:mm][...][rsakeyid:32][rsakeylength:02][rsakey:mm][nonce:16][message:nn][mac:32]
```

### Message Fields

Messages in the v01 format have the following fields:

* **version** is 1 byte in size and **MUST** have the value `01h`
* **rsakeycount** is 2 bytes in size and **MUST** denote the number of upcoming RSA key blocks
* **rsakeyid** is 32 bytes in size and **MUST** contain the SHA-256 hash of the DER-encoded RSA public key that was used to encrypt the upcoming **rsakey**
* **rsakeylength** is 2 bytes in size and **MUST** denote the length of the upcoming **rsakey**
* **rsakey** has the length of the previous **rsakeylength** field and **MUST** contain the key that was used to derive the encryption key and the message authentication key RSA-encrypted for the RSA key denoted by the previous **rsakeyid** field
* **nonce** is 16 bytes in size and **SHOULD** contain the UNIX timestamp as the first 8 bytes and `00h` bytes as the second 8 bytes
* **message** is the AES-256-CTR encrypted message
* **mac** is 32 bytes in size and **MUST** contain the HMAC-SHA-256 MAC of all previous fields in their given order

### Key Derivation

Messages in the v01 format use the following keys:

* **key** is a cryptographically strong random number
* **enckey** is derived from **key** as the key and the string `enc` as the message using HMAC-SHA-256
* **mackey** is derived from **key** as the key and the string `mac` as the message using HMAC-SHA-256
* **rsakey** is derived by RSA-encrypting **key** with an RSA public key

The required RSA public key can be generated as follows:

```
openssl genrsa -out ./rsa.priv 2048
openssl rsa -in ./rsa.priv -pubout -outform PEM > ./rsa.pub
```

### Key Usage

Keys in the v01 format have the following purposes:

* **enckey** in combination with **nonce** are used to encrypt the message using AES-256-CTR
* **mackey** is used as the key to calculate the MAC of the message `[version:01][rsakeycount:02][rsakeyid:32][rsakeylength:02][rsakey:mm][...][rsakeyid:32][rsakeylength:02][rsakey:mm][nonce:16][message:nn]` using HMAC-SHA-256

### Example

The following Bash command encrypts a message with a given password using the above encryption scheme v01. A current version of OpenSSL/LibreSSL is needed:

```
# version 01 hybrid encryption
MESSAGE="message to encrypt" &&
RSAKEYFILE="./rsa.pub" &&
RSAKEYCOUNT="0001" &&
VERSION="01" &&
NONCE=$(printf "%016x0000000000000000" "$(date +%s)") &&
KEY=$(openssl rand -hex 32) &&
ENCKEY=$(echo -n "enc" | openssl dgst -sha256 -mac "HMAC" -macopt "hexkey:$KEY" -binary | xxd -p | tr -d "\n") &&
MACKEY=$(echo -n "mac" | openssl dgst -sha256 -mac "HMAC" -macopt "hexkey:$KEY" -binary | xxd -p | tr -d "\n") &&
RSAKEY=$(echo -n "$KEY" | xxd -r -p | openssl rsautl -encrypt -oaep -pubin -inkey "$RSAKEYFILE" -keyform PEM | xxd -p | tr -d "\n") &&
RSAKEYID=$(openssl rsa -pubin -in "$RSAKEYFILE" -pubout -outform DER 2>/dev/null | openssl dgst -sha256 -binary | xxd -p | tr -d "\n") &&
RSAKEYLENGTH=$(echo -n "$RSAKEY" | xxd -r -p | wc -c) &&
RSAKEYLENGTH=$(printf "%04x" "$RSAKEYLENGTH") &&
ENCMESSAGE=$(echo -n "$MESSAGE" | openssl enc -aes-256-ctr -K "$ENCKEY" -iv "$NONCE" -nopad | xxd -p | tr -d "\n") &&
MACMESSAGE="$VERSION$RSAKEYCOUNT$RSAKEYID$RSAKEYLENGTH$RSAKEY$NONCE$ENCMESSAGE" &&
MAC=$(echo -n "$MACMESSAGE" | xxd -r -p | openssl dgst -sha256 -mac "HMAC" -macopt "hexkey:$MACKEY" -binary | xxd -p | tr -d "\n") &&
FULLMESSAGE="$MACMESSAGE$MAC" &&
echo "FULLMESSAGE"
```
