<?php
/**
 * Created by PhpStorm.
 * User: wangxi
 * Date: 2018/9/27
 * Time: 下午4:42
 */

/*
 * todo
 * 当前聊天室人数
 * 历史记录
 * 直接关闭浏览器推出的播报
 * */

class swooleWebsocket{
    function __construct(){
        $server = new swoole_websocket_server("0.0.0.0", 9000);

        $server->on('open', function($server, $req) {
            echo "connection open: user id:{$req->fd}\n";
            $name = $req->get["name"] ?: "%";
            $this->push_to_everyone($server, $this->write_system_msg("{$name} come to chatroom", $req->fd));
        });

        $server->on('message', function($server, $frame) {
            $msg = $frame->data;
            $this->push_to_everyone($server, $msg);
        });

        $server->on('close', function($server, $fd) {
            echo "connection close: {$fd}\n";
        });
        $server->start();
    }

    function push_to_everyone($server, $msg){
        foreach ($server->connections as $user){
            $server->push($user, $msg);
        }
    }

    function write_system_msg($msg, $id=null){
        return json_encode([
            "msg" => $msg,
            "id" => $id,
            "name" => "server"
        ]);
    }

}


new swooleWebsocket();




