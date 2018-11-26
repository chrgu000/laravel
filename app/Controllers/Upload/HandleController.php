<?php
namespace  App\Controllers\Upload;
use App\Http\Controllers\Controller;
use App\Service\Upload\UploadService;
class HandleController extends Controller{

    /**
     * form-data图片上传接口
     * @return mixed
     */
    public function uploadsFile()
    {

        return UploadService::init("uploadsFile");
    }


    /**
     * base64图片上传接口
     * @return mixed
     */
    public function base64UploadsImg()
    {

        return UploadService::init("base64UploadsImg");
    }


    /**
     * 删除上传的图片
     * @return mixed
     */
    public function delUploadsImg()
    {

        return UploadService::init("delUploadsImg");
    }

}