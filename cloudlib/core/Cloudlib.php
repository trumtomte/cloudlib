<?php
/**
 * Cloudlib
 *
 * @author      Sebastian Bengtegård <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2013 Sebastian Bengtegård <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace cloudlib\core;

use ArrayAccess;
use Exception;
use ErrorException;
use InvalidArgumentException;
use ArrayObject;

use cloudlib\core\ClassLoader;
use cloudlib\core\Request;
use cloudlib\core\Response;
use cloudlib\core\Router;

require_once 'ClassLoader.php';

/**
 * The core framework class, which takes use of the other available classes
 *
 * @copyright   Copyright (c) 2013 Sebastian Bengtegård <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Cloudlib implements ArrayAccess
{
    /**
     * Array of closures used to lazyload classes
     *
     * @var array
     */
    public $vars = [];

    /**
     * Array of custom defined error handlers
     *
     * @var array
     */
    public $errors = [];

    /**
     * Initializes the application, defines error handlers and the core classes.
     *
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

        $this->loader = new ClassLoader();
        $this->loader->registerNamespaces(['cloudlib\\core' => dirname(dirname(__DIR__))]);
        $this->loader->register();

        $this->request = new Request($_SERVER, $_GET, $_POST, $_FILES, $_COOKIE);
        $this->response = new Response();
        $this->router = new Router();

        $this->response->prepare($this->request);
    }

    /**
     * Add a class ($key) for lazy loading
     *
     * @param   string      $key    The class name
     * @param   callable    $callback   The closure
     * @return  void
     */
    public function lazy($key, callable $callback)
    {
        $callback = $callback->bindTo($this, $this);

        $this->vars[$key] = function($app) use ($callback)
        {
            static $instance = null;

            if($instance === null)
            {
                $instance = $callback($app);
            }

            return $instance;
        };
    }

    /**
     * Add a $callback that will be called at shutdown
     *
     * @param   callable    $callback   The callback to be executed
     * @return  void
     */
    public function teardown(callable $callback)
    {
        $callback = $callback->bindTo($this, $this);
        register_shutdown_function($callback);
    }

    /**
     * Terminate the current response
     *
     * @param   int     $status     The status code
     * @param   mixed   $message    Abort message or exception
     * @param   array   $headers    Array of HTTP headers to be sent
     * @return  void
     */
    public function abort($status, $message = null, array $headers = [])
    {
        if(isset($this->errors[$status]))
        {
            if($message instanceof Exception)
            {
                $parameters = [$message];
            }
            else
            {
                $parameters = [
                    $message,
                    $status,
                    $this->response->httpStatusCodes[$status]
                ];
            }

            $func = $this->errors[$status]->bindTo($this, $this);

            $message = call_user_func_array($func, $parameters);
        }

        $this->response->abort($status, $message, $headers);
    }

    /**
     * Create complete URLs from route paths (ex. /home => http://host.com/home)
     *
     * @param   string  $location   URL path
     * @return  string              The complete URL
     */
    public function urlFor($location)
    {
        if( ! filter_var($location, FILTER_VALIDATE_URL))
        {
            $path = preg_replace('/\/{2,}/', '/', sprintf('%s/%s', $this->request->base, $location));

            if(strpos($location, '://') !== false)
            {
                $location = $path;
            }
            else
            {
                $protocol = $this->request->secure ? 'https' : 'http';
                $location = sprintf('%s://%s%s', $protocol, $this->request->host, $path);
            }
        }

        return $location;
    }

    /**
     * Make a response for the current request
     *
     * @return  void
     */
    public function listen()
    {
        if( ! in_array($this->request->method, ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD']))
        {
            $this->abort(405);
        }

        $path = preg_replace('/\/{2,}/', '/', '/' . preg_replace('#' . $this->request->base . '#', '', $this->request->path, 1));

        $routes = $this->router->findMatchingRoutes($path);

        // No matching route
        if( ! $routes)
        {
            $this->abort(404);
        }

        $foundRoute = false;

        foreach($routes as $route)
        {
            // HEAD is the same as GET but only outputs headers
            $method = $this->request->method == 'HEAD' ? 'GET' : $this->request->method;

            if($route->method == $method)
            {
                $foundRoute = $route;
            }
        }

        // Invalid HTTP method
        if( ! $foundRoute)
        {
            $this->abort(405);
        }

        $this->request->params = (object) $foundRoute->params;

        foreach($foundRoute->callbacks as $callback)
        {
            $callback = $callback->bindTo($this, $this);
            $callback($this->request, $this->response);
        }

        $this->response->respond();
    }

    /**
     * Framework exception handler,
     *
     * If a HTTP status handler for "Internal Server Error" (500) was defined it will be used.
     *
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

    /**
     * Enable calling property closures as class methods
     *
     * @access  public
     * @param   string  $key    Method name
     * @param   array   $args   Method arguments
     * @return  mixed
     */
    public function __call($key, $args)
    {
        if( ! property_exists($this, $key))
        {
            throw new InvalidArgumentException(sprintf('Property [%s] does not exist', $key));
        }

        $closure = $this->$key->bindTo($this, $this);
        return call_user_func_array($closure, $args);
    }

    /**
     * Enable access to lazy loadable classes
     *
     * @param   string  $key    The class name
     * @return  mixed           The lazy loaded class
     */
    public function __get($key)
    {
        if( ! array_key_exists($key, $this->vars))
        {
            throw new InvalidArgumentException(sprintf('Key [%s] does not exist', $key));
        }

        return is_callable($this->vars[$key]) ? $this->vars[$key]($this) : $this->vars[$key];
    }

    /**
     * ArrayAccess method used to set error/route handlers
     *
     * @access  public
     * @param   mixed   $key        The route/error handler identifier
     * @param   mixed   $callback   The route/error handler callback(s)
     * @return  void
     */
    public function offsetSet($key, $callback)
    {
        if(is_int($key))
        {
            $this->errors[$key] = $callback;
        }

        if(is_string($key))
        {
            $this->router->add($key, $callback);
        }
    }

    /**
     * Unused ArrayAccess methods
     */
    public function offsetGet($key) {}
    public function offsetExists($key) {}
    public function offsetUnset($key) {}

}
