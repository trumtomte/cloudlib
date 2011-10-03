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
 * The controller class.
 *
 * Abstract class for all the controllers
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
abstract class controller extends master
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
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct($class)
    {
        $this->classname = $class;
        $this->loadModel($class);
        $this->loadView($class);
    }

    /**
     * Loads the corresponding model
     *
     * @access  public
     * @param   string  $model
     * @return  void
     */
    final public function loadModel($model)
    {
        $model .= 'Model';

        $this->model = $model::factory();
    }

    /**
     * Loads the view object
     *
     * @access  public
     * @return  void
     */
    final public function loadView($classname)
    {
        $this->view = view::factory($classname);
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
     * Magic method to load modules
     *
     * @access  public
     * @param   string  $class
     * @return  object
     */
    final public function __get($module)
    {
        return core::loadModule($module);
    }

    /**
     * Abstract method for controllers
     *
     * @access  public
     */
    abstract public function index();
}
