<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # define page title
  define("PAGE_TITLE", "Share a Secret.");

  # include header
  require_once(ROOT_DIR."/template/header.php");

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
  <p><pre id="secret"><?php print(share_secret(SECRET_PARAM)); ?></pre>
     <button type="btn" class="btn btn-default pull-right" data-clipboard-target="#secret" id="copy-to-clipboard">Copy to Clipboard!</button></p>

  <link href="/resources/css/share.css" integrity="sha256-d3wZL0SNgWVcA6m0aWipQ9T/4I0p55dnYZCVKzsaYlo=" rel="stylesheet" type="text/css" />

  <script src="/vendors/clipboard/clipboard.min.js" integrity="sha256-YPxFEfHAzLj9n2T+2UXAKGNCRUINk0BexppujiVhRH0=" type="text/javascript"></script>
  <script src="/resources/js/copy-to-clipboard.js" integrity="sha256-LRwH9pTwY5TAE7KIJSReEy1y29iPc/AbugOTd1LOjrc=" type="text/javascript"></script>

<?php

  # include footer
  require_once(ROOT_DIR."/template/footer.php");

?>
