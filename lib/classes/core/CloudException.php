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
 * The CloudException class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class CloudException extends Exception
{
    /**
     * Array of Readable errors
     *
     * @access  public
     * @var     array
     */
    public static $error = array(
        E_ERROR             => 'Fatal Error',
        E_USER_ERROR        => 'User Error',
        E_PARSE             => 'Parse Error',
        E_WARNING           => 'Warning',
        E_USER_WARNING      => 'User Warning',
        E_STRICT            => 'Strict',
        E_NOTICE            => 'Notice',
        E_RECOVERABLE_ERROR => 'Recoverable Error'
    );

    /**
     * Constructor
     *
     * @access  public
     * @param   string  $message
     * @param   int     $code
     * @return  void
     */
    public function __construct($message = null, $code = 1)
    {
        parent::__construct($message, $code);
    }

    /**
     * Custom exception handler
     *
     * @access  public
     * @param   object  $e
     * @return  void
     */
    public static function exceptionHandler(Exception $e)
    {
        try
        {
            $message = $e->getMessage();
            $code    = $e->getCode();
            $file    = $e->getFile();
            $line    = $e->getLine();
            $trace   = $e->getTrace();
    
            if(isset(static::$error[$code]))
            {
                $code = static::$error[$code];
            }

            if(ob_get_contents() !== false)
            {
                ob_end_clean();
            }

            if(!PRODUCTION)
            {
                require LIB . 'error/exception.php';
            }
        }
        catch(Exception $e)
        {
            if(ob_get_contents() !== false)
            {
                ob_end_clean();
            }

            if(!PRODUCTION)
            {
                echo $e->getMessage();
            }
        }
    }
}
