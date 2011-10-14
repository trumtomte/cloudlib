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
 * The database class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Database extends Factory
{
    /**
     * Database object
     *
     * @access  private
     * @var     object
     */
    private static $instance = null;

   /**
     * Constructor
     *
     * @access  private
     * @return  object
     */
    public function __construct()
    {
        $config = config::database();

        $driverOptions = array(
                PDO::ATTR_PERSISTENT         => $config['persistent'],
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $config['charset']
            );

        if(!isset(self::$instance))
        {
            try
            {
                self::$instance = new PDO($config['dsn'], $config['username'],
                                          $config['password'], $driverOptions);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch(PDOException $e)
            {
                throw new cloudException($e->getMessage());
            }
        }

        return self::$instance;
    }

    /**
     * Prepare an SQL statement
     *
     * @access  public
     * @param   string  $statement
     * @param   array   $driver
     * @return  PDOstatement
     */
    public function prepare($statement, array $driver = array())
    {
        return self::$instance->prepare($statement, $driver);
    }

    /**
     * Starts a transaction
     *
     * @access  public
     * @return  bool
     */
    public function beginTransaction()
    {
        return self::$instance->beginTransaction();
    }

    /**
     * Commits a transactions
     *
     * @access  public
     * @return  bool
     */
    public function commit()
    {
        return self::$instance->commit();
    }

    /**
     * Get the error code
     *
     * @access  public
     * @return  string
     */
    public function errorCode()
    {
        return self::$instance->errorCode();
    }

    /**
     * Get the error info
     *
     * @access  public
     * @return  array
     */
    public function errorInfo()
    {
        return self::$instance->errorInfo();
    }

    /**
     * Database query statement
     *
     * @access  public
     * @return  object
     */
    public function query($statement)
    {
        return self::$instance->query($statement);
    }

    /**
     * Database exec statement
     *
     * @access  public
     * @return  integer
     */
    public function exec($statement)
    {
        return self::$instance->exec($statement);
    }

    /**
     * Magic method
     *
     * @access  private
     * @return  void
     */
    private function __clone() {}

    /**
     * Magic method
     *
     * @access  private
     * @return  void
     */
    private function __wakeup() {}
}
