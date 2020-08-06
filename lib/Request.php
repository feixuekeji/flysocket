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
     * 当前请求app
     * @var array
     */
    protected $app;

    /**
     * 当前请求版本号
     * @var array
     */
    protected $ver;

    public function __construct(array $options = [])
    {
        $this->init($options);


    }

    public function init(array $options = [])
    {
        $this->param = $options['data'] ?? [];
        $this->api =  $options['api'] ?? '';
        $this->app =  $options['app'] ?? '';
        $this->ver =  $options['ver'] ?? '';
        $api = explode('/',$this->api);
        if (count($api) < 3)
            throw new \Exception('api  is not exists');
        $this->setModule($api[0]);
        $this->setController($api[1]);
        $this->setAction($api[2]);

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
     * 设置路由变量
     * @access public
     * @param  array         $route 路由变量
     * @return $this
     */
    public function setRouteVars(array $route)
    {
        $this->route = array_merge($this->route, $route);
        return $this;
    }

    /**
     * 获取路由参数
     * @access public
     * @param  string|false  $name 变量名
     * @param  mixed         $default 默认值
     * @param  string|array  $filter 过滤方法
     * @return mixed
     */
    public function route($name = '', $default = null, $filter = '')
    {
        return $this->input($this->route, $name, $default, $filter);
    }

    /**
     * 获取GET参数
     * @access public
     * @param  string|false  $name 变量名
     * @param  mixed         $default 默认值
     * @param  string|array  $filter 过滤方法
     * @return mixed
     */
    public function get($name = '', $default = null, $filter = '')
    {
        if (empty($this->get)) {
            $this->get = $_GET;
        }

        return $this->input($this->get, $name, $default, $filter);
    }

    /**
     * 获取POST参数
     * @access public
     * @param  string|false  $name 变量名
     * @param  mixed         $default 默认值
     * @param  string|array  $filter 过滤方法
     * @return mixed
     */
    public function post($name = '', $default = null, $filter = '')
    {
        if (empty($this->post)) {
            $this->post = !empty($_POST) ? $_POST : $this->getInputData($this->input);
        }

        return $this->input($this->post, $name, $default, $filter);
    }

    /**
     * 获取PUT参数
     * @access public
     * @param  string|false      $name 变量名
     * @param  mixed             $default 默认值
     * @param  string|array      $filter 过滤方法
     * @return mixed
     */
    public function put($name = '', $default = null, $filter = '')
    {
        if (is_null($this->put)) {
            $this->put = $this->getInputData($this->input);
        }

        return $this->input($this->put, $name, $default, $filter);
    }

    protected function getInputData($content)
    {
        if (false !== strpos($this->contentType(), 'application/json') || 0 === strpos($content, '{"')) {
            return (array) json_decode($content, true);
        } elseif (strpos($content, '=')) {
            parse_str($content, $data);
            return $data;
        }

        return [];
    }

    /**
     * 获取DELETE参数
     * @access public
     * @param  string|false      $name 变量名
     * @param  mixed             $default 默认值
     * @param  string|array      $filter 过滤方法
     * @return mixed
     */
    public function delete($name = '', $default = null, $filter = '')
    {
        return $this->put($name, $default, $filter);
    }

    /**
     * 获取PATCH参数
     * @access public
     * @param  string|false      $name 变量名
     * @param  mixed             $default 默认值
     * @param  string|array      $filter 过滤方法
     * @return mixed
     */
    public function patch($name = '', $default = null, $filter = '')
    {
        return $this->put($name, $default, $filter);
    }

    /**
     * 获取request变量
     * @access public
     * @param  string|false  $name 变量名
     * @param  mixed         $default 默认值
     * @param  string|array  $filter 过滤方法
     * @return mixed
     */
    public function request($name = '', $default = null, $filter = '')
    {
        if (empty($this->request)) {
            $this->request = $_REQUEST;
        }

        return $this->input($this->request, $name, $default, $filter);
    }

    /**
     * 获取session数据
     * @access public
     * @param  string        $name 数据名称
     * @param  string        $default 默认值
     * @return mixed
     */
    public function session($name = '', $default = null)
    {
        if (empty($this->session)) {
            $this->session = Session::get();
        }

        if ('' === $name) {
            return $this->session;
        }

        $data = $this->getData($this->session, $name);

        return is_null($data) ? $default : $data;
    }

    /**
     * 获取cookie参数
     * @access public
     * @param  string        $name 变量名
     * @param  string        $default 默认值
     * @param  string|array  $filter 过滤方法
     * @return mixed
     */
    public function cookie($name = '', $default = null, $filter = '')
    {
        if (empty($this->cookie)) {
            $this->cookie = Cookie::get();
        }

        if (!empty($name)) {
            $data = Cookie::has($name) ? Cookie::get($name) : $default;
        } else {
            $data = $this->cookie;
        }

        // 解析过滤器
        $filter = $this->getFilter($filter, $default);

        if (is_array($data)) {
            array_walk_recursive($data, [$this, 'filterValue'], $filter);
            reset($data);
        } else {
            $this->filterValue($data, $name, $filter);
        }

        return $data;
    }

    /**
     * 获取server参数
     * @access public
     * @param  string        $name 数据名称
     * @param  string        $default 默认值
     * @return mixed
     */
    public function server($name = '', $default = null)
    {
        if (empty($name)) {
            return $this->server;
        } else {
            $name = strtoupper($name);
        }

        return isset($this->server[$name]) ? $this->server[$name] : $default;
    }

    /**
     * 获取上传的文件信息
     * @access public
     * @param  string   $name 名称
     * @return null|array|\think\File
     */
    public function file($name = '')
    {
        if (empty($this->file)) {
            $this->file = isset($_FILES) ? $_FILES : [];
        }

        $files = $this->file;
        if (!empty($files)) {
            if (strpos($name, '.')) {
                list($name, $sub) = explode('.', $name);
            }

            // 处理上传文件
            $array = $this->dealUploadFile($files, $name);

            if ('' === $name) {
                // 获取全部文件
                return $array;
            } elseif (isset($sub) && isset($array[$name][$sub])) {
                return $array[$name][$sub];
            } elseif (isset($array[$name])) {
                return $array[$name];
            }
        }

        return;
    }

    protected function dealUploadFile($files, $name)
    {
        $array = [];
        foreach ($files as $key => $file) {
            if ($file instanceof File) {
                $array[$key] = $file;
            } elseif (is_array($file['name'])) {
                $item  = [];
                $keys  = array_keys($file);
                $count = count($file['name']);

                for ($i = 0; $i < $count; $i++) {
                    if ($file['error'][$i] > 0) {
                        if ($name == $key) {
                            $this->throwUploadFileError($file['error'][$i]);
                        } else {
                            continue;
                        }
                    }

                    $temp['key'] = $key;

                    foreach ($keys as $_key) {
                        $temp[$_key] = $file[$_key][$i];
                    }

                    $item[] = (new File($temp['tmp_name']))->setUploadInfo($temp);
                }

                $array[$key] = $item;
            } else {
                if ($file['error'] > 0) {
                    if ($key == $name) {
                        $this->throwUploadFileError($file['error']);
                    } else {
                        continue;
                    }
                }

                $array[$key] = (new File($file['tmp_name']))->setUploadInfo($file);
            }
        }

        return $array;
    }

    protected function throwUploadFileError($error)
    {
        static $fileUploadErrors = [
            1 => 'upload File size exceeds the maximum value',
            2 => 'upload File size exceeds the maximum value',
            3 => 'only the portion of file is uploaded',
            4 => 'no file to uploaded',
            6 => 'upload temp dir not found',
            7 => 'file write error',
        ];

        $msg = $fileUploadErrors[$error];

        throw new Exception($msg);
    }

    /**
     * 获取环境变量
     * @access public
     * @param  string        $name 数据名称
     * @param  string        $default 默认值
     * @return mixed
     */
    public function env($name = '', $default = null)
    {
        if (empty($name)) {
            return $this->env;
        } else {
            $name = strtoupper($name);
        }

        return isset($this->env[$name]) ? $this->env[$name] : $default;
    }

    /**
     * 获取当前的Header
     * @access public
     * @param  string   $name header名称
     * @param  string   $default 默认值
     * @return string|array
     */
    public function header($name = '', $default = null)
    {
        if (empty($this->header)) {
            $header = [];
            if (function_exists('apache_request_headers') && $result = apache_request_headers()) {
                $header = $result;
            } else {
                $server = $this->server;
                foreach ($server as $key => $val) {
                    if (0 === strpos($key, 'HTTP_')) {
                        $key          = str_replace('_', '-', strtolower(substr($key, 5)));
                        $header[$key] = $val;
                    }
                }
                if (isset($server['CONTENT_TYPE'])) {
                    $header['content-type'] = $server['CONTENT_TYPE'];
                }
                if (isset($server['CONTENT_LENGTH'])) {
                    $header['content-length'] = $server['CONTENT_LENGTH'];
                }
            }
            $this->header = array_change_key_case($header);
        }

        if ('' === $name) {
            return $this->header;
        }

        $name = str_replace('_', '-', strtolower($name));

        return isset($this->header[$name]) ? $this->header[$name] : $default;
    }

    /**
     * 递归重置数组指针
     * @access public
     * @param array $data 数据源
     * @return void
     */
    public function arrayReset(array &$data)
    {
        foreach ($data as &$value) {
            if (is_array($value)) {
                $this->arrayReset($value);
            }
        }
        reset($data);
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
     * 是否存在某个请求参数
     * @access public
     * @param  string    $name 变量名
     * @param  string    $type 变量类型
     * @param  bool      $checkEmpty 是否检测空值
     * @return mixed
     */
    public function has($name, $type = 'param', $checkEmpty = false)
    {
        if (!in_array($type, ['param', 'get', 'post', 'request', 'put', 'patch', 'file', 'session', 'cookie', 'env', 'header', 'route'])) {
            return false;
        }

        if (empty($this->$type)) {
            $param = $this->$type();
        } else {
            $param = $this->$type;
        }

        // 按.拆分成多维数组进行判断
        foreach (explode('.', $name) as $val) {
            if (isset($param[$val])) {
                $param = $param[$val];
            } else {
                return false;
            }
        }

        return ($checkEmpty && '' === $param) ? false : true;
    }

    /**
     * 获取指定的参数
     * @access public
     * @param  string|array  $name 变量名
     * @param  string        $type 变量类型
     * @return mixed
     */
    public function only($name, $type = 'param')
    {
        $param = $this->$type();

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
     * @param  string        $type 变量类型
     * @return mixed
     */
    public function except($name, $type = 'param')
    {
        $param = $this->$type();
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
     * 当前是否ssl
     * @access public
     * @return bool
     */
    public function isSsl()
    {
        if ($this->server('HTTPS') && ('1' == $this->server('HTTPS') || 'on' == strtolower($this->server('HTTPS')))) {
            return true;
        } elseif ('https' == $this->server('REQUEST_SCHEME')) {
            return true;
        } elseif ('443' == $this->server('SERVER_PORT')) {
            return true;
        } elseif ('https' == $this->server('HTTP_X_FORWARDED_PROTO')) {
            return true;
        } elseif ($this->config['https_agent_name'] && $this->server($this->config['https_agent_name'])) {
            return true;
        }

        return false;
    }

    /**
     * 当前是否Ajax请求
     * @access public
     * @param  bool $ajax  true 获取原始ajax请求
     * @return bool
     */
    public function isAjax($ajax = false)
    {
        $value  = $this->server('HTTP_X_REQUESTED_WITH');
        $result = 'xmlhttprequest' == strtolower($value) ? true : false;

        if (true === $ajax) {
            return $result;
        }

        $result           = $this->param($this->config['var_ajax']) ? true : $result;
        $this->mergeParam = false;
        return $result;
    }

    /**
     * 当前是否Pjax请求
     * @access public
     * @param  bool $pjax  true 获取原始pjax请求
     * @return bool
     */
    public function isPjax($pjax = false)
    {
        $result = !is_null($this->server('HTTP_X_PJAX')) ? true : false;

        if (true === $pjax) {
            return $result;
        }

        $result           = $this->param($this->config['var_pjax']) ? true : $result;
        $this->mergeParam = false;
        return $result;
    }

    /**
     * 获取客户端IP地址
     * @access public
     * @param  integer   $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param  boolean   $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    public function ip($type = 0, $adv = true)
    {
        $type      = $type ? 1 : 0;
        static $ip = null;

        if (null !== $ip) {
            return $ip[$type];
        }

        $httpAgentIp = $this->config['http_agent_ip'];

        if ($httpAgentIp && $this->server($httpAgentIp)) {
            $ip = $this->server($httpAgentIp);
        } elseif ($adv) {
            if ($this->server('HTTP_X_FORWARDED_FOR')) {
                $arr = explode(',', $this->server('HTTP_X_FORWARDED_FOR'));
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim(current($arr));
            } elseif ($this->server('HTTP_CLIENT_IP')) {
                $ip = $this->server('HTTP_CLIENT_IP');
            } elseif ($this->server('REMOTE_ADDR')) {
                $ip = $this->server('REMOTE_ADDR');
            }
        } elseif ($this->server('REMOTE_ADDR')) {
            $ip = $this->server('REMOTE_ADDR');
        }

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

    /**
     * 检测是否使用手机访问
     * @access public
     * @return bool
     */
    public function isMobile()
    {
        if ($this->server('HTTP_VIA') && stristr($this->server('HTTP_VIA'), "wap")) {
            return true;
        } elseif ($this->server('HTTP_ACCEPT') && strpos(strtoupper($this->server('HTTP_ACCEPT')), "VND.WAP.WML")) {
            return true;
        } elseif ($this->server('HTTP_X_WAP_PROFILE') || $this->server('HTTP_PROFILE')) {
            return true;
        } elseif ($this->server('HTTP_USER_AGENT') && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $this->server('HTTP_USER_AGENT'))) {
            return true;
        }

        return false;
    }

    /**
     * 当前URL地址中的scheme参数
     * @access public
     * @return string
     */
    public function scheme()
    {
        return $this->isSsl() ? 'https' : 'http';
    }

    /**
     * 当前请求URL地址中的query参数
     * @access public
     * @return string
     */
    public function query()
    {
        return $this->server('QUERY_STRING');
    }

    /**
     * 设置当前请求的host（包含端口）
     * @access public
     * @param  string $host 主机名（含端口）
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * 当前请求的host
     * @access public
     * @param bool $strict  true 仅仅获取HOST
     * @return string
     */
    public function host($strict = false)
    {
        if (!$this->host) {
            $this->host = $this->server('HTTP_X_REAL_HOST') ?: $this->server('HTTP_HOST');
        }

        return true === $strict && strpos($this->host, ':') ? strstr($this->host, ':', true) : $this->host;
    }

    /**
     * 当前请求URL地址中的port参数
     * @access public
     * @return integer
     */
    public function port()
    {
        return $this->server('SERVER_PORT');
    }

    /**
     * 当前请求 SERVER_PROTOCOL
     * @access public
     * @return string
     */
    public function protocol()
    {
        return $this->server('SERVER_PROTOCOL');
    }

    /**
     * 当前请求 REMOTE_PORT
     * @access public
     * @return integer
     */
    public function remotePort()
    {
        return $this->server('REMOTE_PORT');
    }

    /**
     * 当前请求 HTTP_CONTENT_TYPE
     * @access public
     * @return string
     */
    public function contentType()
    {
        $contentType = $this->server('CONTENT_TYPE');

        if ($contentType) {
            if (strpos($contentType, ';')) {
                list($type) = explode(';', $contentType);
            } else {
                $type = $contentType;
            }
            return trim($type);
        }

        return '';
    }

    /**
     * 获取当前请求的路由信息
     * @access public
     * @param  array $route 路由名称
     * @return array
     */
    public function routeInfo(array $route = [])
    {
        if (!empty($route)) {
            $this->routeInfo = $route;
        }

        return $this->routeInfo;
    }

    /**
     * 设置或者获取当前请求的调度信息
     * @access public
     * @param  \think\route\Dispatch  $dispatch 调度信息
     * @return \think\route\Dispatch
     */
    public function dispatch($dispatch = null)
    {
        if (!is_null($dispatch)) {
            $this->dispatch = $dispatch;
        }

        return $this->dispatch;
    }

    /**
     * 获取当前请求的安全Key
     * @access public
     * @return string
     */
    public function secureKey()
    {
        if (is_null($this->secureKey)) {
            $this->secureKey = uniqid('', true);
        }

        return $this->secureKey;
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
     * 设置当前的语言
     * @access public
     * @param  string $lang 语言名
     * @return $this
     */
    public function setLangset($lang)
    {
        $this->langset = $lang;
        return $this;
    }

    /**
     * 获取当前的语言
     * @access public
     * @return string
     */
    public function langset()
    {
        return $this->langset ?: '';
    }

    /**
     * 设置或者获取当前请求的content
     * @access public
     * @return string
     */
    public function getContent()
    {
        if (is_null($this->content)) {
            $this->content = $this->input;
        }

        return $this->content;
    }

    /**
     * 获取当前请求的php://input
     * @access public
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * 生成请求令牌
     * @access public
     * @param  string $name 令牌名称
     * @param  mixed  $type 令牌生成方法
     * @return string
     */
    public function token($name = '__token__', $type = null)
    {
        $type  = is_callable($type) ? $type : 'md5';
        $token = call_user_func($type, $this->server('REQUEST_TIME_FLOAT'));

        if ($this->isAjax()) {
            header($name . ': ' . $token);
        }

        facade\Session::set($name, $token);

        return $token;
    }

    /**
     * 设置当前地址的请求缓存
     * @access public
     * @param  string $key 缓存标识，支持变量规则 ，例如 item/:name/:id
     * @param  mixed  $expire 缓存有效期
     * @param  array  $except 缓存排除
     * @param  string $tag    缓存标签
     * @return mixed
     */
    public function cache($key, $expire = null, $except = [], $tag = null)
    {
        if (!is_array($except)) {
            $tag    = $except;
            $except = [];
        }

        if (false === $key || !$this->isGet() || $this->isCheckCache || false === $expire) {
            // 关闭当前缓存
            return;
        }

        // 标记请求缓存检查
        $this->isCheckCache = true;

        foreach ($except as $rule) {
            if (0 === stripos($this->url(), $rule)) {
                return;
            }
        }

        if ($key instanceof \Closure) {
            $key = call_user_func_array($key, [$this]);
        } elseif (true === $key) {
            // 自动缓存功能
            $key = '__URL__';
        } elseif (strpos($key, '|')) {
            list($key, $fun) = explode('|', $key);
        }

        // 特殊规则替换
        if (false !== strpos($key, '__')) {
            $key = str_replace(['__MODULE__', '__CONTROLLER__', '__ACTION__', '__URL__'], [$this->module, $this->controller, $this->action, md5($this->url(true))], $key);
        }

        if (false !== strpos($key, ':')) {
            $param = $this->param();
            foreach ($param as $item => $val) {
                if (is_string($val) && false !== strpos($key, ':' . $item)) {
                    $key = str_replace(':' . $item, $val, $key);
                }
            }
        } elseif (strpos($key, ']')) {
            if ('[' . $this->ext() . ']' == $key) {
                // 缓存某个后缀的请求
                $key = md5($this->url());
            } else {
                return;
            }
        }

        if (isset($fun)) {
            $key = $fun($key);
        }

        $this->cache = [$key, $expire, $tag];
        return $this->cache;
    }

    /**
     * 读取请求缓存设置
     * @access public
     * @return array
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * 设置GET数据
     * @access public
     * @param  array $get 数据
     * @return $this
     */
    public function withGet(array $get)
    {
        $this->get = $get;
        return $this;
    }

    /**
     * 设置POST数据
     * @access public
     * @param  array $post 数据
     * @return $this
     */
    public function withPost(array $post)
    {
        $this->post = $post;
        return $this;
    }

    /**
     * 设置php://input数据
     * @access public
     * @param  string $input RAW数据
     * @return $this
     */
    public function withInput($input)
    {
        $this->input = $input;
        return $this;
    }

    /**
     * 设置文件上传数据
     * @access public
     * @param  array $files 上传信息
     * @return $this
     */
    public function withFiles(array $files)
    {
        $this->file = $files;
        return $this;
    }

    /**
     * 设置COOKIE数据
     * @access public
     * @param  array $cookie 数据
     * @return $this
     */
    public function withCookie(array $cookie)
    {
        $this->cookie = $cookie;
        return $this;
    }

    /**
     * 设置SERVER数据
     * @access public
     * @param  array $server 数据
     * @return $this
     */
    public function withServer(array $server)
    {
        $this->server = array_change_key_case($server, CASE_UPPER);
        return $this;
    }

    /**
     * 设置HEADER数据
     * @access public
     * @param  array $header 数据
     * @return $this
     */
    public function withHeader(array $header)
    {
        $this->header = array_change_key_case($header);
        return $this;
    }

    /**
     * 设置ENV数据
     * @access public
     * @param  array $env 数据
     * @return $this
     */
    public function withEnv(array $env)
    {
        $this->env = $env;
        return $this;
    }

    /**
     * 设置ROUTE变量
     * @access public
     * @param  array $route 数据
     * @return $this
     */
    public function withRoute(array $route)
    {
        $this->route = $route;
        return $this;
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
     * 检测请求数据的值
     * @access public
     * @param  string $name 名称
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->param[$name]);
    }

    public function __debugInfo()
    {
        $data = get_object_vars($this);
        unset($data['dispatch'], $data['config']);

        return $data;
    }
}
