<?php

class indexController extends controller
{
    public function index()
    {
        $this->view->test = $this->model->test();
        $this->view->render('index');
    }

    public function upload()
    {
        echo '<pre>';
        echo print_r($_FILES);
        echo '</pre>';
        $test = array_shift($_FILES);
        echo '<pre>';
        echo print_r($test);
        echo '</pre>';
    }
}
