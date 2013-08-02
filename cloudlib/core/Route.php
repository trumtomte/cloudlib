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
     * The uri path
     *
     * @var string
     */
    public $path = '';

    /**
     * The route callbacks
     *
     * @var array
     */
    public $callbacks = [];

    /**
     * Route method
     *
     * @var array
     */
    public $method = '';

    /**
     * Route path regexp
     *
     * @var string
     */
    public $regexp = '';

    /**
     * Route parameters
     *
     * @var array
     */
    public $params = [];

    /**
     * Create a new Route object
     *
     * @param   string  $path       The route uri
     * @param   string  $method     Allowed request method
     * @param   mixed   $callbacks  The route response
     * @return  void
     */
    public function __construct($path, $method, array $callbacks)
    {
        $this->path = $path;
        $this->method = $method;
        $this->callbacks = $callbacks;
        $this->regexp = $this->pattern($path);
    }

    /**
     * Create a pattern for regexp matching of this route path
     *
     * @param   string  $path   The route path
     * @return  string          Returns the pattern for regexp matching
     */
    public function pattern($path)
    {
        return str_replace('/', '\/', 
            preg_replace('/;/', '',
            preg_replace('/:(\w+)/', '(\w+)', $path)));
    }

    /**
     * Set the request parameters
     *
     * @param   string  $request    The request path
     * @return  void
     */
    public function setParams($request)
    {
        $pathParts = explode('/', $this->path);
        $requestParts = explode('/', $request);

        foreach($pathParts as $index => $part)
        {
            // Named
            if(strpos($part, ':') !== false)
            {
                $this->params[substr($part, 1)] = $requestParts[$index];
            }
            // Regexp
            if(strpos($part, ';') !== false)
            {
                $this->params[] = $requestParts[$index];
            }
        } 

        return $this;
    }

    /**
     * Check if a given request path matches the route regexp (path)
     *
     * @param   string  $path   The request path
     * @return  bool            True if the request path matches the route regexp
     */
    public function match($path)
    {
        $path = ($path == '/') ? $path : rtrim($path, '/');
        return (bool) preg_match('#^' . $this->regexp . '$#', $path);
    }
}
