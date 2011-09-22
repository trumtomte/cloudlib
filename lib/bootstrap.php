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
 * Set error reporting.
 */
error_reporting(E_ALL);

/**
 * Log all errors
 */
ini_set('log_errors', 1);
ini_set('error_log', LOGS . 'error.log');

/**
 * If we are in production, do not display errors.
 */
if(PRODUCTION == true)
{
    ini_set('display_errors', 0);
}
else
{
    ini_set('display_errors', 1);
}

/**
 * Require the core class
 */
require CLASSES . 'core' . CLASS_EXT;

/**
 * Set the autoloader
 */
spl_autoload_register(array('core', 'autoload'));

/**
 * Set the error handler
 */
set_error_handler(array('core', 'errorHandler'));

/**
 * Set the exception handler
 */
set_exception_handler(array('cloudException', 'exceptionHandler'));

/**
 * Start the timer as 'boot'
 */
timer::start('boot');

/**
 * Initialize everything
 */
core::initialize();
