<?php
/**
 * @author miaokuan
 */

namespace Wee;

require __DIR__ . '/ssdb/SSDB.php';

class SSDB
{
    public static function factory()
    {
        $host = '127.0.0.1';
        $port = 8888;
        $ssdb = new \SimpleSSDB($host, $port, $timeout_ms = 20000);
        return $ssdb;
    }
}
