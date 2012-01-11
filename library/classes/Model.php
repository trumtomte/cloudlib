<?php
/**
 * CloudLib :: Lightweight RESTful MVC PHP Framework
 *
 * @author      Sebastian Book <email>
 * @copyright   Copyright (c) 2011 Sebastian Book <email>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     cloudlib
 */

/**
 * <class name>
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <email>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
abstract class Model
{
    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Magic method for loading classes
     *
     * @access  public
     * @param   string  $class
     * @return  object
     */
    public function __get($class)
    {
        return new $class();
    }
}
