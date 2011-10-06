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
 * The core class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class core
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
    private static $modules = array();

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

        $dispatcher = dispatcher::factory();
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
     * Autoloader
     *
     * @access  public
     * @param   string  $class
     * @return  string
     */
    public static function autoload($class)
    {
        if(preg_match('/Controller$/', $class))
        {
            $file = CTRLS . $class . CLASS_EXT;
        }
        elseif(preg_match('/Model$/', $class))
        {
            $file = MODELS . $class . CLASS_EXT;
        }
        else
        {
            $file = CLASSES . $class . CLASS_EXT;
        }

        if(!file_exists($file))
        {
            throw new cloudException('Unable to autoload: ' . $class);
        }

        require $file;
    }

    /**
     * Loads a module
     *
     * @access  public
     * @param   string  $module
     * @return  object
     */
    public static function loadModule($module)
    {
        $modules = config::modules();

        if(!array_key_exists($module, $modules))
        {
            throw new cloudException('Module does not exist: ' . $module);
        }

        if($modules[$module] == false)
        {
            throw new cloudException('Module is set to inactive(false): ' . $module);
        }

        if(!in_array($module, self::$modules))
        {
            self::$modules[$module] = $module::factory();
        }

        return self::$modules[$module];
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
            throw new cloudException($directory . ' does not exist');
        }
        if(!is_dir($directory))
        {
            throw new cloudException($directory . ' is not a valid directory');
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
