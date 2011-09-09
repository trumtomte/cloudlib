<?php

class index_controller extends controller
{
    public function index()
    {
        echo $this->view->render('index');
    }
}
