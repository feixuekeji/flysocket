<?php
namespace Applications\admin\validate;
use lib\Validate;
class Admin extends Validate
{
    protected $rule = [
        'name'         =>  'require|max:50|unique:admin',
        'password'    =>  'require',
        //'role_id'      => '>:100'

    ];
    protected $message  =   [
        'name.max'     =>  '标题不能超过50个字符',
        'name.require' =>   '标题不能为空',
        'password'    =>  '内容不能为空',
        'role_id'    =>  '角色不能为空',

    ];
}