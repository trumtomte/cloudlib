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
class Session extends Factory
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
    public function start()
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
    public function destroy()
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
    public function name($name = null)
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
    public function id($id = null)
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
    public function close()
    {
        session_write_close();
    }

    /**
     * Set a session variable
     *
     * @access  public
     * @param   string          $name
     * @param   string|integer  $value
     * @return  void
     */
    public function __set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * Get a session variable
     *
     * @access  public
     * @param   string  $name
     * @return  mixed
     */
    public function __get($name)
    {
        if(isset($_SESSION[$name]))
        {
            return $_SESSION[$name];
        }
    }

    /**
     * Check if a variable is set
     *
     * @access  public
     * @param   string @name
     * @return  string
     */
    public function __isset($name)
    {
        return isset($_SESSION[$name]);
    }

    /**
     * Unset a variable
     *
     * @access  public
     * @param   string $name
     * @return  void
     */
    public function __unset($name)
    {
        unset($_SESSION[$name]);
    }
}
