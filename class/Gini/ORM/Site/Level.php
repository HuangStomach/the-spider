<?php

namespace Gini\ORM\Site;

class Level extends \Gini\ORM\Object
{
    public $site = 'object:site';
    public $disk = 'int';
    public $host = 'int';
    public $http = 'int';
    public $load = 'int';
    public $ntp = 'int';
    public $update = 'datetime';

    protected static $db_index = [
        'unique:site'
    ];
}