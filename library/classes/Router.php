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
class Router extends Factory
{
    /**
     * The requested URI
     *
     * @access  public
     * @var     string
     */
    public $uri;

    /**
     * The request method
     *
     * @access  public
     * @var     string
     */
    public $method;

    /**
     * The base url
     *
     * @access  public
     * @var     string
     */
    public $baseurl;

    /**
     * Variable to determine if the route is valid
     *
     * @access  public
     * @var     boolean
     */
    public $validRoute = false;

    /**
     * Variable to determine if the method is valid
     *
     * @access  public
     * @var     boolean
     */
    public $validMethod = false;

    /**
     * Constructor
     *
     * @access  public
     * @param   string  $uri
     * @param   string  $method
     * @param   string  $baseurl
     * @return  void
     */
    public function __construct($uri, $method, $baseurl)
    {
        $this->uri = $uri;
        $this->method = $method;
        $this->baseurl = $baseurl;
    }

    /**
     * Validate the requested route from a file of routes
     *
     * @access  public
     * @param   string  $file
     * @return  boolean
     */
    public function validate($file)
    {
        $app = Controller::factory(Request::input());

        if( ! file_exists($file))
        {
            throw new RuntimeException(sprintf('"%s" does not exist',
                $file));
        }

        $routes = require $file;

        if( ! is_array($routes))
        {
            throw new LogicException(sprintf('The file "%s" must return an array',
                $file));
        }
        
        // Remove the baseurl from the requested uri
        $route = trim(preg_replace('#' . $this->baseurl . '#', '', $this->uri), '/');

        // Check for a literal match
        if(array_key_exists($index = $this->method . ' /' . $route, $routes))
        {
            $this->validRoute = $this->validMethod = true;

            $this->response = $this->parseRouteResponse($routes[$index]);

            return true;
        }

        // No literal match was found, parse through the routes array
        return $this->parseRoutes($route, $routes, $app);
    }

    /**
     * Parse through the array of routes and set the response body
     *
     * @access  protected
     * @param   string  $route
     * @param   array   $routes
     * @param   object  $app
     * @return  mixed
     */
    protected function parseRoutes($route, array $routes, Controller $app)
    {
        // Split the requested uri into parts
        $routeParts = explode('/', $route);

        foreach($routes as $index => $value)
        {
            // Get the method and the uri
            list($method, $uri) = explode(' ', $index);

            if($uri === '/')
            {
                $uri = null;
            }
            
            // Split the stored uri into parts
            $uriParts = explode('/', trim($uri, '/'));

            // Compare the length of the two arrays of parts
            if(count($uriParts) == count($routeParts))
            {
                $params = array();

                // Replace all parameters in the uri to the regex
                // Store all the parameters in the params array
                foreach($uriParts as $key => &$value)
                {
                    if(strpos($value, ':') !== false)
                    {
                        $value = '[a-zA-Z0-9\.\-_]+';
                        $params[] = $routeParts[$key];
                    }
                }

                // Create the regex
                $regex = implode('\/', $uriParts);

                if(preg_match('/^' . $regex . '$/', $route))
                {
                    $this->validRoute = true;

                    if($this->method == $method)
                    {
                        $this->validMethod = true;

                        $this->response = $this->parseRouteResponse($routes[$index], $params);

                        return true;
                    }
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
    protected function parseRouteResponse($response, array $parameters = array())
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
            $method = $this->method;
        }

        $class .= 'Controller';

        $controller = $class::factory(Request::input());

        if(method_exists($controller, 'before'))
        {
            $controller->before();
        }

        $refMethod = new ReflectionMethod($controller, $method);
        return $refMethod->invokeArgs($controller, $parameters);
    }
}
