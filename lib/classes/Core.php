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
 * @subpackage  cloudlib.lib.classes.core
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
    const VERSION = '0.4.0';

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

        Request::removeMagicQuotes();

        $dispatcher = Dispatcher::factory()->dispatch();
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
            case preg_match('/Controller$/', $class) and !preg_match('/^Controller$/', $class):
                $directory = CTRLS;
                break;
            case preg_match('/Model$/', $class) and !preg_match('/^Model$/', $class):
                $directory = MODELS;
                break;
            default:
                $directory = CLASSES;
                break;
        }

        if(!file_exists($file = $directory . $class . EXT))
        {
            throw new CloudException('Unable to autolad: ' . $class);
        }
        
        require $file;
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
