<?php
namespace WxHome\Controller;
use Think\Controller;



$wechatObj = new WXServerController();
if (isset($_GET['echostr'])) {
    $wechatObj->valid();
}else{
    $wechatObj->responseMsg();
}



class WXServerController extends Controller {
	public function index()
	{
		$role = "/Application/Sdk/View/authorization/";
		$this -> assign("role", $role);
		$this -> display("authorization/index");
	}

	public function valid()
    {
     	$echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
    
	private function checkSignature()
	{
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
   		$nonce = $_GET["nonce"];    
		$token = C("axtoken");
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
	 
	public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            switch ($RX_TYPE)
            {
                case "text":
                    $resultStr = $this->receiveText($postObj);
                    break;
                case "event":
                    $resultStr = $this->receiveEvent($postObj);
                    break;
                default:
                    $resultStr = "unknow msg type: ".$RX_TYPE;
                    break;
            }
            echo $resultStr;
        }else {
            echo "";
            exit;
        }
    }

    private function receiveText($object)
    {
        $funcFlag = 0;

		$Openid = $object->FromUserName;
		$Content = $object->Content;
		//做一次字符串转换（重要）
		$Openid = $Openid."";
        $Content = "12305".$Content;
        //解析出当前行为是否为绑定账号行为
         $Type = strpos($Content, "JCBD") ;//用户输入了绑定命令
         if(!empty($Type))
         {
             $contentStr = "当前用户并没有绑定账户";
             return $this->transmitText($object, $contentStr, $funcFlag);
         }

		//当前是否有账号绑定,为空时。才可以进行绑定账号
         $Type = strpos($Content, "BD") ;//用户输入了绑定命令
         if(!empty($Type))
         {
            $Accountpos = stripos($Content, "#") ;//后面跟着账号
            $Passwordpos = strrpos($Content, "#");//后面跟着密码
            if($Accountpos != 0)
            {
                    $Accountlong = $Passwordpos+1 - $Accountpos-2;//账号截取长度
                    $Account = substr($Content,$Accountpos + 1,$Accountlong);
                    $Password = substr($Content,$Passwordpos+1,strlen($Content));
                    $contentStr = $this->Bindwxinfo($Account, $Password, $Openid);//"输入了账号".$Account."输入了密码".$Password;//
             }
             else
             {
                    $contentStr = "输入绑定账号方式有误,格式为BD#账号#密码";
             }
             return $this->transmitText($object, $contentStr, $funcFlag);
         }
    }

    private function Bindwxinfo($Account , $Password, $Openid)
    {
         //用户想进行绑定操作时。判定该账户是否已绑定
         $map["openid"] = $Openid;
         $sinfo=M("wxs_bindwx")->where($map)->find();//读出活动的信息
         if($sinfo)
         {
             return "用户已绑定了账户";
         }

         //首先通过账号密码查询到相关的账号
         $login_pass = md5($Password);
         $user["login_pass"] = $login_pass;
         $user["login_name_full"] = $Account;
         $userinfo=M("sys_user")->where($user)->find();//读出账号信息
         if(!$userinfo)
         {
             return "对不起，您输入的账号或密码错误";
         }
         //存入用户id
         $user_id = $userinfo["user_id"];
         $data["user_id"] = $user_id;
         
         $user_type = $userinfo["user_type"];
         if($user_type == 1)
         {
             //代理商
            $user_id = $userinfo["proxy_id"];
            $data["enterprise_id"] = 0;
            $data["proxy_id"] = $user_id;
         }
         else
         {
             //企业端
            $user_id = $userinfo["enterprise_id"];
            $data["enterprise_id"] = $user_id;
            $data["proxy_id"] = 0;
         }
	        $data['create_date']=date("Y-m-d H:i:s" ,time());
            $data["openid"] = $Openid;
	        if(!M("wxs_bindwx")->add($data)){
	        	return "服务器维护中，绑定失败";	
			}
            else
            {
                return "账号绑定成功";
            }
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
		else if($Event == "CLICK")
		{
		    	//用户点击菜单事件
	            $Openid = $object->FromUserName;
                //做一次字符串转换（重要）
                $Openid = $Openid."";
                switch ($object->EventKey)
                {
                    case "V1_PTUSER"://绑定账号
                            $sinfo = $this->isbindopenid($Openid);
                            if($sinfo)
                            {
                                $user_type = $sinfo["user_type"];
                                $enterprise["user_type"] = $user_type;
                                if($user_type == 1)
                                {
                                    //代理商
                                    $enterprise["proxy_id"] = $sinfo["proxy_id"];
                                }
                                else
                                {
                                    //企业端
                                    $enterprise["enterprise_id"] = $sinfo["enterprise_id"];
                                }
                                $enterpriseinfo = M("enterprise")->where($enterprise)->find();//读出企业的信息
                                $enterprise_name = $enterpriseinfo["enterprise_name"];
                                //$contentStr = "您好，您已绑定企业\n<a>".$enterprise_name."</a>\n如需解除绑定，请输入JCBD";

                                $contentStr[] = array("Title" =>"流量平台", 
                                "Description" =>$enterprise_name, 
                                "PicUrl" =>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", 
                                "Url" =>"weixin://addfriend/pondbaystudio");
                            }   
                            else
                            {
                               $contentStr = $this->get_bindurl($Openid);
                            }
                        break;
                    default:
                        $contentStr[] = array("Title" =>"默认菜单回复", 
                        "Description" =>"您正在使用的是方倍工作室的自定义菜单测试接口", 
                        "PicUrl" =>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", 
                        "Url" =>"weixin://addfriend/pondbaystudio");
                        break;
                }
		}

        if (is_array($contentStr)){
            $resultStr = $this->transmitNews($object, $contentStr);
        }else{
            $resultStr = $this->transmitText($object, $contentStr);
        }
        return $resultStr;
	}

    private function get_bindurl($Openid)
    {
                             //$contentStr = "当前用户尚未绑定账号，如果需绑定您的企业账号。请输入BD#账号@密码,如果需要解除绑定，即可输入JCBD";
        return "当前用户尚未绑定账号,如需绑定账号。<a href='www.baidu.com'>点击此处绑定您的账号</a>";
    }
    //判断用户是否已绑定了openid
    private function isbindopenid($Openid)
    {
         $map["openid"] = $Openid;
         $sinfo=M("wxs_bindwx")->where($map)->find();//读出活动的信息
         return $sinfo;
    }
	// 回复为图文
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

	
	
    private function transmitText($object, $content, $flag = 0)
    {
        if(empty($content))
            exit();
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
}
?>