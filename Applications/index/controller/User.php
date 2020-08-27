<?php


namespace Applications\index\controller;

use lib\Controller;
use lib\Request;
use Applications\index\model\User as M;

class User extends Controller
{
    public function getInfo(Request $request)
    {
        $id = $request->param('id',0);
        $model = new M();
        $res = $model->getInfo($id);
        return ['data' =>$res ,'code'=> 0, 'msg' => 'success'];

    }

    public function getInfo1()
    {
        var_dump($this->request);
        return ['data' =>'' ,'code'=> 0, 'msg' => 'success'];

    }


}
