<?php
/**
 * CloudLib :: Flexible Lightweight PHP Framework
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

        // Define the Timezone
        'app.timezone'      => 'Europe/Stockholm',
        // Define the Locale
        'app.locale'        => null,
        // Secret for Hasing
        'app.secret'        => 'MySuperSecretSalt',
        // Encoding to be used for databases/string functions etc..
        'app.encoding'      => 'utf8',
        // Display errors?
        'app.errors'        => 1,
        // Log php errors?
        'app.logs'          => 1,

        // Database
        'db.dsn'        => 'mysql:host=localhost;dbname=default',
        'db.username'   => 'root',
        'db.password'   => 'root',
        'db.charset'    => 'utf8',
        'db.persistent' => true,
    ),

    'custom' => array(
        // Application

        // Define the Timezone
        'app.timezone'      => 'Europe/Stockholm',
        // Define the Locale
        'app.locale'        => null,
        // Secret for Hasing
        'app.secret'        => 'MySuperSecretSalt',
        // Encoding to be used for databases/string functions etc..
        'app.encoding'      => 'utf8',
        // Display errors?
        'app.errors'        => 1,
        // Log php errors?
        'app.logs'          => 1,

        // Database
        'db.dsn'        => 'mysql:host=localhost;dbname=default',
        'db.username'   => 'root',
        'db.password'   => 'root',
        'db.charset'    => 'utf8',
        'db.persistent' => true,
    )
);
