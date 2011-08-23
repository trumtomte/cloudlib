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
 * Notes:
 *  Add description of what doesnt exists and link to php.net
 *  Make it more stylish
 *  (Add more tests)
 *  (Add button for removing this file and continue to the index(default) view)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Tests</title>
<style type="text/css">
html { background: #ddd; }
body {
  width: 600px;
  margin: auto;
  margin-top: 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
  padding: 5px;
  background: #fff;
  font-family: Tahoma;
}
h1 {
  font-size: 18px;
  text-align: center;
  font-weight: normal;
}
table {
  margin: auto;
}
th {
  font-weight: normal;
  background: #eee;
  padding: 5px;
}
td {
  padding: 5px;
}
.passed {
  background: #88dd88;
}
.failed {
  background: #dd8888;
}
</style>
</head>
<body>
<h1>Test to see if CloudLib will work properly</h1>
<table>
  <tr>
    <th>Version</th>
    <?php if(version_compare(PHP_VERSION, '5.3.0', '>=')) { ?>
    <td class="passed">Passed</td>
    <?php } else { ?>
    <td class="failed">Failed</td>
    <?php } ?>
  </tr>
  <tr>
    <th>spl</th>
    <?php if(function_exists('spl_autoload_register')) { ?>
    <td class="passed">Passed</td>
    <?php } else { ?>
    <td class="failed">Failed</td>
    <?php } ?>
  </tr>
  <tr>
    <th>mbstring</th>
    <?php if(extension_loaded('mbstring')) { ?>
    <td class="passed">Passed</td>
    <?php } else { ?>
    <td class="failed">Failed</td>
    <?php } ?>
  </tr>
  <tr>
    <th>gd</th>
    <?php if(function_exists('gd_info')) { ?>
    <td class="passed">Passed</td>
    <?php } else { ?>
    <td class="failed">Failed</td>
    <?php } ?>
  </tr>
  <tr>
    <th>mysql</th>
    <?php if(function_exists('mysql_connect')) { ?>
    <td class="passed">Passed</td>
    <?php } else { ?>
    <td class="failed">Failed</td>
    <?php } ?>
  </tr>
  <tr>
    <th>pdo</th>
    <?php if(class_exists('pdo')) { ?>
    <td class="passed">Passed</td>
    <?php } else { ?>
    <td class="failed">Failed</td>
    <?php } ?>
  </tr>
  <tr>
    <th>blowfish</th>
    <?php if(CRYPT_BLOWFISH == 1) { ?>
    <td class="passed">Passed</td>
    <?php } else { ?>
    <td class="failed">Failed</td>
    <?php } ?>
  </tr>
</table>
</body>
</html>
