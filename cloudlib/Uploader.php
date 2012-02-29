<?php
/**
 * CloudLib :: Flexible Lightweight PHP Framework
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     Cloudlib
 */

namespace cloudlib;

// SPL
use SplFileInfo;

/**
 * The Uploader class
 *
 * Class to make file uploading easy!
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
     * @access  protected
     * @var     array
     */
    protected static $paths = array();

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
    public function __construct(array $files, array $config = array())
    {
        $this->files = array_shift($files);

        static::$config = array(
            'directory' => static::$paths['uploadDirectory'],
            'filetypes' => null,
            'filesize' => 1048576,
            'filename' => null,
            'prefix' => null,
            'width' => null,
            'height' => null,
            'replace' => false
        );

        if(isset($config))
        {
            $this->setConfig($config);
        }
    }

    /**
     * Check if any files has been uploaded
     *
     * @access  public
     * @return  boolean
     */
    public function isEmpty()
    {
        if(is_array($this->files['error']))
        {
            foreach($this->files['error'] as $value)
            {
                if($value !== 4)
                {
                    return false;
                }
            }
            return true;
        }

        return ($this->files['error'] !== 4) ? false : true;
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
            $this->errors[$key] = array('error' => $this->fileErrors[$error]);
            return false;
        }

        $nameObj = new SplFileInfo($name);
        $tempObj = new SplFileInfo($tmp);

        // Is it a file?
        if($tempObj->isFile() == false)
        {
            $this->error[$key] = array('name' => $name, 'error' => 'File is invalid');
            return false;
        }

        $this->data[$key]['origname'] = $name;

        // Valid filesize?
        if($tempObj->getSize() > static::$config['filesize'])
        {
            $this->errors[$key] = array(
                'name' => $name,
                'error' => 'File exceeds the max allowed filesize'
            );
            return false;
        }

        $this->data[$key]['size'] = $tempObj->getSize();

        // Valid file extension?
        if(isset(static::$config['filetypes']) && is_array(static::$config['filetypes']))
        {
            if( ! in_array($nameObj->getExtension(), static::$config['filetypes']))
            {
                $this->error[$key] = array(
                    'name' => $name,
                    'error' => 'Invalid file extension'
                );
                return false;
            }
        }

        $this->data[$key]['ext'] = $nameObj->getExtension();

        // Is the file an image?
        if(in_array($nameObj->getExtension(), $this->imageExtensions))
        {
            list($width, $height) = getimagesize($tmp);

            // Check width
            if(isset(static::$config['width']))
            {
                if($width > static::$config['width'])
                {
                    $this->error[$key] = array(
                        'name' => $name,
                        'error' => 'Invalid width'
                    );
                    return false;
                }
            }

            // Check height
            if(isset(static::$config['height']))
            {
                if($height > static::$config['height'])
                {
                    $this->error[$key] = array(
                        'name' => $name,
                        'error' => 'Invalid height'
                    );
                    return false;
                }
            }

            $this->data[$key]['image'] = 1;
            $this->data[$key]['width'] = $width;
            $this->data[$key]['height'] = $height;
        }
        else
        {
            $this->data[$key]['image'] = 0;
            $this->data[$key]['width'] = 0;
            $this->data[$key]['height'] = 0;

        }

        // Set name
        $prefix = isset(static::$config['prefix']) ? static::$config['prefix'] : '';

        if(isset(static::$config['filename']))
        {
            $newName = $prefix . static::$config['filename'] .
                '.' . $nameObj->getExtension();
        }
        else
        {
            $newName = $prefix . $name;
        }

        $newName = str_replace(' ', '_', $newName);

        if(static::$config['replace'] == false)
        {
            $copy = '';
            $counter = 1;

            while(file_exists(static::$config['directory'] . $copy . $newName))
            {
                $copy = sprintf('copy(%s)_', $counter);
                $counter++;
            }

            $newName = $copy . $newName;
        }

        $this->data[$key]['name'] = $newName;

        $dir = rtrim(static::$config['directory'], '/') . '/';

        if( ! move_uploaded_file($tmp, $dir . $newName))
        {
            $this->error[$key] = array(
                'name' => $name,
                'error' => 'Unable to upload the chosen file'
            );
            return false;
        }

        $this->data[$key]['path'] = $dir;
        $this->data[$key]['fullpath'] = $dir . $newName;

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

    /**
     * Define directory pahts
     *
     * @access  public
     * @param   array   $paths
     * @return  void
     */
    public static function setPaths(array $paths)
    {
        foreach($paths as $key => $value)
        {
            static::$paths[$key] = $value;
        }
    }
}
