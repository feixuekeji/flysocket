<?php
namespace lib;

class Request
{

    /**
     * 当前模块名
     * @var string
     */
    protected $module;

    /**
     * 当前控制器名
     * @var string
     */
    protected $controller;

    /**
     * 当前操作名
     * @var string
     */
    protected $action;


    /**
     * 当前请求参数
     * @var array
     */
    protected $param = [];

    /**
     * 当前请求api
     * @var array
     */
    protected $api;

    /**
     * 当前请求路由地址
     * @var array
     */
    protected $route;

    /**
     * 当前请求ip地址
     * @var array
     */
    protected $ip;

    /**
     * 当前请求app
     * @var array
     */
    protected $app;


    /**
     * 当前请求版本号
     * @var array
     */
    protected $ver;
    /**
     * 全局过滤规则
     * @var array
     */
    protected $filter;


    /**
     * 路由配置文件
     * @var array
     */
    protected static $routeConfig;

    private static $_instance; //存储对象

    public function __construct(array $options = [])
    {
        $this->init($options);
        self::$_instance = $this;
    }


    public function init(array $options = [])
    {
        $this->filter = Config::get('default_filter');
        $this->param = $options['data'] ?? [];
        $this->api =  $options['api'] ?? '';
        $this->route =  $options['api'] ?? '';
        $this->app =  $options['app'] ?? '';
        $this->ver =  $options['ver'] ?? '';
        $this->ip =  $this->ip();
        if (empty(self::$routeConfig))
            self::$routeConfig  = include_once __DIR__ . '/../config/route.php';//加载路由表
        if (array_key_exists($this->route, self::$routeConfig))//获取真实路径
            $this->route = self::$routeConfig[$this->route];
        $api = explode('/',$this->route);
        if (count($api) < 3)
            throw new \Exception('api  is not exists',100);
        $this->setModule($api[0]);
        $this->setController($api[1]);
        $this->setAction($api[2]);
        Log::info('request',$options);
        Log::info('request',get_object_vars($this));


    }


    /**
     * @return array
     */
    public function api()
    {
        return $this->api;
    }

    /**
     * @return array
     */
    public function route()
    {
        return $this->route;
    }

    /**
     * @return array
     */
    public function app()
    {
        return $this->app;
    }

    /**
     * @return array
     */
    public function ver()
    {
        return $this->ver;
    }


    /**
     * 设置请求数据
     * @access public
     * @param  string    $name  参数名
     * @param  mixed     $value 值
     */
    public function __set($name, $value)
    {
        return $this->param[$name] = $value;
    }

    /**
     * 获取请求数据的值
     * @access public
     * @param  string $name 参数名
     * @return mixed
     */
    public function __get($name)
    {
        return $this->param($name);
    }

    /**
     * 获取当前请求的参数
     * @access public
     * @param  mixed         $name 变量名
     * @param  mixed         $default 默认值
     * @param  string|array  $filter 过滤方法
     * @return mixed
     */
    public function param($name = '', $default = null, $filter = '')
    {

        return $this->input($this->param, $name, $default, $filter);
    }


    /**
     * 获取变量 支持过滤和默认值
     * @access public
     * @param  array         $data 数据源
     * @param  string|false  $name 字段名
     * @param  mixed         $default 默认值
     * @param  string|array  $filter 过滤函数
     * @return mixed
     */
    public function input($data = [], $name = '', $default = null, $filter = '')
    {
        if (false === $name) {
            // 获取原始数据
            return $data;
        }

        $name = (string) $name;
        if ('' != $name) {
            // 解析name
            if (strpos($name, '/')) {
                list($name, $type) = explode('/', $name);
            }

            $data = $this->getData($data, $name);

            if (is_null($data)) {
                return $default;
            }

            if (is_object($data)) {
                return $data;
            }
        }

        // 解析过滤器
        $filter = $this->getFilter($filter, $default);

        if (is_array($data)) {
            array_walk_recursive($data, [$this, 'filterValue'], $filter);
            if (version_compare(PHP_VERSION, '7.1.0', '<')) {
                // 恢复PHP版本低于 7.1 时 array_walk_recursive 中消耗的内部指针
                $this->arrayReset($data);
            }
        } else {
            $this->filterValue($data, $name, $filter);
        }

        if (isset($type) && $data !== $default) {
            // 强制类型转换
            $this->typeCast($data, $type);
        }

        return $data;
    }

    /**
     * 获取数据
     * @access public
     * @param  array         $data 数据源
     * @param  string|false  $name 字段名
     * @return mixed
     */
    protected function getData(array $data, $name)
    {
        foreach (explode('.', $name) as $val) {
            if (isset($data[$val])) {
                $data = $data[$val];
            } else {
                return;
            }
        }

        return $data;
    }

    /**
     * 设置或获取当前的过滤规则
     * @access public
     * @param  mixed $filter 过滤规则
     * @return mixed
     */
    public function filter($filter = null)
    {
        if (is_null($filter)) {
            return $this->filter;
        }

        $this->filter = $filter;
    }

    protected function getFilter($filter, $default)
    {
        if (is_null($filter)) {
            $filter = [];
        } else {
            $filter = $filter ?: $this->filter;
            if (is_string($filter) && false === strpos($filter, '/')) {
                $filter = explode(',', $filter);
            } else {
                $filter = (array) $filter;
            }
        }

        $filter[] = $default;

        return $filter;
    }

