<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

?>
      <!-- footer -->
    </div>

    <script src="/vendors/jquery/jquery.min.js"></script>
    <script src="/vendors/clipboard/clipboard.min.js"></script>
    <script src="/vendors/bootstrap/js/bootstrap.min.js"></script>

    <script>
      // find copy-to-clipboard button
      var copy_to_clipboard = document.getElementById("copy-to-clipboard");

      if (null != copy_to_clipboard) {
        // check if we're confronted with a Safari browser
        if ((-1 != navigator.userAgent.indexOf("Safari")) &&
            (-1 == navigator.userAgent.indexOf("Android")) &&
            (-1 == navigator.userAgent.indexOf("Chrome"))) {
          // hide copy-to-clipboard button, because it is not supported
          copy_to_clipboard.style.display = "none";
        } else {
          // initialize clipboard feature
          var clipboard = new Clipboard('.btn');
        }
      }
    </script>
  </body>
</html>
