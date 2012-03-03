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
     * Error status code
     *
     * @access  protected
     * @var     int
     */
    protected $errorStatus;

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
        foreach($methods as $method)
        {
            $this->routes[$route][$method] = $response;
        }
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
        // Strip the base uri from the requested uri
        $requestUri = preg_replace('#' . $this->baseUri . '#', '', $this->request->uri);

        foreach($this->routes as $route => $responses)
        {
            // Literal match
            if($route == $requestUri)
            {
                // If request method does not exist
                if( ! array_key_exists($this->request->method, $responses))
                {
                    $this->errorStatus = 405;
                    return false;
                }
                // Found route, set response
                $this->setResponse($responses[$this->request->method]);
                return true;
            }

            // Create an regex out of an route with parameters (:param)
            $regex = str_replace('/', '\/', preg_replace('/:(\w+)/', '(\w+)', $route));

            // Found route
            if(preg_match('#^' . $regex . '$#', $requestUri))
            {
                // Invalid method
                if( ! array_key_exists($this->request->method, $responses))
                {
                    $this->errorStatus = 405;
                    return false;
                }

                // Get all parts from the route and request uri
                list($routeParts, $uriParts) = array(
                    explode('/', $route),
                    explode('/', $requestUri)
                );

                $parameters = array();

                // Extract the parameters from the request uri
                foreach($routeParts as $key => $value)
                {
                    if(strpos($value, ':') !== false)
                    {
                        $parameters[] = $uriParts[$key];
                    }
                }
                // Found route, set response
                $this->setResponse($responses[$this->request->method], $parameters);
                return true;
            }
        }
        // No matching route
        $this->errorStatus = 404;
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
            return $this->errorStatus;
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
