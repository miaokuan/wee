<?php
/**
 * @author miaokuan
 */

namespace Wee;

class Valid
{
    /**
     * @ref:http://developer.51cto.com/art/200810/92652.htm
     */
    public static function email($email)
    {
        $isValid = true;
        $atIndex = strpos($email, "@");
        if (false === $atIndex) {
            $isValid = false;
        } else {
            $domain = substr($email, $atIndex + 1);
            $local = substr($email, 0, $atIndex);
            $localLen = strlen($local);
            $domainLen = strlen($domain);
            if ($localLen < 1 || $localLen > 64) {
                // local part length exceeded
                $isValid = false;
            } else if ($domainLen < 1 || $domainLen > 255) {
                // domain part length exceeded
                $isValid = false;
            } else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
                // local part starts or ends with '.'
                $isValid = false;
            } else if (preg_match('/\\.\\./', $local)) {
                // local part has two consecutive dots
                $isValid = false;
            } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
                // character not valid in domain part
                $isValid = false;
            } else if (preg_match('/\\.\\./', $domain)) {
                // domain part has two consecutive dots
                $isValid = false;
            } else if
            (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                str_replace("\\\\", "", $local))) {
                // character not valid in local part unless
                // local part is quoted
                if (!preg_match('/^"(\\\\"|[^"])+"$/',
                    str_replace("\\\\", "", $local))) {
                    $isValid = false;
                }
            }

            // if ($isValid && !(checkdnsrr($domain, "MX") ||
            //     checkdnsrr($domain, "A"))) {
            //     // domain not found in DNS
            //     $isValid = false;
            // }

        }
        return $isValid;
    }

    public static function phonenum($str)
    {
        $ret = preg_match('#^1([0-9]{10})$#', $str);

        return (bool) $ret;
    }

    public static function username($username)
    {
        if (strlen($username) < 3 || strlen($username) > 45 ||
            preg_match('/^[^a-zA-Z_]/', $username) ||
            preg_match('/[^a-zA-z0-9_-]/', $username)) {
            return false;
        }
        return true;
    }

    public static function password($passwd)
    {
        if (strlen($passwd) < 6) {
            return false;
        }
        return true;
    }

}
