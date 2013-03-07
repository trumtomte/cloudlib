<?php
/**
 * Cloudlib
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2012 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace cloudlib\core;

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
    public $uri;

    /**
     * The route responses
     *
     * @access  public
     * @var     mixed
     */
    public $response = [];

    /**
     * Allowed request methods
     *
     * @access  public
     * @var     string
     */
    public $methods = [];


    /**
     * Sets the $route, $method and $response
     *
     * @access  public
     * @param   string  $route      The route uri
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
     * @param   string  $route  The route uri
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
     * @return  array   $params The request parameters
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

    public function response($method)
    {
        return $this->response[$method];
    }
}
