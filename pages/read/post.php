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
    <div class="alert alert-danger" id="decrypt-error">
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

    <label class="checkbox-inline" for="decrypt-locally"><input type="checkbox" autocomplete="off" id="decrypt-locally" value="" />Password-protected: </label>
    <input type="password" autocomplete="off" class="form-control" id="password" maxlength="64" size="32" />
    <input type="button" class="btn btn-default" id="decrypt" value="Unprotect!" />

    <link href="/resources/css/read.css" integrity="sha256-miIkI5gYeETYUyNUudOMl2RkZ9Akko+1KXYfxih5dD0=" rel="stylesheet" type="text/css" />

    <script src="/vendors/asmcrypto/asmcrypto.js" integrity="sha256-+3Ja+u+3rug2giERjvQSkhc1GZ1jG8ebXZ5TbQe2890=" type="text/javascript"></script>
    <script src="/vendors/buffer/index.js" integrity="sha256-+fItxTnTLDK8HaHyqiP4cD+RxwDK66DqoTE91HqUfnM=" type="text/javascript"></script>
    <script src="/vendors/clipboard/clipboard.min.js" integrity="sha256-YPxFEfHAzLj9n2T+2UXAKGNCRUINk0BexppujiVhRH0=" type="text/javascript"></script>
    <script src="/resources/js/copy-to-clipboard.js" integrity="sha256-LRwH9pTwY5TAE7KIJSReEy1y29iPc/AbugOTd1LOjrc=" type="text/javascript"></script>
    <script src="/resources/js/read.js" integrity="sha256-c/Xg8fJJ0mVIZ7f/V5m0BWsgX3AQ/mgl1VOvXNBP4Ps=" type="text/javascript"></script>

<?php

  }

  # include footer
  require_once(ROOT_DIR."/template/footer.php");

?>
