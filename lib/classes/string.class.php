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
 * The string class.
 *
 * Shorthand class for string functions using mb_functions
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class string extends master
{
    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Shorthand for mb_strtoupper
     *
     * @access  public
     * @return  void
     */
    public static function upper($string)
    {
        return mb_strtoupper($string);
    }

    /**
     * Shorthand for mb_strtolower
     *
     * @access  public
     * @return  void
     */
    public static function lower($string)
    {
        return mb_strtolower($string);
    }

    /**
     * Shorthand for mb_ereg_replace
     *
     * @access  public
     * @return  void
     */
    public static function replace($pattern, $replacement, $string, $options = null)
    {
        return mb_ereg_replace($pattern, $replacement, $string, $options);
    }

    /**
     * Shorthand for mb_ereg_match
     *
     * @access  public
     * @return  void
     */
    public static function match($pattern, $string, $options = null)
    {
        return mb_ereg_match($pattern, $string, $options);
    }

    /**
     * Shorthand for mb_strlen
     *
     * @access  public
     * @return  void
     */
    public static function length($string)
    {
        return mb_strlen($string);
    }

    /**
     * Shorthand for mb_strimwidth
     *
     * @access  public
     * @return  void
     */
    public static function trimwidth($string, $start, $width, $marker = null)
    {
        return mb_strimwidth($string, $start, $width, $marker);
    }

    /**
     * Shorthand for mb_substr
     *
     * @access  public
     * @return  void
     */
    public static function sub($string, $start, $length = null)
    {
        return mb_substr($string, $start, $length);
    }
}
