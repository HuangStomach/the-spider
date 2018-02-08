<?php
namespace Gini\Controller\CGI;

class Host extends Base\Rest
{
    public function postDefault () {
        $form = $this->form('post');
        $last = $form['lasthostcheck'];
        $fqdn = $form['hostname'];
        $address = $form['hostaddress'];
        $state = $form['hoststate']; // 主机状态
        $attempt = $form['hostattempt']; // 插件检测
        $type = $form['hoststatetype']; // 主机状态?
        $runtime = $form['hostexecutiontime']; // 检测主机状态所用的时间
        $output = $form['hostoutput']; // 主机输出
        $perf = $form['hostperfdata']; // 插件返回的额外数据

        $site = a('site')->ensure($fqdn, $address); // TODO: 是否对server的设置一定要在这里做? 能否放到异步任务做处理?
        $record = a('record/host');
        $record->site = $site;
        $record->state = $state;
        $record->attempt = $attempt;
        $record->type = $type;
        $record->runtime = $runtime;
        $record->last = $last;
        $record->output = $output;
        $record->perf = $perf;
        $record->save();

        if ($record->id) {
            // 异步处理任务 发送HTTP的广播以及记录
            $data = ['trigger' => 'status', $record];
            $this->env['swoole']->task($data);
        }
        else {
            // 日志记录有误 也应该进行某些记录操作 来进行日志记录有误标志
        }

        return \Gini\IoC::construct('\Gini\CGI\Response\Json', true);
    }
}
