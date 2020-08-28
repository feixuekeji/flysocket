<?php
namespace application\admin\controller;
use lib\Request;
use application\admin\model\Menu as M;

class Menu extends Base
{
    private $model;
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->model = new M();
    }

    public function list(Request $request)
    {
        $res = $this->model->getPower(1);
        return ['data' =>$res ,'code'=> 0, 'msg' => 'success'];
    }



}
