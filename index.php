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
 * Environment: False for Development and True for Production.
 */
define('PRODUCTION', false);

/**
 * Define the root directory and the directory separator.
 */
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));

/**
 * Define the first-level directories.
 */
define('LIB', ROOT . DS . 'lib' . DS);
define('PUB', ROOT . DS . 'pub' . DS);
define('APP', ROOT . DS . 'app' . DS);

/**
 * Define sub-level directories of the Application directory,
 * directories for controllers, views and models.
 */
define('CTRLS', APP . 'controllers' . DS);
define('VIEWS', APP . 'views' . DS);
define('MODELS', APP . 'models' . DS);

/**
 * Define sub-level directories of the Library directory,
 * directories for classes,config and log files.
 */
define('CLASSES', LIB . 'classes' . DS);
define('CONFIG', LIB . 'config' . DS);
define('LOGS', LIB . 'log' . DS);

/**
 * File extensions for files and classes.
 */
define('EXT', '.php');
define('CLASS_EXT', '.class.php');

/**
 * Check PHP version and available extensions and functions.
 */
// if(file_exists('tests.php')) { require 'tests.php'; exit(); }

/**
 * Require the bootstrap.
 */
require LIB . 'bootstrap.php';
