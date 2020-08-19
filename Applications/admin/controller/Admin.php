<?php
namespace Applications\admin\controller;
use lib\Request;
use Applications\admin\model\Admin as M;

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
        var_dump($param);
        $res = $this->model->getInfo($id);
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
        $res = $this->model->addAdmin($param);
        return $res;
    }

    public function list(Request $request)
    {
        $id = $request->param('id',0);
        $res = $this->model->paginate([
            'list_rows'=> 2,
            'page'     => 2,
        ]);
        return ['data' =>$res ,'code'=> 0, 'msg' => 'success'];
    }

}
