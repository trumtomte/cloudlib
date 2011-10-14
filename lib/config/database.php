<?php
/**
 * CloudLib :: Lightweight MVC PHP Framework
 *
 * @author      Sebastian Book <sebbebook@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     cloudlib
 */
return array
(
    /**
     * Database configuration
     */

    'default' => array(
        'dsn'           => 'mysql:host=localhost;dbname=seb_proj',
        'username'      => 'root',
        'password'      => 'root',
        'charset'       => 'utf8',
        'persistent'    => true
    ),

    'test' => array(
        'dsn'           => 'mysql:host=localhost;dbname=seb_proj',
        'username'      => 'root',
        'password'      => 'roo',
        'charset'       => 'utf8',
        'persistent'    => true
    )
);
