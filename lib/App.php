<?php

namespace lib;

use Exception;
use think\facade\Db;
use \GatewayWorker\Lib\Gateway;
use lib\facade\Log;
use Workerman\Worker;
//加载函数库
require_once __DIR__ . '/../application/common.php';


class App  extends Container
{

    /**
     * 应用根目录
     * @var string
     */
    protected $rootPath;

    /**
     * 运行时目录
     * @var string
     */
    protected $runtimePath;

    /**
     * 配置目录
     * @var string
     */
    protected $configPath;

    /**
     * 路由目录
     * @var string
     */
    protected $routePath;

    public function __construct()
    {
        $this->rootPath = __DIR__ . DIRECTORY_SEPARATOR . '../';
        $this->routePath   = $this->rootPath . 'route' . DIRECTORY_SEPARATOR;
        $this->configPath  = $this->rootPath . 'config' . DIRECTORY_SEPARATOR;
        $this->runtimePath = $this->rootPath . 'runtime' . DIRECTORY_SEPARATOR;
    }

    public function run($client_id, $message)
    {
        try {
            $message = json_decode($message,true);
            !is_array($message) && $message = [];
            $request = Container::get('request',[$message]);
            $res = Route::dispatch($request);
            $response = $request->response($res['data'],$res['code'],$res['msg']);
        } catch (\Throwable $e) {
            Error::exception($e);
            $response = $request->response('',$e->getCode() ?: 1,$e->getMessage());
        }
        Log::info('response',$response);
        // 向当前client_id发送数据
        Gateway::sendToClient($client_id, json_encode($response));
        //清空request
        Container::remove('request');

    }

    /**
     *初始化
     * @author xingxiong.fei@163.com
     * @date 2020-09-03 9:43
     */
    public function init($workerId)
    {
        try {
            $log = Container::get('log',[Config::get('','log')]);
            $cache = Container::get('cache',[Config::get('','cache')]);
            Container::get('session',[Config::get('','session')]);
            //加载路由
            Container::get('route')->import();
            //数据库初始化
            Db::setConfig(Config::get('','database'));
            Db::setCache($cache);
            Db::setLog($log);
            $workerId == 0 && $this->corn();
        } catch (\Exception $e) {
            Error::exception($e);
        }

    }

    /**
     * 获取应用根目录
     * @access public
     * @return string
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }


    /**
     * 获取路由目录
     * @access public
     * @return string
     */
    public function getRoutePath()
    {
        return $this->routePath;
    }

    /**
     * 获取应用配置目录
     * @access public
     * @return string
     */
    public function getConfigPath()
    {
        return $this->configPath;
    }


    /**
     * 获取应用运行时目录
     * @access public
     * @return string
     */
    public function getRuntimePath()
    {
        return $this->runtimePath;
    }

    /**定时器
     * @author waxiongfeifei@gmail.com
     * @date 2020/12/29 下午6:06
     */
    public function corn()
    {
        $filename = __DIR__ . DIRECTORY_SEPARATOR . '../config/corn.php';
        if (is_file($filename)) {
            include $filename;
        }
    }

}