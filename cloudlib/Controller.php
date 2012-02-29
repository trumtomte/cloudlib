<?php
/**
 * CloudLib :: Flexible Lightweight PHP Framework
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     Cloudlib
 */

namespace cloudlib;

// Cloudlib
use cloudlib\Request,
    cloudlib\View,
    cloudlib\Model;

/**
 * The Controller
 *
 * Abstract class for all controllers
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
     * @param   object  $request
     * @param   object  $model
     * @return  void
     */
    public function __construct(Request $request, Model $model = null)
    {
        $this->request = $request;
        $this->input = $request->input;

        if($model)
        {
            $this->model = $model;
        }
    }

    /**
     * Return a Model
     *
     * @access  public
     * @param   string  $model
     * @return  object
     */
    public function model($model)
    {
        $model .= 'Model';
        return new $model();
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
    public function render($view, $layout = null, array $data = array())
    {
        if(isset($data))
        {
            $data = array_merge($this->data, $data);
        }

        return new View($view, $layout, $data);
    }
}
