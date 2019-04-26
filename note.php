<?php
// 这种写法研究一下
public function __call($name, $arguments) {
    ///var_dump(...$arguments);
    return $this->redis->$name(...$arguments);
}