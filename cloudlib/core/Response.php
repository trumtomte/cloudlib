<?php
/**
 * Cloudlib
 *
 * @author      Sebastian Bengtegård <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2013 Sebastian Bengtegård <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace cloudlib\core;

use InvalidArgumentException;
use ArrayAccess;

/**
 * The Response class
 *
 * @copyright   Copyright (c) 2013 Sebastian Bengtegård <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Response implements ArrayAccess
{
    /**
     * Array of HTTP Status Codes
     *
     * @var array
     */
    public $httpStatusCodes = [
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
    ];

    /**
     * Array of HTTP Headers to be sent
     *
     * @var array
     */
    public $headers = [];

    /**
     * The Response HTTP Status Code
     *
     * @var int
     */
    public $status = 200;

    /**
     * The Response body
     *
     * @var string
     */
    public $body = '';

    /**
     * The Response charset
     *
     * @var string
     */
    public $charset = 'utf-8';

    /**
     * Request variables
     *
     * @var array
     */
    public $request = [];

    /**
     * Create a new Response object
     *
     * @param   string  $body       The Response body
     * @param   int     $status     The status code
     * @param   array   $headers    Array of HTTP Headers
     * @return  void
     */
    public function __construct($body = '', $status = 200, array $headers = [])
    {
        $this->body = (string) $body;
        $this->status = (int) $status;
        $this->headers = $headers;
    }

    /**
     * Set the body content
     *
     * @param   string  $body   The body content
     * @return  Response
     */
    public function body($body = null)
    {
        $this->body = (string) $body;
        return $this;
    }

    /**
     * Set the status code
     *
     * @param   int     $status The status code
     * @return  Response
     */
    public function status($status = null)
    {
        $this->status = (int) $status;
        return $this;
    }

    /**
     * Set a response header
     *
     * @param   string  $key    Header attribute
     * @param   string  $value  Header attribute value
     * @return  Response
     */
    public function header($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Set multiple response headers
     *
     * @param   string  $headers    Headers
     * @return  Response
     */
    public function headers(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /**
     * Prepare the response
     *
     * @param   Request     $request    The request object
     * @return  Response
     */
    public function prepare($request)
    {
        $this->request = [
            'protocol' => $request->protocol,
            'method' => $request->method,
            'HTTP_IF_MODIFIED_SINCE' => $request->server('HTTP_IF_MODIFIED_SINCE'),
            'HTTP_IF_NONE_MATCH' => $request->server('HTTP_IF_NONE_MATCH')
        ];

        return $this;
    }

    /**
     * Prepare headers
     *
     * @return  Response
     */
    public function prepareHeaders()
    {
        if( ! isset($this->headers['Content-Type']))
        {
            $this->header('Content-Type', 'text/html; charset=' . $this->charset);
        }
        elseif(strpos($this->headers['Content-Type'], 'text/') === 0 &&
            strpos($this->headers['Content-Type'], 'charset') === false)
        {
            $this->headers['Content-Type'] .= '; charset=' . $this->charset;
        }

        $this->headers['Content-Length'] = strlen( (string) $this->body);

        return $this;
    }

    /**
     * Send all headers
     *
     * @return  Response
     */
    public function sendHeaders()
    {
        header(sprintf('%s %s %s', $this->request['protocol'], $this->status, $this->httpStatusCodes[$this->status]));
        header(sprintf('Status: %s', $this->status));

        foreach($this->headers as $key => $value)
        {
            header(sprintf('%s: %s', $key, $value));
        }

        return $this;
    }

    /**
     * Redirect the request
     *
     * @param   string  $location   The location URL
     * @param   int     $status     The HTTP status code
     * @param   array   $parameters The HTTP parameters
     * @return  void
     */
    public function redirect($location, $status = 302, $parameters = [])
    {
        if(empty($location))
        {
            throw new InvalidArgumentException('Cannot redirect to an empty URL');
        }

        if($parameters)
        {
            $location .= sprintf('?%s', http_build_query($parameters));
        }

        $this->header('Location', $location);
        $this->send($status);
        $this->respond();
    }

    /**
     * Abort the request
     *
     * @param   int     $status     The HTTP status code
     * @param   string  $message    The abort message
     * @param   array   $headers    HTTP headers
     * @return  void
     */
    public function abort($status, $message = null, array $headers = [])
    {
        if( ! isset($this->httpStatusCodes[$status]))
        {
            throw new InvalidArgumentException(sprintf('[%s] is not a valid HTTP status code', $status));
        }

        if( ! $message)
        {
            $message = $this->httpStatusCodes[$status];
        }

        $this->headers($headers);
        $this->send($message, $status);
        $this->respond();
    }

    /**
     * Helper method for defining the response body
     *
     * @param   string  $body   The response body
     * @param   int     $status The HTTP Status code
     * @return  Response
     */
    public function send($body = '', $status = 200)
    {
        $contentType = 'text/html';

        if(is_int($body))
        {
            $status = $body;

            if( ! isset($this->httpStatusCodes[$status]))
            {
                throw new InvalidArgumentException(sprintf('[%s] is not a valid HTTP status code', $status));
            }

            $body = $this->httpStatusCodes[$status];
        }
        elseif(is_object($body) || is_array($body))
        {
            $contentType = 'application/json';
            $body = json_encode($body, JSON_NUMERIC_CHECK);
        }

        $this->status($status);
        $this->header('Content-Type', $contentType);
        $this->body($body);

        return $this;
    }

    /**
     * Helper method for defining the response body (JSON)
     *
     * @param   string  $body   The response body
     * @param   int     $status The HTTP Status code
     * @return  Response
     */
    public function json($body = '', $status = 200)
    {
        if(is_int($body))
        {
            $status = $body;

            if( ! isset($this->httpStatusCodes[$status]))
            {
                throw new InvalidArgumentException(sprintf('[%s] is not a valid HTTP status code', $status));
            }

            $body = $this->httpStatusCodes[$status];
        }

        $body = json_encode($body, JSON_NUMERIC_CHECK);

        $this->status($status);
        $this->header('Content-Type', 'application/json');
        $this->body($body);

        return $this;
    }

    /**
     * Helper method for defining the response body, with templating
     *
     * @param   string  $template   The template filename
     * @param   mixed   $layout     The template layout filename, or template variables
     * @param   array   $vars       Template variables
     * @return  Response
     */
    public function render($template, $layout = null, array $vars = [])
    {
        if(is_array($layout) && ! $vars)
        {
            $vars = $layout;
            $layout = null;
        }

        ob_start();
        extract($vars);
        require $template;

        if($layout)
        {
            $template = ob_get_contents();
            ob_clean();
            require $layout;
        }

        return $this->send(ob_get_clean());
    }

    /**
     * Send the response body to the browser and exit
     *
     * @return  void
     */
    public function respond()
    {
        if( ! headers_sent())
        {
            $this->prepareHeaders();
            $this->sendHeaders();
        }

        if($this->request['method'] == 'HEAD')
        {
            $this->body('');
        }

        echo $this->body;

        exit(0);
    }

    /**
     * ArrayAccess method for defining a response header
     *
     * @param   string  $key    The response header
     * @param   string  $value  The response header value
     * @return  void
     */
    public function offsetSet($key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * ArrayAccess method for retrieving a response header
     *
     * @param   string  $key    The response header
     * @return  mixed           Returns the response header if it exists, else false
     */
    public function offsetGet($key)
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : false; 
    }

    /**
     * ArrayAccess method for checking if a response header was set
     *
     * @param   string  $key    The response header
     * @return  bool            True if the header exists, else false
     */
    public function offsetExists($key)
    {
        return (bool) isset($this->headers[$key]); 
    }

    /**
     * ArrayAccess method for unsetting a response header
     *
     * @return  void
     */
    public function offsetUnset($key)
    {
        unset($this->headers[$key]); 
    }

    /**
     * Shorthand method for setting the Expires header
     *
     * @param   mixed   $time   The time until the response expires
     * @return  void
     */
    public function expires($time)
    {
        $time = is_int($time) ? $time : strtotime($time);
        $this->header('Expires', date(DATE_RFC1123, $time));
    }

    /**
     * Shorthand method for forcing no-cache
     *
     * @return  void
     */
    public function noCache()
    {
        $this->header('Cache-Control', 'no-store, no-cache, max-age=0, must-revalidate');
        $this->header('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
        $this->header('Pragma', 'no-cache');
    }

    /**
     * Shorthand method for setting the Last-Modified header
     *
     * @param   mixed   $time   The time since it was last modified
     * @return  void
     */
    public function lastModified($time)
    {
        $this->header('Last-Modified', date(DATE_RFC1123, $time) . ' GMT');

        if($this->request['HTTP_IF_MODIFIED_SINCE'])
        {
            if(strtotime($this->request['HTTP_IF_MODIFIED_SINCE']) === $time)
            {
                $this->abort(304);
            }
        }
    }

    /**
     * Shorthand metod for setting the ETag header
     *
     * @param   string  $identifier ETag identifier
     * @return  void
     */
    public function etag($identifier)
    {
        if($this->request['HTTP_IF_NONE_MATCH'])
        {
            $this->header('ETag', sprintf('"%s"', $identifier));
        }
    }
}
