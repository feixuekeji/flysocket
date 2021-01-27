<?php

namespace application\admin\model;

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


    public function getList($size = 10,$page = 1,$where = null)
    {
        $list = $this
            ->field('a.*,r.name role_name')
            ->alias('a')
            ->leftJoin('role r','a.role_id = r.id')
            ->where($where)
            ->order(['a.id' => 'desc'])
            ->paginate([
                'list_rows'=> $size,
                'page'     => $page,
            ]);
        return $list;
    }


    public function add($input)
    {
        $salt = Config::get('password_salt');
        $addData = [
            'name' => $input['name'],
            'password' => md5(base64_encode($input['password'].$salt)),
            'create_time' => time(),
            'role_id' => intval($input['role_id'] ?? 0),
        ];
        $validate = new \application\admin\validate\Admin();
        if (!$validate->check($addData))
            return ['data' => '', 'code' => 300, 'msg' => $validate->getError()];
        $roleInfo = Role::where(['id'=>$input['role_id'],'status' => 1])->find();
        if (empty($roleInfo))
            return ['data' => '', 'code' => 20003, 'msg' => '角色不存在'];
        $res = $this->insert($addData);
        if ($res)
            return ['data' => '', 'code' => 0, 'msg' => '成功'];
        return ['data' => '', 'code' => 500, 'msg' => '异常'];

    }



    public function del($id)
    {
        if ($id == 1)
            return ['data' => '', 'code' => 20006, 'msg' => '初始管理员不能删除'];
        $res = $this->where('id',$id)->delete();
        if ($res)
            return ['data' => '', 'code' => 0, 'msg' => '成功'];
        return ['data' => '', 'code' => 20004, 'msg' => '账号不存在'];
    }


}
