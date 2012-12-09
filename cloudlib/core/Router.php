<?php
/**
 * Cloudlib
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2012 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace cloudlib\core;

use cloudlib\core\Cloudlib;
use cloudlib\core\Route;

/**
 * The Router class
 *
 * @copyright   Copyright (c) 2012 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Router
{
    /**
     * The found route
     *
     * @access  public
     * @var     object
     */
    public $route = null;

    /**
     * Array of available routes
     *
     * @access  public
     * @var     array
     */
    public $routes = array();

    /**
     * Array of matching routes for the current request
     *
     * @access  public
     * @var     array
     */
    public $matchingRoutes = array();

    /**
     * Add a new route (route + method + response)
     *
     * @access  public
     * @param   string  $route      The route uri
     * @param   string  $method     Allowed method for the route
     * @param   mixed   $response   The route response
     * @return  object              The newly added route
     */
    public function add($route, $method, $response)
    {
        // Sets the route and then returns it
        return $this->routes[sprintf('%s %s', $method, $route)] = new Route($route, $method, $response);
    }

    /**
     * Add multiple (or a single one) routes
     *
     * @access  public
     * @param   string  $route      The route uri
     * @param   array   $methods    Array of allowed request method
     * @param   mixed   $response   The route response
     * @return  void
     */
    public function route($route, array $methods, $response)
    {
        foreach($methods as $method)
        {
            $this->add($route, $method, $response);
        }
    }

    /**
     * Shorthand function for adding a route which allows the GET method
     *
     * @access  public
     * @param   string  $route      The route uri
     * @param   mixed   $response   The route response
     * @return  object              The newly added route
     */
    public function get($route, $response)
    {
        return $this->add($route, 'GET', $response);
    }

    /**
     * Shorthand function for adding a route which allows the POST method
     *
     * @access  public
     * @param   string  $route      The route uri
     * @param   mixed   $response   The route response
     * @return  object              The newly added route
     */
    public function post($route, $response)
    {
        return $this->add($route, 'POST', $response);
    }

    /**
     * Shorthand function for adding a route which allows the PUT method
     *
     * @access  public
     * @param   string  $route      The route uri
     * @param   mixed   $response   The route response
     * @return  object              The newly added route
     */
    public function put($route, $response)
    {
        return $this->add($route, 'PUT', $response);
    }

    /**
     * Shorthand function for adding a route which allows the DELETE method
     *
     * @access  public
     * @param   string  $route      The route uri
     * @param   mixed   $response   The route response
     * @return  object              The newly added route
     */
    public function delete($route, $response)
    {
        return $this->add($route, 'DELETE', $response);
    }

    /**
     * Check if a route exists based on $uri and $base
     *
     * @access  public
     * @param   string  $base   The base uri
     * @param   string  $uri    The requested uri
     * @return  boolean         Returns true if a matching route does exist
     */
    public function routeExists($base, $uri)
    {
        $request = preg_replace('/\/{2,}/', '/', '/' . preg_replace('#' . $base . '#', '', $uri, 1));

        foreach($this->routes as $route)
        {
            if($route->match($request))
            {
                $route->setRequest($request);

                $this->matchingRoutes[] = $route;
            }
        }

        return empty($this->matchingRoutes) ? false : true;
    }

    /**
     * Check if the requested route allows the request $method
     *
     * @access  public
     * @param   string  $method
     * @return  boolean Returns true if the route allows the requested method
     */
    public function routeHasMethod($method)
    {
        $method = ($method == 'HEAD') ? 'GET' : $method;

        foreach($this->matchingRoutes as $route)
        {
            if($route->hasMethod($method))
            {
                $this->route = $route;
            }
        }

        return isset($this->route) ? true : false;
    }
}
