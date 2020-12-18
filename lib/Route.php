<?php

namespace lib;

class Route
{
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
}
