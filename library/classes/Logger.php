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
class Logger
{
    /**
     * Array of log messages
     *
     * @access  protected
     * @var     array
     */
    protected static $messages = null;

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
     * @param   mixed   $message
     * @param   mixed   $severity
     * @return  void
     */
    public static function log($message, $severity = 0)
    {
        $severity = static::getLevel($severity);

        if(is_array($message))
        {
            foreach($message as $value)
            {
                static::$messages[] = sprintf('[%s][%s]: %s',
                    date(Config::get('log.dateformat')), $severity, $value);
            }
        }
        else
        {
            static::$messages[] = sprintf('[%s][%s]: %s',
                date(Config::get('log.dateformat')), $severity, $message);
        }
    }

    /**
     * Write all log messages to the log file
     *
     * @access  public
     * @return  void
     */
    public static function write()
    {
        $contents = null;

        if(static::$messages === null)
        {
            return false;
        }

        foreach(static::$messages as $message)
        {
            $contents .= $message . PHP_EOL;
        }

        if($contents !== null)
        {
            try
            {
                file_put_contents($file = LOGS . Config::get('log.file'), $contents,
                    LOCK_EX | FILE_APPEND);
            }
            catch(RuntimeException $e)
            {
                throw new RuntimeException(sprintf('Unable to write to the logfile "%s"',
                    $file));
            }
        }
    }

    /**
     * Returns the severity level as a string
     *
     * @access  protected
     * @param   int     $level
     * @return  string
     */
    protected static function getLevel($level = 0)
    {
        switch($level)
        {
            case 0:
            case 'DEBUG':
                return 'DEBUG';
                break;
            case 1:
            case 'INFO':
                return 'INFO';
                break;
            case 2:
            case 'ERROR':
                return 'ERROR';
                break;
            default:
                return 'DEBUG';
                break;
        }
    }

    /**
     * Shorthand for Logger:log()
     *
     * @access  public
     * @param   string  $method
     * @param   array   $args
     * @return  void
     */
    public static function __callStatic($method, $args)
    {
        return static::log($args[0], strtoupper($method));
    }
}
