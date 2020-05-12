<?php
/**
 * Created by PhpStorm.
 * User: CTDJ1803111
 * Date: 2019/7/29
 * Time: 11:38
 */

namespace libs;
use Curl\Curl;

class TxLbscloud
{
    protected   $ak = 'E2WBZ-OZ2WI-LHNGG-5TFPO-KPMUJ-GBFOI';   //ak
    protected   $version = 'v1';   //ak
    protected   $curl;
    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
    }

    /**
     * 地址转经纬度
     * @param string $address
     * @param string $region 城市
     * @return bool
     */
    public function geocoder(string $address,string $region = null){
        $resource = 'https://apis.map.qq.com/ws/geocoder/v1/';
        $data['address'] = $address;
        $data['region'] = $region;
        $data['key'] = $this->ak;
        $result = $this->curl->get($resource,$data);
        if($result){
            if($result->status == 0){
                return json_decode( json_encode( $result->result->location),true);
            }
            return false;
        }
        return false;
    }
}
