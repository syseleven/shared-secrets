<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # redirect to configured imprint URL
  redirect_page(IMPRINT_URL);

