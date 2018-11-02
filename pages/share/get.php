<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # define page title
  define("PAGE_TITLE", "Share a Secret.");

  # include header
  require_once(ROOT_DIR."/template/header.php");

  # prevents cache hits with wrong CSS
  $cache_value = md5_file(__FILE__);

?>

<?php
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

  <form role="form" action="/<?php print(htmlentities(SECRET_URI)); ?>" method="post">
    <label for="secret"><h1>Share a Secret:</h1></label>
    <input type="text" autocomplete="off" class="form-control" id="secret" name="secret" maxlength="512" size="512" />
    <button type="submit" class="btn btn-default pull-right" id="share-secret-btn" name="share-secret-btn">Share the Secret!</button>
  </form>

  <link href="/resources/css/share.css?<?php print($cache_value); ?>" integrity="sha256-tByl5f3IGvPqqtUvyHcSIe4SXVXRnx7wiMlmG07yZbA=" rel="stylesheet" type="text/css" />

<?php
  if (ENABLE_PASSWORD_PROTECTION) {
?>
  <label class="checkbox-inline" for="encrypt-locally"><input type="checkbox" autocomplete="off" id="encrypt-locally" value="" />Password-protected: </label>
  <input type="password" autocomplete="off" class="form-control" id="password" maxlength="64" size="32" />
  <input type="button" class="btn btn-default" id="encrypt" value="Protect!" />

  <script src="/vendors/asmcrypto/asmcrypto.js?<?php print($cache_value); ?>" integrity="sha256-+3Ja+u+3rug2giERjvQSkhc1GZ1jG8ebXZ5TbQe2890=" type="text/javascript"></script>
  <script src="/vendors/buffer/index.js?<?php print($cache_value); ?>" integrity="sha256-IPmwFfeUWk24ndz0SJHTzsHYZPAQac6HfnxyZ+EbqFM=" type="text/javascript"></script>
  <script src="/resources/js/share.js?<?php print($cache_value); ?>" integrity="sha256-tOjQ3Gc/ZSpJ7lVty0FOkP3NRPJkxir1UFXVF3JM4Mw=" type="text/javascript"></script>
<?php
  }
?>

<?php

  # include footer
  require_once(ROOT_DIR."/template/footer.php");

?>
