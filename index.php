<?php

  # Shared-Secrets v0.29b0
  #
  # Copyright (c) 2016-2021, SysEleven GmbH
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

  # include required configuration
  if (is_file(ROOT_DIR."/config/config.php")) {
    # if there is a config file then we use that
    require_once(ROOT_DIR."/config/config.php");
  } else {
    # otherwise we define the config through environment variables
    require_once(ROOT_DIR."/lib/shared-secrets.env.php");
  }

  # include required defines
  require_once(ROOT_DIR."/lib/shared-secrets.def.php");

  # include required execution functions
  require_once(ROOT_DIR."/lib/shared-secrets.exec.php");

  # prepare debug mode
  if (!defined("DEBUG_MODE")) {
    define("DEBUG_MODE", false);
  }
  if (DEBUG_MODE) {
    error_reporting(E_ALL | E_STRICT | E_NOTICE);
  } else {
    error_reporting(0);
  }
  ini_set("display_errors",         (DEBUG_MODE) ? 1 : 0);
  ini_set("display_startup_errors", (DEBUG_MODE) ? 1 : 0);
  ini_set("html_errors",            (DEBUG_MODE) ? 1 : 0);
  ini_set("track_errors",           (DEBUG_MODE) ? 1 : 0);

  # set default timezone because PHP dislikes to use system defaults
  date_default_timezone_set(DEFAULT_TIMEZONE);

  # prepare read-only mode
  if (!defined("READ_ONLY")) {
    define("READ_ONLY", false);
  }

  # prepare share-only mode
  if (!defined("SHARE_ONLY")) {
    define("SHARE_ONLY", false);
  }

  # prepare request method
  define("REQUEST_METHOD", strtolower($_SERVER["REQUEST_METHOD"]));

  # prepare plain param
  define("PLAIN_PARAM", (isset($_POST[PLAIN_PARAM_NAME]) || isset($_GET[PLAIN_PARAM_NAME])));

  # prepare secret param
  $param = null;
  if (isset($_POST[SECRET_PARAM_NAME])) {
    if (!empty($_POST[SECRET_PARAM_NAME])) {
      $param = $_POST[SECRET_PARAM_NAME];
    }
  }
  define("SECRET_PARAM", $param);

  # prepare URI
  $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
  if (false !== strpos($uri, URL_ENCODE_MARKER)) {
    $uri = urldecode($uri);
  }
  define("SECRET_URI", nolead($uri, "/"));

  # prepare action name, show read page by default
  $action = READ_PAGE_NAME;
  if (empty(SECRET_URI)) {
    # show share page if no URI is given
    $action = SHARE_PAGE_NAME;
  } elseif (in_array(SECRET_URI, array(HOW_PAGE_NAME, IMPRINT_PAGE_NAME, PUB_PAGE_NAME))) {
    # show pages based on page URI
    $action = SECRET_URI;
  }
  define("SECRET_ACTION", $action);

  # only proceed when a GET or POST request is encountered
  if (in_array(REQUEST_METHOD, array("get", "post"))) {
    # import actions based on action name
    require_once(ROOT_DIR."/actions/".SECRET_ACTION.".php");

    # import pages based on action name and request method
    require_once(ROOT_DIR."/pages/".SECRET_ACTION."/".REQUEST_METHOD.".php");
  } else {
    # return a corresponding result code
    http_response_code(405);
    header("Allow: GET, POST");
  }

