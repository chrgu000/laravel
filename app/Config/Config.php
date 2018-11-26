<?php
namespace App\Config;

class Config{
    /*返回状态码*/
    const httpCode = [

        "no_existent_code" => 404,//资源不存在返回code码
        "sign_error_code" => 401,//签名错误返回code码
        "unknown_error_code" => -1,//未知错误返回code码
        "validator_error" => -2,//参数验证失败
        "http_success_code" => 1,//业务处理返回成功返回code码
        "http_error_code" => 0,//业务处理返回失败返回code码
        "token_empty_code" => 23,//用户token为空返回code码
        "token_error_code" => 22,//用户token错误
        "unsupported_media_type_code" => 415,//不支持的请求类型
        "user_disable_code" => 2,//账号被禁用

    ];
    //当前域名
    const doAdmin = "http://nm.go5678.cn";
    /*上传配置信息*/
    const saveFilePath = [
        "cdnDomain" => Config::doAdmin . "/uploads/",//cdn访问地址
        "localPath" => "./uploads/",//本地图片保存目录
        "localSavePath" => "./uploads/",//本地图片保存路劲
        "httpPath" => Config::doAdmin . "/uploads/images/"//图片上传访问地址

    ];
    const baidu=[
        "appId"=>"11789722",
        //"api_key"=>"MxB5lfQVzCV74SuIEooICRpU",
        "api_key"=>"yIIjP4EOFPU7mZQVnYH0bNrG",
        //"api_key"=>"2p1ZBkHm0EA4I9bLtfoOCCxH",
        "secret_key"=>"EPbUuFf5DKjB53r7L6elSQsRQBTUKTB2",
        //"secret_key"=>"ndr9AHvlDZyToe8h3jDp2NU24yGC0lj4",
        //"secret_key"=>"WjjCoglEmYk6g64SlZCd9hzUvYLe0huu",
    ];
    ///$appkey = 'hJZAE4D79koMgUZ5';
    //$app_id = '1106874146';
    const Api=[
      'text'=>'https://aip.baidubce.com/rest/2.0/ocr/v1/general_basic',  //提取文字
      'food'=>'https://aip.baidubce.com/rest/2.0/image-classify/v2/dish',  // 菜品识别
      'flower'=>'https://aip.baidubce.com/rest/2.0/image-classify/v1/plant', //花草识别
      'animal'=>'https://aip.baidubce.com/rest/2.0/image-classify/v1/animal', //动物识别
      'car'=>'https://aip.baidubce.com/rest/2.0/image-classify/v1/car',//车型识别
      'face'=>'https://aip.baidubce.com/rest/2.0/face/v3/detect',//  人脸识别
    ];
    const weChat=[
        "app_id"=>"wx891e2288937da72f",
        "app_secret"=>"e87a295bbeb4737f146f3bd4611ad123"
    ];
    const not_verify_token_url=[
        "common"=>[
              "checkToken",
              "getOpenId",
              "login",
              "common",
              "insertApiInfo",
              "feedback"
        ]
    ];
    const  apiName=[
        "flower"=>"秀鲜花" ,
        "face"=>"测颜值",
        "text"=>"拍照识字",
        "car"=>"试新车",
        "animal"=>"晒主子",
        "food"=>"品美食",
    ];
}
