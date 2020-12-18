<?php
namespace application\admin\controller;
use lib\Request;
use application\admin\model\Admin as M;
use extend\Token;

class Login
{

    public function login(Request $request)
    {
        $name = $request->param('name','');
        $password = $request->param('password','');
        $model = new M();
        $res = $model->login($name,$password);
        return $res;
    }

    public function token(Request $request)
    {
        $token = $request->param('token','');
        $jwtToken = new Token();
        try {
            $checkToken = $jwtToken->checkToken($token);
            $data = (array)$checkToken['data']['data'];
            $adminId = $data['adminId'] ?? 0;
            $_SESSION['adminId'] = $adminId;
            return ['data'=>'','code' => 0, 'msg' => 'success'];
        } catch (\Exception $e){
            return ['data'=>'','code' => $e->getCode(), 'msg' => $e->getMessage()];
        }

    }

}
