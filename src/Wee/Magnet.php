<?php
/**
 * @author miaokuan
 */

namespace Wee;

class Magnet
{
    public static function parser($c)
    {
        $magnet = '';
        //preg_match_all("'<\s*a\s[^>]*?href\s*=\s*([\"\'])?(?(1)(.*?)\\1|([^\s\>]+))[^>]*>?(.*?)</a>'isx",$c,$m);
        preg_match_all('#([\'\"])magnet:\?(.+?)\\1#i', $c, $m);
        if (empty($m[2])) {
            return '';
        }

        $m[2] = array_unique($m[2]);
        foreach ($m[2] as $key => $value) {
            $magnet .= 'magnet:?' . trim($value) . "\n";
        }

        return trim($magnet);
    }

    public static function hash($magnet, $part = '')
    {
        $ret = [];
        //magnet:?
        $c = substr($magnet, 8);
        parse_str($c, $ret);

        if (!empty($part)) {
            return isset($ret[$part]) ? $ret[$part] : '';
        }

        return $ret;
    }

    public static function normalization($magnet)
    {
        $magnet = html_entity_decode($magnet);
        $magnet = preg_replace_callback("/(&#[0-9a-fx]+;)/i", function ($m) {return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");}, $magnet);
        $dn = self::name($magnet);
        $dn = Str::conv($dn);
        $dn = Filter::magnet($dn);

        $arr = self::hash($magnet);
        $arr['dn'] = urlencode($dn);
        $magnet = 'magnet:?' . urldecode(http_build_query($arr));
        return $magnet;
    }

    public static function name($magnet)
    {
        $name = self::hash($magnet, 'dn');
        $name = urldecode($name);
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
        $str = '<table class="magnet table table-striped" style="font-size:12px;"><thead><tr><th>SimCD 磁力下载 <span class="pull-right">建议使用迅雷离线下载、QQ旋风极速下载</span></th><td></td></tr></thead><tbody>';
        foreach ($arr as $magnet) {
            $magnet = str_replace('(ED2000.COM)', '', $magnet);
            $magnet = self::normalization($magnet);
            $name = self::name($magnet);
            if ('' == $name) {
                $name = $magnet;
            }
            $str .= '<tr><td style="word-break:break-all;"><input type="checkbox" class="chk" name="chk' . $no . '[]" value="' . $magnet . '" onclick="thunder.check(\'' . $no . '\');" checked="checked" /> <a rel="nofollow" href="' . $magnet . '">' . $name . '</a></td></tr>';

        }
        $str .= '<tr><td><label for="chkall' . $no . '"><input type="checkbox" class="chkall" id="chkall' . $no . '" onclick="thunder.checkAll(\'' . $no . '\',this.checked,event)" checked="checked" /> 全选' . '</label></td></tr><tr><td><input type="button" id="copylinks' . $no . '" class="btn btn-success copylinks" title="复制选中链接" value="复制选中链接" /> <span id="copied' . $no . '" style="display:none;" class="text-success">√ 已复制</span>
        </td></tr>';
        $str .= '</body></table>';
        return $str;
    }
}
