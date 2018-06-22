<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

  ########## URL-ENCODING FUNCTIONS ##########

  # convert URL-safe Base64 encoding to standard Base64 encoding
  function url_base64_decode($url_base64_content) {
    $result = null;

    if (is_string($url_base64_content)) {
      $result = str_replace(URL_BASE64_MARKER_B,
                            BASE64_MARKER_B,
                            str_replace(URL_BASE64_MARKER_A, 
                                        BASE64_MARKER_A,
                                        $url_base64_content));

      # fill up with end markers as necessary
      while (0 !== (strlen($result) % 4)) {
        $result .= BASE64_MARKER_END;
      }
    }

    return $result;
  }

  # convert standard Base64 encoding to URL-safe Base64 encoding
  function url_base64_encode($base64_content) {
    $result = null;

    if (is_string($base64_content)) {
      $result = str_replace(BASE64_MARKER_B,
                            URL_BASE64_MARKER_B,
                            str_replace(BASE64_MARKER_A,
                                        URL_BASE64_MARKER_A,
                                        rtrim($base64_content,
                                              BASE64_MARKER_END)));
    }

    return $result;
  }

  ########## SYSTEM FUNCTIONS ##########

  # calls $command, prints $stdin to its standard input and reads
  # $stdout and $stderr from its standard output and its error output
  function execute_with_stdio($command, $stdin, &$stdout, &$stderr) {
    $result = false;

    # specify the used descriptors
    $descriptors = array(0 => array("pipe", "r"),  # STDIN
                         1 => array("pipe", "w"),  # STDOUT
                         2 => array("pipe", "w")); # STDERR

    # open the process handle
    $handle = proc_open($command, $descriptors, $pipes);
    if (false !== $handle) {
      # write stdin content and close the pipe
      if (null !== $stdin) {
        # prevent stdio from blocking
        #stream_set_blocking($pipes[0], false);

        # write stdin in 1kb chunks to prevent blocking
        $counter = 0;
        while ($counter < strlen($stdin)) {
          $write_size = strlen($stdin)-$counter;
          if ($write_size > STREAM_BUFFER) {
            $write_size = STREAM_BUFFER;
          }

          $bytes_written = fwrite($pipes[0], substr($stdin, $counter, $write_size));
          fflush($pipes[0]);

          $counter += $bytes_written;
        }
      }
      fclose($pipes[0]);

      # read stdout content and close the pipe
      $stdout = stream_get_contents($pipes[1]);
      fclose($pipes[1]);

      # read stderr content and close the pipe
      $stderr = stream_get_contents($pipes[2]);
      fclose($pipes[2]);

      # close the process and get the return value
      $result = proc_close($handle);
    }

    return $result;
  }

  ########## GPG FUNCTIONS ##########

  # decrypts the $content
  function decrypt($content, $homedir, $passphrase_file) {
    $result = null;

    if (is_string($content)) {
      # append homedir and passphrase file if they are given
      $cmd_append = "";
      if (is_dir($homedir)) {
        $cmd_append .= " --homedir ".escapeshellarg($homedir);
      }
      if (is_file($passphrase_file)) {
        $cmd_append .= " --batch --passphrase-file ".escapeshellarg($passphrase_file);
      }

      $ret = execute_with_stdio("LANG=en gpg --quiet --keyid-format LONG --no-tty ".$cmd_append." --output - --decrypt -",
                                $content,
                                $stdout,
                                $stderr);
      if (0 === $ret) {
        # check that the decrypted message has been integrity-protected,
        # older versions of GnuPG set the return code to 0 when this warning occurs 
        if (false === stripos($stderr, GPG_MDC_ERROR)) {
          $result = $stdout;
        }
      }
    }

    return $result;
  }

  # decrypts the $content using the GnuPG PECL
  function decrypt_pecl($content, $recipient, $homedir, $passphrase_file) {
    $result = null;
    if (is_string($content)) {
      # set the GnuPG home dir
      if (is_dir($homedir)) {
        putenv("GNUPGHOME=".$homedir);
      }
      # load the GnuPG passphrase from file
      $passphrase = null;
      if (is_file($passphrase_file)) {
        $passphrase = file_get_contents($passphrase_file);
        # reset the passphrase to null if it could not be read
        if (false === $passphrase) {
          $passphrase = null;
        }
      }
      # initialize GnuPG
      $gnupg = gnupg_init();
      # only proceed when the GnuPG init worked
      if ($gnupg) {
        # set error mode so that we handle them ourselves
        gnupg_seterrormode($gnupg, GNUPG_ERROR_SILENT);
        # disable ASCII armoring
        if (gnupg_setarmor($gnupg, 0)) {
          # make the key known that we use for decryption
          if (gnupg_adddecryptkey($gnupg, $recipient, $passphrase)) {
            # decrypt the $content
            $ret = gnupg_decrypt($gnupg, $content);
            if (false !== $ret) {
              $result = $ret;
            }
          }
        }
      }
    }
    return $result;
  }

  # encrypts the $content for the $recipient
  function encrypt($content, $recipient, $homedir) {
    $result = null;

    if (is_string($content) && (is_string($recipient))) {
      # append homedir if it is given
      $cmd_append = "";
      if (is_dir($homedir)) {
        $cmd_append .= " --homedir ".escapeshellarg($homedir);
      }

      $ret = execute_with_stdio("LANG=en gpg --quiet --keyid-format LONG --no-tty --recipient ".escapeshellarg($recipient)." --trust-model always --yes ".$cmd_append." --output - --encrypt -",
                                $content,
                                $stdout,
                                $stderr);
      if (0 === $ret) {
        $result = $stdout;
      }
    }

    return $result;
  }

  # encrypts the $content for the $recipient using the GnuPG PECL
  function encrypt_pecl($content, $recipient, $homedir) {
    $result = null;

    if (is_string($content) && is_string($recipient)) {
      # set the GnuPG home dir
      if (is_dir($homedir)) {
        putenv("GNUPGHOME=".$homedir);
      }

      # initialize GnuPG
      $gnupg = gnupg_init();

      # only proceed when the GnuPG init worked
      if ($gnupg) {
        # set error mode so that we handle them ourselves
        gnupg_seterrormode($gnupg, GNUPG_ERROR_SILENT);

        # disable ASCII armoring
        if (gnupg_setarmor($gnupg, 0)) {
          # make the key known that we use for encryption
          if (gnupg_addencryptkey($gnupg, $recipient)) {
            # encrypt the $content
           $ret = gnupg_encrypt($gnupg, $content);

            if (false !== $ret) {
              $result = $ret;
            }
          }
        }
      }
    }

    return $result;
  }

?>
