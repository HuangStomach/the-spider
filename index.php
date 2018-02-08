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
        $header = $req->header; // TODO: 可以考虑在header中约定参数 让请求直接end 后续处理逻辑让客户端无需等待
        $server = $req->server; // 代指php的$_SERVER TODO: 考虑合并入$_SERVER
        $get = $req->get;
        $post = $req->post;
        
        // TODO: 传入res对象使后续可以对res对象进行操作
        $content = \Gini\CGI::request($uri, [
            'header' => $header,
            'server' => $server,
            'get' => $get, 
            'post' => $post,
            'files' => [], // 暂且先不考虑file
            'route' => trim($server['request_uri'], '/'),
            'method' => $server['request_method'],
            'swoole' => $this->server, // swoole_server对象
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

    /**
     * 投递一个异步任务到task_worker池中。
     *
     * @param [swoole_server] $server 当前任务执行的server对象 主要用于进行finish操作
     * @param [int] $task 当前任务的id
     * @param [int] $from 启动该task的worker的id
     * @param [mixed] $data 投递进该任务的数据 不能为资源数据
     * @return void
     */
    public function task ($server, $task, $from, $data) {
        $event = $data['trigger'];
        unset($data['trigger']);
        $result = \Gini\Event::trigger($event, ...$data);
        $server->finish($result);
    }

    /**
     * 用于在task进程中通知worker进程，投递的任务已完成。
     *
     * @param [swoole_server] $server
     * @param [int] $task
     * @param [mixed] $data
     * @return void
     */
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
