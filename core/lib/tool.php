<?php
namespace frame3\core\lib;
/**
 * @name 工具库
 */
class tool {

    /**
     * @name 验证码
     * @param  [type] $options [description]
     * @return [type]          [description]
     */
    public function captcha($level = 1, $options = []) {
        $fontFile = CORE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . 'Keyboard.ttf'; //字体文件位置
        switch ($level) {
        case 0:
            extract($options);
            break; //自定义level
        case 1:
            $num = 4; // 验证码数目
            $noise = [5, 5, 30]; // 噪声 直线,弧线,点
            $range = 80; // 前景背景色差
            break;
        case 2:
            $num = 6;
            $noise = [30, 30, 250];
            $range = 50;
            break;
        case 3:
            $num = 4;
            $noise = [10, 10, 0];
            $range = 100;
            $fontFile = CORE_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . 'fzht.ttf'; //中文
            break;
        default:
            break;
        }
        $width = ($num - 1) * 50; // 宽
        $height = 50; // 高
        $fontSize = $height / 2;

        //创建画布
        $img = imagecreatetruecolor($width, $height);

        // 背景色
        $bg_color = mt_rand($range, 255 - $range);
        $backgroud = imagecolorallocate($img, $bg_color, $bg_color, $bg_color);
        // 前景色
        $fr_color = $bg_color + (mt_rand(20, 20 + $range) * array(1, -1)[mt_rand(0, 1)]);
        $front = imagecolorallocate($img, $fr_color, $fr_color, $fr_color);
        imagefill($img, 0, 0, $backgroud); //填充背景

        //添加一些干扰直线
        for ($i = 0; $i < $noise[0]; ++$i) {
            imageline($img, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $front);
        }

        //添加一些干扰弧线
        for ($i = 0; $i < $noise[1]; ++$i) {
            imagearc($img, mt_rand(-$width, $width), mt_rand(-$height, $height), mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, 360), mt_rand(0, 360), $front);
        }

        //添加一些干扰点
        for ($i = 0; $i < $noise[2]; ++$i) {
            imagesetpixel($img, mt_rand(0, 150), mt_rand(0, 60), $front);
        }

        if ($level == 3) {
            $s = '的一是在不了有和人这中大为上个国我以要他时来用们生到作地于出就分对成会可主发年动同工也能下过子说产种面而方后多定行学法所民得经十三之进着等部度家电力里如水化高自二理起小物现实加量都两体制机当使点从业本去把性好应开它合还因由其些然前外天政四日那社义事平形相全表间样与关各重新线内数正心反你明看原又么利比或但质气第向道命此变条只没结解问意建月公无系军很情者最立代想已通并提直题党程展五果料象员革位入常文总次品式活设及管特件长求老头基资边流路级少图山统接知较将组见计别她手角期根论运农指几九区强放决西被干 做必战先回则任取据处队南给色光门即保治北造百规热领七海口东导器压志世金增争济阶油思术极交受联什认六共权收证改清己美再采转更单风切打白教速花带安场身车例真务具万每目至达走积示议声报斗完类八离华名确才科张信马节话米整空元况今集温传土许步群广石记需段研界拉林律叫且究观越织装影算低持音众书布复容儿须际商非验连断深难近矿千周委素技备半办青省列习响约支般史感劳便团往酸历市克何除消构府称太准精值号率族维划选标写存候毛亲快效斯院查江型眼王按格养易置派层片始却专状育厂京识适属圆包火住调满县局照参红细引听该铁价严';
            $code = mb_substr($s, mt_rand(0, 498), $num);
        } else {
            $code = substr(str_shuffle('2345689abdeghpqsABCDEFGHJKLMNPQRSTUVWXYZ'), 0, $num);
        }
        //添加验证码到图像
        $x = 10;
        $y = $height - ($height - $fontSize) / 2;
        $dx = ($width - 10) / $num;
        for ($i = 0; $i < $num; ++$i) {
            imagettftext($img, $fontSize, mt_rand(-30, 30), $x, $y, $front, $fontFile, mb_substr($code, $i, 1));
            $x = $x + $dx - mt_rand(0, 6 * $level); // 写字位置右移随机向前移动部分距离
        }
        header('Content-Type: image/png');
        imagepng($img);
        imagedestroy($img);
        return $code;
    }
}