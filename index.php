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
 * Define the root directory and the directory separator.
 */
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));

/**
 * Define the first-level directories.
 */
define('LIB', ROOT . DS . 'library' . DS);
define('PUB', ROOT . DS . 'public' . DS);
define('APP', ROOT . DS . 'application' . DS);

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
 * File extensions for files and classes.
 */
define('EXT', '.php');

/**
 * Bootstrap the application.
 */
require LIB . 'bootstrap.php';
