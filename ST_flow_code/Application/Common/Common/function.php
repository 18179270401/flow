<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/**
 * 项目函数库
 */
 
/**
 * 生成Excel文件(方法很好用)
 * $title           => excel名称
 * $tableTitle      => excel标题
 * $tableContent    => excel内容
 */
function ExportEexcel($title,$tableTitle,$tableContent){
    set_time_limit(0);
    ini_set('memory_limit', '1024M');
    header("Content-type:application/vnd.ms-excel");
    header("Content-Disposition:attachment;filename=".$title.date("Y_m_d", time()).".xls");
    $html = '';
    $html .='<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"> 
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
    <html> 
    <head> 
        <meta http-equiv="Content-type" content="text/html;charset=UTF-8" /> 
    </head> 
        <body> 
            <div align=center x:publishsource="Excel"> 
                <table x:str border=1 cellpadding=0 cellspacing=0 >';
                    $html .='<tr>';
                        foreach($tableTitle as $t){
                            $html .= '<td>'.$t.'</td>';
                        }
                    $html .='</tr>';
                    foreach($tableContent as $k=>$c){
                        //获取所有数组键名
                        $keys = array_keys($tableContent[$k]);
                        $html .= "<tr>";
                        for($j=0;$j<count($keys);$j++){
							$content = $c[$keys[$j]]?$c[$keys[$j]]:"--";
                            $html .= "<td>".$content."</td>";
                        }
                        $html .= "</tr>";
                    }
                $html .='</table>
            </div> 
        </body> 
    </html>';
    echo $html;
    exit;
}

/**
 * 生成Excel文件
 */
function CreatExcel($fileName, $headArr, $data, $Width = array())
{
	//导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
	import("Vendor.PHPExcel.PHPExcel");
	import("Vendor.PHPExcel.PHPExcel.Writer.Excel5");
	import("Vendor.PHPExcel.PHPExcel.IOFactory.php");

	$date = date("Y_m_d", time());
	$fileName .= "_{$date}.xls";

	//创建PHPExcel对象，注意，不能少了\
	$objPHPExcel = new \PHPExcel();
	$objProps = $objPHPExcel->getProperties();

	//设置表头
	$key = ord("A");
	//print_r($headArr);exit;
	foreach ($headArr as $v) {
		$colum = chr($key);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
		$key += 1;
	}

	$column = 2;
	$objActSheet = $objPHPExcel->getActiveSheet();

	//print_r($data);exit;
	foreach ($data as $key => $rows) { //行写入
		$span = ord("A");
		foreach ($rows as $keyName => $value) {// 列写入
			$j = chr($span);
			$objActSheet->setCellValue($j . $column, $value);
			$span++;
		}
		$column++;
	}
	//列宽设置
	$span = ord("A");
	foreach ($Width as $value) {
		$j = chr($span);
		$objPHPExcel->getActiveSheet()->getColumnDimension($j)->setWidth($value);
		$span++;
	}


	$fileName = iconv("utf-8", "gb2312", $fileName);
	//重命名表
	//$objPHPExcel->getActiveSheet()->setTitle('test');
	//设置活动单指数到第一个表,所以Excel打开这是第一个表
	$objPHPExcel->setActiveSheetIndex(0);
	header('Content-Type: application/vnd.ms-excel');
	header("Content-Disposition: attachment;filename=\"$fileName\"");
	header('Cache-Control: max-age=0');

	$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output'); //文件通过浏览器下载
	exit;
}

/**
 * 读取Excel文件
 */
function readExcel($file)
{

	import("Vendor.PHPExcel.PHPExcel");
	import("Vendor.PHPExcel.PHPExcel.IOFactory.php");

	$cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
	$cacheSettings = array();
	\PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
	$objPHPExcel = new \PHPExcel();
	$objPHPExcel = \PHPExcel_IOFactory::load($file);
	$indata = $objPHPExcel->getSheet(0)->toArray();
	return $indata;
}

/**
 * @return array|string
 */
function get_client_ip2(){
	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")){
		$ip = getenv("HTTP_CLIENT_IP");
	}else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")){
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	}else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")){
		$ip = getenv("REMOTE_ADDR");
	}else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")){
		$ip = $_SERVER['REMOTE_ADDR'];
	}else{
		$ip = "unknown";
	}
	return($ip);
}


/**
 * 生成随机字符串(长度小于63位则不重复)
 * @param $length
 * @return string
 */
function createRandomStr($length)
{
	$str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';//62个字符
	$strlen = 62;
	while ($length > $strlen) {
		$str .= $str;
		$strlen += 62;
	}
	$str = str_shuffle($str);
	return substr($str, 0, $length);
}

/**
 * 产生随机字符串（随机出现，默认数字）
 * @param int $length
 * @param string $chars
 * @return string
 */
function randomY($length, $chars = '0123456789')
{
	$hash = '';
	$max = strlen($chars) - 1;
	for ($i = 0; $i < $length; $i++) {
		$hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}

/**
 * 产生随机字符串
 * @param  int  $length [字符长度]
 * @param  boolean $upper  [是否包含大写，默认包含]
 * @return string          [随机字符串]
 */
function getrandstr($length, $upper = true) {
	$chars = $upper ? "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789" : "abcdefghijklmnopqrstuvwxyz0123456789";
	$str = "";
	if (is_numeric($length) && $length > 0) {
		for ($i = 0; $i < $length; $i++) {
			$str .= $chars[mt_rand(0, strlen($chars) - 1)];
		}
	}
	return $str;
}

/**
 * 获取当前服务器域名(带http不带最后的/)
 * @return string 域名
 */
function gethostwithhttp() {
	$ret = '';
	$domain = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
	if ($domain) {
		$ret = (is_ssl() ? 'https://' : 'http://') . $domain;
	}
	return $ret;
}

/** get请求 */
function Get($url){

	$curl = curl_init();
	//需要获取的URL地址，也可以在curl_init()函数中设置。
	curl_setopt($curl, CURLOPT_URL, $url);
	//将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_HEADER, 0);


	$output = curl_exec($curl);
	curl_close($curl);
	return $output;
}

/**
 * 发送 键值对
 * @param $url
 * @param int $timeout
 * @param $data
 * @return mixed
 */
function Post($url, $timeout = 0, $data)
{

	$curl = curl_init();
	//需要获取的URL地址，也可以在curl_init()函数中设置。
	curl_setopt($curl, CURLOPT_URL, $url);
	//将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	//当根据Location:重定向时，自动设置header中的Referer:信息。
	curl_setopt($curl, CURLOPT_AUTOREFERER, true);
	//启用时会发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
	curl_setopt($curl, CURLOPT_POST, true);
	//设置cURL允许执行的最长秒数。
	curl_setopt($curl, CURLOPT_TIMEOUT_MS, $timeout);
	//全部数据使用HTTP协议中的"POST"操作来发送。
	//要发送文件，在文件名前面加上@前缀并使用完整路径。
	//这个参数可以通过urlencoded后的字符串类似'para1=val1&para2=val2&...'或使用一个以字段名为键值，字段数据为值的数组。
	//如果value是一个数组，Content-Type头将会被设置成multipart/form-data。
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

	$output = curl_exec($curl);
	curl_close($curl);
	return $output;
}

/**
 * 发送 JSON
 * @param $url
 * @param $timeout
 * @param $data
 * @return mixed
 */
function PostJSON($url, $timeout, $data){

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json',
	'Content-Length: ' . strlen($data))
	);

	return $result = curl_exec($ch);
}

function  http_jump($url,$data=null){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($curl);
	curl_close($curl);
	return $output;
}

/** https请求（支持GET和POST） */
function https_request($url, $data = null) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	if (!empty($data)) {
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	}
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($curl);
	curl_close($curl);
	return $output;
}

function is_numerics($text){
	return is_numeric($text) ? true : false;
}

/**
 * 用正则表达式验证手机号码的格式是否正确(中国大陆区)
 * @param integer $tel    所要验证的手机号
 * @return boolean true格式正确/false格式错误
 */
function isMobile($mobile) {
	return preg_match("/^[0-9-]{6,20}$/", $mobile) ? true : false;
}

function isTel($tel){
	return preg_match("/^[0-9-]{6,20}$/", $tel) ? true : false;
}

function isQQ($qq){
	return preg_match("/^[0-9]{5,20}$/", $qq) ? true : false;
}

/**
 * 用正则表达式验证手机号码的格式是否正确(中国大陆区)
 * @param $mobile
 * @return bool
 */
