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
 * The image class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
final class image
{
    /**
     * The image to be modified
     *
     * @access  private
     * @var     string
     */
    private $image;

    /**
     * Array of allowed extensions
     *
     * @access  private
     * @var     array
     */
    private $extensions = array();

    /**
     * The error message
     *
     * @access  private
     * @var     string
     */
    private $error;

    /**
     * The constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct()
    {
        $extensions = config::image('extensions');

        $this->extensions = explode('|', $extensions);
    }

    /**
     * Load a file
     *
     * @access  public
     * @param   string  $file
     * @return  void
     */
    public function load($file)
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);

        if(!in_array($ext, $this->extensions))
        {
            $this->error = 'Invalid file extension';

            return false;
        }

        $type = getimagesize($file);

        switch($type[2])
        {
            case IMAGETYPE_JPEG :
                $this->image = imagecreatefromjpeg($file);
                break;
            case IMAGETYPE_GIF :
                $this->image = imagecreatefromgif($file);
                break;
            case IMAGETYPE_PNG :
                $this->image = imagecreatefrompng($file);
                break;
            default :
                $this->error = 'Could not load image';
                break;
        }
    }

    /**
     * Save a file
     *
     * @access  public
     * @param   string  $file
     * @param   int     $type
     * @param   int     $compression
     * @return  void
     */
    public function save($file, $type = IMAGETYPE_JPEG, $compression = 75)
    {
        switch($type)
        {
            case IMAGETYPE_JPEG :
                imagejpeg($this->image, $file, $compression);
                break;
            case IMAGETYPE_GIF :
                imagegif($this->image, $file);
                break;
            case IMAGETYPE_PNG :
                imagepng($this->image, $file);
                break;
            default :
                $this->error = 'Could not save image';
                break;
        }
    }

    /**
     * Get the width
     *
     * @access  private
     * @return  int
     */
    private function getWidth()
    {
        return imagesx($this->image);
    }

    /**
     * Get the height
     * 
     * @access  private
     * @return  int
     */
    private function getHeight()
    {
        return imagesy($this->image);
    }

    /**
     * Resize to a certain width
     *
     * @access  public
     * @param   int     $width
     * @retun   void
     */
    public function resizeToWidth($width)
    {
        $height = $this->getHeight() * ($width / $this->getWidth());

        $this->resize($width, $height);
    }

    /**
     * Resize to a certain height
     * 
     * @access  public
     * @param   int     $height
     * @return  void
     */
    public function resizeToHeight($height)
    {
        $width = $this->getWidth * ($height / $this->getHeight());

        $this->resize($width, $height);
    }

    /**
     * Scale the image
     *
     * @access  public
     * @param   int     $scale
     * @return  void
     */
    public function scale($scale)
    {
        $width = $this->getWidth() * ($scale / 100);
        $height = $this->getHeight() * ($scale / 100);

        $this->resize($width, $height);
    }

    /**
     * Resize the image
     *
     * @access  private
     * @param   int     $width
     * @param   int     $height
     * @return  void
     */
    private function resize($width, $height)
    {
        if(!($new = imagecreatetruecolor($width, $height)))
        {
            $this->error = 'Imagecreatetruecolor did not work';
        }

        if(!imagecopyresampled($new, $this->image, 0, 0, 0, 0,
            $width, $height, $this->getWidth(), $this->getHeight()))
        {
            $this->error = 'Imagecopyresampled did not work';
        }

        $this->image = $new;
    }

    /**
     * Shorthand function for creating thumbnail images (smaller images)
     *
     * @access  public
     * @param   string  $load
     * @param   string  $save
     * @param   array   $options
     * @return  void
     */
    public function thumb($load, $save, array $options = array())
    {
        $this->load($load);

        


    }
}
