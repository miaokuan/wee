<?php
/**
 * @author miaokuan
 */

namespace Wee;

class Enc
{

    public static function decode($str, $key = '')
    {
        $ckeyLen = 4;
        $key = md5($key ? $key : 'come on baby!');
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckeyLen ? substr($str, 0, $ckeyLen) : '';

        $cryptkey = $keya . md5($keya . $keyc);
        $keyLen = strlen($cryptkey);

        $str = base64_decode(substr($str, $ckeyLen));
        $strLen = strlen($str);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $keyLen]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $strLen; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($str[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0)
            && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    }

    public static function encode($str, $key = '', $expiry = 0)
    {
        $ckeyLen = 4;
        $key = md5($key ? $key : 'come on baby!');
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckeyLen ? substr(md5(microtime()), -$ckeyLen) : '';

        $cryptkey = $keya . md5($keya . $keyc);
        $keyLen = strlen($cryptkey);

        $str = sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($str . $keyb), 0, 16) . $str;
        $strLen = strlen($str);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $keyLen]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $strLen; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($str[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        return $keyc . str_replace('=', '', base64_encode($result));
    }

}
