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
class Uploader
{
    /**
     * The config
     *
     * @access  protected
     * @var     array
     */
    protected static $config = array();

    /**
     * PHP File errors
     * 
     * @access  protected
     * @var     array
     */
    protected $fileErrors = array(
        1 => 'Exceeds the max filesize',
        2 => 'Exceeds the max filesize',
        3 => 'Was only Partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk',
        8 => 'A PHP extension stopped the file upload'
    );

    /**
     * Array of image extensions for recognition
     *
     * @access  protected
     * @var     array
     */
    protected $imageExtensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp');

    /**
     * The $_FILES array
     *
     * @access  protected
     * @var     array
     */
    protected $files = array();

    /**
     * The array of errors
     *
     * @access  protected
     * @var     array
     */
    protected $errors = null;

    /**
     * Array of file data
     *
     * @access  protected
     * @var     array
     */
    protected $data = array();

    /**
     * Directory for uploads
     *
     * @access  public
     * @var     string
     */
    public static $path;

    /**
     * Constructor.
     *
     * Set the $_FILES array and set the config array (optional)
     *
     * @access  public
     * @param   array   $files
     * @param   array   $config
     * @return  void
     */
    public function __construct($files, array $config = array())
    {
        $this->files = array_shift($files);

        static::$config = array(
            'directory' => static::$path,
            'filetypes' => null,
            'filesize' => 1048576,
            'filename' => null,
            'prefix' => null,
            'width' => null,
            'height' => null
        );

        if(isset($config))
        {
            $this->setConfig($config);
        }
    }

    /**
     * Initialize the uploading process
     *
     * @access  public
     * @return  boolean
     */
    public function upload()
    {
        if(isset($this->files))
        {
            if(is_array($this->files['name']))
            {
                return $this->uploadMultiple();
            }
            return $this->uploadSingle();
        }
        $this->errors[] = 'No File(s) was uploaded';
        return false;
    }

    /**
     * Initialize a single upload process
     *
     * @access  protected
     * @return  boolean
     */
    protected function uploadSingle()
    {
        $this->process($this->files['name'], $this->files['tmp_name'],
            $this->files['error'], 0);

        if($this->errors === null)
        {
            return true;
        }
        return false;
    }

    /**
     * Initialize a multi upload process
     *
     * @access  protected
     * @return  boolean
     */
    protected function uploadMultiple()
    {
        foreach($this->files['name'] as $key => $value)
        {
            $this->process($this->files['name'][$key], $this->files['tmp_name'][$key],
                $this->files['error'][$key], $key);
        }

        if($this->errors === null)
        {
            return true;
        }
        return false;
    }

    /**
     * The upload process
     *
     * @access  protected
     * @param   string  $name
     * @param   string  $tmp
     * @param   int     $error
     * @param   int     $key
     * @return  boolean
     */
    protected function process($name, $tmp, $error, $key)
    {
        if($error !== 0)
        {
            $this->errors[$key] = $this->fileErrors[$error];
            return false;
        }

        $nameObj = new SplFileInfo($name);
        $tempObj = new SplFileInfo($tmp);

        // Is it a file?
        if($tempObj->isFile() == false)
        {
            $this->errors[$name] = 'File is invalid';
            return false;
        }

        $this->data[$name]['origname'] = $name;

        // Valid filesize?
        if($tempObj->getSize() > static::$config['filesize'])
        {
            $this->errors[$name] = 'File exceeds the max allowed filesize';
            return false;
        }

        $this->data[$name]['size'] = $tempObj->getSize();

        // Valid file extension?
        if(isset(static::$config['filetypes']) && is_array(static::$config['filetypes']))
        {
            if( ! in_array($nameObj->getExtension(), static::$config['filetypes']))
            {
                $this->errors[$name] = 'Invalid file extension';
                return false;
            }
        }

        $this->data[$name]['ext'] = $nameObj->getExtension();

        // Is the file an image?
        if(in_array($nameObj->getExtension(), $this->imageExtensions))
        {
            list($width, $height) = getimagesize($tmp);

            // Check width
            if(isset(static::$config['width']))
            {
                if($width > static::$config['width'])
                {
                    $this->errors[$name] = 'Invalid width';
                    return false;
                }
            }

            // Check height
            if(isset(static::$config['height']))
            {
                if($height > static::$config['height'])
                {
                    $this->errors[$name] = 'Invalid height';
                    return false;
                }
            }

            $this->data[$name]['image'] = 1;
            $this->data[$name]['width'] = $width;
            $this->data[$name]['height'] = $height;
        }
        else
        {
            $this->data[$name]['image'] = 0;
            $this->data[$name]['width'] = 0;
            $this->data[$name]['height'] = 0;
        }

        // Set name
        $prefix = isset(static::$config['prefix']) ? static::$config['prefix'] : '';

        if(isset(static::$config['filename']))
        {
            $newName = $prefix . static::$config['filename'] . $nameObj->getExtension();
        }
        else
        {
            $newName = $prefix . $name;
        }

        $newName = str_replace(' ', '_', $newName);

        $copy = '';
        $counter = 1;

        while(file_exists(static::$config['directory'] . $copy . $newName))
        {
            $copy = sprintf('copy(%s)_', $counter);
            $counter++;
        }

        $newName = $copy . $newName;

        $this->data[$name]['name'] = $newName;

        $dir = rtrim(static::$config['directory'], '/') . '/';

        if( ! move_uploaded_file($tmp, $dir . $newName))
        {
            $this->errors[$name] = 'Unable to upload the chosen file';
            return false;
        }

        $this->data[$name]['path'] = $dir;
        $this->data[$name]['fullpath'] = $dir . $newName;

        return true;
    }

    /**
     * Get the error array
     *
     * @access  public
     * @return  array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get the data array
     *
     * @access  public
     * @return  array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Sets the config array
     *
     * @access  public
     * @param   array   $config
     * @return  void
     */
    public function setConfig(array $config)
    {
        foreach($config as $key => $value)
        {
            static::$config[$key] = $value;
        }
    }
}
