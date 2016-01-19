<?php
/**
 * @author miaokuan
 */

namespace Wee;

use Wee\Filter;
use Wee\Str;

class Thunder
{

    public static function parser($c)
    {
        // $c = strip_tags($c);
        // var_dump($c);
        $t = [];
        //thunder://QUFmdHA6Ly8wMjYyOjk5QDIxOS4xNDcuMTcuMTg4L1vQwtG4wNfPwtTYzfgtd3d3LnhpbnhsLmNvbV0tz/LI1b/7LnJtdmJaWg==
        preg_match_all('#thunder://[a-zA-Z0-9+/]{1,500}={0,3}#', $c, $n);
        // var_dump($n);
        foreach ($n[0] as $key => $thunder) {
            $thunder = self::decode($thunder);
            if ('' != $thunder) {
                $t[] = $thunder;
            }
        }

        $arr = preg_split('#(http|ftp)://#iu', $c, -1, PREG_SPLIT_DELIM_CAPTURE);
        array_shift($arr);
        $max = count($arr);
        //var_dump($arr);
        for ($i = 0; $i < $max; $i++) {
            $s = $arr[$i] . '://' . $arr[++$i];
            //echo $s;
            $m = [];
            preg_match('#(?:ftp|http)://(?:.*?)\.(?:rmvb|rm|avi|rar|zip|mkv|wmv|mp4|mp3|wma|mpg|iso|exe)#is', $s, $m);
            if (!empty($m[0])) {
                $len = strlen($m[0]);
                if ($len > 350 || $len < 20) {
                    continue;
                }

                $t[] = $m[0];
            }
        }

        $urls = array_unique($t);
        return implode("\n", $urls);
    }

    public static function encode($url)
    {
        $url = trim($url);
        if ($url == '') {
            return '';
        }
        return 'thunder://' . base64_encode('AA' . $url . 'ZZ');
    }

    public static function decode($c)
    {
        $c = base64_decode(substr($c, 10));
        if (substr($c, 0, 2) == 'AA' && substr($c, -2) == 'ZZ') {
            $c = substr($c, 2, -2);
            // echo mb_detect_encoding($c, 'gbk', 'utf-8');
            // if (mb_detect_encoding($c, 'gbk', 'utf-8') != 'UTF-8') {
            //     $c = iconv('gbk', 'utf-8//IGNORE', $c);
            // }
            // gbk -> utf-8 -> gbk
            if (iconv('utf-8', 'gbk//ignore', iconv('gbk', 'utf-8', $c)) == $c) {
                $c = iconv('gbk', 'utf-8//ignore', $c);
            }
            return $c;
        }

        return '';
    }

    public static function hash($c)
    {
        return md5($c);
    }

    public static function name($c)
    {
        $name = basename($c);
        $name = Str::conv($name);
        $name = Filter::thunder($name);

        return $name;
    }

    public static function convert($c)
    {
        $c = trim($c);
        if ('' == $c) {
            return '';
        }

        $no = Str::rand(4);
        $arr = explode("\n", $c);
        $str = '<table class="xunlei table table-striped" style="font-size:12px;"><thead><tr><th>SimCD 迅雷下载<span class="pull-right">建议使用迅雷离线下载、QQ旋风极速下载</span></th></tr></thead><tbody>';
        foreach ($arr as $url) {
            $name = self::name($url);
            if ('' == $name) {
                $name = $url;
            }

            $thunder = self::encode($url);
            $str .= '<tr><td style="word-break:break-all;"><input type="checkbox" class="chk" name="chk' . $no . '[]" value="' . $url . '" onclick="thunder.check(\'' . $no . '\');" checked="checked" /> <a rel="nofollow" href="' . $thunder . '">' . $name . '</a></td></tr>';

        }
        $str .= '<tr><td><label for="chkall' . $no . '"><input type="checkbox" class="chkall" id="chkall' . $no . '" onclick="thunder.checkAll(\'' . $no . '\',this.checked,event)" checked="checked" /> 全选' . '</label></td></tr><tr><td><input type="button" id="copylinks' . $no . '" class="btn btn-success copylinks" title="复制选中链接" value="复制选中链接" /> <span id="copied' . $no . '" style="display:none;" class="text-success">√ 已复制</span>
        </td></tr>';
        $str .= '</body></table>';
        return $str;
    }
}
