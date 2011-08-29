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
final class resizeimage
{
    /**
     * The image to be modified
     *
     * @access  private
     * @var     string
     */
    private static $image;

    private static $ext;

    /**
     * Array of allowed extensions
     *
     * @access  private
     * @var     array
     */
    private $extensions = array('jpg', 'jpeg', 'png', 'gif');

    /**
     * The error message
     *
     * @access  private
     * @var     string
     */
    private static $error;

    /**
     * The constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Load a file
     *
     * @access  public
     * @param   string  $file
     * @return  void
     */
    public function load($file)
    {
        $file = PUB . $file;

        $ext = pathinfo($file, PATHINFO_EXTENSION);

        if(!in_array($ext, $this->extensions))
        {
            self::$error = 'Invalid file extension';

            return false;
        }

        self::$ext = $ext;

        switch($ext)
        {
            case 'jpg' :
            case 'jpeg' :
                self::$image = imagecreatefromjpeg($file);
                break;
            case 'gif' :
                self::$image = imagecreatefromgif($file);
                break;
            case 'png' :
                self::$image = imagecreatefrompng($file);
                break;
            default :
                self::$error = 'Could not load image';
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
    public function save($file = null, $compression = 75)
    {
        if(isset($file))
        {
            $file = PUB . $file;
        }

        switch(self::$ext)
        {
            case 'jpg' :
            case 'jpeg' :
                imagejpeg(self::$image, $file, $compression);
                break;
            case 'gif' :
                imagegif(self::$image, $file);
                break;
            case 'png' :
                imagepng(self::$image, $file);
                break;
            default :
                self::$error = 'Could not save image';
                break;
        }

        imagedestroy(self::$image);
    }

    /**
     * Get the width
     *
     * @access  private
     * @return  int
     */
    private function getWidth()
    {
        return imagesx(self::$image);
    }

    /**
     * Get the height
     * 
     * @access  private
     * @return  int
     */
    private function getHeight()
    {
        return imagesy(self::$image);
    }

    /**
     * Resize to a certain width
     *
     * @access  public
     * @param   int     $width
     * @retun   void
     */
    public function width($width)
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
    public function height($height)
    {
        $width = $this->getWidth() * ($height / $this->getHeight());

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
            self::$error = 'Imagecreatetruecolor did not work';
        }

        if(!imagecopyresampled($new, self::$image, 0, 0, 0, 0,
            $width, $height, $this->getWidth(), $this->getHeight()))
        {
            self::$error = 'Imagecopyresampled did not work';
        }

        self::$image = $new;
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

        if(!array_key_exists('func', $options))
        {
            $options['func'] = 'scale';
        }

        if(!array_key_exists('value', $options))
        {
            $options['value'] = 100;
        }

        switch($options['func'])
        {
            case 'width' :
                $this->width($options['value']);
                break;
            case 'height' :
                $this->height($options['value']);
                break;
            case 'scale' : 
                $this->scale($options['value']);
                break;
            default:
                self::$error = 'Invalid function name, use - width/height/scale.';
                break;
        }

        if(!array_key_exists('compression', $options))
        {
            $compression = null;
        }

        $this->save($save, $compression);
    }

    /**
     * Returns the error
     *
     * @access  public
     * @return  string
     */
    public function error()
    {
        return self::$error;
    }

    /**
     * Returns the dimensions
     *
     * @access  public
     * @return  string
     */
    public function dimensions($file)
    {
        $this->load($file);

        $x = $this->getWidth();
        $y = $this->getHeight();

        $string = '(' . $x . ' x ' . $y . ')';

        return $string;

        //imagedestroy(self::$image);
    }
}
