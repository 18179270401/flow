<?php
function verificationId($openid, $userid, $usertype) {

	$map['user_type'] = $usertype;
	if ((int)$usertype == 2) {
		$map['enterprise_id'] = $userid;
	} else {
		$map['proxy_id'] = $userid;
	}
	$map['wx_openid'] = $openid;
	
	
	$data = M('wx_user') -> where($map) -> find();
	if($data)
	{
		return $data;
	}
	else {
		return false;
	}
}
?>