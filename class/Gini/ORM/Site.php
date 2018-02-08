<?php

namespace Gini\ORM;

class Site extends \Gini\ORM\Object
{
    public $name    = 'string:250';
    public $fqdn    = 'string:500';
    public $address = 'string:250';
    public $status  = 'string:100'; // 服务器的状态
    public $free    = 'double'; // 磁盘剩余容量百分比
    public $top     = 'double'; // 负载
    // public $report;             // 我为什么声明了一个report字段
    public $level   = 'int,default:0'; // 报警等级
    public $active  = 'bool'; // 是否激活
    public $update  = 'datetime';

    protected static $db_index = [
        'unique:ip, fqdn', 'name',
        'status', 'free', 'top',
        'level'
    ];

    const LEVEL_SLEEP = 0;
    const LEVEL_DIM = 10;
    const LEVEL_NORMAL = 20;
    const LEVEL_INFO = 30;
    const LEVEL_WARN = 40;
    const LEVEL_ERROR = 50;
    const LEVEL_FATAL = 60;
    // 严重等级 永远取最严重(最大)的等级来处理
    public static $statuses = [
        self::LEVEL_SLEEP => '待平台审批',
        self::LEVEL_DIM => '待专家论证',
        self::LEVEL_NORMAL => '待院所审批',
        self::LEVEL_INFO => '待财务预算审批',
        self::LEVEL_WARN => '待财务处审批',
        self::LEVEL_ERROR => '待校级审批',
        self::LEVEL_FATAL => '待设备处审批',
    ];
}