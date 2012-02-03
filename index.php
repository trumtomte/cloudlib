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
require 'Cloudlib/Cloudlib.php';

// Inititalize the application
$app = new Cloudlib(__DIR__, '/cloudlib');

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
    $data['code'] = $error['code'];
    $data['message'] = $error['message'];
    return new View('errors/404', null, $data);
});

// Custom 405 view
$app->error(405, function($error) use ($app)
{
    $app->set('code', $error['code'])
        ->set('message', $error['message']);

    return $app->render('errors/405');
});

// Custom 500 error view
$app->error(500, function()
{
    return new View('errors/500');
});

// Run the application
$app->run();
