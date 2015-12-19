<?php
/**
 * @author miaokuan
 */

namespace Wee;

class Cookie
{
    public static function get($key, $default = null)
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
    }

    public static function set($key, $value, $expires = 0, $url = null)
    {
        $path = '/';
        if (!empty($url)) {
            $parsed = parse_url($url);
            $path = empty($parsed['path']) ? '/' : $parsed['path'];
        }

        if (is_array($value)) {
            foreach ($value as $name => $val) {
                setcookie($key . '[' . $name . ']', $val, $expires, $path);
            }
        } else {
            setcookie($key, $value, $expires, $path);
        }
    }

    public function remove($key, $url = null)
    {
        if (!isset($_COOKIE[$key])) {
            return;
        }

        $path = '/';
        if (!empty($url)) {
            $parsed = parse_url($url);
            $path = empty($parsed['path']) ? '/' : $parsed['path'];
        }

        if (is_array($_COOKIE[$key])) {
            foreach ($_COOKIE[$key] as $name => $val) {
                setcookie($key . '[' . $name . ']', '', time() - 2592000, $path);
            }
        } else {
            setcookie($key, '', time() - 2592000, $path);
        }
    }

}
