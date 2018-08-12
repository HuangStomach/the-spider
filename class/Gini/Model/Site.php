<?php

namespace Gini\Model;

class Site {
    // 确保site存在
    static function ensure($form) {
        $site = a('site')->whose('fqdn')->is($form['hostname']);
        if ($site->id) return $site;

        $site->fqdn = $form['hostname'];
        $site->address = $form['hostaddress'];
        $site->name = $form['hostname'];
        $site->save();
        return $site;
    }
}