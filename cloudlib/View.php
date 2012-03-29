<?php
/**
 * Cloudlib
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace cloudlib;

/**
 * The View class
 *
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class View
{
    /**
     * String containing the directory name to be prepended to filenames
     *
     * @access  public
     * @var     string
     */
    public $directory = null;

    /**
     * The rendered content
     *
     * @access  public
     * @var     string
     */
    public $content = '';

    /**
     * Define a View file, layout file (optional) and an array of view variables (optional)
     *
     * @access  public
     * @param   string  $view   The view filename
     * @param   string  $layout The layout filename
     * @param   array   $data   Array of view variables
     * @return  void
     */
    public function __construct($view, $layout = null, array $data = array())
    {
        $view = $this->find($view);

        if($layout)
        {
            $layout = $this->find($layout);
        }

        $this->render($view, $layout, $data);
    }

    /**
     * Render and define the content string
     *
     * @access  public
     * @param   string  $view   The view filename
     * @param   string  $layout The layout filename
     * @param   array   $data   Array of view variables
     * @return  void
     */
    public function render($view, $layout = null, array $data = array())
    {
        ob_start();

        extract($data);

        require $view;

        if($layout)
        {
            $body = ob_get_contents();

            ob_clean();

            require $layout;
        }

        $this->content = ob_get_clean();
    }

    /**
     * Find a file based on the filename
     *
     * @access  public
     * @param   string  $filename   The filename to be found
     * @return  string  $file       Returns the found filename
     */
    public function find($filename)
    {
        if( ! file_exists($file = $this->directory . $filename . '.php'))
        {
            throw new RuntimeException('Could not locate file: ' . $file);
        }

        return $file;
    }

    /**
     * Define the directory path to be prepended to filenames
     *
     * @access  public
     * @param   string  $directory  The directory path
     * @return  void
     */
    public function directory($directory)
    {
        $this->directory = $directory;
    }

    /**
     * When the View object is converted to a string return the rendered content
     *
     * @access  public
     * @return  string  The rendered content
     */
    public function __toString()
    {
        return (string) $this->content;
    }
}
