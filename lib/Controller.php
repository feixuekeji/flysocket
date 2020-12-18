<?php

namespace lib;

/**控制器
 * Class Controller
 * @package lib
 */
class Controller
{

    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 构造方法
     * @access public
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {}


}
