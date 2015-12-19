<?php
/**
 * @author miaokuan
 */

namespace Wee;

class Date
{

    public static function word($from, $now = null)
    {
        if (null === $now) {
            $now = time();
        }
        $between = $now - $from;

        if ($between > 0 && $between < 86400 && idate('d', $from) == idate('d', $now)) {
            if ($between < 3600 && idate('H', $from) == idate('H', $now)) {
                if ($between < 60 && idate('i', $from) == idate('i', $now)) {
                    $second = idate('s', $now) - idate('s', $from);
                    if (0 == $second) {
                        return '刚刚';
                    } else {
                        return sprintf('%d秒前', $second);
                    }
                }

                $min = idate('i', $now) - idate('i', $from);
                return sprintf('%d分钟前', $min);
            }

            $hour = idate('H', $now) - idate('H', $from);
            return sprintf('%d小时前', $hour);
        }

        if ($between > 0 && $between < 172800 && (idate('z', $from) + 1 == idate('z', $now) || idate('z', $from) > 2 + idate('z', $now))) {
            return sprintf('昨天 %s', date('H:i', $from));
        }

        if ($between > 0 && $between < 604800 && idate('W', $from) == idate('W', $now)) {
            $day = intval($between / (3600 * 24));
            return sprintf('%d天前', $day);
        }

        if ($between > 0 && $between < 31622400 && idate('Y', $from) == idate('Y', $now)) {
            return date('n月j日', $from);
        }

        return date('Y年m月d日', $from);
    }

    /**
     * 从普通时间返回Linux时间截(strtotime中文处理版)
     * @parem string $dtime
     * @return int
     */
    public static function strtotime($dtime)
    {
        if (!preg_match("/[^0-9]/", $dtime)) {
            return $dtime;
        }

        $dtime = trim($dtime);
        $dt = array(1970, 1, 1, 0, 0, 0);
        $dtime = preg_replace("/[\r\n\t]|日|秒/", " ", $dtime);
        $dtime = str_replace("年", "-", $dtime);
        $dtime = str_replace("月", "-", $dtime);
        $dtime = str_replace("时", ":", $dtime);
        $dtime = str_replace("分", ":", $dtime);
        $dtime = trim(preg_replace("/[ ]{1,}/", " ", $dtime));
        $ds = explode(" ", $dtime);
        $ymd = explode("-", $ds[0]);
        if (!isset($ymd[1])) {
            $ymd = explode(".", $ds[0]);
        }
        if (isset($ymd[0])) {
            $dt[0] = $ymd[0];
        }
        if (isset($ymd[1])) {
            $dt[1] = $ymd[1];
        }

        if (isset($ymd[2])) {
            $dt[2] = $ymd[2];
        }

        if (strlen($dt[0]) == 2) {
            $dt[0] = '20' . $dt[0];
        }

        if (isset($ds[1])) {
            $hms = explode(":", $ds[1]);
            if (isset($hms[0])) {
                $dt[3] = $hms[0];
            }

            if (isset($hms[1])) {
                $dt[4] = $hms[1];
            }

            if (isset($hms[2])) {
                $dt[5] = $hms[2];
            }
        }

        foreach ($dt as $k => $v) {
            $v = preg_replace("/^0{1,}/", '', trim($v));
            if ($v == '') {
                $dt[$k] = 0;
            }
        }
        $mt = mktime($dt[3], $dt[4], $dt[5], $dt[1], $dt[2], $dt[0]);

        if (!empty($mt)) {
            return $mt;
        }

        return strtotime($dtime);
    }

    public static function standard($fmt = 'DATE_RFC822', $now = null)
    {
        if (null === $now) {
            $now = time();
        }

        $formats = array(
            'DATE_ATOM' => 'Y-m-dTH:i:sQ',
            'DATE_COOKIE' => 'l, d-M-y H:i:s UTC',
            'DATE_ISO8601' => 'Y-m-dTH:i:sQ',
            'DATE_RFC822' => 'D, d M y H:i:s O',
            'DATE_RFC850' => 'l, d-M-y H:i:s UTC',
            'DATE_RFC1036' => 'D, d M y H:i:s O',
            'DATE_RFC1123' => 'D, d M Y H:i:s O',
            'DATE_RSS' => 'D, d M Y H:i:s O',
            'DATE_RFC2822' => 'D, d M Y H:i:s O',
            'DATE_RFC3339' => 'Y-m-d\TH:i:sP',
            'DATE_W3C' => 'Y-m-dTH:i:sQ',
        );

        if (!isset($formats[$fmt])) {
            return false;
        }

        return date($formats[$fmt], $now);
    }
}
