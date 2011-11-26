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
 * The Config class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes.core
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Config
{
    /**
     * Array of each config
     *
     * @access  private
     * @var     array
     */
    private static $configs = array();

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Get a config file or item
     *
     * @access  public
     * @param   string  $config
     * @param   string  $key
     * @return  mixed
     */
    public static function get($config, $key = null, $default = null)
    {
        if(!file_exists($file = CONFIG . $config . EXT))
        {
            throw new CloudException('Unable to locate the config file: ' . $config);
        }

        $array = require $file;

        if(!is_array($array))
        {
            throw new CloudException('Config files must return an array');
        }        
        
        if($default === null)
        {
            $default = CONF;
        }

        if(!array_key_exists($default, $array))
        {
            $default = 'default';

            if(!array_key_exists($default, $array))
            {
                throw new CloudException('Unable to locate any default config item');
            }
        }

        if(!is_array($array = $array[$default]))
        {
            throw new CloudException('Config items must return an array');
        }

        static::set($config, $array);

        if(isset($key))
        {
            if(!array_key_exists($key, static::$configs[$config]))
            {
                throw new CloudException($key . ' not found in config: ' . $config);
            }

            return static::$configs[$config][$key];
        }

        return static::$configs[$config];
    }

    /**
     * Sets an config array
     *
     * @access  private
     * @param   $array
     * @param   $config
     * @return  void
     */
    private static function set($config, array $array)
    {
        if(!isset(static::$configs[$config]))
        {
            static::$configs[$config] = $array;
        }
    }

    /**
     * Shortens the call for a config file or item
     *
     * @access  public
     * @param   string  $config
     * @param   array   $args
     * @return  mixed
     */
    public static function __callStatic($config, array $args)
    {
        if(!isset($args[0]))
        {
            $args[0] = null;
        }
        if(!isset($args[1]))
        {
            $args[1] = null;
        }

        return static::get($config, $args[0], $args[1]);
    }
}
