<?php

namespace Gini\Model\Record;

class Disk
{
    
    /**
     * 将符合Disk条件的请求进行构造并存储
     *
     * @param [\Gini\Event] $e
     * @param [\Gini\ORM\Record\Disk] $record
     * @return void
     */
    function structure ($e, $swoole, $form) {
        $last = $form['lastservicecheck'];
        $fqdn = $form['hostname'];
        $address = $form['hostaddress'];
        $state = $form['servicestate']; // 主机状态
        $attempt = $form['serviceattempt']; // 插件检测
        $type = $form['servicestatetype']; // 主机状态?
        $runtime = $form['serviceexecutiontime']; // 检测主机状态所用的时间
        $delay = $form['servicelatency']; // 服务检测的时间延时
        $output = $form['serviceoutput']; // 插件返回的性能数据
        $perf = $form['serviceperfdate']; // 插件返回的额外数据
        $content = $form['longserviceoutput']; // 插件返回全文

        $site = \Gini\Model\Site::ensure($fqdn, $address); // TODO: 是否对server的设置一定要在这里做? 能否放到异步任务做处理?
        $record = a('record/disk');
        $record->site = $site;
        $record->state = $state;
        $record->attempt = $attempt;
        $record->type = $type;
        $record->runtime = $runtime;
        $record->delay = $delay;
        $record->last = $last;
        $record->output = $output;
        $record->perf = $perf;
        $record->content = $content;
        $record->save();

        if ($record->id) {
            // 异步处理任务 发送Disk的广播以及记录
            $data = ['trigger' => 'status', $record];
            $this->env['swoole']->task($data);
        }
        else {
            // 日志记录有误 也应该进行某些记录操作 来进行日志记录有误标志
        }
    }
}