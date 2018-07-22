<?php
namespace frame3\core;
/**
 *
 */
class frame3 {

    public static function start() {
        // step.1 加载工具函数
        include CORE_PATH . DIRECTORY_SEPARATOR . 'helper.php';
        // step.2 加载默认配置
        config(include DEFAULT_CONFIG_FILE);
        // step.3 接管php异常处理，错误、exception等
        self::handler_register();
        // step.4 错误码
        // global static $__ERR_CODE = 33300;
        // step4.类自动加载器
        include CORE_PATH . '/loader.php';
        (new loader())->init();
        //step5.启动应用
        (new app())->start();
    }

    /**
     * 自定义接管
     * @return [type] [description]
     */
    public static function handler_register() {
        set_exception_handler('\frame3\core\frame3::exception_handler'); // 异常处理
        set_error_handler('\frame3\core\frame3::error_handler', E_ALL); // 错误处理
        register_shutdown_function('\frame3\core\frame3::shutdown');
    }

    /**
     * 接管异常
     * @param  [type] $e [description]
     * @return [type]    [description]
     */
    public static function exception_handler($e) {
        $exp_code = $e->getCode();
        if (config('app_online')) {
            switch ($exp_code) {
            case 33301:
                http_response_code(404);
                R('/', '当前页面被外星人拿走了!', 2);
                break;
            default:
                http_response_code(404);
                R('/', '当前页面被外星人拿走了!', 2);
                break;
            }
            return;
        } else {
            if (config('debug_mode')) {
                $msg = '捕获异常:' . $e->getMessage() . "\n<br/>File: " . $e->getFile() . '(' . $e->getLine() . ")";
                echo tuning(['code' => -1, 'msg' => $msg, 'trace' => $e->getTrace()]);
                L(['type' => 'EXCEPTION', 'msg' => $msg, 'server' => $_SERVER]);
            }
        }
    }

    // php报错处理
    /**
     * 接管报错
     * @param  int    $errno      [description]
     * @param  string $errstr     [description]
     * @param  string $errfile    [description]
     * @param  int    $errline    [description]
     * @param  array  $errcontext [description]
     * @return [type]             [description]
     */
    public static function error_handler(int $errno, string $errstr, string $errfile, int $errline, array $errcontext) {
        // $e = error_get_last();
        // vd(T() . __METHOD__ . '捕获出错', ['errno' => $errno, 'errstr' => $errstr, 'errfile' => $errfile, 'errline' => $errline, 'errcontext' => $errcontext]);
        $msg = "Fatal Error ({$errno}): {$errstr}<br>File:{$errfile}:{$errline} ";
        echo tuning(['code' => -1, 'msg' => $msg, 'errcontext' => $errcontext, 'trace' => debug_backtrace()]);
        L(['type' => 'FATAL', 'msg' => $msg, 'errcontext' => $errcontext, 'server' => $_SERVER]);
        die();
    }

    /**
     * exit 之后
     * @return [type] [description]
     */
    public static function shutdown() {
        $e = error_get_last();
        if (!is_null($e)) {
            vd(T() . '捕获异常:' . $e->getMessage(), $e->getFile() . '(' . $e->getLine() . ')', $e);
        }
        // vd(T() . ' shutting down');
    }
}