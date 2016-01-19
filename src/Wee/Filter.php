<?php
/**
 * @author miaokuan
 */

namespace Wee;

use Wee\Config;

class Filter
{
    public static function all($c)
    {
        $c = self::magnet($c);
        $c = self::emule($c);
        $c = self::thunder($c);

        return $c;
    }

    public static function magnet($c)
    {
        // $search = ['-YYeTs人人影视', '-ASAP', '-EVOLVE', '-IMMERSE', '-KiNGS', '-CMCT', '-人人影视',
        //     '_www.kk55.net', '-Simple字幕组', '(ED2000.COM)',

        // ];
        $search = Config::load('filter', 'magnet');
        $replace = '';
        $c = str_replace($search, $replace, $c);

        return $c;
    }

    public static function emule($c)
    {
        // $search = ['-YYeTs人人影视', '-ASAP', '-EVOLVE', '-IMMERSE', '-KiNGS', '-CMCT', '-人人影视',
        //     '_www.kk55.net', '-Simple字幕组', '(ED2000.COM)',

        // ];
        $search = Config::load('filter', 'emule');
        $replace = '';
        $c = str_replace($search, $replace, $c);

        return $c;
    }

    public static function thunder($s)
    {
        // $search = ['[阳光电影-www.ygdy8.com]', '[电影天堂www.dy2018.com]',
        //     '[电影天堂www.dy2018.net]', '[电影天堂www.dygod.net]',
        //     '[电影天堂www.dygod.cn]',
        // ];
        $search = Config::load('filter', 'thunder');
        $replace = '';
        $s = str_replace($search, $replace, $s);
        $s = preg_replace('#(www|bbs)\.[a-z0-9\.]+\.(com|net|cn|org|me)#i', '', $s);
        return $s;
    }

}
