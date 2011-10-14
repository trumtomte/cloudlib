<?php
/**
 * CloudLib :: Lightweight MVC PHP Framework
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
abstract class Model extends Factory
{
    /**
     * Magic method for loading helper classes
     *
     * @access  public
     * @param   string  $class
     * @return  object
     */
    final public function __get($helper)
    {
        return core::loadHelper($helper);
    }
}
