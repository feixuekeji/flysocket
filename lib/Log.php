<?php
namespace lib;
use Psr\Log\LoggerInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class Log implements LoggerInterface
{

    protected $loggers;


    /**
     * 是否允许日志写入
     * @var bool
     */
    protected $allowWrite = true;

    protected $config = [
        'path'        => '',
        // 日志通道名
        'channel'        => 'app',
        'level' => 'debug',
        'max_files'   => 0,
        'file_permission'  => 0666,
        'sql_level'  => 'info',
    ];

    // 实例化并传入参数
    public function __construct($config = [])
    {

        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }
        if (!empty($config['close'])) {
            $this->allowWrite = false;
        }

        if (empty($this->config['path'])) {
            $this->config['path'] = \lib\facade\App::getRootPath() . 'data/logs' . DIRECTORY_SEPARATOR;
        } elseif (substr($this->config['path'], -1) != DIRECTORY_SEPARATOR) {
            $this->config['path'] .= DIRECTORY_SEPARATOR;
        }
    }



    private function createLogger($name,$fileName = '')
    {
        if (empty($this->loggers[$name.$fileName])) {
            // 根据业务域名与方法名进行日志名称的确定
            $channel       = $this->config['channel'];
            // 日志文件目录
            $path       = $this->config['path'];
            // 日志保存时间
            $maxFiles       = $this->config['max_files'];
            // 日志等级
            $level = Logger::toMonologLevel($this->config['level']);
            // 权限
            $filePermission =  $this->config['file_permission'];
            // 创建日志
            $logger    = new Logger($channel);
            // 日志文件相关操作
            $logFileName = empty($fileName) ? $name : $name . '-' .$fileName;
            $handler   = new RotatingFileHandler("{$path}{$logFileName}.log", $maxFiles, $level, true, $filePermission);
            // 日志格式
            $formatter = new LineFormatter("%datetime% %channel%:%level_name% %message% %context% %extra%\n", "Y-m-d H:i:s", false, true);

            $handler->setFormatter($formatter);
            $logger->pushHandler($handler);

            $this->loggers[$name.$fileName] = $logger;
        }
        return $this->loggers[$name.$fileName];
    }



    /**
     * 记录日志信息
     * @access public
     * @param  mixed  $message       日志信息
     * @param  string $level      日志级别
     * @param  array  $context   替换内容
     * @param string $fileName  文件名
     * @return $this
     */
    public function record($message, $level = 'info', array $context = [],$fileName = '')
    {
        if (!$this->allowWrite) {
            return;
        }
        $logger = $this->createLogger($level,$fileName);
        $level = Logger::toMonologLevel($level);
        if (!is_int($level)) $level = Logger::INFO;
        // $backtrace数组第$idx元素是当前行，第$idx+1元素表示上一层，另外function、class需再往上取一个层次
        // PHP7 不会包含'call_user_func'与'call_user_func_array'，需减少一层
        if (version_compare(PCRE_VERSION, '7.0.0', '>=')) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            $idx       = 0;
        } else {
            $backtrace = debug_backtrace();
            $idx       = 1;
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
     * 记录日志信息
     * @access public
     * @param  string $level     日志级别
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function log($level, $message, array $context = [], $fileName = '')
    {
        if ($level == 'sql')
            $level = $this->config['sql_level'];
        $this->record($message, $level, $context, $fileName);
    }

    /**
     * 记录emergency信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function emergency($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录警报信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function alert($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录紧急情况
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function critical($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录错误信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function error($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录warning信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function warning($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录notice信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * * @param string $fileName
     * @return void
     */
    public function notice($message, array $context = [],$fileName = '')
    {
        $this->log(__FUNCTION__, $message, $context, $fileName);
    }

    /**
     * 记录一般信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @param string $fileName
     * @return void
     */

    public function info($message, array $context = [],$fileName = '')
    {
        $this->log(__FUNCTION__, $message, $context, $fileName);
    }

    /**
     * 记录调试信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function debug($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录sql信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function sql($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

}
