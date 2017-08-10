<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # define page title
  define("PAGE_TITLE", "How does this service work?");

  # include header
  require_once(ROOT_DIR."/template/header.php");

  # prevents cache hits with wrong CSS
  $cache_value = md5_file(__FILE__);

  # handle indentation within shell command
  $indentation = "";
  $space_count = 64-strlen(SECRET_SHARING_URL);
  if (0 < $space_count) {
    $indentation = str_repeat(" ", $space_count);
  }

?>

  <h2>Short description of this service.</h2>
  <p>This secret sharing service is based on GPG encryption. When creating a new secret sharing link, the secret itself is encrypted via GPG. The result of the GPG encryption is URL-safe Base64 encoded and prepended with the URL of this website. When the secret sharing link is called, the URL-safe Base64 encoded GPG message is decrypted and the result of the decryption is displayed on the website. Additionally, the fingerprint of the URL-safe Base64 encoded GPG message is stored in a database to prevent it from being displayed more than once.<br />
     <br />
     You can build your own secret sharing link by following some basic steps.</p>

  <h3>Get the correct public key.</h3>
  <p>First of all you have to retrieve the correct public key to encrypt your secret:<br/>
     <pre>gpg --recv-keys --keyserver "hkps://keyserver.syseleven.de/" "<?php print(htmlentities(GPG_KEY_FINGERPRINT)); ?>"</pre></p>

  <h3>Encrypt the secret you want to share.</h3>
  <p>To create a secret sharing link you have to do certain steps that are decribed here:
     <ol>
       <li>encrypt the secret via GPG</li>
       <li>Base64 encode the encrypted secret</li>
       <li>remove line breaks</li>
       <li>apply URL-safe Base64 encoding:
           <ul>
             <li>remove equation signs</li>
             <li>replace "+" with "-"</li>
             <li>replace "/" with "_"</li>
           </ul></li>
       <li>prepend the secret sharing URL</li>
     </ol>
     <br />
     All of these steps can be executed using a single shell command:<br />
     <pre>echo "secret"                                                                     | # the secret you want to share
gpg --recipient "<?php print(htmlentities(GPG_KEY_FINGERPRINT)); ?>" --output - --encrypt - | # encrypt the secret via GPG
openssl base64                                                                    | # Base64 encode the encrypted secret
tr -d "\n"                                                                        | # remove line breaks
tr -d "="                                                                         | # remove equation signs
tr "+" "-"                                                                        | # replace "+" with "-"
tr "/" "_"                                                                        | # replace "/" with "_"
awk '{print "<?php print(htmlentities(SECRET_SHARING_URL)); ?>" $0}'<?php print($indentation); ?> # prepend secret sharing URL</pre></p>

  <h3>Or...</h3>
  <p>...just use the <a href="/">secret sharing form</a> we provide for your convenience.</p>

<?php
  if (ENABLE_PASSWORD_PROTECTION) {
?>
  <h2>Short description of the password-protection feature.</h2>
  <p>When using the password-protection feature, the secret is encrypted locally using the AES algorithm in GCM mode. The encryption key is derived from the entered password and a dynamically generated salt using the PBKDF2 algorithm. The dynamically generated salt is prepended to the encrypted secret. The password-protection feature is implemented using client-side JavaScript. Please beware that a compromised server may serve you JavaScript code that defeats the purpose of the local encryption. If you do not trust the server that provides the secret sharing service, then encrypt your secret with a locally installed application before sharing it.
<?php
  }
?>

<?php

  # include header
  require_once(ROOT_DIR."/template/footer.php");

?>
