<?php

class indexController extends controller
{
    public function index()
    {
        $this->view->test = $this->model->test();
        echo $this->view->render('index');
    }
}