function isMobile2($mobile) {
	return preg_match("/^1[34578][0-9]{9}$/", $mobile) ? true : false;
	//$rule = "/^0?(13[0-9]|14[57]|15[012356789]|17[0-9]|18[023456789])[0-9]{8}$/"; //参考
}

/**
 * 用正则表达式验证手机号码的格式是否正确(中国大陆区)
 * @param integer $tel    所要验证的手机号
 * @return boolean true格式正确/false格式错误
 */
function isEmail($email) {
	return preg_match("/^(|([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+)$/", $email) ? true : false;
}


function isPassword($password){
	return preg_match('/^[\S]{6,20}$/', $password) ? true : false;
}

function isIdentity($Identity){
	return preg_match('/^(\\d{15}$|^\d{18}$|^\\d{17}(\\d|X|x))$/', $Identity) ? true : false;
}
function isIcense($Icense){
	return preg_match('/^(\\d{10,21})$/', $Icense) ? true : false;
}

/**
 * 验证手机号并返回结果(已检查的手机号会存数据库)
 * @param unknown $mobile
 * @return string|Ambigous <string, mixed>
 */
/*function CheckMobile($mobile) {
	if(!isMobile2($mobile)) {
		$json['status'] = 'error';
		$json['msg'] = "失败：手机号不符合基本规则。";
		return $json;
	}
	//CALL p_get_mobile_info('18879103791');
	//读取数据是否有数据
	$result = M()->query("call p_get_mobile_info('".$mobile."')");
	$ret = $result[0];
	if ($ret) {
		$ret['status'] = 'success';
		$ret['mobile'] = $mobile;
		return $ret;
	}else{
		$json['status'] = 'error';
		$json['msg'] = "判断运营商失败。";
		return $json;
	}
}*/

function CheckMobile($mobile) {
	if(!isMobile2($mobile)) {
		$json['status'] = 'error';
		$json['msg'] = "失败：手机号不符合基本规则。";
		return $json;
	}
	//读取数据是否有数据
	$result = M('SysMobileDict')->where(array('mobile' => $mobile))->find();
	if ($result) {
		$result['status'] = 'success';
		return $result;
	}
	//读取存过中的数据
	$result1 = M()->query("call p_get_mobile_info('".$mobile."')");
	$result = $result1[0];
	//将数据添加到号码查询表中
	$ins = array(
		'mobile'        => $mobile,
		'operator_id'   => $result['operator_id']==''?'':$result['operator_id'],
		'operator_name' => $result['operator_name']==''?'':$result['operator_name'],
		'province_id'   => $result['province_id']==''?'':$result['province_id'],
		'province_name' => $result['province_name']==''?'':$result['province_name'],
		'area_code'     => $result['area_code']==''?'':$result['area_code'],
		'city_name'     => $result['city_name']==''?'':$result['city_name'],
		'city_id'    	=> $result['city_id']==''?'':$result['city_id'],
		'card'          => '',
		//'postcode'      => '',
	);
	M("SysMobileDict")->add($ins);

    if ($result) {
        $result['status'] = 'success';
        $result['mobile'] = $mobile;
        return $result;
    } else {
        $json['status'] = 'error';
        $json['msg'] = "判断运营商失败。";
    }
	return $json;
}

/*function CheckMobile($mobile) {
	if(!isMobile2($mobile)) {
		$json['status'] = 'error';
		$json['msg'] = "失败：手机号不符合基本规则。";
		return $json;
	}
    //读取数据是否有数据
	$result = M('SysMobileDict')->where(array('mobile' => $mobile))->find();
	if ($result) {
		$result['status'] = 'success';
		return $result;
	}

	//聚合数据API
	$appkey = 'd28e2d96a772a54229b30a935abe7637'; #通过聚合申请到数据的appkey
	$url = 'http://apis.juhe.cn/mobile/get'; #请求的数据接口URL
	$params = 'key=' . $appkey . '&phone=' . $mobile;
	$content = juhecurl($url, $params, 0);
    
	if ($content) {
		$result = json_decode($content, true);
		#错误码判断
		$error_code = $result['error_code'];
		if ($error_code == 0) {
			$json['status'] = 'success';
            $date['mobile'] = $json['mobile'] = $mobile;
            
            #根据所需读取相应数据
            $company = array("1"=>"中国移动","2"=>"中国联通","3"=>"中国电信");
            if(in_array($result['result']['company'],$company)){
                //读取省份信息
                $p_map['province_name'] = array("like","%".$result['result']['province']."%");
                $province = M("SysProvince")->where($p_map)->find();
                //获取运营商ID号
                $operator_id = $result['result']['company']=="中国移动"?"1":($result['result']['company']=="中国联通"?"2":"3");
                $date['operator_id']    = $json['operator_id']      = $operator_id;
                $date['operator_name']  = $json['operator_name']    = $company[$operator_id];
                $date['province_id']    = $json['province_id']      = $province['province_id'];
                $date['province_name']  = $json['province_name']    = $province['province_name'];
                $date['area_code']      = $json['area_code']        = $result['result']['areacode']; 
                $date['city_name']      = $json['city_name']        = $result['result']['city'];
                $date['card']           = $json['card']             = $result['result']['card'];
                $date['postcode']       = $json['postcode']         = $result['result']['zip'];
                M("SysMobileDict")->add($date);
            }else{
                $json['status'] = 'error';
				$json['msg'] = "判断运营商失败。";
            }
		} else {
			$json['status'] = 'error';
			$json['msg'] = $result['reason'];
		}
	}
	return $json;
}*/

/**
 ***请求接口，返回JSON数据
 ***@url:接口地址
 ***@params:传递的参数
 ***@ispost:是否以POST提交，默认GET
 */
function juhecurl($url, $params = false, $ispost = 0)
{
	$httpInfo = array();
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22');
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	if ($ispost) {
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_URL, $url);
	} else {
		if ($params) {
			curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
		} else {
			curl_setopt($ch, CURLOPT_URL, $url);
		}
	}
	$response = curl_exec($ch);
	if ($response === FALSE) {
		#echo "cURL Error: " . curl_error($ch);
		return false;
	}
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$httpInfo = array_merge($httpInfo, curl_getinfo($ch));
	curl_close($ch);
	return $response;
}


function CreatUniqueID($key1, $key2)
{
	$md5 = md5((round(microtime(true) * 1000)) . $key1 . $key2);
	$array = array();
	$md5_strlen = strlen($md5);
	for ($i = 0; $i < $md5_strlen; $i++) {

		$value = (int)(ord($md5[$i]));
		if ($value >= 48 && $value <= 57) {
			$array[] = (string)$md5[$i];
		} else {
			$array[] = (string)((abs($value - 100)) % 10);
		}
	}
	return implode('', $array);
}

/**
 * 检查密码强度 总计 200 分
 */
function PasswordStrength($string)
{
	$h = 0;
	$score = 0;
	$size = strlen($string);
	foreach (count_chars($string, 1) as $v) {
		$p = $v / $size;
		$h -= $p * log($p) / log(2);
	}
	$strength = ($h / 4) * 100;
	if ($strength > 100) {
		$strength = 100;
	}
	if (preg_match("/[0-9]+/", $string)) {
		$score++;
	}

	if (preg_match("/[0-9]{3,}/", $string)) {
		//$score ++;
	}

	if (preg_match("/[a-z]+/", $string)) {
		$score++;
	}

	if (preg_match("/[a-z]{3,}/", $string)) {
		$score++;
	}

	if (preg_match("/[A-Z]+/", $string)) {
		$score += 2;
	}

	if (preg_match("/[A-Z]{2,}/", $string)) {
		$score += 2;
	}
	if (preg_match("/[A-Z]{3,}/", $string)) {
		$score += 3;
	}
	if (preg_match("/[A-Z]{4,}/", $string)) {
		$score += 4;
	}
	if (preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]+/", $string)) {
		$score += 2;
	}

	if (preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]{3,}/", $string)) {
		$score++;
	}
	if (strlen($string) >= 8) {
		$score++;
	}
	if (strlen($string) >= 10) {
		$score++;
	}
	return $strength + $score * 10;
}

