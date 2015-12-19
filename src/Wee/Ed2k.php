<?php
/**
 * @author miaokuan
 */

namespace Wee;

class Ed2k
{

    public static function parser($c)
    {
        $emule = '';
        preg_match_all('#"ed2k://(.+)"#Uis', $c, $m);
        if (empty($m[1])) {
            return '';
        }

        array_unique($m[1]);
        foreach ($m[1] as $key => $value) {
            $emule .= 'ed2k://' . trim($value) . "\n";
        }

        return trim($emule);
    }

    public static function hash($emule, $part = '')
    {
        //ed2k://|file|Ja%26%2339%3Bmie.Private.School.Girl.S01E01.Episode.1.720p.WEB-DL.AAC2.0.H.264-NTb.mkv|905656571|3BB1C2023E2C8D0DBC2A35BBCE1653E0|h=32FWJB3BXG3RWDQB7P2LVVCQGPIX2FAA|/
        $arr = explode("|", $emule);

        $ret = [];
        $ret['filename'] = $arr[2];
        $ret['size'] = $arr[3];
        $ret['hash'] = $arr[4];

        if (!empty($part)) {
            return isset($ret[$part]) ? $ret[$part] : $emule;
        }

        return $ret;
    }

    public static function name($emule)
    {
        $name = self::hash($emule, 'filename');
        $name = urldecode($name);
        return $name;
    }

