<?php
/**
 * Cloudlib
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2012 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace cloudlib\core;

use ArrayAccess;
use Exception;
use ErrorException;
use cloudlib\core\ClassLoader;
use cloudlib\core\Request;
use cloudlib\core\Response;
use cloudlib\core\Router;
use cloudlib\core\Template;

require_once 'ClassLoader.php';
require_once 'PropertyContainer.php';

/**
 * The core framework class, which takes use of the other available classes
 *
 * @copyright   Copyright (c) 2012 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Cloudlib implements ArrayAccess
{
    use PropertyContainer;

    /**
     * Array of custom defined error handlers
     *
     * @access  public
     * @var     array
     */
    public $errors = [];

    /**
     * Application base path for requests
     *
     * @access  public
     * @var     string
     */
    public $base = '/';

    /**
     * Initialize ErrorHandlers, and add instances of the Core classes.
     * Set the base uri.
     *
     * @access  public
     * @return  void
     */
    public function __construct()
    {
        $that = $this;

        set_exception_handler(function(Exception $e) use ($that)
        {
            $that->exceptionHandler($e);
        });

        set_error_handler(function($code, $str, $file, $line) use ($that)
        {
            $that->exceptionHandler(new ErrorException($str, $code, 0, $file, $line));
        });

        register_shutdown_function(function() use ($that)
        {
            if(($e = error_get_last()) !== null)
            {
                extract($e);
                $that->exceptionHandler(new ErrorException($message, $type, 0, $file, $line));
            }
        });

        $this->loader = $this->instance(function()
        {
            return new ClassLoader();
        });

        $this->loader->registerNamespaces([
            'cloudlib\\core' => dirname(dirname(__DIR__))
        ]);

        $this->loader->register();

        $this->request = $this->instance(function()
        {
            return new Request($_SERVER, $_GET, $_POST, $_FILES, $_COOKIE);
        });

        // Get current script path
        $scriptPath = $this->request->server('SCRIPT_NAME');

        if($scriptPath)
        {
            // Get current script name
            $scriptName = basename($scriptPath);
            // Set the base to the script path excluding the script name
            $this->base = substr($scriptPath, 0, (strlen($scriptPath) - (strlen($scriptName) + 1)));
        }

        $this->router = $this->instance(function()
        {
            return new Router();
        });

        $this->template = $this->instance(function()
        {
            return new Template();
        });

        $this->response = $this->instance(function()
        {
            return new Response();
        });
    }

    /**
     * Add a $callback that will be called at shutdown (ex. closing of a database connection)
     *
     * @access  public
     * @param   callable    $callback   The callback to be executed
     * @return  void
     */
    public function teardown(callable $callback)
    {
        register_shutdown_function($callback);
    }


    /**
     * Shorthand function for returning JSON
     *
     * @access  public
     * @param   mixed   $input      The input to be converted to JSON
     * @param   int     $options    Options for json_encode()
     * @return  string              The encode JSON
     */
    public function json($input, $options = JSON_NUMERIC_CHECK)
    {
        $this->header('Content-Type', 'application/json');
        return json_encode($input, $options);
    }

    /**
     * Returns an URL relative to the application (absolute if $absolute is true)
     *
     * @access  public
     * @param   string  $location   The URL end point
     * @param   boolean $absolute   If we should return an absolute URL
     * @param   array   $parameters Array of HTTP request parameters
     * @return  string              The complete URL (relative or absolute)
     */
    public function urlFor($location, $absolute = false, $parameters = [])
    {
        if($parameters)
        {
            $location .= sprintf('?%s', http_build_query($parameters));
        }

        if($absolute)
        {
            $protocol = $this->request->isSecure() ? 'https' : 'http';

            return sprintf('%s://%s%s', $protocol, $this->request->host(),
                preg_replace('/\/{2,}/', '/', $this->base . '/' . $location));
        }

        return preg_replace('/\/{2,}/', '/', $this->base . '/' . $location);
    }

    /**
     * Create a new response with a location header
     *
     * @access  public
     * @param   string  $location   The destination (Location: 'destinaton')
     * @param   int     $status     The redirect http status
     * @param   array   $parameters Array of HTTP request parameters
     * @return  void
     */
    public function redirect($location, $status = 302, $parameters = [])
    {
        if($parameters)
        {
            $location .= sprintf('?%s', http_build_query($parameters));
        }

        if( ! filter_var($location, FILTER_VALIDATE_URL))
        {
            $location = $this->urlFor($location, true);
        }

        $response = new Response('', $status, ['Location' => $location]);
        $response->send($this->request->method, $this->request->protocol());

        exit(0);
    }

    /**
     * Create a new response that will terminate the current request (with a status header = $code)
     *
     * @access  public
     * @param   int     $code       The status code
     * @param   mixed   $message    Data that will be passed to the response function
     * @param   array   $headers    Array of HTTP headers to be sent
     * @return  void
     */
    public function abort($code, $message = null, array $headers = [])
    {
        $response = new Response('', $code, $headers);

        if( ! isset($this->errors[$code]))
        {
            $body = $message;
        }
        else
        {
            if($message instanceof Exception)
            {
                $parameter = $message;
            }
            else
            {
                $parameter = [
                    'message' => $message,
                    'statusCode' => $code,
                    'statusMessage' => $response->httpStatusCodes[$code]
                ];
            }

            $body = $this->errors[$code]($parameter);
        }

        $response->body($body);
        $response->send($this->request->method, $this->request->protocol());

        exit(0);
    }

    /**
     * Escape a string, array, object or an array of objects from html entities
     *
     * @access  public
     * @param   mixed   $input      The input to be escaped
     * @param   int     $flags      How to handle the quotes
     * @param   string  $enc        The charset encoding
     * @param   boolean $dbl_enc    If we should convert everything
     * @return  mixed               Returns the escaped input
     */
    public function escape($input, $flags = ENT_COMPAT, $enc = 'UTF-8', $dbl_enc = true)
    {
        // Escape a string
        if(is_string($input))
        {
            return htmlentities($input, $flags, $enc, $dbl_enc);
        }
        // Escape an array or an array of objects
        if(is_array($input))
        {
            $array = ['flags' => $flags, 'enc' => $enc, 'dbl_enc' => $dbl_enc];

            array_walk_recursive($input, function(&$item, $key) use ($array)
            {
                extract($array);

                if(is_string($item))
                {
                    $item = htmlentities($item, $flags, $enc, $dbl_enc);
                }

                if(is_object($item))
                {
                    foreach($item as &$value)
                    {
                        if(is_string($value))
                        {
                            $value = htmlentities($value, $flags, $enc, $dbl_enc);
                        }
                    }
                }
            });

            return $input;
        }
        // Escape an object
        if(is_object($input))
        {
            foreach($input as &$value)
            {
                if(is_string($value))
                {
                    $value = htmlentities($value, $flags, $enc, $dbl_enc);
                }
            }

            return $input;
        }
    }

    /**
     * Find a matching route for the current request, if none is found/allowed
     * we exit with the corresponding HTTP status code.
     *
     * @access  public
     * @return  void
     */
    public function run()
    {
        if( ! $this->request->methodAllowed())
        {
            $this->abort(405);
        }

        $request = preg_replace('/\/{2,}/', '/', '/' . preg_replace('#' . $this->base . '#', '', $this->request->uri, 1));

        $route = $this->router->find($request);

        if($route)
        {
            $method = $this->request->method;

            if($route->allowsMethod($method))
            {
                $response = $route->response($method);

                $response = $response->bindTo($this);

                $params = $route->parameters($request);

                $body = call_user_func_array($response, $params);

                if($body instanceof Response)
                {
                    $this->response = $body;
                }
                else
                {
                    $this->response->body($body);
                }

                $this->response->send($method, $this->request->protocol());

                exit(0);
            }

            $this->abort(405);
        }

        $this->abort(404);
    }

    /**
     * Framework exception handler
     *
     * If an error function for the status code 500 has been defined it will
     * be called with the exception as a parameter instead of outputting
     * information about the exception directly to the browser.
     *
     * @access  public
     * @param   Exception   $e  The exception
     * @return  void
     */
    public function exceptionHandler(Exception $e)
    {
        if(ob_get_contents())
        {
            ob_end_clean();
        }

        if(isset($this->errors[500]))
        {
            $this->abort(500, $e);
        }

        echo sprintf('<pre>Message: %s<br>File: %s<br>Line: %s<br>Trace: %s</pre>',
            $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());

        exit(1);
    }

    /*******************
     * HELPER METHODS
     *******************/


    /**
     * Add a flash message to the template
     *
     * @access  public
     * @param   string  $message    The message to be flashed
     * @param   string  $category   The message category
     * @return  void
     */
    public function flash($message, $category = null)
    {
        if($category)
        {
            $this->template->merge(['flash' => [$category => $message]]);
            return $this;
        }

        $this->template->merge(['flash' => [$message]]);
        return $this;
    }

    /**
     * Define a template variable
     *
     * @access  public
     * @param   string  $key    The template variable name
     * @param   mixed   $value  The template variable value
     * @return  void
     */
    public function set($key, $value)
    {
        $this->template->set($key, $value);
        return $this;
    }

    /**
     * Create a rendered template with the defined template variables
     *
     * @access  public
     * @param   string  $template   The template file path
     * @param   string  $layout     The layout file path
     * @param   array   $vars       Array of template variables
     * @return  object              Returns the template object
     */
    public function render($template, $layout = null, array $vars = [])
    {
        $this->template->setTemplate($template);
        
        if($layout)
        {
            $this->template->setLayout($layout);
        }

        $that = $this;

        $urlFor = function($location, $absolute = false, $params = []) use ($that)
        {
            return $that->urlFor($location, $absolute, $params);
        };

        $urlFor->bindTo($that);
        
        $this->template->set('urlFor', $urlFor);
        $this->template->merge($vars);

        return $this->template;
    }

    /**
     * Set a response header
     *
     * @access  public
     * @param   string  $key    Header attribute
     * @param   string  $value  Header attribute value
     * @return  Response        Returns itself, for method chaining
     */
    public function header($key, $value)
    {
        return $this->response->header($key, $value);
    }

    /**
     * Set the response status code
     *
     * @access  public
     * @param   int     $status The status code
     * @return  Response        Returns itself, for method chaining
     */
    public function status($code)
    {
        return $this->response->status($code);
    }

    /**
     * Shorthand method for setting the Last-Modified header
     *
     * @access  public
     * @param   string|int  $time   The time since it was last modified
     * @return  void
     */
    public function lastModified($time)
    {
        $this->header('Last-Modified', date(DATE_RFC1123, $time) . ' GMT');

        if($this->request->server('HTTP_IF_MODIFIED_SINCE'))
        {
            if(strtotime($this->request->server('HTTP_IF_MODIFIED_SINCE')) === $time)
            {
                $this->abort(304);
            }
        }
    }

    /**
     * Shorthand metod for setting the ETag header
     *
     * @access  public
     * @param   string  $identifier ETag identifier
     * @return  void
     */
    public function etag($identifier)
    {
        if($this->request->server('HTTP_IF_NONE_MATCH'))
        {
            $this->header('ETag', sprintf('"%s"', $identifier));
        }
    }

    /**
     * Shorthand method for setting the Expires header
     *
     * @access  public
     * @param   string|int  $time   The time until the response expires
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
     * @access  public
     * @return  void
     */
    public function noCache()
    {
        $this->header('Cache-Control', 'no-store, no-cache, max-age=0, must-revalidate');
        $this->header('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
        $this->header('Pragma', 'no-cache');
    }

    public function offsetSet($key, $response)
    {
        if(is_int($key) && array_key_exists($key, $this->response->httpStatusCodes))
        {
            $this->errors[$key] = $response;
        }
        else
        {
            $this->router->add(trim($key), $response);
        }
    }

    public function offsetGet($key) {}
    public function offsetExists($key) {}
    public function offsetUnset($key) {}

}
