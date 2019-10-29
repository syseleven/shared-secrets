<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  function redirect_page($target_url) {
    # set the return code to temporary redirect
    http_response_code(302);

    # set the redirect URL
    header("Location: ".$target_url);
  }

