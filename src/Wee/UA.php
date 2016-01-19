<?php
/**
 * @author miaokuan
 */

namespace Wee;

class UA
{
    static $resource = null;

    public $agent = '';

    public $is_browser = false;
    public $is_robot = false;
    public $is_mobile = false;

    public $browser = '';
    public $platform = '';
    public $mobile = '';
    public $robot = '';
    public $version = '';

    public static function load()
    {
        if (null === self::$resource) {
            self::$resource = include __DIR__ . '/ua/resource.php';
        }
    }

    public static function factory($agent = null)
    {
        return new static($agent);
    }

    public function __construct($agent = null)
    {
        if (null === $agent) {
            $agent = isset($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : '';
        }

        if ($agent != '') {
            $this->agent = $agent;
            self::load();
            $this->_parse();
        }
    }

    protected function _parse()
    {
        $this->_parsePlatform();

        foreach (array('_parseBrowser', '_parseRobot', '_parseMobile') as $parser) {
            if ($this->$parser() === true) {
                break;
            }
        }
    }

    protected function _parsePlatform()
    {
        if (is_array(self::$resource['platforms']) && count(self::$resource['platforms'])) {
            foreach (self::$resource['platforms'] as $key => $val) {
                if (false !== (stripos($this->agent, $key))) {
                    $this->platform = $val;
                    return true;
                }
            }
        }
        return false;
    }

    protected function _parseBrowser()
    {
        if (is_array(self::$resource['browsers']) && count(self::$resource['browsers'])) {
            foreach (self::$resource['browsers'] as $key => $val) {
                if (preg_match("|" . preg_quote($key) . ".*?([0-9\.]+)|i", $this->agent, $match)) {
                    $this->is_browser = true;
                    $this->version = $match[1];
                    $this->browser = $val;
                    $this->_parseMobile();
                    return true;
                }
            }
        }

        return false;
    }

    protected function _parseMobile()
    {
        if (is_array(self::$resource['mobiles']) && count(self::$resource['mobiles'])) {
            foreach (self::$resource['mobiles'] as $key => $val) {
                if (false !== (stripos($this->agent, $key))) {
                    $this->is_mobile = true;
                    $this->mobile = $val;
                    return true;
                }
            }
        }

        return false;
    }

    protected function _parseRobot()
    {
        if (is_array(self::$resource['robots']) && count(self::$resource['robots'])) {
            foreach (self::$resource['robots'] as $key => $val) {
                if (false !== (stripos($this->agent, $key))) {
                    $this->is_robot = true;
                    $this->robot = $val;
                    return true;
                }
            }
        }

        return false;
    }

}
