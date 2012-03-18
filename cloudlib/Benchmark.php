<?php
/**
 * CloudLib :: Flexible Lightweight PHP Framework
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     Cloudlib
 */

namespace cloudlib;

/**
 * The Benchmark Class
 *
 * Set timers, compare timers, get memory usage, get peak memory usage
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
     * Define a start time
     *
     * @access  public
     * @param   string  $time
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
     * Define a stop time
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
     * Get the time comparison
     *
     * @access  public
     * @param   string  $time
     * @param   int     $decimals
     * @return  float 
     */
    public static function time($time, $decimals = 5)
    {
        static::stop($time);

        return (float) round((static::$times['stop:' . $time] - static::$times['start:' . $time]), $decimals);
    }

    /**
     * Compare an already defined time with the current timestamp
     *
     * @access  public
     * @param   float   $time
     * @param   int     $decimals
     * @return  float
     */
    public static function compare($time, $decimals = 5)
    {
        return (float) round((microtime(true) - (float) $time), $decimals);
    }

    /**
     * Gets the current memory usage in Megabytes rounded to three decimals
     *
     * @access  public
     * @param   int     $decimals
     * @return  float
     */
    public static function memory($decimals = 3)
    {
        return (float) round((memory_get_usage() / 1024 / 1024), $decimals);
    }

    /**
     * Gets the peak memory usage in Megabytes rounded to three decimals
     *
     * @access  public
     * @param   int     $decimals
     * @return  float 
     */
    public static function peak($decimals = 3)
    {
        return (float) round((memory_get_peak_usage() / 1024 / 1024), $decimals);
    }

    /**
     * Shorthand for Benchmark::timer()
     *
     * @access  public
     * @param   string  $time
     * @param   array   $args
     * @return  float 
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
