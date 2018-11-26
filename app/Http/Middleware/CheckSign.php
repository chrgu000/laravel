<?php
/**
 * 验证参数签名的操作
 * Created by PhpStorm.
 * User: 99490
 * Date: 2018/5/22
 * Time: 18:06
 */

namespace App\Http\Middleware;

use App\Config\Config;
use Closure;
use App\Config\WechatUserConfig;

class CheckSign
{

    private $returnMsg = [];

    public function handle($request, Closure $next)
    {
        return $next($request);

        //检查头信息是否为json提交
        if ($request->method() == 'POST') {
            $checkHeaderResult = $this->checkHeader($request);
            if ($checkHeaderResult !== true) {

                return $checkHeaderResult;
            }
        }

        //判断是否为需要过滤的url
        $path = str_replace("api/", "", $request->path());
        if($path=='common'){
            return $next($request);
        }
        $routeArr = isset(Config::not_verify_sign_url[$path])?Config::not_verify_sign_url[$path]:[];
        //不需要验证签名直接请求
        if (in_array($request->get("action"), $routeArr)) {
            return $next($request);
        }

        $appid = $request->header('appId');
        $sign = $request->header('sign');

        if (!empty($appid) && !empty($sign)) {

            $param = $request->post();
            $result = $this->checkSign($appid, $sign, $param);

            if ($result == true) {
                return $next($request);
            } else {
                $this->returnMsg['code'] = Config::httpCode["sign_error_code"];
                $this->returnMsg['msg'] = __("WechatUser.signature error");
                return response()->json($this->returnMsg);
            }

        } else {

            $this->returnMsg['code'] = Config::httpCode["sign_error_code"];
            $this->returnMsg['msg'] = __("WechatUser.signature error");
            return response()->json($this->returnMsg);
        }

    }

    /**
     * 验证接口参数签名
     * @param $appId
     * @param $sign
     */
    private function checkSign($appId, $clientSign, $param)
    {
        if (count($param) <= 0) {
            return true;
        }

        if ($appId == Config::sign["app_id"]) {

            //获取签名秘钥
            $serverSecret = Config::sign["app_secret"];
            if (!empty($serverSecret)) {
                //生成服务端sign
                $serverSign = $this->getServerSign($serverSecret, $param);
                if ($serverSign == $clientSign) {
                    return true;
                } else {
                    return false;
                }

            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 生成服务器端sign签名信息
     * @param $serverSecret
     * @param $param
     */
    private function getServerSign($serverSecret, $param)
    {
        ksort($param);//将数组key按照升序排序，以免拼接出来的字符串和前端不一致
        $serverstr = "";//生成的签名字符串
        //开始拼接字符串
        foreach ($param as $k => $v) {
            if(is_array($v)){
                $serverstr .= $k . implode(",",$v);
            }else{
                if(is_null($v)){
                    $v="";
                }
                $serverstr .= $k . (string)($v);
            }

        }

        //拼接秘钥
        $reserverstr = $serverstr . $serverSecret;
        $reserverSign = strtoupper(sha1($reserverstr));//将字符串sha1加密再全部转为大写
        return $reserverSign;
    }


    /**
     * 验证请求类型
     * @param $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
    private function checkHeader($request)
    {
        if (!empty($request->header("content-type")) && $request->header("content-type") == 'application/json') {

            return true;
        } else {
            $this->returnMsg['code'] = Config::httpCode["unsupported_media_type_code"];
            $this->returnMsg['msg'] = __("WechatUser.unsupported request type");
            return response()->json($this->returnMsg);
        }
    }
}