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
     * Request uri
     *
     * @access  private
     * @var     string
     */
    private $uri = null;

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
            $uri = $this->getURI();
        }

        $this->uri = $this->filter($uri);
    }

    /**
     * Dispatch
     *
     * @access  public
     * @return  void
     */
    public function dispatch()
    {
        $this->setRoute();

        $controller = $this->loadController();

        if(!method_exists($controller, $this->method))
        {
            $this->method = 'index';
        }

        $this->invoke($controller, $this->method, $this->param);
        
        $response = Response::factory()->body($this->view->body)->send();
    }

    /**
     * Sets the controller route
     *
     * @access  private
     * @return  void
     */
    private function setRoute()
    {
        $route = explode('/', $this->uri);

        $this->class = $route[0];

        if(isset($route[1]))
        {
            $this->method = $route[1];

            if(isset($route[2]))
            {
                $this->param = $route[2];
            }
        }
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
            $response = Response::factory()->notFound();
        }

        $this->view = View::factory($this->class);

        $model = $this->class . 'Model';

        return $controller::factory($this->view, $model::factory());
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
     * Gets the URI
     *
     * @access  private
     * @return  string
     */
    private function getURI()
    {
        return empty($_GET['uri']) ? CONTROLLER : $_GET['uri'];
    }

    /**
     * Remove unwanted characters from the uri
     *
     * @access  private
     * @param   string  $uri
     * @return  string
     */
    private function filter($uri)
    {
        return filter_var($uri, FILTER_SANITIZE_URL);
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
