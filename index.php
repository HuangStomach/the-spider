<?php

class Server {
    protected $server;
    protected $app;

    public function __construct () {
        $this->server = new swoole_http_server('0.0.0.0', 9501);
        $this->server->set([
            'task_worker_num' => 4, // worker process num
            'backlog' => 128, // listen backlog
            // 'max_request' => 50,
            'dispatch_mode' => 1
        ]);
        $this->server->on('Request', [$this, 'request']);
        $this->server->on('task', [$this, 'task']);
        $this->server->on('task', [$this, 'task']);
        $this->server->on('finish', [$this, 'finish']);
        
		define('APP_PATH', dirname(__FILE__) . '/' );
    }

    public function request ($req, $res) {
        $this->server->req = $req;
        $task = $this->server->task([$req, $res]);
    }

    public function task ($server, $task, $from, $data) {
        require_once(APP_PATH . 'lib/dispatcher.php');

        $dispatcher = new Dispatcher($data);
        $dispatcher->dispatch();
        // $server->finish("OK");
    }

    public function finish ($server, $task, $data) {
        // echo print_r($server, true);
        // echo print_r($data, true);
    }

    public function run () {
        $this->server->start();
    }
}

$server = new Server();
$server->run();
