<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # define page title
  define("PAGE_TITLE", "How does this service work?");

  # include header
  require_once(ROOT_DIR."/template/header.php");

?>

  <h2>Short description of this service.</h2>
  <p>This secret sharing service is based on GPG encryption. When creating a new secret sharing link, the secret itself is encrypted via GPG. The result of the GPG encryption is armored and appended with the URL of this website. When the secret sharing link is called, the armored GPG message is decrypted and the result of the decryption is displayed on the website. Additionally, the fingerprint of the armored GPG message is stored in a database to prevent it from being displayed more than once.<br />
     <br />
     You can build your own secret sharing link by following some basic steps.</p>

  <h3>Get the correct public key.</h3>
  <p>First of all you have to retrieve the correct public key to encrypt your secret:<br/>
     <pre>gpg --recv-keys --keyserver "hkps://keyserver.syseleven.de/" "<?php print(htmlentities(GPG_KEY_FINGERPRINT)); ?>"</pre></p>

  <h3>Ecrypt the secret you want to share.</h3>
  <p>The secret you want to share has to be encrypted with the public key of the service:<br/>
     <pre>echo "&lt;secret&gt;" | gpg --armor --recipient "<?php print(htmlentities(GPG_KEY_FINGERPRINT)); ?>" --output - --encrypt -</pre></p>

  <h3>Strip the GPG message.</h3>
  <p>Remove everything from the armored GPG message but the Base64 encoded content itself. Also remove all line breaks.<br />
     <br />
     Take the following armored GPG message as an example:<br />
     <pre>-----BEGIN PGP MESSAGE-----
Comment: GPGTools - https://gpgtools.org

hQIMA9b1swWSlLtCAQ//beVbroVVHHO9zsQMylVQRtwts6oLhNsaK5nN/aiERT6n
UWIakLQ+bvD06LU8o/SHHR/XsFzmNDl20kP7hDAXXVw7EUIH29zja+F/O7h08W5o
b1a9QvePuoBJjfAOnCehaRWdv/u6G6Dcu6r9fbv4bSWQzZwidTr8p9Ht1/mFw0WP
dyaKcixcaCkOaMWZFkEqwhKAn9p4CTc2vsjF4GzeW1EPS96G+F/u0lAyUv0r79lo
AkhaQjzJbKGaKoSPibpl4iPGXAK3dAAMGAfC+BZHNQyywdEA5bBqu2sJxPvbHDQc
ZayHEUa8i7NweSwVBBAWldfz/9mhAd/pNHlMGh8MhXESFgj4113x9K1TdkyYBcjM
XahzZBnGH5ZfR57iDtEdaNYwbuT5CPlBvV/klBBhtFflMDuvhPTTJ1N474qb0z7d
OLs6IzFqQm2WhDa/oPwKG/roX+6dXL8C+mAlsTUhCrufoK24XZPflMA+W9oTg5ur
cUb+hLPVU2ZEaf5+sSJCGCNh4xRRiD4BB0XDQKraF7XaTpgT2Mza8rWHtnR/6HP/
xmnYdRAsN6UdPJbRETCxC/twNPasmSObNn8a2XXLpBs0pEp96z7cQczooXOkGs6y
CPk9XqCagU2ougyACChTSRtPaYl1g9L+Kd418cHJ5PudbhL9OuyCjBHFafFXBLDS
RAHyQRoQcu3VMQyJTLJ5bxgwoRV4pCJteOzwPR8f9lIsR6GsWPa+LxqQY/Cgq7cp
C29Ssb838IMmAKFmqkNY15m/y7zi
=cjY3
-----END PGP MESSAGE-----</pre>
     After stripping it, it is reduced to the following block:<br />
     <pre>hQIMA9b1swWSlLtCAQ//beVbroVVHHO9zsQMylVQRtwts6oLhNsaK5nN/aiERT6nUWIakLQ+bvD06LU8o/SHHR/XsFzmNDl20kP7hDAXXVw7EUIH29zja+F/O7h08W5ob1a9QvePuoBJjfAOnCehaRWdv/u6G6Dcu6r9fbv4bSWQzZwidTr8p9Ht1/mFw0WPdyaKcixcaCkOaMWZFkEqwhKAn9p4CTc2vsjF4GzeW1EPS96G+F/u0lAyUv0r79loAkhaQjzJbKGaKoSPibpl4iPGXAK3dAAMGAfC+BZHNQyywdEA5bBqu2sJxPvbHDQcZayHEUa8i7NweSwVBBAWldfz/9mhAd/pNHlMGh8MhXESFgj4113x9K1TdkyYBcjMXahzZBnGH5ZfR57iDtEdaNYwbuT5CPlBvV/klBBhtFflMDuvhPTTJ1N474qb0z7dOLs6IzFqQm2WhDa/oPwKG/roX+6dXL8C+mAlsTUhCrufoK24XZPflMA+W9oTg5urcUb+hLPVU2ZEaf5+sSJCGCNh4xRRiD4BB0XDQKraF7XaTpgT2Mza8rWHtnR/6HP/xmnYdRAsN6UdPJbRETCxC/twNPasmSObNn8a2XXLpBs0pEp96z7cQczooXOkGs6yCPk9XqCagU2ougyACChTSRtPaYl1g9L+Kd418cHJ5PudbhL9OuyCjBHFafFXBLDSRAHyQRoQcu3VMQyJTLJ5bxgwoRV4pCJteOzwPR8f9lIsR6GsWPa+LxqQY/Cgq7cpC29Ssb838IMmAKFmqkNY15m/y7zi
