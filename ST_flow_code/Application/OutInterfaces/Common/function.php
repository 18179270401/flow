<?php
/**
 * @param $userName
 * @param $passWord
 * @param $login_date
 * @return string
 * token加密
 */
function token_encode($userName,$passWord,$login_date){
    $token = base64_encode(urlencode(md5($userName.$passWord).$login_date.$userName));
    return $token;
}

/**
 * @param $token
 * @return mixed
 * token解密
 */
function token_decode($token){
    $token_info = urldecode(base64_decode($token));
    $result['user_name'] = substr($token_info,51);
    $result['token_md5'] = substr($token_info,0,32);
    $result['token_date'] = substr($token_info,32,19);
    return $result;
}

/**
 * @param $array
 * 数组的值全部转为string
 */
function array_value_to_string(&$array){
    if( !is_array($array) && !is_object($array)){
        $array = (string)$array;
    }elseif(is_object($array)){
        $array = $array;
    }else{
        foreach($array as &$v){
            array_value_to_string($v);
        }
    }
}

/**
 * @param $result
 * 接口返回值处理
 */
function return_tidy_result($result){
    array_value_to_string($result);
    echo json_encode($result);
    return true;
}
