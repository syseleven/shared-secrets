<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # define page title
  define("PAGE_TITLE", "Read a Secret.");

  # include header
  require_once(ROOT_DIR."/template/header.php");

?>

  <h1>Read a Secret:</h1>
  <p><pre id="read-secret"><?php print(read_secret(SECRET_URI)); ?></pre>
     <button type="btn" class="btn btn-default" data-clipboard-target="#read-secret" id="copy-to-clipboard">Copy to Clipboard!</button></p>

<?php

  # include footer
  require_once(ROOT_DIR."/template/footer.php");

?>
