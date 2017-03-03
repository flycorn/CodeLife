<?php
/**
 * SocketChat类
 *
 * 基于php cli应用
 * Author: flycorn
 * Email: ym1992it@163.com
 * Date: 2017/2/12
 * Time: 17:06
 */
namespace Flycorn;

class SocketChat
{
    private $host; //host地址
    private $port; //监听端口
    private $timeout = 60; //超时时间
    private $handShake = false; //默认未牵手
    private $serverSocket; //服务端socket进程
    private $socketPool = []; //socket连接池
    private $maxSocketConnect = 2; //最大socket连接数
    private $chatUsers = []; //参与聊天的用户

    public function __construct($host, $port = 0)
    {
        $this->host = $host;
        !empty( $port ) && $this->port = $port;
        $this->startServer();
    }

    /**
     * 开启Socket服务
     */
    public function startServer()
    {
        $this->serverSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if(!$this->serverSocket) throw new \Exception("listen {$this->port} fail !");

        //允许使用本地地址
        socket_set_option($this->serverSocket, SOL_SOCKET, SO_REUSEADDR, TRUE);
        socket_bind($this->serverSocket, $this->host, $this->port);
        socket_listen($this->serverSocket, $this->maxSocketConnect);

        $this->log("Server Started : ".date('Y-m-d H:i:s'));
        $this->log("Listening on   : 127.0.0.1 port ".$this->port);
        $this->log("Server socket  : " . $this->serverSocket.PHP_EOL);

        $this->socketPool[] = $this->serverSocket;

        //socket多路选择
        while(true){

            $reads = $this->socketPool;

            @socket_select($reads, $writes, $except = null, $this->timeout);

            foreach ($reads as $socket){

                //判断当前socket是否服务端
                if($this->serverSocket == $socket){

                    //接收一个客户端Socket连接
                    $clientSocket = socket_accept( $socket );
                    $this->handShake = false;

                    if(!$clientSocket){
                        //连接失败
                        $this->log('client connect false!');
                        $message = '{"type" : 4, "msg": "连接失败!"}';
                        $this->parseMessage($socket, $message);
                        continue;
                    }

                    //验证是否超过最大连接数
                    if(count($this->socketPool) > $this->maxSocketConnect){
                        $this->log('client connect max nums!');
                        socket_close( $clientSocket ); //断开连接
                        continue;
                    }

                    //加入Socket池
                    $this->connect($clientSocket);

                } else {
                    //客户端Socket进程

                    //接收数据
                    $bytes = @socket_recv($socket, $buffer, 2048, 0);

                    //未读取到数据
                    if($bytes == 0){
                        //断开连接
                        $this->disConnect($socket);
                    } else {
                        //未握手 先握手
                        if( !$this->handShake ) {
                            $this->doHandShake($socket, $buffer);
                        } else {
                            //如果是已经握完手的数据，广播其发送的消息
                            $buffer = $this->decode( $buffer );
                            $this->parseMessage($socket, $buffer);
                        }
                    }
                }

            }

        }

    }

    //客户端socket连接
    public function connect($socket)
    {
        if(!is_int(array_search( $socket, $this->socketPool ))){
            array_push($this->socketPool, $socket);
            $this->log('nums: '.count($this->socketPool));
            $this->log($socket.' connented!');
            $this->log(date('Y-m-d H:i:s'));
        }
    }

    //客户端socket断开连接
    public function disConnect($socket)
    {
        $index = array_search( $socket, $this->socketPool);
        if(is_int($index) && $index >= 0){
            socket_close( $socket );
            array_splice( $this->socketPool, $index, 1 );
        }
    }

    //客户端握手协议
    private function doHandShake($socket, $buffer)
    {
        list($resource, $host, $origin, $key) = $this->getHeaders($buffer);
        $upgrade  = "HTTP/1.1 101 Switching Protocol\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "Sec-WebSocket-Accept: " . $this->calcKey($key) . "\r\n\r\n";  //必须以两个回车结尾

        $this->handShake = true;
        return socket_write($socket, $upgrade, strlen($upgrade));
    }

