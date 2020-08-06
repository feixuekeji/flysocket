<?php


namespace Applications\index\controller;

use lib\Request;
use Applications\index\model\User as M;

class User
{
    public function getInfo(Request $request)
    {
        $id = $request->param('id',0);
        $model = new M();
        $res = $model->getInfo($id);
        var_dump($res);
        return $res;
    }
}
