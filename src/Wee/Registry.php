<?php
/**
 * $Id: Registry.php 473 2014-09-25 14:43:05Z svn $
 * @author miaokuan
 */

namespace Wee;

class Registry
{
    static $_registry = array();

    public static function get($key = null)
    {
        if (null === $key) {
            return self::$_registry;
        }

        $key = (string) $key;
        return isset(self::$_registry[$key]) ? self::$_registry[$key] : null;
    }

    public static function set($key, $value)
    {
        $key = (string) $key;
        self::$_registry[$key] = $value;
        return true;
    }

    public static function remove($key)
    {
        $key = (string) $key;
        if (isset(self::$_registry[$key])) {
            unset(self::$_registry[$key]);
        }
        return true;
    }

    public static function clear()
    {
        self::$_register = array();
    }
}
