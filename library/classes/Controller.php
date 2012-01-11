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
class Controller
{
    /**
     * Array of View variables
     *
     * @var string
     */
    public $data = array();

    /**
     * Array of input variables such as $_POST/$_GET
     *
     * @var string
     */
    public $input;

    /**
     * Constructor
     *
     * @access  public
     * @param   array   $input
     * @return  void
     */
    public function __construct(array $input = array())
    {
        $this->input = $input;
    }

    /**
     * Declare a View variable
     *
     * @access  public
     * @param   mixed   $key
     * @param   mixed   $value
     * @return  object
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Return a Model
     * 
     * @access  public
     * @param   string  $model
     * @return  object
     */
    public function model($model = null)
    {
        //TODO: better solution
        if($model === null)
        {
            if(is_subclass_of($this, 'Controller'))
            {
                $model = preg_replace('/Controller$/', 'Model', get_class($this));
                return new $model();
            }
            $model .= 'default';
        }
        $model .= 'Model';
        return new $model;
    }

    /**
     * Shorthand to render a view
     *
     * @access  public
     * @param   string  $view
     * @return  void
     */
    public function render($view, $layout = null)
    {
        return new View($view, $layout, $this->data);
    }

    /**
     * Magic method to load classes
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
