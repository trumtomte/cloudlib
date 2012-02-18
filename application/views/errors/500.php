<?php
/**
 * CloudLib :: Lightweight RESTful MVC PHP Framework
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     Cloudlib
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>500</title>
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
<h1>500</h1>
<h3>Internal server error</h3>
<?php
    echo $message;
    echo $line;
    echo $file;
    echo $trace;
?>
</body>
</html>
