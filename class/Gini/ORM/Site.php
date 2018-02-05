<?php

namespace Gini\ORM;

class Site extends \Gini\ORM\Object
{
    public $name    = 'string:250';
    public $fqdn    = 'string:500';
    public $ip      = 'string:100';
    public $status  = 'string:100'; // 服务器的状态
    public $free    = 'double'; // 磁盘剩余容量百分比
    public $top     = 'double'; // 负载
    // public $report;             // 我为什么声明了一个report字段
    public $update  = 'datetime';

    protected static $db_index = [
        'unique:ip, fqdn', 'name',
    ];

}