<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace app\chat\socket;

use GatewayWorker\Lib\Gateway;
use Workerman\Worker;
use think\worker\Application;
use libs\Log;

/**
 * Worker 命令行服务类
 */
class Events
{
    /**
     * onWorkerStart 事件回调
     * 当businessWorker进程启动时触发。每个进程生命周期内都只会触发一次
     *
     * @access public
     * @param  \Workerman\Worker    $businessWorker
     * @return void
     */
    public static function onWorkerStart(Worker $businessWorker)
    {
        $app = new Application;
        $app->initialize();
    }

    /**
     * onConnect 事件回调
     * 当客户端连接上gateway进程时(TCP三次握手完毕时)触发
     *
     * @access public
     * @param  int       $clientId
     * @return void
     */
    public static function onConnect($clientId)
    {
        Gateway::sendToCurrentClient(
            json_encode(
                [
                    'type'      => 'init' ,
                    'client_id' => $clientId
                ]
            )
        );
    }

    /**
     * onWebSocketConnect 事件回调
     * 当客户端连接上gateway完成websocket握手时触发
     *
     * @param  integer  $clientId 断开连接的客户端client_id
     * @param  mixed    $data
     * @return void
     */
    public static function onWebSocketConnect($clientId, $data)
    {
        // 判断token合法性
        if (!isset($data['get']['token'])) {
           // Gateway::closeClient($clientId);
        }

        // 获得uid，判断uid对应的group_id(asso_id)
        $groupId = 3;
        $uid = 493;

        // join online group
        Gateway::joinGroup($clientId, $groupId);

        Gateway::bindUid($clientId, $uid);

        Gateway::sendToCurrentClient("成功连接");
        Gateway::sendToGroup($groupId, $uid . ' 已上线');
    }

    /**
     * onMessage 事件回调
     * 当客户端发来数据(Gateway进程收到数据)后触发
     *
     * @access public
     * @param  int       $clientId
     * @param  mixed     $data
     * @return void
     */
    public static function onMessage($clientId, $data)
    {
        // 业务验证
        $data = json_decode($data,true);
        $message = $data['message'];
        $uid = Gateway::getUidByClientId($clientId);
        $groupId = 3;
        $data['uid'] = $uid;

        // 推送出去
        Gateway::sendToGroup( $groupId, $data);
    }

    /**
     * onClose 事件回调 当用户断开连接时触发的方法
     *
     * @param  integer $clientId 断开连接的客户端client_id
     * @return void
     */
    public static function onClose($clientId)
    {
       // GateWay::sendToAll("client[$clientId] logout\n");
    }

    /**
     * onWorkerStop 事件回调
     * 当businessWorker进程退出时触发。每个进程生命周期内都只会触发一次。
     *
     * @param  \Workerman\Worker    $businessWorker
     * @return void
     */
    public static function onWorkerStop(Worker $businessWorker)
    {
        // echo "WorkerStop\n";
    }
}
