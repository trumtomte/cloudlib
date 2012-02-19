<?php
/**
 * CloudLib :: Lightweight RESTful MVC PHP Framework
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     Cloudlib
 */

/**
 * <class name>
 *
 * <short description>
 *
 * @package     Cloudlib
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Request
{
    /**
     * Array of server variables
     *
     * @access  protected
     * @var     array
     */
    protected $server;

    /**
     * Array of get variables
     *
     * @access  protected
     * @var     array
     */
    protected $get;

    /**
     * Array of post variables
     *
     * @access  protected
     * @var     array
     */
    protected $post;

    /**
     * Array of file variables
     *
     * @access  public
     * @var     array
     */
    public $files;

    /**
     * Array of cookie variables
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
     * The request input, GET/POST etc.
     *
     * @access  public
     * @var     array
     */
    public $input;

    /**
     * Constructor.
     *
     * Sets all the Global variables, this enables it to be mocked
     *
     * @access  public
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
     * @param   string  $key
     * @return  mixed
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
     * @return  string
     */
    public function method()
    {
        return ($this->server('REQUEST_METHOD'))
            ? strtoupper($this->server('REQUEST_METHOD')) : null;
    }

    /**
     * Check if the request method is get
     *
     * @access  public
     * @return  boolean
     */
    public function isGet()
    {
        return ($this->method() === 'GET') ? true : false;
    }

    /**
     * Check if the request method is post
     *
     * @access  public
     * @return  boolean
     */
    public function isPost()
    {
        return ($this->method() === 'POST') ? true : false;
    }

    /**
     * Check if the request method is put
     *
     * @access  public
     * @return  boolean
     */
    public function isPut()
    {
        return ($this->method() === 'PUT') ? true : false;
    }

    /**
     * Check if the request method is delete
     *
     * @access  public
     * @return  boolean
     */
    public function isDelete()
    {
        return ($this->method() === 'DELETE') ? true : false;
    }

    /**
     * Check if the request method is head
     *
     * @access  public
     * @return  boolean
     */
    public function isHead()
    {
        return ($this->method() === 'HEAD') ? true : false;
    }

    /**
     * Check if the requested method is allowed
     *
     * @access  public
     * @return  boolean
     */
    public function methodAllowed()
    {
        if(in_array($this->method(), array('GET', 'POST', 'PUT', 'DELETE', 'HEAD')))
        {
            return true;
        }
        return false;
    }

    /**
     * Check if the request method is XHR
     *
     * @access  public
     * @return  boolean
     */
    public function isAjax()
    {
        return ($this->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest') ? true : false;
    }

    /**
     * Check if we're using HTTPS
     *
     * @access  public
     * @return  boolean
     */
    public function isSecure()
    {
        return ($this->server('HTTPS') !== false && $this->server('HTTPS') !== 'off')
            ? true : false;
    }

    /**
     * Return a filtered uri as an array
     *
     * @access  public
     * @return  array
     */
    public function uri()
    {
        $uri = $this->server('REQUEST_URI');

        if($uri)
        {
            return parse_url(preg_replace('/\/{2,}/', '/', filter_var($uri,
                FILTER_SANITIZE_URL)), PHP_URL_PATH);
        }

        return '/';
    }

    /**
     * Return the current protocol
     *
     * @access  public
     * @return  string
     */
    public function protocol()
    {
        return ($this->server('SERVER_PROTOCOL')) ? $this->server('SERVER_PROTOCOL') : 'HTTP/1.1';
    }

    /**
     * Return the input
     *
     * @access  public
     * @return  array
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
