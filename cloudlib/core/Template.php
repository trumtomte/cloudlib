<?php
/**
 * Cloudlib
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2012 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace cloudlib\core;

use RuntimeException;

/**
 * The Template class
 *
 * @copyright   Copyright (c) 2012 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Template
{
    /**
     * Current template (file) that will be used
     *
     * @access  public
     * @var     string
     */
    public $template = null;

    /**
     * Current layout (file) that will be used
     *
     * @access  public
     * @var     string
     */
    public $layout = null;

    /**
     * Array of variables available in the template/layout
     *
     * @access  public
     * @var     array
     */
    public $vars = null;

    /**
     * Set the Template/Layout/Variables that will be used
     *
     * @access  public
     * @param   string  $template   The template filename
     * @param   string  $layout     The layout filename
     * @param   array   $vars       Array of template variables
     * @return  void
     */
    public function __construct($template = null, $layout = null, array $vars = array())
    {
        if($template)
        {
            $this->template = $this->find($template);

            if($layout)
            {
                $this->layout = $this->find($layout);
            }
        }

        $this->vars = $vars;
    }

    /**
     * Set the template file
     *
     * @access  public
     * @param   string  $template   The template file path
     * @return  void
     */
    public function setTemplate($template)
    {
        $this->template = $this->find($template);
    }

    /**
     * Set the layout file
     *
     * @access  public
     * @param   string  $layout The layout file path
     * @return  void
     */
    public function setLayout($layout)
    {
        $this->layout = $this->find($layout);
    }

    /**
     * Set a variable for the template/layout
     *
     * @access  public
     * @param   string  $key    Variable name
     * @param   mixed   $value  Variable value
     * @return  void
     */
    public function set($key, $value)
    {
        $this->vars[$key] = $value;
    }

    /**
     * Merge an array of variables with the current array of variables
     *
     * @access  public
     * @param   array   $vars   Array to be merged
     * @return  void
     */
    public function merge(array $vars)
    {
        $this->vars = array_merge_recursive($this->vars, $vars);
    }

    /**
     * Check if the filename contains a path otherwise use the templates folder.
     * If the file does not exist throw an exception.
     *
     * @access  public
     * @param   string  $filename   The filename to be found
     * @throws  RuntimeException    If the filename was not found
     * @return  string  $filename   Returns the found filename
     */
    public function find($filename)
    {
        if(strpos($filename, '/') === false)
        {
            $filename = 'templates/' . $filename;
        }

        if( ! file_exists($filename))
        {
            throw new RuntimeException(sprintf('File [%s] does not exist', $filename));
        }

        return $filename;
    }

    /**
     * Renders the template/layout with the defined variables and returns the
     * contents
     *
     * @access  public
     * @return  string  The rendered content
     */
    public function render()
    {
        ob_start();

        extract($this->vars);

        require $this->template;

        if($this->layout)
        {
            $template = ob_get_contents();

            ob_clean();

            require $this->layout;
        }

        return ob_get_clean();
    }

    /**
     * When converted to String, the Template class will attempt to return the
     * contents of the Template::render() method
     *
     * @access  public
     * @return  string  The rendered content
     */
    public function __toString()
    {
        return (string) $this->render();
    }
}
