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
 * The factory class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes.core
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
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
    private static $objects = array();

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
    final public static function factory()
    {
        $object = get_called_class();

        if(!in_array($object, self::$objects))
        {
            self::$objects[$object] = new ReflectionClass($object);
            return self::$objects[$object]->newInstanceArgs(func_get_args());
        }

        return self::$objects[$object];
    }
}
