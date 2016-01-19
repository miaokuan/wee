<?php
/**
 * $Id: Registry.php 473 2014-09-25 14:43:05Z svn $
 * @author miaokuan
 */

namespace Wee;

class Registry
{
    protected static $registry = array();

    public static function get($key = null)
    {
        if (null === $key) {
            return self::$registry;
        }

        $key = (string) $key;
        return isset(self::$registry[$key]) ? self::$registry[$key] : null;
    }

    public static function set($key, $value)
    {
        $key = (string) $key;
        self::$registry[$key] = $value;
        return true;
    }

    public static function remove($key)
    {
        $key = (string) $key;
        if (isset(self::$registry[$key])) {
            unset(self::$registry[$key]);
        }
        return true;
    }

    public static function clear()
    {
        self::$registry = array();
    }
}
