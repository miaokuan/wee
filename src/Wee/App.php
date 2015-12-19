<?php
/**
 * @author miaokuan
 */

namespace Wee;

class App
{
    protected $mypid = 0;

    protected $pidfile = '';

    protected $module = '';

    protected $app = 'demo';

    protected $action = 'run';

    /**
     * concurrent num
     */
    protected $concurrent = 1;

    /**
     * profile enable
     */
    public $profile_enable = false;

    public $params = array();

    public function __construct($module = '')
    {
        if (!empty($module)) {
            $this->module = $module;
        }

        $this->mypid = getmypid();
        $this->bootstrap();
    }

    public function run()
    {
        Timer::start('run');

        $this->doRun();

        Timer::end('run');
        $time = Timer::cal('run');
        Log::info('Memory usage: ' .
            round(memory_get_usage() / 1024 / 1024, 2) . 'MB (peak: ' .
            round(memory_get_peak_usage() / 1024 / 1024, 2) . 'MB), time: ' .
            $time . 'us');
    }

    protected function bootstrap()
    {
        global $argv;

        // app
        if (!empty($argv[1])) {
            $this->app = $argv[1];
        }

        // action
        if (!empty($argv[2])) {
            $this->action = $argv[2];
        }

        // params
        if (!empty($argv[3])) {
            parse_str($argv[3], $params);
            $this->params = $params;
        }

        // concurrent
        if (!empty($this->params['concurrent'])) {
            $this->concurrent = $this->params['concurrent'];
        }

        // profile enable
        $this->profile_enable = empty($this->params['profile_enable']) ?
        false : true;

        // log
        $this->initlog();

        // pidfile
        $this->initpid();
    }

    protected function initpid()
    {
        $pidfile = VAR_DIR . '/pid/' . $this->app . '_' . $this->mypid . '.pid';
        if (file_put_contents($pidfile, $this->mypid)) {
            $this->pidfile = $pidfile;
        } else {
            Log::fatal("Unable to write pid to " . $pidfile);
            exit;
        }

        $pidfiles = glob(VAR_DIR . '/pid/' . $this->app . "*.pid");
        if (is_array($pidfiles) && (count($pidfiles) > $this->concurrent)) {
            $text = "pid file(" . $pidfiles[0] . ") existed.";
            Log::fatal($text);
            exit;
        }
    }

    protected function initlog()
    {
        $logfile = VAR_DIR . '/log/' . $this->app . '_' . $this->mypid . '.log';
        Log::logfile($logfile);
        Log::level(Log::INFO);
    }

    public function doRun()
    {
        $pid = $this->mypid;
        $app = $this->app;
        $action = $this->action . 'Action';
        Log::info("Begin to execute. [app:$app action:$action pid:$pid]");

        $class = $this->format($app);
        if (!class_exists($class)) {
            Log::fatal("Failed to find class:$class");
            return;
        }

        $obj = new $class($this->params);
        if (!method_exists($obj, $action)) {
            Log::fatal("Failed to find method:$action");
            return;
        }

        Log::info("Calling method[$action] for $app.");
        $log = array();
        $debug = $obj->$action($log);

        // log
        $this->loger($log);

        // debug
        $this->debuger($debug);

        Log::info("Execute finished. [app:$app action:$action process id:$pid]");
    }

    protected function debuger($debug)
    {
        if (!is_scalar($debug)) {
            $debug = explode("\n", trim(print_r($debug, true)));
        } elseif (strlen($debug) > 256) {
            $debug = substr($debug, 0, 256) . '...(truncated)';
        }

        if (is_array($debug)) {
            foreach ($debug as $ln) {
                Log::debug($ln);
            }
        } else {
            Log::debug($debug);
        }
    }

    protected function loger($log)
    {
        if (empty($log)) {
            return;
        }

        foreach ($log as $l) {
            if (!is_scalar($l)) {
                $l = explode("\n", trim(print_r($l, true)));
            } elseif (strlen($l) > 256) {
                $l = substr($l, 0, 256) . '...(truncated)';
            }

            if (is_array($l)) {
                foreach ($l as $ln) {
                    Log::info($ln);
                }
            } else {
                Log::info($l);
            }
        }
    }

    public function __destruct()
    {
        if (!empty($this->pidfile) && file_exists($this->pidfile)) {
            if (!unlink($this->pidfile)) {
                Log::warning("Could not delete pid file " . $this->pidfile);
            }
        }
    }

    protected function format($app)
    {
        $arr = explode('.', $app);
        foreach ($arr as $key => $word) {
            $word = ucfirst(strtolower($word));
            $arr[$key] = $word;
        }
        $class = ucfirst(strtolower($this->module)) .
        '\\' . implode('\\', $arr);
        return $class;
    }

}
