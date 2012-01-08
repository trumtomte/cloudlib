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
    // TODO
    // hur vill jag ha baseurl'n, /cloudlib?, cloudlib? etc..

    'default' => array(
        // Application
        'app.environment'   => 'development',
        'app.baseurl'       => '/cloudlib',
        'app.timezone'      => 'Europe/Stockholm',
        'app.locale'        => null,
        'app.secret'          => 'MySuperSecretSalt',
        'app.encoding'      => 'utf8',
        // Database
        'db.dsn'        => 'mysql:host=localhost;dbname=default',
        'db.username'   => 'root',
        'db.password'   => 'root',
        'db.charset'    => 'utf8',
        'db.persistent' => true,
        // Logging
        'log.dateformat'    => 'Y-m-d G:i:s',
        'log.file'   => 'logger.log',
    ),

    'custom' => array(
        // Application
        'app.environment'   => 'development',
        'app.baseurl'       => '/cloudliw',
        'app.timezone'      => 'Europe/Stockholm',
        'app.locale'        => null,
        'app.salt'          => 'MySuperSecretSalt',
        'app.encoding'      => 'utf8',
        // Database
        'db.dsn'        => 'mysql:host=localhost;dbname=default',
        'db.username'   => 'sebberoot',
        'db.password'   => 'root',
        'db.charset'    => 'utf8',
        'db.persistent' => true,
        // Logging
        'log.dateformat'    => 'Y-m-d G:i:s',
        'log.file'   => 'logger.log',
    )
);
