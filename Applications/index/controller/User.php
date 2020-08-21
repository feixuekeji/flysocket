<?php


namespace Applications\index\controller;

use lib\Request;
use Applications\index\model\User as M;

class User
{
    public function getInfo(Request $request)
    {
        $id = $request->param('id',0);
        $param = $request->param();
        $model = new M();
        $res = $model->getInfo($id);
        return ['data' =>$res ,'code'=> 0, 'msg' => 'success'];
    }
}
