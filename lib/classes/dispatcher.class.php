<?php
/**
 * Cloudlib :: Minor PHP (M)VC Framework
 *
 * @author      Sebastian Book <sebbebook@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     cloudlib
 */

/**
 * The dispatcher class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
final class dispatcher extends master
{
    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Dispatch
     *
     * @access  public
     * @param   array   $route
     * @return  void
     */
    public static function dispatch(array $route)
    {
        if(empty($route['controller']))
        {
            $route['controller'] = 'index';
        }

        $file = CTRLS . $route['controller'] . 'Controller' . CLASS_EXT;

        if(!is_readable($file))
        {
            header('HTTP/1.0 404 Not Found');
            require LIB . 'error/404.php';
            exit(1);
        }

        require $file;

        $class = $route['controller'] . 'Controller';

        if(empty($route['action']))
        {
            $route['action'] = 'index';
        }
        elseif(!is_callable(array($class, $route['action'])))
        {
            $route['action'] = 'index';
        }

        $controller = new $class();
        $action     = $route['action'];
        $param      = $route['param'];

        $controller->$action($param);
    }
}
