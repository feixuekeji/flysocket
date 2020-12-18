<?php

namespace application\admin\model;
use think\facade\Db;
use \think\Model;


class Menu extends Model
{

    protected $autoWriteTimestamp = true;//自动时间戳


    public function getIdByAction($action)
    {
        $res = $this
            ->where('action',$action)
            ->value('id');
        return $res;
    }


    /**
     * 菜单树
     * @param $data
     * @param int $parent_id
     * @return array
     */
    public function getTree($data, $parent_id = 0)
    {
        $tree = [];
        foreach ($data as $k => $v) {
            if ($v['parent_id'] == $parent_id) {
                $children = $this->getTree($data, $v['id']);
                !empty($children) && $v['children'] = $children;
                $tree[] = $v;
                unset($data[$k]);
            }
        }
        return $tree;
    }



    public function getMenu()
    {
        $list = $this->order('id desc')->select();
        $menu = $this->getTree($list);
        return $menu;
    }


    public function getPower($roleId = 0)
    {
        $field = array('list','add','edit','delete','custom_one','custom_two');
        $list = $this->order('id desc')->select()->toArray();
        foreach ($list as $k => &$v) {
            foreach ($field as $item)
            {
                if (empty($v[$item]))//菜单没有该功能，删除
                    unset($v[$item]);
                else
                    $v[$item] = 0;//初始权限设为空
            }
        }
        $powerList = Db::table('role_power')->where('role_id',$roleId)->select();//权限列表
        $temp = array_column($list, 'id');
        foreach ($powerList as $k1 => $v1){
            $index = array_search($v1['menu_id'], $temp);
            if ($index){
                foreach ($field as $item)
                {
                    isset($list[$index][$item]) && $list[$index][$item] = $v1[$item];//根据权限表中数据判断是否有该权限
                }
            }

        }
        $list = $this->getTree($list);


        return $list;
    }




}
