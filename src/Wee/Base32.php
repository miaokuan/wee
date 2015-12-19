<?php
/**
 * @usage:
 * $encode = Base32::encode('肖斌-https://xiaobin.net/');
 * $decode = Base32::decode($encode);
 * var_dump($encode, $decode);
 */

namespace Wee;

class Base32
{
    public static function encode($input)
    {
        $input = (string) $input;

        // Reference: http://www.ietf.org/rfc/rfc3548.txt
        // If you want build alphabet own, you should modify the decode section too.
        $BASE32_ALPHABET = 'abcdefghijklmnopqrstuvwxyz234567';
        $output = '';
        $v = 0;
        $vbits = 0;

        for ($i = 0, $j = strlen($input); $i < $j; $i++) {
            $v <<= 8;
            $v += ord($input[$i]);
            $vbits += 8;

            while ($vbits >= 5) {
                $vbits -= 5;
                $output .= $BASE32_ALPHABET[$v >> $vbits];
                $v &= ((1 << $vbits) - 1);
            }
        }

        if ($vbits > 0) {
            $v <<= (5 - $vbits);
            $output .= $BASE32_ALPHABET[$v];
        }

        return $output;
    }

    public static function decode($input)
    {
        $output = '';
        $v = 0;
        $vbits = 0;

        for ($i = 0, $j = strlen($input); $i < $j; $i++) {
            $v <<= 5;
            if ($input[$i] >= 'a' && $input[$i] <= 'z') {
                $v += (ord($input[$i]) - 97);
            } elseif ($input[$i] >= '2' && $input[$i] <= '7') {
                $v += (24 + $input[$i]);
            } else {
                return '';
            }

            $vbits += 5;
            while ($vbits >= 8) {
                $vbits -= 8;
                $output .= chr($v >> $vbits);
                $v &= ((1 << $vbits) - 1);
            }
        }
        return $output;
    }

}
