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
 * The Dependency Injection Container
 *
 * The container variables can be set as array items ($class['item']) or as
 * object properties ($class->property)
 *
 * @copyright   Copyright (c) 2012 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Container implements ArrayAccess
{
    /**
     * Array of all stored array variables
     *
     * @access  public
     * @var     array
     */
    public $arrayVariables = [];

    /**
     * Array of all stored object properties
     *
     * @access  public
     * @var     array
     */
    public $objectProperties = [];

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

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
        $this->objectProperties[$key] = $value;
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
        if( ! array_key_exists($key, $this->objectProperties))
        {
            throw new InvalidArgumentException(sprintf('Key [%s] does not exist', $key));
        }

        if(is_callable($this->objectProperties[$key]))
        {
            return $this->objectProperties[$key]($this);
        }

        return $this->objectProperties[$key];
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
        return isset($this->objectProperties[$key]);
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
        unset($this->objectProperties[$key]);
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
        if( ! array_key_exists($key, $this->objectProperties))
        {
            throw new InvalidArgumentException(sprintf('Key [%s] does not exist', $key));
        }

        if( ! is_callable($this->objectProperties[$key]))
        {
            throw new InvalidArgumentException(sprintf('Key [%s] is not callable', $key));
        }

        array_unshift($args, $this);

        $reflection = new ReflectionFunction($this->objectProperties[$key]);

        return $reflection->invokeArgs($args);
    }

    /**
     * Define an array property
     *
     * @access  public
     * @param   string  $key    The variable identifier (name)
     * @param   mixed   $value  The variable value
     * @return  void
     */
    public function offsetSet($key, $value)
    {
        $this->arrayVariables[$key] = $value;
    }

    /**
     * Get an array property
     *
     * If the property is an anonymous function the first parameter will be the Container object
     *
     * @access  public
     * @param   string  $key    The variable identifier (name)
     * @throws  InvalidArgumentException    If the key does not exist
     * @return  mixed           Return the variable value or, if its an anonymous function, call it
     */
    public function offsetGet($key)
    {
        if( ! array_key_exists($key, $this->arrayVariables))
        {
            throw new InvalidArgumentException(sprintf('Key [%s] does not exist', $key));
        }

        if(is_callable($this->arrayVariables[$key]))
        {
            return $this->arrayVariables[$key]($this);
        }

        return $this->arrayVariables[$key];
    }

    /**
     * Check if an array property has been set
     *
     * @access  public
     * @param   string  $key    The variable identifier (name)
     * @return  boolean         True if it is set, else false
     */
    public function offsetExists($key)
    {
        return isset($this->arrayVariables[$key]);
    }

    /**
     * Unset an array property
     *
     * @access  public
     * @param   string  $key    The variable identifier (name)
     * @return  void
     */
    public function offsetUnset($key)
    {
        unset($this->arrayVariables[$key]);
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
