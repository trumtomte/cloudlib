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
class Config
{
    /**
     * Array of config files
     *
     * @access  private
     * @var     array
     */
    public static $configs = array();

    /**
     * Default config
     *
     * @access  public
     * @var     string
     */
    public static $default = 'default';

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    private function __construct() {}

    /**
     * Get a config item or a config group
     *
     * @access  public
     * @param   string  $item
     * @param   string  $default
     * @param   string  $config
     * @return  mixed
     */
    public static function get($item, $default = null, $config = 'config')
    {
        if($default === null)
        {
            $default = static::$default;
        }

        if( ! isset(static::$configs[$config]))
        {
            static::load($config);
        }

        $conf = static::$configs[$config];

        if( ! isset($conf[$default]))
        {
            throw new LogicException(sprintf('Default config "%s" does not exist',
                $default));
        }

        if(strpos($item, '.') !== false)
        {
            if( ! isset($conf[$default][$item]))
            {
                throw new LogicException(sprintf('Config item "%s" does not exist',
                    $item));
            }
            return $conf[$default][$item];
        }
        
        return static::getGroup($item, $default, $config);
    }

    /**
     * Return an array based by a config group
     *
     * @access  private
     * @param   string  $group
     * @param   string  $default
     * @param   string  $config
     * @return  array
     */
    protected static function getGroup($group, $default, $config)
    {
        $config = static::$configs[$config][$default];

        $array = array();

        foreach($config as $key => $value)
        {
            if(strpos($key, $group) !== false)
            {
                $array[substr($key, (strlen($group) + 1))] = $value;
            }
        }

        if( ! isset($array))
        {
            throw new LogicException(sprintf('Config group "%s" does not exist',
                $group));
        }

        return $array;
    }

    /**
     * Load a config file into the array of configs
     *
     * @access  public
     * @param   string  $config
     * @return  void
     */
    public static function load($config)
    {
        if( ! file_exists($file = CONFIG . $config . EXT))
        {
            throw new RuntimeException(sprintf('Config file "%s" does not exist',
                $file));
        }
        if( ! is_array(static::$configs[$config] = require $file))
        {
            throw new LogicException(sprintf('The config file "%s" must return an array',
                $config));
        }
    }

    /**
     *  TODO
     */
    public static function set()
    {
        // TODO
    }
}
