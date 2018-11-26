<?php
namespace App\Service\Common;

use App\Validate\Common\CommonValidate;
use App\Config\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
class CommonService extends BaseService
{
    public $request=null;
    public function __construct()
    {
        parent::__construct();
        $this->request = request();
    }

    public static function init($action){
        return (new self)->$action();
    }
   public function getToken(){
        if(Cache::get('access_token')){
            return Cache::get('access_token');
        }else{
            $api_key=config::baidu['api_key'];
            $secret_Key=config::baidu['secret_key'];
            $url = "https://aip.baidubce.com/oauth/2.0/token?grant_type=client_credentials&client_id=$api_key&client_secret=$secret_Key";
            $result = getJson($url);//获取token
            $minutes=60*24*20;
            Cache::put('access_token', $result, $minutes);
            //session('access_token',$result);
            return $result;
        }
   }
    public function getFilePath($str){
        $reg='/uploads.*/';
        $path=str_replace("/","\\",$str);
        preg_match($reg,$path,$match);
        $img_path='';
        if(count($match)){
            $img_path=dirname(dirname(dirname(__DIR__))).'/public/'.$match[0];
        }
        $img_realpath=str_replace("\\","/",$img_path);
        return $img_realpath;
    }
    public function getApiUrl($type){
        switch ($type){
            case 'text';
             $apiUrl= Config::Api['text'];
             break;
            case 'food';
              $apiUrl= Config::Api['food'];
            break;
            case 'flower';
                $apiUrl= Config::Api['flower'];
                break;
            case 'animal';
                $apiUrl= Config::Api['animal'];
                break;
            case 'car';
                $apiUrl= Config::Api['car'];
                break;
            case 'face';
                $apiUrl= Config::Api['face'];
                break;
        }
        return $apiUrl;
    }
    private function common(){
        $param=$this->request->post();
        if(!isset($param['url'])||!isset($param['type'])){
            return false;
        }
        $result=$this->getToken();
        $str=$param['url'];
        $type=$param['type'];
        $img_realpath=$this->getFilePath($str);
        if(isset($result['access_token'])){
            $apiUrl=$this->getApiUrl($type).'?access_token='.$result['access_token'];

            if($type=='face'){
                $data=array(
                    "image"=>$this->imgToBase64($img_realpath),
                    "image_type"=>"BASE64",
                    "face_field"=>"age,beauty,expression,face_shape,gender,glasses,landmark,race,quality,face_type"
                );
                $data=json_encode($data);
            }else{
                $data=array("image"=>$this->imgToBase64($img_realpath));
            }
            $header=["application/x-www-form-urlencoded"];
            $response=httpPostRequest($apiUrl,$data,$header);
            $result=json_decode($response,true);
            Log::info($result);
            if(count($result)){
                $this->returnMsg["msg"] = __("Common.success");
                $this->returnMsg["data"] = $result;
                $this->returnMsg['code'] = Config::httpCode["http_success_code"];
            }else{
                $this->returnMsg["msg"] = __("Common.error");
                $this->returnMsg["data"] =[];
                $this->returnMsg['code'] = Config::httpCode["http_error_code"];
            }
        }else{
            $this->returnMsg["msg"] = __("Common.unknown error");
            $this->returnMsg['code'] = Config::httpCode["http_error_code"];
        }
        return $this->returnMsg;
    }
    public function imgToBase64($img_file)
    {

        $img_base64 = '';
        if (file_exists($img_file)) {
            $app_img_file = $img_file; // 图片路径
            $img_info = getimagesize($app_img_file); // 取得图片的大小，类型等
            //echo '<pre>' . print_r($img_info, true) . '</pre><br>';
            $fp = fopen($app_img_file, "r"); // 图片是否可读权限
            if ($fp) {
                $filesize = filesize($app_img_file);
                $content = fread($fp, $filesize);
                $file_content = (base64_encode($content)); // base64编码
                switch ($img_info[2]) {           //判读图片类型
                    case 1:
                        $img_type = "gif";
                        break;
                    case 2:
                        $img_type = "jpg";
                        break;
                    case 3:
                        $img_type = "png";
                        break;
                }
                $img_base64 = $file_content;//合成图片的base64编码
            }
            fclose($fp);
        }
        return $img_base64; //返回图片的base64
    }

