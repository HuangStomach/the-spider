<?php

namespace Gini\ORM;

class Site extends Object
{
    public $name = 'string:250';
    public $fqdn = 'string:500';
    public $address = 'string:250';
    public $status = 'string:100'; // 服务器的状态
    public $free = 'double'; // 磁盘剩余容量百分比
    public $top = 'double'; // 负载
    // public $report;             // 我为什么声明了一个report字段
    public $level = 'int,default:0'; // 报警等级
    public $active = 'bool'; // 是否激活
    public $update = 'datetime';

    protected static $db_index = [
        'unique:address, fqdn', 'name',
        'status', 'free', 'top',
        'level', 'active'
    ];

    const LEVEL_SLEEP = 0;
    const LEVEL_DIM = 10;
    const LEVEL_NORMAL = 20;
    const LEVEL_INFO = 30;
    const LEVEL_WARN = 40;
    const LEVEL_ERROR = 50;
    const LEVEL_FATAL = 60;

    // 严重等级 永远取最严重(最大)的等级来处理
    public static $levels = [
        self::LEVEL_SLEEP => '休眠',
        self::LEVEL_DIM => '待机',
        self::LEVEL_NORMAL => '正常',
        self::LEVEL_INFO => '信息',
        self::LEVEL_WARN => '警告',
        self::LEVEL_ERROR => '错误',
        self::LEVEL_FATAL => 'GG',
    ];

    public function level() {
        $siteLevel = a('site/level')->whose('site')->is($this);
        // TODO: 这里就不动态了 能不能动态取呢？
        return max($siteLevel->disk, $siteLevel->host, $siteLevel->http,
            $siteLevel->load, $siteLevel->ntp);
    }

    public function format() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'fqdn' => $this->fqdn,
            'address' => $this->address,
            'status' => $this->status,
            'free' => $this->free,
            'top' => $this->top,
            'level' => $this->level,
            'active' => $this->active,
            'update' => $this->update,
        ];
    }
}