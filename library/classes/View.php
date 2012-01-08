<?php
/**
 * CloudLib :: Lightweight RESTful MVC PHP Framework
 *
 * @author      Sebastian Book <email>
 * @copyright   Copyright (c) 2011 Sebastian Book <email>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     cloudlib
 */

/**
 * <class name>
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <email>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class View extends Factory
{
    /**
     * The View
     *
     * @access  public
     * @var     string
     */
    public $view = null;

    /**
     * Page layout
     *
     * @access  private
     * @var     string
     */
    public $layout = null;

    /**
     * View variables
     *
     * @access  private
     * @var     array
     */
    public $data = array();

    /**
     * Constructor
     *
     * @param   mixed   $view
     * @param   string  $layout
     * @param   array   $data
     * @access  public
     * @return  void
     */
    public function __construct($view, $layout = null, array $data = array())
    {
        if( ! is_array($view))
        {
            $this->view = $view;
            $this->layout = $layout;
            $this->data = $data;
        }
        else
        {
            $this->view = $view[0];
            $this->layout = isset($view[1]) ? $view[1] : null;
            $this->data = isset($view[2]) ? $view[2] : array();
        }
    }

    /**
     * Render a View and return the contents
     *
     * @access  public
     * @param   string  $view
     * @return  string
     */
    public function render()
    {
        if( ! file_exists($view = VIEWS . $this->view . EXT))
        {
            throw new RuntimeException(sprintf('The View "%s" does not exist',
                $this->view));
        }

        ob_start();

        extract($this->data);

        require $view;        

        if(isset($this->layout))
        {
            $body = ob_get_contents();

            ob_clean();

            if( ! file_exists($layout = LAYOUTS . $this->layout . EXT))
            {
                throw new RuntimeException(sprintf('The Layout "%s" does not exist', 
                    $this->layout));
            }

            require $layout;
        }

        return ob_get_clean();
    }

    
    
    /**
     * Magic get method for loading classes
     *
     * @access  public
     * @param   string  $class
     * @return  object
     */
    public function __get($class)
    {
        return $class::factory();
    }
}
