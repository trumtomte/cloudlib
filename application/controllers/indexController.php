<?php
/**
 * CloudLib :: Flexible Lightweight PHP Framework
 *
 * @author      Sebastian Book <cloudlibframework@gmail.com>
 * @copyright   Copyright (c) 2011 Sebastian Book <cloudlibframework@gmail.com>
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package     Cloudlib
 */

class indexController extends Controller
{
    // method used for the request method GET
    public function get()
    {
        $this->set('test', 'hello');

        return $this->render('index', 'index');
    }

    // custom method with a parameter
    public function test($param)
    {
        $this->set('test', $param);

        return $this->render('index', 'index');
    }

    // if a model has been defined when the route to this controller was made,
    // access it via $this->model->method().
    //
    // if you would like to use another model you can pass the model name
    // to $this->model(modelname)->method().
}
