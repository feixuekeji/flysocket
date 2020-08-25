<?php
namespace lib;

/**
 * 读取配置文件
 * Class Config
 * @package lib
 */
class Config
{
    protected static $config;

    // 加载配置文件
    static function load(){
        if (!self::$config){
            $confFile = __DIR__ . '/../config/config.php';
            if (is_file($confFile)){
                self::$config = include_once $confFile;
            }
        }

    }



    /**
     * 获取配置参数 为空则获取所有配置
     * @access public
     * @param  string    $name      配置参数名（支持多级配置 .号分割）
     * @param  mixed     $default   默认值
     * @return mixed
     */
    public static function get($name = null, $default = null)
    {
        self::load();
        // 无参数时获取所有
        if (empty($name)) {
            return self::$config;
        }
        $name    = explode('.', $name);
        $name[0] = strtolower($name[0]);
        $config  = self::$config;
        // 按.拆分成多维数组进行判断
        foreach ($name as $val) {
            if (isset($config[$val])) {
                $config = $config[$val];
            } else {
                return $default;
            }
        }
        return $config;
    }


}