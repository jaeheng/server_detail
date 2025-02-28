<?php
/*
Plugin Name: 服务器信息
Version: 1.1.4
Plugin URL: https://www.emlog.net/plugin/detail/584
Description: 查看服务器详细信息,包括硬件信息、PHP信息、emlog数据统计、其它相关配置信息。
Author: 子恒博客
Author URL: https://blog.phpat.com
*/

!defined('EMLOG_ROOT') && exit('Access denied!');

if (!class_exists('ServerDetail', false)) {
    include __DIR__ . '/server_detail_class.php';
}

function server_detail_dashboard()
{
    $disk_usage = ServerDetail::getInstance()->getDiskUsage();
    $percent = $disk_usage['percent'];
    $usage = changeFileSize($disk_usage['usage']);
    $total_size = changeFileSize($disk_usage['total_size']);
    $cpu = ServerDetail::getInstance()->formatCpuInfo();
    $mem = ServerDetail::getInstance()->getServerMemorySize();

    echo sprintf('<div class="col-lg-6 mb-4">
        <div class="card bg-light text-primary shadow">
            <div class="card-body">
                <div style="display: inline-block;">内存容量: %s,</div>
                <div style="display: inline-block;">%s,</div>
                <span>磁盘容量：</span>
                <div style="display: inline-block;width: 220px;">
                    <div class="progress" style="margin-top: 5px;">
                        <div
                        class="progress-bar"
                        role="progressbar"
                        style="width: %d%%"
                        aria-valuenow="<?= $percent?>"
                        aria-valuemin="0"
                        aria-valuemax="100">%d%% %s/%s</div>
                    </div>
                </div>
                <div class="text-primary-50 small">--来自服务器信息插件，<a class="text-primary" href="plugin.php?plugin=server_detail">查看更多数据</a></div>
            </div>
        </div>
    </div>', $mem, $cpu, $percent, $percent, $usage, $total_size);
}


addAction('adm_menu_ext', function () {
    ServerDetail::getInstance()->server_detail_sidebar();
});
addAction('adm_main_content', 'server_detail_dashboard');