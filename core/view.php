<?php
namespace frame3\core;
/**
 *  页面
 */
class view {

    private static $_instance = NULL;
    private $_assign;
    private $_tpl;

    private function __construct($tpl = '') {}
    private function __clone() {}
    /**
     * @name 获取view实例
     * @return [type] [description]
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new view();
        }
        return self::$_instance;
    }

    /**
     * @name 设置变量
     * @param  string $k [description]
     * @param  string $v [description]
     * @return [type]    [description]
     */
    public function assign($k = '', $v = '') {
        if (is_array($k)) {
            $this->_assign = array_merge($this->_assign_array, $k);
        } else {
            $this->_assign[$k] = $v;
        }
        return TRUE;
    }

    public function fetch($tpl = '') {
        /******************************************************/
        // step1.拼模版路径
        if (strrpos($tpl, DIRECTORY_SEPARATOR) !== FALSE) {
            $this->_tpl = $tpl;
        } else {
            $tpl = ($tpl == '') ? debug_backtrace()[2]['function'] : $tpl;
            $this->_tpl = CONTROLLER_NAME . DIRECTORY_SEPARATOR . $tpl;
        }
        $this->_tpl = APP_PATH . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $this->_tpl . '.html';
        if (!is_file($this->_tpl)) {
            throw new \Exception("tpl file(" . $this->_tpl . ") not found", 1);
        }
        /******************************************************/

        /******************************************************/
        // step2.展开assign变量
        extract($this->_assign);
        /******************************************************/

        /******************************************************/
        // step3.输出模版
        ob_start();
        include $this->_tpl;
        return ob_get_clean();
        /******************************************************/
    }
}