/*
 * 构建支付宝退款参数
function bulidAlipayRefundParam($inParam)
{
	$num = 0;
	$msg = '';
	foreach ($inParam as $row) {
		$msg = $msg . $row['alipay_order_id'] . '^' . $row['price'] . '^充值失败，退款！#';
		$num++;
	}
	$msg = substr($msg, 0, strlen($str) - 1);
	$parameter = array(
			"service" => "refund_fastpay_by_platform_pwd",
			"partner" => trim(C('alipay_config.partner')),
			"notify_url" => C('alipay.notify_url_refund'),
			"seller_email" => C('alipay.seller_email'),
			"refund_date" => date('Y-m-d H:i:s'),
			"batch_no" => date('Ymd') . time() . randomY(8),
			"batch_num" => $num,
			"detail_data" => $msg,
			"_input_charset" => trim(strtolower(C('alipay_config.input_charset')))
	);
	return $parameter;
}
*/

/*
 * 发送邮件
 * @param $to  收件人地址
 * @param $name  发送方名称
 * @param $title  邮件标题
 * @param $content  邮件内容

function senEmail($to,$name,$title,$content,$attachment = null){

	$config = C('THINK_EMAIL');
	vendor('PHPMailer.class#phpmailer'); //从PHPMailer目录导class.phpmailer.php类文件
	date_default_timezone_set('PRC');
	$mail = new PHPMailer(); //PHPMailer对象
	$mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
	$mail->IsSMTP();  // 设定使用SMTP服务
	//$mail->SMTPDebug  = 0;                     // 关闭SMTP调试功能
	// 1 = errors and messages
	// 2 = messages only
	$mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
	// $mail->SMTPSecure = 'ssl';                 // 使用安全协议
	$mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器
	$mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名
	$mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码
	$mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
	$replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
	$replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
	$mail->AddReplyTo($replyEmail, $replyName);
	$mail->Subject    = $title;
	$mail->AltBody    = "为了查看该邮件，请切换到支持 HTML 的邮件客户端";
	$mail->MsgHTML($content);
	$mail->AddAddress($to, $name);
	if(is_array($attachment)){ // 添加附件
		foreach ($attachment as $file){
			is_file($file) && $mail->AddAttachment($file);
		}
	}
	return  $mail->Send() ? true : $mail->ErrorInfo;
}
 */

/*
 * 发送短信方法
 * @param string $mobiles 	手机号，多个手机号为用半角 , 分开，如13899999999,13688888888(GET方式最多5000个，必填)
 * @param unknown $content 	发送内容（必填）

function send_sms1($mobiles, $content) {
	$username = "steoc";
	$pwd = "xm70j04b";
	$password = md5($username."".md5($pwd));
	//$content = "您的验证码是：123456【企业签名】";
	$url = "http://120.55.248.18/smsSend.do?";

	$param = http_build_query(
		array(
			'ext'       => '01',
			'username'	=> $username,
			'password'	=> $password,
			'mobile'	=> $mobiles,
			'content'	=> $content, //iconv("GB2312","UTF-8",$content)
		)
	);

	$ret = https_request($url, $param);
	return $ret;
}
*/

/**
 * 将二维数据转一维数据
 */
function get_array_column($arr,$field){
    if(is_array($arr)){
        foreach($arr as $v){
            $new_arr[] = $v[$field];
        }
        return $new_arr;
    }
    return '';
}


/** XML转成数组(注意值的格式 @attributes) */
function simplest_xml_to_array($xmlstring) {
	return json_decode(json_encode((array) simplexml_load_string($xmlstring)), true);
}

/**
 *
 * 将xml转为数组
 * @param string $xml xml字符串
 * @param string $version xml版本
 * @param string $charset xml编码
 */
function xmlToArray($xml, $version="1.0", $charset="utf-8"){
	$doc = new \DOMDocument ("1.0", $charset);
	$doc->loadXML ($xml);
	$result = domNodeToArray($doc);
	if(isset($result['#document'])){
		$result = $result['#document'];
	}
	return $result;
}

/**
 *
 * 将domNode转为数组
 * @param DOMNode $oDomNode
 */
function domNodeToArray(\DOMNode $oDomNode = null) {
	// return empty array if dom is blank
	if (! $oDomNode->hasChildNodes ()) {
		$mResult = $oDomNode->nodeValue;
	} else {
		$mResult = array ();
		foreach ( $oDomNode->childNodes as $oChildNode ) {
			// how many of these child nodes do we have?
			// this will give us a clue as to what the result structure should be
			$oChildNodeList = $oDomNode->getElementsByTagName ( $oChildNode->nodeName );
			$iChildCount = 0;
			// there are x number of childs in this node that have the same tag name
			// however, we are only interested in the # of siblings with the same tag name
			foreach ( $oChildNodeList as $oNode ) {
				if ($oNode->parentNode->isSameNode ( $oChildNode->parentNode )) {
					$iChildCount ++;
				}
			}
			$mValue = domNodeToArray ( $oChildNode );
			$sKey = ($oChildNode->nodeName {0} == '#') ? 0 : $oChildNode->nodeName;
			$mValue = is_array ( $mValue ) ? $mValue [$oChildNode->nodeName] : $mValue;
			// how many of thse child nodes do we have?
			if ($iChildCount > 1) { // more than 1 child - make numeric array
				$mResult [$sKey] [] = $mValue;
			} else {
				$mResult [$sKey] = $mValue;
			}
		}
		// if the child is <foo>bar</foo>, the result will be array(bar)
		// make the result just 'bar'
		if (count ( $mResult ) == 1 && isset ( $mResult [0] ) && ! is_array ( $mResult [0] )) {
			$mResult = $mResult [0];
		}
	}
	// get our attributes if we have any
	$arAttributes = array ();
	if ($oDomNode->hasAttributes ()) {
		foreach ( $oDomNode->attributes as $sAttrName => $oAttrNode ) {
			// retain namespace prefixes
			$arAttributes ["@{$oAttrNode->nodeName}"] = $oAttrNode->nodeValue;
		}
	}
	// check for namespace attribute - Namespaces will not show up in the attributes list
	if ($oDomNode instanceof DOMElement && $oDomNode->getAttribute ( 'xmlns' )) {
		$arAttributes ["@xmlns"] = $oDomNode->getAttribute ( 'xmlns' );
	}
	if (count ( $arAttributes )) {
		if (! is_array ( $mResult )) {
			$mResult = (trim ( $mResult )) ? array ($mResult ) : array ();
		}
		$mResult = array_merge ( $mResult, $arAttributes );
	}
	$arResult = array ($oDomNode->nodeName => $mResult );
	return $arResult;
}

/**
 * 写错误日志，供查找问题
 * @param array $msg 日志数组
 */
function write_error_log($msg,$name_ext='') {
	$msg = serialize($msg);
	$path = RUNTIME_PATH.'/Debuglogs/'.date('Ym').'/';
	if(!file_exists($path)) {
		@mkdir($path, 0777, true);
		@chmod($path, 0777);
	}
	$logFile = $path.date('d').'_error'.$name_ext.'.log';
	$now = date('Y-m-d H:i:s');
	$msg = "[{$now}] {$msg} \n";
	error_log($msg, 3, $logFile);
}

/**
 * 写调试日志，供查找问题
 * @param array $msg 日志数组
 */
function write_debug_log($msg) {
	$msg = serialize($msg);
	$path = RUNTIME_PATH.'/Debuglogs/'.date('Ym').'/';
	if(!file_exists($path)) {
		@mkdir($path, 0777, true);
		@chmod($path, 0777);
	}
	$logFile = $path.date('d').'_debug'.'.log';
	$now = date('Y-m-d H:i:s');
	$msg = "[{$now}] {$msg} \n";
	error_log($msg, 3, $logFile);
}

/** 给列表数据加上排序号，排序字段为sort_no */
function get_sort_no($list, $firstrow) {
	if(!empty($list) && is_array($list) && $firstrow >= 0) {
		foreach ($list as $k => &$v) {
			$v['sort_no'] = $firstrow+$k+1;
		}
	} else {
		$list = array();
	}
	return $list;
}

/**
 * [msubstr 截取字符串]
 *
 * @param  [type]  $str     [原始字符串]
 * @param  integer $start   [从何处开始截取]
 * @param  [type]  $length  [截取字符串的长度]
 * @param  string  $charset [字符编码]
 * @param  boolean $suffix  [截取后是否需要有...代替被截掉的字符]
 *
 * @return [type]           [截取后的字符串]
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
    if (function_exists("mb_substr")) {
        $slice = mb_substr($str, $start, $length, $charset);
    } elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    $chaochu = true;
    if (function_exists("mb_strlen")) {
        $len = mb_strlen($str, $charset);
        if ($len <= $length)
            $chaochu = false;
    }
    return $suffix ? ($chaochu ? $slice . '...' : $slice) : $slice;
}




/**
 * 获取所属平台
 * @$type_id 字段参数
 * @$arr_num 如果只有代理商和企业这个参数值给出1
 */
