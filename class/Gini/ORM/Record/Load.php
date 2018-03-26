<?php

namespace Gini\ORM\Record;

class Load extends \Gini\ORM\Object
{
    public $site    = 'object:site';
    public $state   = 'string:50'; // 主机状态
    public $attempt = 'int'; // 尝试次数
    public $type    = 'string:50'; // 主机状态?
    public $runtime = 'double'; // 检测主机状态所用的时间
    public $delay   = 'double'; // 服务检测的时间延时
    public $output  = 'string:*'; // 插件返回的性能数据
    public $perf    = 'string:*'; // 插件返回的额外数据
    public $content = 'string:*'; // 插件返回全文
    public $last    = 'datetime'; // 上次测试的时间
    public $ctime   = 'datetime';

    function save() {
        if (!$this->ctime) $this->ctime = date('Y-m-d H:i:s');
        return parent::save();
    }

    const STATE_LEVEL = [
        
    ];

    function level() {
        
    }
}