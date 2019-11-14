<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  function get_public_key(&$error) {
    $result = null;
    $error  = false;

    # only proceed when the read-only mode is not enabled
    if (!READ_ONLY) {
      # for shared-secrets we only support encryption with one key
      $keys   = array_keys(RSA_PRIVATE_KEYS);
      $pubkey = open_pubkey(RSA_PRIVATE_KEYS[$keys[count($keys)-1]]);
      if (null !== $pubkey) {
        try {
          $result = get_keypem($pubkey);
        } finally {
          openssl_pkey_free($pubkey);
        }
      } else {
        if (DEBUG_MODE) {
          $error = "Public key could not be read.";
        }
      }
    } else {
      $error = "The creation of secret sharing links is disabled.";
    }

    # set default error if non is given
    if ((null === $result) && (false === $error)) {
      $error = "An unknown error occured.";
    }

    return $result;
  }

