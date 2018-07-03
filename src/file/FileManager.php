<?php
namespace kucms\file;

class FileManager
{

    /**
     * 遍历文件夹
     *
     * @param string $dir            
     * @return array
     */
    public static function listDir($dir = '.')
    {
        $list = [];
        foreach (scandir($dir) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            } else {
                $list[] = self::fileInfo($dir . DS . $file);
            }
        }
        return $list;
    }

    /**
     * 删除文件或文件夹
     *
     * @param string $path            
     * @return boolean
     */
    public static function delFile($path, $self = false)
    {
        if (is_file($path)) {
            @unlink($path);
        } elseif (is_dir($path)) {
            $dh = opendir($path);
            if ($dh) {
                while (($file = readdir($dh)) !== false) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    } else {
                        self::delFile($path . DS . $file, true);
                    }
                }
            }
            closedir($dh);
            
            // 删除文件夹
            $self && rmdir($path);
        }
    }

    /**
     * 文件信息
     *
     * @param string $file            
     * @return array
     */
    public static function fileInfo($file)
    {
        return [
            'file' => $file,
            'stat' => stat($file),
            'size' => filesize($file),
            'type' => is_dir($file) ? 'dir' : 'file'
        ];
    }
}
