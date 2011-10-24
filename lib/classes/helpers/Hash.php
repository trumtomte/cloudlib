<?php
/**
 * CloudLib :: Lightweight MVC PHP Framework
 *
 * @author      Sebastian Book <sebbebook@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     cloudlib
 */

/**
 * The security class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes.helpers
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Hash extends Factory
{
    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Bcrypt
     *
     * (Function taken and modified from http://www.phpportalen.net)
     *
     * @access  public
     * @param   string  $password
     * @param   string  $salt
     * @param   int     $rounds
     * @return  string
     */
    public static function bcrypt($password, $salt, $rounds = 6)
    {
        if($rounds < 4 or $rounds > 31)
        {
            $rounds = 6;
        }

        $prefix = sprintf('$2a$%02d$', $rounds);

        $salt .= config::general('salt');

        if(!preg_match('#^[A-Za-z0-9./]{22}$#', $salt))
        {
            $new = base64_encode($salt);

            if(strlen($new) < 22)
            {
                $new .= base64_encode(md5($salt));

                $salt = substr($new, 0, 22);

                $salt = str_replace(array('+', '-'), '.', $salt);

                $salt = str_replace(array('=', '_'), '/', $salt);
            }
        }

        $crypt = crypt($password, $prefix . $salt);

        return substr($crypt, 7);
    }
}