function get_sys_type($type_id){
    $arr = array('1'=>'尚通端','2'=>'代理商端','3'=>'企业端');
    if(empty($type_id))return $arr;
    return $arr[$type_id];
}

/**
 * 获取所属运营商名称
 * @param $operator_id 运营商ID
 */
function get_operator_name($operator_id) {
	$arr = array('1'=>'中国移动','2'=>'中国联通','3'=>'中国电信');
	if(empty($operator_id))return $arr;
	return $arr[$operator_id];
}

/**
 * 获取流量场景参与频率
 * @param $id
 * @return mixed
 */
function get_scene_frequency($id=null) {
	$arr = array('1'=>'每天', '2'=>'每周', '3'=>'每月', '4'=>'整个活动');
	if(empty($id)) {
		return $arr;
	}
	return $arr[$id];
}

/**
 * 生成订单、代理商和企业的流水编号
 * @$number 代理商、企业、手机号
 * @$digit  生成随时的个数(默认为4位)
 */
function apply_number($number,$digit = 4){
    $salttype = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    $keys = array_rand($salttype, $digit);
    foreach ($keys as $v) {
        $ints.= $salttype[$v];
    }
    return $number.time().$ints;
}

function apply_number2($number,$digit = 6){
	$salttype = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
	$keys = array_rand($salttype, $digit);
	foreach ($keys as $v) {
		$ints.= $salttype[$v];
	}
	return $number.round(microtime(true) * 1000).$ints;
}

/**
 * 计算折扣价
 */
function discount_price($price,$discount){
    $price = $price*$discount;
    $price = round($price,3);
    return sprintf("%1.3f",$price);
}

/**
 * 判断用户是否有操作权限
 */
function is_jurisdiction($rbacurl){
    $rbacAll = session("Admin");
    if(!empty($rbacAll['right']) && is_array($rbacAll['right'])){
        $rbacurl = strtolower($rbacurl);
        if(in_array($rbacurl,$rbacAll['right'])){
            return true;
        }
    }
    return false;
}

/**
 * 审核关状态
 */
function get_apply_status($st_id){
	$status = array("1"=>"草稿","2"=>"待审核","3"=>"初审通过","4"=>"初审驳回","5"=>"复审通过","6"=>"复审驳回","7"=>"打款成功","8"=>"打款驳回");
	if(empty($st_id)){
		return $status;
	}
	return $status[$st_id];
}

/**
 * 冻结审核状态
 */
function get_frozen_approve_status($st_id){
	$status = array("1"=>"待审核","2"=>"冻结初审成功","3"=>"冻结初审驳回","4"=>"冻结复审成功","5"=>"冻结复审驳回");
	if(empty($st_id)){
		return '';
	}
	return $status[$st_id];
}

function explode_time($str){
	$res=explode(".",$str);
	return $res[0];
}


/**
 * 解冻审核状态
 */
function get_thaw_approve_status($st_id){
	$status = array("1"=>"待审核","2"=>"解冻初审成功","3"=>"解冻初审驳回","4"=>"解冻复审成功","5"=>"解冻复审驳回");
	if(empty($st_id)){
		return '';
	}
	return $status[$st_id];
}

/**
 * 审核类型(1:冻结、2:解冻)
 */
function get_frozen_thaw_process_type($st_id){
	$status = array("1"=>"冻结","2"=>"解冻");
	if(empty($st_id) || !isset($status[$st_id])){
		return '';
	}
	return $status[$st_id];
}

/*企业借款*/

function get_pay_status($st_id){
	$status = array("1"=>"草稿","2"=>"待审核","3"=>"审核通过","4"=>"审核驳回");
	if(empty($st_id)){
		return $status;
	}
	return $status[$st_id];
}

function get_apply_status2($st_id,$type=1){
    if(empty($st_id))return "";
    if($type==1){
        $status = array("1"=>"审核通过","2"=>"审核驳回");
    }elseif($type==2){
        $status = array("1"=>"初审通过","2"=>"初审驳回");
    }elseif($type==3){
        $status = array("1"=>"复审通过","2"=>"复审驳回");
    }else{
        $status = array("1"=>"打款成功","2"=>"打款驳回");
    }
    return $status[$st_id];
}

/*开票详情中审核步骤*/
function get_apply_status3($st_id,$type=1){
	if(empty($st_id))return "";
	if($type==1){
		$status = array("1"=>"审核通过","2"=>"审核驳回");
	}elseif($type==2){
		$status = array("1"=>"初审通过","2"=>"初审驳回");
	}elseif($type==3){
		$status = array("1"=>"复审通过","2"=>"复审驳回");
	}else{
		$status = array("1"=>"开票成功","2"=>"开票驳回");
	}
	return $status[$st_id];
}

/**
 * 合同审核状态
 */
function get_contract_status($st_id){
	$status = array("1"=>"草稿","2"=>"待审核","3"=>"初审通过","4"=>"初审驳回","5"=>"复审通过","6"=>"复审驳回");
	if(empty($st_id)){
		return $status;
	}
	return $status[$st_id];
}

/**
 * 现金
 */
function get_operate_type ($st_id,$type=0){
    $status = array("1"=>"购买流量","2"=>"充值","3"=>"提现","4"=>"划拨","5"=>"返还","6"=>"分红","7"=>"退款","8"=>"测试款","9"=>"账户冻结","10"=>"账户解冻","11"=>"收回");
	//$status = array("1"=>"购买流量","2"=>"充值","3"=>"提现","4"=>"划拨","5"=>"返还","6"=>"分红","7"=>"退款","8"=>"测试款");
    if(empty($st_id)){
        return $status;
    }
	if($st_id==5 && $type==1){
		return "收回";
	}
    return $status[$st_id];
}

/**
 * 订单状态
 */

function get_order_status ($st_id){
	$status = array("0"=>"等待提交","1"=>"提交成功","2"=>"充值成功","3"=>"充值/提交 失败，再次等待备用通道提交","4"=>"备用通道提交成功","5"=>"备用通道充值成功","6"=>"备用通道 充值/提交 失败");
	return $status[$st_id];
}
/**
 * 订单状态
 */

function get_visit_type ($st_id){
	$status = array("1"=>"电话","2"=>"面谈","3"=>"邮件","4"=>"其他");
	return $status[$st_id];
}

/*读取通道信息*/
function channel_info($id){
	$info=M('channel')->where('channel_id='.$id)->find();
	return $info['channel_name'].'('.$info['channel_code'].')';
}



/**
 * 读取用户名称
 */
function get_user_name($id,$type=null){
    if(!isset($id) || empty($id))return $id;
    $name = M("SysUser")->where(array('user_id'=>$id))->field("user_name,proxy_id")->find();
    if(!empty($name)){
		if($type=="proxy"){
			$user_type=D("SysUser")->self_user_type();
			$self_proxy_id=D('SysUser')->self_proxy_id();
			if($user_type==2 && $name['proxy_id']!=$self_proxy_id){
				return "系统管理员";
			}
		}
        return $name['user_name'];
    }
    return $id;
}

/**
 * [in_array2 判断一个值是否在二维数组内]
 *
 * @param  [type] $val [给出的值]
 * @param  array  $a   [一维数组或二维数组]
 *
 * @return [type]      [description]
 */
function in_array2($val = null, $a = array()) {
    if (empty($a) || empty($val))
        return false;
    if (!is_string($val))
        return false;
    if (!is_array($a))
        return false;
    if (in_array($val, $a))
        return true;
    foreach ($a as $k => $v) {
        if (is_array($v)) {
            if (in_array($val, $v))
                return true;
        }
        else {
            if ($val == $v)
                return true;
        }
    }
    return false;
}

