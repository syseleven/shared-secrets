<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  function read_secret($secret) {
    $result = null;

    # handle secret decoding
    $secret = url_base64_decode($secret);

    # only proceed when the secret is not empty
    if (!empty($secret)) {
      # get the checksum of the URI content
      $checksum = hash("sha256", $secret);

      if (!empty($checksum)) {
        # connect to mysql server
        $mysql = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);

        if (null === mysqli_connect_error()) {
          # prepare the read statement
          $select = mysqli_prepare($mysql, MYSQL_READ);

          if (false !== $select) {
            # set string parameter of read statement to $checksum
            if (mysqli_stmt_bind_param($select, "s", $checksum)) {
              # execute statement
              if (mysqli_stmt_execute($select)) {
                # bind result variables
                if (mysqli_stmt_bind_result($select, $fingerprint_found)) {
                  # fetch result variables
                  if (true === mysqli_stmt_fetch($select)) {
                    # close select statement to be able to insert later on
                    mysqli_stmt_close($select);
                    $select = null;

                    # only proceed if the fingerprint has not been found
                    if (0 === $fingerprint_found) {
                      # decrypt secret
                      if (GNUPG_PECL) {
                        $decrypted_secret = decrypt_pecl(base64_decode($secret, true), GPG_KEY_FINGERPRINT, GPG_HOME_DIR, GPG_PASSPHRASE_FILE);
                      } else {
                        $decrypted_secret = decrypt(base64_decode($secret, true), GPG_HOME_DIR, GPG_PASSPHRASE_FILE);
                      }  

                      if (null !== $decrypted_secret) {
                        # prepare the write statement
                        $insert = mysqli_prepare($mysql, MYSQL_WRITE);

                        if (false !== $insert) {
                          # only set temporarily
                          $client_ip = CLIENT_IP;

                          # set string parameter of write statement to $checksum and client IP
                          if (mysqli_stmt_bind_param($insert, "ss", $checksum, $client_ip)) {
                            # execute statement
                            if (mysqli_stmt_execute($insert)) {
                              # return secret
                              $result = htmlentities($decrypted_secret);

                              # close insert statement before proceeding
                              mysqli_stmt_close($insert);
                              $insert = null;
                            }
                          }

                          # close insert statement
                          if (null !== $insert) {
                            mysqli_stmt_close($insert);
                          }
                        }
                      }
                    } else {
                      $result = "<strong>ERROR: SECRET HAS ALREADY BEEN RETRIEVED.</strong>";
                    }
                  }
                }
              }
            }

            # close select statement
            if (null !== $select) {
              mysqli_stmt_close($select);
            }
          }

          # close mysql connection
          mysqli_close($mysql);
        }
      }
    }

    # set default result if non is given
    if (null === $result) {
      $result = "<strong>ERROR: AN UNKNOWN ERROR OCCURED.</strong>";
    }

    return $result;
  }

?>
