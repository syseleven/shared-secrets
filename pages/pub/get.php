<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # set correct content type
  header("Content-Type: application/x-pem-file");

  # output public key pem
  print(get_public_key());

