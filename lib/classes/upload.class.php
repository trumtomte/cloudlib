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
final class upload extends master
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
    private $imagetypes = array('jpg', 'jpeg', 'png', 'gif');

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

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
            self::$error = 'No file was selected';

            return false;
        }

        $files = $_FILES;
        $key = key($_FILES);

        $file = $files[$key]['name'];
        $tmp = $files[$key]['tmp_name'];

        if(is_array($file))
        {
            throw new cloudException('Use uploadFiles() for multiple uploads');
        }

        if(is_string(self::$error = $this->hasError($files[$key]['error'])))
        {
            return false;
        }

        if(is_string(self::$error = $this->isUploaded($tmp)))
        {
            return false;
        }

        if(is_string(self::$error = $this->checkFilesize($tmp)))
        {
            return false;
        }

        if(is_string(self::$error = $this->checkFiletype($file)))
        {
            return false;
        }

        if(is_string(self::$error = $this->isImage($tmp)))
        {
            return false;
        }
        else
        {
            $imageinfo = $this->isImage($tmp);
        }

        $name = $this->setFilename($file);

        $dir = self::$config['directory'];

        if(is_string(self::$error = $this->upload($tmp, $dir . $name)))
        {
            return false;
        }

        /*
         * TODO: fix way of setting the data
         *
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
         */

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
            throw new cloudException('Use uploadFile() for single uploads');
        }


    }

    private function hasError($file)
    {
        if($file !== 0)
        {
            return $this->fileErrors[$file];
        }

        return true;
    }

    private function isUploaded($file)
    {
        if(!is_uploaded_file($file))
        {
            return 'File was not uploaded via HTTP POST';
        }

        return true;
    }

    private function checkFilesize($file)
    {
        $size = filesize($file);
        $max  = self::$config['filesize'];

        if(is_string($max))
        {
            $max = number::byte($max);
        }

        if($size > $max)
        {
            return 'The file exceeds the max allowed filesize';
        }

        return true;
    }

    private function checkFiletype($file)
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);

        if(isset(self::$config['filetypes']))
        {
            $types = explode('|', self::$config['filetypes']);

            if(!in_array($ext, $types))
            {
                return 'Invalid filetype';
            }

            return true;
        }

        return true;
    }

    private function setFilename($file)
    {
        $pre = isset(self::$config['prefix']) ? self::$config['prefix'] : '';

        if(isset(self::$config['filename']))
        {
            $name = $pre . self::$config['filename'];
        }
        else
        {
            $name = $pre . $file;
        }

        $name = string::replace(' ', '_', $name);

        if(self::$config['overwrite'] == false)
        {
            $copy = '';
            $counter = 1;

            while(file_exists(self::$config['directory'] . $copy . $name))
            {
                $copy = 'copy(' . $counter . ')_';
                $counter++;
            }

            $name = $copy . $name;
        }

        return $name;
    }

    private function isImage($file)
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);

        if(in_array($ext, $this->imagetypes))
        {
            list($width, $height) = getimagesize($file);

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

    private function upload($file, $path)
    {
        if(!move_uploaded_file($file, $path))
        {
            return 'Unable to upload the chosen file';
        }

        return true;
    }

    private function setData(array $data)
    {

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
