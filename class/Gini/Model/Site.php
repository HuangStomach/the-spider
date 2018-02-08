<?php

namespace Gini\Model;

class Site
{
    // 确保site存在
    function ensure ($fqdn, $address = null, $name = null) {
        $site = a('site')->whose('fqdn')->is($fqdn);
        if ($site->id) return $site;

        $site->fqdn = $fqdn;
        $site->address = $address;
        $site->name = $name;
        $site->save();
        return $site;
    }
}