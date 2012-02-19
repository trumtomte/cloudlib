<?php
/**
 * CloudLib :: Lightweight RESTful MVC PHP Framework
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     Cloudlib
 */

/**
 * Config
 *
 * <short description>
 *
 * @package     Cloudlib
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Config
{
    /**
     * Stored config items
     *
     * @access  public
     * @var     array
     */
    public static $items = array();

    /**
     * Default config category
     *
     * @access  public
     * @var     string
     */
    public static $category = 'default';

    /**
     * Constructor.
     *
     * @access  public
     * @param   string  $file
     * @return  void
     */
    public function __construct() {}

    /**
     * Load a config file and return the contents
     *
     * @access  public
     * @param   string  $file
     * @return  array
     */
    public static function load($file)
    {
        if( ! file_exists($file))
        {
            throw new RuntimeException(sprintf('Unable to locate the config file [%s]',
                $file));
        }

        if( ! is_array($array = require $file))
        {
            throw new LogicException('The config file MUST return an array');
        }

        static::$items = $array;
    }

    /**
     * Get a config item or config group
     *
     * @access  public
     * @param   string  $item
     * @param   string  $category
     * @return  mixed
     */
    public static function get($item, $category = null)
    {
        if($category === null)
        {
            $category = static::$category;
        }

        if( ! isset(static::$items[$category]))
        {
            throw new LogicException(sprintf('Config category [%s] does not exist',
                $category));
        }

        $items = static::$items[$category];

        if(strpos($item, '.') !== false)
        {
            if( ! isset($items[$item]))
            {
                throw new LogicException(sprintf('The config item [%s] does not exist',
                    $item));
            }
            return $items[$item];
        }

        return static::getGroup($item, $category);
    }

    /**
     * Get a group of items from the config array
     *
     * @access  protected
     * @param   string  $group
     * @param   string  $category
     * @return  array
     */
    protected static function getGroup($group, $category)
    {
        $items = static::$items[$category];

        $array = array();

        foreach($items as $key => $value)
        {
            if(strpos($key, $group) !== false)
            {
                $array[substr($key, (strlen($group) + 1))] = $value;
            }
        }

        if( ! isset($array))
        {
            throw new LogicException(sprintf('The config group [%s] does not exist',
                $group));
        }

        return $array;
    }
}
