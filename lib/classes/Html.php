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
     * Array of strings for formatting
     *
     * @access  private
     * @var     array
     */
    private $tags = array(
        'css'    => '<link rel="stylesheet" href="%s" />',
        'script' => '<script src="%s"></script>',
        'a'      => '<a href="%s" %s>%s</a>',
        'img'    => '<img src="%s" %s />'
    );

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * <link> element for CSS
     *
     * @access  public
     * @param   string|array  $path
     * @return  string
     */
    public function css($path)
    {
        if(is_string($path))
        {
            return sprintf($this->tags['css'], CSS . $path . '.css');
        }
        if(is_array($path))
        {
            $stylesheets = null;

            foreach($path as $value)
            {
                $stylesheets .= sprintf($this->tags['css'], CSS . $value . '.css');
            }

            return $stylesheets;
        }
    }

    /**
     * <script> element
     *
     * @access  public
     * @param   string|array  $path
     * @return  string
     */
    public function script($path)
    {
        if(is_string($path))
        {
            return sprintf($this->tags['script'], JS . $path . '.js');
        }
        if(is_array($path))
        {
            $scripts = null;

            foreach($path as $value)
            {
                $scripts .= sprintf($this->tags['script'], JS . $path . '.js');
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
    public function a($path, $content, array $attributes = array())
    {
        return sprintf($this->tags['a'], URLPATH . $path,
                       $this->getAttrStr($attributes), $content);
    }

    /**
     * <img> element
     *
     * @access  public
     * @param   string  $path
     * @param   array   $attributes
     * @return  string
     */
    public function img($path, array $attributes = array())
    {
        return sprintf($this->tags['img'], IMG . $path, $this->getAttrStr($attributes));
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
            $string .= sprintf('%s="%s"', $key, $value);
        }

        return $string;
    }
}
