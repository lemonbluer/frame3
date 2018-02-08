<?php
namespace frame3\core\lib;
/**
 * @name 上传
 */
class upload {

    public $target_path; // 目标存储目录
    public $static_file_directory; // 静态文件目录
    public $url_prefix;

    function __construct($options = []) {
        $this->static_file_directory = isset($options['static_file_directory']) ? $options['static_file_directory'] : config('static_file_directory');
        $this->url_prefix = isset($options['url_prefix']) ? $options['url_prefix'] : config('url_prefix');
    }

    /**
     * @name 获取图片
     * @param  string $key  提交上来的表单字段名称
     * @param  string $type 图片格式
     * @return array       [ ['name'=>文件名,'url'=>url路径,'size'=>文件大小] ,...]
     */
    public function get_image($key = 'img', $type = 'jpg') {
        return $this->store_file($key, $type);
    }

    // public function image_compress() {

    // }

    private function store_file($key = 'file', $type = '') {
        $files = []; // 返回结果
        if (!isset($_FILES[$key])) {return $files;}
        $path = DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . date('Ym' . DIRECTORY_SEPARATOR . 'd', time());
        $this->target_path = $this->static_file_directory . $path;
        if ($_FILES[$key]['error'] == UPLOAD_ERR_OK) {
            if (!is_dir($this->target_path)) {
                @mkdir($this->target_path, 0755, TRUE);
            }
            $new_file_name = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10) . '.' . $type;
            $tmp_name = $_FILES[$key]['tmp_name'];
            $name = $_FILES[$key]['name'];
            if (move_uploaded_file($tmp_name, $this->target_path . DIRECTORY_SEPARATOR . $new_file_name)) {
                $files[] = [
                    'name' => $new_file_name,
                    'url' => $this->url_prefix . '/static' . str_replace(DIRECTORY_SEPARATOR, '/', $path) . '/' . $new_file_name,
                    'size' => $_FILES[$key]['size'],
                ];
            }
        }
        return $files;
    }
}