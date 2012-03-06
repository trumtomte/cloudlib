<?php
/**
 * CloudLib :: Flexible Lightweight PHP Framework
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
 * Cloudlib´s Class Loader
 *
 * Loads classes, controllers and models
 *
 * It follows the PSR-0 standard, https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
 *
 * @package     Cloudlib
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ClassLoader
{
    /**
     * Namespaces and their directories
     *
     * @access  public
     * @var     array
     */
    public $namespaces = array();

    /**
     * Paths for loading Controllers and Models
     *
     * @access  public
     * @var     array
     */
    public $paths = array();

    /**
     * Array of class aliases
     *
     * @access  public
     * @var     array
     */
    public $aliases = array();

    /**
     * Constructor,
     * set the namespaces-, aliases- (optional) and paths (optional) array
     *
     * @access  public
     * @param   array   $namespaces
     * @param   array   $aliases
     * @param   array   $paths
     * @return  void
     */
    public function __construct(array $namespaces, array $aliases = array(), array $paths = array())
    {
        $this->namespaces = $namespaces;
        $this->registerAliases($aliases);
        $this->setPaths($paths);
    }

    /**
     * Define directory paths
     *
     * @access  public
     * @param   array   $paths
     * @return  void
     */
    public function setPaths(array $paths)
    {
        foreach($paths as $key => $value)
        {
            $this->paths[$key] = $value;
        }
    }

    /**
     * Register class aliases
     *
     * @access  public
     * @param   array   $aliases
     * @return  void
     */
    public function registerAliases(array $aliases)
    {
        foreach($aliases as $alias => $class)
        {
            $this->aliases[$alias] = $class;
        }
    }

    /**
     * Register namespaces
     *
     * @access  public
     * @param   array   $namespaces
     * @return  void
     */
    public function registerNamespaces(array $namespaces)
    {
        foreach($namespaces as $namespace => $directory)
        {
            $this->namespaces[$namespace] = $directory;
        }
    }

    /**
     * Register the autoloader
     *
     * @access  public
     * @param   boolean     $prepend
     * @return  void
     */
    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), true, $prepend);
    }

    /**
     * Unregister the autoloader
     *
     * @access  public
     * @return  void
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * The Autoloader
     *
     * Load a namespaced class or load a Controller/Model class
     *
     * @access  public
     * @param   string  $class
     * @return  void
     */
    public function loadClass($class)
    {
        $class = ltrim($class, '\\');

        // If the class is mapped to its full namespace
        if(array_key_exists($class, $this->aliases))
        {
            // Class will be (re)loaded via class_alias() if it does not already exist
            class_alias($this->aliases[$class], $class);
            return;
        }

        // Class is namespaced
        if(($pos = strrpos($class, '\\')) !== false)
        {
            $namespace = substr($class, 0, $pos);
            $className = substr($class, $pos + 1);
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

            if( ! file_exists($fileName))
            {
                if( ! array_key_exists($namespace, $this->namespaces))
                {
                    // No directory was assigned to the namespace
                    error_log(sprintf('Unable to load class [%s], no directory assigned to namespace [%s]' . PHP_EOL,
                        $className, $namespace), 3, $this->paths['logs']);
                    return;
                }

                // The directory assigned to the namespace is prepended
                $fileName = $this->namespaces[$namespace] . DIRECTORY_SEPARATOR . $fileName;

                if( ! file_exists($fileName))
                {
                    // File does Really not exist
                    error_log(sprintf('Unable to load class [%s] from [%s]' . PHP_EOL,
                        $className, $fileName), 3, $this->paths['logs']);
                    return;
                }
            }

            require $fileName;
        }

        // Class was not namespaced
        else
        {
            // Check if it was a Controller or a Model
            $this->loadControllerModel($class);
        }
    }

    /**
     * Tries to load a Controller or a Model
     *
     * @access  public
     * @param   string  $class
     * @return  void
     */
    public function loadControllerModel($class)
    {
        // Check if it is a Controller or a Model to be loaded
        switch(true)
        {
            case preg_match('/Controller$/', $class) && ! preg_match('/^Controller$/', $class):
                $directory = $this->paths['controllers'];
                break;
            case preg_match('/Model$/', $class) && ! preg_match('/^Model$/', $class):
                $directory = $this->paths['models'];
                break;
            default:
                // Class was not a Controller or a Model
                return;
                break;
        }

        if( ! file_exists($file = $directory . $class . '.php'))
        {
            // Unable to locate the Controller/Model
            error_log(sprintf('Unable to load class [%s] from [%s]' . PHP_EOL,
                $class, $file), 3, $this->paths['logs']);
            return;
        }

        require $file;
    }
}
