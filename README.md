# 入口文件

>1当gatawayWorker启动后进行app初始化
>2当客户端发来消息时，初始化请求信息

## 入口文件定义

应用入口文件位于`application/worker/Event.php`，内容如下：

~~~
public static function onWorkerStart($worker)
{
    // 执行应用并响应
    Container::get('app')->init();
}

public static function onMessage($client_id, $message)
{
 App::run($client_id, $message);
}
~~~
