<?php
/**
 * 连接池抽象类
 */
use Swoole\Coroutine\Channel;

abstract class AbstractPool
{
    private $min;//最少连接数
    private $max;//最大连接数
    private $count;//当前连接数
    private $connections;//连接池组
    protected $spareTime;//用于空闲连接回收判断
    private $inited = false;

    protected abstract function createDb();

    public function __construct()
    {
        $this->min = 10;
        $this->max = 100;
        $this->spareTime = 10 * 3600;
        $this->connections = new Channel($this->max + 1);
    }

    protected function createObject()
    {
        $obj = null;
        $db = $this->createDb();
        if ($db) {
            $obj = [
                'last_used_time' => time() ,
                'db'             => $db ,
            ];
        }
        return $obj;
    }

    /**
     * 初始化最小数量连接池
     * @return $this|null
     */
    public function init()
    {
        if ($this->inited) {
            return null;
        }
        for ($i = 0;$i < $this->min;$i ++) {
            $obj = $this->createObject();
            $this->count ++;
            $this->connections->push($obj);
        }
        return $this;
    }

    public function getConnection($timeOut = 3)
    {
        $obj = null;
        if(!$this->connections->isEmpty()){
            return $this->connections->pop($timeOut);
        }
        // 大量并发请求过多，连接池connections为空
        if ($this->count < $this->max) {
            // 连接数没达到最大，新建连接返回
            $this->count ++;
            $obj = $this->createObject();
            return $obj;
        }

        // timeout为出队的最大的等待时间
        // 如果超过最大等待时间后会返回false，客户端要判断一下
        return $this->connections->pop($timeOut);
    }

    /*
     * 链接使用完进行回收
     */
    public function free($obj)
    {
        if ($obj) {
            $this->connections->push($obj);
        }
    }

    /**
     * 处理空闲连接
     */
    public function gcSpareObject()
    {
        //大约2分钟检测一次连接
        swoole_timer_tick(
            120000 , function (){
            $list = [];
            while (true) {
                if (!$this->connections->isEmpty()) {
                    // 等待的时间要快，免得链接被用掉
                    $obj = $this->connections->pop(0.001);
                    $last_used_time = $obj['last_used_time'];
                    //  超过$this->spareTime的认为是空闲连接，pop掉
                    if (time() - $last_used_time > $this->spareTime) {
                        $this->count --;
                    } else {
                        // 没超过就继续push回去
                        array_push($list , $obj);
                    }
                } else {
                    break;
                }
            }
            foreach ($list as $item) {
                $this->connections->push($item);
            }
            unset($list);
            // keepMin(); 处理完之后就要保证最低连接数
            $this->keepMin();
        }
        );
    }

    private function keepMin()
    {
        if ($this->count >= $this->min) {
            return $this->count;
        } else {
            $num = $this->min - $this->count;
        }
        for ($i = 0;$i < $num;$i ++) {
            $obj = $this->createObject();
            $this->count ++;
            $this->connections->push($obj);
        }
        return $this->count;
    }
}