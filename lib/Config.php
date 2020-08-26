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
    static function load($file){
        if (!isset(self::$config[$file])){
            $confFile = __DIR__ . '/../config/' . $file .'.php';
            if (is_file($confFile)){
                self::$config[$file] = include_once $confFile;
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


    /**
     *获取配置参数 为空则获取所有配置
     * @param null $name
     * @param string $file
     * @param null $default
     * @return |null
     * @author xingxiong.fei@163.com
     * @date 2020-08-26 16:22
     */
    public static function get($name = null,$file = 'config', $default = null)
    {
        self::load($file);
        // 无参数时获取所有
        if (empty($name)) {
            return self::$config[$file];
        }
        $name    = explode('.', $name);
        $name[0] = strtolower($name[0]);
        $config  = self::$config[$file];
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