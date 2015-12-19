<?php
/**
 * $Id: Execute.php 473 2014-09-25 14:43:05Z svn $
 * @author miaokuan
 */

namespace Wee;

class Exec
{
    public static function run($cmd, &$out, &$ret, $maxRetryTimes = 0)
    {
        $retry = 0;
        do {
            sleep($retry * 3);
            $out = array();
            exec($cmd, $out, $ret);

            if (0 == $ret) {
                break;
            }
        } while ($retry++ < $maxRetryTimes);

        return ($ret == 0);
    }

    public static function killProcess($name)
    {
        $cmd = self::KillProcessCmd($name);
        self::run($cmd, $out, $ret);
        return $ret;
    }

    public static function KillProcessCmd($name)
    {
        $cmd = "PID=`ps axu | grep $name | grep -v 'grep $name' | awk -F ' ' '{print \$2}' | head -1`;";
        $cmd .= "\nif [ -z \"\$PID\" ]; then\n\texit 0;\nfi\n";
        $cmd .= "CPIDS=`pgrep -P \$PID`;";
        $cmd .= "\nif [ -n \"\$CPIDS\" ]; then kill -TERM \$CPIDS;fi;\nkill \$PID;";
        return $cmd;
    }

}