=cjY3</pre></p>

  <h3>URL-Encode the GPG message block.</h3>
  <p>After preparing the GPG message block, it has to be url-encoded:<br />
     <pre>hQIMA9b1swWSlLtCAQ%2f%2fbeVbroVVHHO9zsQMylVQRtwts6oLhNsaK5nN%2faiERT6nUWIakLQ%2bbvD06LU8o%2fSHHR%2fXsFzmNDl20kP7hDAXXVw7EUIH29zja%2bF%2fO7h08W5ob1a9QvePuoBJjfAOnCehaRWdv%2fu6G6Dcu6r9fbv4bSWQzZwidTr8p9Ht1%2fmFw0WPdyaKcixcaCkOaMWZFkEqwhKAn9p4CTc2vsjF4GzeW1EPS96G%2bF%2fu0lAyUv0r79loAkhaQjzJbKGaKoSPibpl4iPGXAK3dAAMGAfC%2bBZHNQyywdEA5bBqu2sJxPvbHDQcZayHEUa8i7NweSwVBBAWldfz%2f9mhAd%2fpNHlMGh8MhXESFgj4113x9K1TdkyYBcjMXahzZBnGH5ZfR57iDtEdaNYwbuT5CPlBvV%2fklBBhtFflMDuvhPTTJ1N474qb0z7dOLs6IzFqQm2WhDa%2foPwKG%2froX%2b6dXL8C%2bmAlsTUhCrufoK24XZPflMA%2bW9oTg5urcUb%2bhLPVU2ZEaf5%2bsSJCGCNh4xRRiD4BB0XDQKraF7XaTpgT2Mza8rWHtnR%2f6HP%2fxmnYdRAsN6UdPJbRETCxC%2ftwNPasmSObNn8a2XXLpBs0pEp96z7cQczooXOkGs6yCPk9XqCagU2ougyACChTSRtPaYl1g9L%2bKd418cHJ5PudbhL9OuyCjBHFafFXBLDSRAHyQRoQcu3VMQyJTLJ5bxgwoRV4pCJteOzwPR8f9lIsR6GsWPa%2bLxqQY%2fCgq7cpC29Ssb838IMmAKFmqkNY15m%2fy7zi</pre></p>

  <h3>Prepend the secret sharing URL.</h3>
  <p>The last step is to prepend the secret sharing URL:<br />
     <pre><?php print(htmlentities(SECRET_SHARING_URL)); ?>hQIMA9b1swWSlLtCAQ%2f%2fbeVbroVVHHO9zsQMylVQRtwts6oLhNsaK5nN%2faiERT6nUWIakLQ%2bbvD06LU8o%2fSHHR%2fXsFzmNDl20kP7hDAXXVw7EUIH29zja%2bF%2fO7h08W5ob1a9QvePuoBJjfAOnCehaRWdv%2fu6G6Dcu6r9fbv4bSWQzZwidTr8p9Ht1%2fmFw0WPdyaKcixcaCkOaMWZFkEqwhKAn9p4CTc2vsjF4GzeW1EPS96G%2bF%2fu0lAyUv0r79loAkhaQjzJbKGaKoSPibpl4iPGXAK3dAAMGAfC%2bBZHNQyywdEA5bBqu2sJxPvbHDQcZayHEUa8i7NweSwVBBAWldfz%2f9mhAd%2fpNHlMGh8MhXESFgj4113x9K1TdkyYBcjMXahzZBnGH5ZfR57iDtEdaNYwbuT5CPlBvV%2fklBBhtFflMDuvhPTTJ1N474qb0z7dOLs6IzFqQm2WhDa%2foPwKG%2froX%2b6dXL8C%2bmAlsTUhCrufoK24XZPflMA%2bW9oTg5urcUb%2bhLPVU2ZEaf5%2bsSJCGCNh4xRRiD4BB0XDQKraF7XaTpgT2Mza8rWHtnR%2f6HP%2fxmnYdRAsN6UdPJbRETCxC%2ftwNPasmSObNn8a2XXLpBs0pEp96z7cQczooXOkGs6yCPk9XqCagU2ougyACChTSRtPaYl1g9L%2bKd418cHJ5PudbhL9OuyCjBHFafFXBLDSRAHyQRoQcu3VMQyJTLJ5bxgwoRV4pCJteOzwPR8f9lIsR6GsWPa%2bLxqQY%2fCgq7cpC29Ssb838IMmAKFmqkNY15m%2fy7zi</pre></p>

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
