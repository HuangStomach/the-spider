<?php
use Phalcon\Mvc\Micro;

class Server {
    protected $server;
    protected $app;

    public function __construct () {
        $this->server = new swoole_http_server('0.0.0.0', 9501);
        $this->server->set([
            'worker_num' => 4, // worker process num
            'backlog' => 128, // listen backlog
            // 'max_request' => 50,
            'dispatch_mode' => 1
        ]);
        $this->server->on('Request', [$this, 'request']);
        $this->server->on('WorkerStart', [$this, 'worker']);
    }

    public function request ($req, $res) {
        echo "request \n";
        $this->app->get('/wa', function () use ($res) {
            echo "isIn! \n";
            $res->end('wow');
        });
        $this->app->handle($req->server['request_uri']);
    }

    public function worker ($server, $id) {
        echo "workerstart \n";
        $this->app = new Micro();
    }

    public function run () {
        $this->server->start();
    }
}

$server = new Server();
$server->run();
