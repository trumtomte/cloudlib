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
class Image
{
    /**
     * The image resource
     *
     * @access  protected
     */
    protected $image = null;

    /**
     * Current image extension
     *
     * @access  protected
     * @var     string
     */
    protected $extension;

    /**
     * Current image width
     *
     * @access  protected
     * @var     int
     */
    protected $width;

    /**
     * Current image height
     * 
     * @access  protected
     * @var     int
     */
    protected $height;

    /**
     * JPEG compression value
     *
     * @access  protected
     * @var     int
     */
    protected $compression = 75;

    /**
     * The error
     *
     * @access  protected
     * @var     string
     */
    protected $error;

    /**
     * Allowed image extensions
     *
     * @access  protected
     * @var     array
     */
    protected $imageExtensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp');

    /**
     * Directory paths for image files
     *
     * @access  public
     * @var     string
     */
    public static $path;

    /**
     * Constructor.
     *
     * Load the image (optional) and set the jpeg compression value (optional)
     *
     * @access  public
     * @param   string  $image
     * @param   int     $compression
     * @return  void
     */
    public function __construct($image = null, $compression = null)
    {
        if($image !== null)
        {
            $this->load($image);
        }

        if($compression !== null)
        {
            $this->compression = $compression;
        }
    }

    /**
     * Load an image
     *
     * @access  public
     * @param   string  $image
     * @return  void
     */
    public function load($image)
    {
        $file = static::$path . $image;

        $image = new SplFileInfo($file);

        if( ! in_array($image->getExtension(), $this->imageExtensions))
        {
            $this->error = 'Invalid file extension';
            return false;
        }

        $this->extension = $image->getExtension();

        list($this->width, $this->height) = getimagesize($file);

        switch($image->getExtension())
        {
            case 'jpg':
            case 'jpeg':
                $this->image = imagecreatefromjpeg($file);
                break;
            case 'png':
                $this->image = imagecreatefrompng($file);
                break;
            case 'gif':
                $this->image = imagecreatefromgif($file);
                break;
            case 'bmp':
                $this->image = imagecreatefromwbmp($file);
                break;
            default:
                $this->error = 'Unable to create image';
                break;
        }
    }

    /**
     * Save an image
     *
     * @access  public
     * @param   string  $file
     * @return  void
     */
    public function save($file)
    {
        $newImage = static::$path . $file;

        switch($this->extension)
        {
            case 'jpg':
            case 'jpeg':
                imagejpeg($this->image, $newImage, $this->compression);
                break;
            case 'png':
                imagepng($this->image, $newImage);
                break;
            case 'gif':
                imagegif($this->image, $newImage);
                break;
            case 'bmp':
                imagewbmp($this->image, $newImage);
                break;
            default:
                $this->error = 'Unable to write image';
                break;
        }

        if(is_resource($this->image))
        {
            imagedestroy($this->image);
        }
    }

    /**
     * Resize an image to the given width
     *
     * @access  public
     * @param   int     $width
     * @return  void
     */
    public function resizeToWidth($width)
    {
        if($this->image === null)
        {
            $this->error = 'No image has been loaded';
            return false;
        }

        $height = ($width / $this->width) * $this->height;

        $this->resample($width, $height);
    }

    /**
     * Resize an image to the given height
     *
     * @access  public
     * @param   int     $height
     * @return  void
     */
    public function resizeToHeight($height)
    {
        if($this->image === null)
        {
            $this->error = 'No image has been loaded';
            return false;
        }

        $width = ($height / $this->height) * $this->width;

        $this->resample($width, $height);
    }

    /**
     * Scale an image by a certain percentage
     *
     * @access  public
     * @param   int     $scale
     * @return  void
     */
    public function scale($scale)
    {
        if($this->image === null)
        {
            $this->error = 'No image has been loaded';
            return false;
        }

        $width = ($scale / 100) * $this->width;
        $height = ($scale / 100) * $this->height;

        $this->resample($width, $height);
    }

    // Shorthand for thumbnails
    // TODO
    public function thumb()
    {
        // TODO
    }

    /**
     * Shorthand function for cropping an image from the center
     *
     * @access  public
     * @param   int     $width
     * @param   int     $height
     * @return  void
     */
    public function cropCenter($width = 100, $height = 100)
    {
        if($this->image === null)
        {
            $this->error = 'No image has been loaded';
            return false;
        }

        $max = max($this->width, $this->height);

        $srcW = $max * 0.5;
        $srcH = $max * 0.5;

        $x = ($this->width - $srcW) / 2;
        $y = ($this->height - $srcH) / 2;

        $this->resample($width, $height, $srcW, $srcH, $x, $y);
    }

    // Crop an image by x, y, width and height
    // TODO
    public function crop()
    {
        // TODO
    }

    /**
     * Resample an image
     *
     * @access  protected
     * @param   int     $destW
     * @param   int     $destH
     * @param   int     $srcW
     * @param   int     $srcH
     * @param   int     $x
     * @param   int     $y
     * @return  void
     */
    protected function resample($destW, $destH, $srcW = null, $srcH = null, $x = 0,
        $y = 0)
    {
        $srcW = ($srcW === null) ? $this->width : $srcW;
        $srcH = ($srcH === null) ? $this->height : $srcH;

        if( ! ($identifier = imagecreatetruecolor($destW, $destH)))
        {
            $this->error = 'Resampling failed, imagecreatetruecolor() returned false';
            return false;
        }

        if( ! imagecopyresampled($identifier, $this->image, 0, 0, $x, $y, $destW, $destH,
            $srcW, $srcH))
        {
            $this->error = 'Resampling failed, imagecopyresampled() returned false';
            return false;
        }

        $this->image = $identifier;
    }

    /**
     * Set the JPEG compression value
     * 
     * @access  public
     * @param   int     $compression
     * @return  void
     */
    public function setCompression($compression)
    {
        $this->compression = (int) $compression;
    }

    /**
     * Get the error
     *
     * @access  public
     * @return  string
     */
    public function getError()
    {
        return $this->error;
    }
}
