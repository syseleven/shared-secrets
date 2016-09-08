<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # define GPG message parts
  define("GPG_MESSAGE_COMMENT",         "Comment:");
  define("GPG_MESSAGE_COMMENT_DUMMY",   "Dummy");
  define("GPG_MESSAGE_LINE_LENGTH",     64);
  define("GPG_MESSAGE_LINE_SEPARATOR",  "\n");
  define("GPG_MESSAGE_PARTS_MARKER",    "=");
  define("GPG_MESSAGE_VALUE_SEPARATOR", " ");
  define("GPG_MESSAGE_PREFIX",          "-----BEGIN PGP MESSAGE-----");
  define("GPG_MESSAGE_SUFFIX",          "-----END PGP MESSAGE-----");

  # define stream buffer size
  define("STREAM_BUFFER", 1024);

  # define MySQL parameters
  define("MYSQL_READ",  "SELECT COUNT(*) FROM secrets WHERE fingerprint = ?");
  define("MYSQL_WRITE", "INSERT INTO secrets VALUES (?, ?, CURRENT_TIMESTAMP)");

  # define page names
  define("HOW_PAGE_NAME",     "how");
  define("IMPRINT_PAGE_NAME", "imprint");
  define("READ_PAGE_NAME",    "read");
  define("SHARE_PAGE_NAME",   "share");

  # define parameter name
  define("BASE64_MARKER_A",     "+");
  define("BASE64_MARKER_B",     "/");
  define("MAX_PARAM_SIZE",      512);
  define("PARAM_NAME",          "secret");
  define("URL_BASE64_MARKER_A", "-");
  define("URL_BASE64_MARKER_B", "_");
  define("URL_ENCODE_MARKER",   "%");

  # define 

?>
