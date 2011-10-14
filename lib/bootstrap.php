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
 * Set error reporting.
 */
error_reporting(E_ALL);

/**
 * If we are in production, do not display errors.
 */
if(PRODUCTION == true) {
    ini_set('display_errors', 0);
} else {
    ini_set('display_errors', 1);
}

/**
 * If no RewriteBase is not defined, define it as root.
 */
if(!defined('RWBASE')) {
    define('RWBASE', '/');
}

/**
 * If no default configuration is defined, define it as default.
 */
if(!defined('CONF')) {
    define('CONF', 'default');
}

/**
 * Require the core class and set the autoload method.
 */
require CORE . 'Core' . EXT;
spl_autoload_register(array('Core', 'autoload'));

/**
 * Set file logging.
 */
if(!defined('LOGGING')) {
    define('LOGGING', true);
}

if(LOGGING)
{
    // Log all PHP errors
    ini_set('log_errors', 1);
    ini_set('error_log', LOGS . 'error.log');

    // Log from the Logger class
    register_shutdown_function(array('Logger', 'write'));
}

/**
 * Set the error and exception handler
 */
set_error_handler(array('Core', 'errorHandler'));
set_exception_handler(array('CloudException', 'exceptionHandler'));

/**
 * Start the timer as 'boot' and then initialize
 */
Timer::start('boot');
Core::initialize();
