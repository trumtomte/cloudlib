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
 * The core class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Core
{
    /**
     * Current version of cloudlib
     *
     * @access  public
     */
    const VERSION = '0.3.9.1';

    /**
     * Array of current activated modules
     *
     * @access  private
     * @var     array
     */
    private static $helpers = array();

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Initialize
     *
     * @access  public
     * @return  void
     */
    public static function initialize()
    {
        $config = config::general();
        static::setTimezone($config['timezone']);
        static::setLocale($config['locale']);
        static::setMbEncoding($config['mbstring']);

        static::removeMagicQuotes();

        $dispatcher = Dispatcher::factory();
        $dispatcher->dispatch();
    }

    /**
     * Set the default timezone
     *
     * @access  public
     * @param   string  $timezone
     * @return  void
     */
    public static function setTimezone($timezone)
    {
        return date_default_timezone_set($timezone);
    }

    /**
     * Set the locale
     *
     * @access  public
     * @param   string  $locale
     * @return  void
     */
    public static function setLocale($locale)
    {
        return setlocale(LC_ALL, $locale);
    }

    /**
     * Set the internal encoding for mb_functions
     *
     * @access  public
     * @param   string  $encoding
     * @return  void
     */
    public static function setMbEncoding($encoding)
    {
        return mb_internal_encoding($encoding);
    }

    /**
     * Checks if magic quotes is enabled
     *
     * @access  private
     * @return  void
     */
    private static function removeMagicQuotes()
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
            $array[$key] = is_array($value) ?
                static::stripslashRecursive($value) :
                stripslashes($value);
        }

        return $array;
    }

    /**
     * Autoloader
     *
     * @access  public
     * @param   string  $class
     * @return  string
     */
    public static function autoload($class)
    {
        switch(true)
        {
            case preg_match('/Controller$/', $class):
                $directory = CTRLS;
                break;
            case preg_match('/Model$/', $class):
                $directory = MODELS;
                break;
            default:
                $directory = HELPERS;
                break;
        }

        if(!file_exists($file = $directory . $class . EXT))
        {
            if(!file_exists($file = CORE . $class . EXT))
            {
                throw new CloudException('Unable to autolad: ' . $class);
            }
        }

        require $file;
    }

    /**
     * Loads a helper
     *
     * @access  public
     * @param   string  $helper
     * @return  object
     */
    public static function loadHelper($helper)
    {
        if(!in_array($helper, self::$helpers))
        {
            self::$helpers[$helper] = $helper::factory();
        }

        return self::$helpers[$helper];
    }

    /**
     * Error handler throws a new ErrorException
     *
     * @access  public
     * @param   int     $errno
     * @param   string  $errstr
     * @param   string  $errfile
     * @param   string  $errline
     * @throws  ErrorException
     * @return  void
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        throw new ErrorException($errstr, $errno, $errno, $errfile, $errline);
    }

    /**
     * Delete a directory and its contents
     *
     * @access  public
     * @param   string  $directory
     * @return  void
     */
    public static function deleteDir($directory)
    {
        if(!file_exists($directory))
        {
            throw new CloudException($directory . ' does not exist');
        }
        if(!is_dir($directory))
        {
            throw new CloudException($directory . ' is not a valid directory');
        }

        foreach(scandir($directory) as $item)
        {
            if($item != '.' and $item != '..')
            {
                if(filetype($directory . '/' . $item) == 'dir')
                {
                    static::deleteDir($directory . '/' . $item);
                }
                else
                {
                    unlink($directory . '/' . $item);
                }
            }
        }
        rmdir($directory);
    }
}
