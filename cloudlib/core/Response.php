<?php
/**
 * Cloudlib
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2012 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace cloudlib\core;

use cloudlib\core\Container;

/**
 * The Response class
 *
 * @copyright   Copyright (c) 2012 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Response extends Container
{
    /**
     * Array of HTTP Status Codes
     *
     * @access  public
     * @var     array
     */
    public $httpStatusCodes = array(
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
     * Array of HTTP Headers to be sent
     *
     * @access  public
     * @var     array
     */
    public $headers = array();

    /**
     * The Response HTTP Status Code
     *
     * @access  public
     * @var     int
     */
    public $status;

    /**
     * The Response body
     *
     * @access  public
     * @var     string
     */
    public $body;

    /**
     * Create a new Response object, defining the body, status and the array of headers
     *
     * @access  public
     * @param   string  $body       The Response body
     * @param   int     $status     The status code
     * @param   array   $headers    Array of HTTP Headers
     * @return  void
     */
    public function __construct($body = '', $status = 200, array $headers = array())
    {
        $this->body = (string) $body;
        $this->status = (int) $status;
        $this->headers = $headers;
    }

    /**
     * Set the body content
     *
     * @access  public
     * @param   string  $body   The body content
     * @return  Response        Returns itself, for method chaining
     */
    public function body($body = null)
    {
        $this->body = (string) $body;

        return $this;
    }

    /**
     * Set the status code
     *
     * @access  public
     * @param   int     $status The status code
     * @return  Response        Returns itself, for method chaining
     */
    public function status($status = null)
    {
        $this->status = (int) $status;

        return $this;
    }

    /**
     * Set a HTTP Header (ex array('Location' => 'www.google.com')
     * Would be: 'Location: www.google.com')
     *
     * @access  public
     * @param   string  $key    Header attribute
     * @param   string  $value  Header attribute value
     * @return  Response        Returns itself, for method chaining
     */
    public function header($key, $value)
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * Send all headers
     *
     * @access  public
     * @param   string  $protocol   The current HTTP protocol version
     * @return  void
     */
    public function sendHeaders($protocol)
    {
        $this->headers = array_merge($this->headers, $this->vars);

        header(sprintf('%s %s %s', $protocol, $this->status, $this->httpStatusCodes[$this->status]));

        if( ! isset($this->headers['Content-Type']))
        {
            $this->header('Content-Type', 'text/html; charset=utf8');
        }

        if( ! isset($this->headers['Content-Length']))
        {
            $this->header('Content-Length', strlen( (string) $this->body));
        }

        foreach($this->headers as $key => $value)
        {
            header($key . ': ' . $value);
        }
    }

    /**
     * Echo out the response body
     *
     * Send headers if they have not been sent already
     *
     * @access  public
     * @param   string  $method     The request method
     * @param   string  $protocol   The current HTTP protocol version
     * @return  void
     */
    public function send($method, $protocol)
    {
        if( ! headers_sent())
        {
            $this->sendHeaders($protocol);
        }

        if($method !== 'HEAD')
        {
            if(strlen( (string) $this->body) > 0)
            {
                echo $this->body;
            }
        }
    }
}
