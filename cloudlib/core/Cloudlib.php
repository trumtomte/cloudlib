<?php
/**
 * Cloudlib
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
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
use cloudlib\core\View;

require 'ClassLoader.php';
require 'Container.php';

/**
 * The core framework class
 *
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
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
     * Array of before/after filters that are run before/after each (found) request
     *
     * @access  public
     * @var     array
     */
    public $filters = array();

    /**
     * Array of request variables (based on the request method, ex POST would put $_POST in this array)
     *
     * @access  public
     * @var     array
     */
    public $input = array();

    /**
     * Array of view variables to be used in views
     *
     * @access  public
     * @var     array
     */
    public $data = array();

    /**
     * At object creation define the base uri
     *
     * Set the error/exception handlers
     *
     * Add object instances (ClassLoader, Request, Router)
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
            if( ! ($e = error_get_last()) === null)
            {
                extract($e);
                $self->exceptionHandler(new ErrorException($message, $type, 0, $file, $line));
            }
        });

        $this->addInstance('loader', function()
        {
            return new ClassLoader();
        });

        $this->loader->registerNamespaces(array(
            'cloudlib\\core' => dirname(dirname(__DIR__))
        ));

        $this->loader->register();

        $this->addInstance('request', function()
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

        $this->input = $this->request->input;

        $this->addInstance('router', function()
        {
            return new Router();
        });
    }

    /**
     * Add a new route (with the possibility of multiple request methods)
     *
     * @access  public
     * @param   string  $route      The route pattern (ex '/home')
     * @param   array   $methods    Array of request methods
     * @param   mixed   $response   The response to be returned when the route is found
     * @return  void
     */
    public function route($route, array $methods, $response)
    {
        $this->router->route($route, $methods, $response);
    }

    /**
     * Shorthand function to route(), define a GET route
     *
     * @access  public
     * @param   string  $route      The route pattern
     * @param   mixed   $response   The response to be returned when the route is found
     * @return  void
     */
    public function get($route, $response)
    {
        $this->router->get($route, $response);
    }

    /**
     * Shorthand function to route(), define a POST route
     *
     * @access  public
     * @param   string  $route      The route pattern
     * @param   mixed   $response   The response to be returned when the route is found
     * @return  void
     */
    public function post($route, $response)
    {
        $this->router->post($route, $response);
    }

    /**
     * Shorthand function to route(), define a PUT route
     *
     * @access  public
     * @param   string  $route      The route pattern
     * @param   mixed   $response   The response to be returned when the route is found
     * @return  void
     */
    public function put($route, $response)
    {
        $this->router->put($route, $response);
    }

    /**
     * Shorthand function to route(), define a DELETE route
     *
     * @access  public
     * @param   string  $route      The route pattern
     * @param   mixed   $response   The response to be returned when the route is found
     * @return  void
     */
    public function delete($route, $response)
    {
        $this->router->delete($route, $response);
    }

    /**
     * Add a filter that will be run before a successful request
     *
     * If multiple ones are defined they will be executed successively
     *
     * @access  public
     * @param   Closure $function   The filter function to be executed
     * @return  void
     */
    public function before(Closure $function)
    {
        $this->filters['before'][] = $function;
    }

    /**
     * Add a filter that will be run after a successful request
     *
     * If multiple ones are defined they will be executed successively
     *
     * @access  public
     * @param   Closure $function   The filter function to be executed
     * @return  void
     */
    public function after(Closure $function)
    {
        $this->filters['after'][] = $function;
    }

    /**
     * Define a custom error handler based on a http status code
     *
     * @access  public
     * @param   int     $code       The http status code
     * @param   Closure $response   The error handler
     * @return  void
     */
    public function error($code, Closure $response)
    {
        $this->errors[$code] = $response;
    }

    /**
     * Returns an url relative to the application (absolute if $absolute is true)
     *
     * @access  public
     * @param   string  $location   The URL end point
     * @param   boolean $absolute   If we should return an absolute URL
     * @return  string              The complete URL (relative or absolute)
     */
    public function urlFor($location, $absolute = false)
    {
        if($absolute)
        {
            $protocol = $this->request->isSecure() ? 'https' : 'http';

            return sprintf('%s://%s%s', $protocol,
                $this->request->server('HTTP_HOST'),
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
     * @return  void
     */
    public function redirect($location, $status = 302)
    {
        if( ! filter_var($location, FILTER_VALIDATE_URL))
        {
            $location = $this->urlFor($location, true);
        }

        $response = new Response('', $status, array('Location' => $location));

        $response->send($this->request->method, $this->request->protocol());

        exit(0);
    }

    /**
     * Create a new response that will end the current process of the application (404, 405, 500 etc)
     *
     * @access  public
     * @param   int     $code       The status code
     * @param   mixed   $data       Data that will be passed to the response function
     * @param   array   $headers    Array of HTTP headers to be sent
     * @return  void
     */
    public function abort($code, $data = null, array $headers = array())
    {
        $response = new Response('', $code, $headers);

        if( ! isset($this->errors[$code]))
        {
            $body = ($data) ? $data : sprintf('%s: %s', $code, $response->codes[$code]);
        }
        else
        {
            $param = array(
                'data' => $data,
                'statusCode' => $code,
                'statusMessage' => $response->codes[$code]
            );

            $body = $this->errors[$code]($param);
        }

        $response->body($body);
        $response->send($this->request->method, $this->request->protocol());

        exit(0);
    }

    /**
     * Define a view variable
     *
     * @access  public
     * @param   string  $key    The view variable name
     * @param   mixed   $value  The view variable value
     * @return  void
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Create a rendered View template with the defined view variables
     *
     * @access  public
     * @param   string  $view   The view filename
     * @param   string  $layout The layout filename
     * @param   array   $data   Array of view variables
     * @return  View            Returns a rendered view with view variables
     */
    public function render($view, $layout = null, array $data = array())
    {
        $data = (isset($data)) ? array_merge($this->data, $data) : $data;

        return new View($view, $layout, $data);
    }

    /**
     * Match a route to the request and send it to the browser.
     *
     * Run before/after filters.
     *
     * If the request method is not allowed abort with a 405 http status
     *
     * If no route was matched exit with the appropriate http status
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

        // Parse through the array of routes
        if($this->router->parse($this))
        {
            // Run before filters, if any was defined
            if(isset($this->filters['before']))
            {
                foreach($this->filters['before'] as $beforeFilter)
                {
                    $beforeFilter();
                }
            }

            // Create the response object
            $response = ($this->router->response instanceof Response) ? $this->router->response : new Response($this->router->response);
            // Output to browser
            $response->send($this->request->method, $this->request->protocol());

            // Run after filters, if any was defined
            if(isset($this->filters['after']))
            {
                foreach($this->filters['after'] as $afterFilter)
                {
                    $afterFilter();
                }
            }

            // Exit successfully
            exit(0);
        }
        // No route was found
        else
        {
            $this->abort($this->router->response);
        }
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
     * Framework exception handler
     *
     * If an error function for the status code 500 has been defined it will be called
     * instead of outputting information about the exception directly to the browser
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

        echo sprintf('<pre>Message: %s</pre><pre>File: %s</pre><pre>Line: %s</pre><pre>Trace: %s</pre>',
            $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());

        exit(1);
    }
}
