<?php
/**
 * CloudLib :: Lightweight RESTful MVC PHP Framework
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     Cloudlib
 */
return array
(
    'default' => array(
        // Application
        'app.timezone'      => 'Europe/Stockholm',
        'app.locale'        => null,
        'app.secret'        => 'MySuperSecretSalt',
        'app.encoding'      => 'utf8',
        'app.errors'        => 1,
        // Database
        'db.dsn'        => 'mysql:host=localhost;dbname=default',
        'db.username'   => 'root',
        'db.password'   => 'root',
        'db.charset'    => 'utf8',
        'db.persistent' => true,
    ),

    'custom' => array(
        // Application
        'app.timezone'      => 'Europe/Stockholm',
        'app.locale'        => null,
        'app.salt'          => 'MySuperSecretSalt',
        'app.encoding'      => 'utf8',
        'app.errors'        => 1,
        // Database
        'db.dsn'        => 'mysql:host=localhost;dbname=default',
        'db.username'   => 'sebberoot',
        'db.password'   => 'root',
        'db.charset'    => 'utf8',
        'db.persistent' => true,
    )
);
