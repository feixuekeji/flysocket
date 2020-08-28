<?php

namespace Applications\index\model;

use think\Model;

class User extends Model
{
    public function getInfo($id)
    {
        $res = $this->cache(true)->select();
        return $res;
    }
}
