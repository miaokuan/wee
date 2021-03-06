<?php
/**
 * @author miaokuan
 */

namespace Wee;

use Haf\Response;

class Common
{
    protected static $config = array(
        'autoload' => false,
        'timezone' => 'Asia/Shanghai',
        'exception' => false,
    );

    public static function init(array $config = null)
    {
        if (is_array($config)) {
            self::$config = array_merge(self::$config, $config);
        }

        /**
         * timezone
         */
        date_default_timezone_set(self::$config['timezone']);

        /**
         * GPC
         */
        if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
            $_GET = self::stripslashes($_GET);
            $_POST = self::stripslashes($_POST);
            $_COOKIE = self::stripslashes($_COOKIE);
            reset($_GET);
            reset($_POST);
            reset($_COOKIE);
        }

        /**
         * autoload
         */
        if (self::$config['autoload']) {
            if (function_exists('__autoload')) {
                spl_autoload_register('__autoload');
            }

            spl_autoload_register(array('Wee\\Common', 'autoload'));
        }

        /**
         * exception handler
         */
        set_exception_handler(array('Wee\\Common', 'exceptionHandler'));

        /**
         * error handler
         */
        set_error_handler(array('Wee\\Common', 'errorHandler'));
    }

    public static function stripslashes($value)
    {
        $value = is_array($value) ?
        array_map(array('Wee\\Common', 'stripslashes'), $value) :
        stripslashes($value);
        return $value;
    }

    public static function addslashes($value)
    {
        $value = is_array($value) ?
        array_map(array('Wee\\Common', 'addslashes'), $value) :
        addslashes($value);
        return $value;
    }

    public static function autoload($class)
    {
        $file = __DIR__ . '/../' . strtr($class, '\\', '/') . '.php';
        if (file_exists($file)) {
            require $file;
            return true;
        }
    }

    public static function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        $l = error_reporting();
        if ($l & $errno) {

            $exit = false;
            switch ($errno) {
                case E_USER_ERROR:
                    $type = 'Fatal Error';
                    $exit = true;
                    break;
                case E_USER_WARNING:
                case E_WARNING:
                    $type = 'Warning';
                    break;
                case E_USER_NOTICE:
                case E_NOTICE:
                case @E_STRICT:
                    $type = 'Notice';
                    break;
                case @E_RECOVERABLE_ERROR:
                    $type = 'Catchable';
                    break;
                default:
                    $type = 'Unknown Error';
                    $exit = true;
                    break;
            }

            $exception = new \ErrorException($type . ': ' . $errstr, 0, $errno, $errfile, $errline);

            if ($exit) {
                exceptionHandler($exception);
                exit(1);
            } else {
                throw $exception;
            }

        }
        return false;
    }

    public static function exceptionHandler($e)
    {

        $log = $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
        Log::Warning('Unhandled Exception' . $log);

        if (self::$config['exception']) {
            $code = $e->getCode();
            echo "Code:$code\n";

            $errfile = $e->getFile();
            echo "File:$errfile\n";

            $errline = $e->getLine();
            echo "Line:$errline\n";

            echo "Message:\n";
            echo $e->getMessage(), "\n";
            echo $e->getTraceAsString(), "\n";

            // var_dump($e->getTrace());

            echo "\n\n";
        } else {
            self::error($e);
        }

        // exit(1);
    }

    public static function error($e)
    {
        $isException = is_object($e);

        if ($isException) {
            $code = $e->getCode();
            $message = $e->getMessage();
        } else {
            $code = $e;
        }

        switch ($code) {
            case 500:
                $message = 'Internal Server Error!';
                break;
            case 404:
                // $message = "Not Found! <!--$code-->";
                $code = 404;
                break;
            case 403:
                $message = 'Forbidden';
                break;
            default:
                $message = 'Error!';
        }

        @ob_end_clean();
        ob_start();
        header('Content-Type: text/html; charset=utf-8', true);
        if (is_numeric($code) && $code > 200) {
            Response::setStatus($code);
        }
        $message = nl2br($message);
        print <<<EOF
<!doctype html><html><head><meta charset="utf-8" />
<title>{$code}</title>
<meta http-equiv="refresh" content="5; url=/" />
<style type="text/css">
body{background: #f7fbe9;font-family: "Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana;}
#error {background: #333;width: 360px;margin: 0 auto;margin-top: 100px;color: #fff;padding: 10px;
}
a{color:#fff;text-decoration:none}
h1 {padding: 10px;margin: 0;font-size: 36px;}
p {padding: 0 20px 20px 20px;margin: 0;font-size: 12px;}
img {padding: 0 0 5px 260px;}
</style>
</head>
<body>
<div id="error">
<h1>{$code}</h1>
<p>{$message}</p>
<p><a href="/">请访问首页</a></p>
</div>
</body>
</html>

EOF;

    }

}
