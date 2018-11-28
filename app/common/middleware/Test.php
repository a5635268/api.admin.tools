<?php

namespace app\common\middleware;

class Test
{
    public function handle($request, \Closure $next)
    {
        return $next($request);
    }
}