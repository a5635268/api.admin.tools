<?php
/**
 * Created by PhpStorm.
 * User: CTDJ1803111
 * Date: 2018/12/17
 * Time: 14:23
 */

namespace app\chat\controller;
use Workerman\Worker;

class Chat extends Start
{


    public function __construct()
    {
        Gateway::$registerAddress = '127.0.0.1:1236';
    }

    public function init()
    {
        // 获得uid，判断uid对应的group_id(asso_id)
        $groupId = 3;
        $uid = 493;
        $clientId = 2222;

        // join online group
        Gateway::joinGroup($clientId, $groupId);

        // bind clientId and memberId
        Gateway::bindUid($clientId,$uid);
    }

    public function add($data)
    {
        $token = $data['token'];
        $groupId = 3;
        $uid = 493;
        $clientId = 2222;
        $message = 'hahahaha';

        // 推送出去
        $data = [
            'uid' => $uid,
            'message' => $message
        ];
        Gateway::sendToGroup( $groupId, $data);
    }



}