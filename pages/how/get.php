<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # define page title
  define("PAGE_TITLE", "How does this service work?");

  # include header
  require_once(ROOT_DIR."/template/header.php");

  # prevents cache hits with wrong CSS
  $cache_value = md5_file(__FILE__);

?>

  <h2>Short description of this service.</h2>
  <p>This secret sharing service is based on AES and RSA encryption. When creating a new secret sharing link, a random key is generated which is used to encrypt the secret using AES. The key itself is then encrypted using RSA. The result of the encryption is URL-safe Base64 encoded and prepended with the URL of this website. When the secret sharing link is called, the URL-safe Base64 encoded message is decrypted and the result of the decryption is displayed on the website. Additionally, the fingerprint of the encrypted message is stored in a database to prevent it from being displayed more than once.<br /></p>

  <h3>Get the correct public key.</h3>
  <p>First of all you have to retrieve the correct public key to encrypt your secret:<br/>
     <pre>wget -O "./secrets.pub" "<?= html(trail(SECRET_SHARING_URL, "/")) ?>pub?plain"</pre></p>

  <h3>Encrypt the secret you want to share.</h3>
  <p>To create a secret sharing link you have to do certain steps that are decribed here:
     <ol>
       <li>derive the required key material</li>
       <li>encrypt the secret via AES-256-CTR</li>
       <li>encrypt the key material via RSA</li>
       <li>calculate a MAC of the data via HMAC-SHA-256</li>
       <li>Base64 encode the result</li>
       <li>remove line breaks</li>
       <li>apply URL-safe Base64 encoding:
           <ul>
             <li>remove equation signs</li>
             <li>replace "+" with "-"</li>
             <li>replace "/" with "_"</li>
           </ul></li>
       <li>prepend the secret sharing URL</li>
     </ol></p>

   <h3>Shell example.</h3>
   <p>All of these steps can be executed using a single shell command:<br />
     <pre>MESSAGE="message to encrypt" &&
RSAKEYFILE="./secrets.pub" &&
URLPREFIX="<?= html(trail(SECRET_SHARING_URL, "/")) ?>" &&
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
OUTPUT=$(echo -n "$FULLMESSAGE" | xxd -r -p | openssl base64 | tr "+" "-" | tr "/" "_" | tr "\n" "/" | tr -d "=") &&
OUTPUT="$URLPREFIX$OUTPUT" &&
echo "$OUTPUT"</pre></p>

  <h3>Or...</h3>
  <p>...just use the <a href="/">secret sharing form</a> we provide for your convenience.</p>

  <h2>Short description of the password-protection feature.</h2>
  <p>When using the password-protection feature, the secret is encrypted locally in your browser using AES-256-CTR. The encryption key is derived from the entered password and a dynamically generated salt using the PBKDF2-SHA-256 algorithm. The password-protection feature is implemented using client-side JavaScript. Please beware that a compromised server may serve you JavaScript code that defeats the purpose of the local encryption. If you do not trust the server that provides the secret sharing service, then encrypt your secret with a locally installed application before sharing it.</p>

  <h3>Shell example.</h3>
  <p>You can use the following shell command to encrypt a message and be compatible with the browser-based encryption. You will need the additional tool <a href="http://manpages.ubuntu.com/manpages/en/man1/nettle-pbkdf2.1.html">nettle-pbkdf2</a> for this:<br />
    <pre>MESSAGE="message to encrypt" &&
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
OUTPUT=$(echo -n "$FULLMESSAGE" | xxd -r -p | openssl base64 | tr -d "\n") &&
echo "$OUTPUT"</pre></p>

<?php

  # include header
  require_once(ROOT_DIR."/template/footer.php");