    //获取请求头
    private function getHeaders( $req )
    {
        $r = $h = $o = $key = null;
        if (preg_match("/GET (.*) HTTP/"              , $req, $match)) { $r = $match[1]; }
        if (preg_match("/Host: (.*)\r\n/"             , $req, $match)) { $h = $match[1]; }
        if (preg_match("/Origin: (.*)\r\n/"           , $req, $match)) { $o = $match[1]; }
        if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $req, $match)) { $key = $match[1]; }
        return [$r, $h, $o, $key];
    }

    //验证socket
    private function calcKey( $key )
    {
        //基于websocket version 13
        $accept = base64_encode(sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
        return $accept;
    }

    //打包函数 返回帧处理
    private function frame( $buffer )
    {
        $len = strlen($buffer);
        if ($len <= 125) {

            return "\x81" . chr($len) . $buffer;
        } else if ($len <= 65535) {

            return "\x81" . chr(126) . pack("n", $len) . $buffer;
        } else {

            return "\x81" . char(127) . pack("xxxxN", $len) . $buffer;
        }
    }

    //解码 解析数据帧
    private function decode( $buffer )
    {
        $len = $masks = $data = $decoded = null;
        $len = ord($buffer[1]) & 127;

        if ($len === 126) {
            $masks = substr($buffer, 4, 4);
            $data = substr($buffer, 8);
        }
        else if ($len === 127) {
            $masks = substr($buffer, 10, 4);
            $data = substr($buffer, 14);
        }
        else {
            $masks = substr($buffer, 2, 4);
            $data = substr($buffer, 6);
        }
        for ($index = 0; $index < strlen($data); $index++) {
            $decoded .= $data[$index] ^ $masks[$index % 4];
        }
        return $decoded;
    }

    /**
     * 解析数据
     * @param $socket
     * @param $message
     */
    public function parseMessage($socket, $message)
    {
        //msg type 0 初始化 1 通知 2 普通消息 3 断开连接 4 错误 5 刷新页面处理
        $message = json_decode( $message, true );

        if(is_numeric($message['type'])){
            switch ($message['type']){
                case 0:
                    $this->bind($socket, $message);
                    //通知其他客户端,当前用户上线
                    $msgOnline = [
                        'type' => 1,
                        'msg' => '上线了...',
                    ];
                    $this->sendToAll( $socket,  $msgOnline );
                    break;
                case 1:
                    $this->sendToAll( $socket,  $message );
                    break;
                case 2:
                    $this->sendToAll( $socket,  $message );
                    break;
                case 3:
                    //通知用户离线
                    $msgOutline = [
                        'type' => 1,
                        'user' => $this->chatUsers[(int)$socket]['user'],
                        'msg' => '下线了...',
                    ];
                    $this->sendToAll( $socket,  $msgOutline );
                    //断开 要离线的用户
                    $this->disConnect( $socket );
                    break;
                case 4:
                    //错误信息
                    $this->send( $socket, $message );
                    break;
                case 5:
                    $this->bind($socket, $message);
                    break;
                default:
                    break;
            }
        }
    }

    //用户--连接 绑定
    public function bind($socket, $user)
    {
        $this->chatUsers[(int) $socket] = [
            'uid' => (int) $socket,
            'uname' => $user['uname'],
            'avatar' => $user['avatar'],
        ];
    }

    //用户--连接 解绑
    public function unBind($socket)
    {
        unset($this->chatUsers[(int) $socket]);
    }

    //发送给所有客户端(除自己和服务端外)
    private function sendToAll($client, $msg)
    {
        //组装信息
        $msg['user'] = $this->chatUsers[(int) $client];
        $msg['stime'] = date('Y-m-d H:i:s');

        foreach($this->socketPool as $socket ){
            if( $socket != $this->serverSocket && $socket != $client  ){
                $this->send( $socket, $msg );
            }
        }
    }

    /**
     * 发送
     * @param $client
     * @param $msg
     */
    private function send($client, $msg)
    {
        $msg = $this->frame( json_encode( $msg ) );
        socket_write( $client, $msg, strlen($msg) );
    }

    /**
     * cli 日志
     * @param string $msg
     */
    private function log( $msg = '')
    {
        echo $msg.PHP_EOL;
    }
    
}