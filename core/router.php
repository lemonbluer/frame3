<?php
namespace frame3\core;

/**
 * 路由
 */
class router {

    /**
     * 解析路由，定义常量
     * @return [type] [description]
     */
    public function parse() {

        $path = preg_replace('/\/+/', '/', $_SERVER['REQUEST_URI']);
        if ($par_pos = strpos($_SERVER['REQUEST_URI'], '?')) {
            $path = substr($_SERVER['REQUEST_URI'], 0, $par_pos);
        }

        /******************************************************/
        // 扩展后缀最多为5个字符，其余丢弃
        if ($extension_pos = strrpos($path, '.')) {
            $extension = substr($path, $extension_pos + 1, 5);
            define('URL_EXTENSTION', $extension);
            if (strtolower($extension) != 'php') {
                // define('IS_STATIC', in_array(strtolower($extension), config('static_file_extension')));
                if (in_array(strtolower($extension), config('static_file_extension'))) {
                    // TODO : 设置的静态文件后缀匹配到的请求处理
                    // 已经在nginx中配置直接返回
                }
            }
            $path = substr($path, 0, strrpos($path, '.'));
        }
        /******************************************************/

        /******************************************************/
        // 定义全局公共变量
        $uri = explode('/', $path);
        $this->set_global_define($uri);
        /******************************************************/

        /******************************************************/
        // 拆分路由中的隐式参数，合并到$_GET中
        if (count($uri) > 4) {
            $par = array_slice($uri, 4);
            while ($cur = current($par)) {
                if (!isset($_GET['' . $cur])) {
                    $_GET['' . $cur] = urldecode(next($par));
                } else {
                    next($par);
                }
                next($par);
            }
        }
        /******************************************************/
    }

    public function set_global_define($uri = NULL) {

        /******************************************************/
        // 应用
        define('APP_NAME', (isset($uri[1]) && $uri[1] != '') ? $uri[1] : config('default_app_name'));
        define('APP_URL', '/' . APP_NAME);
        define('APP_PATH', realpath(CORE_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . APP_NAME));
        if (!is_dir(APP_PATH)) {
            throw new \Exception("App[" . APP_NAME . "] not exist", 33300);
        }
        define('APP_CONFIG_FILE', APP_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');
        //运行时缓存文件目录，view编译后模版存放位置
        define('RUNTIME_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . APP_NAME);
        /******************************************************/

        /******************************************************/
        // 控制器
        define('CONTROLLER_NAME', (isset($uri[2]) && $uri[2] != '') ? $uri[2] : 'index');
        define('CONTROLLER_URL', APP_URL . '/' . CONTROLLER_NAME);
        /******************************************************/

        /******************************************************/
        // 方法
        // 访问controller默认index方法可以省略不写
        define('FUNCTION_NAME', (isset($uri[3]) && $uri[3] != '') ? $uri[3] : 'index');
        define('FUNCTION_URL', CONTROLLER_URL . '/' . FUNCTION_NAME);
        /******************************************************/

        /******************************************************/
        // IS_AJAX
        define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
        /******************************************************/
    }
}