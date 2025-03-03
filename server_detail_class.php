<?php
!defined('EMLOG_ROOT') && exit('Access denied!');

class ServerDetail
{

    private static $_instance;

    public $sysctl = '/usr/sbin/sysctl';

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function server_detail_sidebar()
    {
        $url = BLOG_URL . "admin/plugin.php?plugin=server_detail";
        echo '<a class="collapse-item" id="server_detail" href="' . $url . '">服务器信息</a>';
    }

    /**
     * 检查某函数是否可用
     * @param $func
     * @return bool
     */
    public function isEnabled($func)
    {
        return is_callable($func) && false === stripos(ini_get('disable_functions'), $func);
    }

    /**
     * 获取目录大小
     * @param $dir
     * @return int
     */
    public function getDirSize($dir)
    {
        $handle = opendir($dir);
        if (!$handle) {
            return 0;
        }
        $sizeResult = 0;
        while (false !== ($FolderOrFile = readdir($handle))) {
            if ($FolderOrFile != "." && $FolderOrFile != "..") {
                if (is_dir("$dir/$FolderOrFile")) {
                    $sizeResult += $this->getDirSize("$dir/$FolderOrFile");
                } else {
                    $sizeResult += filesize("$dir/$FolderOrFile");
                }
            }
        }
        closedir($handle);
        return $sizeResult;
    }

    public function getUname()
    {
        return php_uname('s') . ' ' . php_uname('m');
    }

    public function getDiskUsage()
    {
        $total_size = disk_total_space('.');
        $free_size = disk_free_space('.');
        $usage = $total_size - $free_size;
        return [
            'percent' => ($usage / $total_size) * 100,
            'usage' => $usage,
            'total_size' => $total_size,
            'free_size' => $free_size
        ];
    }

    public function getIp()
    {
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        return file_get_contents('https://blog.phpat.com/ifconfig.php', false, stream_context_create($arrContextOptions));
    }

    public function getBlogSize()
    {
        return changeFileSize($this->getDirSize(EMLOG_ROOT));
    }

}