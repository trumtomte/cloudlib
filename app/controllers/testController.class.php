<?php

class testController extends controller
{
    public function index()
    {
        $this->view->render('test');
    }

    public function test()
    {
        echo 'testfunction';
        $this->view->render('test');
    }
}
