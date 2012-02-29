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

// SPL
use Closure,
    ReflectionMethod,
    ReflectionFunction;

// Cloudlib
use cloudlib\Request;

/**
 * The Router class
 *
 * Matches URI routes to defined responses
 *
 * @package     Cloudlib
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Router
{
    /**
     * Array of available routes
     *
     * @access  protected
     * @var     array
     */
    protected $routes = array();

    /**
     * The request object
     *
     * @access  protected
     * @var     object
     */
    protected $request;

    /**
     * The request metod
     *
     * @access  protected
     * @var     string
     */
    protected $requestMethod;

    /**
     * The base uri
     *
     * @access  protected
     * @var     string
     */
    protected $baseUri;

    /**
     * The response
     *
     * @access  protected
     * @var     mixed
     */
    protected $response = null;

    /**
     * Error code
     *
     * @access  protected
     * @var     int
     */
    protected $error;

    /**
     * Constructor.
     *
     * Set the Request object and the Base uri
     *
     * @access  public
     * @param   object  $request
     * @param   string  $baseUri
     * @return  void
     */
    public function __construct(Request $request, $baseUri)
    {
        $this->request = $request;
        $this->baseUri = $baseUri;

        if($this->request->method == 'HEAD')
        {
            // HEAD is the same as GET but will only output headers,
            // hence the temporary conversion of HEAD > GET
            $this->requestMethod = 'GET';
        }
        else
        {
            $this->requestMethod = $this->request->method;
        }
    }

    /**
     * Add a route
     *
     * @access  public
     * @param   string  $route
     * @param   array   $methods
     * @param   mixed   $response
     * @return  void
     */
    public function route($route, array $methods, $response)
    {
        $this->routes[$route] = array('methods' => $methods, 'response' => $response);
    }

    /**
     * Shorthand function to add a GET route
     *
     * @access  public
     * @param   string  $route
     * @param   mixed   $response
     * @return  void
     */
    public function get($route, $response)
    {
        $this->route($route, array('GET'), $response);
    }

    /**
     * Shorthand function to add a POST route
     *
     * @access  public
     * @param   string  $route
     * @param   mixed   $response
     * @return  void
     */
    public function post($route, $response)
    {
        $this->route($route, array('POST'), $response);
    }

    /**
     * Shorthand function to add a PUT route
     *
     * @access  public
     * @param   string  $route
     * @param   mixed   $response
     * @return  void
     */
    public function put($route, $response)
    {
        $this->route($route, array('PUT'), $response);
    }

    /**
     * Shorthand function to add a DELETE route
     *
     * @access  public
     * @param   string  $route
     * @param   mixed   $response
     * @return  void
     */
    public function delete($route, $response)
    {
        $this->route($route, array('DELETE'), $response);
    }

    /**
     * Parse through the array of routes and return true if it was found otherwise return
     * false and set the error code accordingly.
     *
     * @access  public
     * @return  boolean
     */
    public function routeExists()
    {
        $uri = preg_replace('#' . $this->baseUri . '#', '', $this->request->uri);

        foreach($this->routes as $route => $array)
        {
            $methods = $array['methods'];
            $response = $array['response'];

            // Literal match
            if($route == $uri)
            {
                if(in_array($this->requestMethod, $methods))
                {
                    $this->setResponse($response);
                    return true;
                }
                $this->error = 405;
                return false;
            }

            $regex = str_replace('/', '\/', preg_replace('/:(\w+)/', '(\w+)', $route));

            if(preg_match('#^' . $regex . '$#', $uri))
            {
                list($rParts, $uParts) = array(explode('/', $route), explode('/', $uri));

                $parameters = array();

                foreach($rParts as $index => $value)
                {
                    if(strpos($value, ':') !== false)
                    {
                        $parameters[] = $uParts[$index];
                    }
                }

                if(in_array($this->requestMethod, $methods))
                {
                    $this->setResponse($response, $parameters);
                    return true;
                }
                $this->error = 405;
                return false;
            }
        }
        $this->error = 404;
        return false;
    }

    /**
     * Return the response, if no response is set, return the error code
     *
     * @access  public
     * @return  mixed
     */
    public function getResponse()
    {
        if($this->response === null)
        {
            return $this->error;
        }
        return $this->response;
    }

    /**
     * Set the response,
     * either call the closure or create a controller and return the called method
     *
     * @access  protected
     * @param   mixed   $response
     * @param   array   $parameters
     * @return  void
     */
    protected function setResponse($response, array $parameters = array())
    {
        if($response instanceof Closure)
        {
            $func = new ReflectionFunction($response);
            $this->response = $func->invokeArgs($parameters);
        }

        if(is_array($response))
        {
            $class = $response['controller'] . 'Controller';

            if(isset($response['model']))
            {
                $model = $response['model'] . 'Model';
                $controller = new $class($this->request, new $model());
            }
            else
            {
                $controller = new $class($this->request);
            }

            if(isset($response['method']))
            {
                $refMethod = new ReflectionMethod($controller, $response['method']);
            }
            else
            {
                $refMethod = new ReflectionMethod($controller, $this->request->method);
            }

            $this->response = $refMethod->invokeArgs($controller, $parameters);
        }
    }
}
