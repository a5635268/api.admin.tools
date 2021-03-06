<?php

namespace app\ticket\service;

use app\common\facade\Http;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use libs\CacheKeyMap;
use libs\Log;
use traits\ResponsDataBuild;

class Ticket
{
    use ResponsDataBuild;

    public function __construct()
    {

    }

    /**
     * 获取秒杀列表  原价字段："original_price": 0.0
     * @param null $date
     * @return mixed
     * @throws \Exception
     */
    public function getSeckidllTicketList($date=null)
    {
        $date = is_null($date) ? date("Ymd") : $date;
        $apiPath = config('ticket.api') . '/ota/v1/ticket/search';
        $apiArgs = [
            'partner_id' => config('ticket.seckill_partner_id'),
            'scenic_sid' => config('ticket.sid'),
            'date' => $date,
        ];
        return $this->getCacheData($apiPath,$apiArgs);
    }

    /**
     * 获取秒杀票详情
     * @param $ticketId
     * @param null $date
     * @return mixed
     * @throws \Exception
     */
    public function getSeckidllTicket($ticketId,$date=null)
    {
        $date = is_null($date) ? date("Ymd") : $date;
        $apiPath = config('ticket.api') . '/ota/v1/ticket/' . $ticketId;
        $apiArgs = [
            'partner_id' => config('ticket.seckill_partner_id'),
            'date' => $date,
        ];
        return $this->getCacheData($apiPath,$apiArgs);
    }

    /**
     * 获取缓存活接口数据
     * @param $apiPath
     * @param $apiArgs
     * @return mixed
     * @throws \Exception
     */
    private function getCacheData($apiPath,$apiArgs)
    {
        $redis = app()->redis;
        $key = CacheKeyMap::InterfaceCacheKey($apiPath,$apiArgs);
        $data = $redis->get($key);
        if(!empty($data)){
            return json_decode($data,true);
        }
        try{
            $apiRes = Http::get($apiPath,$apiArgs);
            if($apiRes['code'] !== 'success'){
                $this->thrError(25,$apiRes['msg']);
            }
            $apiData = $apiRes['data'];
            $redis->set($key,json_encode($apiData),600);
            return $apiData;
        }catch (ConnectException|ClientException $ex){
            // 链接超时
            Log::error($ex->getCode(),$ex->getMessage(),$apiArgs);
            $this->thrError(4);
        }
    }
}
