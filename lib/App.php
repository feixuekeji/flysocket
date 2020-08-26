<?php

namespace lib;

use Exception;
use think\facade\Db;
use \GatewayWorker\Lib\Gateway;
//加载函数库
require_once __DIR__ . '/../Applications/common.php';
class App
{
    public static function run($client_id, $message)
    {
        $message = json_decode($message,true) ?? [];
        try {
            $request = new Request($message);
        } catch (Exception $e){
            $response = ['data' => '','code' => $e->getCode(),'msg' => $e->getMessage()];
            Gateway::sendToClient($client_id, json_encode($response));
            return;
        }

        try {
            $res = Route::dispatch($request);
            $response = $request->response($res['data'],$res['code'],$res['msg']);
        } catch (Exception $e) {
            Log::error('exception',[$e]);
            //echo 'Error: ' . $e . PHP_EOL;
            $response = $request->response('',$e->getCode() ?: 1,iconv('gbk', 'utf-8', $e->getMessage()));
        } catch (Error $error) {
            Log::error('error',[$error]);
            $response = $request->response('',$error->getCode() ?: 1,$error->getMessage());
        }
        Log::info('response',$response);
        // 向当前client_id发送数据
        Gateway::sendToClient($client_id, json_encode($response));

    }


    public static function init()
    {
        Redis::set('asd',123);
        $cache = new Cache(Config::get('','cache'));
        //数据库初始化
        Db::setConfig(Config::get('','database'));

        $cache->set('a','ferg',86400);
        Db::setCache($cache);
    }
}