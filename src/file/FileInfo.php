<?php
namespace kucms\file;

class FileInfo
{

    /**
     * FINFO
     *
     * @var unknown
     */
    const MIME_FINFO = 'finfo';

    /**
     * 命令行
     *
     * @var unknown
     */
    const MIME_CMD = 'cmd';

    /**
     * 文件大小
     *
     * @param string $path            
     * @return number
     */
    public static function getFileSize($path)
    {
        if (is_file($path)) {
            return filesize($path);
        } elseif (substr($path, 0, 6) == 'ftp://') {
            return self::getFileSizeFtp($path);
        } elseif (substr($path, 0, 4) == 'http') {
            return self::getFileSizeHttp($path);
        } else {
            return 0;
        }
    }

    /**
     * 远程文件大小
     *
     * @param string $url            
     * @return number
     */
    public static function getFileSizeHttp($url)
    {
        try {
            $res = get_headers($url, true);
            return isset($res['Content-Length']) ? $res['Content-Length'] : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * FTP文件大小
     *
     * @param string $path            
     * @param string $username            
     * @param string $password            
     * @return number
     */
    public static function getFileSizeFtp($path, $username = 'anonymous', $password = '')
    {
        try {
            $query = parse_url($path);
            $connection = ftp_connect($query['host'], isset($query['port']) ? $query['port'] : 21);
            ftp_login($connection, $username, $password);
            $file_size = ftp_size($connection, $query['path']);
            return $file_size ? $file_size : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * 文件后缀名
     *
     * @param string $path            
     * @param string $type            
     * @return string
     */
    public static function getFileExt($path, $type = self::MIME_FINFO)
    {
        $mime = self::getFileMime($path, $type);
        return $mime ? self::mimeToExt($mime) : null;
    }

    /**
     * 文件MIME
     *
     * @param string $path            
     * @param string $type            
     * @return string
     */
    public static function getFileMime($path, $type = self::MIME_FINFO)
    {
        switch ($type) {
            case self::MIME_CMD:
                $mime = self::getFileMimeByCmd($path);
                break;
            default:
                $mime = self::getFileMimeByFinfo($path);
                break;
        }
        return $mime;
    }

    /**
     * FINFO获取文件MIME
     *
     * @param string $path            
     * @return string
     */
    public static function getFileMimeByFinfo($path)
    {
        if (empty($path) || ! is_file($path)) {
            return null;
        }
        
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->file($path);
    }

    /**
     * 命令行获取文件MIME
     *
     * @param string $path            
     * @return string
     */
    public static function getFileMimeByCmd($path)
    {
        if (empty($path) || ! is_file($path)) {
            return null;
        }
        
        $info = exec('file -ib "' . $path . '"');
        list ($type, $temp) = explode(';', $info);
        return $type;
    }

    /**
     * MIME转后缀名
     *
     * @param string $mime            
     * @return string
     */
    public static function mimeToExt($mime)
    {
        static $mime_map;
        if (empty($mime_map)) {
            $mime_map = include (__DIR__ . '/Mime.php');
        }
        return isset($mime_map[$mime]) ? $mime_map[$mime] : null;
    }
}