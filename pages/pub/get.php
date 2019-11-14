<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # define page title
  define("PAGE_TITLE", "Download the Public Key.");

  $pubkey = get_public_key($error);

  # set the correct response code on error
  if ((null === $pubkey) || (false !== $error)) {
    http_response_code(403);
  }

  if (PLAIN_PARAM) {
    # set correct content type
    header("Content-Type: application/x-pem-file");

    if ((null !== $pubkey) && (false === $error)) {
      print($pubkey);
    }
  } else {
    if ((null !== $pubkey) && (false === $error)) {
      $pubkey = html($pubkey);
    } else {
      $pubkey = "<strong>ERROR:</strong> ".html($error);
    }

    # include header
    require_once(ROOT_DIR."/template/header.php");

    # prevents cache hits with wrong CSS
    $cache_value = md5_file(__FILE__);

?>

  <h1>Download the Public Key:</h1>
  <p><pre id="pubkey"><?= $pubkey ?></pre></p>

  <link href="/resources/css/pub.css?<?= $cache_value ?>" integrity="sha256-wPffseYftFWOZpxIAfjpeKOz9Cac5cSXZGlXKaUc7bA=" rel="stylesheet" type="text/css" />

<?php

    # include footer
    require_once(ROOT_DIR."/template/footer.php");
  }

