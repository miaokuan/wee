<?php
/**
 * @author miaokuan
 */

namespace Wee;

use Haf\Front;
use Haf\Request;
use Wee\Log;
use Wee\Timer;

class App extends Front
{
    protected $mypid = 0;

    protected $pidfile = '';

    protected $namespace = 'AppController\\';
    
    protected $sign = '';

    /**
     * concurrent num
     */
    protected $concurrent = 1;

    /**
     * profile enable
     */
    public $profile_enable = false;

    public $params = array();

    protected $request;

    public function dispatch(Request $request = null)
    {
        Timer::start('run');

        if (null === $request) {
            $this->request = Request::singleton();
        } else {
            $this->request = $request;
        }

        $this->mypid = getmypid();
        $mypid = $this->mypid;
        $this->bootstrap();
        
        $app = $this->request->getController();
        $action = $this->request->getAction();
        Log::info("Begin to execute. [app:$app action:$action pid:$mypid]");

        parent::dispatch($this->request);

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

        // controller
        if (!empty($argv[1])) {
            $this->request->setController($argv[1]);
        }
        $this->sign = str_replace('\\', '_', $this->request->getController());

        // action
        if (!empty($argv[2])) {
            $this->request->setAction($argv[2]);
        }

        // params
        if (!empty($argv[3])) {
            parse_str($argv[3], $params);
            $this->request->setParams($params);
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
        $pidfile = VAR_DIR . '/pid/' . $this->sign . '_' . $this->mypid . '.pid';
        if (file_put_contents($pidfile, $this->mypid)) {
            $this->pidfile = $pidfile;
        } else {
            Log::fatal("Unable to write pid to " . $pidfile);
            exit;
        }

        $pidfiles = glob(VAR_DIR . '/pid/' . $this->sign . "*.pid");
        if (is_array($pidfiles) && (count($pidfiles) > $this->concurrent)) {
            $text = "pid file(" . $pidfiles[0] . ") existed.";
            Log::fatal($text);
            exit;
        }
    }

    protected function initlog()
    {
        $logfile = VAR_DIR . '/log/' . $this->sign . '_' . $this->mypid . '.log.' . date('Ymd');
        Log::logfile($logfile);
        Log::level(Log::INFO);
    }

    public function __destruct()
    {
        if (!empty($this->pidfile) && file_exists($this->pidfile)) {
            if (!unlink($this->pidfile)) {
                Log::warning("Could not delete pid file " . $this->pidfile);
            }
        }
    }

}
