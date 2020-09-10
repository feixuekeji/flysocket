<?php

namespace lib;

/**
 *
 * Class Session
 * @package lib
 * @author xingxiong.fei@163.com
 * @date 2020-09-10 14:26
 */
class Session
{
    /**
     * 配置参数
     * @var array
     */
    protected $config = [];

    /**
     * 前缀
     * @var string
     */
    protected $prefix = '';

    /**
     * 是否初始化
     * @var bool
     */
    protected $init = null;


    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * 设置或者获取session作用域（前缀）
     * @access public
     * @param  string $prefix
     * @return string|void
     */
    public function prefix($prefix = '')
    {
        if (empty($prefix) && null !== $prefix) {
            return $this->prefix;
        } else {
            $this->prefix = $prefix;
        }
    }

    public static function __make(Config $config)
    {
        return new static($config->get('','session'));
    }

    /**
     * 配置
     * @access public
     * @param  array $config
     * @return void
     */
    public function setConfig(array $config = [])
    {
        $this->config = array_merge($this->config, array_change_key_case($config));

        if (isset($config['prefix'])) {
            $this->prefix = $config['prefix'];
        }
    }


    /**
     * session初始化
     * @access public
     * @param  array $config
     * @return void
     * @throws \think\Exception
     */
    public function init(array $config = [])
    {
        $config = $config ?: $this->config;

        if (isset($config['prefix'])) {
            $this->prefix = $config['prefix'];
        }
        $this->init = true;
        return $this;
    }

    /**
     *设置session
     * @param $name
     * @param $value
     * @param null $prefix
     * @throws \think\Exception
     * @date 2020-09-10 14:26
     */
    public function set($name, $value, $prefix = null)
    {
        is_null($this->init) && $this->init();
        $prefix = !is_null($prefix) ? $prefix : $this->prefix;
        if (strpos($name, '.')) {
            // 二维数组赋值
            list($name1, $name2) = explode('.', $name);
            if ($prefix) {
                $_SESSION[$prefix][$name1][$name2] = $value;
            } else {
                $_SESSION[$name1][$name2] = $value;
            }
        } elseif ($prefix) {
            $_SESSION[$prefix][$name] = $value;
        } else {
            $_SESSION[$name] = $value;
        }
    }

    /**
     *获取
     * @param string $name
     * @param null $prefix
     * @return array|mixed|null
     * @throws \think\Exception
     * @date 2020-09-10 14:26
     */
    public function get($name = '', $prefix = null)
    {

        is_null($this->init) && $this->init();

        $prefix = !is_null($prefix) ? $prefix : $this->prefix;

        $value = $prefix ? (!empty($_SESSION[$prefix]) ? $_SESSION[$prefix] : []) : $_SESSION;

        if ('' != $name) {
            $name = explode('.', $name);

            foreach ($name as $val) {
                if (isset($value[$val])) {
                    $value = $value[$val];
                } else {
                    $value = null;
                    break;
                }
            }
        }
        return $value;
    }


    /**
     *获取后删除
     * @param $name
     * @param null $prefix
     * @return array|mixed|void|null
     * @throws \think\Exception
     * @date 2020-09-10 14:27
     */
    public function pull($name, $prefix = null)
    {
        $result = $this->get($name, $prefix);

        if ($result) {
            $this->delete($name, $prefix);
            return $result;
        } else {
            return;
        }
    }


    /**
     *删除
     * @param $name
     * @param null $prefix
     * @throws \think\Exception
     * @author xingxiong.fei@163.com
     * @date 2020-09-10 14:24
     */
    public function delete($name, $prefix = null)
    {
        is_null($this->init) && $this->init();

        $prefix = !is_null($prefix) ? $prefix : $this->prefix;

        if (is_array($name)) {
            foreach ($name as $key) {
                $this->delete($key, $prefix);
            }
        } elseif (strpos($name, '.')) {
            list($name1, $name2) = explode('.', $name);
            if ($prefix) {
                unset($_SESSION[$prefix][$name1][$name2]);
            } else {
                unset($_SESSION[$name1][$name2]);
            }
        } else {
            if ($prefix) {
                unset($_SESSION[$prefix][$name]);
            } else {
                unset($_SESSION[$name]);
            }
        }
    }

    /**
     *清空
     * @param null $prefix
     * @throws \think\Exception
     * @author xingxiong.fei@163.com
     * @date 2020-09-10 14:25
     */
    public function clear($prefix = null)
    {
        is_null($this->init) && $this->init();
        $prefix = !is_null($prefix) ? $prefix : $this->prefix;

        if ($prefix) {
            unset($_SESSION[$prefix]);
        } else {
            $_SESSION = [];
        }
    }

    /**
     *判断是否存在
     * @param $name
     * @param null $prefix
     * @return bool
     * @throws \think\Exception
     * @author xingxiong.fei@163.com
     * @date 2020-09-10 14:22
     */
    public function has($name, $prefix = null)
    {
        is_null($this->init) && $this->init();
        $prefix = !is_null($prefix) ? $prefix : $this->prefix;
        $value  = $prefix ? (!empty($_SESSION[$prefix]) ? $_SESSION[$prefix] : []) : $_SESSION;

        $name = explode('.', $name);

        foreach ($name as $val) {
            if (!isset($value[$val])) {
                return false;
            } else {
                $value = $value[$val];
            }
        }
        return true;
    }

    /**
     *追加到session数组
     * @param $key
     * @param $value
     * @throws \think\Exception
     * @date 2020-09-10 14:28
     */
    public function push($key, $value)
    {
        $array = $this->get($key);

        if (is_null($array)) {
            $array = [];
        }

        $array[] = $value;

        $this->set($key, $array);
    }

}
