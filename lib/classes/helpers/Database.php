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
 * @subpackage  cloudlib.lib.classes.helpers
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
     * Current SQL statement
     *
     * @access  private
     * @var     string
     */
    private $statement = null;

    /**
     * Array of to be prepared variables
     *
     * @access  private
     * @var     array
     */
    private $variables = array();

   /**
     * Constructor
     *
     * @access  private
     * @return  object
     */
    public function __construct()
    {
        $config = Config::database();

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
     * Initiate a transaction
     *
     * @access  public
     * @return  void
     */
    public function begin()
    {
        return self::$instance->beginTransaction();
    }

    /**
     * Commit a transaction
     * 
     * @access  public
     * @return  void
     */
    public function commit()
    {
        return self::$instance->begin();
    }

    /**
     * Roll back a transaction
     *
     * @access  public
     * @return  void
     */
    public function rollback()
    {
        return self::$instance->rollBack();
    }

    /**
     * Custom query statement
     *
     * @access  public
     * @param   string  $statement
     * @return  object
     */
    public function query($statement)
    {
        $this->statement = $statement;
        return $this;
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
        $this->statement = 'SELECT ' . $statement;
        return $this;
    }

    /**
     * FROM statement
     *
     * @access  public
     * @param   string  $statement
     * @return  void
     */
    public function from($statement)
    {
        $this->statement .= ' FROM ' . $statement;
        return $this;
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
        $this->statement .= ' WHERE ' . $statement;

        if(isset($variables))
        {
            foreach($variables as $variable)
            {
                $this->variables[] = $variable;
            }
        }
        return $this;
    }

    /**
     * ORDER statement
     *
     * @access  public
     * @param   string  $statement
     * @return  object
     */
    public function order($statement = 'ASC')
    {
        $this->statement .= ' ORDER BY ' . $statement;
        return $this;
    }

    /**
     * GROUP statement
     *
     * @access  public
     * @param   string  $statement
     * @return  object
     */
    public function group($statement)
    {
        $this->statement .= ' GROUP BY ' . $statement;
        return $this;
    } 

    /**
     * LIMIT statement
     * 
     * @access  public
     * @param   int     $statement
     * @return  object
     */
    public function limit($statement = 1)
    {
        $this->statement .= ' LIMIT ' . $statement;
        return $this;
    }

    /**
     * JOIN statement
     *
     * @access  public
     * @return  object
     */
    public function join()
    {
        // TBD
    }

    /**
     * INSERT statement
     *
     * @access  public
     * @param   string  $table
     * @param   array   $columns
     * @param   array   $variables
     * @return  object
     */
    public function insert($table, array $columns, array $variables)
    {
        $this->statement = 'INSERT INTO ' . $table . ' ';

        $cols = null;

        for($i = 0; $i < (count($columns) - 1); $i++)
        {
            $cols .= $columns[$i] . ', ';
        }

        $cols .= end($columns);

        $values = '(' . str_repeat('?, ', (count($columns) - 1)) . '?)';
        $values = str_repeat($values . ', ', ((count($variables) / count($columns)) - 1)) . $values;

        $this->statement .= $cols . ' VALUES ' . $values;

        foreach($variables as $variable)
        {
            $this->variables[] = $variable;
        }

        return $this;
    }

    /**
     * UPDATE statement
     * 
     * @access  public
     * @param   string  $table
     * @param   array   $columns
     * @param   array   $variables
     * @return  object
     */
    public function update($table, array $columns, array $variables)
    {
        $this->statement = 'UPDATE ' . $table . ' SET ';

        $updates = null;

        for($i = 0; $i < (count($columns) - 1); $i++)
        {
            $updates .= $columns[$i] . ' = ?, ';
        }

        $updates .= end($columns) . ' = ? ';

        $this->statement .= $updates;

        foreach($variables as $variable)
        {
            $this->variables[] = $variable;
        }

        return $this;
    }

    /**
     * UPDATE statement, with CASE
     *
     * @access  public
     * @param   string  $table
     * @param   string  $column
     * @param   string  $case
     * @param   array   $variables
     * @return  object
     */
    public function updateMulti($table, $column, $case, array $variables)
    {
        $this->statement = 'UPDATE ' . $table . ' SET ' . $column . ' = CASE ' . $case;

        $cases = null;

        foreach($variables as $key => $value)
        {
            $cases .= ' WHEN \'' . $key . '\' THEN \'' . $value . '\'';
        }

        $this->statement .= $cases . ' ELSE ' . $column . ' END';

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
        $this->statement = 'DELETE FROM ' . $table;
        return $this;
    }

    /**
     * Execute a prepared statement
     *
     * @access  public
     * @param   string  $function
     * @param   string  $parameter
     * @return  mixed
     */
    public function execute($function = null, $parameter = null)
    {
        $sth = self::$instance->prepare($this->statement);

        if(empty($function))
        {
            return $sth->execute($this->variables);
        }

        $sth->execute($this->variables);

        return $sth->$function($parameter);
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
