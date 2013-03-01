<?php
/**
 * Cloudlib
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2012 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace cloudlib\core;

use Closure;
use Exception;
use ErrorException;
use cloudlib\core\ClassLoader;
use cloudlib\core\Container;
use cloudlib\core\Request;
use cloudlib\core\Response;
use cloudlib\core\Router;
use cloudlib\core\Template;

require 'ClassLoader.php';
require 'Container.php';

/**
 * The core framework class, which takes use of the other available classes
 *
 * @copyright   Copyright (c) 2012 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Cloudlib extends Container
{
    /**
     * Array of custom defined error handlers
     *
     * @access  public
     * @var     array
     */
    public $errors = array();

    /**
     * Array of before/after filters that are run before/after each (found)
     * request
     *
     * @access  public
     * @var     array
     */
    public $filters = array();

    /**
     * Array of request variables (based on the request method, ex POST would
     * put $_POST in this array)
     *
     * @access  public
     * @var     array
     */
    public $args = array();

    /**
     * Application base path for requests
     *
     * @access  public
     * @var     string
     */
    public $base = '/';

    /**
     * Defines the error handlers
     * Initializes the ClassLoader, Request, Response, Template objects
     * Defines the $base uri path
     *
     * @access  public
     * @return  void
     */
    public function __construct()
    {
        $self = $this;

        set_exception_handler(function(Exception $e) use ($self)
        {
            $self->exceptionHandler($e);
        });

        set_error_handler(function($code, $str, $file, $line) use ($self)
        {
            $self->exceptionHandler(new ErrorException($str, $code, 0, $file, $line));
        });

        register_shutdown_function(function() use ($self)
        {
            if(($e = error_get_last()) !== null)
            {
                extract($e);
                $self->exceptionHandler(new ErrorException($message, $type, 0, $file, $line));
            }
        });

        $this->loader = $this->instance(function()
        {
            return new ClassLoader();
        });

        $this->loader->registerNamespaces(array(
            'cloudlib\\core' => dirname(dirname(__DIR__))
        ));

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

        $this->args = $this->request->arguments;

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
     * Adds multiple routes (or a single one)
     *
     * @access  public
     * @param   string  $route      The route uri
     * @param   array   $methods    Array of request methods
     * @param   mixed   $response   The route response
     * @return  void
     */
    public function route($route, array $methods, $response)
    {
        $this->router->route($route, $methods, $response);
    }

    /**
     * Shorthand function for adding a route which allows the GET method
     *
     * @access  public
     * @param   string  $route      The route uri
     * @param   mixed   $response   The route response
     * @return  object              The newly added route
     */
    public function get($route, $response)
    {
        return $this->router->get($route, $response);
    }

    /**
     * Shorthand function for adding a route which allows the POST method
     *
     * @access  public
     * @param   string  $route      The route uri
     * @param   mixed   $response   The route response
     * @return  object              The newly added route
     */
    public function post($route, $response)
    {
        return $this->router->post($route, $response);
    }

    /**
     * Shorthand function for adding a route which allows the PUT method
     *
     * @access  public
     * @param   string  $route      The route uri
     * @param   mixed   $response   The route response
     * @return  object              The newly added route
     */
    public function put($route, $response)
    {
        return $this->router->put($route, $response);
    }

    /**
     * Shorthand function for adding a route which allows the DELETE method
     *
     * @access  public
     * @param   string  $route      The route uri
     * @param   mixed   $response   The route response
     * @return  object              The newly added route
     */
    public function delete($route, $response)
    {
        return $this->router->delete($route, $response);
    }

    /**
     * Add a filter that will be run before a successful request
     *
     * @access  public
     * @param   Closure $callback   The filter function to be executed
     * @return  void
     */
    public function before(Closure $callback)
    {
        $this->filters['before'] = $callback;
    }

    /**
     * Add a filter that will be run after a successful request
     *
     * @access  public
     * @param   Closure $callback   The filter function to be executed
     * @return  void
     */
    public function after(Closure $callback)
    {
        $this->filters['after'] = $callback;
    }

    /**
     * Add a $callback that will be called at shutdown (ex. closing of a database connection)
     *
     * @access  public
     * @param   Closure $callback   The callback to be executed
     * @return  void
     */
    public function teardown(Closure $callback)
    {
        register_shutdown_function($callback);
    }

    /**
     * Try calling a filter based on $key
     *
     * @access  public
     * @param   string  $key    The filter key identifier
     * @return  void
     */
    public function callFilter($key)
    {
        if(isset($this->filters[$key]))
        {
            $this->filters[$key]();
        }
    }

    /**
     * Add a before/after filter callback to a route
     *
     * @access  public
     * @param   string  $route      The route uri
     * @param   string  $method     Allowed method for the route
     * @param   string  $filter     The filter key identifier (before/after)
     * @param   Closure $callback   The filter callback
     * @return void
     */
    public function filter($route, $method, $filter, Closure $callback)
    {
        $this->router->routes[sprintf('%s %s', $method, $route)]->filters[$filter] = $callback;
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
     * Shorthand method for setting the Cache-Control header
     *
     * @access  public
     * @param   string  $directive  The Cache-Control directive
     * @return  void
     */
    public function cacheControl($directive)
    {
        $this->header('Cache-Control', $directive);
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

    /**
     * Define a custom error handler based on a HTTP status code
     *
     * @access  public
     * @param   int     $code       The HTTP status code
     * @param   Closure $response   The error handler
     * @return  void
     */
    public function error($code, Closure $response)
    {
        $this->errors[$code] = $response;
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
    public function urlFor($location, $absolute = false, $parameters = array())
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
    public function redirect($location, $status = 302, $parameters = array())
    {
        if($parameters)
        {
            $location .= sprintf('?%s', http_build_query($parameters));
        }

        if( ! filter_var($location, FILTER_VALIDATE_URL))
        {
            $location = $this->urlFor($location, true);
        }

        $response = new Response('', $status, array('Location' => $location));

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
    public function abort($code, $message = null, array $headers = array())
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
                $parameter = array(
                    'message' => $message,
                    'statusCode' => $code,
                    'statusMessage' => $response->httpStatusCodes[$code]
                );
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
            $array = array('flags' => $flags, 'enc' => $enc, 'dbl_enc' => $dbl_enc);

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
            $this->template->merge(array(
                'flash' => array($category => $message)
            ));
        }
        else
        {
            $this->template->merge(array('flash' => array($message)));
        }
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
    public function render($template, $layout = null, array $vars = array())
    {
        $this->template->setTemplate($template);
        
        if($layout)
        {
            $this->template->setLayout($layout);
        }

        $that = $this;
        
        $this->template->set('urlFor',
            function($locaction, $absolute = false, $params = array()) use ($that)
            {
                return $that->urlFor($location, $absolute, $params);
            }
        );

        $this->template->merge($vars);

        return $this->template;
    }

    /**
     * Find a matching route for the current request, if none is found/allowed
     * we exit with the corresponding HTTP status code.
     *
     * Before/After filters are also run before/after the request
     *
     * @access  public
     * @return  void
     */
    public function run()
    {
        // Check if the request method is allowed (GET, POST, PUT, DELETE, HEAD)
        if( ! $this->request->methodAllowed())
        {
            $this->abort(405);
        }

        if($this->router->routeExists($this->base, $this->request->uri))
        {
            if($this->router->routeHasMethod($this->request->method))
            {
                $this->callFilter('before');

                $route = $this->router->route;

                $routeResponse = $route->getResponse($this);

                if($routeResponse instanceof Reponse)
                {
                    $this->response = $routeResponse;
                }
                else
                {
                    $this->response->body($routeResponse);
                }

                $route->callFilter('before');
                $this->response->send($this->request->method, $this->request->protocol());
                $route->callFilter('after');

                $this->callFilter('after');

                exit(0);
            }
            else
            {
                $this->abort(405);
            }
        }
        else
        {
            $this->abort(404);
        }
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
}
