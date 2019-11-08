
  // find decrypt button
  var decrypt_button = document.getElementById("decrypt");
  if (null != decrypt_button) {
    // attach onClick event
    decrypt_button.addEventListener("click", function(){decrypt();});
  }

  // find decrypt-locally checkbox
  var decrypt_locally_checkbox = document.getElementById("decrypt-locally");
  if (null != decrypt_locally_checkbox) {
    // attach onClick event
    decrypt_locally_checkbox.addEventListener("click", function(){decrypt_locally();});
  }

  // action happening on local decryption
  async function decrypt() {
    var result = null;

    // check the length of the input
    if ((0 < document.getElementById("secret").innerHTML.length) &&
        (0 < document.getElementById("password").value.length)) {
      result = await decrypt_v00(document.getElementById("secret").innerHTML,
                                 document.getElementById("password").value);
    }

    if (null != result) {
      document.getElementById("secret").innerHTML = html_entities(result);

      document.getElementById("decrypt").disabled         = true;
      document.getElementById("decrypt-locally").disabled = true;

      document.getElementById("password").readOnly = "readonly";

      document.getElementById("decrypt-error").style.display = "none";
    } else {
      document.getElementById("decrypt-error").style.display = "block";
    }
  }

  // show/hide local decryption
  function decrypt_locally(checkbox) {
    if (document.getElementById("decrypt-locally").checked) {
      document.getElementById("decrypt").style.visibility  = "visible";
      document.getElementById("password").style.visibility = "visible";
    } else {
      document.getElementById("decrypt").style.visibility  = "hidden";
      document.getElementById("password").style.visibility = "hidden";
    }
  }