    public static function normalization($emule)
    {
        $emule = html_entity_decode($emule);
        $emule = preg_replace_callback("/(&#[0-9a-fx]+;)/i", function ($m) {return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");}, $emule);
        $file = self::name($emule);
        $file = Str::conv($file);
        $file = Filter::emule($file);

        $arr = explode("|", $emule);
        $arr[2] = urlencode($file);
        $emule = implode('|', $arr);
        return $emule;
    }

    public static function convert($content, $stat = "http://ed2k.shortypower.org/?hash=", $no = '')
    {
        $no == '' && $no = Str::rand(4);
        $totalsize = 0;
        $extArr = array();
        $c = '<table class="emule table table-striped" style="font-size:12px;" id="table' . $no . '"><thead><tr><th>SimCD 电驴下载 <span class="pull-right">建议使用迅雷离线下载、QQ旋风极速下载</span></th><th width="80"></th></tr></thead><tbody>';
        $num = 0;
        $content = preg_replace(
            "/(?<!ed2k=)(?<!ed2k=[\"\'])(?<!href=)(?<!href=[\"\'])ed2k:\/\/\|file\|.+?\|\/(?!\|)/i",
            "\n\\0\n",
            $content
        );
        preg_match_all(
            "/^.+$/m",
            $content,
            $lines
        );
        if (empty($lines[0])) {return '';}

        $arr = [];
        foreach ($lines[0] as $myline) {
            $myline .= '/';
            preg_match(
                "/(?<!ed2k=)(?<!ed2k=[\"\'])(?<!href=)(?<!href=[\"\'])ed2k:\/\/\|(file)\|([^|]+\|[0-9]+\|[a-f0-9]+)\|(?:[a-z0-9=|\/]+)?/i",
                $myline,
                $matches
            );
            //var_dump($matches);
            /*
            preg_match (
            "/(?<!ed2k=)(?<!ed2k=[\"\'])(?<!href=)(?<!href=[\"\'])ed2k:\/\/\|(file)\|(.+?)\|\/(?!\|)/i",
            $myline,
            $matches
            );
             */
            if (count($matches) != 0) {
                $pieces = explode("|", $matches[2]);
                $ed2k = 'ed2k://|file|' . implode('|', $pieces) . '|/';
                $ed2k = self::normalization($ed2k);
                $file = self::name($ed2k);
                if (strpos($file, '.')) {
                    $ext = Str::suffix($file);

                    array_push($extArr, $ext);
                }
                $size = $pieces[1];
                $totalsize += $size;
                $size = Str::size($size);
                $hash = $pieces[2];
                if (isset($arr[$hash])) {
                    continue;
                }
                $num++;
                $arr[$hash] = 1;
                $c .= '<tr><td style="word-break:break-all;"><input type="checkbox" class="chk" name="chk' . $no . '[]" value="' . $ed2k . '" onclick="ed2k.check(\'' . $no . '\',event);" checked="checked" /> <a rel="nofollow" href="' . $ed2k . '">' . $file . '</a>';
                if ($stat != '') {
                    $c .= ' <a href="' . $stat . $hash . '" target="_blank">' . '查源' . '</a>';
                }
                $c .= '</td><td>' . $size . '</td></tr>';
            } else {
                $myline = preg_replace(
                    "/<p>|<\/p>|<br\s\/>|<br\/>|<br>/i",
                    "",
                    $myline
                );
                $myline = trim($myline);
                if ($myline !== "") {
                    $c .= '<tr><td colspan="2">' . $myline . '  </td></tr>';
                }
            }

        }

        $totalsize = Str::size($totalsize);

        $c .= '<tr><td><label for="chkall' . $no . '" style="float:left;display:inline"><input type="checkbox" class="chkall" id="chkall' . $no . '" onclick="ed2k.checkAll(\'' . $no . '\',this.checked)" checked="checked" /> 全选' . '</label>';

        if ($num >= 2) {
            $c .= '<ul style="font-size:12px;float:left;margin:0 20px" class="unstyled"><li><label class="namefilter" for="namefilter' . $no . '" style="display:inline">' . '文件名选择' . ':</label>';
            $extArr = array_unique($extArr);
            foreach ($extArr as $ext) {
                $c .= ' <label style="display:inline" for="chktype' . $ext . '-' . $no . '"><input type="checkbox" value="' . $ext . '" name="chktype' . $no . '[]" id="chktype' . $ext . '-' . $no . '" onclick="ed2k.typeFilter(\'' . $no . '\',this.value,this.checked)" />' . strtoupper($ext) . '</label>';
            }
            $c .= ' <input style="width:160px;padding:0px;font-size:12px;" type="text" id="namefilter' . $no . '" onkeyup="ed2k.filter(\'' . $no . '\')" /></li>';
            $c .= '<li><label style="display:inline" for="sizefilter' . $no . '-1">大小选择:</label><select id="sizesymbol' . $no . '-1" onchange="ed2k.filter(\'' . $no . '\')">
                    <option selected="selected" value="1">&gt;</option>
                    <option value="2">&lt;</option>
                </select><input style="width:60px;padding:0px;font-size:12px;" type="text" id="sizefilter' . $no . '-1" onkeyup="ed2k.filter(\'' . $no . '\')" /><select id="sizeunit' . $no . '-1" onchange="ed2k.filter(\'' . $no . '\')">
                    <option value="1073741824">' . 'GB' . '</option>
                    <option selected="selected" value="1048576">' . 'MB' . '</option>
                    <option value="1024">' . 'KB' . '</option>
                    <option value="1">' . 'B' . '</option>
                </select> && <select id="sizesymbol' . $no . '-2" onchange="ed2k.filter(\'' . $no . '\')">
                    <option value="1">&gt;</option>
                    <option selected="selected" value="2">&lt;</option>
                </select><input style="width:60px;padding:0px;font-size:12px;" type="text" id="sizefilter' . $no . '-2" onkeyup="ed2k.filter(\'' . $no . '\')" /><select id="sizeunit' . $no . '-2" onchange="ed2k.filter(\'' . $no . '\')">
                    <option value="1073741824">' . 'GB' . '</option>
                    <option selected="selected" value="1048576">' . 'MB' . '</option>
                    <option value="1024">' . 'KB' . '</option>
                    <option value="1">' . 'B' . '</option>
                </select></label></li></ul>';
        }

        $c .= '</td><td><span id="totalsize' . $no . '">' . $totalsize . '</span><br />(<span id="totalnum' . $no . '">' . $num . '</span>' . '文件' . ')</td></tr><tr><td colspan="2"> <input type="button" id="copylinks' . $no . '" class="btn btn-success copylinks" title="复制选中链接" value="复制选中链接" /> <span id="copied' . $no . '" style="display:none;" class="text-success">√ 已复制</span>
        <input type="button" id="downlinks' . $no . '" class="btn btn-danger downlinks" onclick="ed2k.download(\'' . $no . '\')" title="下载选中链接" value="下载选中链接" />
        </td></tr>
    </tbody>
</table>';

        return $c;

    }
}
