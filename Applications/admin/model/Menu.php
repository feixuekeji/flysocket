<?php

namespace Applications\admin\model;
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

}
