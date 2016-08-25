<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  # define page title
  define("PAGE_TITLE", "Share a Secret.");

  # include header
  require_once(ROOT_DIR."/template/header.php");

  if (ENABLE_PASSWORD_PROTECTION) {

?>

    <noscript>
      <div class="alert alert-warning">
        <strong>Warning!</strong> You don't have JavaScript enabled. You will not be able to share password-protected secrets.
      </div>
    </noscript>
    <div class="alert alert-danger" id="encrypt-error" style="display: none;">
      <strong>Error!</strong> Local encryption failed.
    </div>

<?php

  }

?>

  <form role="form" action="/<?php print(htmlentities(urlencode(SECRET_URI))); ?>" method="post">
    <label for="secret"><h1>Share a Secret:</h1></label>
    <input type="text" autocomplete="off" class="form-control" id="secret" name="secret" maxlength="512" size="512" />
    <button type="submit" class="btn btn-default pull-right" id="share-secret-btn" name="share-secret-btn" style="margin-top: 0.5em;">Share the Secret!</button>
  </form>

<?php

  if (ENABLE_PASSWORD_PROTECTION) {

?>

    <label class="checkbox-inline" for="encrypt-locally"><input type="checkbox" autocomplete="off" id="encrypt-locally" value="" onclick="encrypt_locally();" />Password-protected: </label>
    <input type="password" autocomplete="off" class="form-control" id="password" maxlength="64" size="32" style="display: inline; margin-top: 0.5em; visibility: hidden; width: 25%;" />
    <input type="button" class="btn btn-default" id="encrypt" style="visibility: hidden;" value="Protect!" onclick="encrypt();" />

    <script src="/vendors/asmcrypto/asmcrypto.js" type="text/javascript"></script>
    <script src="/vendors/buffer/index.js" type="text/javascript"></script>

    <script type="text/javascript">
      // action happening on local encryption
      function encrypt() {
        var result = encrypt_secret(document.getElementById("secret").value,
                                    document.getElementById("password").value,
                                    "<?php print(base64_encode(openssl_random_pseudo_bytes(32))); ?>");

        if (null != result) {
          document.getElementById("secret").value = result;

          document.getElementById("share-secret-btn").disabled = false;

          document.getElementById("encrypt").disabled         = true;
          document.getElementById("encrypt-locally").disabled = true;

          document.getElementById("password").readOnly = "readonly";
          document.getElementById("secret").readOnly   = "readonly";

          document.getElementById("encrypt-error").style.display = "none";
        } else {
          document.getElementById("encrypt-error").style.display = "block";
        }
      }

      // show/hide local encryption
      function encrypt_locally(checkbox) {
        if (document.getElementById("encrypt-locally").checked) {
          document.getElementById("share-secret-btn").disabled = true;

          document.getElementById("encrypt").style.visibility  = "visible";
          document.getElementById("password").style.visibility = "visible";
        } else {
          document.getElementById("share-secret-btn").disabled = false;

          document.getElementById("encrypt").style.visibility  = "hidden";
          document.getElementById("password").style.visibility = "hidden";
        }
      }

      function encrypt_secret(secret, password, base64Salt) {
        // these variables configure the PBKDF2 call
        var outputLength = 32;
        var workFactor   = 1024;

        // retrieve salt from Base64-encoded salt
        var salt = (new buffer.SlowBuffer(base64Salt, "base64")).toArrayBuffer();

        // derive encryption key
        var pbkdf2Key = asmCrypto.PBKDF2_HMAC_SHA256.bytes(password, salt, workFactor, outputLength);

        try {
          // encrypt secret with derived encryption key
          var aesResult = asmCrypto.AES_GCM.encrypt(secret, pbkdf2Key, new Uint8Array(12));
        } catch (err) {
          var aesResult = null;
        }

        if (null != aesResult) {
          // create Base64-encoded encrypted secret
          var base64Secret = (new buffer.SlowBuffer(aesResult)).toString("base64");

          // return concatenation of Base64-encoded salt and Base64-encoded encrypted secret
          return (base64Salt + base64Secret);
        } else {
          return aesResult;
        }
      }
    </script>

<?php

  }

  # include footer
  require_once(ROOT_DIR."/template/footer.php");

?>
