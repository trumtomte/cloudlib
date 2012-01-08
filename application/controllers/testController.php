<?php
class testController extends Controller
{
    public function get($param = 'lol')
    {
        $this->set('test', $param. ' GET');
        return $this->render('index', 'index');
    }

    public function before()
    {
        echo 'before';
    }

    public function test()
    {
        $this->set('test', $this->model()->test());
        return $this->render('index', 'index');
    }

    public function param($param = 'test')
    {
        $this->set('test', $param);
        return $this->render('index', 'index');
    }
}
