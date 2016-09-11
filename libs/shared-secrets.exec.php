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
                                        $url_base64_content)).
                str_repeat(BASE64_MARKER_END,
                           strlen($url_base64_content) % 4);
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

      $ret = execute_with_stdio("gpg --quiet --keyid-format LONG --no-tty ".$cmd_append." --output - --decrypt -",
                                $content,
                                $stdout,
                                $stderr);
      if (0 === $ret) {
        $result = $stdout;
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

      $ret = execute_with_stdio("gpg --quiet --keyid-format LONG --no-tty --recipient ".escapeshellarg($recipient)." --trust-model always --yes ".$cmd_append." --output - --encrypt -",
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

  # only define the legacy functions if they are activated
  if (SUPPORT_LEGACY_LINKS) {
    # convert URL-safe Base64 encoding to standard Base64 encoding
    function url_base64_decode_legacy($url_base64_content) {
      $result = null;

      if (is_string($url_base64_content)) {
        $result = str_replace(URL_BASE64_MARKER_B,
                              BASE64_MARKER_B,
                              str_replace(URL_BASE64_MARKER_A, 
                                          BASE64_MARKER_A,
                                          $url_base64_content));
      }

      return $result;
    }

    # decrypts the $content
    function decrypt_legacy($content, $homedir, $passphrase_file) {
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

        $ret = execute_with_stdio("gpg --quiet --keyid-format LONG --no-tty ".$cmd_append." --output - --decrypt -",
                                  unstrip_message_legacy($content),
                                  $stdout,
                                  $stderr);
        if (0 === $ret) {
          $result = $stdout;
        }
      }

      return $result;
    }

    # converts a stripped message back to a full GPG message
    # adding a prefix, dummy comment and suffix
    function unstrip_message_legacy($content) {
      $result = null;

      if (is_string($content)) {
        $left  = null;
        $right = null;

        # search for equation sign from the end to fix line breaks
        $equation_pos = strrpos($content, GPG_MESSAGE_PARTS_MARKER);
        if (false !== $equation_pos) {
          $left  = substr($content, 0, $equation_pos);
          $right = substr($content, $equation_pos);
        } else {
          $left  = $content;
          $right = null;
        }

        $result = GPG_MESSAGE_PREFIX.GPG_MESSAGE_LINE_SEPARATOR.
                  GPG_MESSAGE_COMMENT.GPG_MESSAGE_VALUE_SEPARATOR.GPG_MESSAGE_COMMENT_DUMMY.GPG_MESSAGE_LINE_SEPARATOR.
                  GPG_MESSAGE_LINE_SEPARATOR.
                  trim(chunk_split($left, GPG_MESSAGE_LINE_LENGTH, GPG_MESSAGE_LINE_SEPARATOR)).GPG_MESSAGE_LINE_SEPARATOR;

        if (null !== $right) {
          $result .= $right.GPG_MESSAGE_LINE_SEPARATOR;
        }

        $result .= GPG_MESSAGE_SUFFIX;
      }

      return $result;
    }
  }

?>
