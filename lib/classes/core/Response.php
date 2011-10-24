<?php
/**
 * CloudLib :: Lightweight MVC PHP Framework
 *
 * @author      Sebastian Book <sebbebook@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     cloudlib
 */

/**
 * The response class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes.core
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Response extends Factory
{
    /**
     * Array of status codes
     *
     * @access  private
     * @var     array
     */
    public static $codes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );

    /**
     * Array of headers
     *
     * @access  public
     * @var     array
     */
    public static $headers = array();

    /**
     * Current status code, defaults 200
     *
     * @access  public
     * @var     int
     */
    public $status = 200;

    /**
     * Output body
     *
     * @access  public
     * @var     string
     */
    public $body = null;

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct($status = 200)
    {
        $this->status = $status;
    }

    /**
     * 404
     *
     * @access  public
     * @return  void
     */
    public function notFound()
    {
        ob_start();

        require LIB . 'error/404.php';

        $response = Response::factory(404)->body(ob_get_clean())->send();

        exit(0);
    }

    /**
     * Sets the output body
     *
     * @access  public
     * @param   string  $body
     * @return  object
     */
    public function body($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Add a header
     *
     * @access  public
     * @param   string  $header
     * @return  void
     */ 
    public static function addHeader($header)
    {
        static::$headers[] = $header;
    }

    /**
     * Send the headers
     *
     * @access  public
     * @return  void
     */
    public function sendHeaders()
    {
        if(!headers_sent())
        {
            if(Request::_server('SERVER_PROTOCOL') !== false)
            {
                $protocol = Request::_server('SERVER_PROTOCOL');
            }
            else
            {
                $protocol = 'HTTP/1.1';
            }

            header($protocol . ' ' . $this->status . ' ' . static::$codes[$this->status]);

            if(isset(static::$headers))
            {
                foreach(static::$headers as $header)
                {
                    header($header);
                }
            }
        }       
    }

    /**
     * Echo the output
     * 
     * @access  public
     * @return  void
     */
    public function send() 
    {
        $this->sendHeaders();

        if(isset($this->body))
        {
            echo $this->body;
        }
    }
}
