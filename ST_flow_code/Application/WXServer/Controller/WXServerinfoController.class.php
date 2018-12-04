<?php
namespace WXServer\Controller;
use Think\Controller;

require "WXReceiveText.php";

class WXServerinfoController extends Controller {
	
	public function _initialize() 
	{
		Vendor("WXSdk.WXBizMsgCrypt");
	}

    //获取用户token
	private function getcomponent_token()
    {
        $fp = fopen("componentverifyticket.json","r");
		$jsonticket = fread($fp,2000);
		fclose($fp);
        //2491
        //签到时间范围。
        //活动预算。
        //流量兑换

        $jsonobj = json_decode($jsonticket,true);
        //每10分钟更新一次的ticket
        $ticket = $jsonobj["ticket"];
        //申请的token
        $component_access_token = $jsonobj["component_access_token"];
        //token时间
        $expires_time = $jsonobj["expires_time"];
        //有效期7200秒
        //现在时间
        $nowtime = time();
        //http://test.liuliang.net.cn/index.php/WXServer/WXServerinfo/adduser
        if($nowtime - $expires_time > 7200 || empty($component_access_token))
        {
            $APPSECRET = C("open_AppSecret");
            $APPID = C("open_AppID");
            //获取ticketcode
            $submiturl = "https://api.weixin.qq.com/cgi-bin/component/api_component_token";
            $pd = array('component_appid'=>$APPID,'component_appsecret'=>$APPSECRET,'component_verify_ticket'=>$ticket);
            $json = json_encode($pd);
            $rt = https_request($submiturl,$json);
            $obj = json_decode($rt,true); 
            $component_access_token = $obj['component_access_token'];
            $jsoninfo = array();
            //每10分钟更新一次的ticket
            $jsoninfo["ticket"] = $ticket;
            //每10分钟更新一次的ticket
            $jsoninfo["expires_time"] = $nowtime;
            $jsoninfo["component_access_token"] = $component_access_token;
            //转换为json字符串存入
            $jsonstr = json_encode($jsoninfo);

            $fp = fopen("componentverifyticket.json","w");
            fwrite($fp, $jsonstr);
            fclose($fp);
        }
        return $component_access_token;
    }
	
    //获取授权页面
	public function adduser()
	{
        //获取token
        $component_access_token = $this->getcomponent_token();

		$APPID = C("open_AppID");
		//获取pre_auth_code授权码
		$submiturl = "https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=".$component_access_token;
		$codepd["component_appid"] = $APPID;
		$json = json_encode($codepd);
		$rt = https_request($submiturl,$json);
		$obj = json_decode($rt,true); 
		$pre_auth_code = $obj['pre_auth_code'];
//		第三方平台方可以在自己的网站:中放置“微信公众号授权”的入口，引导公众号运营者进入授权页。授权页网址为
//		https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=xxxx&pre_auth_code=xxxxx&redirect_uri=xxxx，
//		该网址中第三方平台方需要提供第三方平台方appid、预授权码和回调URI
	     //$redirect_uri='http://'.$_SERVER['HTTP_HOST']."index.php/WXServer/WXServerinfo/callback";
	    $redirect_uri= gethostwithhttp()."/index.php/Admin/Authorize/index";
		// $this->assign("APPID",$APPID);
		// $this->assign("pre_auth_code",$pre_auth_code);
		// $this->assign("redirect_uri",$redirect_uri);
		// $this->display("authorization/index");

		$submiturl = "https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=".$APPID."&pre_auth_code=".$pre_auth_code."&redirect_uri=".$redirect_uri;
	
        echo "<script language='javascript' type='text/javascript'> window.location='".$submiturl."';</script>";  
		//https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=xxxx
	}
	
    //授权回调页面
	public function callback()
	{
        $auth_code =  $_GET['auth_code'];

		$rsaKey = $_SERVER["QUERY_STRING"];

	    // $fp = fopen("access_token.json","a");
		// fwrite($fp, $rsaKey);
	    // fclose($fp);
        // var_dump($rsaKey);
        // https://mp.weixin.qq.com/cgi-bin/www.baidu.com?auth_code=queryauthcode@@@zC8f7f2WqxtBb5x93Oe7gnHaRXzfJKQZSVGGIfgjsdMIDalzWoY2uxvzlp53vogXvqkp0oNqWrfVH3oeg72j4Q&expires_in=3600





	    $oldurl='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		//component
        //https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=xxxx
		$submiturl = "https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=".$component_access_token;
		$componentpd["component_appid"] = $APPID;
		$componentpd["authorization_code"] = $pre_auth_code;
		$rt = https_request($submiturl,$componentpd);
		$obj = json_decode($rt,true); 
		echo "success";
    }
    
