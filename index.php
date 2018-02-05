<?php
class Server {
    protected $server;
    

    public function __construct ($host, $port) {
        $this->server = new swoole_http_server($host, $port);
        $this->server->set([
            'daemonize' => 0,
            'worker_num' => 8, // worker process num
            'task_worker_num' => 8, // 
            'backlog' => 128, // listen backlog
            'max_request' => 50,
            'dispatch_mode' => 1
        ]);
        $this->server->on('request', [$this, 'request']);
        $this->server->on('workerStart', [$this, 'workerStart']);
        $this->server->on('task', [$this, 'task']);
        $this->server->on('finish', [$this, 'finish']);
    }

    public function request ($req, $res) {
        // 按照swoole的参数传递来植入gini参数
        $uri = trim($req->server['request_uri'], '/');

        $content = \Gini\CGI::request($uri, [
            'get' => $req->get, 
            'post' => $req->post,
            'files' => [], // 暂且先不考虑file
            'route' => $uri,
            'method' => $req->server['request_method'],
            'server' => $this->server, // 这里应该是 swoole server 对象
        ])
        ->execute()
        ->content();
        
        $res->end(J($content));
    }

    public function workerStart ($server, $work) {
        // 我只是不想输出那个模板html
        ob_start();
        require "/usr/local/share/gini/lib/cgi.php";
        ob_end_clean();
    }

    public function task ($server, $task, $from, $data) {
        $server->spider->dispatch($data['class'], $data['action'], $data['content']);
        $server->finish('');
    }

    public function finish ($server, $task, $data) {
        // $logger = new \Logger('task');
        // $logger->notice("task[{$task}] is finished");
    }

    public function run () {
        $this->server->start();
    }
}

$params = getopt('', [
    'host:',
    'port:'
]);
$host = $params['host'] ? : '0.0.0.0';
$port = $params['port'] ? : '3000';
$server = new Server($host, $port);
$server->run();
