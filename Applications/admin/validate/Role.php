<?php
namespace Applications\admin\validate;
use lib\Validate;
class Role extends Validate
{
    protected $rule = [
        'name'         =>  'require|max:50|unique:admin',

    ];
    protected $message  =   [
        'name.max'     =>  '标题不能超过50个字符',
        'name.require' =>   '标题不能为空',

    ];
}