<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  function get_public_key() {
    $result = null;

    # for shared-secrets we only support encryption with one key
    $keys   = array_keys(RSA_PRIVATE_KEYS);
    $pubkey = open_pubkey(RSA_PRIVATE_KEYS[$keys[count($keys)-1]]);
    if (null !== $pubkey) {
      try {
        $result = get_keypem($pubkey);
      } finally {
        openssl_pkey_free($pubkey);
      }
    }

    return $result;
  }

