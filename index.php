<?php

class Server 
{
    protected $server;
    protected $websocket;
    
    public function __construct($host, $port) {
        $this->server = new swoole_websocket_server($host, $port);
        $this->server->set([
            'daemonize' => 0,
            'worker_num' => 8, // worker process num
            'task_worker_num' => 8, // 
            'backlog' => 128, // listen backlog
            'max_request' => 50,
            'dispatch_mode' => 1,
            'user' => 'www-data', 
            'group' => 'www-data'
        ]);
        $this->server->on('request', [$this, 'request']);
        $this->server->on('workerStart', [$this, 'work']);
        $this->server->on('task', [$this, 'task']);
        $this->server->on('finish', [$this, 'finish']);

        // websocket 托管内容
        $this->server->on('open', [$this, 'open']);
        $this->server->on('message', [$this, 'message']);
        $this->server->on('close', [$this, 'close']);

        $table = new swoole_table(1024);
        $table->column('fd', swoole_table::TYPE_INT);
        $table->create();

        $this->server->table = $table;
    }

    /**
     * 客户端连接
     *
     * @param [swoole_server] $server
     * @param [swoole_http_request] $req
     * @return void
     */
    public function open($server, $req) {
        $server->table->set($req->fd, ['fd' => $req->fd]);//获取客户端id插入table
        echo "server: handshake success with fd{$request->fd}\n";
    }
    
    /**
     * 将请求进行分发 符合Gini框架要求
     *
     * @param [swoole_http_request] $req http请求对象
     * @param [swoole_http_response] $res http应答对象
     * @return void
     */
    public function request($req, $res) {
        $header = $req->header; // TODO: 可以考虑在header中约定参数 让请求直接end 后续处理逻辑让客户端无需等待
        $_SERVER = array_merge($_SERVER, $req->server);
        
        // TODO: 传入res对象使后续可以对res对象进行操作
        $uri = trim($_SERVER['request_uri'], '/');
        $content = \Gini\CGI::request($uri, [
            'header' => $header,
            'get' => $req->get,
            'post' => $req->post,
            'files' => [], // 暂且先不考虑file
            'route' => $uri,
            'method' => $_SERVER['request_method'],
            'swoole' => $this->server, // swoole_server 对象
            'raw' => $req->rawContent(),
        ])
        ->execute()
        ->content();
        
        $res->end(J($content));
    }

    /**
     * 接收来自websocket的消息
     *
     * @param [swoole_server] $server
     * @param [swoole_websocket_frame] $frame 客户端发来的数据帧信息
     * @return void
     */
    public function message($server, $frame) {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        $server->push($frame->fd, "this is server");
    }

    public function work($server, $work) {
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
    public function task($server, $task, $from, $data) {
        /* $event = $data['trigger'];
        unset($data['trigger']);
        array_unshift($data, $server);
        $result = \Gini\Event::trigger($event, ...$data);
        $server->finish($result); */
        $server->finish(true);
    }

    /**
     * 用于在task进程中通知worker进程，投递的任务已完成。
     *
     * @param [swoole_server] $server
     * @param [int] $task
     * @param [mixed] $data
     * @return void
     */
    public function finish($server, $task, $data) {
        // $logger = new \Logger('task');
        // $logger->notice("task[{$task}] is finished");
    }

    /**
     * 当客户端关闭时的回调函数
     *
     * @param [swoole_server] $server
     * @param [int] $fd 连接的文件描述符
     * @return void
     */
    public function close($server, $fd) {
        $server->table->del($fd);
        echo "client {$fd} closed\n";
    }

    public function run() {
        $this->server->start();
    }
}

$params = getopt('', [
    'host:',
    'port:'
]);
$host = array_key_exists('host', $params) ? $params['host'] : '0.0.0.0';
$port = array_key_exists('port', $params) ? $params['port'] : '3000';
$server = new Server($host, $port);
$server->run();
