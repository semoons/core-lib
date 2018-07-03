<?php
namespace kucms\traits;

trait Format
{

    /**
     * 结果格式化
     *
     * @param integer $code            
     * @param string $msg            
     * @param string $data            
     * @return array
     */
    public static function formatResult($code, $msg = '', $data = '')
    {
        return array(
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        );
    }

    /**
     * 跳转格式化
     *
     * @param integer $code            
     * @param string $msg            
     * @param string $url            
     * @param string $data            
     * @param number $wait            
     * @return array
     */
    public static function formatJump($code, $msg = '', $url = '', $data = '', $wait = 3)
    {
        return array(
            'code' => $code,
            'msg' => $msg,
            'url' => $url,
            'data' => $data,
            'wait' => $wait
        );
    }
}