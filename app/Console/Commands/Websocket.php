<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\SystemUser;
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Lib\Timer;

class Websocket extends Command
{
    protected $signature = 'websocket {action}  {--daemonize=1}';
    protected $description = 'websocket服务';

    const ADMIN_TOKEN = 'demodemodemo';

    /**
     ** 执行控制台命令
     **/
    public function handle()
    {
        global $argv;

        $action=$this->argument('action');

        $argv[0]='workerman:wsserver';
        $argv[1]=$action;
        $argv[2]=$this->option('daemonize')?'-d':'';
        // 创建一个Worker监听2346端口，使用WebSocket协议通讯
        $ws_worker = new Worker("websocket://0.0.0.0:2346");
        $ws_worker->count = 1;

        // 定义一个空数组，用于存储连接的客户端信息，格式为[token => connection]
        $clients = [];

        // 当客户端连接时触发的回调函数
        $ws_worker->onConnect = function($connection) {
            // $connection->send('连接成功，请输入Token登录！');
        };

        // 当接收到客户端消息时触发的回调函数
        $ws_worker->onMessage = function($connection, $data) use (&$clients) {
            $this->log("收到：".$data);
            // 解析客户端发送过来的JSON数据
            $json = json_decode($data, true);
            $action = $json['action'];

            if ($data == 'ping') {
                $connection->send("pong");
            } else if ($action == 'login') {
                $token = $json['token'];
                if (empty($token)) {
                    $connection->send(json_encode(['action'=>'logout', 'msg'=>'token不能为空']));
                } else {
                    $user = SystemUser::where('token', $token)->where('status', 1)->where('is_del', 0)->first();
                    if (empty($user)) {
                        $this->log("token 过期" . $token);
                        $connection->send(json_encode(['action'=>'logout', 'msg'=>'token过期']));
                    } else {
                        // 将该连接与指定Token进行绑定
                        $clients[$token] = $connection;
                        // 向客户端发送登录成功的消息
                        $connection->send(json_encode(['action'=>'login_success']));
                    }
                }
            } else if ($action == 'list') {
                $connection->send(count($clients). ":" . json_encode(array_keys($clients)));
            } else if ($action == 'notify') {
                $admin_token = $json['admin_token'];
                $user_token = $json['user_token'];
                $message = $json['message'];
                // 检查管理员Token是否正确
                if ($admin_token !== static::ADMIN_TOKEN) {
                    $this->log("管理员TOKEN不正确") ;
                    $connection->send('管理员Token不正确！');
                    return;
                }
                // 检查要通知的用户是否在线
                if (!isset($clients[$user_token])) {
                    $this->log("用户不在线");
                    $connection->send('用户不在线！');
                    return;
                }
                // 向指定用户发送通知消息
                $clients[$user_token]->send($message);
                $connection->send('发送通知成功！');
            }
        };

        // 当客户端断开连接时触发的回调函数
        $ws_worker->onClose = function($connection) use (&$clients) {
            // 在$clients数组中查找并移除与该连接对应的Token
            foreach ($clients as $token => $conn) {
                if ($conn == $connection) {
                    $this->log("断开连接".$token);
                    unset($clients[$token]);
                    break;
                }
            }
        };

        // 运行Worker
        $ws_worker->runAll();// 定义一个空数组，用于存储连接的客户端信息，格式为[token => connection]
    }

    public function log($info) {
        file_put_contents("/www/wwwlogs/websocket.log", "[".date('Y-m-d H:i:s')."] ".$info . PHP_EOL, FILE_APPEND);
    }
}
