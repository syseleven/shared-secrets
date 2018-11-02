<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # forcefully disable GnuPG PECL
  define("DISABLE_GNUPG_PECL", true);

  # define encoding markers
  define("BASE64_MARKER_A",     "+");
  define("BASE64_MARKER_B",     "/");
  define("BASE64_MARKER_END",   "=");
  define("URL_BASE64_MARKER_A", "-");
  define("URL_BASE64_MARKER_B", "_");
  define("URL_ENCODE_MARKER",   "%");

  # define GnuPG error message
  define("GPG_MDC_ERROR", "gpg: WARNING: message was not integrity protected");

  # define MySQL queries
  define("MYSQL_READ",  "SELECT COUNT(*) FROM secrets WHERE fingerprint = ?");
  define("MYSQL_WRITE", "INSERT INTO secrets VALUES (?, ?, CURRENT_TIMESTAMP)");

  # define page names
  define("HOW_PAGE_NAME",     "how");
  define("IMPRINT_PAGE_NAME", "imprint");
  define("READ_PAGE_NAME",    "read");
  define("SHARE_PAGE_NAME",   "share");

  # define parameter values
  define("MAX_PARAM_SIZE",    512);
  define("PLAIN_PARAM_NAME",  "plain");
  define("SECRET_PARAM_NAME", "secret");

  # define stream buffer size
  define("STREAM_BUFFER", 1024);

  # define Apache Bugfix length
  define("APACHE_BUGFIX_LENGTH", 200);

?>
