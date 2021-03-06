<?php
namespace Gini\Controller\CGI;

use \Gini\CGI\Response;

class Site extends Restful {
    public function get($id = 0) {
        $site = a('site', $id);
        
        if (!$site->id) {
            $code = 404;
            $response = '没有找到对应的服务器信息';
            goto output;
        }

        $response = $site->format();

        output:
        return new Response\JSON($response, $code);
    }

    public function fetch() {
        $form = $this->form('get');

        $sites = those('site');
        if ($form['name']) $sites->whose('name')->contains($form['name']);
        if ($form['lab']) $sites->whose('lab')->is($form['lab']);
        if ($form['fqdn']) $sites->whose('fqdn')->contains($form['fqdn']);
        if ($form['address']) $sites->whose('address')->contains($form['address']);

        // 对可以通用的字段进行统配查询
        foreach (['status', 'active'] as $key) {
            $this->query($sites, $key);
        }
        
        if ($form['sortby'] && $form['order']) {
            $sites->orderBy((string)$form['sortby'], (string)$form['order']);
        }
        
        // 成对出现 limit之前再取totalCount
        $response['total'] = $sites->totalCount();
        list($start, $per) = $form['limit'] ? : [0, 20];
        $sites->limit(max(0, $start), min($per, 100));

        $response['data'] = [];
        if ($sites->totalCount()) foreach ($sites as $site) {
            $response['data'][] = $site->format();
        }

        return new Response\JSON($response, $code);
    }

    public function put($id = 0) {
        $site = a('site', $id);
        
        if (!$site->id) {
            $code = 404;
            $response = '没有找到对应的服务器信息';
            goto output;
        }
        $form = $this->form('post');
        $site->name = $form['name'];
        $site->lab = $form['lab'];
        $site->site = $form['site'];
        $site->active = $form['active'];
        if ($site->save()) $response = $site->format();
        else {
            $code = 500;
            $response = '服务器信息更新失败';
        }

        output:
        return new Response\JSON($response, $code);
    }
}
