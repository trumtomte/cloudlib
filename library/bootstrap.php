<?php
/**
 * CloudLib :: Lightweight RESTful MVC PHP Framework
 *
 * @author      Sebastian Book <email>
 * @copyright   Copyright (c) 2011 Sebastian Book <email>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     cloudlib
 */

// TODO
error_reporting(-1);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', LOGS . 'error_php.log');

/**
 * Register the autoloader
 */
spl_autoload_register(function($class)
{
    switch(true)
    {
        case preg_match('/Controller$/', $class) && ! preg_match('/^Controller$/', $class):
            $directory = CTRLS;
            break;
        case preg_match('/Model$/', $class) && ! preg_match('/^Model$/', $class):
            $directory = MODELS;
            break;
        default:
            $directory = CLASSES;
            break;
    }
    
    if( ! file_exists($file = $directory . $class . EXT))
    {
        throw new RuntimeException(sprintf('Unable to load class "%s"',
            $class));
    }

    require $file;
});

/**
 * Magic quotes...
 */
if(get_magic_quotes_gpc())
{
    foreach(array($_GET, $_POST, $_COOKIE, $_REQUEST) as $var)
    {
        if(is_array($var))
        {
            array_walk_recursive($var, function(&$value) {
                $value = stripslashes($value);
            });
        }
    }
}

/**
 * Set the error handler
 */
set_error_handler(function($code, $str, $file, $line)
{
    throw new ErrorException($str, $code, $code, $file, $line);
});

/**
 * Set the exception handler
 */
set_exception_handler(function(Exception $e)
{
    if(ob_get_contents())
    {
        ob_end_clean();
    }

    // TODO Cloudlib environment (internal error 500)

    echo sprintf('<pre>Message: %s</pre><pre>File: %s, Line: %s</pre><pre>Trace: %s</pre>',
        $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());

    exit();
});

// TODO: use a "global" error handling function
register_shutdown_function(function()
{
    if(is_array($e = error_get_last()))
    {
        extract($e);
        throw new ErrorException($message, $type, $type, $file, $line);
    }
});
