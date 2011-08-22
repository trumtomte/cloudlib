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
 * The view class.
 *
 * Determines what view to be rendered.
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
final class view
{
    /**
     * View variables
     *
     * @access  private
     * @var     array
     */
    private $vars = array();

    /**
     * Constructor
     *
     * @access  public
     * @param   string  $path
     * @return  void
     */
    public function __construct() {}

    /**
     * Factory
     *
     * @access  public
     * @param   string  $path
     * @return  void
     */
    public static function factory()
    {
        return new view();
    }

    /**
     * Renders the view
     *
     * @access  public
     * @param   string  $view
     * @return  void
     */
    public function render($view)
    {
        if(!is_string($view))
        {
            throw new cloud_exception('render() requires first argument to be string');
        }

        $file = VIEWS . $view . VIEWS_EXT;

        if(!is_readable($file))
        {
            header('HTTP/1.0 404 Not Found');
            require LIB . 'error/404.php';
            exit();
        }

        ob_start();

        if(isset($this->vars))
        {
            extract($this->vars);
        }

        require $file;        

        return ob_get_clean();
    }

    /**
     * Magic method,
     * sets the view variables
     *
     * @access  public
     * @param   string          $index
     * @param   string|integer  $value
     * @return  void
     */
    public function __set($index, $value)
    {
        $this->vars[$index] = $value;
    }

    /**
     * Magic method,
     * gets a module, initiates a new one if it doesnt exist
     *
     * @access  public
     * @param   string  $class
     * @return  object
     */
    public function __get($module)
    {
        return core::loadModule($module);
    }
}
