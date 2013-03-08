<?php
/**
 * Cloudlib 
 *
 * @author      Sebastian Bengtegård <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2013 Sebastian Bengtegård <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace cloudlib\core;

use RuntimeException;

/**
 * Class autoloader, following the PSR-0 standard
 *
 * @copyright   Copyright (c) 2013 Sebastian Bengtegård <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ClassLoader
{
    /**
     * Array of namespaces and their corresponding directory path.
     *
     * @access  public
     * @var     array
     */
    public $namespaces = [];

    /**
     * Array of class aliases
     *
     * @access  public
     * @var     array
     */
    public $aliases = [];

    /**
     * Define the namespaces and aliases array at creation.
     *
     * @access  public
     * @param   array   $namespaces Array of Namespace:Directory pairs
     * @param   array   $aliases    Array of Alias:Class(namespaced) pairs
     * @return  void
     */
    public function __construct(array $namespaces = [], array $aliases = [])
    {
        $this->namespaces = $namespaces;
        $this->aliases = $aliases;
    }

    /**
     * Register class aliases
     *
     * @access  public
     * @param   array   $aliases Array of Alias:Class(namespaced) pairs
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
     * @param   array   $namespaces Array of Namespace:Directory pairs
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
     * @param   boolean $prepend    If ClassLoader should be prepended to the autoload stack
     * @return  void
     */
    public function register($prepend = false)
    {
        spl_autoload_register([$this, 'loadClass'], true, $prepend);
    }

    /**
     * Unregister the autoloader
     *
     * @access  public
     * @return  void
     */
    public function unregister()
    {
        spl_autoload_unregister([$this, 'loadClass']);
    }

    /**
     * Load a namespaced class via the PSR-0 Standard
     *
     * @access  public
     * @param   string  $class  The namespaced class to be loaded
     * @return  string          The file path (if found)
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
                    return;
                }

                // The directory assigned to the namespace is prepended
                $fileName = $this->namespaces[$namespace] . DIRECTORY_SEPARATOR . $fileName;

                if( ! file_exists($fileName))
                {
                    // File does Really not exist
                    return;
                }
            }

            require $fileName;
        }
        // Class was not namespaced
    }
}
