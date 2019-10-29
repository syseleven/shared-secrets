
  // find encrypt button
  var encrypt_button = document.getElementById("encrypt");
  if (null != encrypt_button) {
    // attach onClick event
    encrypt_button.addEventListener("click", function(){encrypt();});
  }

  // find encrypt-locally checkbox
  var encrypt_locally_checkbox = document.getElementById("encrypt-locally");
  if (null != encrypt_locally_checkbox) {
    // attach onClick event
    encrypt_locally_checkbox.addEventListener("click", function(){encrypt_locally();});
  }

  // action happening on local encryption
  async function encrypt() {
    var result = await encrypt_v00(document.getElementById("secret").value,
                                   document.getElementById("password").value);

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
  function encrypt_locally() {
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

