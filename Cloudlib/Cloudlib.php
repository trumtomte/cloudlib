<?php if( ! defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }
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
     * It has the option of turing of the default start, if the user wants to define
     * the paths instead
     *
     * @access  public
     * @param   string  $root
     * @param   string  $baseUri
     * @param   boolean $default
     * @return  void
     */
    public function __construct($root, $baseUri = '/', $default = true)
    {
        static::$root = $root;
        static::$baseUri = $baseUri;
        
        static::$paths = array(
            'controllers' => static::$root . DS . 'Application' . DS . 'controllers' . DS,
            'models'      => static::$root . DS . 'Application' . DS . 'models' . DS,
            'views'       => static::$root . DS . 'Application' . DS . 'views' . DS,
            'layouts'     => static::$root . DS . 'Application' . DS . 'views' . DS . 'layouts' . DS,
            'classes'     => static::$root . DS . 'Cloudlib' . DS,
            'logs'        => static::$root . DS . 'Application' . DS . 'logs' . DS,
            'css'         => static::$baseUri . 'Public' . DS . 'css' . DS,
            'img'         => static::$baseUri . DS . 'Public' . DS . 'img' . DS,
            'js'          => static::$baseUri . DS . 'Public' . DS . 'js' . DS,
            'uploader'    => static::$root . DS . 'Public' . DS . 'img' . DS,
            'image'       => static::$root . DS . 'Public' . DS . 'img' . DS
        );

        if($default)
        {
            $this->start();
        }
    }

    /**
     * Define the paths
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
     * Start the application
     *
     * @access  public
     * @return  void
     */
    public function start()
    {
        $this->registerClassLoader();
        $this->setErrorHandling();

        View::$paths = array(
            'views' => static::$paths['views'],
            'layouts' => static::$paths['layouts']
        );

        Html::$paths = array(
            'base' => static::$baseUri,
            'css' => static::$paths['css'],
            'img' => static::$paths['img'],
            'js' => static::$paths['js']
        );

        Uploader::$path = static::$paths['uploader'];
        Image::$path = static::$paths['image'];

        Config::load(static::$root . DS . 'Application' . DS . 'config.php');

        $this->request = new Request($_SERVER, $_GET, $_POST, $_FILES, $_COOKIE);
        $this->response = new Response($this->request);
        $this->router = new Router($this->request, static::$baseUri);

        $this->input = $this->request->input;

        date_default_timezone_set(Config::get('app.timezone'));
        mb_internal_encoding(Config::get('app.encoding'));

        Session::start();
        Session::set('csrf-token', sha1(time() . uniqid(rand(), true)));
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
     * Add an Error
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
    public function render($view, $layout = null)
    {
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
        
        $exceptionHandler = function(Exception $e)
        {
            if(ob_get_contents()) { ob_end_clean(); }

            if($_SERVER['CLOUDLIB_ENV'] == 'production')
            {
                $response = new Response(new Request($_SERVER));
                $response->body(new View('errors/500', null, array('e' => $e)));
                $response->status(500);
                $response->send();
                exit(1);
            }

            echo sprintf('<pre>Message: %s</pre><pre>File: %s, Line: %s</pre><pre>Trace: %s</pre>',
                $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());

            exit(1);
        };

        set_exception_handler(function(Exception $e) use ($exceptionHandler)
        {
            $exceptionHandler($e);
        });

        set_error_handler(function($code, $str, $file, $line) use ($exceptionHandler)
        {
            $exceptionHandler(new ErrorException($str, $code, $code, $file, $line));
        });

        register_shutdown_function(function() use ($exceptionHandler)
        {
            if( ! ($e = error_get_last()) === null)
            {
                extract($e);
                $exceptionHandler(new ErrorException($message, $type, $type, $file, $line));
            }
        });
    }
}
