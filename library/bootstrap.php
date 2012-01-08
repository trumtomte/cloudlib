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
// TODO ska php logga dessa errors? eller ska jag ha en egen som gör det, både och?
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

Benchmark::start('boot');

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

    if(strtolower(Request::server('CLOUDLIB_ENV')) === 'production')
    {
        Response::factory(500)->body('error/500')->send();
    }
    else
    {
        $message = $e->getMessage();
        $file    = $e->getFile();
        $line    = $e->getLine();
        $trace   = $e->getTrace();
        $traceStr = $e->getTraceAsString();

        if(file_exists($file = VIEWS . 'error/exception.php'))
        {
            require $file;
        }
        else
        {
            // TODO
            // HTML output
        }
    }
    exit();
});

// TODO använd en "global" funktion för error handling? som i laravel..
register_shutdown_function(function()
{
    $e = error_get_last();

    if(isset($e))
    {
        throw new ErrorException($e['message'], $e['type'], $e['type'], $e['file'], $e['line']);
    }

});


// TODO ha en klass som kör allt detta?

Config::load('config');
// Config::$default = 'default';

date_default_timezone_set(Config::get('app.timezone'));
mb_internal_encoding(Config::get('app.encoding'));

register_shutdown_function(array('Logger', 'write'));

// TODO ska jag ha dessa här?
define('BASEURL', Config::get('app.baseurl'));
define('CSS', BASEURL . DS . 'pub/css/');
define('JS', BASEURL . DS . 'pub/js/');
define('IMG', BASEURL . DS . 'pub/img/');

$router = Router::factory(Request::uri(), Request::method(), Config::get('app.baseurl'));
$router->validate(APP . 'routes.php');

if($router->validRoute)
{
    if($router->validMethod)
    {
        Response::factory(200)->body($router->response)->send();
    }
    else
    {
        Response::factory(405)->body('error/405')->send();
    }
}
else
{
    Response::factory(404)->body('error/404')->send();
}

//Logger::log(Database::$queries, 1);
