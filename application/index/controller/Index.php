<?php

namespace application\index\controller;
use lib\Controller;
use lib\Request;
class Index extends Controller
{


    public function index(Request $request)
    {

        return ['data' => 'Hello', 'code' => 0, 'msg' => 'success'];
    }




}
