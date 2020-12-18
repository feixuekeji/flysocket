<?php
namespace application\admin\controller;
use lib\Request;
use application\admin\model\Admin as M;

class Admin extends Base
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


    public function list(Request $request)
    {
        $size = $request->param('size/d',10);
        $page = $request->param('page/d',1);
        $status = $request->param('status', 0);
        $keyword = $request->param('keyword', '');
        $where = [];
        $status > 0 && $where[] = array('a.status', '=', $status);
        if (!empty($keyword))
            $where[] = array('a.name', 'like', '%' . $keyword . '%');
        $res = $this->model->getList($size,$page,$where);
        return ['data' =>$res ,'code'=> 0, 'msg' => 'success'];
    }


    public function add(Request $request)
    {
        $param = $request->param();
        $res = $this->model->add($param);
        return $res;
    }

    public function edit(Request $request)
    {
        $param = $request->param();
        $res = $this->model->edit($param);
        return $res;
    }

    public function delete(Request $request)
    {
        $id = $request->param('id/d',0);
        $res = $this->model->del($id);
        return $res;
    }


}
