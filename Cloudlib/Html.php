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
 * The HTML Class 
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
     * @param   string|array  $filename
     * @return  string
     */
    public static function css($filename)
    {
        if(is_string($filename))
        {
            return sprintf('<link rel="stylesheet" href="%s" />' . PHP_EOL,
                static::$paths['css'] . $filename . '.css');
        }
        if(is_array($filename))
        {
            $stylesheets = null;

            foreach($filename as $value)
            {
                $stylesheets .= sprintf('<link rel="stylesheet" href="%s" />' . PHP_EOL,
                    static::$paths['css'] . $value . '.css');
            }

            return $stylesheets;
        }
    }

    /**
     * Shorthand for JavaScript links
     *
     * @access  public
     * @param   string|array  $filename
     * @return  string
     */
    public static function js($filename)
    {
        if(is_string($filename))
        {
            return sprintf('<script src="%s"></script>' . PHP_EOL,
                static::$paths['js'] . $filename . '.js');
        }
        if(is_array($filename))
        {
            $scripts = null;

            foreach($filename as $value)
            {
                $scripts .= sprintf('<script src="%s"></script>' . PHP_EOL, 
                   static::$paths['js'] . $value . '.js');
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
     * @param   boolean $default
     * @return  string
     */
    public static function a($path, $content, array $attributes = array())
    {
        if(isset($attributes['relative']) && $attributes['relative'] == false)
        {
            unset($attributes['relative']);
            return sprintf('<a href="%s" %s>%s</a>', $path, static::getAttrStr($attributes),
                $content);
        }
        return sprintf('<a href="%s" %s>%s</a>' . PHP_EOL, static::$paths['base'] .
            $path, static::getAttrStr($attributes), $content);
    }

    /**
     * <img> element
     *
     * @access  public
     * @param   string  $path
     * @param   array   $attributes
     * @param   boolean $default
     * @return  string
     */
    public static function img($path, array $attributes = array())
    {
        if(isset($attributes['relative']) && $attributes['relative'] == false)
        {
            unset($attributes['relative']);
            return sprintf('<img src="%s" %s/>', $path, static::getAttrStr($attributes));
        }
        return sprintf('<img src="%s" %s/>' . PHP_EOL, static::$paths['img'] . $path,
            static::getAttrStr($attributes));
    }

    /**
     * Create a <script> code block
     *
     * @access  public
     * @param   string  $script
     * @return  strig
     */
    public static function script($script)
    {
        return sprintf('<script>%s%s%s</script>' . PHP_EOL, PHP_EOL, $script, PHP_EOL);
    }

    /**
     * Create a <style> code block
     *
     * @access  public
     * @param   string  $style
     * @return  string
     */
    public static function style($style)
    {
        return sprintf('<style>%s%s%s</style>' . PHP_EOL, PHP_EOL, $style, PHP_EOL);
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
        return str_repeat('<br />', $times) . PHP_EOL;
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


