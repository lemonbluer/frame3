<?php
/**
 * 格式化时间
 * @param string $format [description]
 */
function T($time = 0, $format = 'Y-m-d H:i:s') {
    if ($time == 0) {
        $time = time();
    }
    return date($format, $time);
}

function L($data) {
    echo $data;
}

/**
 * 输入参数
 * @param [type] $name          [description]
 * @param [type] $default_value [description]
 * @param string $type             数据类型
 */
function I($name, $default_value = '', $type = '') {
    if ($name === '') {
        $par = [];
        return array_merge($par, $_GET, $_POST);
    }
    $value = isset($_GET[$name]) ? $_GET[$name] : (isset($_POST[$name]) ? $_POST[$name] : $default_value);
    if ('' !== $type) {
        switch ($type) {
        case 'string':
            $value = '' . $value;
            break;
        case 'int':
            $value = intval($value);
            break;
        default:break;
        }
    }
    return $value;
}

/**
 * 获取配置文件中的信息
 * @param  string $v [description]
 * @return [type]    [description]
 */
function config($name = '') {
    static $__CONFIG = array();
    if ($name === '') {
        return $__CONFIG;
    } elseif (is_array($name)) {
        $__CONFIG = array_merge($__CONFIG, $name);
    } else {
        return $__CONFIG[$name] ?? null;
    }
}

/**
 * 数据模型函数
 * @param  string $name [description]
 * @return [type]       [description]
 */
function m($name = '') {
    static $model;
    if (!isset($model[$name])) {
        $model_file = APP_PATH . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR . $name . '.php';
        $class_name = '\\frame3\\' . APP_NAME . '\\model\\';
        // 默认使用app\model\base.php做为数据模型
        $class_name .= (is_file($model_file)) ? $name : 'base';
        $model[$name] = new $class_name($name);
    }
    return $model[$name]->renew();
}

// 页面跳转
function R($url = '', $msg = '', $delay = 0) {
    // TODO : 死循环页面跳出
    if ($delay == 0 && $url != '') {
        header($url);
        return;
    }
    $resp = '<html><head></head><body><div style="text-align:center;"><h1>__msg__</h1>__jump__</div></body></html>';
    $jump = ($url == '') ? '' : '<h4>__delay__秒后自动跳转到&nbsp;&nbsp;<a href="__url__">__url__</a></h4><script type="text/javascript">setInterval(function(){location.href=\'__url__\';},(__delay__*1000));</script>';
    echo str_replace(['__jump__', '__msg__', '__delay__', '__url__'], [$jump, $msg, $delay, $url], $resp);
    return;
}

/**
 * @name 设置session
 * @param  string $key   [description]
 * @param  string $value [description]
 * @return [type]        [description]
 */
function session($key = NULL, $value = '') {
    static $is_started = FALSE;
    if (!$is_started) {
        session_start([
            'cookie_lifetime' => config('session_cookie_lifetime'),
        ]);
        $is_started = TRUE;
    }
    if (is_null($key)) {
        return $_SESSION;
    }
    if (is_null($value)) {
        unset($_SESSION[$key]);
        return TRUE;
    }
    if ('' === $value) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : NULL;
    } else {
        $_SESSION[$key] = $value;
        return true;
    }
}

/**
 * var_dump变量
 * @param  [type] $var [description]
 * @return [type]      [description]
 */
function vd() {
    $args = func_get_args();
    ob_start();
    if (is_array($args)) {
        foreach ($args as $k => $v) {
            var_dump($v);
            echo "\n--------------------------------------------\n";
        }
    } else {
        var_dump($args);
    }
    $log = ob_get_clean();
    // highlight_string("<?php\n" . implode("--------------------------------------------\n", $log));
    // $trace = debug_backtrace()[2];
    // echo $trace['file'] . '(' . $trace['line'] . ")\n";
    echo "<pre>\n" . $log . "\n";
    exit;
}

/**
 * 调试
 * @param  [type] $log       ['msg'=>'信息','trace'=>debug信息]
 * @param  string $resp_type [description]
 * @return [type]            [description]
 */
function tuning($log, $resp_type = '') {
    $resp_type = ($resp_type == '') ? config('tunning_out_put_type') : $resp_type;
    switch ($resp_type) {
    case 'JSON':
        header('Content-type: application/json');
        return json_encode(['time' => T(), 'log' => $log]);
        break;
    case 'HTML':
        $trace_html = '';
        if (isset($log['trace'])) {
            ob_start();
            var_dump($log['trace']);
            $trace = ob_get_contents();
            ob_clean();
            $trace_html = highlight_string("<?php\n" . $trace, true);
        }
        return '<html><head></head><body><h1>' . $log['msg'] . '</h1><hr>' . $trace_html . '</body></html>';
        break;
    case 'RAW';
    default:
        vd(T(), $log);
        break;
    }
}
