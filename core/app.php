<?php
namespace frame3\core;
/**
 *
 */
class app {

    /**
     * 应用启动
     * @return [type] [description]
     */
    public function start() {
        // step1.解析路由
        (new router())->parse();
        // step2.加载app的个性化设置
        config(include APP_CONFIG_FILE);
        // step3.注册app类加载器
        spl_autoload_register('self::app_auto_loader');
        try {
            // step4.初始化对应controller
            $c = new \ReflectionClass('frame3\\' . APP_NAME . '\\' . CONTROLLER_NAME);
            $c_instance = $c->newInstance();
            $f = $c->getMethod(FUNCTION_NAME);
            // step5.执行对应方法
            if ($f->isPublic()) {
                $f->invoke($c_instance);
            }
        } catch (\ReflectionException $e) {
            // 控制器不存在
            if ($e->getCode() == -1) {
                throw new \Exception("controller[" . CONTROLLER_NAME . "] not found in app[" . APP_NAME . "]", 33301);
            } elseif ($e->getCode() == 0) {
                // 控制器中没有找到对应方法
                throw new \Exception('method[' . FUNCTION_NAME . '] not fount in controller[' . CONTROLLER_NAME . ']', 33302);
            } else {
                throw new \Exception("Reflection ERROR", 33303);
            }
        }
    }

    // TODO:日志记录
    public function log() {
        echo "log someting \n";
    }

    /**
     * app内自动加载器
     * @param  [type] $class [description]
     * @return [type]        [description]
     */
    public function app_auto_loader($class) {
        $class = ltrim($class, '\\');
        $name_space = $file_name = '';
        if ($class_name_pos = strrpos($class, '\\')) {
            $name_space = substr($class, 0, $class_name_pos);
            $class_name = substr($class, $class_name_pos + 1);
            if (strrpos($class, '\\lib\\')) {
                // app目录内lib自动加载
                $file_name = str_replace(['frame3\\' . APP_NAME . '\\lib', '\\'], [APP_PATH . '\\lib', DIRECTORY_SEPARATOR], $name_space) . DIRECTORY_SEPARATOR . $class_name . '.php';
                if (!is_file($file_name)) {
                    $file_name = str_replace(['frame3\\' . APP_NAME . '\\lib', '\\'], [CORE_PATH . '\\lib', DIRECTORY_SEPARATOR], $name_space) . DIRECTORY_SEPARATOR . $class_name . '.php';
                }
            } elseif (strrpos($class, '\\model\\')) {
                // app内controller\lib\model自动加载
                $file_name = str_replace(['frame3\\' . APP_NAME . '\\model', '\\'], [APP_PATH . '\\model', DIRECTORY_SEPARATOR], $name_space) . DIRECTORY_SEPARATOR . $class_name . '.php';
            } else {
                // app目录内controller自动加载
                $file_name = str_replace(['frame3\\' . APP_NAME, '\\'], [APP_PATH, DIRECTORY_SEPARATOR], $name_space) . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $class_name . '.php';
            }
        }
        if (is_file($file_name)) {
            include $file_name;
        } else {
            // TODO
            // echo 'app class loader failed : ' . $file_name . '||class:' . $class;
        }
    }
}