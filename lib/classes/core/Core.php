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
    const VERSION = '0.3.10.1';

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
     * Main
     *
     * @access  public
     * @return  void
     */
    public static function main()
    {
        date_default_timezone_set(Config::general('timezone'));
        setlocale(LC_ALL, Config::general('locale'));
        mb_internal_encoding(Config::general('mbstring'));

        Request::removeMagicQuotes();

        $dispatcher = Dispatcher::factory();
        $dispatcher->dispatch();
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
