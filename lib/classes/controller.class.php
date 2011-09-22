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
    public function __construct($model)
    {
        $this->loadView();
        $this->loadModel($model);
    }

    /**
     * Magic method
     * Loads a module, initiates a new one if it doesnt exist
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
     * Loads the view object
     *
     * @access  public
     * @return  void
     */
    final public function loadView()
    {
        $this->view = view::factory();
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
     * Abstract method for controllers
     *
     * @access  public
     */
    abstract public function index();
}
