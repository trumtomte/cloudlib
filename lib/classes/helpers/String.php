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
 * The string class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes.helpers
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class String extends Factory
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
        if($separator !== null)
        {
            return str_repeat($string . $separator, ($times - 1)) . $string;
        }

        return str_repeat($string, $times);
    }
}
