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

    public function __construct()
    {
        $this->rootPath = __DIR__ . DIRECTORY_SEPARATOR . '../';
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