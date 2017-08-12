<?php

class Server {
    protected $server;
    protected $app;

    public function __construct () {
        $this->server = new swoole_http_server('0.0.0.0', 9501);
        $this->server->set([
            'worker_num' => 4, // worker process num
            'backlog' => 128, // listen backlog
            'max_request' => 50,
            'dispatch_mode' => 1
        ]);
        $this->server->on('request', [$this, 'request']);
        $this->server->on('workerStart', [$this, 'workerStart']);
        $this->server->on('finish', [$this, 'finish']);

		define('APP_PATH', dirname(__FILE__) . '/' );
    }

    public function request ($req, $res) {
        $spider = $this->server->spider;
        $spider->router->add('/:controller/:params', [
            'controller' => 1,
            'params' => 2,
            'action' => $req->server['request_method']
        ]);

        // 按照swoole的参数传递来植入phalcon参数
        $method = $req->server['request_method'];
        $_SERVER['REQUEST_METHOD'] = $method;
        switch ($method) {
            case 'GET':
                $_GET = $_REQUEST = $req->get;
                break;
            case 'POST':
            case 'PATCH':
            case 'DELETE':
                $_POST = $_REQUEST = $req->post;
                break;
            case 'PUT':
                $_PUT = $_REQUEST = $req->post;
                break;
        }

        $content = $spider->handle($req->server['request_uri'])->getContent();
        $res->end($content);
    }

    public function workerStart ($server, $work) {
        require_once(APP_PATH . 'lib/Spider.php');

        $server->spider = new Spider();
        $server->spider->init();
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
