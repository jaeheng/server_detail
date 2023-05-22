<?php
/*
Plugin Name: 服务器信息
Version: 1.0.1
Plugin URL:
Description: 查看服务器详细信息
Author: jaeheng
Author URL: https://blog.zhangziheng.com
*/

!defined('EMLOG_ROOT') && exit('access deined!');

function server_detail_sidebar()
{
    $url = BLOG_URL . "admin/plugin.php?plugin=server_detail";
    echo '<a class="collapse-item" id="server_detail" href="' . $url . '">服务器信息</a>';
}

function server_detail_dashboard()
{
    $url = BLOG_URL . "admin/plugin.php?plugin=server_detail";
    $link = '<a style="float: right;" href="' . $url . '">查看服务器信息</a>';
    echo "<script>$('.card-header:contains(\"站点信息\")').append('{$link}')</script>";
}


addAction('adm_menu_ext', 'server_detail_sidebar');
addAction('adm_main_content', 'server_detail_dashboard');