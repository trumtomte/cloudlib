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
     * Current active SQL statement
     *
     * @access  private
     * @var     string
     */
    private static $statement;

    /**
     * Variables to be prepared
     *
     * @access  private
     * @var     array
     */
    private static $variables = array();

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
    public function begin()
    {
        return self::$instance->beginTransaction();
    }

    /**
     * Commits a transaction
     *
     * @access  public
     * @return  bool
     */
    public function commit()
    {
        return self::$instance->commit();
    }

    /**
     * SELECT statement
     *
     * @access  public
     * @param   string  $statement
     * @return  object
     */
    public function select($statement = '*')
    {
        self::$statement = 'SELECT ' . $statement;

        return $this;
    }

    /**
     * FROM statement
     *
     * @access  public
     * @param   string  $statement
     * @return  object
     */
    public function from($statement)
    {
        self::$statement .= ' FROM ' . $statement;

        return $this;
    }

    /**
     * JOIN statement
     *
     * @access  public
     * @param
     * @return  object
     */
    public function join()
    {

    }

    /**
     * WHERE statement
     *
     * @access  public
     * @param   string  $statement
     * @param   array   $variables
     * @return  object
     */
    public function where($statement, array $variables = array())
    {
        self::$statement .= ' WHERE ' . $statement;

        if(isset($variables))
        {
            foreach($variables as $variable)
            {
                self::$variables[] = $variable;
            }
        }

        return $this;
    }

    /**
     * ORDER BY statement
     *
     * @access  public
     * @param   string  $statement
     * @return  object
     */
    public function order($statement)
    {
        self::$statement .= ' ORDER BY ' . $statement;

        return $this;
    }

    /**
     * LIMIT statement
     *
     * @access  public
     * @param   string  $statement
     * @return  object
     */
    public function limit($statement)
    {
        self::$statement .= ' LIMIT ' . $statement;

        return $this;
    }

    /**
     * INSERT INTO statement
     *
     * @access  public
     * @param   string  $table
     * @param   array   $variables
     * return   object
     */
    public function insert($table, array $variables)
    {
        $columns = null;
        $values  = null;

        foreach($variables as $key => $value)
        {
            self::$variables[] = $value;

            $columns .= $key . ', ';
            $values  .= '?, ';
        }

        $columns = rtrim($columns, ', ');
        $values  = rtrim($values, ', ');

        self::$statement = 'INSERT INTO ' . $table . ' (' . $columns . ') VALUES (' 
                         . $values . ')';

        return $this;
    }

    /**
     * UPDATE statement
     *
     * @access  public
     * @param   string  $statement
     * @param   array   @variables
     * @return  object
     */
    public function update($table, array $variables)
    {
        $updates = null;

        foreach($variables as $key => $value)
        {
            self::$variables[] = $value;

            $updates .= $key . ' = ?, ';
        }

        $updates = rtrim($updates, ', ');

        self::$statement = 'UPDATE ' . $table . ' SET ' . $updates;

        return $this;
    }

    /**
     * DELETE statement
     *
     * @access  public
     * @param   string  $table
     * @return  object
     */
    public function delete($table)
    {
        self::$statement = 'DELETE FROM ' . $table;

        return $this;
    }

    /**
     * Executes an prepare SQL statement
     *
     * @access  public
     * @param   string      $function
     * @param   string|int  $parameter
     * @return  mixed
     */
    public function execute($function = null, $parameter = null)
    {
        $sth = self::$instance->prepare(self::$statement);

        if(empty($function))
        {
            return $sth->execute(self::$variables);
        }

        $sth->execute(self::$variables);

        return $sth->$function($parameter);
    }
}
