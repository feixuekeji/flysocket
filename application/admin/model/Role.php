<?php

namespace application\admin\model;
use think\facade\Db;
use think\Model;

class Role extends Model
{


    public function getList($size = 10,$page = 1,$where = null)
    {
        $list = $this
            ->where($where)
            ->order(['id' => 'desc'])
            ->cache(true)
            ->paginate([
                'list_rows'=> $size,
                'page'     => $page,
            ]);
        return $list;
    }




    public function add($data)
    {

        $roleData = [
            'name' => $data['name'],
            'create_time' => time(),
            'status' => $data['status'] ?? 1,
        ];
        $validate = new \application\admin\validate\Role();

        if (!$validate->check($roleData))
            return ['data' => '', 'code' => 300, 'msg' => $validate->getError()];
        $this->startTrans();
        try {
            $roleId = $this->insertGetId($roleData);
            $powerData = $data['power'];
            $this->editPower($roleId,$powerData);
            // 提交事务
            $this->commit();
            if ($roleId)
                return ['data' => '', 'code' => 0, 'msg' => '成功'];

        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return ['data' => '', 'code' => 500, 'msg' => $e->getMessage()];
        }
    }


    public function edit($id,$data)
    {
        $roleData = [
            'name' => $data['name'],
            'status' => $data['status'],
        ];
        $info = $this->where('id',$id)->find($id);
        if (empty($info))
            return ['data' => '', 'code' => 300, 'msg' => '不存在此id'];
        $validate = new \application\admin\validate\Role();
        if (!$validate->check($roleData))
            return ['data' => '', 'code' => 300, 'msg' => $validate->getError()];
        $info->name = $data['name'];
        $info->status = $data['status'];
        $this->startTrans();
        try {
            $roleId = $info->save();
            $powerData = $data['power'];
            $this->editPower($roleId,$powerData);
            // 提交事务
            $this->commit();
            if ($roleId)
                return ['data' => '', 'code' => 0, 'msg' => '成功'];

        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return ['data' => '', 'code' => 500, 'msg' => $e->getMessage()];
        }
    }

    /**
     * 编辑权限
     * @param int $roleId
     * @param $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function editPower($roleId = 0,$data)
    {
        foreach($data as $k => $v)
        {
            $v['role_id'] = $roleId;
            $v['menu_id'] = $v['menu_id'];

            $info =  Db::table('role_power')->where(['role_id'=>$roleId,'menu_id'=>$v['menu_id']])->find();
            if (!empty($info))
                Db::table('role_power')->where(['role_id'=>$roleId,'menu_id'=>$v['menu_id']])->update($v);
            else
                Db::table('role_power')->save($v);
        }
        return;
    }

    public function del($id = 0)
    {
        $existAdmin = Admin::where('role_id',$id)->find();
        if (!empty($existAdmin))
            return ['data' => '', 'code' => 20001, 'msg' => '该角色下存在管理员，不能删除'];
        $this->startTrans();
        try {
            Db::table('role_power')->where(['role_id'=>$id])->delete();
            $res = $this->where('id',$id)->delete();
            // 提交事务
            $this->commit();
            if ($res)
                return ['data' => '', 'code' => 0, 'msg' => '成功'];
            return ['data' => '', 'code' => 20002, 'msg' => '角色不存在'];

        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
            return ['data' => '', 'code' => 500, 'msg' => $e->getMessage()];
        }
    }

}
