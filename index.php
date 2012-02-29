<?php
/**
 * CloudLib :: Flexible Lightweight PHP Framework
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     Cloudlib
 */

// Lots of things going on here, will be commented and restructured soon!


// For testing purposes
define('BOOT_TIME', microtime(true));

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

// Run the application
$app->run();
