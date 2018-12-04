<?php
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

/**
* @param $msg,$info_data
* 显示正确数据
*/
function show_sucess($msg,$info_data)
{
    $result = array(
    'ret' => 0,
    'msg' => $msg,
    'info' => $info_data,
    );

    return $result;//return_tidy_result($result);
}


/**
* @param $error_code $error_msg
* 错误处理 错误显示
*/
function show_error($error_code)
{
    $result = array(
    'ret' => C($error_code),
    'msg' =>  C($error_code.'_Msg'),
    'info' => new \stdClass()
    );

    return $result;//return_tidy_result($result);
}

function curlFunction($url,$post_data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function localdecode($data) {
     $data = base64_decode($data);
      for($i=0;$i<strlen($data);$i++){
      $ord = ord($data[$i]);
      $ord -= 20;
      $string = $string.chr($ord);
      }
     return $string;
 }

//获取用户id
 function getuser_id() {
        $data = session("enterprise_key");
        if ($data != null) {
            $strArray = localdecode($data);
            $InfoArray = explode(",",$strArray);
            $user_id = $InfoArray[0];
            return $user_id;
        }
        return null;
 }

//获取企业id
 function getenterprise_id() {
        $data = session("enterprise_key");
        if ($data != null) {
            $strArray = localdecode($data);
            $InfoArray = explode(",",$strArray);
            $enterprise_id = $InfoArray[1];
            return $enterprise_id;
        }
        return null;
 }

//获取企业类型  //1是运营 2代理 3企业
 function getuser_type() {
        $data = session("enterprise_key");
        if ($data != null) {
            $strArray = localdecode($data);
            $InfoArray = explode(",",$strArray);
            $user_type = $InfoArray[2];
            return $user_type;
        }
        return null;
 }

//获取企业是否拥有财物管理权限 0表示没有， 1表示有
 function getroletype() {
        $data = session("enterprise_key");
        if ($data != null) {
            $strArray = localdecode($data);
            $InfoArray = explode(",",$strArray);
            $roletype = $InfoArray[3];
            return $roletype;
        }
        return null;
 }
?>
