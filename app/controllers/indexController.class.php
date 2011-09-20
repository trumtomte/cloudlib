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
        $this->upload->config(array(
            'directory' => 'img/'
        ));

        if($this->upload->uploadFile())
        {
            $this->view->error = 'funkade';
        }
        else
        {
            $this->view->error = $this->upload->error();
        }

        $this->view->render('index');
    }
}
