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
 * The Request class
 *
 * @copyright   Copyright (c) 2013 Sebastian Bengtegård <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Request
{
    /**
     * Array of $_SERVER variables
     *
     * @var array
     */
    public $server = [];

    /**
     * $_GET variables
     *
     * @var object
     */
    public $query;

    /**
     * $_POST variables
     *
     * @var object
     */
    public $body;

    /**
     * Array of $_FILES variables
     *
     * @var array
     */
    public $files = [];

    /**
     * Array of $_COOKIE variables
     *
     * @var array
     */
    public $cookies = [];

    /**
     * The request path
     *
     * @var string
     */
    public $path = '';

    /**
     * The request base path
     *
     * @var string
     */
    public $base = '/';

    /**
     * The request method
     *
     * @var string
     */
    public $method = '';

    /**
     * Request uri parameters
     *
     * @var object
     */
    public $params;

    /**
     * If request was ajax
     *
     * @var boolean
     */
    public $xhr = false;

    /**
     * If request was via HTTPS
     *
     * @var boolean
     */
    public $secure = false;

    /**
     * Request protocol
     *
     * @var string
     */
    public $protocol = '';

    /**
     * Request host
     *
     * @var string
     */
    public $host = '';

    /**
     * Request ip
     *
     * @var string
     */
    public $ip = '';

    /**
     * Request Content-Type
     *
     * @var string
     */
    public $type = '';

    /**
     * If request was json
     *
     * @var boolean
     */
    public $json = false;

    /**
     * Creates a new Request object from PHP Global arrays
     *
     * @param   array   $server     The $_SERVER array
     * @param   array   $query      The $_GET array
     * @param   array   $body       The $_POST array
     * @param   array   $files      The $_FILES array
     * @param   array   $cookies    The $_COOKIE array
     * @return  void
     */
    public function __construct(array $server = [], array $query = [],
        array $body = [], array $files = [], array $cookies = [])
    {
        $this->server = $server;
        $this->query = (object) $query;
        $this->body = $this->getRequestBody($body);
        $this->files = $files;
        $this->cookies = $cookies;

        $this->base = $this->getBase();
        $this->path = $this->getPath();
        $this->method = $this->getMethod();
        $this->xhr = $this->isXhr();
        $this->secure = $this->isSecure();
        $this->protocol = $this->getProtocol();
        $this->host = $this->getHost();
        $this->ip = $this->getIp();
        $this->type = $this->getType();
        $this->json = $this->isJson();
    }

    /**
     * Get a $_SERVER variable
     *
     * @param   string  $key    The $_SERVER key
     * @return  mixed           Returns the found item if found, else null
     */
    public function server($key)
    {
        return isset($this->server[$key]) ? $this->server[$key] : null;
    }

    /**
     * Gets the base request path
     *
     * @return  string  The base request path
     */
    public function getBase()
    {
        $base = '/';

        if($this->server('SCRIPT_NAME'))
        {
            $scriptPath = $this->server('SCRIPT_NAME');
            $scriptName = basename($scriptPath);
            $base = substr($scriptPath, 0, (strlen($scriptPath) - (strlen($scriptName) + 1)));
        }

        return $base;
    }

    /**
     * Gets the request path
     *
     * @return  string  The request path
     */
    public function getPath()
    {
        $path = '/';

        if($this->server('REQUEST_URI'))
        {
            $path = parse_url(preg_replace('/\/{2,}/', '/', filter_var($this->server['REQUEST_URI'], FILTER_SANITIZE_URL)), PHP_URL_PATH);
        }

        return $path;
    }

    /**
     * Gets the request method
     *
     * @return  string  The request method
     */
    public function getMethod()
    {
        return $this->server('REQUEST_METHOD');
    }

    /**
     * Tries to check if the request is XHR
     *
     * @return  bool    True if the request is XHR, else false
     */
    public function isXhr() 
    {
        $xhr = $this->server('HTTP_X_REQUESTED_WITH');
        return ($xhr && strtolower($xhr) === 'xmlhttprequest') ? true : false;
    }

    /**
     * Tries to check if the request was made through HTTPS
     *
     * @return  bool    True if the request was made through HTTPS, else false
     */
    public function isSecure()
    {
        $https = $this->server('HTTPS');
        return ($https && $https !== false && $https !== 'off') ? true : false;
    }

    /**
     * Gets the request protocol
     *
     * @return  string  The request protocol
     */
    public function getProtocol()
    {
        return $this->server('SERVER_PROTOCOL') ? $this->server('SERVER_PROTOCOL') : 'HTTP/1.1';   
    }

    /**
     * Gets the request host
     *
     * @return  string  The request host
     */
    public function getHost()
    {
       return $this->server('HTTP_HOST'); 
    }

    /**
     * Tries to get the request IP
     *
     * @return  string  The request IP
     */
    public function getIp()
    {
        return $this->server('X_FORWARDED_FOR') ? $this->server('X_FORWARDED_FOR') : $this->server('REMOTE_ADDR'); 
    }

    /**
     * Gets the request content type
     *
     * @return  string  The request content type
     */
    public function getType()
    {
        return $this->server('CONTENT_TYPE') ? $this->server('CONTENT_TYPE') : '';
    }

    /**
     * Tries to check if the request was JSON
     *
     * @return  bool    True if the request was JSON, else false
     */
    public function isJson()
    {
        $type = $this->getType();
        return ($type == 'application/json' || $type == 'application/x-json') ? true : false; 
    }

    /**
     * Gets the request body
     *
     * @access  public
     * @param   array   $input  The $_POST variable
     * @return  object          The request body as an object
     */
    public function getRequestBody($input)
    {
        $method = $this->getMethod();

        if($this->isJson())
        {
            return json_decode(file_get_contents('php://input'));
        }

        if(in_array($method, ['PUT', 'DELETE', 'PATCH']))
        {
            parse_str(file_get_contents('php://input'), $body);
            return (object) $body;
        }

        if($method == 'POST')
        {
            return (object) $input;
        }

        return null;
    }
}
