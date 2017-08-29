<?php
use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class Record extends Model {
    public $id;
    public $fqdn;
    public $name;
    public $state;
    public $output;
    public $type;
    public $time;

    public function initialize () {
        $this->belongsTo(
            'fqdn',
            'Server',
            'fqdn'
        );
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

    // 根据传递进来的记录获取server
    public function setServer ($form) {
        $server = Server::findFirst([
            "fqdn = '{$this->fqdn}'"
        ]);

        if ($server->id) {
            $server->ip = $form['ip'];
            
        } else {
            $server = new Server();
            $server->fqdn = $this->fqdn;
            $server->ip = $form['ip'];
        }

        return $server->save();
    }
    
    // 获取具体的Server状态的级别
    public function level () {
        $level = Server::STATUS_NORMAL;
        switch ($this->state) {
            case '500':
            case '502':
                $level = Server::STATUS_DANGER;
                break;
            default:
                break;
        }
        return $level;
    }

}
