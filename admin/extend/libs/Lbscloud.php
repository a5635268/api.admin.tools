<?php
/**
 * Created by PhpStorm.
 * User: CTDJ1803111
 * Date: 2019/6/24
 * Time: 14:28
 */

namespace libs;

class Lbscloud
{
    /**
     * 百度地图定位
     */
    const AK = 'u5yDVTMaGK82vmTptyvbiCjp7GqqEedp';
    const VERSION = 'v3';

    public static function getLocation($address)
    {
        $resource = 'http://api.map.baidu.com/geocoding/' . self::VERSION . '/?address=' . $address . '&output=json&ak=' . self::AK;
        $result = Curl()->get($resource);
        if ($result) {
            $result = json_decode($result , true);
            if ($result['status'] == 0) {
                return $result['result']['location'];
            }
            return false;
        }
        return false;
    }
}
