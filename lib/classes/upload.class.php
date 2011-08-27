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
 * The upload class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
final class upload
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
     * @var     array|string
     */
    private static $error;

    /**
     * Array of imagetypes for identifying if the file is an image
     *
     * @access  private
     * @var     array
     */
    private $imagetypes = array();

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct()
    {
        $imagetypes = config::general('imageExtensions');

        $this->imagetypes = explode('|', $imagetypes);
    }

    /**
     * Upload a single file
     *
     * @access  public
     * @return  boolean
     */
    public function uploadFile()
    {
        if(empty($_FILES))
        {
            //self::$error = '$_FILES array is empty';

            return false;
        }

        $files = $_FILES;
        $first = key($_FILES);

        if(is_array($files[$first]['name']))
        {
            throw new cloud_exception('Use uploadFiles() for multiple uploads');
        }

        if($files[$first]['error'] !== 0)
        {
            self::$error = $this->fileErrors[$files[$first]['error']];

            return false;
        }

        if(!is_uploaded_file($files[$first]['tmp_name']))
        {
            self::$error = 'Not an HTTP upload';

            return false;
        }

        $size = filesize($files[$first]['tmp_name']);

        $maxsize = self::$config['filesize'];

        if(is_string($maxsize))
        {
            $maxsize = number::byte($maxsize);
        }

        if($size > $maxsize)
        {
            self::$error = 'File is to large';

            return false;
        }

        $ext = pathinfo($files[$first]['name'], PATHINFO_EXTENSION);

        if(isset(self::$config['filetypes']))
        {
            $types = explode('|', self::$config['filetypes']);

            if(!in_array($ext, $types))
            {
                self::$error = 'Invalid filetype';

                return false;
            }
        }

        $dir = self::$config['directory'];

        $pre = isset(self::$config['prefix']) ? self::$config['prefix'] : '';

        if(isset(self::$config['filename']))
        {
            $name = $pre . self::$config['filename'];
        }
        else
        {
            $name = $pre . $files[$first]['name'];
        }

        if(self::$config['overwrite'] == false)
        {
            $copy = '';
            $counter = 1;

            while(file_exists($dir . $copy . $name))
            {
                $copy = 'copy(' . $counter . ')_';
                $counter++;
            }

            $name = $copy . $name;
        }

        $tmp = $files[$first]['tmp_name'];


        if(in_array($ext, $this->imagetypes))
        {
            $image = true;

            list($width, $height) = getimagesize($tmp);

            if(isset(self::$config['width']))
            {
                if($width > self::$config['width'])
                {
                    self::$error = 'Invalid width';
    
                    return false;
                }
            }

            if(isset(self::$config['height']))
            {
                if($height > self::$config['height'])
                {
                    self::$error = 'Invalid height';
    
                    return false;
                }
            }
        }
        else
        {
            $image  = 0;
            $width  = 0;
            $height = 0;
        }

        if(!move_uploaded_file($tmp, $dir . $name))
        {
            self::$error = 'Could not move the uploaded file';

            return false;
        }

        self::$data = array(
            'name'     => $name,
            'path'     => $dir,
            'fullpath' => $dir . $name,
            'ext'      => $ext,
            'size'     => number::byte($size),
            'image'    => $image,
            'width'    => $width,
            'height'   => $height
        );

        return true;
    }

    /**
     * Upload multiple files
     *
     * @access  public
     * @return  boolean
     */
    public function uploadFiles()
    {
        if(empty($_FILES))
        {
            //self::$error = '$_FILES array is empty';

            return false;
        }

        $files = $_FILES;
        $first = key($_FILES);

        if(!is_array($files[$first]['name']))
        {
            throw new cloud_exception('Use uploadFile() for single uploads');
        }


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
