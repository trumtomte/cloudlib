<?php
/**
 * CloudLib :: Lightweight RESTful MVC PHP Framework
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     Cloudlib
 */

/**
 * <class name>
 *
 * <short description>
 *
 * @package     Cloudlib
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class View
{
    /**
     * Directory paths
     *
     * @access  public
     * @var     string
     */
    public static $paths = array();

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
     * The rendered content
     *
     * @access  public
     * @var     string
     */
    public $content;

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

        $this->setContent();
    }

    /**
     * Render the content and assign it to the content variable
     *
     * @access  protected
     * @return  void
     */
    protected function setContent()
    {
        if( ! file_exists($view = static::$paths['views'] . $this->view . '.php'))
        {
            throw new RuntimeException(sprintf('Unable to locate the View [%s] in [%s]',
                $this->view, $layout));
        }

        $this->view = $view;

        if($this->layout)
        {
            if( ! file_exists($layout = static::$paths['layouts'] . $this->layout . '.php'))
            {
                throw new RuntimeException(sprintf('Unable to locate the Layout [%s] in [%s]', 
                    $this->layout, $layout));
            }

            $this->layout = $layout;
        }
        
        $this->content = $this->render();
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
        ob_start();

        extract($this->data);

        require $this->view;        

        if(isset($this->layout))
        {
            $body = ob_get_contents();

            ob_clean();

            require $this->layout;
        }

        return ob_get_clean();
    }

    /**
     * Return the content, used by Views to echo the content
     *
     * @access  public
     * @return  string
     */
    public function __toString()
    {
        return $this->content;
    }
}