    //每十分钟更新一次的ticket
	public function componentverifyticket()
	{
		$msg = $this->msgcomplate();
		if(!empty($msg))
		{
            $postObj = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
            $AUTH_TYPE = trim($postObj->InfoType);



            switch ($AUTH_TYPE) {
                case 'component_verify_ticket':
                {
                    //保存ticket
                    //返回的消息
                    // $xml = new  \DOMDocument;
                    // $xml->loadXML($msg);
                    // $array_e = $xml->getElementsByTagName('ComponentVerifyTicket');
                    // $component_verify_ticket = $array_e->item(0)->nodeValue;

                    //msg = <xml><AppId><![CDATA[wxde416d584bca0e10]]></AppId>
                    //<CreateTime>1465871547</CreateTime>
                    //<InfoType><![CDATA[component_verify_ticket]]></InfoType>
                    //<ComponentVerifyTicket><![CDATA[ticket@@@DNk-HdP2I8T-tXYwyEdkCXKC7cZjq1f6IqlrtVf5jIgN8ZKpcm8rJ6nEtT8qXuU93dlLFI-K4EYb-aQ14_UJRw]]></ComponentVerifyTicket>
                    //</xml>
                    $component_verify_ticket = trim($postObj->ComponentVerifyTicket);
                    //读取出文件中的内容
                    $fp = fopen("componentverifyticket.json","r+");
                    $jsonticket = fread($fp,2000);
                    fclose($fp);

                    //解析出json对象
                    $jsonobj = json_decode($jsonticket,true);

                    $jsoninfo = array();
                    //每10分钟更新一次的ticket
                    $jsoninfo["ticket"] = $component_verify_ticket;
                    $jsoninfo["expires_time"] = $jsonobj["expires_time"];
                    $jsoninfo["component_access_token"] = $jsonobj["component_access_token"];
                    //转换为json字符串存入
                    $jsonstr = json_encode($jsoninfo);
                    $fp = fopen("componentverifyticket.json","w+");
                    fwrite($fp, $jsonstr);
                    fclose($fp);
                }
                break;
                case 'unauthorized':
                //取消授权
                {
                    //授权公众号appid
                    $AuthorizerAppid = $postObj->AuthorizerAppid;
                    //API授权码
                    $AuthorizationCode = $postObj->AuthorizationCode; 
                    $this->unauthinfo($AuthorizerAppid);
                }
                break;
                case 'authorized':
                 //授权成功将授权信息存入数据库
                {

                    //授权公众号appid
                    $AuthorizerAppid = $postObj->AuthorizerAppid;
                    //API授权码
                    $AuthorizationCode = $postObj->AuthorizationCode; 
                    //将appid和授权码填入数据库
                    $this->authinfo($AuthorizerAppid,$AuthorizationCode);
                }
                break;
                case 'updateauthorized':
                //更新授权
                {
                    //授权公众号appid
                    $AuthorizerAppid = $postObj->AuthorizerAppid;
                    //API授权码
                    $AuthorizationCode = $postObj->AuthorizationCode; 
                    $this->authinfo($AuthorizerAppid,$AuthorizationCode);
                }
                break;
                default:
                     # code...
                break;
             }
	        echo "success";
        }
        else
        {
	        echo "false";
        }
    }

    //取消授权
	public function unauthinfo($authorizer_appid)
    {
        //第三方平台appid
		$APPID = C("open_AppID");
        $authorizer_appid = $authorizer_appid."";
        $map['auth_appid']=$authorizer_appid;
        //关闭授权
	    $data['auth_status'] = 0;
		M("wxs_auth")->where($map)->save($data);

    }

