<?php
/**
 * Cloudlib
 *
 * @author      Sebastian Bengtegård <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2013 Sebastian Bengtegård <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace cloudlib\core;

use cloudlib\core\Route;

/**
 * The Router class
 *
 * @copyright   Copyright (c) 2013 Sebastian Bengtegård <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Router
{
    /**
     * Array of available routes
     *
     * @access  public
     * @var     array
     */
    public $routes = [];

    /**
     * Add a new route
     *
     * @access  public
     * @param   string      $route      The route uri (and methods)
     * @param   callable    $response   The route response
     * @return  void
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

    /**
     * Finds a given route based on $request
     *
     * @access  public
     * @param   string      $request    The request uri
     * @return  object|null             Returns the found route (or null if none was found)
     */
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
