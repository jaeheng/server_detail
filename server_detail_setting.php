<?php
!defined('EMLOG_ROOT') && exit('access deined!');

if (!class_exists('ServerDetail', false)) {
    include __DIR__ . '/server_detail_class.php';
}

function plugin_setting_view()
{
    $CACHE = Cache::getInstance();
    $sta = $CACHE->readCache('sta');
    ?>
    <style>
        .table th {
            width: 180px;
        }

        .table td {
            word-break: break-all;
        }
    </style>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">服务器信息</h1>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <h6 class="card-header">服务器信息</h6>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>服务器软件</th>
                            <td><?= $_SERVER['SERVER_SOFTWARE']; ?></td>
                        </tr>
                        <tr>
                            <th>操作系统</th>
                            <td><?= ServerDetail::getInstance()->getUname(); ?></td>
                        </tr>
                        <tr>
                            <th>磁盘空间</th>
                            <?php
                            $disk_usage = ServerDetail::getInstance()->getDiskUsage();
                            $percent = floatval($disk_usage['percent']);
                            $usage = $disk_usage['usage'];
                            $total_size = $disk_usage['total_size'];
                            ?>
                            <td>
                                <div style="display: flex">
                                    <div style="width: 50%;margin-right: 20px;">
                                        <div class="progress" style="margin-top: 5px;">
                                            <div
                                                    class="progress-bar"
                                                    role="progressbar"
                                                    style="width: <?= $percent ?>%"
                                                    aria-valuenow="<?= $percent ?>"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100"><?= round($percent, 2) ?>%
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <?= changeFileSize($usage); ?> / <?= changeFileSize($total_size); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>域名</th>
                            <td><?= $_SERVER['HTTP_HOST']; ?></td>
                        </tr>
                        <tr>
                            <th>服务器IP</th>
                            <td><?= ServerDetail::getInstance()->getIp(); ?></td>
                        </tr>
                        <tr>
                            <th>通信协议</th>
                            <td><?= $_SERVER['SERVER_PROTOCOL']; ?></td>
                        </tr>
                        <tr>
                            <th>PHP部署方式</th>
                            <td><?= php_sapi_name(); ?></td>
                        </tr>
                        <tr>
                            <th>时区</th>
                            <td><?= Option::get('timezone'); ?></td>
                        </tr>
                        <tr>
                            <th>系统负载</th>
                            <td><?= implode(', ', array_map(function ($item) {
                                    return round(floatval($item), 2);
                                }, sys_getloadavg())); ?></td>
                        </tr>
                        <tr>
                            <th>当前时间</th>
                            <td><?= date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="card" style="margin-top: 20px;">
                <h6 class="card-header">Emlog</h6>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>访问地址</th>
                            <td><a href="<?= BLOG_URL; ?>" target="_blank"><?= BLOG_URL; ?></a></td>
                        </tr>
                        <tr>
                            <th>博客名称</th>
                            <td><?= Option::get('blogname'); ?></td>
                        </tr>
                        <tr>
                            <th>Emlog注册</th>
                            <td><?= Register::isRegLocal() ? '<span class="text-success">正版已注册</span>' : '尚未完成正版注册 <a href="' . BLOG_URL . '/admin/auth.php" style="color:red;">去注册</a> <a href="https://www.emlog.net/?ic=SX1I7B5D" target="_blank">购买正版</a>' ?></td>
                        </tr>
                        <tr>
                            <th>您的角色</th>
                            <td><?= ROLE; ?></td>
                        </tr>
                        <tr>
                            <th>版本</th>
                            <td><?= Option::EMLOG_VERSION; ?></td>
                        </tr>
                        <tr>
                            <th>MySQL版本</th>
                            <?php
                            $DB = Database::getInstance();
                            $mysql_ver = $DB->getVersion();
                            ?>
                            <td><?= $mysql_ver; ?></td>
                        </tr>
                        <tr>
                            <th>博客占用大小</th>
                            <td><?= ServerDetail::getInstance()->getBlogSize(); ?></td>
                        </tr>
                        <tr>
                            <th>当前模版</th>
                            <td><?= Option::get('nonce_templet'); ?></td>
                        </tr>
                        <tr>
                            <th>开启的插件</th>
                            <td><?php
                                $server_detail_version = 'unknown';
                                $pluginModel = new Plugin_Model();
                                $plugins = Option::get('active_plugins');
                                if (is_array($plugins) && !empty($plugins)) {
                                    foreach ($plugins as $item) {
                                        if (file_exists(EMLOG_ROOT . '/content/plugins/' . $item)) {
                                            $plugin = $pluginModel->getPluginData($item);
                                            if ($plugin) {
                                                echo "<a href='{$plugin['Url']}' target='_blank'>{$plugin['Name']} ({$plugin['Version']})</a>、";
                                                if ($plugin['Plugin'] === 'server_detail') {
                                                    $server_detail_version = $plugin['Version'];
                                                }
                                            }
                                        }
                                    }
                                }
                                ?></td>
                        </tr>
                        <tr>
                            <th>用户数量</th>
                            <td><?= count($CACHE->readCache('user')); ?></td>
                        </tr>
                        <tr>
                            <th>文章数量</th>
                            <td>已发表: <?= $sta['lognum']; ?> 草稿: <?= $sta['draftnum']; ?></td>
                        </tr>
                        <tr>
                            <th>评论数量</th>
                            <td>总: <?= $sta['comnum_all']; ?> 已审核: <?= $sta['comnum']; ?>
                                未审核: <?= $sta['hidecomnum']; ?></td>
                        </tr>
                        <tr>
                            <th>微语数量</th>
                            <td><?= $sta['note_num']; ?></td>
                        </tr>
                        <tr>
                            <th>总阅读量</th>
                            <td><?php
                                $sql = "select sum(views) as views, sum(comnum) as comnum from " . DB_PREFIX . 'blog';
                                $db = Database::getInstance();
                                $res = $db->query($sql)->fetch_assoc();
                                echo $res['views'];
                                ?></td>
                        </tr>
                        <tr>
                            <th>总评论数</th>
                            <td><?= $res['comnum']; ?></td>
                        </tr>
                        <tr>
                            <th>文件上传最大限制</th>
                            <td><?= changeFileSize(Option::get('att_maxsize') * 1024); ?></td>
                        </tr>
                        <tr>
                            <th>允许上传的文件类型</th>
                            <td><?= Option::get('att_type'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <h6 class="card-header">PHP信息</h6>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>php版本</th>
                            <td><?= phpversion(); ?></td>
                        </tr>
                        <tr>
                            <th title="max_execution_time">最大执行时间</th>
                            <td><?= ini_get('max_execution_time'); ?>秒</td>
                        </tr>
                        <tr>
                            <th title="upload_max_filesize">最大上传大小</th>
                            <td><?= ini_get('upload_max_filesize'); ?>
                                (emlog注册用户上传最大限制<?= changeFileSize(Option::get('att_maxsize') * 1024); ?>)
                            </td>
                        </tr>
                        <tr>
                            <th title="post_max_size">最大post大小</th>
                            <td><?= ini_get('post_max_size'); ?></td>
                        </tr>
                        <tr>
                            <th title="memory_limit">最大内存限制</th>
                            <td><?= ini_get('memory_limit'); ?></td>
                        </tr>
                        <tr>
                            <th title="display_errors">开启报错</th>
                            <td><?= ini_get('display_errors') ? '已开启' : '未开启'; ?></td>
                        </tr>
                        <tr>
                            <th>PHP脚本所有者</th>
                            <td><?= get_current_user(); ?></td>
                        </tr>
                        <tr>
                            <th>禁用的函数</th>
                            <td><?= ini_get('disable_functions') ?: '无'; ?></td>
                        </tr>
                        <tr>
                            <th>curl版本</th>
                            <td><?= curl_version()['version']; ?></td>
                        </tr>
                        <tr>
                            <th>zip扩展</th>
                            <td><?= class_exists('ZipArchive', false) ? '已安装' : '未安装'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="card" style="margin-top: 20px;">
                <h6 class="card-header">其它信息</h6>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>根目录</th>
                            <td><?= EMLOG_ROOT; ?></td>
                        </tr>
                        <tr>
                            <th>php.ini文件</th>
                            <td><?= php_ini_loaded_file(); ?></td>
                        </tr>
                        <tr>
                            <th>php安装目录</th>
                            <td><?= PHP_BINDIR; ?></td>
                        </tr>
                        <tr>
                            <th>emlog插件路径</th>
                            <td><?= EMLOG_ROOT . '/content/plugins/'; ?></td>
                        </tr>
                        <tr>
                            <th>emlog模版路径</th>
                            <td><?= TPLS_PATH; ?></td>
                        </tr>
                        <tr>
                            <th>emlog后台模版路径</th>
                            <td><?= ADMIN_TEMPLATE_PATH; ?></td>
                        </tr>
                        <tr>
                            <th>cookie路径</th>
                            <td><?= ini_get('session.cookie_path') ?: '/'; ?></td>
                        </tr>
                        <tr>
                            <th>session路径</th>
                            <td><?= ini_get('session.save_path') ?: '/tmp'; ?></td>
                        </tr>
                        <tr>
                            <th>扩展</th>
                            <td><?= implode(', ', get_loaded_extensions()); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="card" style="margin-top: 20px;">
                <div class="card-body">
                    <div class="card-head">
                        <span>服务器信息插件信息</span>
                    </div>
                    <hr/>
                    <table class="table table-bordered">
                        <tr>
                            <th>版本号</th>
                            <td><?= $server_detail_version; ?></td>
                        </tr>
                        <tr>
                            <th>作者主页</th>
                            <td><?php
                                $options = Cache::getInstance()->readCache('options');
                                $blog_name = $options['blogname'];
                                $blog_url = $options['blogurl'];
                                ?>
                                <a href="https://blog.phpat.com" target="_blank">
                                    <img src="https://blog.phpat.com/logo.png&url=<?= base64_encode($blog_url); ?>&blogname=<?= $blog_name; ?>&type=server_detail&url_type=base64"
                                         style="width: 1.2em;height:1.2em;vertical-align: middle" alt="server_detail"/>
                                    子恒博客
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>github</th>
                            <td>
                                <a href="https://github.com/jaeheng/server_detail" target="_blank">https://github.com/jaeheng/server_detail</a>
                            </td>
                        </tr>
                        <tr>
                            <th>Emlog应用商店</th>
                            <td>
                                <a href="https://www.emlog.net/plugin/detail/584" target="_blank">https://www.emlog.net/plugin/detail/584</a>
                                (<a href="https://www.emlog.net/author/index/74" target="_blank">获取更多模版/插件</a>)
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <script>
        setTimeout(hideActived, 3600);
        $("#menu_category_ext").addClass('active');
        $("#menu_ext").addClass('show');
        $("#server_detail").addClass('active');
    </script>
<?php }