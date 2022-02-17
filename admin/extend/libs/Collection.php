<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: zhangyajun <448901948@qq.com>
// +----------------------------------------------------------------------
namespace libs;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;

/**
 * 数据集管理类
 */
class Collection implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{
    /**
     * 数据集数据
     * @var array
     */
    protected $items = [];

    public function __construct($items = [])
    {
        $this->items = $this->convertToArray($items);
    }

    public static function make($items = [])
    {
        return new static($items);
    }

    /**
     * 是否为空
     * @access public
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function toArray(): array
    {
        return array_map(function ($value) {
            return $value instanceof self ? $value->toArray() : $value;
        }, $this->items);
    }

    public function all(): array
    {
        return $this->items;
    }

    /**
     * 合并数组
     *
     * @access public
     * @param mixed $items 数据
     * @return static
     */
    public function merge($items)
    {
        return new static(array_merge($this->items, $this->convertToArray($items)));
    }

    /**
     * 按指定键整理数据
     *
     * @access public
     * @param mixed  $items    数据
     * @param string $indexKey 键名
     * @return array
     */
    public function dictionary($items = null, string &$indexKey = null)
    {
        if ($items instanceof self) {
            $items = $items->all();
        }

        $items = is_null($items) ? $this->items : $items;

        if ($items && empty($indexKey)) {
            $indexKey = is_array($items[0]) ? 'id' : $items[0]->getPk();
        }

        if (isset($indexKey) && is_string($indexKey)) {
            return array_column($items, null, $indexKey);
        }

        return $items;
    }

    /**
     * 比较数组，返回差集
     *
     * @access public
     * @param mixed  $items    数据
     * @param string $indexKey 指定比较的键名
     * @return static
     */
    public function diff($items, string $indexKey = null)
    {
        if ($this->isEmpty() || is_scalar($this->items[0])) {
            return new static(array_diff($this->items, $this->convertToArray($items)));
        }

        $diff       = [];
        $dictionary = $this->dictionary($items, $indexKey);

        if (is_string($indexKey)) {
            foreach ($this->items as $item) {
                if (!isset($dictionary[$item[$indexKey]])) {
                    $diff[] = $item;
                }
            }
        }

        return new static($diff);
    }

    /**
     * 比较数组，返回交集
     *
     * @access public
     * @param mixed  $items    数据
     * @param string $indexKey 指定比较的键名
     * @return static
     */
    public function intersect($items, string $indexKey = null)
    {
        if ($this->isEmpty() || is_scalar($this->items[0])) {
            return new static(array_diff($this->items, $this->convertToArray($items)));
        }

        $intersect  = [];
        $dictionary = $this->dictionary($items, $indexKey);

        if (is_string($indexKey)) {
            foreach ($this->items as $item) {
                if (isset($dictionary[$item[$indexKey]])) {
                    $intersect[] = $item;
                }
            }
        }

        return new static($intersect);
    }

    /**
     * 交换数组中的键和值
     *
     * @access public
     * @return static
     */
    public function flip()
    {
        return new static(array_flip($this->items));
    }

    /**
     * 返回数组中所有的键名
     *
     * @access public
     * @return static
     */
    public function keys()
    {
        return new static(array_keys($this->items));
    }

    /**
     * 返回数组中所有的值组成的新 Collection 实例
     * @access public
     * @return static
     */
    public function values()
    {
        return new static(array_values($this->items));
    }

    /**
     * 删除数组的最后一个元素（出栈）
     *
     * @access public
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->items);
    }

    /**
     * 通过使用用户自定义函数，以字符串返回数组
     *
     * @access public
     * @param callable $callback 调用方法
     * @param mixed    $initial
     * @return mixed
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * 以相反的顺序返回数组。
     *
     * @access public
     * @return static
     */
    public function reverse()
    {
        return new static(array_reverse($this->items));
    }

    /**
     * 删除数组中首个元素，并返回被删除元素的值
     *
     * @access public
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->items);
    }

    /**
     * 在数组结尾插入一个元素
     * @access public
     * @param mixed  $value 元素
     * @param string $key   KEY
     * @return $this
     */
    public function push($value, string $key = null)
    {
        if (is_null($key)) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }

