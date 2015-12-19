<?php
/**
 * @author miaokuan
 */

namespace Wee;

require __DIR__ . '/XML/xml2json.php';

class XML
{
    public static function json($xml)
    {
        return xml2json::transformXmlStringToJson($xml);
    }
}
