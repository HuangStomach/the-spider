<?php
namespace Gini\Controller\CGI\Service;

use \Gini\CGI\Response;

class Ntp extends \Gini\Controller\CGI\Restful {
    public function get($id = 0) {
        $ntp = a('record/ntp', $id);
        
        if (!$ntp->id) {
            $code = 404;
            $response = '没有找到对应的信息';
            goto output;
        }
        $response = $ntp->format();

        output:
        return new Response\JSON($response, $code);
    }

    public function fetch() {
        $form = $this->form('get');

        $ntps = those('record/ntp');

        if ($form['site']) {
            $site = a('site', $form['site']);
            if (!$site->id) {
                $code = 404;
                $response = '没有找到对应的站点';
                goto output;
            }
            $ntps->whose('site')->is($site);
        }

        // 对可以通用的字段进行统配查询
        foreach (['state', 'type', 'runtime', 'delay', 'ctime'] as $key) {
            $this->query($ntps, $key);
        }
        
        if ($form['sortby'] && $form['order']) {
            $ntps->orderBy((string)$form['sortby'], (string)$form['order']);
        }
        
        // 成对出现 limit之前再取totalCount
        $response['total'] = $ntps->totalCount();
        list($start, $per) = $form['limit'] ? : [0, 20];
        $ntps->limit(max(0, $start), min($per, 100));

        $response['data'] = [];
        if ($ntps->totalCount()) foreach ($ntps as $ntp) {
            $response['data'][] = $ntp->format();
        }

        output:
        return new Response\JSON($response, $code);
    }
    
    public function post() {
        $form = $this->form('post');
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
        $site = \Gini\Model\Site::ensure($form); // TODO: 是否对server的设置一定要在这里做? 能否放到异步任务做处理?
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
        $record->last = date('Y-m-d H:i:s', $last);
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
        return new Response\Json($res);
    }
}
