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
 * The log class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
final class Logger extends Factory
{
    /**
     * Constant for information messages
     *
     * @access  public
     * @var     string
     */
    const INFO = 0;

    /**
     * Constant for debug messages
     *
     * @access  public
     * @var     string
     */
    const DEBUG = 1;

    /**
     * Constant for error messages
     *
     * @access  public
     * @var     string
     */
    const ERROR = 2;

    /**
     * Array of all log messages
     *
     * @access  private
     * @var     array
     */
    private static $messages = array();

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Log a message
     *
     * @access  public
     * @param   string  $msg
     * @param   int     $severity
     * @return  void
     */
    public static function log($msg, $severity = null)
    {
        if($severity === null)
        {
            $severity = self::DEBUG;
        }

        $level = static::getLevel($severity);

        $date = Config::log('dateformat');
        
        static::$messages[] = '[' . date($date) . '][' . $level .']: ' . $msg;
    }

    /**
     * Write the message to the log file
     *
     * @access  public
     * @return  void
     */
    public static function write()
    {
        $messages = null;

        foreach(static::$messages as $message)
        {
            $messages .= $message . PHP_EOL;
        }

        $file = LOGS . Config::log('filename') . '.log';

        if(Config::log('overwrite') === true)
        {
            file_put_contents($file, $messages);
        }
        else
        {
            file_put_contents($file, $messages . PHP_EOL, FILE_APPEND);
        }
    }

    /**
     * Get the severity level as string
     *
     * @access  private
     * @param   int     $level
     * @return  string
     */
    private static function getLevel($level)
    {
        switch($level)
        {
            case self::INFO:
                return 'INFO';
                break;
            case self::DEBUG:
                return 'DEBUG';
                break;
            case self::ERROR:
                return 'ERROR';
                break;
        }
    }
}
