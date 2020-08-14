<?php
namespace Applications\admin\controller;
use lib\Request;
use Applications\admin\model\Admin as M;

class Admin extends Base
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }
    public function getInfo(Request $request)
    {
        $id = $request->param('id/d',0);
        $param = $request->param();
        var_dump($param);
        $model = new M();
        $res = $model->getInfo($id);
        return ['data' =>$res ,'code'=> 0, 'msg' => 'success'];
    }

    public function index(Request $request)
    {
        $id = $request->param('id',0);

        return ['data' =>$id ,'code'=> 0, 'msg' => 'success'];
    }



    public function add(Request $request)
    {
        $param = $request->param();
        $model = new M();
        $res = $model->addAdmin($param);
        return $res;
    }

}
