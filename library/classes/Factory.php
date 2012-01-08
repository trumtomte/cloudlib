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
abstract class Factory
{
    /**
     * Array of objects
     *
     * @access  private
     * @var     array
     */
    public static $objects = array();

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Centralized way of initiating classes
     *
     * @access  public
     * @return  object
     */
    public static function factory()
    {
        $class =  new ReflectionClass(get_called_class());
        return $class->newInstanceArgs(func_get_args());
    }

    /**
     * Centralized way of initiating classes with the singleton pattern
     *
     * @access  public
     * @return  object
     */
    public static function singleton()
    {
        $object = get_called_class();

        if( ! in_array($object, self::$objects))
        {
            self::$objects[$object] = new ReflectionClass($object);
            return self::$objects[$object]->newInstanceArgs(func_get_args());
        }
        return self::$objects[$object];
    }
}
