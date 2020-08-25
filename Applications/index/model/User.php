<?php

namespace Applications\index\model;

use lib\Request;
use think\Model;

class User extends Model
{
    public function getInfo($id)
    {
        $a = Request::_param();
        var_dump($a);
        $res = $this->where('id',2)->cache(true)->select();
        return $res;
    }
}
