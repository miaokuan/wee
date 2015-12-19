<?php
/**
 * @author miaokuan
 */

namespace Wee;

class Xpath
{
    protected $dom;

    protected $charset = 'utf-8';

    public static function factory()
    {
        return new self();
    }

    public function dom($c)
    {
        $c = mb_convert_encoding($c, 'HTML-ENTITIES', $this->charset);

        // 剔除多余的 HTML 编码标记，避免解析出错
        preg_match("/charset=([\w|\-]+);?/", $c, $match);
        if (isset($match[1])) {
            $c = preg_replace("/charset=([\w|\-]+);?/", "", $c, 1);
        }

        $this->dom = new DOMDocument('1.0', $this->charset);
        try {
            //libxml_use_internal_errors(true);
            // 会有些错误信息，不过不要紧 :^)
            if (!@$this->dom->loadHTML('<?xml encoding="' . $this->charset . '">' . $c)) {
                throw new Exception("Parse HTML Error!");
            }

            foreach ($this->dom->childNodes as $item) {
                if ($item->nodeType == XML_PI_NODE) {
                    $this->dom->removeChild($item); // remove hack
                }
            }

            // insert proper
            $this->dom->encoding = $this->charset;
        } catch (Exception $e) {

        }

        return $this;
    }

    public function find($xp)
    {
        //var_dump($this->dom);
        $xpath = new DOMXpath($this->dom);
        //$xpath->registerNamespace('html','http://www.w3.org/1999/xhtml');

        $eles = $xpath->query($xp);
        //var_dump($eles);
        if ($eles->length == 0) {
            return '';
        }
        //var_dump($eles);
        $ele = $eles->item(0);
        $dom = new DOMDocument;
        $dom->appendChild($dom->importNode($ele, true));
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;
        $c = $dom->saveHtml();
        $c = mb_convert_encoding($c, $this->charset, 'HTML-ENTITIES');
        return $c;
    }

}
