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
<?php
echo $code;
echo '<br>';
echo $message;
?>
</body>
</html>