    //使用授权码换取公众号的接口调用凭据和授权信息
	public function authinfo($authorizer_appid,$authorizer_code = 0)
    {
        //授权方appid
        //$authorizer_appid = "";
        //第三方平台appid
		$APPID = C("open_AppID");

        $component_access_token = $this->getcomponent_token();


        $authorizer_appid = $authorizer_appid."";

        $submiturl = "https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=".$component_access_token;
        $componentpd["component_appid"] = $APPID;
        $componentpd["authorizer_appid"] = $authorizer_appid;
        $json = json_encode($componentpd);
        $rt = https_request($submiturl,$json);
        $obj = json_decode($rt,true); 

 

        $authorizer_info = $obj["authorizer_info"];
        //公众号名称
        $nick_name = $authorizer_info["nick_name"];
        //公众号头像
        $head_img = $authorizer_info["head_img"];
        //授权方公众号类型，0代表订阅号，1代表由历史老帐号升级后的订阅号，2代表服务号
        $service_type_info = $authorizer_info["service_type_info"]["id"];
        //授权方公众号所设置的微信号，可能为空
        $wxname = $authorizer_info["alias"];
        //是否开通了微信支付功能
        $business_info = $authorizer_info["business_info"];
        $open_pay = $business_info["open_pay"];

	    $fp = fopen("access_token.json","a");
		fwrite($fp, json_encode($authorizer_info));
	    fclose($fp);

        $map['active_appid']=$authorizer_appid;
        $sinfo=M("SceneInfo")->where($map)->find();//读出活动的信息s

		$user_type = $sinfo['user_type'];
        $where['user_type']=$user_type;
        $data['user_type']=$user_type;
        if($user_type==1)
        {
            $user_id=$sinfo['proxy_id'];
        	$where['proxy_id']=$user_id;
        	$data['proxy_id']=$user_id;
        	$data['enterprise_id']=0;
        }else{
            $user_id=$sinfo['enterprise_id'];
        	$where['enterprise_id']=$user_id;
        	$data['enterprise_id']=$user_id;
        	$data['proxy_id']=0;
        }
        //将授权信息存入表
		$info = M("wxs_auth")->where($where)->find();
        $nowtime = date("Y-m-d H:i:s", time());
		if(!$info){
	        $data['auth_status'] = 1;
	        $data['auth_nickname']=$nick_name;
	        $data['auth_headimg']=$head_img;
	        $data['auth_service_type']=$service_type_info;
	        $data['auth_wxname']=$wxname;
	        $data['auth_businesspay']=$open_pay;
	        $data['auth_appid']=$authorizer_appid;
	        $data['auth_code']=$authorizer_code;
	        $data['create_date']=$nowtime;
	        if(!M("wxs_auth")->add($data)){
	        		return "";	
			}
		}
        else
        {
            //更新数据
	        $data['auth_status'] = 1;
	        $data['auth_nickname']=$nick_name;
	        $data['auth_headimg']=$head_img;
	        $data['auth_service_type']=$service_type_info;
	        $data['auth_wxname']=$wxname;
	        $data['auth_businesspay']=$open_pay;
	        $data['auth_appid']=$authorizer_appid;
	        $data['auth_code']=$authorizer_code;
	        $data['create_date']=$nowtime;
			M("wxs_auth")->where($where)->save($data);
        }
    }

