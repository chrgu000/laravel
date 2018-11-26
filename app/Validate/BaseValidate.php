<?php
 namespace App\Validate;
 use App\Config\Config;
 use Illuminate\Database\Eloquent\ModelNotFoundException;
 use Illuminate\Support\Facades\Validator;
 class BaseValidate
 {
     /**
      * 验证器公共方法
      * @param array $param 验证参数
      * @param array $roule 验证规则
      * @return mixed
      */
     public  function instance($param=array(),$rule=array()){
         $validator = Validator::make($param, $rule);

         if ($validator->fails()) {

             $error = $validator->errors()->toArray();
             $errorArr = [];
             foreach ($error as $value) {
                 $errorArr[] = $value[0];
             }

             throw new ModelNotFoundException(implode(",", $errorArr), Config::httpCode["validator_error"]);

         } else {

             return true;
         }
     }
 }