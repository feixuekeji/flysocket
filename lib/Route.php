<?php

namespace lib;

class Route
{

    /**
     * 路由表
     * @var array
     */
    protected $routeList = [];

    /**
     * 路由分发
     * @param Request $request
     * @return mixed|void
     */
    public static function dispatch(Request $request)
    {
        $module = $request->module();
        $controller = $request->controller();
        $action = $request->action();
        if (!$module || !$controller || !$action)
           throw new \Exception('api  is not exists',100);
        //将api转换为控制器方法的命名空间
        $className = '\\application\\' . $module . '\\controller\\' . ucfirst($controller);
        $obj = new $className($request);
        if (!method_exists($obj, $action))
            throw new \Exception('method ' . $action . ' is not exists',100);
        $res = call_user_func_array(array($obj, $action), array($request));
        return $res;
    }

    /**
     * desc:导入
     * author: xxf<waxiongfeifei@gmail.com>
     * date: 2021/4/22
     * time: 上午11:04
     */
    public function import()
    {
        $path = Container::get('app')->getRoutePath();

        $files = is_dir($path) ? scandir($path) : [];

        foreach ($files as $file) {
            if (strpos($file, '.php')) {
                $filename = $path . DIRECTORY_SEPARATOR . $file;
                // 导入路由配置
                $rules = include $filename;
                if (is_array($rules)) {
                    $this->routeList = array_merge($this->routeList,$rules);
                }
            }
        }
    }



    public function getRoute($api)
    {
        if (array_key_exists($api, $this->routeList))//获取真实路径
            $api = $this->routeList[$api];
        return $api;
    }
}
