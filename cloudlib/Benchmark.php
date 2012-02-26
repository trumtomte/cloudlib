<?php
/**
 * CloudLib :: Lightweight RESTful MVC PHP Framework
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     Cloudlib
 */

namespace cloudlib;

/**
 * <class name>
 *
 * <short description>
 *
 * @package     Cloudlib
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Benchmark
{
    /**
     * Array of start and stop times
     *
     * @access  public
     * @var     array
     */
    public static $times = array();

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Sets the start time
     *
     * @access  public
     * @return  void
     */
    public static function start($time)
    {
        if( ! isset(self::$times['start:' . $time]))
        {
            self::$times['start:' . $time] = microtime(true);
        }
    }

    /**
     * Sets the stop time
     *
     * @access  protected
     * @param   int     $time
     * @return  void
     */
    protected static function stop($time)
    {
        self::$times['stop:' . $time] = microtime(true);
    }

    /**
     * Returns the load time rounded to 5 decimals
     *
     * @access  public
     * @return  int
     */
    public static function time($time, $round = 5)
    {
        static::stop($time);

        return round((static::$times['stop:' . $time] - static::$times['start:' . $time]), $round);
    }

    /**
     * Compare an already defined time with the current timestamp
     *
     * @access  public
     * @param   float   $time
     * @param   int     $round
     * @return  float
     */
    public static function compare($time, $round = 5)
    {
        return round((microtime(true) - (float) $time), $round);
    }

    /**
     * Gets the current memory usage in Megabytes rounded to three decimals
     *
     * @access  public
     * @param   int     $round
     * @return  int
     */
    public static function memory($round = 3)
    {
        return (float) round((memory_get_usage() / 1024 / 1024), $round);
    }

    /**
     * Gets the peak memory usage in Megabytes rounded to three decimals
     *
     * @access  public
     * @param   int     $round
     * @return  int
     */
    public static function peak($round = 3)
    {
        return (float) round((memory_get_peak_usage() / 1024 / 1024), $round);
    }

    /**
     * Shorthand for Timer::time()
     *
     * @access  public
     * @param   string  $time
     * @param   array   $args
     * @return  int
     */
    public static function __callStatic($time, array $args)
    {
        if( ! isset($args[0]))
        {
            $args[0] = 5;
        }
        return static::time($time, $args[0]);
    }
}
