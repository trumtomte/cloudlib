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
 * The html class.
 *
 * <short description>
 *
 * Pointers on what could be implemented:
 *
 * CSS (<link>)
 * SCRIPT (<script>)
 * META (favicon/charset etc..)
 * LINK (<a>)
 * IMAGE (<img>)
 * DOCTYPE (HTML5)
 * CSS / SCRIPT codeblocks?
 * DIV (<div>)
 * PARAGRAPH (<p>)
 * TABLES (<table>)
 * HEADERS (<h1-6>)
 *
 * Default <head> block?, array of options?
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes.helpers
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
final class Html extends Factory
{
    /**
     * Array of html tags
     *
     * @access  private
     * @var     array
     */
    private $tags = array(
        'css' => '<link rel="stylesheet" href="%s" />',
        'script' => '<script src="%s"></script>',
        'a' => '<a href="%s" %s>%s</a>',
        'img' => '<img src="%s" %s />'
    );

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Create a stylesheet
     *
     * @access  public
     * @param   string  $path
     * @return  string
     */
    public function css($path)
    {
        if(is_string($path))
        {
            return sprintf($this->tags['css'], CSS . $path . '.css') . PHP_EOL;
        }
        if(is_array($path))
        {
            $stylesheets = null;

            foreach($path as $value)
            {
                $stylesheets .= sprintf($this->tags['css'], CSS . $value . '.css') . PHP_EOL;
            }

            return $stylesheets;
        }
    }

    /**
     * Create a script tag
     *
     * @access  public
     * @param   string  $path
     * @return  string
     */
    public function script($path)
    {
        if(is_string($path))
        {
            return sprintf($this->tags['script'], JS . $path . '.js') . PHP_EOL;
        }
        if(is_array($path))
        {
            $scripts = null;

            foreach($path as $value)
            {
                $scripts .= sprintf($this->tags['script'], JS . $path . '.js') . PHP_EOL;
            }

            return $scripts;
        }
    }

    /**
     * Create an anchor
     *
     * @access  public
     * @param   string  $path
     * @param   string  $content
     * @param   array   $attributes
     * @return  string
     */
    public function a($path, $content, array $attributes = array())
    {
        return sprintf($this->tags['a'], RWBASE . $path, $this->getAttrStr($attributes), $content) . PHP_EOL;
    }

    /**
     * Create an image
     *
     * @access  public
     * @param   string  $path
     * @param   array   $attributes
     * @return  string
     */
    public function img($path, array $attributes = array())
    {
        return sprintf($this->tags['img'], IMG . $path, $this->getAttrStr($attributes)) . PHP_EOL;
    }

    /**
     * Take an array of attributes and return it as a string
     *
     * @access  private
     * @param   array   $attributes
     * @return  string
     */
    private function getAttrStr(array $attributes)
    {
        $string = null;

        foreach($attributes as $key => $value)
        {
            $string .= $key . '="' . $value . '" ';
        }

        return $string;
    }
}
