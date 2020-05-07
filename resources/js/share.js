
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

  // find secret textarea
  var secret_textarea = document.getElementById("secret");
  if (null != secret_textarea) {
    // attach key events
    secret_textarea.addEventListener("input",    function(){update_counter();});
    secret_textarea.addEventListener("keydown",  function(){update_counter();});
    secret_textarea.addEventListener("keypress", function(){update_counter();});
    secret_textarea.addEventListener("keyup",    function(){update_counter();});
  }

  // action happening on local encryption
  async function encrypt() {
    var result = null;

    // check the length of the input
    if ((0 < document.getElementById("secret").value.length) &&
        (0 < document.getElementById("password").value.length)) {
      result = await encrypt_v00(document.getElementById("secret").value,
                                 document.getElementById("password").value);
    }

    // check the length of the output
    if ((null != result) && (MAX_PARAM_SIZE >= result.length)) {
      document.getElementById("secret").value = result;

      document.getElementById("share-secret-btn").disabled = false;

      document.getElementById("encrypt").disabled         = true;
      document.getElementById("encrypt-locally").disabled = true;

      document.getElementById("password").readOnly = "readonly";
      document.getElementById("secret").readOnly   = "readonly";

      document.getElementById("counter").style.display       = "none";
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

  // update the character counter
  // we have to count the bytes and line breaks have to be counted as two characters
  function update_counter() {
    var maxLimit  = MAX_PARAM_SIZE;
    var softLimit = Math.ceil(maxLimit-((maxLimit/4)*3-81)); // based on Base64-encoded v00 message length

    var length     = new TextEncoder("utf-8").encode(document.getElementById("secret").value).length;
    var linebreaks = (document.getElementById("secret").value.match(/\n/g) || []).length;
    var counter    = maxLimit-length-linebreaks;

    // set the counter
    document.getElementById("counter").innerHTML   = counter.toString();
    document.getElementById("counter").style.color = "#000000";

    // check if the secret is short enough for local encryption
    document.getElementById("encrypt").disabled = (softLimit > counter);
    if (document.getElementById("encrypt").disabled) {
      // change text colour to yellow
      document.getElementById("counter").style.color = "#FFAA1D";
    }

    // disable the submit button if the secret is too long
    document.getElementById("share-secret-btn").disabled = (0 > counter);
    if (document.getElementById("share-secret-btn").disabled) {
      // change text colour to red
      document.getElementById("counter").style.color = "#C40233";
    }
  }