    /**
     * 获取openId
     * @return mixed
     */
    private function getOpenId(){
        $fixed = $this->request->post();
        CommonValidate::getOpenId($fixed);
        $url='https://api.weixin.qq.com/sns/jscode2session?appid='.Config::weChat['app_id'].'&secret='.Config::weChat['app_secret'].'&js_code='.$fixed['jscode'].'&grant_type=authorization_code';
        $openid=getJson($url);
        if($openid){
            $this->checkOpenId($openid['openid']);
            $this->returnMsg['code'] = Config::httpCode["http_success_code"];
            $this->returnMsg['msg'] = __("Common.success");
            $this->returnMsg['data'] = $openid;
            return $this->returnMsg;
        }else{
            $this->returnMsg['code'] = Config::httpCode["http_error_code"];
            $this->returnMsg['msg'] = __("Common.unknown error");
            return $this->returnMsg;
        }
    }

    public function checkOpenId($openId){
        $userOpenId=DB::table("user_info")
            ->select("smallopenid")
            ->where("smallopenid","=",$openId)
            ->first();
        if($userOpenId){
            /*$t=DB::table("user_info")
                ->where("smallopenid","=",$openId)
                ->update(["update_time"=>date("Y-m-d H:i:s")]);*/
            $t=1;
        }else{
            $t=DB::table("user_info")->insert(["create_time"=>date("Y-m-d H:i:s"),"smallopenid"=>$openId]);
        }
         return $t;
    }
    /**
     * 用户登录
     * @return array
     */
    private function login()
    {

        $param = $this->request->post();
        CommonValidate::login($param);
        //判断员工是否存在
        $userInfo = DB::table("user_info")
            ->where("smallopenid", "=", $param['smallopenid'])
            ->first();

        if ($userInfo) {
            //更新信息
            //$data['smallopenid'] = isset($param['smallopenid'])?$param['smallopenid']:'';
            $data['header'] = $param['header'];
            $data['nickname'] = $param['nickName'];
            $data['sex'] = $param['sex'];
            $data['country'] = isset($param['country'])?$param['country']:'';
            $data['province'] = isset($param['province'])?$param['province']:'';
            $data['update_time'] = date("Y-m-d H:i:s");
            $up=DB::table('user_info')->where("smallopenid", "=", $param['smallopenid'])->update($data);
            if ($up) {
                $token = SessionService::SetSession('USER', $userInfo->user_id);
                $this->returnMsg["msg"] = __("Common.success");
                $this->returnMsg["data"] =$token;
                $this->returnMsg['code'] = Config::httpCode["http_success_code"];
                return $this->returnMsg;

            } else {
                $this->returnMsg['code'] = Config::httpCode["http_error_code"];
                $this->returnMsg['msg'] = __("Common.login error");
                return $this->returnMsg;
            }

        } else {
            //新注册
            $data['smallopenid'] = $param['smallopenid'];
            $data['header'] = $param['header'];
            $data['nickname'] = $param['nickName'];
            $data['sex'] = $param['sex'];
            $data['country'] = isset($param['country'])?$param['country']:'';
            $data['province'] = isset($param['province'])?$param['province']:'';
            $data['update_time'] = date("Y-m-d H:i:s");
            $data['create_time'] = date("Y-m-d H:i:s");
            $user_id = DB::table("user_info")->insertGetId($data);
            if ($user_id) {
                $token = SessionService::SetSession('USER', $user_id);

                $this->returnMsg["msg"] = __("Common.success");
                $this->returnMsg["data"] =$token;
                $this->returnMsg['code'] = Config::httpCode["http_success_code"];
                return $this->returnMsg;
            } else {
                $this->returnMsg['code'] = Config::httpCode["http_error_code"];
                $this->returnMsg['msg'] = __("Common.login error");
                return $this->returnMsg;
            }

        }
    }
    /**
     * 根据用户openid
     *  检测用户登录
     */
    public function checkToken(){
        $param = $this->request->post();
        CommonValidate::checkToken($param);
        $info=DB::table('user_info')
            ->select('session.token')
            ->join('session','session.roleId','=','user_info.user_id')
            ->where('smallopenid','=',$param['smallopenid'])
            ->first();
        if($info){
            $this->returnMsg["msg"]  = __("Common.success");
            $this->returnMsg["data"] =$info->token;
            $this->returnMsg['code'] = Config::httpCode["http_success_code"];
        }else{
            $this->returnMsg["msg"]  = __("Common.error");
            $this->returnMsg["data"] =[];
            $this->returnMsg['code'] = Config::httpCode["http_error_code"];
        }
        return $this->returnMsg;
    }
    /**
     *  调用api记录
     */
    private function insertApiInfo(){
        $param = $this->request->post();
        CommonValidate::insertApiInfo($param);
        $user_id=DB::table('user_info')
               ->select('user_id')
               ->where('smallopenid','=',$param['openid'])
               ->first();
        if(!$user_id){
            $this->returnMsg["msg"]  = __("Common.unknown error");
            $this->returnMsg['code'] = Config::httpCode["http_error_code"];
            return $this->returnMsg;
        }
        $fixed=[
            "api_name"=>Config::apiName[$param['api_name']],
            "file_path"=>$param['file_path'],
            "api_info"=>$param['api_info'],
            "user_id"=>$user_id->user_id,
            "create_time"=>date('Y-m-d H:i:s'),
            "result"=>isset($param['result'])?$param['result']:''
        ];
        $id=DB::table('item_info')
            ->insertGetId($fixed);
        if($id){
            $this->returnMsg["msg"]  = __("Common.success");
            $this->returnMsg['code'] = Config::httpCode["http_success_code"];
        }else{
            $this->returnMsg["msg"]  = __("Common.error");
            $this->returnMsg['code'] = Config::httpCode["http_error_code"];
        }
        return $this->returnMsg;
    }
    /**
     * 历史记录查询
     */
    private function history(){
        //$param = $this->request->post();
        //CommonValidate::history($param);
        $nowDate=date('Y-m-d H:i:s');
        $historyDate=date('Y-m-d 00:00:00',strtotime("-2 day"));
        $history=DB::table('user_info')
             ->select('item_info.create_time','item_info.result','item_info.file_path','item_info.api_name')
             ->join('item_info','item_info.user_id','=','user_info.user_id')
             ->where('user_info.user_id','=',$this->request->user_id)
             ->where('item_info.create_time','<=',$nowDate)
             ->where('item_info.create_time','>=',$historyDate)
             ->get();
        if($history){
            foreach ($history as $k=>$v){
                $history[$k]->create_time=date('m.d',strtotime($v->create_time));
            }
            $this->returnMsg["msg"]  = __("Common.select success");
            $this->returnMsg['code'] = Config::httpCode["http_success_code"];
            $this->returnMsg["data"]  = $history;
        }else{
            $this->returnMsg["msg"]  = __("Common.select error");
            $this->returnMsg['code'] = Config::httpCode["http_error_code"];
        }
        return $this->returnMsg;

    }

