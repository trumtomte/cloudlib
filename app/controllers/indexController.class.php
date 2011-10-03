<?php

class indexController extends controller
{
    public function index()
    {
        $this->layout()->render();
    }

    public function upload()
    {
        $this->upload->config(array(
            'filesize' => '3mb'
        ));

        if($this->upload->upload())
        {
            $this->set('msg', 'OK');
            $this->set('info', $this->upload->data());
        }
        else
        {
            $this->set('msg', $this->upload->error());
        }

        $this->layout()->render();
    }
}
