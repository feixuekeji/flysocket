<?php


namespace application\index\controller;

use lib\Controller;
use lib\Request;
use application\index\model\User as M;

class User extends Controller
{
    public function getInfo(Request $request)
    {
        $id = $request->param('id',0);
        //$model = new M();
        //$res = $model->getInfo($id);
        $time = time();
        sleep(20);
        var_dump(\lib\facade\Request::action());
        return ['data' =>'hahhahah' ,'code'=> 0, 'msg' => 'success'];

    }

    public function getInfo1()
    {
        return ['data' =>'' ,'code'=> 0, 'msg' => 'success'];

    }


}
