<?php

class Server 
{
    protected $server;
    protected $websocket;
    
    public function __construct($host, $tcpPort, $udpPort = 0) {
        $server = new swoole_websocket_server($host, $tcpPort);
        $server->set([
            'daemonize' => 0,
            'worker_num' => 8, // worker process num
            'task_worker_num' => 8, // 
            'backlog' => 128, // listen backlog
            'max_request' => 50,
            'dispatch_mode' => 1,
            'user' => 'www-data', 
            'group' => 'www-data'
        ]);
        $server->on('request', [$this, 'request']);
        $server->on('workerStart', [$this, 'work']);
        $server->on('task', [$this, 'task']);
        $server->on('finish', [$this, 'finish']);

        // websocket 托管内容
        $server->on('open', [$this, 'open']);
        $server->on('message', [$this, 'message']);
        $server->on('close', [$this, 'close']);

        // 如果传递了UDP端口 则开启UDP-Server
        if ($udpPort) {
            $server->addlistener($host, $udpPort, SWOOLE_SOCK_UDP);
            $server->on('packet', [$this, 'packet']);
        }

        $table = new swoole_table(1024);
        $table->column('fd', swoole_table::TYPE_INT);
        $table->create();
        $server->table = $table;

        $this->server = $server;
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
        $header = $req->header;
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
        ])->execute();
        $result->output();

        $res->status(http_response_code());
        $res->end(J($result->content()));
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
    
    /**
     * 将请求进行分发 符合Gini框架要求
     *
     * @param [swoole_server] $server
     * @param [string] $data 收到的数据内容，可能是文本或者二进制内容
     * @param [array] $client客户端信息包括address/port/server_socket 3项数据
     * @return void
     */
    public function packet($server, $data, $client) {
        // 该处目前只做接受nagios请求的处理
        $data; // TODO: data得处理一下 订一个协议
        
        // TODO: content是否考虑进行后续操作? 没有保存成功记录日志? 但是controller里面其实有
        $uri = trim($_SERVER['request_uri'], '/');
        $content = \Gini\CGI::request($uri, [
            'post' => $data['post'],
            'route' => $uri,
            'method' => 'POST',
            'swoole' => $server, // swoole_server对象
        ])->execute();
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
        $event = $data['trigger'];
        unset($data['trigger']);
        array_unshift($data, $server);
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
    'tcp-port:'
]);
$host = array_key_exists('host', $params) ? $params['host'] : '127.0.0.1';
$tcpPort = array_key_exists('tcp-port', $params) ? $params['tcp-port'] : 3000;
$udpPort = array_key_exists('udp-port', $params) ? $params['udp-port'] : 0;
$server = new Server($host, $tcpPort, $udpPort);
$server->run();
