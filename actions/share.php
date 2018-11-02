<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  function share_secret($secret) {
    $result = null;

    # only proceed when the secret is not empty
    if (!empty($secret)) {
      # only proceed when the secret is not too long
      if (MAX_PARAM_SIZE >= strlen($secret)) {
        if (GNUPG_PECL) {
          $encrypted_secret = encrypt_pecl($secret, GPG_KEY_FINGERPRINT, GPG_HOME_DIR);
        } else {
          $encrypted_secret = encrypt($secret, GPG_KEY_FINGERPRINT, GPG_HOME_DIR);
        }

        if (null !== $encrypted_secret) {
          # return the secret sharing URL
          $result = htmlentities(SECRET_SHARING_URL.apache_bugfix_encode(url_base64_encode(base64_encode($encrypted_secret))));
        }
      } else {
        $result = "<strong>ERROR: THE SECRET MUST BE SMALLER THAN ".MAX_PARAM_SIZE." CHARACTERS.</strong>";
      }
    } else {
      $result = "<strong>ERROR: THE SECRET MUST NOT BE EMPTY.</strong>";
    }

    # set default result if non is given
    if (null === $result) {
      $result = "<strong>ERROR: AN UNKNOWN ERROR OCCURED.</strong>";
    }

    return $result;
  }

?>
