<?php
namespace frame3\core;
/**
 *  页面
 */
class view {

    private static $_instance = NULL;
    private $_assign;
    private $_tpl; // CONTROLLER/[...|FUNCTION_NAME]
    private $_tpl_runtime;

    private function __construct() {}
    private function __clone() {}

    /**
     * 获取view
     * @param  string $tpl [description]
     * @param  array  $options [description]
     * @return [type]      [description]
     */
    public function fetch($tpl = '', $options = []) {
        /**
         * TODO: 现在view基础模版bed.html为静态,
         *         还需要增加缓存功能，缓存view和缓存文件对应关系，
         *         过期功能
         */
        /******************************************************/
        // step1.拼模版路径   CONTROLLER/FUNCTION
        if (strrpos($tpl, DIRECTORY_SEPARATOR) !== FALSE) {
            $this->_tpl_name = $tpl;
        } else {
            $tpl = ($tpl == '') ? debug_backtrace()[2]['function'] : $tpl;
            $this->_tpl_name = CONTROLLER_NAME . DIRECTORY_SEPARATOR . $tpl;
        }
        $this->_tpl = APP_PATH . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $this->_tpl_name . '.html';
        $this->_tpl_runtime = RUNTIME_PATH . DIRECTORY_SEPARATOR . $this->_tpl_name . '.html';
        /******************************************************/

        /******************************************************/
        // step3.检查缓存 && 编译模版 && 更新缓存
        if ($this->compile() === FALSE) {return FALSE;}
        /******************************************************/

        /******************************************************/
        // step4.展开assign变量
        isset($this->_assign) && extract($this->_assign);
        /******************************************************/

        /******************************************************/
        // step5.输出模版
        ob_start();
        include $this->_tpl_runtime;
        if (!is_null(config('view_bed')) && (!isset($options['disable_bed']) || !$options['disable_bed'])) {
            $bed_content = ob_get_clean();
            // $code = ''; //未include的php拼接源码
            $this->compile(config('view_bed'));
            include RUNTIME_PATH . DIRECTORY_SEPARATOR . config('view_bed') . '.html';
            $bed = ob_get_clean();
            return str_replace('__CONTENT__', $bed_content, $bed);
        }
        return ob_get_clean();
        /******************************************************/
    }

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

    /**
     * @name 缓存是否过期
     * @return bool T:缓存没过期  F:缓存已过期
     */
    private function check_view_cache($tpl = '', $tpl_runtime = '') {

        if ($tpl == '') {$tpl = $this->_tpl;}
        if ($tpl_runtime == '') {$tpl_runtime = $this->_tpl_runtime;}

        if (config('debug_mode')) {
            return FALSE;
        }
        // step1.检测时间
        if (!is_file($tpl_runtime)) {
            return FALSE;
        }
        return (filemtime($tpl_runtime) >= filemtime($tpl));
    }

    /**
     * @name 拼装
     * @param  string $tpl [description]
     * @return [type]      [description]
     */
    private function compile($tpl_name = '') {
        $tpl = ($tpl_name == '') ? $this->_tpl : APP_PATH . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $tpl_name . '.html';
        $tpl_runtime = ($tpl_name == '') ? $this->_tpl_runtime : ROOT_PATH . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . APP_NAME . DIRECTORY_SEPARATOR . $tpl_name . '.html';

        if (!is_file($tpl)) {
            throw new \Exception("tpl file not found at $tpl", 1);
            return FALSE;
        }
        $content = file_get_contents($tpl);
        // include引用文件递归处理
        $matches = array();
        preg_match_all('/<include\s+[\'"](.+?)[\'"]\s*\/>/', $content, $matches);
        if (!empty($matches)) {
            foreach ($matches[1] as $k => $v) {
                $this->compile($v);
            }
        }

        // cache check
        if ($this->check_view_cache($tpl, $tpl_runtime)) {
            return file_get_contents($tpl_runtime);
        }

        $content = preg_replace(
            [
                '/{\$([\w\[\]\'"\$]+)}/s', // echo
                '/<each\s+[\'"](.+?)[\'"]\s*>/', // foreach
                '/<if\s*[\'"](.+?)[\'"]\s*>/', // if
                '/<elseif\s*[\'"](.+?)[\'"]\s*>/', // elseif
                '/<include\s+[\'"](.+?)[\'"]\s*\/>/', // include
            ],
            [
                '<?php echo $\\1;?>',
                '<?php $i=0;foreach( \\1 ){ ?>',
                '<?php if( \\1 ){ ?>',
                '<?php }elseif( \\1 ){ ?>',
                '<?php include(\'' . ROOT_PATH . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . APP_NAME . DIRECTORY_SEPARATOR . '\\1' . '.html\');?>',
            ], $content);
        $content = str_replace(['</if>', '<else />', '</each>'], ['<?php } ?>', '<?php }else{ ?>', '<?php $i++;} ?>'], $content);
        $dir = dirname($tpl_runtime);
        if (is_dir($dir)) {
            $flag = file_put_contents($tpl_runtime, $content, LOCK_EX);
            return $content;
        }
        if (@mkdir($dir, 0755, TRUE)) {
            $flag = file_put_contents($tpl_runtime, $content, LOCK_EX);
            return $content;
        }
        throw new \Exception("runtime directory created failed", 1);
        return FALSE;
    }
}