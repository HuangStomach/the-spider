<?php
use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class Record extends Model {
    public $id;
    public $name;
    public $fqdn;
    public $ip;
    public $status;
    public $free;
    public $top;
    public $report;
    public $update;

    const STATUS_NORMAL = 0;
    const STATUS_WARNING = 1;
    const STATUS_DANGER = 2;

    public function initialize () {
        
    }

    public function beforeUpdate () {
        $this->update = date('Y-m-d h:i:s', time());
    }
    
}
