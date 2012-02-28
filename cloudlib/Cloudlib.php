<?php
/**
 * CloudLib :: Lightweight RESTful MVC PHP Framework
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     Cloudlib
 */

namespace cloudlib;

// SPL
use Exception,
    ErrorException,
    Closure;

// Cloudlib
use cloudlib\View,
    cloudlib\Html,
    cloudlib\Uploader,
    cloudlib\Image,
    cloudlib\Session,
    cloudlib\Config,
    cloudlib\Request,
    cloudlib\Response,
    cloudlib\Router,
    cloudlib\ClassLoader;

require 'ClassLoader.php';

/**
 * Cloudlib
 *
 * <short description>
 *
 * @package     Cloudlib
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Cloudlib
{
    /**
     * The Request object
     *
     * @access  public
     * @var     object
     */
    public $request;

    /**
     * The Response object
     *
     * @access  public
     * @var     object
     */
    public $response;

    /**
     * The Router object
     *
     * @access  protected
     * @var     object
     */
    protected $router;

    /**
     * CLoudlib class vars for use in different routes
     *
     * @access  protected
     * @var     array
     */
    protected $vars = array();

    /**
     * Stored data for Views
     *
     * @access  public
     * @var     array
     */
    public $data = array();

    /**
     * Input data from Globals
     *
     * @access  public
     * @var     array
     */
    public $input;

    /**
     * Array of stored errors
     *
     * @access  protected
     * @var     array
     */
    protected $errors =  array();

    /**
     * Absolute and Relative paths for directories and classes
     *
     * @access  public
     * @var     array
     */
    public static $paths = array();

    /**
     * The root directory
     *
     * @access  public
     * @var     string
     */
    public static $root;

    /**
     * The base uri
     *
     * @access  public
     * @var     string
     */
    public static $baseUri;

    /**
     * Array of options for Cloudlib
     *
     * @access  public
     * @var     array
     */
    public static $options = array(
        'bootstrap' => true,
        'autoloader' => true
    );

    /**
     * Constructor.
     *
     * Sets the root directory, base uri.
     * It has the option of turning of the default start, if the user wants to define
     * the paths instead
     *
     * @access  public
     * @param   string  $root
     * @param   string  $baseUri
     * @param   array   $options
     * @return  void
     */
    public function __construct($root, $baseUri = '/', array $options = array())
    {
        static::$root = $root;
        static::$baseUri = $baseUri;

        // Define toplevel paths
        $ds = DIRECTORY_SEPARATOR;
        $app = $root . $ds . 'application' . $ds;
        $pub = $root . $ds . 'public' . $ds;
        $relPub = $baseUri . $ds . 'public' . $ds;

        // Define specific sublevel paths
        static::$paths = array(
            'controllers' => $app . 'controllers' . $ds,
            'models'      => $app . 'models' . $ds,
            'views'       => $app . 'views' . $ds,
            'layouts'     => $app . 'views' . $ds . 'layouts' . $ds,
            'logs'        => $app . 'logs' . $ds . 'error_php.log',
            'config'      => $app . 'config.php',
            'uploader'    => $pub . 'img' . $ds,
            'image'       => $pub . 'img' . $ds,
            'css'         => $relPub . 'css' . $ds,
            'js'          => $relPub . 'js' . $ds,
            'img'         => $relPub . 'img' . $ds,
            'classes'     => $root . $ds . 'Cloudlib' . $ds
        );

        static::setOptions($options);

        if(static::$options['bootstrap'])
        {
            $this->bootstrap();
        }
    }

    /**
     * Bootstrap the application
     *
     * @access  public
     * @return  void
     */
    public function bootstrap()
    {
        $loader = new ClassLoader(array(
            'cloudlib', static::$root . DIRECTORY_SEPARATOR . 'cloudlib'
        ), array(
            'Benchmark'     => 'cloudlib\\Benchmark',
            'ClassLoader'   => 'cloudlib\\ClassLoader',
            'Cloudlib'      => 'cloudlib\\Cloudlib',
            'Config'        => 'cloudlib\\Config',
            'Controller'    => 'cloudlib\\Controller',
            'Database'      => 'cloudlib\\Database',
            'Form'          => 'cloudlib\\Form',
            'Hash'          => 'cloudlib\\Hash',
            'Html'          => 'cloudlib\\Html',
            'Image'         => 'cloudlib\\Image',
            'Logger'        => 'cloudlib\\Logger',
            'Model'         => 'cloudlib\\Model',
            'Number'        => 'cloudlib\\Number',
            'Request'       => 'cloudlib\\Request',
            'Response'      => 'cloudlib\\Response',
            'Router'        => 'cloudlib\\Router',
            'Session'       => 'cloudlib\\Session',
            'String'        => 'cloudlib\\String',
            'Uploader'      => 'cloudlib\\Uploader',
            'View'          => 'cloudlib\\View'
        ), array(
            'controllers' => static::$paths['controllers'],
            'models'      => static::$paths['models'],
            'logs'        => static::$paths['logs']
        ));

        if(static::$options['autoloader'])
        {
            $loader->register();
        }
        else
        {
            spl_autoload_register(array($loader, 'loadControllerModel'), true, true);
        }

        // Load the config
        Config::load(static::$paths['config']);

        $this->setErrorHandling();

        // Define directory paths for the classes
        View::setPaths(array(
            'views'     => static::$paths['views'],
            'layouts'   => static::$paths['layouts']
        ));
        Html::setPaths(array(
            'base'  => static::$baseUri,
            'css'   => static::$paths['css'],
            'img'   => static::$paths['img'],
            'js'    => static::$paths['js']
        ));
        Uploader::setPaths(array(
            'uploadDirectory' => static::$paths['uploader']
        ));
        Image::setPaths(array(
            'imageDirectory' => static::$paths['image']
        ));

        $this->request = new Request($_SERVER, $_GET, $_POST, $_FILES, $_COOKIE);
        $this->response = new Response($this->request);
        $this->router = new Router($this->request, static::$baseUri);

        $this->input = $this->request->input;

        date_default_timezone_set(Config::get('app.timezone'));
        mb_internal_encoding(Config::get('app.encoding'));

        Session::start();

        if( ! Session::has('token'))
        {
            Session::generateToken('token');
        }
    }

    /**
     * Define directory paths
     *
     * @access  public
     * @param   array   $paths
     * @return  void
     */
    public function setPaths(array $paths)
    {
        foreach($paths as $key => $value)
        {
            static::$paths[$key] = $value;
        }
    }

    /**
     * Get the directory pahts
     *
     * @access  public
     * @return  array
     */
    public function getPaths()
    {
        return static::$paths;
    }

    /**
     * Define a directory path
     *
     * @access  public
     * @param   string  $key
     * @param   string  $value
     * @return  void
     */
    public function setPath($key, $value)
    {
        static::$path[$key] = $value;
    }

    /**
     * Get a directory path by name
     *
     * @access  public
     * @param   string  $name
     * @return  string
     */
    public function getPath($name)
    {
        return static::$paths[$name];
    }

    /**
     * Add a Route
     *
     * @access  public
     * @param   string  $route
     * @param   array   $methods
     * @param   mixed   $response
     * @return  void
     */
    public function route($route, array $methods, $response)
    {
        $this->router->route($route, $methods, $response);
    }

    /**
     * Shorthand function to add a GET route
     *
     * @access  public
     * @param   string  $route
     * @param   mixed   $response
     * @return  void
     */
    public function get($route, $response)
    {
        $this->router->get($route, $response);
    }

    /**
     * Shorthand function to add a POST route
     *
     * @access  public
     * @param   string  $route
     * @param   mixed   $response
     * @return  void
     */
    public function post($route, $response)
    {
        $this->router->post($route, $response);
    }

    /**
     * Shorthand function to add a PUT route
     *
     * @access  public
     * @param   string  $route
     * @param   mixed   $response
     * @return  void
     */
    public function put($route, $response)
    {
        $this->router->put($route, $response);
    }

    /**
     * Shorthand function to add a DELETE route
     *
     * @access  public
     * @param   string  $route
     * @param   mixed   $response
     * @return  void
     */
    public function delete($route, $response)
    {
        $this->router->delete($route, $response);
    }

    /**
     * Redirect to a new location
     *
     * @access  public
     * @param   string  $location
     * @param   int     $status
     * @return  void
     */
    public function redirect($location, $status = 302)
    {
        $this->response->redirect(static::$baseUri . $location, $status);
    }

    /**
     * Add an Error response
     *
     * @access  public
     * @param   int     $error
     * @param   closure $response
     * @return  void
     */
    public function error($error, $response)
    {
        $this->errors[$error] = $response;
    }

    /**
     * Return an already set error page
     *
     * @access  public
     * @param   mixed     $error
     * @return  mixed
     */
    public function errorPage($error)
    {
        if( ! isset($this->errors[$error]))
        {
            return false;
        }
        if($this->errors[$error] instanceof Closure)
        {
            return $this->errors[$error]();
        }
        return $this->errors[$error];
    }

    /**
     * Set a variable used by the View
     *
     * @access  public
     * @param   mixed   $key
     * @param   mixed   $value
     * @return  object
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Render a view
     *
     * @access  public
     * @param   string  $view
     * @param   string  $layout
     * @return  object
     */
    public function render($view, $layout = null, array $data = array())
    {
        if(isset($data))
        {
            $data = array_merge($this->data, $data);
        }
        return new View($view, $layout, $data);
    }

    /**
     * Return a Model
     *
     * @access  public
     * @param   string  $model
     * @return  object
     */
    public function model($model)
    {
        $model .= 'Model';
        return new $model();
    }

    /**
     * Run the application
     *
     * @access  public
     * @return  void
     */
    public function run()
    {
        if($this->request->methodAllowed())
        {
            if($this->router->routeExists())
            {
                $this->response->body($this->router->getResponse());
                $this->response->status(200);
            }
            else
            {
                $this->response->error($this->router->getResponse(), $this->errors);
            }
        }
        else
        {
            $this->response->error(405, $this->errors);
        }

        $this->response->send();
    }

    /**
     * Function that handles all errors and exceptions
     *
     * @access  protected
     * @return  void
     */
    protected function setErrorHandling()
    {
        // Force error reporting
        error_reporting(-1);
        // Display the errors?
        ini_set('display_errors', Config::get('app.errors'));

        // Log the errors
        ini_set('log_errors', 1);
        ini_set('error_log', static::$paths['logs']);

        $that = $this;

        //TODO: use($this) in PHP 5.4
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
            if( ! ($e = error_get_last()) === null)
            {
                extract($e);
                $that->exceptionHandler(new ErrorException($message, $type, 0, $file, $line));
            }
        });
    }

    /**
     * Custom exception handler
     *
     * @access  public
     * @param   object  $e
     * @return  void
     */
    public function exceptionHandler(Exception $e)
    {
        if(ob_get_contents()) { ob_end_clean(); }

        if(isset($this->errors[500]))
        {
            $this->response->error(500, $this->errors, $e);
            $this->response->send();
            exit(1);
        }

        echo sprintf('<pre>Message: %s</pre><pre>File: %s, Line: %s</pre><pre>Trace: %s</pre>',
            $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());

        exit(1);
    }

    /**
     * Set the options array
     *
     * @access  public
     * @param   array   $options
     * @return  void
     */
    public static function setOptions(array $options)
    {
        foreach($options as $key => $value)
        {
            static::$options[$key] = $value;
        }
    }

    /**
     * Set a variable
     *
     * @access  public
     * @param   mixed  $key
     * @param   mixed   $value
     * @return  void
     */
    public function __set($key, $value)
    {
        $this->vars[$key] = $value;
    }

    /**
     * Get a variable
     *
     * @access  public
     * @param   string  $key
     * @return  mixed
     */
    public function __get($key)
    {
        return $this->vars[$key];
    }
}
