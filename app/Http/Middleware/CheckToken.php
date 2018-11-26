<?php
/**
 * 验证用户身份的操作
 * Created by PhpStorm.
 * User: 99490
 * Date: 2018/5/22
 * Time: 18:06
 */

namespace App\Http\Middleware;

use App\Config\Config;

use Closure;

use App\Service\Common\CheckTokenService;

class CheckToken
{

    private $pathRoleType = [

    ]; //须校验token的模块

    public function handle($request, Closure $next)
    {
        //需执行的操作
        $path = str_replace("api/", "", $request->path());
        $action = $request->get("action");
        $routeArr = Config::not_verify_token_url[$path];
        if($action){
            if (in_array($action, $routeArr)) {
                return $next($request);
            }
            $token = $request->header("token");
            $tokenResult = CheckTokenService::checkToken($token);
            if (is_array($tokenResult)) {

                $request->user_id = $tokenResult["user_id"];
                return $next($request);

            } else {
                return $tokenResult;
            }
        }else{
            return response()->json(["code" => Config::httpCode['no_existent_code'], "msg" => __("Common.resources do not exist")]);
        }


    }

}