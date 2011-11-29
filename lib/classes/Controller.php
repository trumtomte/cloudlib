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
 * The controller class.
 *
 * Abstract class for all the controllers
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes.core
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
abstract class Controller extends Factory
{
    /**
     * View object
     *
     * @access  public
     * @var     object
     */
    public $view;

    /**
     * Model object
     *
     * @access  public
     * @var     object
     */
    public $model;

    /**
     * Array of request variables
     *
     * @access  public
     * @var     array
     */
    public $data;

    /**
     * Constructor
     *
     * @access  public
     * @param   object  $view
     * @param   object  $model
     * @return  void
     */
    public function __construct()
    {
        $args = func_get_args();

        if(is_array($args[0]))
        {
            $args = array_shift($args);
        }

        if(!($args[0] instanceof View) or !isset($args[0]))
        {
            throw new cloudException('Controller::__construct(): argument 1 must be an instance of View');
        }
        if(!($args[1] instanceof Model) or !isset($args[1]))
        {
            throw new cloudException('Controller::__construct(): argument 2 must be an instance of Model');
        }
        if(!is_array($args[2]) or !isset($args[2]))
        {
            throw new cloudException('Controller::__construct(): argument 3 must be an Array');
        }

        $this->view = $args[0];
        $this->model = $args[1];
        $this->data = $args[2];
    }

    /**
     * Shorthand for setting a view variable
     *
     * @access  public
     * @param   string|int  $index
     * @param   mixed       $value
     * @return  void
     */
    final public function set($index, $value)
    {
        $this->view->$index = $value;
    }

    /**
     * Shorthand to set the layout
     *
     * @access  public
     * @param   string  $layout
     * @return  object
     */
    final public function layout($layout = null)
    {
        return $this->view->layout($layout);
    }

    /**
     * Shorthand to render a view
     *
     * @access  public
     * @param   string  $view
     * @return  void
     */
    final public function render($view = null)
    {
        $this->view->render($view);
    }

    /**
     * Magic method to load helpers
     *
     * @access  public
     * @param   string  $class
     * @return  object
     */
    final public function __get($helper)
    {
        return $helper::factory();
    }

    /**
     * Abstract method for controllers
     *
     * @access  public
     */
    abstract public function index();
}
