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
 * The number class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Number extends Factory
{
    /**
     * Array for pow()
     *
     * @access  private
     * @var     array
     */
    private static $byteSize = array(
        'KB' => 10,
        'MB' => 20,
        'GB' => 30,
        'TB' => 40
    );

    /**
     * Array of abbreviations for bytesizes
     *
     * @access  private
     * @var     array
     */
    private static $byteType = array('B', 'KB', 'MB', 'GB', 'TB');

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Function for converting into bytes or a shorthand for the bytesize
     *
     * @access  public
     * @param   string|int  $value
     * @param   int         $round
     * @return  string|int
     */
    public static function byte($value, $round = null)
    {
        if(is_int($value))
        {
            $counter = 0;

            while($value > 1000)
            {
                $value = $value / 1024;

                $counter++;
            }

            $type = self::$byteType[$counter];

            if(!isset($round))
            {
                $round = $counter;
            }

            $value = round($value, $round);

            return $value . $type;
        }

        if(is_string($value))
        {
            $type = substr($value, -2);

            $value = explode($type, $value);

            $value = $value[0];

            $bytes = $value * pow(2, self::$byteSize[strtoupper($type)]);

            return $bytes;
        }
    }   
}
