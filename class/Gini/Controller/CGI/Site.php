<?php
namespace Gini\Controller\CGI;

use \Gini\CGI\Response;

class Site extends \Gini\Controller\CGI
{
    public function get($id = 0) {
        $site = a('site', $id);
        
        if (!$site->id) {
            $code = 404;
            $response = '没有找到对应的送样信息';
            goto output;
        }

        $response = [
            'id' => $site->id,
            'name' => $site->name,
            'fqdn' => $site->fqdn,
            'address' => $site->address,
            'status' => $site->status,
            'free' => $site->free,
            'top' => $site->top,
            'level' => $site->level,
            'active' => $site->active,
            'update' => $site->update,
        ];

        output:
        return new Response\JSON($response, $code);
    }

    public function fetch() {
        $form = $this->form('get');

        $sites = those('site');
        if ($form['name']) $sites->whose('name')->contains($form['name']);
        if ($form['fqdn']) $sites->whose('fqdn')->contains($form['fqdn']);
        if ($form['address']) $sites->whose('address')->contains($form['address']);
        if ($form['status']) $sites->whose('status')->is($form['status']);
        if ($form['active']) $sites->whose('active')->is($form['active']);
        if ($form['sortby'] && $form['order']) $sites->orderBy((string)$form['sortby'], (string)$form['order']);
        
        // 成对出现 limit之前再取totalCount
        $response['total'] = $sites->totalCount();
        if ($form['limit']) {
            list($start, $per) = $form['limit'];
            $sites->limit($start, $per);
        }
        else {
            $sites->limit(0, 20);
        }

        $response['data'] = [];
        if ($sites->totalCount()) foreach ($sites as $site) {
            $response['data'][] = [
                'id' => $site->id,
                'name' => $site->name,
                'fqdn' => $site->fqdn,
                'address' => $site->address,
                'status' => $site->status,
                'free' => $site->free,
                'top' => $site->top,
                'level' => $site->level,
                'active' => $site->active,
                'update' => $site->update,
            ];
        }

        return new Response\JSON($response, $code);
    }
}
