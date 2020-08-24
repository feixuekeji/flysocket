<?php


namespace lib;


class Redis
{

    private static $_instance; //存储对象
    private function __construct($redisConfig ){

        self::$_instance = new \Redis();
        //从配置读取
        self::$_instance->pconnect($redisConfig['host'], $redisConfig['port']);
        self::$_instance->auth($redisConfig['auth']);
    }

    public static function getInstance($redisConfig )
    {
        if (!self::$_instance) {
           new self($redisConfig);
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