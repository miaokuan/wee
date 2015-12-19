<?php
/**
 * @author miaokuan
 */

namespace Wee;

class U2U8
{
    public static function convert($str, $target = 'utf-8')
    {
        switch ($target) {
            case 'unicode':
                return self::utf8ToUnicode($str);
            case 'utf-8':
                return self::unicodeToUtf8($str);
        }
    }

    public static function unicodeToUtf8($str)
    {
        //$str = preg_replace("|&#(x[0-9a-fA-F]{1,5});|se", '"&#".hexdec("\\1").";"', $str);
        $str = preg_replace_callback("|&#(x[0-9a-fA-F]{1,5});|s", 'Wee\\U2U8::hexdec', $str);

        //$str = preg_replace("|&#([0-9a-fA-F]{1,5});|se", 'self::unicode_utf8("\\1")', $str);
        $str = preg_replace_callback("|&#([0-9a-fA-F]{1,5});|s", 'Wee\\U2U8::unicode_utf8', $str);

        return $str;
    }

    public static function utf8ToUnicode($str)
    {
        //$str = preg_replace("|.|use", '"&#".self::utf8_unicode("\\0").";"', $str);
        $str = preg_replace_callback("|.|us", 'Wee\\U2U8::utf8_unicode', $str);

        return $str;
    }

    protected static function hexdec($matches)
    {
        return "&#" . hexdec($matches[1]) . ";";
    }

    protected static function unicode_utf8($matches)
    {
        $c = $matches[1];
        $str = "";
        if ($c < 0x80) {
            $str .= $c;
        } else if ($c < 0x800) {
            $str .= chr(0xC0 | $c >> 6);
            $str .= chr(0x80 | $c & 0x3F);
        } else if ($c < 0x10000) {
            $str .= chr(0xE0 | $c >> 12);
            $str .= chr(0x80 | $c >> 6 & 0x3F);
            $str .= chr(0x80 | $c & 0x3F);
        } else if ($c < 0x200000) {
            $str .= chr(0xF0 | $c >> 18);
            $str .= chr(0x80 | $c >> 12 & 0x3F);
            $str .= chr(0x80 | $c >> 6 & 0x3F);
            $str .= chr(0x80 | $c & 0x3F);
        }
        return $str;
    }

    protected static function utf8_unicode($matches)
    {
        $char = $matches[0];
        switch (strlen($char)) {
            case 1:
                $n = ord($char);
                break;
            case 2:
                $n = (ord($char[0]) & 0x3f) << 6;
                $n += ord($char[1]) & 0x3f;
                break;
            case 3:
                $n = (ord($char[0]) & 0x1f) << 12;
                $n += (ord($char[1]) & 0x3f) << 6;
                $n += ord($char[2]) & 0x3f;
                break;
            case 4:
                $n = (ord($char[0]) & 0x0f) << 18;
                $n += (ord($char[1]) & 0x3f) << 12;
                $n += (ord($char[2]) & 0x3f) << 6;
                $n += ord($char[3]) & 0x3f;
                break;
        }
        return '&#' . $n . ';';
    }
}
