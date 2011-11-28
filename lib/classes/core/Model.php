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
 * The model class.
 *
 * Abstract class for all models.
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes.core
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
abstract class Model extends Factory
{
    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Gets the first value from a SELECT query by id
     *
     * @access  public
     * @param   string  $select
     * @param   string  $from
     * @param   int     $id
     * @return  string
     */
    public function getFirstById($select, $from, $id)
    {
        $array = $this->database
                    ->select($select)
                    ->from($from)
                    ->where('id = ?', array($id))
                    ->execute('fetch');

        return $array[0];
    }

    /**
     * Gets the first row from a SELECT query by id
     *
     * @access  public
     * @param   string  $select
     * @param   string  $from
     * @param   int     $id
     * @return  array
     */
    public function getRowById($select, $from, $id)
    {
        return $this->database
                ->select($select)
                ->from($from)
                ->where('id = ?', array($id))
                ->execute('fetch');
    }

    /**
     * Delete an item based on an id
     *
     * @access  public
     * @param   string  $from
     * @param   int     $id
     * @return  void
     */
    public function deleteById($from, $id)
    {
        $this->database
            ->delete($from)
            ->where('id = ?', array($id))
            ->execute();
    }

    /**
     * Update a single column by id
     *
     * @access  public
     * @param   string  $table
     * @param   string  $column
     * @param   string  $value
     * @param   int     $id
     * @return  void
     */
    public function updateColById($table, $column, $value, $id)
    {
        $this->database
            ->update($table, array($column), array($value))
            ->where('id = ?', array($id))
            ->execute();
    }

    /**
     * Magic method for loading helper classes
     *
     * @access  public
     * @param   string  $class
     * @return  object
     */
    final public function __get($helper)
    {
        return $helper::factory();
    }
}
