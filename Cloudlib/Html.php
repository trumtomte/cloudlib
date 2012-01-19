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
 * <class name>
 *
 * <short description>
 *
 * @package     Cloudlib
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Html
{
    /**
     * Relative paths to files
     *
     * @access  public
     * @var     array
     */
    public static $paths = array();

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Shorthand for CSS stylesheet links
     *
     * @access  public
     * @param   string|array  $path
     * @return  string
     */
    public static function css($path)
    {
        if(is_string($path))
        {
            return sprintf('<link rel="stylesheet" href="%s" />',
                static::$paths['css'] . $path . '.css');
        }
        if(is_array($path))
        {
            $stylesheets = null;

            foreach($path as $value)
            {
                $stylesheets .= sprintf('<link rel="stylesheet" href="%s" />',
                    static::$paths['css'] . $value . '.css');
            }

            return $stylesheets;
        }
    }

    /**
     * Shorthand for JavaScript links
     *
     * @access  public
     * @param   string|array  $path
     * @return  string
     */
    public static function script($path)
    {
        if(is_string($path))
        {
            return sprintf('<script src="%s"></script>',
                static::$paths['js'] . $path . '.js');
        }
        if(is_array($path))
        {
            $scripts = null;

            foreach($path as $value)
            {
                $scripts .= sprintf('<script src="%s"></script>', 
                   static::$paths['js'] . $path . '.js');
            }

            return $scripts;
        }
    }

    /**
     * <a> element
     *
     * @access  public
     * @param   string  $path
     * @param   string  $content
     * @param   array   $attributes
     * @return  string
     */
    public static function a($path, $content, array $attributes = array())
    {
        return sprintf('<a href="%s" %s>%s</a>', static::$paths['base'] . $path,
            static::getAttrStr($attributes), $content);
    }

    /**
     * <img> element
     *
     * @access  public
     * @param   string  $path
     * @param   array   $attributes
     * @return  string
     */
    public static function img($path, array $attributes = array())
    {
        return sprintf('<img src="%s" %s/>', static::$paths['img'] . $path,
            static::getAttrStr($attributes));
    }

    /**
     * Return <br /> a number of times
     *
     * @access  public
     * @param   int     $times
     * @return  string
     */
    public static function br($times = 1)
    {
        return str_repeat('<br />', $times);
    }

    /**
     * Take an array of attributes and return it as a string
     *
     * @access  public
     * @param   array   $attributes
     * @return  string
     */
    public static function getAttrStr(array $attributes)
    {
        $string = null;

        foreach($attributes as $key => $value)
        {
            $string .= sprintf('%s="%s" ', $key, $value);
        }

        return $string;
    }
}
