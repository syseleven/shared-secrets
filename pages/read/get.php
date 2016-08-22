<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # define page title
  define("PAGE_TITLE", "Read a Secret.");

  # include header
  require_once(ROOT_DIR."/template/header.php");

?>

  <h1>Read a Secret:</h1>
  <form role="form" action="/<?php print(htmlentities(urlencode(SECRET_URI))); ?>" method="post">
    <div class="form-group">
      <pre><?php print(htmlentities(SECRET_URI)); ?></pre>
    </div>
    <button type="submit" class="btn btn-default pull-right">Read the Secret!</button>
  </form>

<?php

  # include footer
  require_once(ROOT_DIR."/template/footer.php");

?>
