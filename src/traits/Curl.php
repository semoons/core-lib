<?php
namespace kucms\traits;

use CURLFile;

trait Curl
{

    /**
     * 请求JSON
     *
     * @param string $url            
     * @param string $data            
     * @param string $header            
     * @param array $option            
     * @return array
     */
    public static function doCurlJson($url, $data = null, $header = null, $option = [])
    {
        return json_decode(self::doCurl($url, $data, $header, $option), true);
    }

    /**
     * 请求URL
     *
     * @param string $url            
     * @param string $data            
     * @param string $header            
     * @param array $option            
     * @return mixed
     */
    public static function doCurl($url, $data = null, $header = null, $option = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        // 请求头
        empty($header) || curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        
        // HTTPS
        if (strpos($url, 'https://') !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        
        // 提交数据
        if (! empty($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, self::transPostFile($data));
        }
        
        // 代理
        if (isset($option['proxy'])) {
            $proxy_config = $option['proxy'];
            curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXY, $proxy_config['host']);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_config['port']);
            isset($proxy_config['user']) && curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_config['user'] . ':' . $proxy_config['passwd']);
        }
        
        // 跳转
        isset($option['location']) && curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $option['location']);
        
        // 超时
        isset($option['timeout']) && curl_setopt($ch, CURLOPT_TIMEOUT, $option['timeout']);
        
        // 头信息
        isset($option['header']) && curl_setopt($ch, CURLOPT_HEADER, $option['header']);
        
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    /**
     * 上传文件
     *
     * @param array $data            
     * @return array
     */
    public static function transPostFile($data)
    {
        if (PHP_VERSION_ID < 50500 || ! is_array($data)) {
            return $data;
        }
        foreach ($data as &$v) {
            if (is_string($v) && substr($v, 0, 1) == '@') {
                $file_path = substr($v, 1);
                is_file($file_path) && ($v = new CURLFile($file_path));
            }
        }
        return $data;
    }
}