/*读取记录中对象企业或代理商的信息
$boj  对象 id
$type 对象类型 1代理商 2企业
返回：企业或者代理商的名称和编号
*/
function obj_data($obj,$type,$data=''){
	$info='';
	if($type==1){
		$res = M("proxy")->where('proxy_id='.$obj)->field("proxy_code,proxy_name")->find();
		if($data=='name'){
			$info=$res['proxy_name'];
		}else{
			$info=$res['proxy_code'];
		}
	}
	if($type==2){
		$res = M("enterprise")->where('enterprise_id='.$obj)->field("enterprise_code,enterprise_name")->find();
		if($data=='name'){
			$info=$res['enterprise_name'];
		}else{
			$info=$res['enterprise_code'];
	    }
	}
	return $info;
}
function obj_channel($channel_id){
	$channels=M("channel")->where(array("channel_id"=>$channel_id))->field("channel_name ,channel_code ")->find();
	if($channels){
		return "(".$channels['channel_code'].")".$channels['channel_name'];
	}else{
		return "";
	}
}

/*获取操作日志中企业或代理商的名称
$boj  对象 id
$type 对象类型 1代理商 2企业
返回：企业或者代理商的名称和编号
*/
function obj_name($obj,$type){
	$info='';
	$where['status']=array('neq',2);
	if($type==1){
		$where['proxy_id']=$obj;
		$res = M("proxy")->where($where)->field("proxy_code,proxy_name")->find();
		$info=$res['proxy_name'].'('.$res['proxy_code'].')';
	}
	if($type==2){
		$where['enterprise_id']=$obj;
		$res = M("enterprise")->where($where)->field("enterprise_code,enterprise_name")->find();
		$info=$res['enterprise_name'].'('.$res['enterprise_code'].')';

	}
	return $info;
}

/**
  *	获取当前审核过程阶段
  */
function get_approve_stage($st_id){
	$status = array("1"=>"初审信息","2"=>"复审信息","3"=>"打款信息");
	return $status[$st_id];
}

/**
 *	获取当前审核过程阶段
 */
function get_ticket_approve_stage($st_id){
	$status = array("1"=>"初审信息","2"=>"复审信息","3"=>"开票信息");
	return $status[$st_id];
}

/**
 *	获取当前
 */
function get_order_status_st($st_id){
	if(D('SysUser')->self_user_type()==1){
		$status = array("0"=>"等待提交","1"=>"提交成功","2"=>"充值成功","3"=>"充值失败","4"=>"提交成功(备)","5"=>"充值成功(备)","6"=>"充值失败(备)");
	}else{
		$status = array("0"=>"正在送充","1"=>"正在送充","2"=>"充值成功","3"=>"充值失败","4"=>"正在送充","5"=>"充值成功","6"=>"充值失败");
	}

	return $status[$st_id];
}

/**
 * 生成提现和充值申请单号
 * $number => 数量
 * $type   => 类型（1：充值，2为提现）
 */
function generate_order($number,$type){
    $type = $type==1?"CZSQD":"TXSQD";
    $var=sprintf("%04d", $number);
    return $type.date("Ymd",time()).$var;
}


/**
 * 生成借款和充值申请单号
 * $number => 数量
 * $type   => 类型（1：借款，2还款）
 */
function generate_loan($number,$type){
	$type = $type==1?"JKSQD":"HKSQD";
	$var=sprintf("%04d", $number);
	return $type.date("Ymd",time()).$var;
}
/**
 * 生成企业或者代理商资金管理
 * $number => 数量
 * $type   => 类型（1：代理商间划拨，2企业间划拨）
 */
function generate_transfer($number,$type){
	$type = $type==1?"DLSZJHBSQD":"QYZJHBSQD";
	$var=sprintf("%04d", $number);
	return $type.date("Ymd",time()).$var;
}

/**
 * 生成企业开票、代理商开票管理，上游开票
 * $number => 数量
 * $type   => 类型（1：代理商，2企业，3上游）
 */
function generate_ticket($number,$type){
	$tittle='';
	if($type==1) {
		$tittle = "DLSKPSQD";
	}
	if($type==2){
		$tittle = "QYKPSQD";
	}
	if($type==3){
		$tittle = "SYKPSQD";
	}
	$var=sprintf("%04d", $number);
	return $tittle.date("Ymd",time()).$var;
}

function get_source_name($source=NULL,$type){
	if($type==2){  //2 表示是授信的
		$status=array("1"=>"汇款","2"=>"微信支付","3"=>"支付宝支付","4"=>"");//4表示授信
	}else{
		$status=array("1"=>"汇款","2"=>"微信支付","3"=>"支付宝支付");
	}

    if($source){
        return $status[$source];
    }
	return $status;
}
function get_text_name($source,$type=null){
	$status=array("1"=>"打款户名","2"=>"支付订单号","3"=>"交易号","4"=>"打款户名");
	if($source){
		return $status[$source];
	}else{
		if($type){
			return $status[1];
		}
	}
	return $status;
}
function get_source_name_channel($source=NULL){
	$status=array("1"=>"汇款","2"=>"微信支付","3"=>"支付宝支付","4"=>"授信");
	if($source){
		return $status[$source];
	}
	return $status;
}

function get_source_info($source){
	$status=array("1"=>"汇款","2"=>"微信支付","3"=>"支付宝支付");
	if($source){
		return $status[$source];
	}
	return $status;
}

function get_transaction_name($id){
	$status=array("1"=>"打款户名","2"=>"支付订单号","3"=>"交易号");
	if($id){
		return $status[$id];
	}
	return $status;
}

function back_content($st_id){
	if($st_id!=='' ||$st_id!=null){

		$array=explode('->',$st_id);
		if(count($array)!=1){
			$res='';
			foreach($array as $v){
				$res.=$v.'<br/>';
			}
			return  $res;
		}else{
			return  $st_id;
		}
	}else{
		return $st_id;
	}
}


/**
 * 开始时间
 */
function start_time($time){
    $time = strtotime($time);
    $beginToday = date("Y-m-d H:i:s",mktime(0,0,0,date('m',$time),date('d',$time),date('Y',$time)));
    return $beginToday;
}

/**
 * 开始时间
 * 转换为20160808的格式
 */
function record_start_time($time){
	$time = strtotime($time);
	$beginToday = date("Ymd",mktime(0,0,0,date('m',$time),date('d',$time),date('Y',$time)));
	return $beginToday;
}

/**
 * 结束时间
 */
function end_time($time){
    $time = strtotime($time);
    $endToday = date("Y-m-d H:i:s",mktime(23,59,59,date('m',$time),date('d',$time),date('Y',$time)));
    return $endToday;
}
/**
 * 结束时间
 * 转换为20160808的格式
 */
function record_end_time($time){
	$time = strtotime($time);
	$endToday = date("Ymd",mktime(23,59,59,date('m',$time),date('d',$time),date('Y',$time)));
	return $endToday;
}

/*获取顶级代理商*/
function top_up_proxy($top_proxy){
	$info='';
	$where['proxy_id']=$top_proxy;
	$where['status']=1;
	$where['approve_status']=1;
	$res = M("proxy")->where($where)->field("proxy_name,top_proxy_id,proxy_level")->find();
	if($res['proxy_level']==1 || $res['proxy_level']==0 ){
		return $res['proxy_name'];
	}else{
		return top_up_proxy($res['top_proxy_id']);
	}
}

/*获取顶级代理商信息*/
function top_up_proxy2($top_proxy){
	if(empty($_SESSION['proxy'])){
		$where['status']=1;
		$where['approve_status']=1;
		$resall = M("proxy")->where($where)->field("proxy_id,proxy_name,top_proxy_id,proxy_level")->select();
		$_SESSION['proxy'] = $resall;
		unset($resall);
	}
	if(!empty($_SESSION['proxy'])){
		foreach($_SESSION['proxy'] as $v){
			if($v['proxy_id']==$top_proxy){
				$info = $v;
				break;
			}
		}
		if($info['proxy_level']==1 || $info['proxy_level']==0 ){
			$data=$info['proxy_name'];
			return $data;
		}else{
			return top_up_proxy($info['top_proxy_id']);
		}
	}
	return "";
}

/*借款申请单未还款金额 */
function  last_money($id,$type=''){
	$where['loan_id']=$id;
	if($type){
		$list =M('proxy_loan')->field('loan_money,repayment_money')->where($where)->find();
	}else{
		$list =M('enterprise_loan')->field('loan_money,repayment_money')->where($where)->find();
	}

	//$where['approve_status']=3;
	//$sum= M('enterprise_repaymen')->where($where)->sum('repayment_money');
	$last_money=bcsub($list['loan_money'], $list['repayment_money'], 3);
	return $last_money;
}

//显示十折的折扣数据（原始数据0~1之间）
function show_discount_ten($dis_one) {
	$rt = empty($dis_one) ? 0 : $dis_one * 10;
	return sprintf("%.2f", $rt);
}

