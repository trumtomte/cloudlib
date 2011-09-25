<?php
/**
 * Cloudlib :: Minor PHP (M)VC Framework
 *
 * @author      Sebastian Book <sebbebook@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     cloudlib
 */

/**
 * The server class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class server extends master
{
    /**
     * Shorthand function
     *
     * @access  public
     * @return  string
     */
    public static function host()
    {
        return static::get('HTTP_HOST');
    }

    /**
     * Shorthand function
     *
     * @access  public
     * @return  string
     */
    public static function agent()
    {
        return static::get('HTTP_USER_AGENT');
    }

    /**
     * Shorthand function
     *
     * @access  public
     * @return  string
     */
    public static function servername()
    {
        return static::get('SERVER_NAME');
    }

    /**
     * Shorthand function
     *
     * @access  public
     * @return  string
     */
    public static function serverport()
    {
        return static::get('SERVER_PORT');
    }

    /**
     * Shorthand function
     *
     * @access  public
     * @return  string
     */
    public static function filename()
    {
        return static::get('SCRIPT_FILENAME');
    }

    /**
     * Shorthand function
     *
     * @access  public
     * @return  string
     */
    public static function protocol()
    {
        return static::get('SERVER_PROTOCOL');
    }

    /**
     * Shorthand function
     *
     * @access  public
     * @return  string
     */
    public static function method()
    {
        return static::get('REQUEST_METHOD');
    }

    /**
     * Shorthand function
     *
     * @access  public
     * @return  string
     */
    public static function query()
    {
        return static::get('QUERY_STRING');
    }

    /**
     * Shorthand function
     *
     * @access  public
     * @return  string
     */
    public static function uri()
    {
        return static::get('REQUEST_URI');
    }

    /**
     * Shorthand function
     *
     * @access  public
     * @return  string
     */
    public static function scriptname()
    {
        return static::get('SCRIPT_NAME');
    }

    /**
     * Shorthand function
     *
     * @access  public
     * @return  string
     */
    public static function self()
    {
        return static::get('PHP_SELF');
    }

    /**
     * Shorthand function
     *
     * @access  public
     * @return  string
     */
    public static function time()
    {
        return static::get('REQUEST_TIME');
    }

    /**
     * Get server index value
     *
     * @access  public
     * @param   string  $index
     * @return  string
     */
    public static function get($index)
    {
        return $_SERVER[$index];
    }

    /**
     * CallStatic function for shorter get calls
     *
     * @access  public
     * @param   string  $index
     * @param   array   $args
     * @return  void
     */
    public static function __callStatic($index, array $args = array())
    {
        return static::get($index);
    }
}
