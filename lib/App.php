<?php

namespace lib;

use Exception;
use think\facade\Db;
use \GatewayWorker\Lib\Gateway;
use lib\facade\Log;
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
        $message = json_decode($message,true) ?? [];
        try {
            $request = Container::get('request',[$message]);
            $res = Route::dispatch($request);
            $response = $request->response($res['data'],$res['code'],$res['msg']);
        } catch (Exception $e) {
            Error::exception($e);
            $response = $request->response('',$e->getCode() ?: 1,iconv('gbk', 'utf-8', $e->getMessage()));
        } catch (\Error $error) {
            \lib\Error::errorLog($error);
            $response = $request->response('',$error->getCode() ?: 1,$error->getMessage());
        }
        Log::info('response',$response);
        // 向当前client_id发送数据
        Gateway::sendToClient($client_id, json_encode($response));
        //清空request
        Container::remove('request');

    }


    public function init()
    {
        try {
            $cache = Container::get('cache',[Config::get('','cache')]);
            $log = Container::get('log',[Config::get('','log')]);
            //数据库初始化
            Db::setConfig(Config::get('','database'));
            Db::setCache($cache);
            Db::setLog($log);
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

}