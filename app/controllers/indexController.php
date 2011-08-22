<?php

class indexController extends controller
{
    public function index()
    {

        echo $this->view->render('index');
    }
}
