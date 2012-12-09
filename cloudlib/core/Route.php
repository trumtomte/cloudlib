<?php
/**
 * Cloudlib
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2012 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace cloudlib\core;

use Closure;
use ReflectionFunction;
use ReflectionMethod;
use cloudlib\core\Cloudlib;

/**
 * The Route class
 *
 * @copyright   Copyright (c) 2012 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Route
{
    /**
     * The route uri
     *
     * @access  public
     * @var     string
     */
    public $route;

    /**
     * The route response
     *
     * @access  public
     * @var     mixed
     */
    public $response;

    /**
     * Allowed request method
     *
     * @access  public
     * @var     string
     */
    public $method;

    /**
     * Request uri
     *
     * @access  public
     * @var     string
     */
    public $request;

    /**
     * Array of before/after filters to be run
     *
     * @access  public
     * @var     array
     */
    public $filters = array();

    /**
     * Sets the $route, $method and $response
     *
     * @access  public
     * @param   string  $route      The route uri
     * @param   string  $method     Allowed request method
     * @param   mixed   $response   The route response
     * @return  void
     */
    public function __construct($route, $method, $response)
    {
        $this->route = $route;
        $this->method = $method;
        $this->response = $response;
    }

    /**
     * Sets the request uri
     *
     * @access  public
     * @param   string  $request    The request uri
     * @return  void
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * Create a pattern for regex matching of this route uri
     *
     * @access  public
     * @param   string  $route  The route uri
     * @return  string          Returns the pattern for regex matching
     */
    public function pattern($route)
    {
        $regex = array(
            '/:str/' => '([a-zA-Z]+)',
            '/:int/' => '(\d+)',
            '/:alpha/' => '([a-zA-Z0-9]+)',
            '/:(\w+)/' => '(\w+)',
            '/;/' => ''
        );

        array_walk($regex, function($replacement, $pattern) use (&$route)
        {
            $route = preg_replace($pattern, $replacement, $route);
        });

        return str_replace('/', '\/', $route);
    }

    /**
     * Extract the request parameters from the route uri
     *
     * @access  public
     * @return  array   $params The request parameters
     */
    public function parameters()
    {
        $params = array();

        $route = explode('/', $this->route);
        $request = explode('/', $this->request);

        array_walk($route, function($param, $key) use ($request, &$params)
        {
            if(strpos($param, ':') !== false || strpos($param, ';') !== false)
            {
                $params[] = $request[$key];
            }
        });

        return $params;
    }

    /**
     * Check if $request matches the route uri
     *
     * @access  public
     * @param   string  $request    The request uri
     * @return  boolean             Returns true if the request matches the route uri
     */
    public function match($request)
    {
        return (bool) preg_match('#^' . $this->pattern($this->route) . '$#', $request);
    }

    /**
     * Check if the route allows $method
     *
     * @access  public
     * @param   string  $method The request method
     * return   boolean         Returns true if the method is allowed
     */
    public function hasMethod($method)
    {
        return ($this->method === $method) ? true : false;
    }

    /**
     * Add a filter that will be run before a successful request
     *
     * @access  public
     * @param   Closure $callback   The filter function to be executed
     * @return  object
     */
    public function before(Closure $callback)
    {
        $this->filters['before'] = $callback;
        return $this;
    }

    /**
     * Add a filter that will be run after a successful request
     *
     * @access  public
     * @param   Closure $callback   The filter function to be executed
     * @return  object
     */
    public function after(Closure $callback)
    {
        $this->filters['after'] = $callback;
        return $this;
    }

    /**
     * Try calling a filter based on $key
     *
     * @access  public
     * @param   string  $key    The filter key identifier
     * @return  void
     */
    public function callFilter($key)
    {
        if(isset($this->filters[$key]))
        {
            $this->filters[$key]();
        }
    }

    /**
     * Gets the route response
     *
     * @access  public
     * @param   object  $app    The core framework class to be used and passed along
     * @return  mixed           Returns the reponse of the method/function
     */
    public function getResponse(Cloudlib $app)
    {
        if($this->response instanceof Closure)
        {
            $reflection = new ReflectionFunction($this->response);

            return $reflection->invokeArgs($this->parameters());
        }

        if(is_array($this->response))
        {
            if(isset($this->response['class']))
            {
                $classname = $this->response['class'];

                $class = new $classname($app);

                $method = isset($this->response['method']) ? $this->response['method'] : $app->request->method;

                $reflection = new ReflectionMethod($class, $method);

                return $reflection->invokeArgs($class, $this->parameters());
            }
        }
    }
}
