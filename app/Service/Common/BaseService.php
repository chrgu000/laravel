<?php
 namespace App\Service\Common;
 use App\Config\Config;
 class BaseService
 {

     public function __construct()
     {
         $this->returnMsg = ["code" => Config::httpCode["http_error_code"], "msg" => __("Common.unknown error"), 'data' => []];
         $this->dbPrefix = config("database.connections.mysql.prefix");
     }
 }