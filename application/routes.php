<?php
/**
 * CloudLib :: Lightweight RESTful MVC PHP Framework
 *
 * @author      Sebastian Book <email>
 * @copyright   Copyright (c) 2011 Sebastian Book <email>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     cloudlib
 */
return array
(
    'GET /' => function() use ($app)
    {
        $app->set('test', $app->model('test')->test());

        return $app->render('index', 'index');
    },

    'GET /home' => function() use ($app)
    {
        $app->set('test', '/home');

        return $app->render('index', 'index');
    },

    'POST /home/test' => function() use ($app)
    {
        $app->set('test', '/home/test');

        return $app->render('index', 'index');
    },

    'GET /home/:test/:lol' => function($test, $lol) use ($app)
    {
        $app->set('test', $test . ' ' . $lol);

        return $app->render('index', 'index');
    },

    'GET /home/:test' => function($test) use ($app)
    {
        $app->set('test', $test);

        return $app->render('index', 'index');
    },

    'GET /asd' => function() use ($app)
    {
        return array('index', 'index', array());
    },

    'GET /start/test' => 'test.test',
    'GET /start/asd' => 'test',
    'GET /start/:param' => 'test.param',

    'GET /blog' => function() 
    {
        return array('index', 'index');
    }
);
