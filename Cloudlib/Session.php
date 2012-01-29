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
class Session
{
    /**
     * Constant for a started session
     *
     * @access  public
     * @var     boolean
     */
    const SESSION_STARTED = true;

    /**
     * Constant for a session that has not started
     *
     * @access  public
     * @var     boolean
     */
    const SESSION_NOT_STARTED = false;

    /**
     * Session state, default is false
     *
     * @access  protected
     * @var     boolean
     */
    protected static $session = self::SESSION_NOT_STARTED;

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Starts a session
     *
     * @access  public
     * @return  boolean
     */
    public static function start()
    {
        if(self::$session == self::SESSION_STARTED)
        {
            return false;
        }

        self::$session = self::SESSION_STARTED;

        return session_start();
    }

    /**
     * Ends the session,
     * destroys the session and unsets the variables
     *
     * @access  public
     * @return  boolean
     */
    public static function destroy()
    {
        if(self::$session == self::SESSION_NOT_STARTED)
        {
            return false;
        }

        self::$session = self::SESSION_NOT_STARTED;

        unset($_SESSION);

        return session_destroy();
    }

    /**
     * Set or get a name
     *
     * @access  public
     * @param   string  $name
     * @return  mixed
     */
    public static function name($name = null)
    {
        if($name === null)
        {
            return session_name();
        }

        session_name($name);
    }

    /**
     * Set or get an id
     *
     * @access  public
     * @param   string  $id
     * @return  mixed
     */
    public static function id($id = null)
    {
        if($id === null)
        {
            return session_id();
        }

        session_id($id);
    }

    /**
     * Write session data and end session
     *
     * @access  public
     * @return  void
     */
    public static function close()
    {
        session_write_close();
    }

    /**
     * Set a session variable
     *
     * @access  public
     * @param   string  $key
     * @param   mixed   $value
     * @return  void
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get a session variable
     *
     * @access  public
     * @param   string  $key
     * @return  void
     */
    public static function get($key)
    {
        return $_SESSION[$key];
    }

    /**
     * Delete a session variable
     *
     * @access  public
     * @param   string  $key
     * @return  void
     */
    public static function del($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * Get the CSRF token
     *
     * @access  public
     * @return  string
     */
    public static function token()
    {
        return static::get('csrf-token');
    }

    /**
     * Check if a session variable exists
     *
     * @access  public
     * @param   string  $key
     * @return  boolean
     */
    public static function has($key)
    {
        return (bool) isset($_SESSION[$key]);
    }
}
