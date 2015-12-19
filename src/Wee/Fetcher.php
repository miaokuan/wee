<?php
/**
 * @author miaokuan
 */

namespace Wee;

class Fetcher
{
    public static function wget($url, $file, $timeout = 120, $retry = 3)
    {
        $opt = array("-O $file");

        if (!empty($retry)) {
            $opt[] = "--tries=$retry";
        }

        if ($timeout > 0) {
            $opt[] = "--timeout=$timeout";
        }

        $options = implode(' ', $opt);
        $url = escapeshellarg($url);
        $cmd = "wget $options '$url'";
        system($cmd, $ret);
        if ($ret !== 0) {
            return false;
        }
        return true;
    }

    /**
     * must $params['url']
     */
    public static function curl($params)
    {
        $timeout = 120;
        if (isset($params['timeout'])) {
            $timeout = $params['timeout'];
        }

        if (empty($params['method'])) {
            $params['method'] = 'GET';
        }

        $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36';
        if (isset($params['user_agent'])) {
            $user_agent = $params['user_agent'];
        }

        $header = array(
            "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Encoding:gzip",
            "Accept-Language:zh-CN,zh;q=0.8",
            "Cache-Control:max-age=0",
            "Connection:keep-alive",
        );
        if (isset($params['host']) && $params['host']) {
            $header[] = "Host: " . $host;
        }
        if (isset($params['header']) && $params['header']) {
            $header[] = $params['header'];
        }

        $ch = curl_init();
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //@curl_setopt ($ch, CURLOPT_VERBOSE , 1 );
        @curl_setopt($ch, CURLOPT_HEADER, 1);

        if ($params['method'] == "HEAD") {
            @curl_setopt($ch, CURLOPT_NOBODY, 1);
        }
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        @curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if (isset($params['referer'])) {
            @curl_setopt($ch, CURLOPT_REFERER, $params['referer']);
        }
        @curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        if (isset($params['cookie'])) {
            @curl_setopt($ch, CURLOPT_COOKIE, $params['cookie']);
        }

        if ($params['method'] == "POST") {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params['post']));
        }
        @curl_setopt($ch, CURLOPT_URL, $params['url']);
        @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        if (isset($params['login']) && isset($params['password'])) {
            @curl_setopt($ch, CURLOPT_USERPWD, $params['login'] . ':' . $params['password']);
        }

        @curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $result = array(
            'header' => '',
            'body' => '',
            'curl_error' => '',
            'http_code' => '',
            'last_url' => '');
        if ($error != "") {
            $result['curl_error'] = $error;
            return $result;
        }

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $result['header'] = substr($response, 0, $header_size);
        $result['body'] = substr($response, $header_size);
        $result['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result['last_url'] = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        $gzip = 'ncoding: gzip';
        if (strpos($result['header'], $gzip)) {
            $result['body'] = gzdecode($result['body']);
        }

        return $result;
    }

    /**
     * Send a GET requst using cURL
     * @param string $url to request
     * @param array $get values to send
     * @param array $options for cURL
     * @return string
     */
    public static function get($url, array $get = null, array $options = array())
    {
        if (!empty($get)) {
            $url .= (strpos($url, '?') === false ? '?' : '') . http_build_query($get);
        }
        $defaults = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36',
        );

        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));

        $header = array(
            "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            // "Accept-Encoding:gzip",
            "Accept-Language:zh-CN,zh;q=0.8",
            "Cache-Control:max-age=0",
            "Connection:keep-alive",
        );
        @curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $result = array(
            'header' => '',
            'body' => '',
            'curl_error' => '',
            'http_code' => '',
            'last_url' => '');
        if ($error != "") {
            $result['curl_error'] = $error;
            return '';
        }

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $result['header'] = substr($response, 0, $header_size);
        $result['body'] = substr($response, $header_size);
        $result['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result['last_url'] = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        // var_dump($result['header']);
        $gzip = 'ncoding: gzip';
        if (strpos($result['header'], $gzip)) {
            $result['body'] = gzdecode($result['body']);
        }

        return $result['body'];
    }

    /**
     * Send a POST requst using cURL
     * @param string $url to request
     * @param array $post values to send
     * @param array $options for cURL
     * @return string
     */
    public function post($url, array $post = null, array $options = array())
    {
        $defaults = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 1,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_POSTFIELDS => http_build_query($post),
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36',
        );

        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));

        $header = array(
            "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            // "Accept-Encoding:gzip",
            "Accept-Language:zh-CN,zh;q=0.8",
            "Cache-Control:max-age=0",
            "Connection:keep-alive",
        );
        @curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $result = array(
            'header' => '',
            'body' => '',
            'curl_error' => '',
            'http_code' => '',
            'last_url' => '');
        if ($error != "") {
            $result['curl_error'] = $error;
            return '';
        }

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $result['header'] = substr($response, 0, $header_size);
        $result['body'] = substr($response, $header_size);
        $result['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result['last_url'] = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        // var_dump($result['header']);
        $gzip = 'ncoding: gzip';
        if (strpos($result['header'], $gzip)) {
            $result['body'] = gzdecode($result['body']);
        }

        return $result['body'];
    }

}
