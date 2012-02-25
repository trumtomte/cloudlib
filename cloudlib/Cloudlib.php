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
     * Constructor.
     *
     * Sets the root directory, base uri.
     * It has the option of turning of the default start, if the user wants to define
     * the paths instead
     *
     * @access  public
     * @param   string  $root
     * @param   string  $baseUri
     * @param   boolean $autoStart
     * @return  void
     */
    public function __construct($root, $baseUri = '/', $autoStart = true)
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
            'logs'        => $app . 'logs' . $ds,
            'config'      => $app . 'config.php',
            'uploader'    => $pub . 'img' . $ds,
            'image'       => $pub . 'img' . $ds,
            'css'         => $relPub . 'css' . $ds,
            'js'          => $relPub . 'js' . $ds,
            'img'         => $relPub . 'img' . $ds,
            'classes'     => $root . $ds . 'Cloudlib' . $ds
        );

        if($autoStart)
        {
            $this->start();
        }
    }

    /**
     * Start the application
     *
     * @access  public
     * @return  void
     */
    public function start()
    {
        $this->registerClassLoader();
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
        Uploader::setPath(static::$paths['uploader']);
        Image::setPath(static::$paths['image']);

        // Load the config
        Config::load(static::$paths['config']);

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
        $this->router->route($route, array('GET'), $response);
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
        $this->router->route($route, array('POST'), $response);
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
        $this->router->route($route, array('PUT'), $response);
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
        $this->router->route($route, array('DELETE'), $response);
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
            array_merge($this->data, $data);
        }
        return new View($view, $layout, $this->data);
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
     * Register an autoloader
     *
     * @access  protected
     * @return  void
     */
    protected function registerClassLoader()
    {
        spl_autoload_register(array($this, 'classLoader'));
    }

    /**
     * The class autoloader
     *
     * @access  protected
     * @param   string  $class
     * @return  void
     */
    protected function classLoader($class)
    {
        switch(true)
        {
            case preg_match('/Controller$/', $class) && ! preg_match('/^Controller$/', $class):
                $directory = static::$paths['controllers'];
                break;
            case preg_match('/Model$/', $class) && ! preg_match('/^Model$/', $class):
                $directory = static::$paths['models'];
                break;
            default:
                $directory = static::$paths['classes'];
                break;
        }

        if( ! file_exists($file = $directory . $class . '.php'))
        {
            throw new RuntimeException(sprintf('Unable to load class [%s] from [%s]',
                $class, $file));
        }

        require $file;
    }

    /**
     * Function that handles all errors and exceptions
     *
     * @access  protected
     * @return  void
     */
    protected function setErrorHandling()
    {
        error_reporting(-1);
        ini_set('display_errors', 1);
        ini_set('log_errors', 1);
        ini_set('error_log', static::$paths['logs'] . 'error_php.log');

        $that = $this;

        //TODO: use($this) in PHP 5.4
        set_exception_handler(function(Exception $e) use ($that)
        {
            $that->exceptionHandler($e);
        });

        set_error_handler(function($code, $str, $file, $line) use ($that)
        {
            $that->exceptionHandler(new ErrorException($str, $code, 0, $file, $lime));
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
