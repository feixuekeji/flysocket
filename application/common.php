<?php
###公共函数库

/**
 * Notes:生成UID
 * @auther: xxf
 * Date: 2019/7/17
 * Time: 17:28
 * @return string
 */
function make_uid()
{
    @date_default_timezone_set("PRC");
    //号码主体（YYYYMMDDHHIISSNNNNNNNN）
    $order_id_main = date('YmdHis') . rand(10000000,99999999);
    $order_id_len = strlen($order_id_main);
    $order_id_sum = 0;
    for($i=0; $i<$order_id_len; $i++){
        $order_id_sum += (int)(substr($order_id_main,$i,1));
    }
    //唯一号码（YYYYMMDDHHIISSNNNNNNNNCC）
    $uid = $order_id_main . str_pad((100 - $order_id_sum % 100) % 100,2,'0',STR_PAD_LEFT);
    return $uid;
}



function signature($data, $key)
{
    if (isset($data["sign"])) {
        unset($data["sign"]);//剥离签名
    }
    if (isset($data["key"])) {
        unset($data["key"]);//剥离密匙
    }
    ksort($data);
    $data["key"] = $key;
    $sign = urldecode(http_build_query($data));
    $sign = strtoupper(md5($sign));
    return $sign;
}

/**验证签名
 * @param $data
 * @param $key
 * @return bool
 */
function checkSign($data, $key)
{
    $sign = $data['sign'] ?? '';
    if ($sign == signature($data, $key))
        return true;
    return false;
}

/**
 * 文件是否存在
 * @param $url
 * @return bool
 */
function fileIsExit($url){
    if( @fopen( $url, 'r' ) )
        return true;
    return false;
}






