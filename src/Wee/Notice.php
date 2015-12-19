<?php
/**
 * @author miaokuan
 */

namespace Wee;

class Notice
{
    const __NOTICE = 'notice';
    const __WARNING = 'warning';
    const __ERROR = 'warning';

    static $_key = '__notice__';

    /**
     * set notice
     * @param string $message
     * @param string $type notice|warning|error
     */
    public static function set($message, $type = self::__NOTICE)
    {
        $value = json_encode(array('message' => $message, 'type' => $type));
        Cookie::set(self::$_key, $value);
    }

    public static function get()
    {
        $notice = json_decode(Cookie::get(self::$_key));
        Cookie::remove(self::$_key);
        return $notice;
    }

}
