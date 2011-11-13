<?php
/**
 * CloudLib :: Lightweight MVC PHP Framework
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
 * Default configuration.
 */
define('CONF', 'default');

/**
 * Default controller.
 */
define('CONTROLLER', 'index');

/**
 * Logging: True allows logging and false does not.
 */
define('LOGGING', true);

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
 * directories for controllers, models and views.
 */
define('CTRLS', APP . 'controllers' . DS);
define('MODELS', APP . 'models' . DS);
define('VIEWS', APP . 'views' . DS);

/**
 * Sub-level directory of views for layouts.
 */
define('LAYOUTS', VIEWS . 'layouts' . DS);

/**
 * Define sub-level directories of the Library directory,
 * directories for classes, config and log files.
 */
define('CLASSES', LIB . 'classes' . DS);
define('CONFIG', LIB . 'config' . DS);
define('LOGS', LIB . 'log' . DS);

/**
 * Define sub-level directories of the Class directory,
 * directories for core and helper classes.
 */
define('CORE', CLASSES . 'core' . DS);
define('HELPERS', CLASSES . 'helpers' . DS);

/**
 * Define the URL path.
 */
define('URLPATH', substr(substr($_SERVER['SCRIPT_FILENAME'], 0, -9),
        strlen($_SERVER['DOCUMENT_ROOT'])));

/**
 * Define paths for CSS, JavaScript and Image files.
 */
define('CSS', URLPATH . 'pub/css' . DS);
define('JS', URLPATH . 'pub/js' . DS);
define('IMG', URLPATH . 'pub/img' . DS);

/**
 * File extensions for files and classes.
 */
define('EXT', '.php');

/**
 * Check PHP version and available extensions and functions.
 */
// if(file_exists('tests.php')) { require 'tests.php'; exit(); }

/**
 * Require the bootstrap.
 */
require LIB . 'bootstrap.php';

/**
 * Start the timer as 'boot' and then run Core::main()
 */
Timer::start('boot');
Core::main();
