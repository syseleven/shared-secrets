<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  function read_secret($secret, &$error = null) {
    $result = null;
    $error  = false;

    # only proceed when the share-only mode is not enabled
    if (!SHARE_ONLY) {
      # handle secret decoding
      $secret = parse_secret_url($secret);

      # only proceed when the secret is not empty
      if (!empty($secret)) {
        $keys       = array_keys(RSA_PRIVATE_KEYS);
        $recipients = [];
        foreach ($keys as $key) {
          if (is_privkey(RSA_PRIVATE_KEYS[$key])) {
            # open the private key
            $privkey = open_privkey(RSA_PRIVATE_KEYS[$key]);
            if (null !== $privkey) {
              $recipients[] = $privkey;
            }
          }
        }

        if (0 < count($recipients)) {
          try {
            $decrypted_secret = decrypt_v01($secret, $recipients, $decrypt_error, $keyid, $fingerprint);
          } finally {
            $keys = array_keys($recipients);
            foreach ($keys as $key) {
              openssl_pkey_free($recipients[$key]);
            }

            zeroize_array($recipients);
          }

          if (null !== $decrypted_secret) {
            $link = mysqli_init();
            $link->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
            $link->ssl_set(NULL, NULL, "certs.pem", NULL, NULL);
            if ($link->real_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB, MYSQL_PORT)) {
              try {
                if ($statement = mysqli_prepare($link, MYSQL_WRITE)) {
                  $fingerprint = bin2hex($fingerprint);
                  $keyid       = bin2hex($keyid);

                  if (mysqli_stmt_bind_param($statement, "ss", $keyid, $fingerprint)) {
                    if (mysqli_stmt_execute($statement)) {
                      if (1 === mysqli_affected_rows($link)) {
                        $result = $decrypted_secret;
                      } else {
                        $error = "Secret has already been retrieved.";
                      }
                    } else {
                      if (DEBUG_MODE) {
                        $error = "Insert statement could not be executed";
                      }
                    }
                  } else {
                    if (DEBUG_MODE) {
                      $error = "Insert statement parameters could not be bound.";
                    }
                  }
                } else {
                  if (DEBUG_MODE) {
                    $error = "Insert statement could not be prepared.";
                  }
                }
              } finally {
                mysqli_close($link);
              }
            } else {
              if (DEBUG_MODE) {
                $error = "Database connection could not be established.";
              }
            }
          } else {
            if (DEBUG_MODE) {
              $error = "Decryption failed: $decrypt_error";
            }
          }
        } else {
          if (DEBUG_MODE) {
            $error = "Private key could not be read.";
          }
        }
      } else {
        if (DEBUG_MODE) {
          $error = "The secret must not be empty.";
        }
      }
    } else {
      $error = "The retrieval of secret sharing links is disabled.";
    }

    # set default error if non is given
    if ((null === $result) && (false === $error)) {
      $error = "An unknown error occured.";
    }

    return $result;
  }
