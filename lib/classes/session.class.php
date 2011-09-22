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
 * The session class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class session extends master
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
     * Session state,
     * defaults as false
     *
     * @access  private
     * @var     boolean
     */
    private static $session = self::SESSION_NOT_STARTED;

    /**
     * Magic method
     *
     * @access  private
     * @return  void
     */
    private function __clone() {}

    /**
     * Magic method
     *
     * @access  private
     * @return  void
     */
    private function __wakeup() {}

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
            throw new cloudException('A session has already been started');
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
            throw new cloudException('There is no session to be destroyed');
        }

        self::$session = self::SESSION_NOT_STARTED;

        unset($_SESSION);

        return session_destroy();
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
