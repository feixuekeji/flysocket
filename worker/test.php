<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Workerman\Worker;
use Workerman\Lib\Timer;
use Workerman\Connection\AsyncTcpConnection;
$worker = new Worker();
$worker->onWorkerStart = 'connect';
function connect(){
    static $count = 0;
    // 2000个链接
    if ($count++ >= 2000) return;
    // 建立异步链接
    $con = new AsyncTcpConnection('ws://127.0.0.1:8282');
    $con->onConnect = function($con) {
        // 递归调用connect
        connect();
    };
    $con->onMessage = function($con, $msg) {
        echo "recv $msg\n";
    };
    $con->onClose = function($con) {
        echo "con close\n";
    };

        //$con->send("ping");
        $con->send('{
                    "app": "http://www.bejson.com",
                    "api": "myinfo",
                    "ver": 88,
                    "data": {
                        "id": 1
                    },
                    "code": 0,
                    "msg": "success"
                }');
    $con->connect();
    echo 'time:'.time().',,,'.$count, " connections complete\n";
}
Worker::runAll();