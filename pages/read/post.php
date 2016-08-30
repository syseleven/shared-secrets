<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # define page title
  define("PAGE_TITLE", "Read a Secret.");

  # include header
  require_once(ROOT_DIR."/template/header.php");

  if (ENABLE_PASSWORD_PROTECTION) {

?>

    <noscript>
      <div class="alert alert-warning">
        <strong>Warning!</strong> You don't have JavaScript enabled. You will not be able to read password-protected secrets.
      </div>
    </noscript>
    <div class="alert alert-danger" id="decrypt-error" style="display: none;">
      <strong>Error!</strong> Local decryption failed.
    </div>

<?php

  }

?>

  <h1>Read a Secret:</h1>
  <p><pre id="secret"><?php print(read_secret(SECRET_URI)); ?></pre>
     <button type="btn" class="btn btn-default pull-right" data-clipboard-target="#secret" id="copy-to-clipboard">Copy to Clipboard!</button></p>

<?php

  if (ENABLE_PASSWORD_PROTECTION) {

?>

    <label class="checkbox-inline" for="decrypt-locally"><input type="checkbox" autocomplete="off" id="decrypt-locally" value="" onclick="decrypt_locally();" />Password-protected: </label>
    <input type="password" autocomplete="off" class="form-control" id="password" maxlength="64" size="32" style="display: inline; visibility: hidden; width: 25%;" />
    <input type="button" class="btn btn-default" id="decrypt" style="visibility: hidden;" value="Unprotect!" onclick="decrypt();" />

    <script src="/vendors/asmcrypto/asmcrypto.js" integrity="sha256-+3Ja+u+3rug2giERjvQSkhc1GZ1jG8ebXZ5TbQe2890=" type="text/javascript"></script>
    <script src="/vendors/buffer/index.js" integrity="sha256-+fItxTnTLDK8HaHyqiP4cD+RxwDK66DqoTE91HqUfnM=" type="text/javascript"></script>
    <script src="/vendors/clipboard/clipboard.min.js" integrity="sha256-YPxFEfHAzLj9n2T+2UXAKGNCRUINk0BexppujiVhRH0=" type="text/javascript"></script>
    <script src="/resources/js/copy-to-clipboard.js" integrity="sha256-pdoft+huio0ejiVD+B0WLnFm9Wab+1Yj1nODdPNAZI4=" type="text/javascript"></script>
    <script src="/resources/js/decrypt.js" integrity="sha256-rbZOtsEWrdRylOtuKJGpCMFTvrU4AE2jy2fbbDLteqM=" type="text/javascript"></script>

<?php

  }

  # include footer
  require_once(ROOT_DIR."/template/footer.php");

?>
