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
function encrypt() {
  var result = encrypt_secret(document.getElementById("secret").value,
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

function encrypt_secret(secret, password) {
  // these variables configure the PBKDF2 call
  var outputLength = 32;
  var workFactor   = 1024;

  // disable asmCrypto warning
  asmCrypto.random.skipSystemRNGWarning = true;

  // retrieve salt from PRNG
  var salt = new Uint8Array(32);
  asmCrypto.getRandomValues(salt);

  // derive encryption key
  var pbkdf2Key = asmCrypto.PBKDF2_HMAC_SHA256.bytes(password, salt, workFactor, outputLength);

  try {
    // encrypt secret with derived encryption key
    var aesResult = asmCrypto.AES_GCM.encrypt(secret, pbkdf2Key, new Uint8Array(12));
  } catch (err) {
    var aesResult = null;
  }

  if (null != aesResult) {
    // create Base64 encoded salt
    var base64Salt = (new buffer.SlowBuffer(salt)).toString("base64");

    // create Base64 encoded encrypted secret
    var base64Secret = (new buffer.SlowBuffer(aesResult)).toString("base64");

    // return concatenation of Base64 encoded salt and Base64 encoded encrypted secret
    return (base64Salt + base64Secret);
  } else {
    return aesResult;
  }
}
