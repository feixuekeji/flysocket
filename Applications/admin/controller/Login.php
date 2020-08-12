<?php
namespace Applications\admin\controller;
use lib\Request;
use Applications\admin\model\Admin as M;

class Login
{

    public function login(Request $request)
    {
        $name = $request->param('name','');
        $password = $request->param('password','');
        $model = new M();
        $res = $model->login($name,$password);
        return $res;
    }

}
