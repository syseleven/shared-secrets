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

<?php

  }

?>

  <h1>Share a Secret:</h1>
  <p><pre id="share-secret"><?php print(share_secret(SECRET_PARAM)); ?></pre>
     <button type="btn" class="btn btn-default pull-right" data-clipboard-target="#share-secret" id="copy-to-clipboard">Copy to Clipboard!</button></p>

<?php

  # include footer
  require_once(ROOT_DIR."/template/footer.php");

?>
