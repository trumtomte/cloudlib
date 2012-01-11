<?php
/**
 * CloudLib :: Lightweight RESTful MVC PHP Framework
 *
 * @author      Sebastian Book <email>
 * @copyright   Copyright (c) 2011 Sebastian Book <email>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     cloudlib
 */

/**
 * <class name>
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <email>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Router
{
    /**
     * Route map
     *
     * @access  protected
     * @var     string
     */
    protected $map = null;

    /**
     * Request object
     *
     * @access  protected
     * @var     object
     */
    protected $request;

    /**
     * Base uri
     *
     * @access  protected
     * @var     string
     */
    protected $baseUri;

    /**
     * Determine wether the route is valid or not
     *
     * @access  public
     * @var     boolean
     */
    public $validRoute = false;

    /**
     * Determine wether the method is valid or not
     *
     * @access  public
     * @var     boolean
     */
    public $validMethod = false;

    /**
     * The response
     *
     * @access  public
     * @var     string
     */
    public $response;

    /**
     * Constructor
     * 
     * Sets the request object and the base uri
     *
     * @access  public
     * @param   object  $request
     * @param   string  $baseUri
     * @return  void
     */
    public function __construct(Request $request, $baseUri = '/')
    {
        $this->request = $request;
        $this->baseUri = $baseUri;
    }

    /**
     * Register a route map
     *
     * @access  public
     * @param   string  $map
     * @return  void
     */
    public function registerMap($map)
    {
        if( ! file_exists($map))
        {
            throw new RuntimeException(sprintf('Route map [%s] does not exist!',
                $file));
        }
        $this->map = $map;
    }

    /**
     * Parse the route map and set the response
     *
     * @access  public
     * @return  boolean
     */
    public function parseMap()
    {
        if($this->map === null)
        {
            throw new RuntimeException('No map has been set!');
        }

        $app = new Controller($this->request->input);

        $routes = require $this->map;

        if( ! is_array($routes))
        {
            throw new LogicException(sprintf('The route map [%s] must return an array',
                $routes));
        }

        // Remove the base uri from the requested uri
        $requestUri = rtrim(preg_replace('#' . $this->baseUri . '#', '',
            $this->request->uri), '/');

        // Check for a literal match
        if(array_key_exists($index = $this->request->method . $requestUri, $routes))
        {
            $this->validRoute = $this->validMethod = true;

            $this->response = $this->getRouteResponse($routes[$index]);

            return true;
        }

        // Go through all the routes in the route map
        foreach($routes as $route => $response)
        {
            list($method, $uri) = explode(' ', $route);

            // Escape all slashes ("/") and replace variables (:name) with the regex.
            $regex = str_replace('/', '\/',
                preg_replace('/(:[a-zA-Z0-9\.\-_]+)/', '[a-zA-Z0-9\.\-_]+', $uri));

            if(preg_match('#^' . $regex . '$#', $requestUri))
            {
                $this->validRoute = true;

                list($uriParts, $requestUriParts) = array(explode('/', $uri),
                    explode('/', $requestUri));

                $parameters = array();

                foreach($uriParts as $index => $value)
                {
                    if(strpos($value, ':') !== false)
                    {
                        $parameters[] = $requestUriParts[$index];
                    }
                }

                if($method == $this->request->method)
                {
                    $this->validMethod = true;

                    $this->response = $this->getRouteResponse($response, $parameters);

                    return true;
                }
            }
        }
    }


    /**
     * Parse the route response from the given file of routes and return the contents
     *
     * @access  protected
     * @param   mixed   $response
     * @param   array   $parameters
     * @return  string
     */
    protected function getRouteResponse($response, array $parameters = array())
    {
        if($response instanceof Closure)
        {
            $func = new ReflectionFunction($response);
            return $func->invokeArgs($parameters);
        }

        if(strpos($response, '.') !== false)
        {
            list($class, $method) = explode('.', $response);
        }
        else
        {
            $class = $response;
            $method = $this->request->method;
        }

        $class .= 'Controller';

        $controller = new $class($this->request->input);

        if(method_exists($controller, 'before'))
        {
            $controller->before();
        }

        $refMethod = new ReflectionMethod($controller, $method);
        return $refMethod->invokeArgs($controller, $parameters);
    }
}
