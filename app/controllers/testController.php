<?php

class testController extends Controller
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
