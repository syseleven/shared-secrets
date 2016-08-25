<?php

  # Shared-Secrets v0.3b0
  #
  # Copyright (c) 2016, SysEleven GmbH
  # All rights reserved.
  #
  # This page allows you to share a secret through a secret sharing link.
  # The secret is stored in the secret sharing link and not on the server.
  # A secret sharing link can only be used once.
  #
  # The actions can be found in ./actions/<name>.php
  #
  # The configuration can be found in ./config.php
  #
  # The pages can be found in ./pages/<name>/get.php and ./pages/<name>/post.php
  #
  # The template can be found in ./template/header.php and ./template/footer.php

  # prevent direct access
  define("SYS11_SECRETS", true);

  # store the __DIR__ constant in an additional constant
  # so that is does not change between script files
  define("ROOT_DIR", __DIR__);

  # include required defines
  require_once(ROOT_DIR."/libs/shared-secrets.def.php");

  # include required execution functions
  require_once(ROOT_DIR."/libs/shared-secrets.exec.php");

  # include required configuration
  require_once(ROOT_DIR."/config.php");

  # set default timezone because PHP dislikes to use system defaults
  date_default_timezone_set(DEFAULT_TIMEZONE);

  # prepare client IP
  $client_ip = null;
  if (LOG_IP_ADDRESS) {
    $client_ip = $_SERVER["REMOTE_ADDR"];
  }
  define("CLIENT_IP", $client_ip);

  # prepare request method
  define("REQUEST_METHOD", strtolower($_SERVER["REQUEST_METHOD"]));

  # prepare param
  $param = null;
  if (isset($_POST[PARAM_NAME])) {
    if (!empty($_POST[PARAM_NAME])) {
      $param = $_POST[PARAM_NAME];
    }
  }
  define("SECRET_PARAM", $param);

  # prepare uri
  $uri = $_SERVER["REQUEST_URI"];
  if (0 === stripos($uri, "/")) {
    $uri = substr($uri, 1);
  }
  # handle URL-encoded URIs
  if (false !== strpos($uri, BASE64_MARKER)) {
    $uri = urldecode($uri);
  }
  define("SECRET_URI", $uri);

  # prepare action name, show read page by default
  $action = READ_PAGE_NAME;
  # show share page if no URI is given
  if (empty(SECRET_URI)) {
    $action = SHARE_PAGE_NAME;
  } else {
    # show pages based on page URI
    if (in_array(SECRET_URI, array(HOW_PAGE_NAME, IMPRINT_PAGE_NAME))) {
      $action = SECRET_URI;
    }
  }
  define("SECRET_ACTION", $action);

  # only proceed when a GET or POST request is encountered
  if (in_array(REQUEST_METHOD, array("get", "post"))) {
    # import actions based on action name
    require_once(ROOT_DIR."/actions/".SECRET_ACTION.".php");

    # import pages based on action name and request method
    require_once(ROOT_DIR."/pages/".SECRET_ACTION."/".REQUEST_METHOD.".php");
  }

?>
