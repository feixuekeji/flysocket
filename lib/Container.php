<?php
namespace worker\base;


class Container
{
    private $container = [];

    private static $instance;

    private function __construct()
    {

    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function getInstance(){

        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __set($name, $value)
    {
        // TODO: Implement __set() method.
        $this->container[$name] = $value;
    }

    public function __get($name)
    {
        // TODO: Implement __get() method.
        return $this->container[$name];
    }

    public static function make($class){
        $self = self::getInstance();
        if(array_key_exists($class,$self->container)){
            return new $self->container[$class];
        }
        if (is_object(new $class)){
            $self->bind([$class=>$class]);
        }
        return new $class;
    }

    public static function __callStatic($name, $arguments)
    {
        // TODO: Implement __callStatic() method.
        self::make($name);

    }

    public function bind($data){
        if (is_array($data)){
            foreach ($data as $k => $v){
                self::getInstance()->container[$k] = $v;
            }
        }
        return null;
    }

    /**
     * @return array
     */
    public function getContainer(): array
    {
        return $this->container;
    }

}