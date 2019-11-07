<?php

  // Usage: `php -S localhost:8080 ./router.php`

  // this script shall only be called from the CLI server
  if ("cli-server" !== PHP_SAPI) { die(""); }

  function preg_match_array($pattern, $subject) {
    $result = false;

    if (is_array($pattern) && is_string($subject)) {
      foreach ($pattern as $pattern_item) {
        $result = (1 === preg_match($pattern_item, $subject));

        // it's enough to have one match
        if ($result) {
          break;
        }
      }
    }

    return $result;
  }

  // do some URL handling
  $result = false;
  $path   = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
  if (preg_match_array(["@^\/\.git(\/.*)?$@", "@^\/\.gitattributes$@", "@^\/\.gitignore$@", "@^\/\.htaccess$@",
                        "@^\/actions(\/.*)?$@", "@^\/CHANGELOG\.md$@", "@^\/config(\/.*)?$@", "@^\/ENCRYPTION\.md$@",
                        "@^\/lib(\/.*)?$@", "@^\/LICENSE$@", "@^\/pages(\/.*)?$@", "@^\/README\.md$@",
                        "@^\/router\.php$@", "@^\/template(\/.*)?$@"], $path)) {
    // prevent access to certain locations
    http_response_code(404);
    $result = true;
  } elseif (!is_file(__DIR__.$path)) {
    // single entrypoint
    require_once(__DIR__."/index.php");
    $result = true;
  }
  return $result;

