<?php
/**
 * Created by PhpStorm.
 * User: 99490
 * Date: 2018/6/4
 * Time: 13:10
 */

namespace App\Service\Common;

use App\Config\Config;
use Illuminate\Support\Facades\DB;


class CheckTokenService
{

    /**
     * 验证token有效性
     * @param $token
     * @param $roleType
     */
    public static function checkToken($token)
    {

        if (!empty($token)) {

            $where = [
                ["token", "=", $token],
                ["rtype", "=", 'USER'],
            ];

            $info = DB::table("session")
                ->where($where)
                ->first();

            if ($info) {

                return ["user_id" => $info->roleId];

            } else {
                return response()->json(["code" => Config::httpCode["token_error_code"], "msg" => __("Common.token error")]);
            }

        } else {
            return response()->json(["code" => Config::httpCode["token_empty_code"], "msg" => __("Common.token empty")]);
        }
    }
}