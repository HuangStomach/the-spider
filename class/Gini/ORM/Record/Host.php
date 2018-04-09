<?php

namespace Gini\ORM\Record;

class Host extends \Gini\ORM\Object
{
    public $site = 'object:site';
    public $state = 'string:50'; // 主机状态
    public $attempt = 'int'; // 尝试次数
    public $type = 'string:50'; // ?
    public $runtime = 'double'; // 检测所用时间
    public $output = 'string:*'; // 测试输出
    public $perf = 'string:*'; // 结果详细信息
    public $last = 'datetime'; // 上次测试的时间
    public $ctime = 'datetime';

    function save() {
        if (!$this->ctime) $this->ctime = date('Y-m-d H:i:s');
        return parent::save();
    }

    function level() {
        switch ($this->state) {
            case 'DOWN':
                return Site::LEVEL_FATAL;
            case 'UP':
                return Site::LEVEL_NORMAL;
            default:
                return Site::LEVEL_DIM;
        }
    }
}