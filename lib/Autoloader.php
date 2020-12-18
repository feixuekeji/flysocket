<?php
namespace lib;

/**
 * 自动加载
 */
class Autoloader
{
    public static function load($className)
    {
        $classPath = str_replace('\\', '/', $className);
        $classFile = __DIR__ .'/../'.$classPath.'.php';
        if (is_file($classFile)) {
            require_once($classFile);
            if (class_exists($className, false)) {
                return true;
            }
        }
        return false;
    }
}

spl_autoload_register('\lib\Autoloader::load');
