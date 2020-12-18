<?php

namespace lib\facade;

use lib\Facade;

/**
 * @method mixed param(string $name = '', mixed $default = null, mixed $filter = '') static 获取当前请求的参数
 * @method mixed env(string $name = '', mixed $default = null, mixed $filter = '') static 获取环境变量
 * @method mixed file(string $name = '') static 获取上传的文件信息
 * @method mixed header(string $name = '', mixed $default = null) static 设置或者获取当前的Header
 * @method mixed input(array $data,mixed $name = '', mixed $default = null, mixed $filter = '') static 获取变量 支持过滤和默认值
 * @method mixed filter(mixed $filter = null) static 设置或获取当前的过滤规则
 * @method mixed has(string $name, string $type = 'param', bool $checkEmpty = false) static 是否存在某个请求参数
 * @method mixed only(mixed $name, string $type = 'param') static 获取指定的参数
 * @method mixed except(mixed $name, string $type = 'param') static 排除指定参数获取
 * @method bool isSsl() static 当前是否ssl
 * @method bool isAjax(bool $ajax = false) static 当前是否Ajax请求
 * @method bool isPjax(bool $pjax = false) static 当前是否Pjax请求
 * @method mixed ip(int $type = 0, bool $adv = true) static 获取客户端IP地址
 * @method bool isMobile() static 检测是否使用手机访问
 * @method string scheme() static 当前URL地址中的scheme参数
 * @method string query() static 当前请求URL地址中的query参数
 * @method string host(bool $stric = false) static 当前请求的host
 * @method string port() static 当前请求URL地址中的port参数
 * @method string protocol() static 当前请求 SERVER_PROTOCOL
 * @method string remotePort() static 当前请求 REMOTE_PORT
 * @method string contentType() static 当前请求 HTTP_CONTENT_TYPE
 * @method array routeInfo() static 获取当前请求的路由信息
 * @method array dispatch() static 获取当前请求的调度信息
 * @method string module() static 获取当前的模块名
 * @method string controller(bool $convert = false) static 获取当前的控制器名
 * @method string action(bool $convert = false) static 获取当前的操作名
 */
class Request extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'request';
    }
}
