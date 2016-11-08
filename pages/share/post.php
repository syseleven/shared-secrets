<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # define page title
  define("PAGE_TITLE", "Share a Secret.");

  $secret = share_secret(SECRET_PARAM);

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
      <strong>Warning!</strong> You don't have JavaScript enabled. You will not be able to share password-protected secrets.
    </div>
  </noscript>
<?php
  }
?>

  <h1>Share a Secret:</h1>
  <p><pre id="secret"><?php print($secret); ?></pre></p>

  <link href="/resources/css/share.css?<?php print($cache_value); ?>" integrity="sha256-tByl5f3IGvPqqtUvyHcSIe4SXVXRnx7wiMlmG07yZbA=" rel="stylesheet" type="text/css" />

<?php

    # include footer
    require_once(ROOT_DIR."/template/footer.php");
  }

?>
