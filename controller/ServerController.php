<?php

use Phalcon\Mvc\Controller;

class ServerController extends Controller {
    
    public function getAction () {
        $form = $this->request->getQuery();
        $conditions = [];
        $bind = [];

        if ($form['keyword']) {
            $conditions[] = " name LIKE '%:keyword:%' ";
            $conditions[] = " fqdn LIKE '%:keyword:%' ";
            $conditions[] = " ip LIKE '%:keyword:%' ";
            $bind['keyword'] = $form['keyword'];
        }

        if ($form['status']) {
            $conditions[] = " status = :status: ";
            $bind['status'] = $form['status'];
        }
        
        if ($form['sortby'] && $form['order']) {
            $order = (string)$form['sortby'] . ' ' . (string)$form['order'];
        }

        $servers = Server::find([
            'conditions' => implode('AND', $conditions),
            'bind' => $bind,
            'order' => $order,
            'limit' => $limit,
            'offset' => $offset
        ]);

        // TODO
        // do sth

    }

}