<?php
/**
 * Cloudlib :: Minor PHP (M)VC Framework
 *
 * @author      Sebastian Book <sebbebook@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     cloudlib
 */

if(version_compare(PHP_VERSION, '5.3.0') < 0)
{
    exit('Please upgrade to a PHP version of at least 5.3.x - ' .
         'Current PHP version: ' . PHP_VERSION);
}

if(function_exists('spl_autoload_register'))
{

}

if(ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING)
{

}

if(function_exists('gd_info'))
{

}

if(function_exists('mysql_connect'))
{

}

if(class_exists('PDO'))
{

}

if(CRYPT_BLOWFISH == 1)
{

}

