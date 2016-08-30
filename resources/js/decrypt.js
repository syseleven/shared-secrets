// action happening on local decryption
function decrypt() {
  var result = decrypt_secret(document.getElementById("secret").innerHTML,
                              document.getElementById("password").value);

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

// prevent code injection through locally decrypted secret
function html_entities(content) {
  return content.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
}

function decrypt_secret(concatSecret, password) {
  // these variables configure the PBKDF2 call
  var outputLength = 32;
  var workFactor   = 1024;

  // split concatenation of Base64-encoded salt and Base64-encoded encrypted secret
  var base64Salt   = concatSecret.substring(0, 44);
  var base64Secret = concatSecret.substring(44);

  // retrieve plain salt from Base64-encoded salt
  var salt = (new buffer.SlowBuffer(base64Salt, "base64")).toArrayBuffer();

  // retrieve plain secret from Base64-encoded encrypted secret
  var secret = (new buffer.SlowBuffer(base64Secret, "base64")).toArrayBuffer();

  // derive decryption key
  var pbkdf2Key = asmCrypto.PBKDF2_HMAC_SHA256.bytes(password, salt, workFactor, outputLength);

  try {
    // decrypt secret with derived decryption key
    var aesResult = asmCrypto.AES_GCM.decrypt(secret, pbkdf2Key, new Uint8Array(12));
  } catch(err) {
    var aesResult = null;
  }

  if (null != aesResult) {
    // return UTF-8-encoded decrypted secret
    return (new buffer.SlowBuffer(aesResult)).toString("utf-8");
  } else {
    return aesResult;
  }
}
