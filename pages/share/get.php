<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # define page title
  define("PAGE_TITLE", "Share a Secret.");

  # include header
  require_once(ROOT_DIR."/template/header.php");

  if (ENABLE_PASSWORD_PROTECTION) {

?>

    <noscript>
      <div class="alert alert-warning">
        <strong>Warning!</strong> You don't have JavaScript enabled. You will not be able to share password-protected secrets.
      </div>
    </noscript>
    <div class="alert alert-danger" id="encrypt-error">
      <strong>Error!</strong> Local encryption failed.
    </div>

<?php

  }

?>

  <form role="form" action="/<?php print(htmlentities(urlencode(SECRET_URI))); ?>" method="post">
    <label for="secret"><h1>Share a Secret:</h1></label>
    <input type="text" autocomplete="off" class="form-control" id="secret" name="secret" maxlength="512" size="512" />
    <button type="submit" class="btn btn-default pull-right" id="share-secret-btn" name="share-secret-btn">Share the Secret!</button>
  </form>

<?php

  if (ENABLE_PASSWORD_PROTECTION) {

?>

    <label class="checkbox-inline" for="encrypt-locally"><input type="checkbox" autocomplete="off" id="encrypt-locally" value="" />Password-protected: </label>
    <input type="password" autocomplete="off" class="form-control" id="password" maxlength="64" size="32" />
    <input type="button" class="btn btn-default" id="encrypt" value="Protect!" />

    <link href="/resources/css/share.css" integrity="sha256-d3wZL0SNgWVcA6m0aWipQ9T/4I0p55dnYZCVKzsaYlo=" rel="stylesheet" type="text/css" />

    <script src="/vendors/asmcrypto/asmcrypto.js" integrity="sha256-+3Ja+u+3rug2giERjvQSkhc1GZ1jG8ebXZ5TbQe2890=" type="text/javascript"></script>
    <script src="/vendors/buffer/index.js" integrity="sha256-+fItxTnTLDK8HaHyqiP4cD+RxwDK66DqoTE91HqUfnM=" type="text/javascript"></script>
    <script src="/resources/js/share.js" integrity="sha256-HJaUUzmSuv9fd99m8Fr1c/Mxh0J88JLL/R6xisdXTZ4=" type="text/javascript"></script>

<?php

  }

  # include footer
  require_once(ROOT_DIR."/template/footer.php");

?>