function money_round($num){
	return round($num, 2);
}

//判断是否有角色的限制：true被限制、false不被限制
function is_role_right(){
	return false;
	/*
	$limit_rights = C('RBAC_ROLE');
	$user_rights = D('SysRole')->get_the_user_roles();
	$result_right = array_intersect($user_rights,$limit_rights);
	//如果限制角色里含有该用户的角色，则没有权限
	if(empty($result_right)){
		return false;
	}else{
		return true;
	}
	*/
}

/**
 * 发送提醒短信
 */
function send_sms($mobile, $content) {
	$username = 'steoc';
	$pwd = 'xm70j04b';
	$password = md5($username."".md5($pwd));
	//$mobile = "13576111111";
	//$content = "您的验证码是：123456【企业签名】";
	$url = "http://120.55.248.18/smsSend.do?";

	$param = http_build_query(
			array(
					'username'	=> $username,
					'password'	=> $password,
					'mobile'	=> $mobile,
					//'content'	=> iconv("GB2312","UTF-8",$content),
					'content'	=> $content,
					'ext'		=> '02'
			)
	);

	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_HEADER,0);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$param);
	$result = curl_exec($ch);
	curl_close($ch);

	return $result;
}

//读取通道编码
function select_channel($channel_id){
    if(empty($_SESSION['channel'])){
        $channel = M("channel")->field("channel_id,channel_code")->select();
        $_SESSION['channel'] = $channel;
    }
    if(!empty($_SESSION['channel'])){
        foreach($_SESSION['channel'] as $ch){
            if($ch['channel_id']==$channel_id){
                $info = $ch['channel_code'];
				break;
            }
        }
        return $info;
    }
    return '';
}

//读取通道产品
function select_channel_product($product_id){
    if(!empty($_SESSION['channel_product'])){
        foreach($_SESSION['channel_product'] as $cp){
            if($cp['product_id']==$product_id){
                $info = $cp['product_name'];
				break;
            }
        }
    }
    if($info!=""){
        return $info;
    }else{
        $channelproduct = M("channel_product")->where(array('product_id'=>$product_id))->field("product_id,product_name")->find();
        $_SESSION['channel_product'][]=$channelproduct;
        return $channelproduct['product_name'];
    }
}

/**
 * 域名状态
 */
function get_domain_status($st_id){
    $status = array("0"=>"等待审核","1"=>"初审通过","2"=>"初审驳回","3"=>"复审通过","4"=>"复审驳回");
    return $status[$st_id];
}

/**
 * 域名状态
 * 发票状态（1：草稿、2：等待审核、3：初审通过、4：初审驳回、5：复审通过、6：复审驳回、7：已开票、8：开票驳回）
 */
function get_ticket_status($st_id){
	$status = array("1"=>"草稿","2"=>"等待审核","3"=>"初审通过","4"=>"初审驳回","5"=>"复审通过","6"=>"复审驳回","7"=>"已开票","8"=>"开票驳回");
	return $status[$st_id];
}

/**
 * 分割字符串
 * 以第一个逗号分割
 */
function get_substr($str){
	$str = str_replace(",", "，", $str);
	$str = str_replace("，", ",", $str);
	$str = substr( $str , strpos($str,',')+1 , strlen($str)-strpos($str,',')-1);
	$str = str_replace(",", "，", $str);
	return $str;
}

/**
 * 资源方所使用的所有状态信息
 *
 */
//连接类型
function connect_type($type=NULL){
	$types = array("1"=>"直连运营商","2"=>"第三方公司");
	if($type==""){
		return $types;
	}
	return $types[$type];
}

//资源类型
function resources_type($type=NULL){
	$resources_type = array("1"=>"省内号码省内流量","2"=>"省内号码全国流量","3"=>"全国号码全国流量");
	if($type==""){
		return $resources_type;
	}
	return $resources_type[$type];
}

//洽谈状态
function negotiate_status($status=NULL){
	$negotiate_status = array("1"=>"等待洽谈","2"=>"洽谈中","3"=>"走合同中","4"=>"合作中","5"=>"复通中","6"=>"停止合作");
	if($status==""){
		return $negotiate_status;
	}
	return $negotiate_status[$status];
}
//通道状态
function channel_status($status=NULL){
	$channel_status = array("1"=>"等待对接","2"=>"测试中","3"=>"已开通","4"=>"已关闭");
	if($status==""){
		return $channel_status;
	}
	return $channel_status[$status];
}
//落地公司
function fall_company($name=NULL){
	$fall_company = array("1"=>"江西尚通","2"=>"广东尚通","3"=>"广东尚云","4"=>"深圳诚汇赢","5"=>"深圳赢通","6"=>"深圳真辉映","7"=>"北京达通","8"=>"君诚科技","9"=>"石家庄星桥","10"=>"深圳凌沃","11"=>"深圳诚立业","12"=>"江西锋潮","13"=>"江西欧创","14"=>"江西朗迪","15"=>"北京百兆");
	if($name==""){
		return $fall_company;
	}
	return $fall_company[$name];
}

//跟进记录回访方式
function visit_type($type=NULL){
	$visit_type = array("1"=>"电话","2"=>"面谈","3"=>"邮件","4"=>"其他");
	if($type==""){
		return $visit_type;
	}
	return $visit_type[$type];
}

//获取省份名称
function get_province_name($province_id){
	$province_info = M('sys_province')->where(array('province_id'=>$province_id))->find();
	return $province_info['province_name'];
}

//获取市名称
function get_city_name($city_id){
	$province_info = M('sys_city')->where(array('city_id'=>$city_id))->find();
	return $province_info['city_name'];
}

/**
 * 通过市读取市名称
 * @$city_id => 市ID
 * @$province_id => 省ID（市ID为空，省ID必须）
 */
function get_city_province_name($city_id,$province_id=NULL){
	//读取所以省信息写入session
	$province_all = $_SESSION['province_all'];
	if($province_id==1){
		return "全国";
	}
	if(!$province_all){
		$province_all = M('sys_province')->select();
		$_SESSION['province_all'] = $province_all;
	}
	//读取当前市的省ID
	if($city_id){
		$city_info = M('sys_city')->where(array('city_id'=>$city_id))->field('province_id')->find();
	}
	if(!$city_info['province_id']){
		$city_info['province_id'] = $province_id;
	}
	foreach($province_all as $v){
		if($v['province_id']==$city_info['province_id']){
			$province_name = $v['province_name'];
			break;
		}
	}
	return $province_name;
}

/**
 * 金额统一
 * 生成 .000 的金额并格式化
 */
function money_format2($money){
	return number_format($money, 3);
}


/*查找部门*/
function  get_depart_name($id){
	$depart_name=M('sys_depart')->where('depart_id='.$id)->field('depart_name')->find();
	return $depart_name['depart_name'];
}

/*浮点操作 ， 计算已完成充值记录中折扣数，如果$n 有值 则是折后价格  ，没有则正常做浮点操作*/
function  float_operate($num,$n=''){
	if($n){
		$res=($n/$num)*10;
		return sprintf("%1.3f",$res );
	}else{
		return sprintf("%1.3f", $num);
	}

}


	/*已完成充值记录计算成本价格  $cost原价  $discount通道折扣数   $top_rebate_discount返利折扣数*/
	function cost_price($cost,$discount,$top_rebate_discount){
		if(empty($discount)){
			$price=$cost;
		}else{
			$price=$cost*($discount-$top_rebate_discount);
		}

		return sprintf("%1.3f", $price);
	}

/**
 * 开票审核状态
 */
function get_ticket_approve_status($st_id){
	$status = array("1"=>"草稿","2"=>"待审核","3"=>"初审通过","4"=>"初审驳回","5"=>"复审通过","6"=>"复审驳回","7"=>"已开票","8"=>"开票驳回");
	if(empty($st_id)){
		return $status;
	}
	return $status[$st_id];
}

/**
 * 开票状态
 */
function get_ticket_types($st_id){
	$status = array("1"=>"增值税普通发票","2"=>"增值税专用发票");
	if(empty($st_id)){
		return $status;
	}
	return $status[$st_id];
}

/**
 * 开票内容
 */
function get_ticket_contents($st_id){
	$status = array("1"=>"电信增值业务","2"=>"信息服务费");
	if(empty($st_id)){
		return $status;
	}
	return $status[$st_id];
}

/**
 * 自定义回复类型
 */
