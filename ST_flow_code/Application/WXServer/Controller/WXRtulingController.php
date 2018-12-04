<?php
namespace WXServer\Controller;
use Think\Controller;
//图灵机器人
class WXRtulingController{
	 
    public function getinfo()
    {
		$Content = "你好";
        $tulinkey = "375ff24d049ab573b37df24821b79131";
		$submiturl = "http://www.tuling123.com/openapi/api?key=".$tulinkey."&info=".$Content;
		$rt = https_request($submiturl);
		$obj = json_decode($rt,true); 
		$text = $obj['text'];
		var_dump($text);
    }


    public function receiveText($object)
	{
		$Openid = $object->FromUserName;
		$Content = $object->Content;

        $tulinkey = "375ff24d049ab573b37df24821b79131";
		$submiturl = "http://www.tuling123.com/openapi/api?key=".$tulinkey."&info=".$Content."&userid=".$Openid;
		$rt = https_request($submiturl);
		$obj = json_decode($rt,true); 
		$text = $obj['text'];

        return $text;
	}
}

?>