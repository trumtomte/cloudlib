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
}
