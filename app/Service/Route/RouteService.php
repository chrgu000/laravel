<?php

/**
 * 路由处理服务类
 * Created by PhpStorm.
 * User: 99490
 * Date: 2018/5/22
 * Time: 16:45
 */
namespace App\Service\Route;
use App\Config\Config;

class RouteService
{

    /**
     * 检查路由完整性
     * @param $request
     */
    public static function checkRoute($request)
    {

        $action = $request->get("action", "");

        if (!empty($action)) {

            return true;

        } else {

            return response()->json(["code" => Config::httpCode["no_existent_code"], "msg" => __("Common.resources do not exist")]);
        }
    }
}