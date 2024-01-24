<?php
!defined('EMLOG_ROOT') && exit('access deined!');

class ServerDetail {

    private static $_instance;

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
    public function isEnabled($func) {
        return is_callable($func) && false === stripos(ini_get('disable_functions'), $func);
    }

    /**
     * 使用shell_exec运行命令
     * @param $command
     * @return false|string|null
     */
    public function processShell($command) {
        if ($this->isEnabled('shell_exec')) {
            return shell_exec($command);
        }
        return 'shell_exec函数不可用';
    }

    /**
     * 获取服务器内存大小
     * @return float|int
     */
    public function getServerMemorySize() {
        if (!$this->isEnabled('shell_exec')) {
            return 'shell_exec函数不可用';
        }
        // 获取操作系统类型
        $os = strtoupper(PHP_OS);

        // 根据操作系统类型使用不同的命令,兼容mac/linux
        if (strpos($os, 'DARWIN') === 0) {
            // macOS
            $output = $this->processShell('sysctl hw.memsize');
            $mem = explode(" ", $output);
        } else {
            // Linux
            $output = $this->processShell('free -b');
            $lines = explode("\n", $output);
            $mem = explode(":", $lines[1]);
        }
        return changeFileSize((int) $mem[1]);
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
        while (false!==($FolderOrFile = readdir($handle)))
        {
            if($FolderOrFile != "." && $FolderOrFile != "..")
            {
                if(is_dir("$dir/$FolderOrFile"))
                {
                    $sizeResult += $this->getDirSize("$dir/$FolderOrFile");
                } else {
                    $sizeResult += filesize("$dir/$FolderOrFile");
                }
            }
        }
        closedir($handle);
        return $sizeResult;
    }

    /**
     * 获取cpu信息
     * @return array
     */
    function getServerCpuInfo() {
        $cpu = array();
        $os = strtoupper(PHP_OS);
        if (!$this->isEnabled('shell_exec')) {
            return [
                'model' => '-',
                'cores' => '-',
                'mhz' => '-',
                'cache' => '-',
            ];
        }

        if (strpos($os, 'DARWIN') === 0) {
            // macOS
            $output = $this->processShell('sysctl -n machdep.cpu.brand_string');

            if (!empty($output)) {
                $cpu['model'] = trim($output);
                $cpu['cores'] = $this->processShell('sysctl -n hw.ncpu');
                $cpu['mhz'] = $this->processShell('sysctl -n hw.cpufrequency');
                $cpu['cache'] = '';
            }
        } else {
            // Linux
            $output = $this->processShell('cat /proc/cpuinfo | grep "model name\\|cores\\|cpu MHz\\|cache size"');

            if (!empty($output)) {
                $output = explode("\n", $output);
                foreach ($output as $line) {
                    $fields = explode(':', $line, 2);
                    $key = trim($fields[0]);
                    $value = trim($fields[1]);

                    switch ($key) {
                        case 'model name':
                            $cpu['model'] = $value;
                            break;
                        case 'cpu MHz':
                            $cpu['mhz'] = $value;
                            break;
                        case 'cache size':
                            $cpu['cache'] = $value;
                            break;
                        case 'cores':
                            $cpu['cores'] = $value;
                            break;
                    }
                }
            }
        }

        return $cpu;
    }

    public function formatCpuInfo() {
        $result = '';
        $cpu = $this->getServerCpuInfo();

        if (!$cpu) {
            return 'CPU型号: unknown';
        }

        if (!empty($cpu['model'])) {
            $result .= 'CPU型号: ' . $cpu['model'] . ', ';
        }

        if (!empty($cpu['cores'])) {
            $result .= '核心数: ' . $cpu['cores'] . ', ';
        }

        if (!empty($cpu['mhz'])) {
            $result .= '频率: ' . round($cpu['mhz'] / 1000, 2) . ' GHz';
        }

        return rtrim($result, ', ');
    }

    public function getUname() {
        return php_uname('s') . ' ' . php_uname('m');
    }

    public function getDiskUsage() {
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

    public function getIp() {
        return $this->processShell('curl https://pangheng.com/ifconfig.php');
    }

    public function getBlogSize() {
        return changeFileSize($this->getDirSize(EMLOG_ROOT));
    }

}