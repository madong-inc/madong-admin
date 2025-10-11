<?php

namespace plugin\test\app\controller;

use support\Request;

class IndexController
{

    public function index()
    {
        return view('index/index', ['name' => 'test']);
    }

}
