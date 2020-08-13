<?php

namespace Applications\admin\model;

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
            $roleId = $this->where('id',$adminId)->value('role_id');
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
        $salt = 'eifvk6';
            $res = $this
                ->field('password,id')
                ->where('name', $name)
                ->find();
            if ($res) {
                //if ($res->password == md5(base64_encode($password).$salt)) {
                if ($res->password == $password) {
                    $token = 1;
                   $data = ['data' => ['token' => $token], 'code' => 0, 'msg' => 'success'];
                } else {
                    $data = ['data' => '', 'code' => 401, 'msg' => '登录失败，请检查您的信息'];
                }
            } else {
                $data = ['data' => '', 'code' => 401, 'msg' => '该用户名不存在'];
            }
            return $data;
        }


    public function addAdmin($input)
    {

            $addData = [
                'name' => $input['name'],
                'password' => md5(base64_encode($input['password'])),
                'create_time' =>time(),
                'role_id' => intval($input['role_id'] ?? 0),
            ];
        $validate = new \Applications\admin\validate\Admin();

        if (!$validate->check($addData)) {
            var_dump($validate->getError());
        }

                $tag = $this->insert($addData);


        return $tag;

    }


}
