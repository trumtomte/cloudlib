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
     * Current controller classname
     *
     * @access  private
     * @var     string
     */
    private $classname;

    /**
     * Page layout
     *
     * @access  private
     * @var     string
     */
    private $layout = null;

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
    public function __construct($classname)
    {
        $this->classname = $classname;
    }

    /**
     * Function for declaring a view variable
     *
     * @access  public
     * @param   string|int  $index
     * @param   mixed       $value
     * @return  void
     */
    public function set($index, $value)
    {
        $this->$index = $value;
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
     * Sets the page layout
     *
     * @access  public
     * @param   string  $layout
     * @return  object
     */
    public function layout($layout = null)
    {
        if($layout === null)
        {
            $layout = $this->classname;
        }

        $file = LAYOUTS . $layout . EXT;

        if(!file_exists($file))
        {
            throw new cloudException('Layout "' . $layout . '" does not exist');
        }

        $this->layout = $file;

        return $this;
    }

    /**
     * Renders the view
     *
     * @access  public
     * @param   string  $view
     * @return  void
     */
    public function render($view = null)
    {
        if($view === null)
        {
            $view = $this->classname;
        }

        $file = VIEWS . $view . EXT;

        if(!file_exists($file))
        {
            throw new cloudException('View "' . $view . '" does not exist');
        }

        ob_start();

        if(isset($this->vars))
        {
            foreach($this->vars as $key => $value)
            {
                $$key = $value;
            }
        }

        require $file;        

        if(isset($this->layout))
        {
            $body = ob_get_clean();

            require $this->layout;
        }
        else
        {
            echo ob_get_clean();
        }
    }
}
