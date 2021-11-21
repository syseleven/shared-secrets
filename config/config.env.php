<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # load composer generated autoload
  require_once( ROOT_DIR . '/vendors/composer/autoload.php' );
  # initiate dotenv
  if ( ! file_exists( ROOT_DIR . '/.env' ) ) {
    $file = fopen( ROOT_DIR . '/.env', "w" );
    fwrite( $file, "" );
    fclose( $file );
  }
  $dotenv = Dotenv\Dotenv::createImmutable( ROOT_DIR );
  $dotenv->load();

  function checkBoolEnv( $value ) {
    if ( is_bool( $value ) ) {
      return $value;
    }
    elseif ( in_array( strtolower( $value ), [ '1', 'yes', 'y', 'true' ] ) ) {
      return true;
    }
    return false;
  }

  # this is an array containing the supported RSA privated keys for encryption and decryption, the LAST RSA private key
  # within the array is used to encrypt new secrets while all RSA private keys are used to decrypt secrets, this allows
  # for smooth key rollovers; for share-only instances it is sufficient to set the RSA public key of the corresponding
  # read-only instance here

  $nokey="-----BEGIN RSA PRIVATE KEY-----
...
...
...
-----END RSA PRIVATE KEY-----";
  $pattern = '/-{5}BEGIN.*?-{5}.+?-{5}END.*?-{5}/si';
  preg_match_all($pattern, env( "RSA_PRIVATE_KEYS", $nokey ), $keys);
  define("RSA_PRIVATE_KEYS", $keys[0] );

  # this is the title of the service, it is shown in header of all pages
  define("SERVICE_TITLE", env("SERVICE_TITLE", "Shared-Secrets"));

  # this is the full path to the secret sharing service, the encrypted secret will be appended to this string
  $realUrl = (
    (
      isset($_SERVER['HTTPS']) &&
      $_SERVER['HTTPS'] != "off"
    ) ||
    (
      isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
      strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0
    )
  ) ? "https" : "http";
  $realUrl .= "://".$_SERVER['HTTP_HOST'];
  define("SECRET_SHARING_URL", env("SECRET_SHARING_URL", $realUrl));

  # this is the text of the imprint link
  define("IMPRINT_TEXT", env("IMPRINT_TEXT", null));

  # this is the URL the imprint link shall forward to
  define("IMPRINT_URL", env("IMPRINT_URL", "https://localhost.local/"));

  # this is the MySQL configuration, do not forget to create the corresponding database and the following table:
  # > CREATE TABLE secrets ( keyid VARCHAR(64), fingerprint VARCHAR(64), time TIMESTAMP, PRIMARY KEY (keyid, fingerprint) );
  define("MYSQL_HOST",   env("MYSQL_HOST", "localhost"));
  define("MYSQL_PORT",   env("MYSQL_PORT", 3306));
  define("MYSQL_USER",   env("MYSQL_USER", "<SET THE MYSQL USER!!!>"));
  define("MYSQL_PASS",   env("MYSQL_PASS", "<SET THE MYSQL PASSWORD!!!>"));
  define("MYSQL_DB",     env("MYSQL_DB",   "<SET THE MYSQL DATABASE!!!>"));

  # this enables or disables the debug mode of the instance
  define("DEBUG_MODE", checkBoolEnv(env("DEBUG_MODE", false)));

  # this is the default timezone for the execution of the script
  define("DEFAULT_TIMEZONE", env("DEFAULT_TIMEZONE", "Europe/Berlin"));

  # this enables or disables the read-only mode of the instance,
  # by using the read-only mode you need another instance to create secret sharing links,
  # this separation can be useful if you only want to be internally able to create links
  define("READ_ONLY", checkBoolEnv(env("READ_ONLY", false)));

  # this enables or disables the share-only mode of the instance,
  # by using the share-only mode you need another instance to read secret sharing links,
  # this separation can be useful if you only want to be internally able to create links
  define("SHARE_ONLY", checkBoolEnv(env("SHARE_ONLY", false)));

  # this enables or disables the jumbo secret support,
  # jumbo secrets can be up to 16384 bytes (16kb) in size,
  # jumbo secret sharing links that exceed 2048 bytes (2k) in size will most likely be incompatible with older Internet Explorer versions
  define("JUMBO_SECRETS", checkBoolEnv(env("JUMBO_SECRETS", false)));
