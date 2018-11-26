<?php
/**
 * 数组转对象
 */
if(!function_exists('objectToArray')){
    function objectToArray($param){
        return json_decode(json_encode($param), true);
    }
}
/**
 * 多字段多条件更新
 * @param string $tableName
 * @param array $multipleData
 * @param string $type
 * @return bool
 */
function updateBatch($tableName = "", $multipleData = array(),$type='update')
{

    if ($tableName && !empty($multipleData)) {

        // column or fields to update
        $updateColumn = array_keys($multipleData[0]);
        $referenceColumn = $updateColumn[0]; //e.g id
        unset($updateColumn[0]);
        $whereIn = "";

        $q = "UPDATE " . $tableName . " SET ";
        foreach ($updateColumn as $uColumn) {
            $q .= $uColumn . " = CASE ";

            foreach ($multipleData as $data) {
                if($type=='increment'){
                    $q .= "WHEN " . $referenceColumn . " = " . $data[$referenceColumn] . " THEN " .$uColumn.'+'.$data[$uColumn]." ";
                }else{
                    $q .= "WHEN " . $referenceColumn . " = " . $data[$referenceColumn] . " THEN '" .$data[$uColumn] . "' ";
                }
            }
            $q .= "ELSE " . $uColumn . " END, ";
        }
        foreach ($multipleData as $data) {
            $whereIn .= "'" . $data[$referenceColumn] . "', ";
        }
        $q = rtrim($q, ", ") . " WHERE " . $referenceColumn . " IN (" . rtrim($whereIn, ', ') . ")";

        // Update
        return DB::update(DB::raw($q));

    } else {
        return false;
    }
}
/**
 * @desc arraySort php二维数组排序 按照指定的key 对数组进行排序
 * @param array $arr 将要排序的数组
 * @param string $keys 指定排序的key
 * @param string $type 排序类型 asc | desc
 * @return array
 */
function arraySort($arr, $keys, $type = 'asc')
{
    $keysvalue = $new_array = array();
    foreach ($arr as $k => $v) {
        $keysvalue[$k] = $v[$keys];
    }
    $type == 'asc' ? asort($keysvalue) : arsort($keysvalue);
    reset($keysvalue);
    foreach ($keysvalue as $k => $v) {
        $new_array[$k] = $arr[$k];
    }
    return $new_array;
}
/**
 * 返回数组中指定多列
 *
 * @param  Array  $input       需要取出数组列的多维数组
 * @param  String $column_keys 要取出的列名，逗号分隔，如不传则返回所有列
 * @param  String $index_key   作为返回数组的索引的列
 * @return Array
 */
function array_columns($input, $column_keys=null, $index_key=null){
    $result = array();

    $keys =isset($column_keys)? explode(',', $column_keys) : array();

    if($input){
        foreach($input as $k=>$v){

            // 指定返回列
            if($keys){
                $tmp = array();
                foreach($keys as $key){
                    $tmp[$key] = $v[$key];
                }
            }else{
                $tmp = $v;
            }

            // 指定索引列
            if(isset($index_key)){
                $result[$v[$index_key]] = $tmp;
            }else{
                $result[] = $tmp;
            }

        }
    }

    return $result;
}


/**
 * 调用类私有方法
 * @throws ReflectionException
 */
function Reflection($class,$method){

    //通过类名MyClass进行反射
    $ref_class = new \ReflectionClass($class);

//通过反射类进行实例化
    $instance  = $ref_class->newInstance();

//通过方法名myFun获取指定方法
    $method = $ref_class->getmethod($method);

//设置可访问性
    $method->setAccessible(true);

//执行方法
    return $method->invoke($instance)->original;
}

/**
 *
 */
if (!function_exists("getJson")) {

    function getJson($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }
}

if (!function_exists("dumpSql")) {

    /**
     * 打印执行sql
     * @return mixed
     */
    function dumpSql()
    {

        \Illuminate\Support\Facades\DB::listen(function ($sql) {
            echo $sql->sql;
            exit;
        });
    }
}

if (!function_exists("base64EncodeImage")) {

    /**
     * 图片转base64
     * @param $image_file
     * @return string
     */
    function base64EncodeImage($image_file)
    {
        $base64_image = '';
        if (file_exists($image_file)) {

            $image_info = getimagesize($image_file);
            $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
            $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
            return $base64_image;

        } else {
            return "";
        }

    }
}

if (!function_exists("encryption")) {

    /**
     * 字符串加密解密函数
     * @param $string
     * @param string $operation
     * @param string $key
     * @param int $expiry
     * @return string
     */
    function encryption($string, $operation = 'DECODE', $key = '', $expiry = 0)
    {
        // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
        $ckey_length = 4;
        $keya = "";
        // 密匙
        $key = md5($key ? $key : $GLOBALS['discuz_auth_key']);

        // 密匙a会参与加解密
        //$keya = md5(substr($key, 0, 16));
        // 密匙b会用来做数据完整性验证
        $keyb = md5(substr($key, 16, 16));
        // 密匙c用于变化生成的密文
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) :
            substr(md5(microtime()), -$ckey_length)) : '';
        // 参与运算的密匙
        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，
        //解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :
            sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        // 产生密匙簿
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 核心加解密部分
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'DECODE') {
            // 验证数据有效性，请看未加密明文的格式
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
                substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)
            ) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyc . str_replace('=', '', base64_encode($result));
        }
    }
}
if (!function_exists("httpPostRequest")) {

    /**
     * 模拟POST请求方式访问远程接口
     * @param $url 远程接口地址URL
     * @param $formData POST的数据
     * @return string | boolean
     */
    function httpPostRequest($url = "", $param = array(), $header = array())
    {
        if (empty($url)) {
            $result = "remote Addr cannot be empty!";
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, trim($url));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.69 Safari/537.36');

            if (strpos("==" . $url, "https")) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            }

            curl_setopt($ch, CURLOPT_HEADER, 0);// 不要http header 加快效率
            if (!empty($header) && count($header) > 0) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $header[0]));
            } else {
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            }

            curl_setopt($ch, CURLOPT_POST, 1);

            if ($param) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
            }
            $result = curl_exec($ch);
            curl_close($ch);
        }
        return $result;
    }
}
if (!function_exists("createOrderSn")) {

    /**
     * 生成唯一订单号
     * @return string
     */
    function createOrderSn()
    {
        //订单号码主体（YYYYMMDDHHIISSNNNNNNNN）
        $order_id_main = date('YmdHis') . rand(10000, 99999);
        //订单号码主体长度
        $order_id_len = strlen($order_id_main);
        $order_id_sum = 0;
        for ($i = 0; $i < $order_id_len; $i++) {
            $order_id_sum += (int)(substr($order_id_main, $i, 1));
        }
        //唯一订单号码（YYYYMMDDHHIISSNNNNNNNNCC）
        $order_id = $order_id_main . str_pad((100 - $order_id_sum % 100) % 100, 2, '0', STR_PAD_LEFT);

        return $order_id;
    }
}