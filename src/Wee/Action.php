<?php
/**
 * @author miaokuan
 */

namespace Wee;

use Haf\Action as HafAction;
use Haf\Response;
use Wee\Str;

abstract class Action extends HafAction
{

    public function json($data)
    {
        $callback = $this->request->get('callback');
        $callback = preg_replace('/[^a-z0-9_]/i', '', $callback);

        // jsonp
        if ('' != $callback) {
            header('Content-Type: application/javascript');
            echo $callback . '(' . json_encode($data) . ');';
            return true;
        } else {
            // json
            header('Content-type: application/json');
            echo json_encode($data);
            return true;
        }
    }

    /**
     * csv format
     * 0 开头是不留空，以行为单位。
     * 1 可含或不含列名，含列名则居文件第一行。
     * 2 一行数据不垮行，无空行。
     * 3 以半角符号，作分隔符，列为空也要表达其存在。
     * 4 列内容如存在半角逗号（即,）则用半角引号（即""）将该字段值包含起来。（ad中有可能出现逗号："\""+ ad + "\"" ）
     * 5 列内容如存在半角引号（即"）则应替换成半角双引号（""）转义。
     * 6 文件读写时引号，逗号操作规则互逆。
     * 7 内码格式不限，可为ASCII、Unicode或者其他。
     *
     * output csv format data
     * @param null $filename
     * @return bool
     */
    public function csv($filename = null)
    {
        $view = $this->initView();
        $template = $this->template(null, null, '.csv');
        $content = $view->render($template, true);

        $content = mb_convert_encoding($content, 'gbk', 'utf-8');

        if (empty($filename)) {
            $filename = Str::rand(8) . '.csv';
        }

        Response::download($filename);
        echo $content;
        return true;
    }
}
