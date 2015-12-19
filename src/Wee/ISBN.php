<?php
/**
 * $Id: ISBN.php 473 2014-09-25 14:43:05Z svn $
 * @author miaokuan
 */

namespace Wee;

class ISBN
{

    public static function isbn($str)
    {
        $isbn = self::isbn13($str);
        if ('' === $isbn) {
            $isbn = self::isbn10($str);
        }

        return $isbn;
    }

    public static function isbn13($str)
    {
        preg_match_all("/([0-9x]{13})/uis", $str, $m);
        if (isset($m[1])) {
            foreach ($m[1] as $isbn) {
                if (self::valid($isbn)) {
                    return $isbn;
                }
            }
        }

        return '';
    }

    public static function isbn10($str)
    {
        preg_match_all("/([0-9x]{10})/uis", $str, $m);
        if (isset($m[1])) {
            foreach ($m[1] as $isbn) {
                if (self::valid($isbn)) {
                    return $isbn;
                }
            }
        }

        return '';
    }

    /**
     * 该函数用于判断是否为ISBN号
     * 参数说明：
     *    $isbn : isbn码
     */
    public static function valid($isbn)
    {
        $len = strlen($isbn);
        if ($len != 10 && $len != 13) {
            return false;
        }

        $rc = self::compute($isbn, $len);

        /* ISBN尾数与计算出来的校验码不符 */
        if ($isbn[$len - 1] != $rc) {
            return false;
        }

        return true;
    }

    /**
     * 该函数用于计算ISBN末位校验码
     * 参数说明：
     *   $isbn : isbn码
     *   $len  : isbn码长度
     */
    protected static function compute($isbn, $len)
    {
        if ($len == 10) {
            $digit = 11 - self::sum($isbn, $len) % 11;
            if ($digit == 10) {
                $rc = 'X';
            } elseif ($digit == 11) {
                $rc = '0';
            } else {
                $rc = (string) $digit;
            }
        } else if ($len == 13) {
            $digit = 10 - self::sum($isbn, $len) % 10;
            if ($digit == 10) {
                $rc = '0';
            } else {
                $rc = (string) $digit;
            }
        }

        return $rc;
    }

    /**
     * 该函数用于计算ISBN加权和
     * 参数说明：
     *   $isbn : isbn码
     *   $len  : isbn码长度
     */
    protected static function sum($isbn, $len)
    {
        $sum = 0;
        if ($len == 10) {
            for ($i = 0; $i < $len - 1; $i++) {
                $sum = $sum + (int) $isbn[$i] * ($len - $i);
            }
        } elseif ($len == 13) {
            for ($i = 0; $i < $len - 1; $i++) {
                if ($i % 2 == 0) {
                    $sum = $sum + (int) $isbn[$i];
                } else {
                    $sum = $sum + (int) $isbn[$i] * 3;
                }
            }
        }

        return $sum;
    }

}
