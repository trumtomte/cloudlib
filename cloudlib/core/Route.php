<?php
/**
 * Cloudlib
 *
 * @author      Sebastian Bengtegård <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2013 Sebastian Bengtegård <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace cloudlib\core;

/**
 * The Route class
 *
 * @copyright   Copyright (c) 2013 Sebastian Bengtegård <cloudlibframework@gmail.com>
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
    public $uri;

    /**
     * The route responses (Method => Response)
     *
     * @access  public
     * @var     array
     */
    public $response = [];

    /**
     * Array of allowed request methods
     *
     * @access  public
     * @var     array
     */
    public $methods = [];


    /**
     * Sets the $route, $method and $response
     *
     * @access  public
     * @param   string  $uri        The route uri
     * @param   string  $method     Allowed request method
     * @param   mixed   $response   The route response
     * @return  void
     */
    public function __construct($uri, array $methods, callable $response)
    {
        $this->uri = $uri;
        $this->methods = $methods;

        foreach($methods as $method)
        {
            $this->response[$method] = $response;
        }
    }

    /**
     * Append more route responses based on $methods to the Route
     *
     * @access  public
     * @param   array       $methods    The http methods
     * @param   callable    $response   The route response
     * @return  void
     */
    public function append(array $methods, callable $response)
    {
        $this->methods = array_merge($this->methods, $methods);

        foreach($methods as $method)
        {
            $this->response[$method] = $response;
        }
    }

    /**
     * Create a pattern for regex matching of this route uri
     *
     * @access  public
     * @param   string  $uri    The route uri
     * @return  string          Returns the pattern for regex matching
     */
    public function pattern($uri)
    {
        $regex = [
            '/:str/' => '([a-zA-Z]+)',
            '/:int/' => '(\d+)',
            '/:alpha/' => '([a-zA-Z0-9]+)',
            '/:(\w+)/' => '(\w+)',
            '/;/' => ''
        ];

        array_walk($regex, function($replacement, $pattern) use (&$uri)
        {
            $uri = preg_replace($pattern, $replacement, $uri);
        });

        return str_replace('/', '\/', $uri);
    }

    /**
     * Extract the request parameters from the route uri
     *
     * @access  public
     * @param   string  $request    The request uri
     * @return  array   $params     The request parameters
     */
    public function parameters($request)
    {
        $params = [];

        $uri = explode('/', $this->uri);
        $request = explode('/', $request);

        array_walk($uri, function($param, $key) use ($request, &$params)
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
        return (bool) preg_match('#^' . $this->pattern($this->uri) . '$#', $request);
    }

    /**
     * Check if the route allows $method
     *
     * @access  public
     * @param   string  $method The request method
     * return   boolean         Returns true if the method is allowed
     */
    public function allowsMethod($method)
    {
        return in_array($method, $this->methods);
    }

    /**
     * Get a route response based on $method
     *
     * @access  public
     * @param   string  $method The http method
     * @return  callable        Returns the callable set to $method
     */
    public function response($method)
    {
        return $this->response[$method];
    }
}
