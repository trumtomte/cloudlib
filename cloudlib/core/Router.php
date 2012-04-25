<?php
/**
 * Cloudlib
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace cloudlib\core;

use Closure;
use ReflectionFunction;
use ReflectionMethod;
use cloudlib\core\Cloudlib;

/**
 * The Router class
 *
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Router
{
    /**
     * Array of stored routes with corresponding request method and response
     *
     * @access  public
     * @var     array
     */
    public $routes = array();

    /**
     * The response to be set if a route is found or not found (status code)
     *
     * @access  public
     * @var     mixed
     */
    public $response = null;

    /**
     * At object creation define a new $routes array
     *
     * @access  public
     * @param   array   $routes Array of routes
     * @return  void
     */
    public function __construct(array $routes = array())
    {
        $this->routes = $routes;
    }

    /**
     * Add a new route (with the possibility of multiple request methods)
     *
     * @access  public
     * @param   string  $route      The route pattern (ex '/home')
     * @param   array   $methods    Array of request methods
     * @param   mixed   $response   The response to be returned when the route is found
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
     * Shorthand function to route(), define a GET route
     *
     * @access  public
     * @param   string  $route      The route pattern
     * @param   mixed   $response   The response to be returned when the route is found
     * @return  void
     */
    public function get($route, $response)
    {
        $this->route($route, array('GET'), $response);
    }

    /**
     * Shorthand function to route(), define a POST route
     *
     * @access  public
     * @param   string  $route      The route pattern
     * @param   mixed   $response   The response to be returned when the route is found
     * @return  void
     */
    public function post($route, $response)
    {
        $this->route($route, array('POST'), $response);
    }

    /**
     * Shorthand function to route(), define a PUT route
     *
     * @access  public
     * @param   string  $route      The route pattern
     * @param   mixed   $response   The response to be returned when the route is found
     * @return  void
     */
    public function put($route, $response)
    {
        $this->route($route, array('PUT'), $response);
    }

    /**
     * Shorthand function to route(), define a DELETE route
     *
     * @access  public
     * @param   string  $route      The route pattern
     * @param   mixed   $response   The response to be returned when the route is found
     * @return  void
     */
    public function delete($route, $response)
    {
        $this->route($route, array('DELETE'), $response);
    }

    /**
     * Parse through the $routes array to find a matching route to the request uri
     *
     * @access  public
     * @param   Cloudlib    $app    The core framework class (DIC) to be used to fetch the request method and uri
     * @return  boolean             Returns true if a route was found, else false
     */
    public function parse(Cloudlib $app)
    {
        // HEAD acts as GET but only outputs header; therefore change it temporarily
        $method = ($app->request->isHead()) ? 'GET' : $app->request->method;

        // Strip the base uri from the requested uri
        $request = preg_replace('/\/{2,}/', '/', '/' . preg_replace('#' . $app->base . '#', '', $app->request->uri, 1));

        foreach($this->routes as $route => $responses)
        {
            // Literal match
            if($route == $request)
            {
                // If request method does not exist
                if( ! array_key_exists($method, $responses))
                {
                    $this->response = 405;
                    return false;
                }
                // Found route, set response
                $this->setResponse($responses[$method], $app);
                return true;
            }

            // Create an regex out of an route with parameters (:param or ;regex)
            $regex = str_replace('/', '\/', preg_replace('/;/', '', preg_replace('/:(\w+)/', '(\w+)', $route)));

            // Found route
            if(preg_match('#^' . $regex . '$#', $request))
            {
                // Invalid method
                if( ! array_key_exists($method, $responses))
                {
                    $this->response = 405;
                    return false;
                }

                // Get all parts from the route and request uri
                list($routeParts, $uriParts) = array(
                    explode('/', $route),
                    explode('/', $request)
                );

                $parameters = array();

                // Extract the parameters from the request uri
                foreach($routeParts as $key => $value)
                {
                    if(strpos($value, ':') !== false || strpos($value, ';') !== false)
                    {
                        $parameters[] = $uriParts[$key];
                    }
                }
                // Found route, set response
                $this->setResponse($responses[$method], $app, $parameters);
                return true;
            }
        }
        // No matching route
        $this->response = 404;
        return false;
    }

    /**
     * Set the response, either by calling an anonymous function or creating a new object and invoking the object method
     *
     * @access  protected
     * @param   Closure|array   $response   The route response
     * @param   Cloudlib        $app        The core framework class (DIC) to be used for object creation
     * @param   array           $parameters Array of route parameters
     * @return  void
     */
    protected function setResponse($response, Cloudlib $app, array $parameters = array())
    {
        if($response instanceof Closure)
        {
            $reflection = new ReflectionFunction($response);

            $this->response = $reflection->invokeArgs($parameters);
        }

        if(is_array($response))
        {
            if(isset($response['class']))
            {
                $classname = $response['class'];

                $class = new $classname($app);

                $method = (isset($response['method'])) ? $response['method'] : $app->request->method;

                $reflection = new ReflectionMethod($class, $method);

                $this->response = $reflection->invokeArgs($class, $parameters);
            }
        }
    }
}
