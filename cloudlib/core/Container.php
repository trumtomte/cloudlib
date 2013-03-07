<?php
/**
 * Cloudlib
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2012 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace cloudlib\core;

use ArrayAccess;
use ReflectionFunction;
use InvalidArgumentException;

/**
 * Make setting object properties available to a class.
 *
 * Also makes it able to define properties as singletons.
 *
 * @copyright   Copyright (c) 2012 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
trait PropertyContainer {

    /**
     * Array of properties
     *
     * @access  protected
     * @var     array
     */
    protected $_objectProperties = [];

    /**
     * Define a class property
     *
     * @access  public
     * @param   string  $key    The variable identifier (name)
     * @param   mixed   $value  The variable value
     * @return  void
     */
    public function __set($key, $value)
    {
        $this->_objectProperties[$key] = $value;
    }

    /**
     * Get a class property
     *
     * If the property is an anonymous function the first parameter will be the
     * Container object
     *
     * @access  public
     * @param   string  $key    The variable identifier (name)
     * @throws  InvalidArgumentException    If the key does not exist
     * @return  mixed           Return the variable value or, if its an anonymous function, call it
     */
    public function __get($key)
    {
        if( ! array_key_exists($key, $this->_objectProperties))
        {
            throw new InvalidArgumentException(sprintf('Key [%s] does not exist', $key));
        }

        if(is_callable($this->_objectProperties[$key]))
        {
            return $this->_objectProperties[$key]($this);
        }

        return $this->_objectProperties[$key];
    }

    /**
     * Check if a class property has been set
     *
     * @access  public
     * @param   string  $key    The variable identifier (name)
     * @return  boolean         True if it is set, else false
     */
    public function __isset($key)
    {
        return isset($this->_objectProperties[$key]);
    }

    /**
     * Unset a class property
     *
     * @access  public
     * @param   string  $key    The variable identifier (name)
     * @return  void
     */
    public function __unset($key)
    {
        unset($this->_objectProperties[$key]);
    }

    /**
     * If a container item has been defined as an anonymous function we can invoke it with parameters
     *
     * The first parameter will always be the Container object
     *
     * @access  public
     * @param   string  $key                The variable identifier (name)
     * @param   array   $args               Array containing all the parameters
     * @throws  InvalidArgumentException    If the key does not exist
     * @return  mixed                       Is depending on what the function was defined to return
     */
    public function __call($key, $args)
    {
        if( ! array_key_exists($key, $this->_objectProperties))
        {
            throw new InvalidArgumentException(sprintf('Key [%s] does not exist', $key));
        }

        if( ! is_callable($this->_objectProperties[$key]))
        {
            throw new InvalidArgumentException(sprintf('Key [%s] is not callable', $key));
        }

        array_unshift($args, $this);

        return call_user_func_array($this->_objectProperties[$key], $args);
    }

    /**
     * Return a single instance of an object (singleton)
     *
     * @access  public
     * @param   object  $callable   The object of which an instance will be created
     * @return  Closure             Returns a Closure that returns a single instance of the class
     */
    public function instance(callable $callable)
    {
        return function($self) use ($callable)
        {
            static $instance = null;

            if($instance === null)
            {
                $instance = $callable($self);
            }

            return $instance;
        };
    }
}
