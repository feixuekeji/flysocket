<?php


namespace lib;

use Throwable;


class Error
{
    /**
     * 配置参数
     * @var array
     */
    protected static $exceptionHandler;

    /**
     * 注册异常处理
     * @access public
     * @return void
     */
    public static function register()
    {
        error_reporting(E_ALL);
        set_error_handler([__CLASS__, 'error']);
        set_exception_handler([__CLASS__, 'exception']);
        register_shutdown_function([__CLASS__, 'shutdown']);
    }

    /**
     *
     * @param $e
     * @author xingxiong.fei@163.com
     * @date 2020-09-02 17:36
     */
    public static function exception($e)
    {
        self::report($e);
    }

    public static function report(Throwable $exception)
    {
        $data = [
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
            'message' => $exception->getMessage(),
            'code'    => $exception->getCode(),
        ];
        \lib\facade\Log::error('错误信息',$data);
        \lib\facade\Log::error('错误跟踪',$exception->getTrace());
    }

    /**
     * Error Handler
     * @access public
     * @param  integer $errno   错误编号
     * @param  integer $errstr  详细错误信息
     * @param  string  $errfile 出错的文件
     * @param  integer $errline 出错行号
     * @throws ErrorException
     */
    public static function error($errno, $errstr, $errfile = '', $errline = 0): void
    {
        $data = [
            'file'    => $errfile,
            'line'    =>$errline,
            'message' => $errstr,
            'code'    => $errno,
        ];
        \lib\facade\Log::error('错误信息',$data);

    }

    public static function errorLog(\Error $error): void
    {
        $data = [
            'file'    => $error->getFile(),
            'line'    => $error->getLine(),
            'message' => $error->getMessage(),
            'code'    => $error->getCode(),
        ];
        \lib\facade\Log::error('错误信息',$data);
    }

    /**
     * Shutdown Handler
     * @access public
     */
    public static function shutdown()
    {

        if (!is_null($error = error_get_last()) && self::isFatal($error['type'])) {

            self::error($error);
        }
    }

    /**
     * 确定错误类型是否致命
     *
     * @access protected
     * @param  int $type
     * @return bool
     */
    protected static function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }


}