    /**
     * 递归过滤给定的值
     * @access public
     * @param  mixed     $value 键值
     * @param  mixed     $key 键名
     * @param  array     $filters 过滤方法+默认值
     * @return mixed
     */
    private function filterValue(&$value, $key, $filters)
    {
        $default = array_pop($filters);

        foreach ($filters as $filter) {
            if (is_callable($filter)) {
                // 调用函数或者方法过滤
                $value = call_user_func($filter, $value);
            } elseif (is_scalar($value)) {
                if (false !== strpos($filter, '/')) {
                    // 正则过滤
                    if (!preg_match($filter, $value)) {
                        // 匹配不成功返回默认值
                        $value = $default;
                        break;
                    }
                } elseif (!empty($filter)) {
                    // filter函数不存在时, 则使用filter_var进行过滤
                    // filter为非整形值时, 调用filter_id取得过滤id
                    $value = filter_var($value, is_int($filter) ? $filter : filter_id($filter));
                    if (false === $value) {
                        $value = $default;
                        break;
                    }
                }
            }
        }

        return $value;
    }

    /**
     * 强制类型转换
     * @access public
     * @param  string $data
     * @param  string $type
     * @return mixed
     */
    private function typeCast(&$data, $type)
    {
        switch (strtolower($type)) {
            // 数组
            case 'a':
                $data = (array) $data;
                break;
            // 数字
            case 'd':
                $data = (int) $data;
                break;
            // 浮点
            case 'f':
                $data = (float) $data;
                break;
            // 布尔
            case 'b':
                $data = (boolean) $data;
                break;
            // 字符串
            case 's':
                if (is_scalar($data)) {
                    $data = (string) $data;
                } else {
                    throw new \InvalidArgumentException('variable type error：' . gettype($data));
                }
                break;
        }
    }

    /**
     * 获取指定的参数
     * @access public
     * @param  string|array  $name 变量名
     * @return mixed
     */
    public function only($name)
    {
        $param = $this->param;

        if (is_string($name)) {
            $name = explode(',', $name);
        }

        $item = [];
        foreach ($name as $key => $val) {

            if (is_int($key)) {
                $default = null;
                $key     = $val;
            } else {
                $default = $val;
            }

            if (isset($param[$key])) {
                $item[$key] = $param[$key];
            } elseif (isset($default)) {
                $item[$key] = $default;
            }
        }

        return $item;
    }

    /**
     * 排除指定参数获取
     * @access public
     * @param  string|array  $name 变量名
     * @return mixed
     */
    public function except($name)
    {
        $param = $this->param;
        if (is_string($name)) {
            $name = explode(',', $name);
        }

        foreach ($name as $key) {
            if (isset($param[$key])) {
                unset($param[$key]);
            }
        }

        return $param;
    }


    /**
     * 设置当前的模块名
     * @access public
     * @param  string $module 模块名
     * @return $this
     */
    public function setModule($module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * 设置当前的控制器名
     * @access public
     * @param  string $controller 控制器名
     * @return $this
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * 设置当前的操作名
     * @access public
     * @param  string $action 操作名
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * 获取当前的模块名
     * @access public
     * @return string
     */
    public function module()
    {
        return $this->module ?: '';
    }

    /**
     * 获取当前的控制器名
     * @access public
     * @param  bool $convert 转换为小写
     * @return string
     */
    public function controller($convert = false)
    {
        $name = $this->controller ?: '';
        return $convert ? strtolower($name) : $name;
    }

    /**
     * 获取当前的操作名
     * @access public
     * @param  bool $convert 转换为驼峰
     * @return string
     */
    public function action($convert = false)
    {
        $name = $this->action ?: '';
        return $convert ? $name : strtolower($name);
    }

    /**
     * 获取客户端IP地址
     * @access public
     * @param  integer   $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @return mixed
     */
    public function ip($type = 0)
    {
        $type      = $type ? 1 : 0;

        if (null !== $this->ip) {
            return $this->ip[$type];
        }

        $ip = $_SERVER['REMOTE_ADDR'];

        // IP地址类型
        $ip_mode = (strpos($ip, ':') === false) ? 'ipv4' : 'ipv6';

        // IP地址合法验证
        if (filter_var($ip, FILTER_VALIDATE_IP) !== $ip) {
            $ip = ('ipv4' === $ip_mode) ? '0.0.0.0' : '::';
        }

        // 如果是ipv4地址，则直接使用ip2long返回int类型ip；如果是ipv6地址，暂时不支持，直接返回0
        $long_ip = ('ipv4' === $ip_mode) ? sprintf("%u", ip2long($ip)) : 0;

        $ip = [$ip, $long_ip];

        return $ip[$type];
    }


    public function response($data,$code,$msg)
    {
        $response = array('app' => $this->app,
            'api' => $this->api,
            'ver' => $this->ver,
            'data' => $data,
            'code' => $code,
            'msg' => $msg
        );
        return $response;
    }

    /**
     * 静态获取，对应的非静态前加上_
     * @param $method
     * @param $args
     * @return mixed
     */
    public static function __callStatic($method,$args)
    {
        $method = explode('_',$method)[1];
        $res = call_user_func_array([self::$_instance, $method], $args);
        return $res;

    }


}
