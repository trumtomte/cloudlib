<?php

class indexController extends controller
{
    public function index()
    {
        $this->view->test = $this->model->test();
        $this->view->render('index');
    }
}
