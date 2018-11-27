<?php

namespace app\http\middleware;

class Check
{
    public function handle($request, \Closure $next, $name)
    {
        echo 2222;
        return $next($request);
    }
}
