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
class Number
{
    /**
     * Array of byte units
     *
     * @access  protected
     * @var     array
     */
    protected static $byteUnits = array(
        'B'  => 1,
        'KB' => 10,
        'MB' => 20,
        'GB' => 30,
        'TB' => 40
    );

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Shorthand function for toBytes() and fromBytes()
     *
     * @access  public
     * @param   mixed   $value
     * @param   int     $round
     * @return  mixed
     */
    public static function byte($value, $round = 3)
    {
        if(is_string($value))
        {
            return static::toBytes($value);
        }
        elseif(is_int( (int) $value))
        {
            return static::fromBytes($value, $round);
        }
        else
        {
            throw new InvalidArgumentException('Argument 1 has to be of type Str or Int');
        }
    }

    /**
     * Convert a string like '100mb' to its corresponding byte value
     *
     * @access  public
     * @param   string  $value
     * @return  int
     */
    public static function toBytes($value)
    {
        if( ! is_string($value))
        {
            throw new InvalidArgumentException('Argument 1 has to be of type String');
        }
        
        $value = explode($type = substr($value, -2), $value);

        return $value[0] * pow(2, static::$byteUnits[strtoupper($type)]);
    }

    /**
     * Convert bytes into a string like '0.444MB'
     *
     * @access  public
     * @param   int     $value
     * @param   int     $round
     * @return  string
     */
    public static function fromBytes($value, $round = 3)
    {
        if( ! is_int( (int) $value))
        {
            throw new InvalidArgumentException('Argument 1 has to be of type Integer');
        }
        
        return sprintf('%sMB', round(( (int) $value / 1024 / 1024), $round));
    }
}
