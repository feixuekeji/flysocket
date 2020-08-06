<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace lib;

/**
 * Autoload.
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
