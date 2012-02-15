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
        $app = $root . $ds . 'Application' . $ds;
        $pub = $root . $ds . 'Public' . $ds;
        $relPub = $baseUri . $ds . 'Public' . $ds;

        // Define specific sublevel paths
        static::$paths = array(
            'Controllers' => $app . 'controllers' . $ds,
            'Models'      => $app . 'models' . $ds,
            'Views'       => $app . 'views' . $ds,
            'Layouts'     => $app . 'views' . $ds . 'layouts' . $ds,
            'Logs'        => $app . 'logs' . $ds,
            'Config'      => $app . 'config.php',
            'Uploader'    => $pub . 'img' . $ds,
            'Image'       => $pub . 'img' . $ds,
            'CSS'         => $relPub . 'css' . $ds,
            'JS'          => $relPub . 'js' . $ds,
            'Img'         => $relPub . 'img' . $ds,
            'Classes'     => $root . $ds . 'Cloudlib' . $ds
        );

        if($autoStart)
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
        View::$paths = array(
            'views' => static::$paths['Views'],
            'layouts' => static::$paths['Layouts']
        );
        Html::$paths = array(
            'base' => static::$baseUri,
            'css' => static::$paths['CSS'],
            'img' => static::$paths['Img'],
            'js' => static::$paths['JS']
        );
        Uploader::$path = static::$paths['Uploader'];
        Image::$path = static::$paths['Image'];

        // Load the config
        Config::load(static::$paths['Config']);

        $this->request = new Request($_SERVER, $_GET, $_POST, $_FILES, $_COOKIE);
        $this->response = new Response($this->request);
        $this->router = new Router($this->request, static::$baseUri);

        $this->input = $this->request->input;

        date_default_timezone_set(Config::get('app.timezone'));
        mb_internal_encoding(Config::get('app.encoding'));

        Session::start();

        if(Session::has('csrf-token') == false)
        {
            Session::generateToken('csrf-token');
        }
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
     * @param   int     $error
     * @return  object
     */
    public function errorPage($error)
    {
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
                $directory = static::$paths['Controllers'];
                break;
            case preg_match('/Model$/', $class) && ! preg_match('/^Model$/', $class):
                $directory = static::$paths['Models'];
                break;
            default:
                $directory = static::$paths['Classes'];
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
        ini_set('error_log', static::$paths['Logs'] . 'error_php.log');

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

        //TODO: use($this) in PHP 5.4
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

    /**
     * Set a variable
     *
     * @access  public
     * @param   string  $index
     * @param   mixed   $value
     * @return  void
     */
    public function __set($index, $value)
    {
        $this->vars[$index] = $value;
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
        return $this->vars[$index];
    }
}
