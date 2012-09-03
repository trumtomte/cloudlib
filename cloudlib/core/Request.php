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
     * The request input (based on the request method)
     *
     * @access  public
     * @var     array
     */
    public $input;

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
    public function __construct(array $server = array(), array $get = array(),
        array $post = array(), array $files = array(), array $cookies = array())
    {
        $this->server = $server;
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
        $this->cookies = $cookies;

        $this->uri = $this->uri();
        $this->method = $this->method();
        $this->input = $this->input();
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
     * Check if the request method is get
     *
     * @access  public
     * @return  boolean Return true if the request method is get, else false
     */
    public function isGet()
    {
        return ($this->method() === 'GET') ? true : false;
    }

    /**
     * Check if the request method is post
     *
     * @access  public
     * @return  boolean Return true if the request method is post, else false
     */
    public function isPost()
    {
        return ($this->method() === 'POST') ? true : false;
    }

    /**
     * Check if the request method is put
     *
     * @access  public
     * @return  boolean Return true if the request method is put, else false
     */
    public function isPut()
    {
        return ($this->method() === 'PUT') ? true : false;
    }

    /**
     * Check if the request method is delete
     *
     * @access  public
     * @return  boolean Return true if the request method is delete, else false
     */
    public function isDelete()
    {
        return ($this->method() === 'DELETE') ? true : false;
    }

    /**
     * Check if the request method is head
     *
     * @access  public
     * @return  boolean Return true if the request method is head, else false
     */
    public function isHead()
    {
        return ($this->method() === 'HEAD') ? true : false;
    }

    /**
     * Check if the requested method is allowed, (get, post, put, delete, head)
     *
     * @access  public
     * @return  boolean Return true if the request method is allowed, else false
     */
    public function methodAllowed()
    {
        return in_array($this->method(), array('GET', 'POST', 'PUT', 'DELETE', 'HEAD')) ? true : false;
    }

    /**
     * Check if the request method is XHR
     *
     * @access  public
     * @return  boolean Return true if XHR has been set, else false
     */
    public function isAjax()
    {
        return ($this->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest') ? true : false;
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
     * Return a filtered uri as an array
     *
     * @access  public
     * @return  string  Return an filtered uri string if an request uri as been set, else '/'
     */
    public function uri()
    {
        $uri = $this->server('REQUEST_URI');

        if($uri)
        {
            return parse_url(preg_replace('/\/{2,}/', '/', filter_var($uri, FILTER_SANITIZE_URL)), PHP_URL_PATH);
        }

        return '/';
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
     * Return the input
     *
     * @access  public
     * @return  array   Return an array containing the request variables based on the request method
     */
    public function input()
    {
        switch($this->method())
        {
            case 'GET':
                $input = $this->get;
                break;
            case 'POST':
                $input = $this->post;
                break;
            case 'PUT':
            case 'DELETE':
                parse_str(file_get_contents('php://input'), $input);
                break;
            default:
                $input = array();
                break;
        }
        return $input;
    }
}
