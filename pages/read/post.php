<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # define page title
  define("PAGE_TITLE", "Read a Secret.");

  $secret = read_secret(SECRET_URI);

  if (null !== PLAIN_PARAM) {
    print($secret);
  } else {
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
  <p><pre id="secret"><?php print($secret); ?></pre></p>

  <link href="/resources/css/read.css?<?php print($cache_value); ?>" integrity="sha256-wgpxEGDMqG2EJxicZqc40OJMPwN8rBAZTYLdGyagQGw=" rel="stylesheet" type="text/css" />

<?php
  if (ENABLE_PASSWORD_PROTECTION) {
?>
  <label class="checkbox-inline" for="decrypt-locally"><input type="checkbox" autocomplete="off" id="decrypt-locally" value="" />Password-protected: </label>
  <input type="password" autocomplete="off" class="form-control" id="password" maxlength="64" size="32" />
  <input type="button" class="btn btn-default" id="decrypt" value="Unprotect!" />

  <script src="/vendors/asmcrypto/asmcrypto.js?<?php print($cache_value); ?>" integrity="sha256-+3Ja+u+3rug2giERjvQSkhc1GZ1jG8ebXZ5TbQe2890=" type="text/javascript"></script>
  <script src="/vendors/buffer/index.js?<?php print($cache_value); ?>" integrity="sha256-IPmwFfeUWk24ndz0SJHTzsHYZPAQac6HfnxyZ+EbqFM=" type="text/javascript"></script>
  <script src="/resources/js/read.js?<?php print($cache_value); ?>" integrity="sha256-BQqHaEJFlJhgMLM7401/LIdtAQ1VNLmhqePSQPS1foY=" type="text/javascript"></script>
<?php
  }
?>

<?php

    # include footer
    require_once(ROOT_DIR."/template/footer.php");
  }

?>
