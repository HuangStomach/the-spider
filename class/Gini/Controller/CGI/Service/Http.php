<?php
namespace Gini\Controller\CGI\Service;

use \Gini\CGI\Response;

class Http extends \Gini\Controller\CGI\Restful {
    public function get($id = 0) {
        $http = a('record/http', $id);
        
        if (!$http->id) {
            $code = 404;
            $response = '没有找到对应的信息';
            goto output;
        }
        $response = $http->format();

        output:
        return new Response\JSON($response, $code);
    }

    public function fetch() {
        $form = $this->form('get');

        $https = those('record/http');

        if ($form['site']) {
            $site = a('site', $form['site']);
            if (!$site->id) {
                $code = 404;
                $response = '没有找到对应的站点';
                goto output;
            }
            $https->whose('site')->is($site);
        }

        // 对可以通用的字段进行统配查询
        foreach (['state', 'type', 'runtime', 'delay', 'ctime'] as $key) {
            $this->query($https, $key);
        }
        
        if ($form['sortby'] && $form['order']) {
            $https->orderBy((string)$form['sortby'], (string)$form['order']);
        }
        
        // 成对出现 limit之前再取totalCount
        $response['total'] = $https->totalCount();
        list($start, $per) = $form['limit'] ? : [0, 20];
        $https->limit(max(0, $start), min($per, 100));

        $response['data'] = [];
        if ($https->totalCount()) foreach ($https as $http) {
            $response['data'][] = $http->format();
        }

        output:
        return new Response\JSON($response, $code);
    }

    public function post() {
        $form = $this->form('post');
        $service = $form['servicedesc'];
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
            \Gini\Logger::of('http')->alert($content);
            goto output;
        }

        $record = a('record/http');
        $record->site = $site;
        $record->service = $service;
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
            \Gini\Logger::of('http')->alert($content);
        }

        output:
        return new Response\Json($res);
    }
}