	//正对第三方公众平台进行加密处理
	private function msgcomplate()
	{
		$encryptMsg = $GLOBALS["HTTP_RAW_POST_DATA"];
		$timeStamp  = empty($_GET['timestamp'])     ? ""    : trim($_GET['timestamp']) ;
		$nonce      = empty($_GET['nonce'])     ? ""    : trim($_GET['nonce']) ;
		$msg_sign   = empty($_GET['msg_signature']) ? ""    : trim($_GET['msg_signature']) ;
		
		
		$pc = new  \WXBizMsgCrypt(C("Token"), C("EncodingAesKey"), C("open_AppID"));
        $xml_tree = new \DOMDocument;
        $xml_tree->loadXML($encryptMsg);
		$array_e = $xml_tree->getElementsByTagName('Encrypt');
        $encrypt = $array_e->item(0)->nodeValue;
		
		
        $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
        $from_xml = sprintf($format, $encrypt);
        $msg = '';
//      $timeStamp = time();
//		$nonce = this->createNonceStr();
        $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg);
		return $msg;
	}

    //接收消息
	public function callbackappid()
	{
		//$tools = new WXServerController();
		$this->responseMsg();
	}
    
    //处理接收到的消息
	public function responseMsg()
    {
        $postStr = $this->msgcomplate();
                
        if (!empty($postStr)){

            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
             switch ($RX_TYPE)
            {
                    case "text":
                        $resultStr = $this->receiveText($postObj);
                        break;
                    case "image":
                        $resultStr = $this->receiveImage($postObj);
                        break;
                    case "location":
                        $resultStr = $this->receiveLocation($postObj);
                        break;
                    case "voice":
                        $resultStr = $this->receiveVoice($postObj);
                        break;
                    case "video":
                        
                        $fp = fopen("access_token.json","a");
                        fwrite($fp, json_encode($postStr));
                        fclose($fp);
                
                        $resultStr = $this->receiveVideo($postObj);
                        break;
                    case "link":
                        $resultStr = $this->receiveLink($postObj);
                        break;
                    case "event":
                        $resultStr = $this->receiveEvent($postObj);
                        break;
             }	
                //将消息封装后发出
            $pc = new  \WXBizMsgCrypt(C("Token"), C("EncodingAesKey"), C("open_AppID"));
            $timeStamp = time();
            $nonce = $this->createNonceStr();
            $encryptMsg = "";
            $errCode = $pc->encryptMsg($resultStr, $timeStamp, $nonce, $encryptMsg);
            echo $encryptMsg;


		}
		else 
		{
            //如果接受到的是授权信息。
			echo "";
			exit;
		}   
    }

    //生成随机字符串
    private function createNonceStr($length = 16) {
	    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	    $str = "";
	    for ($i = 0; $i < $length; $i++) {
	      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	    }
	    return $str;
	}

    //处理文字消息
    private function receiveText($object)
    {
        $funcFlag = 0;
		if(strpos($object->Content, "天气"))
		{
			 //如果字符串中包含天气字样s
			include("WXWeatherController.php");
			$tools = new WXWeatherController();
			$city = str_replace("天气","",$object->Content);	
			$contentArray = $tools->getWeatherInfo($city);
			$resultStr = $this->transmitNews($object, $contentArray);
		}
        else
        {
            //活动本身数据控制器 
			$tools = new WXReceiveText();
			$contentStr = $tools->receiveText($object);

            // if(empty($contentStr))
            // {
            //     include("WXRtulingController.php");
            //     $tools = new WXRtulingController();
            //     $contentStr = $tools->receiveText($object);
            // }

            if(is_string($contentStr))
            {
                //如果为空 则处理反空
                if(empty($contentStr))
                {
                    return "";
                }
                $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
            }
            else
            {
                //如果不是字符串就默认为数组了
			    $resultStr = $this->transmitNews($object, $contentStr);
            }
	    }
        return $resultStr;
    }

    private function receiveImage($object)
    {
        $funcFlag = 0;
        $contentStr = "你发送的是图片，地址为：".$object->PicUrl;
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }

    private function receiveLocation($object)
    {
        $funcFlag = 0;
        $contentStr = "你发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }

    private function receiveVoice($object)
    {
        $funcFlag = 0;
        $contentStr = "你发送的是语音，媒体ID为：".$object->MediaId."翻译后的文字：".Recognition;;
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }

    private function receiveVideo($object)
    {
        $funcFlag = 0;
        $contentStr = "你发送的是视频，媒体ID为：".$object->MediaId;
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }

    private function receiveLink($object)
    {
        $funcFlag = 0;
        $contentStr = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }

	//收到关注事件和取消关注事件
    private function receiveEvent($object)
	{
		$Event = $object->Event;
		if($Event == "subscribe")
		{
			//用户关注事件
       	 	$resultStr = $this->subscribe($object);
		}
		else if($Event == "unsubscribe")
		{
			//用户取消关注事件
		}
        return $resultStr;
	}

	//回复为纯文字
    private function transmitText($object, $content, $flag = 0)
    {
	        $textTpl = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[text]]></MsgType>
			<Content><![CDATA[%s]]></Content>
			<FuncFlag>%d</FuncFlag>
			</xml>";
	        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
				
	        return $resultStr;
    }
	
	//回复为图文
	//Title 标题
	//Description 内容
	//picurl 图片地址
	//url 链接地址
    //多图文模式设计
     private function transmitNews($object, $arr_item)
     {
         if(!is_array($arr_item))
             return;
 
         $itemTpl = "<item>
         <Title><![CDATA[%s]]></Title>
         <Description><![CDATA[%s]]></Description>
         <PicUrl><![CDATA[%s]]></PicUrl>
         <Url><![CDATA[%s]]></Url>
     	 </item> ";
         $item_str = "";
         foreach ($arr_item as $item)
             $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
 
         $newsTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[news]]></MsgType>
					<Content><![CDATA[]]></Content>
					<ArticleCount>%s</ArticleCount>
					<Articles>
					$item_str</Articles>
					</xml>";
 
        $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($arr_item));
        return $result;
    }

    private function transmitImgText($object, $Title,$Description,$picurl,$url, $flag = 0)
    {
	        $textTpl = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[news]]></MsgType>
			<ArticleCount>1</ArticleCount>
		    <Articles>
			    <item>
				<Title><![CDATA[%s]]></Title>
			    <Description><![CDATA[%s]]></Description>
            	<PicUrl><![CDATA[%s]]></PicUrl>
            	<Url><![CDATA[%s]]></Url>
		        </item>
		    </Articles>
			<FuncFlag>%d</FuncFlag>
			</xml>";
	        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(),$Title,$Description,$picurl,$url, $flag);
			
	        return $resultStr;
    }
    //多图文混排模式
 	 private function subscribe($object)
    {

            $APPID   = $_GET["APPID"];
            $map['active_appid'] = $APPID;
            $sinfo=M("SceneInfo")->where($map)->find();//读出活动的信息

            //id和type的信息
            $user_type = $sinfo["user_type"];
            $usermap["user_type"] = $user_type;
            if($user_type==1){
                $user_id = $sinfo["proxy_id"];
                $usermap['proxy_id']=$user_id;
            }else{
                $user_id = $sinfo["enterprise_id"];
                $usermap['enterprise_id']=$user_id;
            }
            // 1是 2否
            $usermap['reply_concern']=1;
            //关键词查找
            $sua = M("wxs_reply")->where($usermap)->find(); //读出活动的而信息

            if(empty($sua))
            {
                //没有找到当前关键词字段
                return "";//$this->transmitmoreinfo($object);
            }
		
            //回复类型（1文字，2图文。3多图文。4活动）
            $reply_type = $sua["reply_type"];
            //回复关键词id
            $reply_keywordid = $sua["reply_keywordid"];
            //活动本身数据控制器 
			$tools = new WXReceiveText();
			$contentStr = $tools->rpkeyword($reply_type,$reply_keywordid);
            $funcFlag = 0;
            $resultStr = "1";
            if(is_string($contentStr))
            {
                $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
            }
            else
            {
                //如果不是字符串就默认为数组了
			    $resultStr = $this->transmitNews($object, $contentStr);
            }
            return $resultStr;
    }
	//多图文混排模式
 	 private function transmitmoreinfo($object, $flag = 0)
    {
	        $textTpl = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>1359011829</CreateTime>
			<MsgType><![CDATA[news]]></MsgType>
			<Content><![CDATA[]]></Content>
			<ArticleCount>5</ArticleCount>
			<Articles>
				<item>
					<Title><![CDATA[【深圳】天气实况 温度：3℃ 湿度：43﹪ 风速：西南风2级]]></Title>
					<Description><![CDATA[]]></Description>
						<PicUrl><![CDATA[http://www.fangbei.org/weixin/weather/icon/banner.jpg]]></PicUrl>
					<Url><![CDATA[]]></Url>
				</item>
				<item>
					<Title><![CDATA[06月24日 周四 2℃~-7℃ 晴 北风3-4级转东南风小于3级]]></Title>
					<Description><![CDATA[]]></Description>
						<PicUrl><![CDATA[http://www.fangbei.org/weixin/weather/icon/d00.gif]]></PicUrl>
					<Url><![CDATA[]]></Url>
				</item>
				<item>
					<Title><![CDATA[06月25日 周五 -1℃~-8℃ 晴 东南风小于3级转东北风3-4级]]></Title>
					<Description><![CDATA[]]></Description>
						<PicUrl><![CDATA[http://www.fangbei.org/weixin/weather/icon/d00.gif]]></PicUrl>
					<Url><![CDATA[]]></Url>
				</item>
				<item>
					<Title><![CDATA[06月26日 周六 -1℃~-7℃ 多云 东北风3-4级转东南风小于3级]]></Title>
					<Description><![CDATA[]]></Description>
						<PicUrl><![CDATA[http://www.fangbei.org/weixin/weather/icon/d01.gif]]></PicUrl>
					<Url><![CDATA[]]></Url>
				</item>
				<item>
					<Title><![CDATA[06月27日 周日 0℃~-6℃ 多云 东南风小于3级转东北风3-4级]]></Title>
					<Description><![CDATA[]]></Description>
						<PicUrl><![CDATA[http://www.fangbei.org/weixin/weather/icon/d01.gif]]></PicUrl>
					<Url><![CDATA[]]></Url>
				</item>
			</Articles>
				<FuncFlag>%d</FuncFlag>
			</xml>";
	        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(),$flag);
			
	        return $resultStr;
    }
}

?>