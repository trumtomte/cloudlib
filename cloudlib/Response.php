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
class Response
{
    /**
     * Array of status codes
     *
     * @access  private
     * @var     array
     */
    public $statusCodes = array(
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
    public $headers = array();

    /**
     * Current status code
     *
     * @access  public
     * @var     int
     */
    public $status;

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
    public function __construct(Request $request)
    {
        $this->request = $request;
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
        $this->status = (int) $status;
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
        $this->body = (string) $body;
        return $this;
    }

    /**
     * Shorthand function for errors
     *
     * @access  public
     * @param   int     $code
     * @param   array   $errors
     * @param   mixed   $data
     * @return  void
     */
    public function error($code, array $errors = array(), $data = null)
    {
        $this->status($code);

        if( ! isset($errors[$code]))
        {
            $this->body(sprintf('%s: %s', $code, $this->statusCodes[$code]));
        }
        else
        {
            if($data === null)
            {
                $data = array('statusCode' => $code, 'statusMessage' => $this->statusCodes[$code]);
            }
            elseif(is_array($data))
            {
                array_merge($data, array('statusCode' => $code, 'statusMessage' => $this->statusCodes[$code]));
            }

            if($errors[$code] instanceof Closure)
            {
                $this->body($errors[$code]($data));
            }
            else
            {
                $this->body($errors[$code]);
            }
        }
    }

    /**
     * Add a header
     *
     * @access  public
     * @param   string  $key
     * @param   string  $value
     * @return  void
     */
    public function header($key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * Send the headers
     *
     * @access  public
     * @return  void
     */
    public function sendHeaders()
    {
        header(sprintf('%s %s %s', $this->request->protocol(), $this->status,
            $this->statusCodes[$this->status]));

        if( ! isset($this->headers['Content-Type']))
        {
            $this->header('Content-Type', 'text/html; charset=utf8');
        }

        if( ! isset($this->headers['Content-Length']))
        {
            $this->header('Content-Length', strlen( (string) $this->body));
        }

        if(isset($this->headers))
        {
            foreach($this->headers as $key => $value)
            {
                header($key . ': ' . $value);
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
        if( ! headers_sent())
        {
            $this->sendHeaders();
        }
        
        if($this->request->isHead() === false)
        {
            if($this->body !== null)
            {
                echo $this->body;
            }
        }
    }
}
