<?php

namespace application\index\model;

use think\Model;

class User extends Model
{
    public function getInfo($id)
    {
        $res = $this->where('id',$id)->cache(true)->find();
        return $res;
    }
}
