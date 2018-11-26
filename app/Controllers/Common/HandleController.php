<?php
namespace  App\Controllers\Common;
use App\Http\Controllers\Controller;
use App\Service\Common\CommonService;
class HandleController extends Controller{

    public function test()
    {
      return  CommonService::init('test');
    }
    public function common(){
        return  CommonService::init('common');
    }
    public function getOpenId(){
        return CommonService::init('getOpenId');
    }
    public function login(){
        return  CommonService::init('login');
    }
    public function checkToken(){
        return  CommonService::init('checkToken');
    }
    public function insertApiInfo(){
        return  CommonService::init('insertApiInfo');
    }
    public function history(){
        return CommonService::init('history');
    }
    public function getUserInfo(){
        return CommonService::init('getUserInfo');
    }
    public function feedback(){
        return CommonService::init('feedback');
    }
}