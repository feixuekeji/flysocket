<?php


namespace lib;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

class Log
{
    private static $loggers;

    /**
     * 日志默认保存路径
     * @var string
     */
    private static $fileName = __DIR__.'/../data/logs/';

    /**
     * 日志留存时间
     * @var int
     */
    private static $maxFiles = 31;

    /**
     * 日志等级
     * @var int
     */
    private static $level = Logger::DEBUG;

    /**
     * 文件读写权限分配
     * 0666 保证log日志文件可以被其他用户/进程读写
     * @var int
     */
    private static $filePermission = 0666;

    /**
     * monolog日志
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $logger = self::createLogger($name);

        $message         = empty($arguments[0]) ? ''    : $arguments[0];
        $context         = empty($arguments[1]) ? []    : $arguments[1];
        $levelName       = empty($arguments[2]) ? $name : $arguments[2];
        $backtraceOffset = empty($arguments[3]) ? 0     : intval($arguments[3]);


        $level = Logger::toMonologLevel($levelName);
        if (!is_int($level)) $level = Logger::INFO;

        // $backtrace数组第$idx元素是当前行，第$idx+1元素表示上一层，另外function、class需再往上取一个层次
        // PHP7 不会包含'call_user_func'与'call_user_func_array'，需减少一层
        if (version_compare(PCRE_VERSION, '7.0.0', '>=')) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            $idx       = 0 + $backtraceOffset;
        } else {
            $backtrace = debug_backtrace();
            $idx       = 1 + $backtraceOffset;
        }

        $trace = basename($backtrace[$idx]['file']) . ":" . $backtrace[$idx]['line'];
        if (!empty($backtrace[$idx + 1]['function'])) {
            $trace .= '##';
            $trace .= $backtrace[$idx + 1]['function'];
        }

        $message = sprintf('==> LOG: %s -- %s', $message, $trace);

        return $logger->addRecord($level, $message, $context);
    }

    /**
     * 创建日志
     * @param $name
     * @return mixed
     */
    private static function createLogger($name)
    {
        if (empty(self::$loggers[$name])) {
            // 根据业务域名与方法名进行日志名称的确定
            $category       = 'test';
            // 日志文件目录
            $fileName       = self::$fileName;
            // 日志保存时间
            $maxFiles       = self::$maxFiles;
            // 日志等级
            $level          = self::$level;
            // 权限
            $filePermission = self::$filePermission;

            // 创建日志
            $logger    = new Logger($category);
            // 日志文件相关操作
            $handler   = new RotatingFileHandler("{$fileName}{$name}.log", $maxFiles, $level, true, $filePermission);
            // 日志格式
            $formatter = new LineFormatter("%datetime% %channel%:%level_name% %message% %context% %extra%\n", "Y-m-d H:i:s", false, true);

            $handler->setFormatter($formatter);
            $logger->pushHandler($handler);

            self::$loggers[$name] = $logger;
        }
        return self::$loggers[$name];
    }
}