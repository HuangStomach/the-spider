<?php

namespace Gini\Model\Record;

class Ntp
{
    
    /**
     * 将符合Ntp条件的请求进行构造并存储
     *
     * @param [\Gini\Event] $e
     * @param [\Gini\ORM\Record\Ntp] $record
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

        $res = false;
        $site = \Gini\Model\Site::ensure($fqdn, $address); // TODO: 是否对server的设置一定要在这里做? 能否放到异步任务做处理?
        if (!$site->id) {
            $content = "FQDN[{$fqdn}]服务器对象无法生成";
            \Gini\Logger::of('ntp')->alert($content);
            goto output;
        }

        $record = a('record/ntp');
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
            // 异步处理任务 发送HTTP的广播以及记录
            // $data = ['trigger' => 'status', $record];
            // $swoole->task($data);
            $res = true;
        }
        else {
            // 日志记录有误 也应该进行某些记录操作 来进行日志记录有误标志
            $content = "FQDN[{$fqdn}]发送的数据无法保存成功";
            \Gini\Logger::of('ntp')->alert($content);
        }

        output:
        return $res;
    }
}