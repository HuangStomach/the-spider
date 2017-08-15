<?php

use Phalcon\Mvc\Model;
use DateTime;
use DateTimeZone;
use Phalcon\Mvc\Model\Behavior\Timestampable;
use Phalcon\Config\Adapter\Ini as ConfigIni;

class Record extends Model {
    public $id;
    public $name;
    public $state;
    public $output;
    public $type;
    public $time;

    public function initialize()
    {
        $config = new ConfigIni(CONFIG_PATH . 'application.ini');
        $this->addBehavior(new Timestampable([
            "beforeCreate" => [
                "field"  => "time",
                "format" => function () {
                    $datetime = new Datetime(
                        new DateTimeZone($config->env->timezone)
                    );
                    return $datetime->format("Y-m-d H:i:s");
                }
            ]
        ]));
    }
}
