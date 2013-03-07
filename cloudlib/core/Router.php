<?php
/**
 * Cloudlib
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2012 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace cloudlib\core;

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
    public $routes = [];

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
    public function add($route, callable $response)
    {
        $uri = $route;
        $methods = ['GET'];

        if($route[0] !== '/')
        {
            list($methods, $uri) = explode(' ', $route);
            $methods = explode('|', $methods);
        }

        if(isset($this->routes[$uri]))
        {
            $this->routes[$uri]->append($methods, $response);
        }
        else
        {
            $this->routes[$uri] = new Route($uri, $methods, $response);
        }
    }

    public function find($request)
    {
        $match = null;

        foreach($this->routes as $route)
        {
            if($route->match($request))
            {
                $match = $route;
            }
        }

        return $match;
    }
}
