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

// SPL
use PDO;
use PDOException;
use RuntimeException;

/**
 * <class name>
 *
 * <short description>
 *
 * @package     Cloudlib
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Database
{
    /**
     * Current connection
     *
     * @access  protected
     * @var     object
     */
    protected $connection;

    /**
     * Array of queries that has been run
     *
     * @access  public
     * @var     array
     */
    public static $queries = array();

    /**
     * Constructor,
     * initiates the connection
     *
     * @access  public
     * @param   array   $settings
     * @return  void
     */
    public function __construct(array $settings = array())
    {
        if(empty($settings))
        {
            $settings = Config::get('db');
        }

        $driverOptions = array(
            PDO::ATTR_PERSISTENT => $settings['persistent'],
            PDO::MYSQL_ATTR_INIT_COMMAND => sprintf('SET NAMES %s', $settings['charset']
        ));

        try
        {
            $this->connection = new PDO($settings['dsn'], $settings['username'],
                $settings['password'], $driverOptions);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e)
        {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * Begin a transaction
     *
     * @access  public
     * @return  boolean
     */
    public function begin()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit a transaction
     *
     * @access  public
     * @return  boolean
     */
    public function commit()
    {
        return $this->connection->commit();
    }

    /**
     * Roll back a transaction
     *
     * @access  public
     * @return  boolean
     */
    public function rollBack()
    {
        return $this->connection->rollBack();
    }

    /**
     * Fetch all results from a query
     *
     * @access  public
     * @param   string  $statement
     * @param   array   $bindings
     * @return  mixed
     */
    public function fetchAll($statement, array $bindings = array())
    {
        $sth = $this->execute($statement, $bindings);
        $result = $sth->fetchAll(PDO::FETCH_CLASS);

        static::$queries[] = sprintf('Query "%s" returned %s row(s)', $statement,
            $sth->rowCount());

        $sth->closeCursor();

        unset($sth);

        return empty($result) ? false : $result;
    }

    /**
     * Fetch first result from a query
     *
     * @access  public
     * @param   string  $statement
     * @param   array   $bindings
     * @return  mixed
     */
    public function fetchFirst($statement, array $bindings = array())
    {
        $sth = $this->execute($statement, $bindings);
        $result = $sth->fetchObject();

        static::$queries[] = sprintf('Query "%s" returned %s row(s)', $statement,
            $sth->rowCount());

        $sth->closeCursor();

        unset($sth);

        return is_object($result) ? $result : false;
    }

    /**
     * Return number of rows from a query
     *
     * @access  public
     * @param   string  $statement
     * @param   array   $bindings
     * @return  mixed
     */
    public function rows($statement, array $bindings = array())
    {
        $sth = $this->execute($statement, $bindings);
        $result = $sth->rowCount();

        static::$queries[] = sprintf('Query "%s" returned %s row(s)', $statement,
            $result);

        $sth->closeCursor();

        unset($sth);

        return ($result > 0) ? $result : false;
    }

    /**
     * Perform a insert query
     *
     * @access  public
     * @param   string  $table
     * @param   array   $columns
     * @param   array   $bindings
     * @return  mixed
     */
    public function insert($table, array $columns, array $bindings)
    {
        $cols = null;
        $count = count($columns) - 1;

        for($i = 0; $i < $count; $i++)
        {
            $cols .= sprintf('%s, ', $columns[$i]);
        }
        $cols .= end($columns);

        $insert = sprintf('(%s?)', str_repeat('?, ', $count));
        $values = str_repeat($insert . ', ', ((count($bindings) / count($columns)) - 1))
            . $insert;

        $statement = sprintf('INSERT INTO %s (%s) VALUES %s', $table, $cols, $values);

        $sth = $this->execute($statement, $bindings);
        $result = $sth->rowCount();

        static::$queries[] = sprintf('Query "%s" returned %s row(s)', $statement,
            $result);

        $sth->closeCursor();

        unset($sth);

        return ($result > 0) ? $result : false;
    }

    /**
     * Perform an update query
     *
     * @access  public
     * @param   string  $table
     * @param   array   $columns
     * @param   array   $bindings
     * @return  mixed
     */
    public function update($table, $where, array $columns, array $bindings)
    {
        $updates = null;
        $count = count($columns) - 1;

        for($i = 0; $i < (count($columns) - 1); $i++)
        {
            $updates .= sprintf('%s = ?, ', $columns[$i]);
        }
        $updates .= sprintf('%s = ?', end($columns));

        $statement = sprintf('UPDATE %s SET %s WHERE %s', $table, $updates, $where);

        $sth = $this->execute($statement, $bindings);
        $result = $sth->rowCount();

        static::$queries[] = sprintf('Query "%s" returned %s row(s)', $statement,
            $result);

        $sth->closeCursor();

        unset($sth);

        return ($result > 0) ? $result : false;
    }

    /**
     * Perform an update query with cases
     *
     * @access  public
     * @param   string  $table
     * @param   string  $column
     * @param   string  $case
     * @param   array   $variables
     * @return  mixed
     */
    public function updateMany($table, $column, $case, array $variables)
    {
        $cases = null;

        foreach($variables as $key => $value)
        {
            $cases .= sprintf(" WHEN '%s' THEN '%s'", $key, $value);
        }

        $statement = sprintf('UPDATE %s SET %s = CASE %s %s ELSE %s END', $table,
            $column, $case, $cases, $column);

        $sth = $this->execute($statement, array());
        $result = $sth->rowCount();

        static::$queries[] = sprintf('Query "%s" returned %s row(s)', $statement,
            $result);

        $sth->closeCursor();

        unset($sth);

        return ($result > 0) ? $result : false;
    }

    /**
     * Perform a delete query
     *
     * @access  public
     * @param   string  $statement
     * @param   array   $bindings
     * @return  mixed
     */
    public function delete($statement, array $bindings = array())
    {
        $sth = $this->execute($statement, $bindings);
        $result = $sth->rowCount();

        static::$queries[] = sprintf('Query "%s" returned %s row(s)', $statement,
            $result);

        $sth->closeCursor();

        unset($sth);

        return ($result > 0) ? $result : false;
    }

    /**
     * Execute a prepared statement
     *
     * @access  public
     * @param   string  $statement
     * @param   array   $bindings
     * @return  object
     */
    protected function execute($statement, array $bindings = array())
    {
        $sth = $this->connection->prepare($statement);

        $sth->execute($bindings);

        return $sth;
    }
}

