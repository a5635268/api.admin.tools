<?php

namespace app\common\service;

use app\common\controller\Base;
use GrpcServer\ClientFactory;
use Grpc\UserScoreGrpcRequest as gRpcRequest;
use libs\Log;

class Gold extends Base
{
    /**
     * 根据token获取积分
     * @param $token
     * @param int $isAll
     * @return int
     * @throws \Exception
     */
    public function getByToken($token,$isAll = 0)
    {
        $api = config('java_service_api.gold_url') . '/v1/scores/json/getScoreByUid';
        $reObj = curl(1)->get($api,['token' => $token]);
        if(intval($reObj->code) !== 0){
            $this->thrError(1,$reObj->data);
        }
        if($isAll){
            return $reObj->data;
        }
        return intval($reObj->data->scores);
    }

    /**
     * 金币扣减
     * @param $uid
     * @param $score
     * @param null $comment
     * @return array
     * @throws \Exception
     */
    public function decrease($uid , $score , $comment = null)
    {
        if(empty($uid) || intval($score) < 1){
            $this->thrError(21);
        }
        if(is_null($comment)){
            $comment = '第三方赛事竞猜金币扣减';
        }
        $body = [
            'uid' => $uid,
            'type' => 2, // 1=收入 2=支出
            'change_scores' => $score,
            'order_no' => $this->createOrderNo($uid),
            'src_id' => 1, // 来源珑讯电竞app
            'comment' => $comment
        ];
        Log::info(__METHOD__ . __LINE__, '金币扣减', $body);
        return $this->send($body);
    }

    /**
     * 金币增加
     * @param $uid
     * @param $score
     * @param null $comment
     * @return array
     * @throws \Exception
     */
    public function increase($uid , $score , $comment = null)
    {
        if(empty($uid) || intval($score) < 1){
            $this->thrError(21);
        }
        if(is_null($comment)){
            $comment = '第三方赛事竞猜金币增加';
        }
        $body = [
            'uid' => $uid,
            'type' => 1, // 1=收入 2=支出
            'change_scores' => $score,
            'order_no' => $this->createOrderNo($uid,'02'),
            'src_id' => 1, // 来源珑讯电竞app
            'comment' => $comment
        ];
        Log::info(__METHOD__ . __LINE__, '金币增加', $body);
        return $this->send($body);
    }

    /**
     * 01,代表金币扣减单 02，代表金币增加单
     * @param $uid
     * @param string $businessNo
     * @return string
     */
    private function createOrderNo($uid, $businessNo = '01')
    {
        return $businessNo . str_pad($uid , 3 , '0' , STR_PAD_LEFT) . date('ymdHis' , time()) . rand(1000,9999);
    }

    private function send($data)
    {
        $grpcClient = ClientFactory::createClient('grpc.UserScore');
        $grpcRequest = new gRpcRequest();
        $grpcRequest->setUid($data['uid'])
            ->setOrderNo($data['order_no'])
            ->setType($data['type'])
            ->setChangeScores($data['change_scores'])
            ->setSrcId($data['src_id'])
            ->setComment($data['comment']) ;
        list($result, $status) = $grpcClient->updateScore($grpcRequest)->wait();
        if($status->code != 0){
            Log::err(__METHOD__,'UserScore service error',$status->dedetails,$data);
            $this->thrError(4001);
        }
        if(intval($result->getCode()) !== 0){
            $errmsg = $result->getMsg() . '-' . $result->getData();
            Log::warn('金币操作错误', $errmsg);
            $this->thrError(1,$errmsg);
        }
        return $this->returnSucc('gold update success');
    }
}