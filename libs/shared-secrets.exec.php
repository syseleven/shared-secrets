<?php

  # prevent direct access
  if (!defined("SYS11_SECRETS")) { die(""); }

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
  function decrypt($content, $passphrase_file) {
    $result = null;

    if (is_string($content)) {
      # append passphrase file if it is given
      $cmd_append = "";
      if (is_file($passphrase_file)) {
        $cmd_append = "--batch --passphrase-file ".escapeshellarg($passphrase_file);
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

  # encrypts the $content for the list of $recipients
  function encrypt($content, $recipients) {
    $result = null;

    if (is_string($content) && (is_string($recipients))) {
      $ret = execute_with_stdio("gpg --quiet --keyid-format LONG --armor --group encryption=".escapeshellarg($recipients)." --no-tty --recipient encryption --trust-model always --yes --output - --encrypt -",
                                $content,
                                $stdout,
                                $stderr);
      if (0 === $ret) {
        $result = $stdout;
      }
    }

    return $result;
  }

  # strips all unnecessary content from an armored GPG message
  function strip_message($content) {
    $result = null;

    if (is_string($content)) {
      # get content as separate lines
      $lines = explode(GPG_MESSAGE_LINE_SEPARATOR, $content);

      if (is_array($lines)) {
        $is_content = false;

        # iterate through all lines to only retrieve the conteht
        foreach ($lines as $lines_item) {
          # ignore prefix
          if (0 !== strcasecmp($lines_item, GPG_MESSAGE_PREFIX)) {
            # ignure suffix
            if (0 !== strcasecmp($lines_item, GPG_MESSAGE_SUFFIX)) {
              if (!$is_content) {
                # the content starts after an empty line
                $is_content = (0 === strlen(trim($lines_item)));
              } else {
                # append content to result
                if (null === $result) {
                  $result = trim($lines_item);
                } else {
                  $result .= trim($lines_item);
                }
              }
            }
          }
        }
      }
    }

    return $result;
  }

  # converts a stripped message back to a full GPG message
  # adding a prefix, dummy comment and suffix
  function unstrip_message($content) {
    $result = null;

    if (is_string($content)) {
      $left  = null;
      $right = null;

      # search for double equation to fix line breaks
      $double_equation = strrpos($content, "==");
      if (false !== $double_equation) {
        $left  = substr($content, 0, $double_equation+1);
        $right = substr($content, $double_equation+1, strlen($content)-$double_equation-1);
      } else {
        $left  = $content;
        $right = null;
      }

      $result = GPG_MESSAGE_PREFIX."\n".
                GPG_MESSAGE_COMMENT." Dummy\n".
                "\n".
                trim(chunk_split($left, GPG_MESSAGE_LINE_LENGTH, "\n"))."\n";

      if (null !== $right) {
        $result .= $right."\n";
      }

      $result .= GPG_MESSAGE_SUFFIX;
    }

    return $result;
  }

?>
