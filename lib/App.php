<?php

namespace lib;

use Exception;
use think\facade\Db;
use \GatewayWorker\Lib\Gateway;
//加载函数库
require_once __DIR__ . '/../Applications/common.php';
class App  extends Container
{
    public static function run($client_id, $message)
    {
        $message = json_decode($message,true) ?? [];
        try {
            $request = Container::get('request',[$message]);
            $res = Route::dispatch($request);
            $response = $request->response($res['data'],$res['code'],$res['msg']);
        } catch (Exception $e) {
            Log::error('exception',[$e]);
            //echo 'Error: ' . $e . PHP_EOL;
            $response = $request->response('',$e->getCode() ?: 1,iconv('gbk', 'utf-8', $e->getMessage()));
        } catch (\Error $error) {
            Log::error('error',[$error]);
            $response = $request->response('',$error->getCode() ?: 1,$error->getMessage());
        }
        Log::info('response',$response);
        // 向当前client_id发送数据
        Gateway::sendToClient($client_id, json_encode($response));
        //清空request
        Container::remove('request');

    }


    public static function init()
    {
//        $cache = new Cache(Config::get('','cache'));
        $cache = Container::get('cache',[Config::get('','cache')]);
        //数据库初始化
        Db::setConfig(Config::get('','database'));
        Db::setCache($cache);
    }
}