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
    public function get_image($key = 'img', $type = 'jpg', $new_file_name = '') {
        return $this->convert_jpeg($key, $type, $new_file_name);
    }

    private function convert_jpeg($key = 'file', $type = '', $new_file_name = '') {
        // TODO : 批量传图
        if (is_array($_FILES[$key]['error'])) {return [];}
        if (!isset($_FILES[$key])) {return [];}
        if ($_FILES[$key]['error'] == UPLOAD_ERR_OK) {
            $file_type = $_FILES[$key]['type'];
            $tmp_img = $_FILES[$key]['tmp_name'];
            switch ($file_type) {
            case 'image/gif':
                // $im = imagecreatefromgif($im);
                // $result = imagegif($im, $new_file_name);
                $result = FALSE;
                break;
            case 'image/pjpeg':
            case 'image/jpeg':
                $im = imagecreatefromjpeg($tmp_img); //PHP图片处理系统函数
                break;
            case 'image/png':
                $im = imagecreatefrompng($tmp_img);
                break;
            case 'image/wbmp':
                $im = imagecreatefromwbmp($tmp_img);
                break;
            }
            // @unlink($tmp_img);
            if ($new_file_name == '') {
                $new_file_name = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10) . '.' . $type;
            }
            $path = DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . date('Ym' . DIRECTORY_SEPARATOR . 'd', time());
            $this->target_path = $this->static_file_directory . $path;
            // vd($this->url_prefix . '/static' . str_replace(DIRECTORY_SEPARATOR, '/', $path) . '/' . $new_file_name, $_FILES[$key]['size']);

            if (!is_dir($this->target_path)) {
                @mkdir($this->target_path, 0755, TRUE);
            }
            $new_file = $this->target_path . DIRECTORY_SEPARATOR . $new_file_name;

            if (imagejpeg($im, $new_file)) {
                imagedestroy($im);
                $b = [
                    'name' => $new_file_name,
                    'url' => $this->url_prefix . '/static' . str_replace(DIRECTORY_SEPARATOR, '/', $path) . '/' . $new_file_name,
                    'size' => $_FILES[$key]['size'],
                ];
                return $b;
            }

            return [];
        }
    }
}