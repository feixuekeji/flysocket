<?php


namespace lib;


class Redis
{

    private static $_instance; //存储对象
    private function __construct( ){
        $config = Config::get('redis');
        self::$_instance = new \Redis();
        //从配置读取
        self::$_instance->connect($config['host'], $config['port']);
        if ('' != $config['password']) {
            self::$_instance->auth($config['password']);
        }

    }




    public static function getInstance( )
    {
        if (!self::$_instance) {
            new self();
        }
        else{
            try {
                @trigger_error('flag', E_USER_NOTICE);
                self::$_instance->ping();
                $error = error_get_last();
                if($error['message'] != 'flag')
                    throw new \Exception('Redis server went away');
            } catch (\Exception $e) {
                // 断线重连
                new self();
            }
        }
        return self::$_instance;
    }

//    public static function getInstance( )
//    {
//        try {
//            if (!self::$_instance) {
//                new self();
//            } else {
//                if (!self::$_instance->ping())
//                    new self();
//            }
//        } catch (\Exception $e) {
//            // 断线重连
//            new self();
//        }
//        return self::$_instance;
//    }



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
        self::getInstance();
        return call_user_func_array([self::$_instance, $method], $args);
    }



}