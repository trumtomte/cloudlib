<?php

class indexController extends Controller
{
    public function index()
    {
        $this->layout()->render();
    }

    public function upload()
    {
        $this->Uploader->config(array(
            'filesize' => '3mb'
        ));

        if($this->Uploader->upload())
        {
            $this->set('msg', 'OK');
            $this->set('info', $this->Uploader->data());
        }
        else
        {
            $this->set('msg', $this->Uploader->error());
        }

        $this->layout()->render();
    }
}
