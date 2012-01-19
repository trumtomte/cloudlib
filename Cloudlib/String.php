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
class String
{
    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Repeat a string 
     *
     * @access  public
     * @param   string  $string
     * @param   int     $times
     * @param   string  $separator
     * @return  string
     */
    public static function repeat($string, $times = 2, $separator = null)
    {
        if($separator === null)
        {
            return str_repeat($string, $times);
        }
        return str_repeat($string . $separator, ($times - 1)) . $string;
    }

    // TODO: shorthand functions for mb_(functions)?
}
