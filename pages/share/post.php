<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # define page title
  define("PAGE_TITLE", "Share a Secret.");

  $secret = share_secret(SECRET_PARAM, $error);

  # set the correct response code on error
  if ((null === $secret) || (false !== $error)) {
    http_response_code(403);
  }

  if (PLAIN_PARAM) {
    # set correct content type
    header("Content-Type: text/plain");

    if ((null !== $secret) && (false === $error)) {
      print($secret);
    }
  } else {
    if ((null !== $secret) && (false === $error)) {
      $secret = html($secret);
    } else {
      $secret = "<strong>ERROR:</strong> ".html($error);
    }

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

  <h1>Share a Secret:</h1>
  <p><pre id="secret"><?= $secret ?></pre></p>

  <link href="/resources/css/share.css?<?= $cache_value ?>" integrity="sha256-EYu1Dc10IDi0yUOyV55YWmCKWfVlBj1rTMk/AsbViKE=" rel="stylesheet" type="text/css" />

  <script src="/resources/js/norepost.js?<?= $cache_value ?>" integrity="sha256-SdShL5XtGY7DRT4OatFFRS1b3QdADS22I8eEP1GA/As=" type="text/javascript"></script>

<?php

    # include footer
    require_once(ROOT_DIR."/template/footer.php");
  }

