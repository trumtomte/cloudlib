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
class Uploader extends Factory
{
    /**
     * Config array
     *
     * @access  private
     * @var     array
     */
    private static $config = array(
        'directory' => PUB,
        'filetypes' => null,
        'filesize'  => 1048576,
        'filename'  => null,
        'overwrite' => false,
        'prefix'    => null,
        'width'     => null,
        'height'    => null
    );

    /**
     * Array of file errors
     *
     * @access  private
     * @var     array
     */
    private $fileErrors = array(
        1 => 'Exceeds the max filesize',
        2 => 'Exceeds the max filesize',
        3 => 'Was only Partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk',
        8 => 'A PHP extension stopped the file upload'
    );

    /**
     * Array with file information
     *
     * @access  private
     * @var     array
     */
    private static $data = array();

    /**
     * Error message
     *
     * @access  private
     * @var     string|array
     */
    private static $error = null;

    /**
     * Array of imagetypes for identifying if the file is an image
     *
     * @access  private
     * @var     array
     */
    private $imagetypes = array('jpg', 'jpeg', 'png', 'gif', 'bmp');

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Upload function
     *
     * @access  public
     * @return  bool
     */
    public function upload()
    {
        if(Request::method() === 'POST')
        {
            if(empty($_FILES))
            {
                self::$error = 'No file(s) was selected';
                return false;
            }
        }

        $files = array_shift($_FILES);

        if(is_array($files['name']))
        {
            return $this->uploadFiles($files);
        }

        return $this->uploadFile($files);
    }

    /**
     * Upload a single file
     *
     * @access  private
     * @param   array   $files
     * @return  bool
     */
    private function uploadFile($files)
    {
        $data = $this->doUpload($files['name'], $files['tmp_name'], $files['error']);

        if(is_string($data))
        {
            self::$error = $data;
            return false;
        }

        self::$data = $data;
        return true;
    }

    /**
     * Upload multiple files
     *
     * @access  private
     * @param   array   $files
     * @return  bool
     */
    private function uploadFiles($files)
    {
        foreach($files['name'] as $key => $value)
        {
            $data = $this->doUpload($files['name'][$key], $files['tmp_name'][$key], 
                                    $files['error'][$key]);

            if(is_string($data))
            {
                if(empty($files['name'][$key]))
                {
                    self::$error[$key] = $data;
                }
                else
                {
                    self::$error[$files['name'][$key]] = $data;
                }
            }
            else
            {
                self::$data[$files['name'][$key]] = $data;
            }
        }

        if(self::$error === null)
        {
            return true;
        }
        return false;
    }

    /**
     * Main function for uploading a file
     *
     * @access  private
     * @param   string  $name
     * @param   string  $tmp_name
     * @param   int     $error
     * @return  string|array
     */
    private function doUpload($name, $tmp_name, $error)
    {
        if(is_string($error = $this->getError($error)))
        {
            return $error;
        }
        if(!$this->isUploaded($tmp_name))
        {
            return 'File was not uploaded via HTTP POST';
        }
        if(($size = $this->getSize($tmp_name)) == false)
        {
            return 'File exceeds the max allowed filesize';
        }
        if(($ext = $this->getType($name)) == false)
        {
            return 'Invalid file extension';
        }
        if(is_string($image = $this->isImage($tmp_name, $ext)))
        {
            return $image;
        }
        
        $filename = $this->getName($name, $ext);

        if(!$this->moveUpload($tmp_name, self::$config['directory'] . $filename))
        {
            return 'Unable to upload the chosen file';
        }

        $dir = self::$config['directory'];

        return $this->getData($name, $filename, $dir, $ext, $size, $image);
    }

    /**
     * Gets the file error
     * 
     * @access  private
     * @param   int     $error
     * @return  mixed
     */
    private function getError($error)
    {
        if($error !== 0)
        {
            return $this->fileErrors[$error];
        }
        return true;
    }

    /**
     * Check if a file was uploaded via HTTP POST
     *
     * @access  private
     * @param   string  $tmp_name
     * @return  bool
     */
    private function isUploaded($tmp_name)
    {
        if(!is_uploaded_file($tmp_name))
        {
            return false;
        }
        return true;
    }