function get_reply_type($reply_type){
	$list=array("1"=>"文字回复","2"=>"单图片回复","3"=>"多图片回复","4"=>"活动回复");
	if(!empty($reply_type)){
		return $list[$reply_type];
	}else{
		return "";
	}
}

//通过用户创建的活动id 获取活动名称
function get_activity_name($user_activity_id){
	if($user_activity_id==-1){
		return "每日签到";
	}
	$rt=M("scene_user_activity as sua")
		->join("t_flow_scene_activity as sa on sa.activity_id=sua.activity_id","left")
		->where(array("sua.user_activity_id"=>$user_activity_id))
		->field("sa.activity_name,sua.user_activity_name")
		->find();
	if($rt['user_activity_name']){
		return $rt['user_activity_name'];
	}else{
		return $rt['activity_name'];
	}
}

/*
 * 上游开票剩余可开票金额
 * $id  数据id
 *$record 可开票金额 true 为记录的  false 上游开票申请的
 *
 *
 */

function get_can_top_ticke_money($id,$record=false){
	if($record){ //上游开票记录的可开票金额
		$where['record_id']=$id;
		$list =M('top_ticke_record')->where($where)->find();
		$not_ticke_money=(($list['ticket_money']*1000)-($list['operater_before_money']*1000))/1000;
	}else{ //上游开票申请的可开票金额
		$where['ticke_id']=$id;
		$list =M('top_ticke')->where($where)->find();
		$not_ticke_money=(($list['ticket_money']*1000)-($list['cumulative']*1000))/1000;
	}

	return money_format2($not_ticke_money);
}

/**
 * 通道类型
 */
function get_platform_lists($id){
	$status = array("1"=>"php端","2"=>"java端");
	if(empty($id)){
		return $status;
	}
	return $status[$id];
}

/**
 * 通道属性
 */
function get_attribute_lists($id){
	$status = array("1"=>"普通通道","2"=>"流量池通道");
	if(empty($id)){
		return $status;
	}
	return $status[$id];
}

/*企业充值管理获取复审人*/
function get_approve_people($apply_id,$type){
    $where['apply_id']=$apply_id;
	$where['approve_stage']=2;
	if($type==2){ //企业复审人
		$list=M('enterprise_recharge_process')->field('approve_user_id')->where($where)->order('approve_date desc')->find();
	}else{
		//1 代理商复审人
		$list=M('proxy_recharge_process')->field('approve_user_id')->where($where)->order('approve_date desc')->find();
	}

	return  get_user_name($list['approve_user_id']);
}

function proxy_code_list($proxy_id){
	if(empty($_SESSION['all_proxy_code'])){
		$where['status']=1;
		$where['approve_status']=1;
		$os_proxy_ids = D('SysUser')->self_proxy_id().','.D('Proxy')->proxy_child_ids();
		$where['proxy_id']=array('in',$os_proxy_ids);
		$resall = M("proxy")->where($where)->field("proxy_id as id,proxy_code as code")->select();
		$_SESSION['all_proxy_code'] = $resall;
		unset($resall);
	}
	if(!empty($_SESSION['all_proxy_code'])){
		foreach($_SESSION['all_proxy_code'] as $v){
			if($v['id']==$proxy_id){
				$info = $v;
				break;
			}
		}
			return $info['code'];
	}
	return "";
}


function enterprise_code_list($id){
	if(empty($_SESSION['all_enterprise_code'])){
		$where['status']=1;
		$where['approve_status']=1;
		$where['enterprise_id']=array('in',D('Enterprise')->enterprise_ids()) ;
		$resall = M("enterprise")->where($where)->field("enterprise_id as id,enterprise_code as code")->select();
		$_SESSION['all_enterprise_code'] = $resall;
		unset($resall);
	}
	if(!empty($_SESSION['all_enterprise_code'])){
		foreach($_SESSION['all_enterprise_code'] as $v){
			if($v['id']==$id){
				$info = $v;
				break;
			}
		}
		return $info['code'];
	}
	return "";
}

//获取网关通道范围
function get_range_name($range){
	$list=array(0=>"全国",1=>"省",3=>"城市");
	return $list[$range];
}
//获取网关类型名称
function get_report_type($report_type){
	$list=array(0=>"没有",1=>"推送",2=>"查询",3=>"推送并查询");
	return $list[$report_type];
}

//营销场景设置没有三网没有的产品
function get_scene_product($operator_id){
	$list=array(
		"1"=>array(""),
		"2"=>array("300"),
		"3"=>array()
	);
	return $list[$operator_id];
}

//流量统计获取代理商消费编码，$stat_price折后价格    $stat_refund_price退款金额
function get_consume_money($stat_price,$stat_refund_price){
	$money=$stat_price-$stat_refund_price;
	 return float_operate($money);
}

//需要时间样式：20160808 =》2016-08-08
function change_record_day($record_day){
	$d=date("Y-m-d",strtotime($record_day));
	return $d;
}

/**
 * 省名称（用户折扣选择省）
 */
function province_list(){
	return array("1"=>"全国","2"=>"北京市","3"=>"天津市","4"=>"上海市","5"=>"重庆市","6"=>"河北省","7"=>"山西省","8"=>"陕西省","9"=>"山东省","10"=>"河南省","11"=>"辽宁省","12"=>"吉林省","13"=>"黑龙江","14"=>"江苏省","15"=>"浙江省","16"=>"安徽省","17"=>"江西省","18"=>"福建省","19"=>"湖北省","20"=>"湖南省","21"=>"四川省","22"=>"贵州省","23"=>"云南省","24"=>"广东省","25"=>"海南省","26"=>"甘肃省","27"=>"青海省","28"=>"台湾省","29"=>"内蒙古","30"=>"新疆省","31"=>"西藏省","32"=>"广西省","33"=>"宁夏省");
}

/**
 *	产品名称获取产品大小
*/
function cs_product_size(){
	$all_product=M("product")->field("product_id,product_name,size")->select();
	foreach ($all_product as $k=>$v){
		$v['size']=substr($v['product_name'],-1)=="G"?substr($v['product_name'],0,strlen($v['product_name'])-1)*1024:substr($v['product_name'],0,strlen($v['product_name'])-1);
		M("product")->where(array("product_id"=>$v['product_id']))->save($v);
	}
}

/**
 *  获取退款原因
 */
function get_refund_cause($refund_cause=null){
	$data=array(1=>"流量未到账",2=>"订单超时",3=>"其他");
	if($refund_cause!=null){
		return $data[$refund_cause];
	}
	return $data;
}

/**
 *  获取流量券状态
 */
function get_flowcode_status($status){
	$data=array(1=>"未激活",2=>"已激活",3=>"已使用",4=>"已作废");
	if($status){
		return $data[$status];
	}
	return $data;
}

/**
 *  获取流量券属性
 */
function get_flowcode_type($type){
	$data=array(1=>"全网通用",2=>"广东省移动",3=>"广东省联通",4=>"广东省电信");
	if($type){
		return $data[$type];
	}
	return $data;
}
function get_flowcode_operator($type){
	$data=array(1=>"全网通用",2=>"广东省移动",3=>"广东省联通",4=>"广东省电信");
	$op=$data[$type];
	$str=mb_substr($op,-2,2,"utf-8");
	$operator_id=null;
	switch ($str){
		case "移动":
			$operator_id=1;
			break;
		case "联通":
			$operator_id=2;
			break;
		case "电信":
			$operator_id=3;
			break;
	}
	return $operator_id;
}

/**
 *  预设流量码可用流量包
 */
function get_flowcode_product(){
	$data=array(1=>"100M",2=>"500M",3=>"1G",4=>"2G",5=>"3G");
	return $data;
}

