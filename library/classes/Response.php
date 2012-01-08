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
class Response extends Factory
{
    /**
     * Array of status codes
     *
     * @access  private
     * @var     array
     */
    public $codes = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        // Successful 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        // Client Error 4xx
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
        // Server Error 5xx
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
     * Current status code
     *
     * @access  public
     * @var     int
     */
    // TODO statisk status?
    public $status;

    /**
     * Output body
     *
     * @access  public
     * @var     string
     */
    public $body;

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
     * Sets the response status
     *
     * @access  public
     * @param   int     $status
     * @return  object
     */
    public function status($status)
    {
        $this->status = $status;
        return $this;
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
        if($body instanceof View)
        {
            $this->body = $body->render();
        } 
        else
        {
            $this->body = View::factory($body)->render();
        }
        return $this;
    }

    /**
     * Add a header
     *
     * @access  public
     * @param   string  $key
     * @param   string  $value
     * @return  void
     */
    public static function header($key, $value)
    {
        static::$headers[$key] = $value;
    }

    /**
     * Send the headers
     *
     * @access  public
     * @return  void
     */
    public function sendHeaders()
    {
        if( ! headers_sent())
        {
            header(Request::protocol() . ' ' . $this->status . ' ' . $this->codes[$this->status]);

            if( ! isset(static::$headers['Content-Type']))
            {
                $encoding = Config::get('app.encoding');
                header('Content-Type: text/html; charset=' . $encoding);
            }

            if( ! isset(static::$headers['Content-Length']))
            {
                header('Content-Length: ' . strlen($this->body));
            }

            if(isset(static::$headers))
            {
                foreach(static::$headers as $key => $value)
                {
                    header($key . ': ' . $value);
                }
            }
        }
    }

    /**
     * Send (echo) the output
     *
     * @access  public
     * @return  void
     */
    public function send()
    {
        if( ! isset($this->body))
        {
            throw new Exception('No response body has been set');
        }

        $this->sendHeaders();

        echo $this->body;
    }
}
