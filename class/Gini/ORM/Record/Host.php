<?php

namespace Gini\ORM\Record;

class Host extends \Gini\ORM\Object
{
    public $site    = 'object:site';
    public $state   = 'int';
    public $ip      = 'string:100';
    public $status  = 'string:100'; // 服务器的状态
    public $free    = 'double'; // 磁盘剩余容量百分比
    public $top     = 'double'; // 负载
    // public $report;             // 我为什么声明了一个report字段
    public $ctime  = 'datetime';

    protected static $db_index = [
        'unique:ip, fqdn', 'name',
    ];

}