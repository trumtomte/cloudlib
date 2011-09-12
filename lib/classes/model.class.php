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
 * The model class.
 *
 * Abstract class for all models.
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
abstract class model extends master
{
    /**
     * Magic method
     * Loads a module, initiates a new one if it doesnt exist
     *
     * @access  public
     * @param   string  $class
     * @return  object
     */
    public function __get($module)
    {
        return core::loadModule($module);
    }
}
