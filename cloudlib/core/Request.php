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
 * The Request class
 *
 * @copyright   Copyright (c) 2012 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Request
{
    /**
     * Array of $_SERVER variables
     *
     * @access  public
     * @var     array
     */
    public $server;

    /**
     * Array of $_GET variables
     *
     * @access  public
     * @var     array
     */
    public $get;

    /**
     * Array of $_POST variables
     *
     * @access  public
     * @var     array
     */
    public $post;

    /**
     * Array of $_FILES variables
     *
     * @access  public
     * @var     array
     */
    public $files;

    /**
     * Array of $_COOKIE variables
     *
     * @access  public
     * @var     array
     */
    public $cookies;

    /**
     * The request uri
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
     * Request arguments (GET, POST, PUT, DELETE)
     *
     * @access  public
     * @var     array
     */
    public $arguments;

    /**
     * Set all global arrays, request uri, request method and request variables
     *
     * @access  public
     * @param   array   $server     The $_SERVER array
     * @param   array   $get        The $_GET array
     * @param   array   $post       The $_POST array
     * @param   array   $files      The $_FILES array
     * @param   array   $cookies    The $_COOKIE array
     * @return  void
     */
    public function __construct(array $server = [], array $get = [],
        array $post = [], array $files = [], array $cookies = [])
    {
        $this->server = $server;
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
        $this->cookies = $cookies;

        $this->uri = $this->filterUri();
        $this->method = $this->method();
        $this->arguments = $this->getArguments();
    }

    /**
     * Get a $_SERVER variable
     *
     * @access  public
     * @param   string              $key    The array key identifier
     * @return  string|array|false          Return $_SERVER array if $key=null, else return the $_SERVER[$key] if set, else false
     */
    public function server($key = null)
    {
        if($key === null)
        {
            return $this->server;
        }
        return isset($this->server[$key]) ? $this->server[$key] : false;
    }

    /**
     * Get a request variable based on $key
     *
     * @access  public
     * @param   string              $key    The array key identifier
     * @return  string|array|false          Return the input variable value if found, else false
     */
    public function get($key)
    {
        return isset($this->arguments[$key]) ? $this->arguments[$key] : false;
    }

    /**
     * Get the current request method
     *
     * @access  public
     * @return  string|null Return the request method, else null
     */
    public function method()
    {
        return $this->server('REQUEST_METHOD') ? strtoupper($this->server('REQUEST_METHOD')) : null;
    }

    /**
     * Check if the request method is GET
     *
     * @access  public
     * @return  boolean Return true if the request method is GET, else false
     */
    public function isGet()
    {
        return ($this->method() === 'GET') ? true : false;
    }

    /**
     * Check if the request method is POST
     *
     * @access  public
     * @return  boolean Return true if the request method is POST, else false
     */
    public function isPost()
    {
        return ($this->method() === 'POST') ? true : false;
    }

    /**
     * Check if the request method is PUT
     *
     * @access  public
     * @return  boolean Return true if the request method is PUT, else false
     */
    public function isPut()
    {
        return ($this->method() === 'PUT') ? true : false;
    }

    /**
     * Check if the request method is DELETE
     *
     * @access  public
     * @return  boolean Return true if the request method is DELETE, else false
     */
    public function isDelete()
    {
        return ($this->method() === 'DELETE') ? true : false;
    }

    /**
     * Check if the request method is HEAD
     *
     * @access  public
     * @return  boolean Return true if the request method is HEAD, else false
     */
    public function isHead()
    {
        return ($this->method() === 'HEAD') ? true : false;
    }

    /**
     * Check if the requested method is allowed, (GET, POST, PUT, DELETE, HEAD)
     *
     * @access  public
     * @return  boolean Return true if the request method is allowed, else false
     */
    public function methodAllowed()
    {
        return in_array($this->method(), ['GET', 'POST', 'PUT', 'DELETE', 'HEAD']) ? true : false;
    }

    /**
     * Check if the request method is XHR
     *
     * @access  public
     * @return  boolean Return true if XHR has been set, else false
     */
    public function isXhr()
    {
        return ($this->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest') ? true : false;
    }

    /**
     * Same as Request::isXhr()
     *
     * @access  public
     * @return  boolean Return true if XHR has been set, else false
     */
    public function isAjax()
    {
        return $this->isXhr();
    }

    /**
     * Check if we're using HTTPS
     *
     * @access  public
     * @return  boolean Return true if the server variable HTTPS is set, else false
     */
    public function isSecure()
    {
        return ($this->server('HTTPS') !== false && $this->server('HTTPS') !== 'off') ? true : false;
    }

    /**
     * Check if the content type is JSON
     *
     * @access  public
     * @return  boolean Return true if the content type is json, else false
     */
    public function isJson()
    {
        return ($this->server('CONTENT_TYPE') == 'application/json') ? true : false;
    }

    /**
     * Return the current protocol
     *
     * @access  public
     * @return  string  Return the current server protocol, else HTTP/1.1
     */
    public function protocol()
    {
        return $this->server('SERVER_PROTOCOL') ? $this->server('SERVER_PROTOCOL') : 'HTTP/1.1';
    }

    /**
     * Get the current User Agent
     *
     * @access  public
     * @return  string|boolean  Return the user agent, else false
     */
    public function userAgent()
    {
        return $this->server('USER_AGENT');
    }

    /**
     * Get the current HTTP Host
     *
     * @access  public
     * @return  string|boolean  Return the HTTP Host, else false
     */
    public function host()
    {
        return $this->server('HTTP_HOST');
    }

    /**
     * Get the current Server Port
     *
     * @access  public
     * @return  int     Returns the current Server Port, else false
     */
    public function port()
    {
        return (int) $this->server('SERVER_PORT');
    }

    /**
     * Get the current Content Type
     *
     * @access  public
     * @return  string|boolean  Return the current Content Type, else false
     */
    public function contentType()
    {
        return $this->server('CONTENT_TYPE');
    }

    /**
     * Get the current IP adress being used (with a grain of salt)
     *
     * @access  public
     * @return  string|boolean  Return the IP, else false
     */
    public function ip()
    {
        return $this->server('X_FORWARDED_FOR') ? $this->server('X_FORWARDED_FOR') : $this->server('REMOTE_ADDR');
    }

    /**
     * Return a filtered uri
     *
     * @access  public
     * @return  string  Return an filtered uri string if an request uri as been set, else '/'
     */
    public function filterUri()
    {
        $uri = $this->server('REQUEST_URI');

        if($uri)
        {
            return parse_url(preg_replace('/\/{2,}/', '/', filter_var($uri, FILTER_SANITIZE_URL)), PHP_URL_PATH);
        }

        return '/';
    }

    /**
     * Returns an array of HTTP request variables based on the request method
     *
     * @access  public
     * @return  array   Return an array of HTTP request variables
     */
    public function getArguments()
    {
        if(in_array($this->method, ['POST', 'PUT', 'DELETE']) && $this->isJson())
        {
            return file_get_contents('php://input');
        }

        switch($this->method())
        {
            case 'GET':
                $args = $this->get;
                break;
            case 'POST':
                $args = $this->post;
                break;
            case 'PUT':
            case 'DELETE':
                parse_str(file_get_contents('php://input'), $args);
                break;
            default:
                $args = [];
                break;
        }

        return $args;
    }
}
