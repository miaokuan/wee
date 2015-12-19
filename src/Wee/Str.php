<?php

/**
 * @author miaokuan
 */

namespace Wee;

class Str
{
    public static function getQuery($refer)
    {
        $query = '';
        if (!empty($refer)) {
            $arr = [
                'baidu' => 'word',
                'soso' => 'w',
                '3721' => 'name',
                'youdao' => 'q',
                'so.360.cn' => 'q',
                'vnet' => 'kw',
                'sogou' => 'query',
            ];
            foreach ($arr as $host => $param) {
                $tarr = parse_url($refer);
                if (strpos($tarr['host'], $host)) {
                    $queries = [];
                    parse_str($tarr['query'], $queries);
                    $query = $queries[$param];
                }
            }
        }

        return $query;
    }

    public static function isbn2dna($isbn)
    {
        $dna = '';
        $len = strlen($isbn);
        if (13 == $len) {
            $dna = substr($isbn, 3, 9);
        } elseif (10 == $len) {
            $dna = substr($isbn, 0, 9);
        }
        return $dna;
    }

    public static function conv($str)
    {
        if (false !== strpos($str, '%')) {
            $str = urldecode($str);
        }

        if (mb_detect_encoding($str, 'utf-8') != 'UTF-8') {
            $str = iconv('gbk', 'utf-8//ignoe', $str);
        }

        $str = self::semi($str);
        $str = trim($str);

        return $str;
    }

    public static function csv($str)
    {
        $search = array('"', "\n");
        $replace = array('""', '');
        return '"' . str_replace($search, $replace, $str) . '"';
    }

    public static function crc32($str)
    {
        $checksum = crc32($str);
        return sprintf("%u", $checksum);
    }

    public static function hash($str)
    {
        $checksum = crc32($str);
        $sum1 = sprintf("%u", $checksum);

        $str = substr($str, 0, strlen($str) / 2);
        $checksum = crc32($str);
        $sum2 = sprintf("%u", $checksum);

        return $sum1 . substr($sum2, 1);
    }

    public static function rand($length = 8, $numeric = false)
    {
        if ($numeric) {
            return sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
        } else {
            $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $max = strlen($chars) - 1;

            $str = $chars[mt_rand(9, $max)];
            for ($i = 1; $i < $length; $i++) {
                $str .= $chars[mt_rand(0, $max)];
            }
            return $str;
        }
    }

    public static function sub($str, $start, $end)
    {
        $spos = strpos($str, $start);
        if (false === $spos) {
            return '';
        }
        $spos = $spos + strlen($start);
        $epos = strpos($str, $end, $spos);
        if (false === $epos) {
            return '';
        }
        $str = substr($str, $spos, $epos - $spos);
        return $str;
    }

    public static function cut($str, $start, $end, $retain_start = false, $retain_end = false, $fix = '')
    {
        $result = '';
        $startpos = stripos($str, $start);
        $endpos = stripos($str, $end, $startpos + strlen($start));
        if ($startpos === false || $endpos === false) {
            return $str;
        }
        if ($retain_start) {
            $startpos += strlen($start);
        }
        if (!$retain_end) {
            $endpos = $endpos + strlen($end);
        }
        if ($startpos !== false && $endpos !== false) {
            $result = trim(substr($str, 0, $startpos)) . $fix . trim(substr($str, $endpos));
        }

        return $result;
    }

    public static function strlen($str)
    {
        if (function_exists('mb_get_info')) {
            return mb_strlen($str, 'UTF-8');
        } else {
            preg_match_all("/./us", $str, $m);
            return count($m[0]);
        }
    }

    public static function substr($str, $length, $strimmarker = '...', $start = 0)
    {
        if (function_exists('mb_get_info')) {
            $iLength = mb_strlen($str, 'utf-8');
            $str = mb_substr($str, $start, $length, 'utf-8');
            return ($length < $iLength) ? $str . $strimmarker : $str;
        } else {
            preg_match_all("/./us", $str, $m);
            $str = join("", array_splice($m[0], $start, $length));
            return ($length < count($m[0])) ? $str . $strimmarker : $str;
        }
    }

    public static function strimwidth($str, $width, $strimmarker = '...', $start = 0)
    {
        if (function_exists('mb_get_info')) {
            return mb_strimwidth($str, $start, $width, $strimmarker, 'utf-8');
        } else {
            return self::substr($str, $width, $strimmarker, $start);
        }
    }