    /**
     * Get the filesize
     *
     * @access  private
     * @param   string  $tmp_name
     * @return  mixed
     */
    private function getSize($tmp_name)
    {
        $size = filesize($tmp_name);
        $max = self::$config['filesize'];

        if(is_string($max))
        {
            $max = Number::byte($max);
        }
        if($size > $max)
        {
            return false;
        }
        return $size;
    }

    /**
     * Get the file extension
     * 
     * @access  private
     * @param   string  $name
     * @return  mixed
     */
    private function getType($name)
    {
        $ext = pathinfo($name, PATHINFO_EXTENSION);

        if(isset(self::$config['filetypes']))
        {
            $types = explode('|', self::$config['filetypes']);

            if(!in_array($ext, $types))
            {
                return false;
            }
            return $ext;
        }
        return $ext;
    }

    /**
     * Check if the file is an image
     *
     * @access  private
     * @param   string  $tmp_name
     * @param   string  $ext
     * @return  mixed
     */
    private function isImage($tmp_name, $ext)
    {
        if(in_array($ext, $this->imagetypes))
        {
            list($width, $height) = getimagesize($tmp_name);
        
            if(isset(self::$config['width']))
            {
                if($width > self::$config['width'])
                {
                    return 'Invalid width';
                }
            }
            if(isset(self::$config['height']))
            {
                if($height > self::$config['height'])
                {
                    return 'Invalid height';
                }
            }
            return array(
                'image' => 1,
                'width' => $width,
                'height' => $height
            );
        }
        return array(
            'image' => 0,
            'width' => 0,
            'height' => 0
        );
    }

    /**
     * Get the filename
     *
     * @access  private
     * @param   string  $name
     * @param   string  $ext
     * @return  string
     */
    private function getName($name, $ext)
    {
        $prefix = isset(self::$config['prefix']) ? self::$config['prefix'] : '';

        if(isset(self::$config['filename']))
        {
            $filename = $prefix . self::$config['filename'] . $ext;
        }
        else
        {
            $filename = $prefix . $name;
        }

        $filename = mb_ereg_replace(' ', '_', $filename);

        if(self::$config['overwrite'] == false)
        {
            $copy = '';
            $counter = 1;

            while(file_exists(self::$config['directory'] . $copy . $filename))
            {
                $copy = 'copy(' . $counter . ')_';
                $counter++;
            }
            $filename = $copy . $filename;
        }
        return $filename;
    }

    /**
     * Move the uploaded file
     * 
     * @access  private
     * @param   string  $tmp_name
     * @param   string  $path
     * @return  bool
     */
    private function moveUpload($tmp_name, $path)
    {
        if(!move_uploaded_file($tmp_name, $path))
        {
            return false;
        }
        return true;
    }

    /**
     * Get the file data as an array
     * 
     * @access  private
     * @param   string  $name
     * @param   string  $filename
     * @param   string  $dir
     * @param   string  $ext
     * @param   int     $size
     * @param   array   $image
     * @return  array
     */
    private function getData($name, $filename, $dir, $ext, $size, $image)
    {
        return array(
            'origname' => $name,
            'name'     => $filename,
            'path'     => $dir,
            'fullpath' => $dir . $filename,
            'ext'      => $ext,
            'size'     => Number::byte($size),
            'image'    => $image['image'],
            'width'    => $image['width'],
            'height'   => $image['height']
        );
    }
    

    /**
     * Set config items
     *
     * @access  public
     * @param   array   $config
     * @return  void
     */
    public function config(array $config)
    {
        foreach($config as $key => $value)
        {
            if(array_key_exists($key, self::$config))
            {
                if($key === 'directory')
                {
                    self::$config[$key] .= $value;
                }
                else
                {
                    self::$config[$key] = $value;
                }
            }
        }
    }

    /**
     * Get the error
     *
     * @access  public
     * @return  array|string
     */
    public function error()
    {
        return self::$error;
    }

    /**
     * Get the file information
     *
     * @access  public
     * @return  array
     */
    public function data()
    {
        return self::$data;
    }
}
