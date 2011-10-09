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
 * The Config class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Config extends Factory
{
    /**
     * Array of each config
     *
     * @access  private
     * @var     array
     */
    private static $config = array();

    /**
     * Array of each config item
     *
     * @access  private
     * @var     array
     */
    private static $items = array();

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
    public static function get($config, $key = null)
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
        
        static::set($array, $config);

        if(isset($key))
        {
            if(!array_key_exists($key, static::$items))
            {
                throw new CloudException('Item: ' . $key . ' not found in ' . $config);
            }

            return static::$items[$key];
        }

        return static::$config[$config];
    }

    /**
     * Sets the config item
     *
     * @access  private
     * @param   $array
     * @param   $config
     * @return  void
     */
    private static function set(array $array, $config)
    {
        foreach($array as $key => $value)
        {
            static::$items[$key] = $value;
        }

        static::$config[$config] = $array;
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
        $key = array_shift($args);

        if(empty($key))
        {
            $key = null;
        }

        return static::get($config, $key);
    }
}
