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
 * The dispatcher class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes.core
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Dispatcher extends Factory
{
    /**
     * Controller
     *
     * @access  private
     * @var     string
     */
    private $class = null;

    /**
     * Controller method
     *
     * @access  private
     * @var     string
     */
    private $method = null;

    /**
     * Controller method parameter
     *
     * @access  private
     * @var     string
     */
    private $param = null;

    /**
     * Current view object
     *
     * @access  private
     * @var     object
     */
    private $view = null;

    /**
     * Constructor
     *
     * @access  public
     * @param   string  $uri
     * @return  void
     */
    public function __construct($uri = null)
    {
        if($uri === null)
        {
            $uri = Request::uri();
        }
        else
        {
            $uri = explode('/', $uri);
        }

        $this->class = $uri[0];

        if(isset($uri[1]))
        {
            $this->method = $uri[1];

            if(isset($uri[2]))
            {
                $this->param = $uri[2];
            }
        }

        $this->view = View::factory($this->class);
    }

    /**
     * Dispatch
     *
     * @access  public
     * @return  void
     */
    public function dispatch()
    {
        $controller = $this->loadController();

        if(!method_exists($controller, $this->method))
        {
            $this->method = 'index';
        }

        $this->invoke($controller, $this->method, $this->param);
        
        $response = Response::factory()->body($this->view->body)->send();
    }

    /**
     * Loads a controller with a view object and the corresponding model
     *
     * @access  private
     * @return  object
     */
    private function LoadController()
    {
        $controller = $this->class . 'Controller';

        if(!is_readable(CTRLS . $controller . EXT))
        {
            //$response = Response::factory()->notFound();
            ob_start();
            require LIB . 'error/404.php';
            $response = Response::factory(404)->body(ob_get_clean())->send();
        }

        switch(strtolower(Request::method()))
        {
            case 'get':
                $data = Request::_get();
                break;
            case 'post':
                $data = Request::_post();
                break;
            default:
                $data = array();
                break;
        }

        $model = $this->class . 'Model';

        return $controller::factory($this->view, $model::factory(), $data);
    }

    /**
     * Invoke the controller
     *
     * @access  private
     * @param   object  $class
     * @param   string  $method
     * @param   string  $param
     * @return  void
     */
    private function invoke($class, $method, $param)
    {
        $class->$method($param);
    }

    /**
     * Redirect
     *
     * @access  public
     * @param   string  $uri
     * @return  void
     */
    public static function redirect($uri = null)
    {
        $dispatcher = Dispatcher::factory($uri)->dispatch();
    }
}
