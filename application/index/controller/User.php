<?php


namespace application\index\controller;

use lib\Controller;
use lib\facade\Session;
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


    public function session()
    {
        Session::set('a',123);

        Session::set('user.name','llll');
        Session::push('user',['age' => 18]);
        $data = Session::get('a');
        var_dump($_SESSION);
        return ['data' =>$data ,'code'=> 0, 'msg' => 'success'];

    }


}
