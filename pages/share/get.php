<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # define page title
  define("PAGE_TITLE", "Share a Secret.");

  # include header
  require_once(ROOT_DIR."/template/header.php");

?>

  <form role="form" action="/<?php print(htmlentities(urlencode(SECRET_URI))); ?>" method="post">
    <div class="form-group">
      <label for="secret"><h1>Share a Secret:</h1></label>
      <input type="text" autocomplete="off" class="form-control" id="secret" name="secret" maxlength="512" size="512" />
    </div>
    <button type="submit" class="btn btn-default pull-right">Share the Secret!</button>
  </form>

<?php

  # include footer
  require_once(ROOT_DIR."/template/footer.php");

?>
