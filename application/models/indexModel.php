<?php
/**
 * CloudLib :: Flexible Lightweight PHP Framework
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     Cloudlib
 */

class indexModel extends Model
{
    // super simple model function that does nothing
    public function index()
    {
        return 'this is my model: ' . __CLASS__;
    }

    // a model can access the database functions via
    // $this->database->method()
}
