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

    /**
     * Database query
     *
     * @access  public
     * @param   string      $statement
     * @param   array       $variables
     * @param   int         $fetchArgument
     * @return  array|bool
     */
    public function q($statement, array $variables = array(), $fetchArgument = null)
    {
        $sth = self::$instance->prepare($statement);
        
        $sth->execute($variables);

        if($sth->rowCount() > 0)
        {
            return $sth->fetchAll($fetchArgument);
        }

        return false;
    }

    /**
     * Updates table data
     *
     * @access  public
     * @param   string  $table
     * @param   array   $variables
     * @param   string  $conditions
     * @return  bool
     */
    public function update($table, array $variables, $conditions = null)
    {
        $updates = null;
        $values  = array();

        foreach($variables as $key => $value)
        {
            $updates .= $key . ' = ?, ';
            $values[] = $value;
        }
        
        $updates = rtrim($updates, ', ');

        $sth = self::$instance->prepare('UPDATE ' . $table . ' SET '
                                      . $updates . ' ' . $conditions);

        return (bool) $sth->execute($values);
    }

    public function delete($table, array $variables, $conditions = null)
    {

    }

    /**
     * Insert data into a table
     *
     * @access  public
     * @param   string  $table
     * @param   array   $variables
     * @return  bool
     */
    public function insert($table, array $variables)
    {
        $columns     = null;
        $placeholder = null;
        $values      = array();

        foreach($variables as $key => $value)
        {
            $columns     .= $key . ', ';
            $placeholder .= '?, ';
            $values[]    .= $value;
        } 

        $columns = rtrim($columns, ', ');
        $placeholder = rtrim($placeholder, ', ');

        $sth = self::$instance->prepare('INSERT INTO ' . $table
                                      . ' (' . $columns . ') '
                                      . 'VALUES (' . $placeholder . ')');

        return (bool) $sth->execute($values);
    }
}
