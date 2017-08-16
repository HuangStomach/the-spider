<?php
use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class Record extends Model {
    public $id;
    public $name;
    public $state;
    public $output;
    public $type;
    public $time;

    public function initialize () {
        
    }

    public function validation () {
        $validator = new Validation();

        $validator->add('name', new PresenceOf([
            'message' => '缺少hostname',
        ]));

        $validator->add('state', new PresenceOf([
            'message' => '缺少httpstate',
        ]));

        return $this->validate($validator);
    }

    public function beforeCreate () {
        $this->time = date('Y-m-d h:i:s', time());
    }
    
}
