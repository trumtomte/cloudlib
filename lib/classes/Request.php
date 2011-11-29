<?php
/**
 * CloudLib :: Lightweight MVC PHP Framework
 *
 * @author      Sebastian Book <sebbebook@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     cloudlib
 */

/**
 * The request class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes.core
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
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
     * Get a $_GET variable
     *
     * @access  public
     * @param   string  $key
     * @return  mixed
     */
    public static function _get($key = null)
    {
        if($key === null)
        {
            return $_GET;
        }
        return (isset($_GET[$key])) ? $_GET[$key] : false;
    }

    /**
     * Get a $_POST variable
     *
     * @access  public
     * @param   string  $key
     * @return  mixed
     */
    public static function _post($key = null)
    {
        if($key === null)
        {
            return $_POST;
        }
        return isset($_POST[$key]) ? $_POST[$key] : false;
    }

    /**
     * Get a $_COOKIE variable
     *
     * @access  public
     * @param   string  $key
     * @return  mixed
     */
    public static function _cookie($key = null)
    {
        if($key === null)
        {
            return $_COOKIE;
        }
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : false;
    }

    /**
     * Get a $_REQUEST variable
     *
     * @access  public
     * @param   string  $key
     * @return  mixed
     */
    public static function _request($key = null)
    {
        if($key === null)
        {
            return $_REQUEST;
        }
        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : false;
    }

    /**
     * Get a $_SERVER variable
     *
     * @access  public
     * @param   string  $key
     * @return  mixed
     */
    public static function _server($key = null)
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
        return static::_server('REQUEST_METHOD');
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
     * Check if the request method is AJAX
     *
     * @access  public
     * @return  boolean
     */
    public static function isAjax()
    {
        return (static::_server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest') ? true : false;
    }

    /**
     * Return a filtered uri as an array
     *
     * @access  public
     * @return  array
     */
    public static function uri()
    {
        $uri = (empty($_GET['uri'])) ? CONTROLLER : $_GET['uri'];

        return explode('/', filter_var($uri, FILTER_SANITIZE_URL));
    }

    /**
     * Shorthand function to get a variable from one of the global arrays
     *
     * @access  public
     * @param   string  $key
     * @param   string  $array
     * @return  mixed
     */
    public static function get($key)
    {
        switch(true)
        {
            case isset($_GET[$key]):
                return $_GET[$key];
                break;
            case isset($_POST[$key]):
                return $_POST[$key];
                break;
            case isset($_COOKIE[$key]):
                return $_COOKIE[$key];
                break;
            case isset($_REQUEST[$key]):
                return $_REQUEST[$key];
                break;
            case isset($_SERVER[$key]):
                return $_SERVER[$key];
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * Checks if magic quotes is enabled
     *
     * @access  private
     * @return  void
     */
    public static function removeMagicQuotes()
    {
        if(get_magic_quotes_gpc())
        {
            $_GET = static::stripslashRecursive($_GET);
            $_POST = static::stripslashRecursive($_POST);
            $_COOKIE = static::stripslashRecursive($_COOKIE);
            $_REQUEST = static::stripslashRecursive($_REQUEST);
        }
    }

    /**
     * Apply stripslashes() on each item in an array
     *
     * @access  private
     * @param   array   $array
     * @return  array
     */
    private static function stripslashRecursive($array)
    {
        foreach($array as $key => $value)
        {
            $array[$key] = is_array($value)
                ? static::stripslashRecursive($value)
                : stripslashes($value);
        }

        return $array;
    }

    /**
     * Shorthand function for get()
     *
     * @access  public
     * @param   string  $key
     * @param   array   $args
     * @return  mixed
     */
    public static function __callStatic($key, array $args = array())
    {
        return static::get($key);
    }
}