        return $this;
    }

    /**
     * 把一个数组分割为新的数组块.
     *
     * @access public
     * @param int  $size 块大小
     * @param bool $preserveKeys
     * @return static
     */
    public function chunk(int $size, bool $preserveKeys = false)
    {
        $chunks = [];

        foreach (array_chunk($this->items, $size, $preserveKeys) as $chunk) {
            $chunks[] = new static($chunk);
        }

        return new static($chunks);
    }

    /**
     * 在数组开头插入一个元素
     * @access public
     * @param mixed  $value 元素
     * @param string $key   KEY
     * @return $this
     */
    public function unshift($value, string $key = null)
    {
        if (is_null($key)) {
            array_unshift($this->items, $value);
        } else {
            $this->items = [$key => $value] + $this->items;
        }

        return $this;
    }

    /**
     * 给每个元素执行个回调
     * @access public
     * @param callable $callback 回调
     * @return $this
     */
    public function each(callable $callback)
    {
        foreach ($this->items as $key => $item) {
            $result = $callback($item, $key);
            // 其中一个回调返回false就结束操作
            if (false === $result) {
                break;
            } elseif (!is_object($item)) {
                // 只对非对象的进行赋值
                $this->items[$key] = $result;
            }
        }

        return $this;
    }

    /**
     * 用回调函数处理数组中的元素
     * @access public
     * @param callable|null $callback 回调
     * @return static
     */
    public function map(callable $callback)
    {
        return new static(array_map($callback, $this->items));
    }

    /**
     * 用回调函数过滤数组中的元素，返回true的才筛选出来
     * @access public
     * @param callable|null $callback 回调
     * @return static
     */
    public function filter(callable $callback = null)
    {
        if ($callback) {
            return new static(array_filter($this->items, $callback));
        }

        return new static(array_filter($this->items));
    }

    /**
     * 根据字段条件过滤数组中的元素
     * @access public
     * @param string $field    字段名
     * @param mixed  $operator 操作符
     * @param mixed  $value    数据
     * @return static
     */
    public function where(string $field, $operator, $value = null)
    {
        if (is_null($value)) {
            $value    = $operator;
            $operator = '=';
        }

        return $this->filter(function ($data) use ($field, $operator, $value) {
            if (strpos($field, '.')) {
                [$field, $relation] = explode('.', $field);
                $result = $data[$field][$relation] ?? null;
            } else {
                $result = $data[$field] ?? null;
            }

            switch (strtolower($operator)) {
                case '===':
                    return $result === $value;
                case '!==':
                    return $result !== $value;
                case '!=':
                case '<>':
                    return $result != $value;
                case '>':
                    return $result > $value;
                case '>=':
                    return $result >= $value;
                case '<':
                    return $result < $value;
                case '<=':
                    return $result <= $value;
                case 'like':
                    return is_string($result) && false !== strpos($result, $value);
                case 'not like':
                    return is_string($result) && false === strpos($result, $value);
                case 'in':
                    return is_scalar($result) && in_array($result, $value, true);
                case 'not in':
                    return is_scalar($result) && !in_array($result, $value, true);
                case 'between':
                    [$min, $max] = is_string($value) ? explode(',', $value) : $value;
                    return is_scalar($result) && $result >= $min && $result <= $max;
                case 'not between':
                    [$min, $max] = is_string($value) ? explode(',', $value) : $value;
                    return is_scalar($result) && $result > $max || $result < $min;
                case '==':
                case '=':
                default:
                    return $result == $value;
            }
        });
    }

    /**
     * LIKE过滤
     * @access public
     * @param string $field 字段名
     * @param string $value 数据
     * @return static
     */
    public function whereLike(string $field, string $value)
    {
        return $this->where($field, 'like', $value);
    }

    /**
     * NOT LIKE过滤
     * @access public
     * @param string $field 字段名
     * @param string $value 数据
     * @return static
     */
    public function whereNotLike(string $field, string $value)
    {
        return $this->where($field, 'not like', $value);
    }

    /**
     * IN过滤
     * @access public
     * @param string $field 字段名
     * @param array  $value 数据
     * @return static
     */
    public function whereIn(string $field, array $value)
    {
        return $this->where($field, 'in', $value);
    }

    /**
     * NOT IN过滤
     * @access public
     * @param string $field 字段名
     * @param array  $value 数据
     * @return static
     */
    public function whereNotIn(string $field, array $value)
    {
        return $this->where($field, 'not in', $value);
    }

    /**
     * BETWEEN 过滤
     * @access public
     * @param string $field 字段名
     * @param mixed  $value 数据
     * @return static
     */
    public function whereBetween(string $field, $value)
    {
        return $this->where($field, 'between', $value);
    }

    /**
     * NOT BETWEEN 过滤
     * @access public
     * @param string $field 字段名
     * @param mixed  $value 数据
     * @return static
     */
    public function whereNotBetween(string $field, $value)
    {
        return $this->where($field, 'not between', $value);
    }

    /**
     * 返回数据中指定的一列
     * @access public
     * @param string|null $columnKey 键名
     * @param string|null $indexKey  作为索引值的列
     * @return array
     */
    public function column( ? string $columnKey, string $indexKey = null)
    {
        return array_column($this->items, $columnKey, $indexKey);
    }

    /**
     * 对数组排序
     * 默认升序。 降序改变一下大小判断号。
     * @access public
     * @param callable|null $callback 回调
     * @return static
     */
    public function sort(callable $callback = null)
    {
        $items = $this->items;

        $callback = $callback ?: function ($a, $b) {
            return $a == $b ? 0 : (($a < $b) ? -1 : 1);
        };

        uasort($items, $callback);

        return new static($items);
    }

    /**
     * 指定字段排序
     * @access public
     * @param string $field 排序字段
     * @param string $order 排序
     * @return $this
     */
    public function order(string $field, string $order = 'asc')
    {
        return $this->sort(function ($a, $b) use ($field, $order) {
            $fieldA = $a[$field] ?? null;
            $fieldB = $b[$field] ?? null;
            return 'desc' == strtolower($order) ? intval($fieldB > $fieldA) : intval($fieldA > $fieldB);
        });
    }

    /**
     * 将数组打乱
     *
     * @access public
     * @return static
     */
    public function shuffle()
    {
        $items = $this->items;

        shuffle($items);

        return new static($items);
    }


    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param array         $array
     * @param callable|null $callback
     * @param mixed         $default
     * @return mixed
     */
    public function first(callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            if (empty($this->items)) {
                return value($default);
            }

            foreach ($this->items as $item) {
                return $item;
            }
        }

        foreach ($this->items as $key => $value) {
            if (call_user_func($callback, $value, $key)) {
                return $value;
            }
        }

        return value($default);
    }

    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param array         $array
     * @param callable|null $callback
     * @param mixed         $default
     * @return mixed
     */
    public function last(callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($this->items) ? value($default) : end($this->items);
        }

        return self::first(array_reverse($this->items, true), $callback, $default);
    }


    /**
     * 截取数组
     *
     * @access public
     * @param int  $offset       起始位置
     * @param int  $length       截取长度
     * @param bool $preserveKeys preserveKeys
     * @return static
     */
    public function slice(int $offset, int $length = null, bool $preserveKeys = false)
    {
        return new static(array_slice($this->items, $offset, $length, $preserveKeys));
    }

    /**
     * 生成序号id
     * author: xiaogang.zhou@qq.com
     * datetime: 2021/11/4 17:29
     * @param $offset
     * @return $this
     */
    public function numGenerate($offset)
    {
        $id = $offset + 1;
        foreach ($this->items as $k => &$v){
            $v['_num'] =  $id ++;
        }
        return $this;
    }

    public function field($fields = '')
    {
        if (empty($fields)){
            return $this;
        }
        $items = $this->items;
        $arr = is_array($fields) ? $fields : explode(',', $fields);
        foreach ($items as $k=>&$v){
            foreach ($v as $kk=>$vv){
                if (!in_array($kk, $arr)){
                    unset($v[$kk]);
                }
            }
        }
        unset($v);
        return new static($items);
    }

    /**
     * 数组重置
     * author: xiaogang.zhou@qq.com
     * datetime: 2021/11/4 17:44
     * @param $newIndexSource
     * @param string $delimiter 多key为索引时，分隔符
     * @param false $isSon 是否重置到子数组里面
     * @return $this
     */
    public function resetArrayIndex($newIndexSource ,  bool $isSon = false,  string $delimiter = ':'): Collection
    {
        $resultArray = [];
        foreach ($this->items as $v) {
            // string格式的单key索引, 则直接赋值, 继续下一个
            if (is_string($newIndexSource)) {
                if ($isSon){
                    $resultArray[$v[$newIndexSource]][] = $v;
                }else{
                    $resultArray[$v[$newIndexSource]] = $v;
                }
                continue;
            }
            // 数组格式多key组合索引处理
            $k = '';
            foreach ($newIndexSource as $index) {
                $k .= "$v[$index]$delimiter";
            }
            $k = rtrim($k , $delimiter);
            if ($isSon){
                $resultArray[$k][] = $v;
            }else{
                $resultArray[$k] = $v;
            }
        }
        return new static($resultArray);
    }


    // ArrayAccess
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->items);
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    //Countable
    public function count()
    {
        return count($this->items);
    }

    //IteratorAggregate
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    //JsonSerializable
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * 转换当前数据集为JSON字符串
     * @access public
     * @param integer $options json参数
     * @return string
     */
    public function toJson(int $options = JSON_UNESCAPED_UNICODE) : string
    {
        return json_encode($this->toArray(), $options);
    }

    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * 转换成数组
     *
     * @access public
     * @param mixed $items 数据
     * @return array
     */
    protected function convertToArray($items): array
    {
        if ($items instanceof self) {
            return $items->all();
        }

        return (array) $items;
    }
}
