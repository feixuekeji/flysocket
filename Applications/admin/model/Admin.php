<?php

namespace Applications\admin\model;

use lib\Config;
use lib\Token;
use think\facade\Db;
use think\Model;

class Admin extends Model
{
    public function getInfo($id)
    {
        $res = $this->find($id);
        return $res;
    }


    /**
     * 权限检查
     * @param int $adminId
     * @param string $api
     * @return mixed
     */
    public function checkAdminAuth($adminId = 0, $api = '')
    {
        try {
            $api = explode('/',$api);
            $controller = $api[0].'/'.$api[1];
            $action = $api[2];
            $menuModel = new Menu();
            $menuId =  $menuModel->getIdByAction($controller);
            $roleId = $this->where(['id' =>$adminId,'status' => 1])->value('role_id');
            $where = [
                'role_id' => $roleId,
                'menu_id' => $menuId,
            ];
            $checkTag = Db::table('role_power')->where($where)->value($action,0);
            return $checkTag;
        } catch (\Exception $e) {
            return false;
        }

    }




    public function login($name,$password)
    {
        $salt = Config::get('password_salt');
            $res = $this
                ->field('password,id')
                ->where('name', $name)
                ->find();
            if ($res) {
                if ($res->password == md5(base64_encode($password.$salt))) {
                    $jwtToken = new Token();
                    $tokenData = array(
                        'adminId' => $res->id,
                    );
                    $token = $jwtToken->createToken($tokenData, 86400);
                   $data = ['data' => ['token' => $token['token'],'adminId' => $res->id], 'code' => 0, 'msg' => 'success'];
                } else {
                    $data = ['data' => '', 'code' => 40101, 'msg' => '登录失败，请检查您的信息'];
                }
            } else {
                $data = ['data' => '', 'code' => 40102, 'msg' => '该用户名不存在'];
            }
            return $data;
        }


    public function addAdmin($input)
    {
        $salt = Config::get('password_salt');
        $addData = [
            'name' => $input['name'],
            'password' => md5(base64_encode($input['password'].$salt)),
            'create_time' => time(),
            'role_id' => intval($input['role_id'] ?? 0),
        ];
        $validate = new \Applications\admin\validate\Admin();

        if (!$validate->check($addData))
            return ['data' => '', 'code' => 300, 'msg' => $validate->getError()];
        $res = $this->insert($addData);
        if ($res)
            return ['data' => '', 'code' => 0, 'msg' => '成功'];
        return ['data' => '', 'code' => 500, 'msg' => '异常'];

    }


}
