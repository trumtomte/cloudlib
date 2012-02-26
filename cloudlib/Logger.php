<?php
/**
 * CloudLib :: Lightweight RESTful MVC PHP Framework
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     Cloudlib
 */

namespace cloudlib;

// SPL
use RuntimeException;

/**
 * <class name>
 *
 * <short description>
 *
 * @package     Cloudlib
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Logger
{
    /**
     * File to write the logs to
     *
     * @access  protected
     * @var     string
     */
    protected $file;

    /**
     * Array of log messages
     *
     * @access  protected
     * @var     array
     */
    protected $messages = array();

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct($file)
    {
        $this->file = $file;
        register_shutdown_function(array($this, 'write'));
    }

    /**
     * Log a message
     *
     * @access  public
     * @param   mixed   $message
     * @param   mixed   $severity
     * @return  void
     */
    public function log($message, $severity = 0)
    {
        $severity = $this->getLevel($severity);

        if(is_array($message))
        {
            foreach($message as $value)
            {
                $this->messages[] = sprintf('[%s][%s]: %s', date('Y-m-d G:i:s'),
                    $severity, $value);
            }
        }
        else
        {
            $this->messages[] = sprintf('[%s][%s]: %s', date('Y-m-d G:i:s'), $severity,
                $message);
        }
    }

    /**
     * Write all log messages to the log file
     *
     * @access  public
     * @return  boolean
     */
    public function write()
    {
        $contents = null;

        if(empty($this->messages))
        {
            return false;
        }

        foreach($this->messages as $message)
        {
            $contents .= $message . PHP_EOL;
        }

        if($contents !== null)
        {
            try
            {
                file_put_contents($this->file, $contents, LOCK_EX | FILE_APPEND);
            }
            catch(RuntimeException $e)
            {
                throw new RuntimeException(sprintf('Unable to write to the logfile [%s]',
                    $file));
            }
            return true;
        }
    }

    /**
     * Returns the severity level as a string
     *
     * @access  protected
     * @param   int     $level
     * @return  string
     */
    protected function getLevel($level = 0)
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
            case 'WARNING':
                return 'WARNING';
                break;
            case 3:
            case 'ERROR':
                return 'ERROR';
                break;
            default:
                return 'DEBUG';
                break;
        }
    }

    /**
     * Shorthand function for log()
     *
     * @access  public
     * @param   string  $message
     * @return  void
     */
    public function debug($message)
    {
        $this->log($message, 0);
    }

    /**
     * Shorthand function for log()
     *
     * @access  public
     * @param   string  $message
     * @return  void
     */
    public function info($message)
    {
        $this->log($message, 1);
    }

    /**
     * Shorthand function for log()
     *
     * @access  public
     * @param   string  $message
     * @return  void
     */
    public function warning($message)
    {
        $this->log($message, 2);
    }

    /**
     * Shorthand function for log()
     *
     * @access  public
     * @param   string  $message
     * @return  void
     */
    public function error($message)
    {
        $this->log($message, 3);
    }

    /**
     * Return the current logged messages
     *
     * @access  public
     * @return  array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
