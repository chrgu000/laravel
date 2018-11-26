<?php
namespace App\Validate\Common;

use App\Validate\BaseValidate;

class CommonValidate extends BaseValidate
{
    public static function  test($param){

        $roule = [
            'test'=>'bail|required',
        ];
        return (new self)->instance($param, $roule);
    }
    public static function getOpenId($param){
        $roule = [
            'jscode'=>'bail|required',
        ];
        return (new self)->instance($param, $roule);
    }
    public static function login($param){
        $roule = [
            'smallopenid'=>'bail|required',
            'header'=>'bail|required',
            'nickName'=>'bail|required',
            'sex'=>'bail|required',
        ];
        return (new self)->instance($param, $roule);
    }
    public static function checkToken($param){
        $roule = [
            'smallopenid'=>'bail|required',
        ];
        return (new self)->instance($param, $roule);
    }
    public static function insertApiInfo($param){
        $roule = [
            'api_name'=>'bail|required',
            'file_path'=>'bail|required',
            'api_info'=>'bail|required',
            'openid'=>'bail|required'
        ];
        return (new self)->instance($param, $roule);
    }
    public static function history($param){
        $roule = [
            'openid'=>'bail|required'
        ];
        return (new self)->instance($param, $roule);
    }
    public static function feedback($param){
        $roule = [
            'phone'=>'required|digits:11',
            'content'=>'bail|required'
        ];
        return (new self)->instance($param, $roule);
    }
}