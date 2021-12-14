<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  function env($name, $default = null) {
    $result = getenv($name);

    # set the default if the environment variable isn't set
    if (false === $result) {
      $result = $default;
    }

    return $result;
  }

  function checkbool($string) {
    return filter_var($string, FILTER_VALIDATE_BOOLEAN);
  }

  function load_dot_env($filename) {
    # read the .env file
    $dotenv = parse_ini_file($filename);
    if (false !== $dotenv) {
      foreach ($dotenv as $key => $value) {
        # only set environment variables that are not already set
        if (false === getenv($key)) {
          putenv($key."=".$value);
        }
      }
    }
  }

  function split_rsa_keys($string) {
    $result = [];

    if (false !== preg_match_all("@(?<rsakeys>-----BEGIN (?:RSA )?(?:PRIVATE|PUBLIC) KEY-----(?:.+?)-----END (?:RSA )?(?:PRIVATE|PUBLIC) KEY-----)@is",
                                 $string, $matches)) {
      if (array_key_exists("rsakeys", $matches)) {
        # cleanup strings
        foreach ($matches["rsakeys"] as $match_key => $match_value) {
          $lines = explode("\n", $match_value);
          foreach ($lines as $line_key => $line_value) {
            $lines[$line_key] = trim($line_value);
          }
          $matches["rsakeys"][$match_key] = implode("\n", $lines);
        }

        $result = $matches["rsakeys"];
      }
    }

    return $result;
  }

  # load a .env file if it exists
  if (is_file(ROOT_DIR."/.env")) {
    load_dot_env(ROOT_DIR."/.env");
  }

  # this is an array containing the supported RSA privated keys for encryption and decryption, the LAST RSA private key
  # within the array is used to encrypt new secrets while all RSA private keys are used to decrypt secrets, this allows
  # for smooth key rollovers; for share-only instances it is sufficient to set the RSA public key of the corresponding
  # read-only instance here
  define("RSA_PRIVATE_KEYS", split_rsa_keys(env("RSA_PRIVATE_KEYS", null)));

  # this is the title of the service, it is shown in header of all pages
  define("SERVICE_TITLE", env("SERVICE_TITLE", "Shared-Secrets"));

  # this is the full path to the secret sharing service, the encrypted secret will be appended to this string
  define("SECRET_SHARING_URL", env("SECRET_SHARING_URL", "https://localhost.local/"));

  # this is the text of the imprint link
  define("IMPRINT_TEXT", env("IMPRINT_TEXT", null));

  # this is the URL the imprint link shall forward to
  define("IMPRINT_URL", env("IMPRINT_URL", "https://localhost.local/"));

  # this is the MySQL configuration, do not forget to create the corresponding database and the following table:
  # > CREATE TABLE secrets ( keyid VARCHAR(64), fingerprint VARCHAR(64), time TIMESTAMP, PRIMARY KEY (keyid, fingerprint) );
  define("MYSQL_HOST", env("MYSQL_HOST", "localhost"));
  define("MYSQL_PORT", intval(env("MYSQL_PORT", 3306)));
  define("MYSQL_USER", env("MYSQL_USER", null));
  define("MYSQL_PASS", env("MYSQL_PASS", null));
  define("MYSQL_DB",   env("MYSQL_DB",   null));

  # this enables or disables the debug mode of the instance
  define("DEBUG_MODE", checkbool(env("DEBUG_MODE", false)));

  # this is the default timezone for the execution of the script
  define("DEFAULT_TIMEZONE", env("DEFAULT_TIMEZONE", "Europe/Berlin"));

  # this enables or disables the read-only mode of the instance,
  # by using the read-only mode you need another instance to create secret sharing links,
  # this separation can be useful if you only want to be internally able to create links
  define("READ_ONLY", checkbool(env("READ_ONLY", false)));

  # this enables or disables the share-only mode of the instance,
  # by using the share-only mode you need another instance to read secret sharing links,
  # this separation can be useful if you only want to be internally able to create links
  define("SHARE_ONLY", checkbool(env("SHARE_ONLY", false)));

  # this enables or disables the jumbo secret support,
  # jumbo secrets can be up to 16384 bytes (16kb) in size,
  # jumbo secret sharing links that exceed 2048 bytes (2k) in size will most likely be incompatible with older Internet Explorer versions
  define("JUMBO_SECRETS", checkbool(env("JUMBO_SECRETS", false)));

