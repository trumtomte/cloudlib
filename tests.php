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
 *
 * A PHP version of 5.3 or higher is required for cloudlib to work properly
 */
if(version_compare(PHP_VERSION, '5.3.0') < 0)
{
    exit('Please upgrade to a PHP version of at least 5.3.x - ' .
         'Current PHP version: ' . PHP_VERSION);
}
//check for mbstring, blowfish, pdo, gd2
