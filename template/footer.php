<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # prevents cache hits with wrong CSS
  $cache_value = md5_file(__FILE__);

?>
      <!-- footer -->
    </div>

    <script src="/vendors/jquery/jquery.min.js?<?php print($cache_value); ?>" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" type="text/javascript"></script>
    <script src="/vendors/bootstrap/js/bootstrap.min.js?<?php print($cache_value); ?>" integrity="sha256-U5ZEeKfGNOja007MMD3YBI0A3OSZOQbeG6z2f2Y0hu8=" type="text/javascript"></script>
  </body>
</html>
