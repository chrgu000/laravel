<?php

namespace App\Http\Middleware;

use Closure;
use App\Service\Log\LogService;

class LogRecorder
{
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);   //记录日志
        //$request->setTrustedProxies(['192.168.31.1/16']);
       /* $startTime = LogService::MSec();
        $resp = $next($request);
        $uri = $request->path();
        $command = $request->get("action", "");
        $argus = $request->post();
        if ($uri == "api/Gm") {
            $argus['_areaId'] = $request->header('areaid');
            //$argus['roleType']= $request->header('roleType');
            $argus['rtype'] = "Gm";
        }
        
        LogService::ApiRecord(
            $uri, 
            LogService::MSec() - $startTime,
            $command,
            json_encode($argus),
            json_encode($resp),
            $request->getUri(),
            $request->getClientIp(),
            json_encode($request->header())
        );
        return $resp;*/
    }
}
