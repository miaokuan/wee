<?php
/**
 * @author miaokuan
 */

namespace Wee;

class Captcha
{
    public $text_num;
    public $ttf_file;
    public $im_x;
    public $im_y;
    public $text;

    public function __construct($text, $im_x = 150, $im_y = 30)
    {
        $this->text_num = strlen($text);
        $this->text = $text;
        $this->ttf_file = __DIR__ . '/font/' . rand(1, 3) . '.ttf';
        $this->im_x = $im_x;
        $this->im_y = $im_y;
    }

    public function show()
    {
        $size = min($this->im_y, $this->im_x / $this->text_num);
        $size = ceil($size * 0.8);

        $im = imagecreatetruecolor($this->im_x, $this->im_y);
        $text_c = imagecolorallocate($im, 255, 0, 0);
        $bg_c = imagecolorallocate($im, 255, 255, 255);
        imagefill($im, 0, 0, $bg_c);

        for ($i = 0; $i < $this->text_num; $i++) {
            $ypos = $size * 1.2;
            if (preg_match('/[ygJup]/', $this->text[$i])) {
                $ypos = ceil($ypos * 0.8);
            }

            //反色
            $rnd = rand(0, $this->text_num - 1);
            if (($i % $this->text_num) == $rnd) {
                imagefilledellipse($im, $this->im_x * 0.05 + $i * $size * 1.3 + $size / 2, $ypos / 2,
                    $size * 1.2, $this->im_y * 1.1, $text_c);
                imagettftext($im, $size, 0, $this->im_x * 0.05 + $i * $size * 1.3, $ypos,
                    $bg_c, $this->ttf_file, $this->text[$i]);
            } else {
                imagettftext($im, $size, rand(-20, 20), $this->im_x * 0.05 + $i * $size * 1.3, $ypos,
                    $text_c, $this->ttf_file, $this->text[$i]);
            }
        }

        ob_clean();
        header("Content-type: image/png");
        ImagePNG($im);
        ImageDestroy($im);
        echo '= @author:miaokuan';
    }

}