    /**
     * 全角转半角
     */
    public static function semi($str)
    {
        $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
            '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
            'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
            'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
            'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
            'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
            'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
            'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
            'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
            'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
            'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
            'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
            'ｙ' => 'y', 'ｚ' => 'z',
            '（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',
            '】' => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']',
            '‘' => '[', '’' => ']', '｛' => '{', '｝' => '}', '《' => '<',
            '》' => '>',
            '％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
            '：' => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.',
            '；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',
            '”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',
            '　' => ' ', '＄' => '$', '＠' => '@', '＃' => '#', '＾' => '^', '＆' => '&', '＊' => '*',
            '＂' => '"');

        return strtr($str, $arr);

        $search = array_keys($arr);
        $replace = array_values($arr);
        return str_replace($search, $replace, $str);
    }

    public static function size($size)
    {
        if ($size > 1024 * 1024 * 1024) {
            return number_format($size / 1024 / 1024 / 1024, 2) . ' GB';
        }
        if ($size > 1024 * 1024) {
            return number_format($size / 1024 / 1024, 2) . ' MB';
        }
        if ($size > 1024) {
            return number_format($size / 1024, 2) . ' KB';
        }
        return $size . ' B';
    }

    public static function suffix($str)
    {
        return trim(substr(strrchr($str, '.'), 1, 8));
    }

    public static function escape($str)
    {
        preg_match_all("/[\xc2-\xdf][\x80-\xbf]+|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}|[\x01-\x7f]+/e", $str, $r);

        //匹配utf-8字符，
        $str = $r[0];
        $l = count($str);
        for ($i = 0; $i < $l; $i++) {
            $value = ord($str[$i][0]);
            if ($value < 223) {
                $str[$i] = rawurlencode(utf8_decode($str[$i]));
                //先将utf8编码转换为ISO-8859-1编码的单字节字符，urlencode单字节字符.
                //utf8_decode()的作用相当于iconv("UTF-8","CP1252",$v)。
            } else {
                $str[$i] = "%u" . strtoupper(bin2hex(iconv("UTF-8", "UCS-2", $str[$i])));
            }
        }
        return join("", $str);
    }

    public static function unescape($str)
    {
        $ret = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            if ($str[$i] == '%' && $str[$i + 1] == 'u') {
                $val = hexdec(substr($str, $i + 2, 4));
                if ($val < 0x7f) {
                    $ret .= chr($val);
                } else if ($val < 0x800) {
                    $ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f));
                } else {
                    $ret .= chr(0xe0 | ($val >> 12)) . chr(0x80 | (($val >> 6) & 0x3f)) . chr(0x80 | ($val & 0x3f));
                }

                $i += 5;
            } else if ($str[$i] == '%') {
                $ret .= urldecode(substr($str, $i, 3));
                $i += 2;
            } else {
                $ret .= $str[$i];
            }

        }
        return $ret;
    }

    public static function preg_replace_nth($pattern, $replacement, $subject, $nth = 1)
    {
        return preg_replace_callback($pattern,
            function ($found) use (&$pattern, &$replacement, &$nth) {
                $nth--;
                if ($nth == 0) {
                    return preg_replace($pattern, $replacement, reset($found));
                }

                return reset($found);
            }, $subject, $nth);
    }

    public static function tagcloud(array $tags)
    {
        arsort($tags);
        $colorArr = [
            'font-turquoise',
            'font-green',
            'font-emerland',
            'font-nephritis',
            'font-river',
            'font-belize',
            'font-amethyst',
            'font-wisteria',
            'font-asphalt',
            'font-midnight',
            'font-flower',
            'font-orange',
            'font-carrot',
            'font-pumpkin',
            'font-alizarin',
            'font-pomegranate',
            'font-clouds',
            'font-silver',
        ];
        $num = ceil(count($tags) / count($colorArr));
//        $tags = array_keys($tags);
        $i = 0;
        foreach ($tags as $key => $val) {
            $i++;
            $size = 28 - ceil(($i / $num) * 1.2);
            $tags[$key] = 'class="' . $colorArr[array_rand($colorArr)] . '" style="font-size:' . $size . 'px;margin:4px;"';
        }
        $tags = Arr::shuffle($tags);
        return $tags;
    }
}
