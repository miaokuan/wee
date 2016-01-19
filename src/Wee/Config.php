<?php
/**
 * @author miaokuan
 */

namespace Wee;

class Config
{
    public static function load($name, $piece = null)
    {
        $name = preg_replace('#[^a-z0-9A-Z_-]#', '', $name);
        $config = include ROOT_DIR . "/config/$name.php";

        if (null !== $piece && isset($config[$piece])) {
            return $config[$piece];
        }

        return $config;
    }

}
