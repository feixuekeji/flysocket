<?php
namespace Applications\admin\controller;
use lib\Request;
use Applications\admin\model\Role as M;

class Role extends Base
{
    private $model;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->model = new M();
    }

    public function list(Request $request)
    {
        $res = $this->model->getList(10);
        return ['data' =>$res ,'code'=> 0, 'msg' => 'success'];
    }


    public function add(Request $request)
    {
        $data = $request->param();
        $res = $this->model->add($data);
        return $res;
    }

    public function edit(Request $request)
    {
        $id = $request->param('id/d');
        $data = $request->param();
        $res = $this->model->edit($id,$data);
        return $res;
    }

    public function delete(Request $request)
    {
        $id = $request->param('id/d');
        $res = $this->model->del($id);
        return $res;
    }

}
