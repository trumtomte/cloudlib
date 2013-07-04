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
     * Array of routes
     *
     * @var array
     */
    public $routes = [];

    /**
     * Add a new route
     *
     * @param   string  $path       The route path
     * @param   mixed   $callback   The route callback(s)
     * @return  void
     */
    public function add($path, $callbacks)
    {
        $methods = ['GET'];
        $callbacks = is_array($callbacks) ? $callbacks : [$callbacks];

        if($path[0] !== '/')
        {
            list($methods, $path) = explode(' ', $path);
            $methods = explode('|', $methods);
        }

        foreach($methods as $method)
        {
            $this->routes[] = new Route($path, $method, $callbacks);
        }
    }

    /**
     * Gets the matching routes based on a given request path
     *
     * @param   string  $path   The request path
     * @return  mixed           Returns the matching routes, else false
     */
    public function findMatchingRoutes($path)
    {
        $routes = [];

        foreach($this->routes as $route)
        {
            if($route->match($path))
            {
                $routes[] = $route->setParams($path);
            }
        }

        return $routes ? $routes : false;
    }
}
