<?php
namespace application\admin\controller;
use lib\Controller;
use lib\Request;
use application\admin\model\Admin as M;

class Admin extends Controller
{
    private $model;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->model = new M();
    }

    public function getInfo(Request $request)
    {
        $id = $request->param('id/d',0);
        $param = $request->param();
        $res = $this->model->getInfo($id);
        return ['data' =>$res ,'code'=> 0, 'msg' => 'success'];
    }



    public function add(Request $request)
    {
        $param = $request->param();
        $res = $this->model->add($param);
        return $res;
    }



}
