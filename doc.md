# 请求对象
当前的请求对象由`lib\Request`类负责,收到消息时初始化Request，并注入控制器中，控制器中使用依赖注入获取。在其它场合则可以使用`lib\facade\Request`静态类操作。



## 请求对象调用

在控制器中通常情况下有两种方式进行依赖注入。

### 构造方法注入

~~~
<?php
namespace application\admin\controller;

use lib\Request;

class Index 
{
    /**
     * @var \think\Request Request实例
     */
    protected $request;
    
    /**
     * 构造方法
     * @param Request $request Request对象
     * @access public
     */
    public function __construct(Request $request)
    {
		$this->request = $request;
    }
    
    public function index()
    {
		return $this->request->param('name');
    }    
}

~~~

> 如果你继承了控制器基类`lib\Controller`的话，系统已经自动完成了请求对象的构造方法注入了，你可以直接使用`$this->request`属性调用当前的请求对象。

~~~
<?php
namespace application\index\controller;

use lib\Controller;


class Index extends Controller
{
    
    public function index()
    {
	
        $id = $this->request->param('id',0);

    }    
}

~~~

### 操作方法注入


~~~
<?php


namespace application\index\controller;

use lib\Controller;
use lib\Request;

class Index extends Controller
{
    
    public function index(Request $request)
    {
		return $request->param('name');
    }    
}

~~~

无论是否继承系统的控制器基类，都可以使用操作方法注入。



## Facade调用

在没有使用依赖注入的场合，可以通过`Facade`静态代理机制来静态调用请求对象的方法
use lib\facade\aaa

~~~
<?php

namespace app\index\controller;

use lib\facade\Request;

class Index extends Controller
{
    
    public function index()
    {
		return Request::param('name');
    }    
}

~~~
