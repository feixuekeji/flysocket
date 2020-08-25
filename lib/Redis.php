<?php


namespace lib;


class Redis
{

    private static $_instance; //存储对象
    private function __construct( ){
        $config = Config::get('redis');
        self::$_instance = new \Redis();
        //从配置读取
        self::$_instance->pconnect($config['host'], $config['port']);
        self::$_instance->auth($config['password']);
    }

    public static function getInstance( )
    {
        if (!self::$_instance) {
           new self();
        }
        return self::$_instance;
}


    /**
    * 禁止clone
    */
    private function __clone(){}

    /**
     * 其他方法自动调用
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method,$args)
    {
        return call_user_func_array([self::$_instance, $method], $args);
    }

    /**
     * 静态调用
     * @param $method
     * @param $args
     * @return mixed
     */
    public static function __callStatic($method,$args)
    {
        return call_user_func_array([self::$_instance, $method], $args);
    }



}