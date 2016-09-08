<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  function share_secret($secret) {
    $result = null;

    # only proceed when the secret is not empty
    if (!empty($secret)) {
      # only proceed when the secret is not too long
      if (MAX_PARAM_SIZE >= strlen($secret)) {
        $encrypted_secret = encrypt($secret, GPG_KEY_FINGERPRINT);

        if (null !== $encrypted_secret) {
          # remove everything from encrypted secret that is not necessary
          $stripped_secret = strip_message($encrypted_secret);

          if (null !== $stripped_secret) {
            # return the secret sharing URL
            $result = htmlentities(SECRET_SHARING_URL.urlencode(url_base64_encode($stripped_secret)));
          }
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
