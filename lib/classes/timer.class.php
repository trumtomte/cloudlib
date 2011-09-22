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
 * The timer class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class timer extends master
{
    /**
     * Array of start and stop times
     *
     * @access  private
     * @var     array
     */
    private static $times = array();

    /**
     * Sets the start time
     *
     * @access  public
     * @return  void
     */
    public static function start($time)
    {
        self::$times['start:' . $time] = static::getTime();
    }

    /**
     * Sets the stop time
     *
     * @access  public
     * @return  void
     */
    private static function stop($time)
    {
        self::$times['stop:' . $time] = static::getTime();
    }

    /**
     * Gets the time
     *
     * @access  private
     * @return  int
     */
    private static function getTime()
    {
        list($usec, $sec) = explode(" ", microtime());

        return ((float)$usec + (float)$sec);
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

        $stop = static::$times['stop:' . $time];
        $start = static::$times['start:' . $time];

        $loadtime = $stop - $start;

        return round($loadtime, $round);
    }

    /**
     * Shorthand for timer::loadtime()
     *
     * @access  public
     * @param   string  $time
     * @param   array   $args
     * @return  int
     */
    public static function __callStatic($time, array $args)
    {
        $round = array_shift($args);

        if(empty($round))
        {
            $round = 5;
        }

        return static::time($time, $round);
    }
}
