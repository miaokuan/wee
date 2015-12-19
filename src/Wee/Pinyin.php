<?php
/**
 * @author miaokuan
 */

namespace Wee;

class Pinyin
{
    static $data = array();

    static $instance = null;

    public static function convert($str, $encoding = 'utf-8')
    {
        if ($encoding == 'utf-8') {
            $str = iconv('utf-8', 'gbk//ignore', $str);
        }

        if (self::$instance === null) {
            self::$instance = new self();
        }

        $pinyin = '';
        $start = 0;
        $max = strlen($str);
        for ($i = 0; $i < $max; $i++) {
            $code = ord($str{$i});
            if ($code > 160) {
                $nextcode = ord($str{ ++$i});
                $code = $code * 256 + $nextcode;
                $pinyin .= ' ' . self::$instance->search($code) . ' ';
            } else {
                $pinyin .= chr($code);
            }
        }
        return trim(preg_replace('#[ ]+#', ' ', $pinyin));
    }

    protected function __construct()
    {
        $tmp = file(__DIR__ . '/pinyin/hz2py');
        if (!$tmp) {
            exit('hz2py not exists!');
        }
        self::$data = array();
        foreach ($tmp as $v) {
            self::$data[] = explode("\t", $v);
        }
    }

    public function __destruct()
    {
        self::$data = array();
    }

    private function search($code)
    {
        if ($code > 0 && $code < 160) {
            return chr($code);
        } elseif ($code < 45217 || $code > 55289) {
            return '';
        } else {
            $max = count(self::$data) - 1;
            for ($i = $max; $i >= 0; $i--) {
                if (self::$data[$i][1] <= $code) {
                    return self::$data[$i][0];
                }
            }
        }
    }

}
