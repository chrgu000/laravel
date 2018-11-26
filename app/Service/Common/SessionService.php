<?php

/**
 * Created by VsCode.
 * User: albert
 * Date: 2018/6/6
 * Time: 13:49
 */

namespace App\Service\Common;

use Illuminate\Support\Facades\DB;

use App\Config\Config;

class SessionService
{
    const tables = "session";

    // 简单的随机算法保证的唯一值
    private static function getNewToken($roleId)
    {
        $randA = substr(md5((string)random_int(1000, 999999)), 0, random_int(1, 10));
        $randB = substr(md5(random_bytes(9)), 0, random_int(3, 8));
        $randC = substr(md5(random_bytes(16)), 0, random_int(5, 15));
        return strtolower(md5($randA . $roleId . $randB . time() . $randC));
    }

    /**
     * 设置指定用户的session
     */
    public static function SetSession($roleType, $roleId)
    {
        $table = SessionService::tables;
        $token = SessionService::getNewToken($roleId);
        $t =
            [
                'update_time' => date("Y-m-d H:i:s"),
                'roleId' => $roleId,
                'token' => $token,
            ];
        DB::table($table)
            ->updateorinsert(['roleId' => $roleId, "rtype" => $roleType], $t);
        return $token;
    }

    public static function RemoveSession($roleType, $roleId, $note)
    {
        $table = SessionService::tables;
        $t = [
            'note' => $note,
            'updateTime' => time(),
            'roleId' => $roleId,
        ];
        DB::table($table)
            ->where('roleId', '=', $roleId)
            ->where('rtype', '=', $roleType)
            ->update($t);
    }

    /**
     * 定时任务，清除过期的session
     */
    /*public static function scheduleTask()
    {
        // 当前不在线
        DB::table(SessionService::tables)
            ->where('isOnline', '=', "0")
            ->delete();

        // 长期无操作角色，强制下线
        $sql = sprintf("select * from %s where isOnline = 1 and UpdateTime + %d < %d", SessionService::tables
            , Config::MaxTokenExpireTime, time());
        $ret = DB::select($sql);

        foreach ($ret as $k => $v) {
            if ($v->rtype == "GM") {
                RoleController::RoleLogout($v->roleId, "sessionTimeout");
            }
        }
    }*/

}
