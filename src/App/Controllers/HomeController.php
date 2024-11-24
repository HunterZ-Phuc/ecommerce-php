<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Trang chủ',
            'message' => 'Xin chào!'
        ];
        
        $this->view('home/index', $data);
    }
}
