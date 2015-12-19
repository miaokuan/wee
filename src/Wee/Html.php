<?php
/**
 * @author miaokuan
 */

namespace Wee;

class Html
{
    public static function links($s)
    {
        //preg_match_all("'<\s*a\s[^>]*?href\s*=\s*([\"\'])?(?(1)(.*?)\\1|([^\s\>]+))[^>]*>?(.*?)</a>'isx",$s,$m);
        //return array_filter($m[2]+$m[3]);
        return self::tagAttrs($s, 'a', 'href');
    }

    public static function tagAttrs($s, $tag, $attr)
    {
        $tag = preg_quote($tag, '#');
        $attr = preg_quote($attr, '#');
        preg_match_all('#<\s*' . $tag . '\s[^>]*?' . $attr . '\s*=\s*([\"\'])?(?(1)(.*?)\\1|([^\s\>]+))[^>]*>#isx', $s, $m);

        $arr = array_filter($m[2] + $m[3]);
        return $arr;
    }

    public static function tagAttr($s, $tag, $attr)
    {
        $tag = preg_quote($tag, '#');
        $attr = preg_quote($attr, '#');
        preg_match('#<\s*' . $tag . '\s[^>]*?' . $attr . '\s*=\s*([\"\'])?(?(1)(.*?)\\1|([^\s\>]+))[^>]*>#isx', $s, $m);

        $rp = error_reporting(0);
        $s = $m[2] . $m[3];
        error_reporting($rp);
        return $s;
    }

    public static function format($s)
    {
        return self::clean($s);
    }

    public static function tidy($s)
    {
        return self::clean($s);
    }

    public static function clean($s)
    {
        $s = strip_tags($s, '<br><p><img>');

        //img
        $s = preg_replace('#<\s*img\s[^>]*?src\s*=\s*([\"\'])?(?(1)(.*?)\\1|([^\s\>]+))[^>]*>#isx', "<img src=\"\\2\\3\" />", $s);

        //p
        $s = preg_replace('#<\s*p([^>]*)>#i', '<p>', $s);

        //br
        $s = preg_replace('#<\s*br([^>]*)>#i', '<br>', $s);

        //\s
        $s = preg_replace('#[\s]+#i', ' ', $s);

        return $s;
    }

    /**
     * first img
     */
    public static function img($s)
    {
        return self::tagAttr($s, 'img', 'src');
    }

    /**
     * 用于拼接URL
     * resolve('http://www.example.com/foo/bar', '../baz');
     * http://www.example.com/baz
     */
    public static function resolve($base, $href)
    {
        // href="" ==> current url.
        if (!$href) {
            return $base;
        }

        // href="http://..." ==> href isn't relative
        $rel_parsed = parse_url($href);
        if (isset($rel_parsed['scheme'])) {
            return $href;
        }

        // add an extra character so that, if it ends in a /, we don't lose the last piece.
        $base_parsed = parse_url("$base ");
        // if it's just server.com and no path, then put a / there.
        if (!array_key_exists('path', $base_parsed)) {
            $base_parsed = parse_url("$base/ ");
        }

        // href="/ ==> throw away current path.
        if ($href{0} === "/") {
            $path = $href;
        } else {
            $path = dirname($base_parsed['path']) . "/$href";
        }

        // bla/./bloo ==> bla/bloo
        $path = preg_replace('~/\./~', '/', $path);

        // resolve /../
        // loop through all the parts, popping whenever there's a .., pushing otherwise.
        $parts = [];
        $arr = explode('/', preg_replace('~/+~', '/', $path));
        foreach ($arr as $part) {
            if ($part == "..") {
                array_pop($parts);
            } elseif ($part != "") {
                $parts[] = $part;
            }
        }

        return (
            (array_key_exists('scheme', $base_parsed)) ?
            $base_parsed['scheme'] . '://' . strtolower($base_parsed['host']) : ""
        ) . "/" . implode("/", $parts);

    }

    public static function highlight($str, $keywords, $replace)
    {
        $htmlArr = preg_split("/(<.*>)/U", $str, -1, PREG_SPLIT_DELIM_CAPTURE); // capture the tags as well as in between

        $textArr = array();
        foreach ($htmlArr as $key => $str) {
            if ((strlen($str) > 0) && ('<' != $str{0})) {
// If it's not a tag
                $textArr[$key] = $str;
            }
        }

        if (is_array($keywords)) {
            foreach ($keywords as $k => $w) {
                $keywords[$k] = '/' . preg_quote($w, '/') . '/';
            }
        } else {
            $keywords = '/' . preg_quote($keywords, '/') . '/';
        }
        $text = implode('<>', $textArr);
        $text = preg_replace($keywords, $replace, $text, 1);

        $tmpArr = explode('<>', $text);
        $i = 0;
        foreach ($textArr as $key => $val) {
            $htmlArr[$key] = $tmpArr[$i];
            $i++;
        }

        $str = implode('', $htmlArr);

        return $str;
    }

}
