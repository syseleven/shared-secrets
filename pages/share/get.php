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

  <form role="form" action="/<?= html(SECRET_URI) ?>" method="post">
    <h1>Share a Secret:</h1>
    <div id="secret-div">
      <textarea autocomplete="off" class="form-control" id="secret" name="secret" rows="5" required="required"></textarea>
      <div id="counter">1024</div>
    </div>
    <button type="submit" class="btn btn-default pull-right" id="share-secret-btn" name="share-secret-btn">Share the Secret!</button>
  </form>

  <label class="checkbox-inline" for="encrypt-locally"><input type="checkbox" autocomplete="off" id="encrypt-locally" value="" />Password-protected: </label>
  <input type="password" autocomplete="off" class="form-control" id="password" maxlength="64" size="32" />
  <input type="button" class="btn btn-default" id="encrypt" value="Protect!" />

  <link href="/resources/css/share.css?<?= $cache_value ?>" integrity="sha256-EYu1Dc10IDi0yUOyV55YWmCKWfVlBj1rTMk/AsbViKE=" rel="stylesheet" type="text/css" />

  <script src="/resources/js/lib.js?<?= $cache_value ?>" integrity="sha256-d6lWbau1r7bB+u7utpkOX6tLTXnbOPqD33NmIXHY19A=" type="text/javascript"></script>
  <script src="/resources/js/share.js?<?= $cache_value ?>" integrity="sha256-sNJqhCLJH7s32SPq0o7P9LJAtxTUhvAOT1CBzfZLbac=" type="text/javascript"></script>

<?php

  # include footer
  require_once(ROOT_DIR."/template/footer.php");

