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
class Request
{
    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Get a $_SERVER variable
     *
     * @access  public
     * @param   string  $key
     * @return  mixed
     */
    public static function server($key = null)
    {
        if($key === null)
        {
            return $_SERVER;
        }
        return isset($_SERVER[$key]) ? $_SERVER[$key] : false;
    }

    /**
     * Get the current request method
     *
     * @access  public
     * @return  string
     */
    public static function method()
    {
        return static::server('REQUEST_METHOD');
    }

    /**
     * Check if the request method is get
     *
     * @access  public
     * @return  boolean
     */
    public static function isGet()
    {
        return (static::method() === 'GET') ? true : false;
    }

    /**
     * Check if the request method is post
     *
     * @access  public
     * @return  boolean
     */
    public static function isPost()
    {
        return (static::method() === 'POST') ? true : false;
    }

    /**
     * Check if the request method is put
     *
     * @access  public
     * @return  boolean
     */
    public static function isPut()
    {
        return (static::method() === 'PUT') ? true : false;
    }

    /**
     * Check if the request method is delete
     *
     * @access  public
     * @return  boolean
     */
    public static function isDelete()
    {
        return (static::method() === 'DELETE') ? true : false;
    }

    /**
     * Check if the request method is XHR
     *
     * @access  public
     * @return  boolean
     */
    public static function isAjax()
    {
        return (static::server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest') ? true : false;
    }

    /**
     * Check if we're using HTTPS
     *
     * @access  public
     * @return  boolean
     */
    public static function isSecure()
    {
        // TODO
    }

    /**
     * Return a filtered uri as an array
     *
     * @access  public
     * @return  array
     */
    public static function uri()
    {
        return (empty($_SERVER['REQUEST_URI']))
            ? '/'
            : filter_var(parse_url(preg_replace('/\/{2,}/', '/',
                $_SERVER['REQUEST_URI']), PHP_URL_PATH), FILTER_SANITIZE_URL);
    }

    /**
     * Return the current protocol
     *
     * @access  public
     * @return  string
     */
    public static function protocol()
    {
        return (static::server('SERVER_PROTOCOL')) ? static::server('SERVER_PROTOCOL') : 'HTTP/1.1';
    }

    /**
     * Return the input
     *
     * @access  public
     * @return  array
     */
    public static function input()
    {
        switch(Request::method())
        {
            case 'GET':
                $input = $_GET;
                break;
            case 'POST':
                $input = $_POST;
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
