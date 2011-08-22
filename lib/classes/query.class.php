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
 * The database class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
final class query
{
    /**
     * Database instance
     *
     * @access  private
     * @var     object
     */
    public static $instance;

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct()
    {
        if(!isset(self::$instance))
        {
            self::$instance = core::loadModule('database');
        }
    }

    /**
     * Begins a transaction
     *
     * @access  public
     * @return  bool
     */
    public function start()
    {
        return self::$instance->beginTransaction();
    }

    /**
     * Commits a transaction
     *
     * @access  public
     * @return  bool
     */
    public function end()
    {
        return self::$instance->commit();
    }

    public function select()
    {

    }

    public function from()
    {

    }

    public function where()
    {

    }

    public function order()
    {

    }

    public function limit()
    {

    }

    public function insert()
    {

    }

    public function values()
    {

    }

    public function update()
    {

    }
}
