<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # define encoding markers
  define("BASE64_MARKER_A",     "+");
  define("BASE64_MARKER_B",     "/");
  define("BASE64_MARKER_END",   "=");
  define("URL_BASE64_MARKER_A", "-");
  define("URL_BASE64_MARKER_B", "_");
  define("URL_ENCODE_MARKER",   "%");

  # define MySQL queries
  define("MYSQL_READ",  "SELECT COUNT(*) FROM secrets WHERE fingerprint = ?");
  define("MYSQL_WRITE", "INSERT IGNORE INTO secrets (keyid, fingerprint, time) VALUES (?, ?, CURRENT_TIMESTAMP)");

  # define page names
  define("HOW_PAGE_NAME",     "how");
  define("IMPRINT_PAGE_NAME", "imprint");
  define("PUB_PAGE_NAME",     "pub");
  define("READ_PAGE_NAME",    "read");
  define("SHARE_PAGE_NAME",   "share");

  # define parameter values
  define("PLAIN_PARAM_NAME",  "plain");
  define("SECRET_PARAM_NAME", "secret");

  # define maximum secret size
  if (defined("JUMBO_SECRETS") && JUMBO_SECRETS) {
    define("MAX_PARAM_SIZE", 16384);
  } else {
    define("MAX_PARAM_SIZE", 1024);
  }

  # define Apache Bugfix length
  define("APACHE_BUGFIX_LENGTH", 64);

  # define OpenSSL encryption fields
  define("OPENSSL_CHECKMAC",      "checkmac");
  define("OPENSSL_ENCKEY",        "enckey");
  define("OPENSSL_ENCMESSAGE",    "encmessage");
  define("OPENSSL_FULLMESSAGE",   "fullmessage");
  define("OPENSSL_KEY",           "key");
  define("OPENSSL_MAC",           "mac");
  define("OPENSSL_MACKEY",        "mackey");
  define("OPENSSL_MACMESSAGE",    "macmessage");
  define("OPENSSL_MESSAGE",       "message");
  define("OPENSSL_NONCE",         "nonce");
  define("OPENSSL_PEM_HEAD_PRIV", "-----BEGIN RSA PRIVATE KEY-----");
  define("OPENSSL_PEM_TAIL_PRIV", "-----END RSA PRIVATE KEY-----");
  define("OPENSSL_PEM_HEAD_PUB",  "-----BEGIN PUBLIC KEY-----");
  define("OPENSSL_PEM_TAIL_PUB",  "-----END PUBLIC KEY-----");
  define("OPENSSL_RSAKEYCOUNT",   "rsakeycount");
  define("OPENSSL_RSAKEYIDS",     "rsakeyids");
  define("OPENSSL_RSAKEYLENGTHS", "rsakeylengths");
  define("OPENSSL_RSAKEYS",       "rsakeys");
  define("OPENSSL_SALT",          "salt");
  define("OPENSSL_VERSION",       "version");

