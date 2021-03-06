<?php
/**
 * @author miaokuan
 */

namespace Wee;

class Log
{
    /**
     * Log levels can be enabled
     */
    const FATAL = 100;
    const WARNING = 200;
    const NOTICE = 300;
    const INFO = 400;
    const DEBUG = 500;

    static $instance = null;

    protected $logfile;

    /**
     * Verbosity level for the running script.
     */
    protected $level = 200;

    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public static function level($level = null)
    {
        if (null === $level) {
            return self::instance()->level;
        } else {
            self::instance()->level = intval($level);
        }
    }

    public static function logfile($logfile)
    {
        self::instance()->logfile = $logfile;
    }

    protected function __clone()
    {}

    protected function __construct()
    {}

    protected function write($message, $level = self::INFO)
    {
        switch ($level) {
            case self::DEBUG:
                $label = 'DEBUG  ';
                break;
            case self::INFO:
                $label = 'INFO   ';
                break;
            case self::NOTICE:
                $label = 'NOTICE ';
                break;
            case self::WARNING:
                $label = 'WARNING';
                break;
            case self::FATAL:
                $label = 'FATAL  ';
                break;
        }

        list($ts, $ms) = explode('.', sprintf("%f", microtime(true)));
        $ds = date('Y-m-d H:i:s') . '.' . str_pad($ms, 6, 0);
        $prefix = "[$ds] $label";
        $log = $prefix . ' ' . str_replace("\n", "\n$prefix ", trim($message)) . "\n";

        if (substr(php_sapi_name(), 0, 3) == 'cli') {
            echo $log;
        }

        if ($level > $this->level) {
            return;
        }

        if ($this->logfile) {
            $file = $this->logfile;
        } else {
            $file = VAR_DIR . '/log/' . date('Ymd') . '.log';
        }

        if ($level <= self::WARNING) {
            $file .= '.wf';
        }

        file_put_contents($file, $log, FILE_APPEND);
    }

    public static function debug($message)
    {
        self::instance()->write($message, self::DEBUG);
    }

    public static function info($message)
    {
        self::instance()->write($message, self::INFO);
    }

    public static function notice($message)
    {
        self::instance()->write($message, self::NOTICE);
    }

    public static function warning($message)
    {
        self::instance()->write($message, self::WARNING);
    }

    public static function fatal($message)
    {
        self::instance()->write($message, self::FATAL);
    }

}
