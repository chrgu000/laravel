<?php
/**
 * 请求前的操作
 * Created by PhpStorm.
 * User: 99490
 * Date: 2018/5/22
 * Time: 18:06
 */

namespace App\Http\Middleware;

use Closure;

class RequestBefore
{

    private $returnMsg = [];

    public function handle($request, Closure $next)
    {
        // 执行动作

        return $next($request);
    }
}