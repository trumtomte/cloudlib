<?php
/**
 * CloudLib :: Lightweight RESTful MVC PHP Framework
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     Cloudlib
 */

/**
 * <class name>
 *
 * <short description>
 *
 * @package     Cloudlib
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
abstract class Controller
{
    /**
     * Array of View variables
     *
     * @var string
     */
    protected $data = array();

    /**
     * The Request object
     *
     * @access  public
     * @var     object
     */
    public $request;

    /**
     * Array of input parameters, i.e from globals such as $_GET
     *
     * @var string
     */
    public $input;

    /**
     * The model object
     *
     * @access  public
     * @var     object
     */
    public $model;

    /**
     * Constructor
     *
     * @access  public
     * @param   array   $input
     * @return  void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->input = $request->input;
        
        $model = preg_replace('/Controller$/', 'Model', get_class($this));
        $this->model = new $model();
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
}
