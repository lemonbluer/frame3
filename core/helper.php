<?php
/**
 * 格式化时间
 * @param string $format [description]
 */
function T($time = 0, $format = 'Y-m-d H:i:s') {
    if ($time == 0) {
        return date($format);
    }
    return date($format, $time);
}

function L($data, $level = 'info') {
    $log = json_encode($data);
    switch ($level) {
    case 'debug':
        if (config('debug_mode')) {
            echo $log . "\r\n";
            return;
        }
        break;
    case 'info':
    default:
        break;
    }
    @file_put_contents(config('log_file'), T() . "|" . $log . "\r\n", FILE_APPEND);

}

/**
 * 输入参数
 * @param [type] $name          [description]
 * @param [type] $default_value [description]
 * @param array  $opitions        扩展选项  type:数据类型
 */
function I($name = NULL, $default_value = NULL, $opitions = NULL) {
    if ($name === NULL) {
        $par = [];
        return array_merge($par, $_GET, $_POST);
    }
    $value = isset($_GET[$name]) ? $_GET[$name] : (isset($_POST[$name]) ? $_POST[$name] : $default_value);
    if (isset($opitions['len'])) {
        $value = mb_substr($value, 0, $opitions['len']);
    }
    if (isset($opitions['type'])) {
        switch ($opitions['type']) {
        case 'string':
            $value = '' . $value;
            break;
        case 'json':
            $value = json_decode($value, TRUE);
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
function M($name = '') {
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
        return header('Location:' . $url);
    }
    $resp = '<html><head></head><body><div style="text-align:center;"><h1>__msg__</h1>__jump__</div></body></html>';
    $jump = ($url == '') ? '' : '<h4>__delay__秒后自动跳转到&nbsp;&nbsp;<a href="__url__">__url__</a></h4><script type="text/javascript">setInterval(function(){location.href=\'__url__\';},(__delay__*1000));</script>';
    echo str_replace(['__jump__', '__msg__', '__delay__', '__url__'], [$jump, $msg, $delay, $url], $resp);
    return;
}

/**
 * @name 设置session
 * @param  string $key
 * @param  string $value   ＝NULL为删除
 * @param  array  $options  配置
 * @return [type]         [description]
 */
function session($key = NULL, $value = '', $options = []) {
    static $is_started = FALSE;
    if (!$is_started) {
        if (!isset($options['cookie_lifetime'])) {
            // cookie_lifetime = 0 直到浏览器关闭
            $options['cookie_lifetime'] = config('session_cookie_lifetime') ?? 0;
        }
        session_start($options);
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
    echo vdr($args);
    exit;
}

/**
 * var_dump变量(返回字符串)
 * @param  [type] $var [description]
 * @return [type]      [description]
 */
function vdr($args = NULL) {
    if (is_null($args)) {$args = func_get_args();}
    ob_start();
    echo "\n-- trace --------------------------------\n";
    $trace = debug_backtrace()[1];
    echo $trace['file'] . '(' . $trace['line'] . ")\n";
    echo "\n-- detail -------------------------------\n";
    if (is_array($args)) {
        foreach ($args as $k => $v) {
            var_dump($v);
            echo "\n-------------\n";
        }
    } else {
        var_dump($args);
    }
    $log = ob_get_clean();
    $log = highlight_string("<?php\n" . $log, TRUE);
    $log = "<pre>" . $log . "\n";
    return $log;
}

/**
 * 调试
 * @param  [type] $log       ['msg'=>'信息','trace'=>debug信息]
 * @param  string $resp_type [description]
 * @return [type]            [description]
 */
function tuning($log, $resp_type = '') {
    $resp_type = ($resp_type == '') ? config('tunning_out_put_type') : $resp_type;
    // TODO: 调试日志信息
    // file_put_contents(ROOT_PATH . DIRECTORY_SEPARATOR . 'a.log', var_export($log) . "\n", FILE_APPEND);
    switch ($resp_type) {
    case 'JSON':
        $log['logged_time'] = T();
        header('Content-type: application/json');
        return json_encode($log);
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

function assign($k, $v) {
    return \frame3\core\view::instance()->assign($k, $v);
}
/**
 * @name 获取页面
 * @param string $tpl     [description]
 * @param array  $options 'disable_bed'=> bool 不使用view/bed
 */
function V($tpl = '', $options = []) {
    echo \frame3\core\view::instance()->fetch($tpl, $options);
    return;
}

/**
 * @name 安全取值
 * @param  array  $array         [description]
 * @param  string $key           [description]
 * @param  int    $defaule_value [description]
 * @return [type]                [description]
 */
function safe($array = [], $key = '', $defaule_value = 0) {
    return isset($array[$key]) ? $array[$key] : $defaule_value;
}