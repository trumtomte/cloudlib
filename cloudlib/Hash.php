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
use InvalidArgumentException;

/**
 * <class name>
 *
 * <short description>
 *
 * @package     Cloudlib
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Hash
{
    /**
     * Constructor
     *
     * @access  public
     * @return  void
     */
    public function __construct() {}

    /**
     * Create a bcrypt hash
     *
     * @acess   public
     * @param   string  $value
     * @param   string  $salt
     * @param   int     $rounds
     * @return  string
     */
    public static function create($value, $salt, $rounds = 8)
    {
        if($rounds > 31 or $rounds < 4)
        {
            throw new InvalidArgumentException('The number of rounds has to be between 4-31');
        }

        $salt = sprintf('$2a$%02d$%s', $rounds,
            substr(base64_encode(sha1($salt . Config::get('app.secret'))), 0, 22));

        return substr(crypt($value, $salt), 7);
    }

    /**
     * Compare inputs with an existing hash
     *
     * @access  public
     * @param   string  $hash
     * @param   string  $value
     * @param   string  $salt
     * @param   int     $rounds
     * @return  boolean
     */
    public static function compare($hash, $value, $salt, $rounds = 8)
    {
        return (bool) (($new = static::create($value, $salt, $rounds)) == $hash);
    }
}
