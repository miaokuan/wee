<?php

namespace Wee;

require __DIR__ . '/markdown/HTML_To_Markdown.php';
require __DIR__ . '/markdown/Parsedown.php';

class Markdown
{
    public static function text($html = null, $overrides = null)
    {
        $md = new \HTML_To_Markdown($overrides);
        return $md->convert($html);
    }

    public static function html($text)
    {
        $html = \Parsedown::instance()->text($text);
        return $html;
    }
}
