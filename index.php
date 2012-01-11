<?php
/**
 * CloudLib :: Lightweight RESTful MVC PHP Framework
 *
 * @author      Sebastian Book <email>
 * @copyright   Copyright (c) 2011 Sebastian Book <email>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     cloudlib
 */

define('BOOTTIME', microtime(true));

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

// TODO: redo class?
Config::load('config');

date_default_timezone_set(Config::get('app.timezone'));
mb_internal_encoding(Config::get('app.encoding'));

define('BASEURL', Config::get('app.baseurl'));

$request = new Request($_SERVER, $_GET, $_POST, $_FILES, $_COOKIE);

$router = new Router($request, Config::get('app.baseurl'));

$router->registerMap(APP . 'routes.php');

$router->parseMap();

$response = new Response($request);

if($request->methodAllowed())
{
    if($router->validRoute)
    {
        if($router->validMethod)
        {
            $response->body($router->response);
            $response->status(200);
        }
        else
        {
            $response->body(new View('error/405'));
            $response->status(405);
        }
    }
    else
    {
        $response->body(new View('error/404'));
        $response->status(404);
    }
}
else
{
    $response->body(new View('error/405'));
    $response->status(405);
}

$response->send();
