<?php
/**
 * CloudLib :: Lightweight RESTful MVC PHP Framework
 *
 * @author      Sebastian Book <email>
 * @copyright   Copyright (c) 2011 Sebastian Book <email>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     cloudlib
 */

/**
 * Page for displaying exceptions in a more readable fashion.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Exception</title>
  <style>
    html {
        background: #eee;
        padding-top: 5px;
    }
    body {
        width: 850px;
        border-radius: 6px;
        border: 1px solid #ccc;
        background: #fff;
        padding: 0px 10px 10px 10px;
        margin: auto;
        margin-bottom: 10px;
        font-family: Courier;
    }
    h1 {
        margin: 0;
        margin-top: 10px;
        margin-bottom: 5px;
        font-size: 1.5em;
        font-weight: normal;
        text-align: center;
        font-family: Courier;
        background:#fbe3e4;
        border-radius: 6px;
        color:#8a1f11;
        border:1px solid #fbc2c4;
        padding: 10px;
    }
    th, td {
        padding: 10px;
        border: 1px solid #fff;
    }
    th {
        text-align: left;
        font-weight: bold;
        font-size: 12px;
        text-align: center;
    }
    td {
        font-size: 13px;
        line-height: 1.2em;
    }
    table {
        margin: auto;
        background: #fff;
        width: 100%;
    }
    .odd { background: #ccc; }
    .even { background: #ddd; }
    table tr:nth-child(odd) { background: #ccc; }
    table tr:nth-child(even) { background: #ddd; }
    .msg { font-size: 16px; }
    h4, p {
        margin: 0;
        padding: 0;
    }
    h4 {
        margin-bottom: 10px;
        font-size: 15px;
        font-family: Monospace;
    }
    p {
        padding-left: 10px;
    }
    #trace th {
        font-size: 18px;
        text-align: center;
        font-weight: normal;
    }
    #trace td { padding: 12px; }
    td:hover { border: 1px solid #ddd; background: #fff; }
  </style>
</head>
<body>
<h1>Exception Caught!</h1>
<table>
  <tr class="odd">
    <th>Message</th>
    <td class="msg"><?php echo $message; ?></td>
  </tr>
  <tr>
    <th class="even">File</th>
    <td><?php echo $file; ?></td>
  </tr>
  <tr class="odd">
    <th>Line</th>
    <td><?php echo $line; ?></td>
  </tr>
</table>
<h1>Stack trace</h1>
<table id="trace">
<?php
/*
    foreach($trace as $key => $value)
    {
        $class = isset($value['class']) ? $value['class'] : null;
        $type = isset($value['type']) ? $value['type'] : null;
        $func = isset($value['function']) ? $value['function'] : null;
        $args = isset($value['args']) ? $value['args'] : null;
        $line = isset($value['line']) ? 'on line ' . $value['line'] : null;
        $file = isset($value['file']) ? 'in ' . $value['file'] : null;

        $argsStr = null;

        foreach($args as $k => $v)
        {
            if(is_string($v))
            {
                $argsStr .= '\'' . mb_strimwidth($v, 0, 18, '...') . '\', ';
            }
            else
            {
                $argsStr .= $v . ', ';
            }
        }

        $argsStr = rtrim($argsStr, ', ');

        echo '<tr>';
        echo '<th>#' . ($key + 1) . '</th>';
        echo '<td>';
        echo '<h4>' . $class . $type . $func . '(' . $argsStr . ')</h4>';
        echo '<p>' . $file . ' ' . $line . '</p>';
        echo '</td>';
        echo '</tr>';
    }
 */
echo '<pre>';
echo print_r($trace) . '<br>' . $file . ' ' .$line . ' '. $message;
?>
</table>
</body>
</html>
