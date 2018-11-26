<?php
namespace App\Service\Upload;

use App\Service\Common\BaseService;
use App\Config\Config;
class UploadService extends BaseService
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
    public static function test()
    {

        return 321;
    }
    /**
     * form-data上传图片
     * @param $request
     */
    private function uploadsFile()
    {

        if ($this->request->hasFile('file')) {

            $img = $this->request->file('file');
            $path = $img->store(date('Ymd'));
            $url = Config::saveFilePath["httpPath"] . $path;

            $this->returnMsg['code'] = Config::httpCode["http_success_code"];
            $this->returnMsg['msg'] = __("Common.success");
            $this->returnMsg['data']["url"] = $url;

        } else {
            $this->returnMsg['msg'] = __("Common.error");
        }

        return $this->returnMsg;

    }

    /**
     * base64上传图片
     * @param $request
     */
    private function base64UploadsImg()
    {

        $param = $this->request->post();

        $validateResult = UploadsServiceValidate::uploadsImg($param);

        if ($validateResult === true) {

            $result = base64ImageContent($param['imgData'], Config::saveFilePath["localSavePath"]);

            if ($result) {
                $this->returnMsg['msg'] = __("WechatUser.success");
                $this->returnMsg['code'] = Config::httpCode["http_success_code"];
                $this->returnMsg['data']['url'] = Config::saveFilePath["httpPath"] . $result;
            } else {
                $this->returnMsg['msg'] = __("WechatUser.error");
            }

            return response()->json($this->returnMsg);

        } else {
            return $validateResult;
        }

    }


    /**
     * 删除上传图片
     * @param $request
     */
    private function delUploadsImg()
    {

        $param = $this->request->post();

        $validateResult = UploadsServiceValidate::delUploadsImg($param);

        if ($validateResult === true) {

            //替换成本地路劲
            $url = str_replace(Config::saveFilePath["httpPath"], Config::saveFilePath["localSavePath"], $param['url']);
            if (file_exists($url)) {

                if (unlink($url)) {
                    $this->returnMsg['msg'] = __("WechatUser.success");
                    $this->returnMsg['code'] = Config::httpCode["http_success_code"];
                } else {
                    $this->returnMsg['msg'] = __("WechatUser.error");
                }

            } else {
                $this->returnMsg['msg'] = __("WechatUser.file non existent");
            }

            return response()->json($this->returnMsg);

        } else {
            return $validateResult;
        }

    }
}