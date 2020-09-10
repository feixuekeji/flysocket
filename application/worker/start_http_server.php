<?php
use Workerman\Worker;
// 自动加载类
require_once __DIR__ . '/../../vendor/autoload.php';

$worker = new Worker('http://0.0.0.0:8080');

$worker->onMessage = function($connection, $request)
{

//    $client = stream_socket_client('tcp://127.0.0.1:7273');
//    if(!$client)exit("can not connect");
//    // 推送的数据，包含uid字段，表示是给这个uid推送
//    $data = array('uid'=>'uid1', 'percent'=>'88%');
//    fwrite($client, json_encode($data)."\n");
//    // 读取推送结果
//    $msg = fread($client, 8192);
//    // $request为请求对象，这里没有对请求对象执行任何操作直接返回hello给浏览器
//    $connection->send($msg);
};

// 运行worker
Worker::runAll();