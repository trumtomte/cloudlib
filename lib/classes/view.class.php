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
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class view extends master
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
     * @return  void
     */
    public function __construct() {}

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
     * Magic method used for loading modules
     *
     * @access  public
     * @param   string  $class
     * @return  object
     */
    public function __get($module)
    {
        return core::loadModule($module);
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
            throw new cloudException('Invalid parameter, string required for render()');
        }

        $file = VIEWS . $view . EXT;

        if(!is_readable($file))
        {
            header('HTTP/1.0 404 Not Found');
            require LIB . 'error/404.php';
            exit(1);
        }

        ob_start();

        if(isset($this->vars))
        {
            extract($this->vars);
        }

        require $file;        

        echo ob_get_clean();
    }
}