/**
 * 代理商、企业结算单专用
 * 生成Excel文件(方法很好用)
 * $title           => excel名称
 * $info      		=> 代理商、企业信息
 * $list    		=> 结算信息
 * $arr             => 三种运营商合计
 * $date            => 查询时间
 */
	function StatementsExportEexcel($title,$info,$list,$arr,$date){
	set_time_limit(0);
	ini_set('memory_limit', '1024M');
	header("Content-type:application/vnd.ms-excel");
	header("Content-Disposition:attachment;filename=".$title.date("Y_m_d", time()).".xls");
	$html = '';
	$html .='<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"> 
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
    <html> 
    <head> 
        <meta http-equiv="Content-type" content="text/html;charset=UTF-8" /> 
    </head> 
        <body> 
            <div align=center x:publishsource="Excel"> 
                <table x:str border=1 cellpadding=0 cellspacing=0 style="font-size: 18px;">';
	$html .='<tr><td colspan="5" align="center" style="font-size: 20px;">业务对账单</td></tr>';
	$html .='<tr><td colspan="5" align="center" style="background-color:#bfbfbf;">客户信息</td></tr>';
	$html .='<tr><td colspan="2">客户名称</td><td colspan="3" align="center">'.($info['proxy_name']?$info['proxy_name']:$info['enterprise_name']).'</td></tr>';
	$html .='<tr><td colspan="2">客户地址</td><td colspan="3" align="center">'.$info['address'].'</td></tr>';
	$html .='<tr><td colspan="2">客户联系人</td><td colspan="3" align="center">'.$info['contact_name'].'</td></tr>';
	$html .='<tr><td colspan="2">联系电话</td><td colspan="3" align="center">'.$info['contact_tel'].'</td></tr>';
	$html .='<tr><td colspan="2">结算日期</td><td colspan="3" align="center">'.$date.'</td></tr>';
	$html .='<tr><td colspan="2">账单金额总计</td><td colspan="3" align="center">'.$arr[4].'</td></tr>';
	$html .='<tr><td colspan="5" align="center" style="background-color:#bfbfbf;">账单明细</td></tr>';
	$i=1;
	while($i<4){
		if(!$arr[$i]){
			$i++;
			continue;
		}
		switch ($i){
			case 1:
				$html .='<tr><td colspan="5"  style="background-color:#b8cce4;">中国移动</td></tr>';
				break;
			case 2:
				$html .='<tr><td colspan="5"  style="background-color:#b8cce4;">中国联通</td></tr>';
				break;
			case 3:
				$html .='<tr><td colspan="5"  style="background-color:#b8cce4;">中国电信</td></tr>';
		}
		$html .='<tr><td style=" width:160px; background-color:#b8cce4;">产品名称</td><td style=" width:160px;background-color:#b8cce4;">数量(个)</td><td style=" width:160px;background-color:#b8cce4;">产品定价(元)</td><td style="width:160px;background-color:#b8cce4;">结算价格(元)</td><td style=" width:160px;background-color:#b8cce4;">小计(元)</td>';
		foreach($list as $t){
			if($t['operator_id']==$i){
				$html .='<tr>';
				$html .= '<td>'.$t['product_name'].'</td>';
				$html .= '<td>'.$t['counts'].'</td>';
				$html .= '<td>'.$t['bprice'].'</td>';
				$html .= '<td>'.$t['dprice'].'</td>';
				$html .= '<td>'.$t['allprice'].'</td>';
				$html .='</tr>';
			}
		}
		$html .= '<tr><td colspan="3" align="center">小计</td><td colspan="2">'.$arr[$i].'</td></tr>';
		$i++;
	}
	$html .='<tr><td colspan="3" align="center">账单金额总计</td><td colspan="2">'.$arr[4].'</td></tr>';
	$html .='</table>
            </div> 
        </body> 
    </html>';
	echo $html;
	exit;
}
function get_order_range(){
	$arr=array(
			array("range_id"=>"-1","range_name"=>"全部"),
			array("range_id"=>"0","range_name"=>"全国"),
			array("range_id"=>"1","range_name"=>"省份")
	);
	return $arr;
}

/**
 * 通道重要程度
 * 1：最重要
 * 以大写英文字母做标识
 */
function channel_importance(){
	return array(1=>'A级',2=>'B级',3=>'C级');
}
/**
 * 借款中打款状态
 */
function get_pay_money($is_pay_money){
	$data=array("1"=>"已打款","2"=>"未打款","0"=>"其他");
	if(!empty($is_pay_money)){
		return $data[$is_pay_money];
	}
	if($is_pay_money==="0"){
		return $data['0'];
	}
	return $data;
}

function get_sence_type($type){
	$data=array(1=>"点击客户量",2=>"输入号码量",3=>"选择包型量",4=>"点击支付量","完成支付量");
	if(!empty($type)){
		return $data[$type];
	}
	return $data;
}

function get_img($type){
	$data=array(
		array("img_url"=>"/Public/Uploads/./Enterprise_scene/2016-05-10/232cee0aceab9.png","img_name"=>"默认","status"=>""),
		array("img_url"=>"/Public/Uploads/./Enterprise_scene/2016-05-10/valentineday.jpg","img_name"=>"情人节","status"=>""),
		array("img_url"=>"/Public/Uploads/./Enterprise_scene/2016-05-10/laborday.jpg","img_name"=>"劳动节","status"=>""),
		array("img_url"=>"/Public/Uploads/./Enterprise_scene/2016-05-10/dragonboatfestival.jpg","img_name"=>"端午节","status"=>""),
		array("img_url"=>"/Public/Uploads/./Enterprise_scene/2016-05-10/childrenday.jpg","img_name"=>"儿童节","status"=>""),
		array("img_url"=>"/Public/Uploads/./Enterprise_scene/2016-05-10/nationalday.jpg","img_name"=>"国庆节","status"=>"")
	);
	if($type){
		return $data[$type-1]["img_url"];
	}else{
		return $data;
	}
}

/**
 *  生成指定长度的随机字符串(包含大写英文字母, 小写英文字母, 数字)
 *
 * @author Wu Junwei <www.wujunwei.net>
 *
 * @param int $length 需要生成的字符串的长度
 * @return string 包含 大小写英文字母 和 数字 的随机字符串
 */
function random_str($length)
{
	$str1 = '0123456789';
	$str2 = 'abcdefghijklmnopqrstuvwxyz';
	$str3 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	$count1 = rand(1,3);
	$pass1 = substr(str_shuffle($str1),0,$count1);

	$count2 = rand(1,3);
	$pass2 = substr(str_shuffle($str2),0,$count2);

	$count3 = $length - $count1 - $count2;
	$pass3 = substr(str_shuffle($str3),0,$count3);
	$password = str_shuffle($pass1.$pass2.$pass3);

	return $password;
}

/**
 *  判断字符串是否包含大写英文字母, 小写英文字母, 数字
 *
 */
function check_random_str($password, $length=8){
	if(preg_match('/\d/is', $password) && preg_match('/[a-z]+/', $password) && preg_match('/[A-Z]+/', $password) && strlen($password)>=$length){
		return true;
	}else{
		return false;
	}
}


/**
 * 开票审核状态
 */
function get_difference_approve_status($st_id){
	$status = array("1"=>"草稿","2"=>"待审核","3"=>"初审通过","4"=>"初审驳回","5"=>"复审通过","6"=>"复审驳回","7"=>"终审通过","8"=>"终审驳回");
	if(empty($st_id)){
		return $status;
	}
	return $status[$st_id];
}

/**
 * 场景用加密 by lv
 */
function localencode($data) {
    $string = "";
    for($i=0;$i<strlen($data);$i++){
        $ord = ord($data[$i]);
        $ord += 20;
        $string = $string.chr($ord);
    }
    $data = base64_encode($string);
    return $data;
}


/**
 * 场景用解密 by lv
 */
function localdecode($data) {
	$data = base64_decode($data);
	for ($i = 0; $i < strlen($data); $i++) {
		$ord = ord($data[$i]);
		$ord -= 20;
		$string = $string . chr($ord);
	}
	return $string;
}

/**
 * obj转换为数组 by lv
 */
function object_array($array) {  
        if(is_object($array)) {  
            $array = (array)$array;  
        } if(is_array($array)) {  
            foreach($array as $key=>$value) {  
                	$array[$key] = object_array($value);  
                }  
        }  
        return $array;
}
//判断两个日期是否在一个月内
function same_month($d1,$d2){
	if(!empty($d1) && !empty($d2)){
		$t1=getdate($d1);
		$t2=getdate($d2);
		if($t1['year']==$t2['year'] && $t1['mon']==$t2['mon']){
			return ture;
		}else{
			return false;
		}
	}else{
			return false;
	}
	
}
//获取日期的年月
function get_year_or_month($d,$v="Y"){
		$t=getdate($d);
		if($v=="Y"){
			return $t['year'];
		}else{
			if($t['mon']<10){
                return "0".$t['mon'];
			}else{
				return $t['mon'];
			}
			
		}
	
}
//分表查询日期结点
function date_node($y,$m){
	if($y==2016 && $m>=4){
		return true;
	}else{
		return false;
	}
}