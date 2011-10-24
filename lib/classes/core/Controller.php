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
     * Constructor
     *
     * @access  public
     * @param   object  $view
     * @param   object  $model
     * @return  void
     */
    public function __construct(View $view, Model $model)
    {
        $this->view = $view;
        $this->model = $model;
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
        return Core::loadHelper($helper);
    }

    /**
     * Abstract method for controllers
     *
     * @access  public
     */
    abstract public function index();
}
