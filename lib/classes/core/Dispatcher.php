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
 * @subpackage  cloudlib.lib.classes
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
        $method = $this->getMethod($controller);

        $this->invoke($controller, $method, $this->param);
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
     * Loads a controller
     *
     * @access  private
     * @return  object
     */
    private function LoadController()
    {
        $controller = $this->class . 'Controller';

        if(!is_readable(CTRLS . $this->class . 'Controller' . EXT))
        {
            header('HTTP/1.1 404 Not Found');
            require LIB . 'error/404.php';
            exit(1);
        }

        return $controller::factory($this->class);
    }

    /**
     * Gets the Method
     *
     * @access  private
     * @param   string  $controller
     * @return  string
     */
    private function getMethod($controller)
    {
        if(!method_exists($controller, $this->method))
        {
            return 'index';
        }

        return $this->method;
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
        return empty($_GET['uri']) ? 'index/index' : $_GET['uri'];
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
        $dispatcher = Dispatcher::factory($uri);
        $dispatcher->dispatch();
    }
}
