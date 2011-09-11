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
 * The security class.
 *
 * <short description>
 *
 * @package     cloudlib
 * @subpackage  cloudlib.lib.classes
 * @copyright   Copyright (c) 2011 Sebastian Book <sebbebook@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
final class security extends master
{
    /**
     * The static salt from the general config file
     *
     * @access  private
     * @var     string
     */
    private static $salt;

    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct()
    {
        self::$salt = config::general('salt');
    }

    /**
     * Bcrypt
     *
     * @access  public
     * @param   string  $password
     * @param   string  $salt
     * @param   int     $rounds
     * @return  string
     */
    public static function encrypt($password, $salt, $rounds = 6)
    {
        if($rounds < 4)
        {
            $rounds = 4;
        }
        elseif($rounds > 31)
        {
            $rounds = 31;
        }

        $prefix = sprintf('$2a$%02d$', $rounds);

        $salt .= self::$salt;

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
