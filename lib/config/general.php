<?php
/**
 * Cloudlib :: Minor PHP (M)VC Framework
 *
 * @author      Sebastian Book <sebbebook@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     cloudlib
 */

/**
 * General configuration for CloudLib
 */
return array
(
    /**
     * Default timezone
     */
    'timezone' => 'Europe/Stockholm',

    /**
     * The locale information
     */
    'locale'   => null,

    /**
     * Encoding for mb_string functions
     */
    'mbstring' => 'UTF-8',

    /**
     * Known image extensions
     */
    'imageExtensions' => 'jpg|jpeg|png|gif',

    /**
     * Static security salt
     */
    'salt' => 'asd23rERt0w./'
);
