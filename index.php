<?php
/**
 * CloudLib :: Flexible Lightweight PHP Framework
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     Cloudlib
 */

// Require the framework
require 'cloudlib/Cloudlib.php';

// Inititalize the application
$app = new cloudlib\Cloudlib(__DIR__, '/cloudlib');

// Define variables outside of routes to be used in routes.

// We create an Uploader object with the $_FILES array.
$app->uploader = new Uploader($app->request->files);

// Define the root route
$app->get('/', function() use ($app)
{
    // Render the View index.php with the Layout index.php
    return $app->render('index', 'index');
});

// Define a route with a parameter
$app->get('/home/:param', function($param) use ($app)
{
    // Set the variable param (so it can be used in the view) and escape it.
    $app->set('param', $app->escape($param));

    return $app->render('index', 'index');
});

// Define a route with multiple request methods
$app->route('/hello/:world', array('GET', 'POST', 'PUT', 'DELETE'), function($world) use ($app)
{
    // Just display Hello + (the escaped parameter)
    return 'Hello ' . $app->escape($world);
});


// Defining controllers is the same as routes, but instead of the response function we have an array.

// This would call the testController and call the get() method in that controller.
$app->get('/controller', array('controller' => 'test'));

// This would call the same controller but call the test() method and pass a parameter to that method.
$app->get('/controller/:param', array('controller' => 'test', 'method' => 'test'));

// This would call the same controller but call the view() method with a parameter,
// the controller will also be loaded with the testModel.
$app->get('/view/:page', array('controller' => 'test', 'method' => 'view', 'model' => 'test'));


// Creating responses for error is also just as easy as defining routes,
// but instead of setting a route we assign it with a status code.

// Cloudlib makes use of the error codes 404 and 405 so creating Views for those is recommended.

// All error functions is passed an array of the status code and status message (exception: the 500 internal server error)
$app->error(404, function($error) use ($app)
{
    $app->set('status', $error['statusCode'])
        ->set('message', $error['statusMessage']);

    // Render the View in errors/404.php
    return $app->render('errors/404');
});

// Lets do the same for 405 (method not allowed)
$app->error(405, function($error) use ($app)
{
    $app->set('status', $error['statusCode'])
        ->set('message', $error['statusMessage']);

    // Render the View in errors/405.php
    return $app->render('errors/405');
});

// The error 500 (internal server error) will be passed an Exception instead of an array of the status code and message.
$app->error(500, function($e) use ($app)
{
    // This is like working with regular exceptions, nothing new.
    $app->set('message', $e->getMessage())
        ->set('line', $e->getLine())
        ->set('file', $e-getFile());

    return $app->render('errors/500');
});

// Lets run the application!
$app->run();
