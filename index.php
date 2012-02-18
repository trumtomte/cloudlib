<?php
/**
 * CloudLib :: Lightweight RESTful MVC PHP Framework
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     Cloudlib
 */

// For testing purposes
define('BOOT_TIME', microtime(true));

// Require the framework
require 'cloudlib/Cloudlib.php';

// Inititalize the application
$app = new Cloudlib(__DIR__, '/cloudlib');

$app->uploader = new Uploader($app->request->files);

// Define the root route
$app->route('/', array('GET', 'POST'), function() use ($app)
{
    return $app->render('index', 'index');
});

// Define a /home route with a parameter
$app->route('/home/:test', array('GET'), function($test)
{
    $data['test'] = $test;

    return new View('index', null, $data);
});

// Define a /test route with a parameter
$app->route('/test/:hej', array('GET'), function($test)
{
    return 'HEEEJ ' . $test;
});

// Define two routes for controllers
$app->route('/ctrl', array('GET'), array('controller' => 'index', 'model' => 'index'));
$app->route('/ctrl/:param', array('GET'), array('controller' => 'index', 'method' => 'test'));

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

// Custom 500 error view
/*
$app->error(500, function($e) use ($app)
{
    $app->set('message', $e->getMessage());
    $app->set('line', $e->getLine());
    $app->set('file', $e->getFile());
    $app->set('trace', $e->getTraceAsString());
    return $app->render('errors/500');
});
 */

// Run the application
$app->run();
