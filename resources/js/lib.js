
  // decrypt a message with a key and a nonce via AES with the Web Cryptography API
  async function aesctr_decrypt(message, key, nonce) {
    return await crypto.subtle.importKey("raw", key, {name: "AES-CTR"}, false, ["decrypt"]
    ).then(
      function(key) {
        return crypto.subtle.decrypt({"name": "AES-CTR", "counter": nonce, "length": 128}, key, message);
      }
    ).catch(
      function(error) {
        return null;
      }
    );
  }

  // encrypt a message with a key and a nonce via AES with the Web Cryptography API
  async function aesctr_encrypt(message, key, nonce) {
    return await crypto.subtle.importKey("raw", key, {name: "AES-CTR"}, false, ["encrypt"]
    ).then(
      function(key) {
        return crypto.subtle.encrypt({"name": "AES-CTR", "counter": nonce, "length": 128}, key, message);
      }
    ).catch(
      function(error) {
        return null;
      }
    );
  }

  // compare two arrays
  function compare(arraya, arrayb) {
    result = (arraya.length == arrayb.length);

    if (result) {
      for (i = 0; i < arraya.length; i++) {
        result = (result && (arraya[i] == arrayb[i]));
      }
    }

    return result;
  }

  // decrypt a message with a password
  async function decrypt_v00(message, password) {
    var result = null;

    // convert string to array
    password = new TextEncoder("utf-8").encode(password);

    var fullMessage = string2array(atob(message));

    if (82 <= fullMessage.length) {
      var macMessage = fullMessage.slice(  0, -32);
      var mac        = fullMessage.slice(-32);

      var version    = macMessage.slice( 0,  1);
      var salt       = macMessage.slice( 1, 33);
      var nonce      = macMessage.slice(33, 49);
      var encMessage = macMessage.slice(49);

      if (0 == version[0]) {
        var key = await pbkdf2(password, salt);

        if (null != key) {
          var encKey = await hmac(new TextEncoder("utf-8").encode("enc"), key);
          var macKey = await hmac(new TextEncoder("utf-8").encode("mac"), key);

          if ((null != encKey) && (null != macKey)) {
            var checkMac = await hmac(macMessage, macKey);

            if (null != checkMac) {
              // convert to correct type
              checkMac = new Uint8Array(checkMac);

              if (compare(checkMac, mac)) {
                var content = await aesctr_decrypt(encMessage, encKey, nonce);

                if (null != content) {
                  result = new TextDecoder().decode(content);
                }
              }
            }
          }
        }
      }
    }

    return result;
  }

  // encrypt a message with a password
  async function encrypt_v00(message, password) {
    var result = null;

    // convert strings to array
    message  = new TextEncoder("utf-8").encode(message);
    password = new TextEncoder("utf-8").encode(password);

    var version = hex2array("00");
    var nonce   = hex2array("00000000"+(Math.floor(Date.now() / 1000).toString(16))+"0000000000000000");
    var salt    = crypto.getRandomValues(new Uint8Array(32));
    var key     = await pbkdf2(password, salt);

    if (null != key) {
      var encKey = await hmac(new TextEncoder("utf-8").encode("enc"), key);
      var macKey = await hmac(new TextEncoder("utf-8").encode("mac"), key);

      if ((null != encKey) && (null != macKey)) {
        var encMessage = await aesctr_encrypt(message, encKey, nonce);

        if (null != encMessage) {
          // convert to correct type
          encMessage = new Uint8Array(encMessage);

          var macMessage = new Uint8Array(version.length + salt.length + nonce.length + encMessage.length);
          macMessage.set(version,    0);
          macMessage.set(salt,       version.length);
          macMessage.set(nonce,      version.length + salt.length);
          macMessage.set(encMessage, version.length + salt.length + nonce.length);

          var mac = await hmac(macMessage, macKey);

          if (null != mac) {
            // convert to correct type
            mac = new Uint8Array(mac);

            var fullMessage = new Uint8Array(macMessage.length + mac.length);
            fullMessage.set(macMessage, 0);
            fullMessage.set(mac,        macMessage.length);

            result = btoa(String.fromCharCode.apply(null, fullMessage));
          }
        }
      }
    }

    return result;
  }

  // convert a hex string to an array
  function hex2array(hex) {
    var result = new Uint8Array(Math.ceil(hex.length / 2));
    for (var i = 0; i < hex.length; i++) {
      result[i] = parseInt(hex.substr(i*2, 2), 16);
    }

    return result;
  }

  // calculate the HMAC of a message over a key with the Web Cryptography API
  async function hmac(message, key) {
    return await crypto.subtle.importKey("raw", key, {name:"HMAC", "hash": "SHA-256"}, false, ["sign"]
    ).then(
      function(key) {
        return crypto.subtle.sign("HMAC", key, message);
      }
    ).catch(
      function(error) {
        return null;
      }
    );
  }

  // replace HTML entities
  function html_entities(content) {
    return content.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
  }

  // calculate the PBKDF2 of a password over a salt with the Web Cryptography API
  async function pbkdf2(password, salt) {
    return await crypto.subtle.importKey("raw", password, {"name": "PBKDF2"}, false, ["deriveKey"]
    ).then(
      function(key) {
        return crypto.subtle.deriveKey({"name": "PBKDF2", "salt": salt, "iterations": 10000, "hash": "SHA-256"}, key,
                                       {"name": "AES-CTR", "length": 256}, true, ["encrypt", "decrypt"]);
      }
    ).then(
      function(key) {
        return crypto.subtle.exportKey("raw", key);
      }
    ).catch(
      function(error) {
        return null;
      }
    );
  }

  // convert a string to an array
  function string2array(string) {
    var result = new Uint8Array(string.length);
    for(i = 0; i < string.length; i++) {
      result[i] = string.charCodeAt(i);
    }

    return result;
  }

