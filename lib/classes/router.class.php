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
 * The router class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
final class router
{
    /**
     * The controller
     *
     * @access  private
     * @var     string
     */
    private static $controller = null;

    /**
     * The controller method
     *
     * @access  private
     * @var     string
     */
    private static $action = null;

    /**
     * The method parameter
     *
     * @access  private
     * @var     string
     */
    private static $param = null;

    /**
     * Constructor
     *
     * @access  public
     * @param   string  $path
     * @return  void
     */
    public function __construct() {}

    /**
     * Gets the uri
     *
     * @access  public
     * @return  array
     */
    public static function uri()
    {
        $uri = empty($_GET['uri']) ? null : $_GET['uri'];

        if(empty($uri))
        {
            return array(
                'controller' => self::$controller,
                'action'     => self::$action,
                'param'      => self::$param
            );
        }

        $parts = explode('/', trim($uri, '/'));

        if(isset($parts[0]))
        {
            self::$controller = $parts[0];

            if(isset($parts[1]))
            {
                self::$action = $parts[1];

                if(isset($parts[2]))
                {
                    self::$param = $parts[2];
                }
            }
        }

        return array(
            'controller' => self::$controller,
            'action'     => self::$action,
            'param'      => self::$param
        );
    }
}
