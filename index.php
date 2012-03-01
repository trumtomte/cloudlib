<?php
/**
 * CloudLib :: Flexible Lightweight PHP Framework
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     Cloudlib
 */

// DISCLAIMER
// 
// this file is an example since the documentation isnt finished yet,
// everything is subject to change!

// Require the framework
require 'cloudlib/Cloudlib.php';

// Inititalize the application
$app = new cloudlib\Cloudlib(__DIR__, '/cloudlib');

// Load classes with their name without namespaces
$app->uploader = new Uploader($app->request->files);

// Define the root route
$app->route('/', array('GET', 'POST'), function() use ($app)
{
    return $app->render('index', 'index');
});

// Define a /home route with a parameter, without use ($app)
$app->route('/home/:test', array('GET'), function($test)
{
    $data['test'] = $test;

    return new View('index', null, $data);
});

// Define a /test route with a parameter
$app->route('/test/:param', array('GET'), function($test)
{
    return 'hello ' . $test;
});

// Define two routes for controllers
$app->route('/ctrl', array('GET'), array(
    // Controller name is required
    'controller' => 'index'

    // Optional to choose if a model should be loaded (this requires the database configuration)
    //'model' => 'index'
));
// Controller route with a parameter
$app->route('/ctrl/:param', array('GET'), array(
    'controller' => 'index',

    // You are able to specify your own method name instead of the default method name (the request method)
    'method' => 'test'
));

// The keys "statusCode" and "statusMessage" will always be sent to the error response
// Custom 404 view
$app->error(404, function($error) {
    $data['code'] = $error['statusCode'];
    $data['message'] = $error['statusMessage'];
    return new View('errors/404', null, $data);
});

// Custom 405 view
$app->error(405, function($error) use ($app)
{
    $app->set('code', $error['statusCode'])
        ->set('message', $error['statusMessage']);

    return $app->render('errors/405');
});

// Custom 500 view, the 500 view will always be passed an Exception object
$app->error(500, function($error) use ($app)
{
    $app->set('message', $error->getMessage());

    return $app->render('errors/500');
});

// Run the application
$app->run();
