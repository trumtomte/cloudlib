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
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Request extends Factory
{
    /**
     * Shorthand names for $_SERVER keys
     *
     * @access  private
     * @var     array
     */
    private static $serverKeys = array(
        'host'       => 'HTTP_HOST',
        'agent'      => 'HTTP_USER_AGENT',
        'servername' => 'SERVER_NAME',
        'serverport' => 'SERVER_PORT',
        'filename'   => 'SCRIPT_FILENAME',
        'protocol'   => 'SERVER_PROTOCOL',
        'method'     => 'REQUEST_METHOD',
        'query'      => 'QUERY_STRING',
        'uri'        => 'REQUEST_URI',
        'scriptname' => 'SCRIPT_NAME',
        'self'       => 'PHP_SELF',
        'time'       => 'REQUEST_TIME',
        'ip'         => 'REMOTE_ADDR'
    );

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

        if(array_key_exists($key, static::$serverKeys))
        {
            return isset($_SERVER[static::$serverKeys[$key]])
                    ? $_SERVER[static::$serverKeys[$key]]
                    : false;
        }

        return isset($_SERVER[$key]) ? $_SERVER[$key] : false;
    }

    /**
     * Get the current request method
     *
     * @access  public
     * @return  mixed
     */
    public static function method()
    {
        return static::_server('REQUEST_METHOD');
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