    /**
     * 获取用户基本信息
     */
    private function getUserInfo(){
       $user_id=$this->request->user_id;
       $user_info=DB::table('user_info')
               ->select('header as avatarUrl','nickname as nickName')
               ->where('user_id','=',$user_id)
               ->first();
       if($user_info){
           $this->returnMsg["msg"]  = __("Common.select success");
           $this->returnMsg['code'] = Config::httpCode["http_success_code"];
           $this->returnMsg["data"]  = $user_info;
       }else{
           $this->returnMsg["msg"]  = __("Common.select error");
           $this->returnMsg['code'] = Config::httpCode["http_error_code"];
       }
       return $this->returnMsg;
    }
    /**
     * 建议反馈
     */
    private function feedback(){
        $param = $this->request->post();
        CommonValidate::feedback($param);
        $data['user_id']=$this->request->user_id;
        $data['create_time']=date('Y-m-d H:i:s');
        $data['phone']=$param['phone'];
        $data['content']=$param['content'];
        $st=DB::table('feedback')
            ->insert($data);
        if($st){
            $this->returnMsg["msg"]  = __("Common.add success");
            $this->returnMsg['code'] = Config::httpCode["http_success_code"];

        }else{
            $this->returnMsg["msg"]  = __("Common.add error");
            $this->returnMsg['code'] = Config::httpCode["http_error_code"];
        }
        return $this->returnMsg;
    }
}