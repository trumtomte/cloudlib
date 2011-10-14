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
 * @subpackage  cloudlib.lib.classes
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
        'css' => '<link rel="stylesheet" href="%s" />'
    );

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    public function css($path)
    {
        if(is_string($path))
        {
            return sprintf($this->tags['css'], RWBASE . $path . '.css') . PHP_EOL;
        }
        if(is_array($path))
        {
            $stylesheets = null;

            foreach($path as $value)
            {
                $stylesheets .= sprintf($this->tags['css'], RWBASE . $value . '.css') . PHP_EOL;
            }

            return $stylesheets;
        }
    }

    public function script()
    {

    }

    public function link()
    {

    }

    public function img()
    {

    }
}
