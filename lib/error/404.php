<?php
/**
 * Cloudlib :: Minor PHP (M)VC Framework
 *
 * @author      Sebastian Book <sebbebook@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     cloudlib
 */

/**
 * 404 page.
 */
header('HTTP/1.1 404 Not Found');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>404</title>
  <style>
    html
    {
      background: #eee;
      margin-top: 10px;
    }
    body
    {
      font-family: sans-serif;
      width: 700px;
      margin: auto;
      background: #fff;
      padding: 10px;
      text-align: center;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
  </style>
</head>
<body>
<h1>404</h1>
<h3>The requested page could not be found</h3>
</body>
</html>
