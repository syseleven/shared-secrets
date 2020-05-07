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

  <noscript>
    <div class="alert alert-warning">
      <strong>Warning!</strong> You don't have JavaScript enabled. You will not be able to share password-protected secrets.
    </div>
  </noscript>
  <div class="alert alert-danger" id="encrypt-error">
    <strong>Error!</strong> Local encryption failed.
  </div>

  <form role="form" action="/<?= html(SECRET_URI) ?><?= (PLAIN_PARAM) ? "?plain" : "" ?>" method="post">
    <h1>Share a Secret:</h1>
    <div id="secret-div">
      <textarea autocomplete="off" class="form-control" id="secret" name="secret" rows="5" required="required"></textarea>
      <div id="counter"><?= MAX_PARAM_SIZE ?></div>
    </div>
    <button type="submit" class="btn btn-default pull-right" id="share-secret-btn" name="share-secret-btn">Share the Secret!</button>
  </form>

  <label class="checkbox-inline" for="encrypt-locally"><input type="checkbox" autocomplete="off" id="encrypt-locally" value="" />Password-protected: </label>
  <input type="password" autocomplete="off" class="form-control" id="password" maxlength="64" size="32" />
  <input type="button" class="btn btn-default" id="encrypt" value="Protect!" />

  <link href="/resources/css/share.css?<?= $cache_value ?>" integrity="sha256-EYu1Dc10IDi0yUOyV55YWmCKWfVlBj1rTMk/AsbViKE=" rel="stylesheet" type="text/css" />

  <script src="/resources/js/lib.js?<?= $cache_value ?>" integrity="sha256-d6lWbau1r7bB+u7utpkOX6tLTXnbOPqD33NmIXHY19A=" type="text/javascript"></script>
<?php
  if (defined("JUMBO_SECRETS") && JUMBO_SECRETS) {
?>
  <script src="/resources/js/jumbo_limit.js?<?= $cache_value ?>" integrity="sha256-7OnyT9osWKeiIPJ7xJ8IF1UYF3c/rpy2+ku0sQ0oue4=" type="text/javascript"></script>
<?php
  } else {
?>
  <script src="/resources/js/limit.js?<?= $cache_value ?>" integrity="sha256-HwcYaoqBBJhR7Y7eG2CepXkamos6C6SaViLGifuuo4E=" type="text/javascript"></script>
<?php
  }
?>
  <script src="/resources/js/share.js?<?= $cache_value ?>" integrity="sha256-JgwhPbFEIzq89yXPJxa5NkZsH8F5MtkCsQ/5sHwU+gg=" type="text/javascript"></script>

<?php

  # include footer
  require_once(ROOT_DIR."/template/footer.php");

