<?php
namespace WxHome\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function _initialize(){
        Vendor("WxPayR.Api");
        Vendor("WxPayR.JsApiPay");
        Vendor("WxPayR.WxPayConfig");
        Vendor('WxPayR.WxPayData');
        Vendor('WxPayR.Exception');
    }
    //加密
	public function localencode($data) {
		for($i=0;$i<strlen($data);$i++){
			$ord = ord($data[$i]);
			$ord += 20;
			$string = $string.chr($ord);
		}
        $string = base64_encode($string);
        return $string;
    }

    //解密
    public function localdecode($data) {
        $data = base64_decode($data);
		for($i=0;$i<strlen($data);$i++){
			$ord = ord($data[$i]);
			$ord -= 20;
			$string = $string.chr($ord);
		}
        return $string;
    }

    public function index(){
        $openid = $this->getopenid();
        $sinfo =  $this->isbindopenid($openid);
        
        if($sinfo)
        {
            //用户已绑定信息
            $user_type = $sinfo["user_type"];
            $enterprise["user_type"] = $user_type;
            //1是运营 2代理 3企业
            if($user_type == 1 || $user_type == 2)
            {
                //运营商
                //运营商
                $enterprise["proxy_id"] = $sinfo["proxy_id"];
                $enterprise_id = $sinfo["proxy_id"];
            }
            else
            {
                //企业端
                $enterprise["enterprise_id"] = $sinfo["enterprise_id"];
                $enterprise_id  = $sinfo["enterprise_id"];
            }
            $enterpriseinfo = M("enterprise")->where($enterprise)->find();//读出企业的信息
            $enterprise_name = $enterpriseinfo["enterprise_name"];
            //获取企业名称
           // var_dump($enterprise_name);

            //放一个企业信息处理
            $user_id = $sinfo["user_id"];

            $role_type = $this->have_app_login_rights($user_id,"APP_CW_RIGHT");

		    $data = $this->localencode($user_id.",".$enterprise_id.",".$user_type.",".$role_type);
            session("enterprise_key", $data);
            
            if($user_type == 3)
            {
                //企业端
                $this->homeindex();
            }
            else
            {
                //运营商端
                $this->ophomeindex();
            }

        }
        else
        {
            //用户没有绑定页面。则进行绑定处理
            $role = "/Application/WxHome/View/Index/";
            $this -> assign("role", $role);

            $url='http://'.$_SERVER['HTTP_HOST']."/index.php/WxHome/index/root";
            $this->assign("url",$url);

            $this->assign("openid",$openid);
            $this -> display("Index/binding");

            
            //$userinfo = $this->getwxuserinfo($openid);

        }
    }

    public function root()
    {
            $openid = $this->getopenid();
            $sinfo =  $this->isbindopenid($openid);
             //用户已绑定信息
            $user_type = $sinfo["user_type"];
            $enterprise["user_type"] = $user_type;
            //1是运营 2代理 3企业
            if($user_type == 1 || $user_type == 2)
            {
                //运营商
                //运营商
                $enterprise["proxy_id"] = $sinfo["proxy_id"];
                $enterprise_id = $sinfo["proxy_id"];
            }
            else
            {
                //企业端
                $enterprise["enterprise_id"] = $sinfo["enterprise_id"];
                $enterprise_id  = $sinfo["enterprise_id"];
            }
            $enterpriseinfo = M("enterprise")->where($enterprise)->find();//读出企业的信息
            $enterprise_name = $enterpriseinfo["enterprise_name"];
            //获取企业名称
           // var_dump($enterprise_name);

            //放一个企业信息处理
            $user_id = $sinfo["user_id"];

            $role_type = $this->have_app_login_rights($user_id,"APP_CW_RIGHT");

		    $data = $this->localencode($user_id.",".$enterprise_id.",".$user_type.",".$role_type);
            session("enterprise_key", $data);
            
            if($user_type == 3)
            {
                //企业端
                $this->homeindex();
            }
            else
            {
                //运营商端
                $this->ophomeindex();
            }
    }

    //权限接口
    private function have_app_login_rights($user_id,$type){
        $type = strtoupper($type);	//将指定名称转大写
        $appoint_role = C($type);	//获取指定信息
        $where['role_id'] = array('in',$appoint_role);
        $where['user_id'] = $user_id;
        $role_id_arr = M('sys_user_role')->field('role_id')->where($where)->select();
        if($user_id == 1 || $role_id_arr){
            return true;
        }else{
            return false;
        }
    }

    public function logout()
    {
        $data = session("enterprise_key");
        $strArray = localdecode($data);
        $InfoArray = explode(",",$strArray);

        $user_id = $InfoArray[0];
        $enterprise_id = $InfoArray[1];
        $user_type = $InfoArray[2];//user_type 1是运营 2代理 3企业
        $sinfo["user_id"] = $user_id;
        // if($user_type == 3)
        // {
        //     $sinfo["enterprise_id"] = $enterprise_id;
        // }
        // else
        // {
        //     $sinfo["proxy_id"] = $enterprise_id;
        // }
        $openid = $this->getopenid();
        $sinfo["openid"] = $openid;
        if(empty($openid) || empty($data))
        {
            //跳转退出登录页面
            $home_url='http://'.$_SERVER['HTTP_HOST']."/index.php/WxHome/index/index";
            echo "<script language='javascript' type='text/javascript'> window.location='{$home_url}';</script>";
            exit();
        }
        M("wxs_bindwx")->where($sinfo)->delete();//删除数据
        session("enterprise_key","");
        //跳转退出登录页面
        $home_url='http://'.$_SERVER['HTTP_HOST']."/index.php/WxHome/index/index";
        echo "<script language='javascript' type='text/javascript'> window.location='{$home_url}';</script>";

    }
    //获取openid
    private function getopenid()
    {
        // return "ceshiqiyeopenid";//
       
		$APPID = C("APPID");//"wxf72845676fe9134b";
		$APPSECRET = C("APPSECRET");//"568832fc8db536b5e3f375af99a218f6";
		$openidkey = "samtonopenidopen".$APPID;
        $openid = cookie($openidkey);
		if(empty($openid))
		{
		    $config=array();
		    $config['APPID'] = $APPID;
		    $config['APPSECRET']=$APPSECRET;
	        $tools = new \JsApiPay($config);
	        $openid = $tools->GetOpenid();
            //如果用户未关注公众号。则进行授权请求

        	cookie($openidkey,$openid);
		}
        return $openid;
    }

    //根据openid获取用户信息，如果没有获取就授权获取
    private function getwxuserinfo($openid)
    {
    
		$APPID = C("APPID");//"wxf72845676fe9134b";
		$APPSECRET = C("APPSECRET");//"568832fc8db536b5e3f375af99a218f6";

		$submiturl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;
		$rt = https_request($submiturl);
		$obj = json_decode($rt,true);
		$accesstoken = $obj['access_token'];


		$submiturl = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$accesstoken."&openid=" .$openid."&lang=zh_CN";
		$retrnrt = https_request($submiturl);
		$retrnrtobj = json_decode($retrnrt,true);
		$subscribe = $retrnrtobj['subscribe'];
		if($subscribe == 1)
		{
	        $newstr = substr($retrnrtobj['headimgurl'],0,strlen($retrnrtobj['headimgurl'])-1);

			$headimgurl = $newstr."64";
			$nickname = $retrnrtobj['nickname'];

		    return array("headimgurl"=>$headimgurl,"nickname"=>$nickname);
		}
        else
        {
			//当用户未关注时。让用户跳转授权页面进行授权
			 $code = $_GET["code"];
            if(empty($code))
            {
                    $redirect_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                    $submiturl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$APPID."&redirect_uri=".$redirect_url."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";

                    echo "<script language='javascript' type='text/javascript'> window.location='{$submiturl}';</script>";
					exit();
            }
                //获取信息之后
            $submiturl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$APPID."&secret=".$APPSECRET."&code=".$code."&grant_type=authorization_code";
            $rt = https_request($submiturl);
            $obj = json_decode($rt,true);
            $accesstoken = $obj['access_token'];
            $openid = $obj['openid'];

            //通过openid获取用户信息
            $submiturl = "https://api.weixin.qq.com/sns/userinfo?access_token=".$accesstoken."&openid=" .$openid."&lang=zh_CN";
            $retrnrt = $this->httpGet($submiturl);
            $retrnrtobj = json_decode($retrnrt,true);
	        $newstr = substr($retrnrtobj['headimgurl'],0,strlen($retrnrtobj['headimgurl'])-1);
			$headimgurl = $newstr."64";
			$nickname = $retrnrtobj['nickname'];

		    return array("headimgurl"=>$headimgurl,"nickname"=>$nickname);
        }
        return array();
    }

    //判断用户是否已绑定了openid
    private function isbindopenid($Openid)
    {
         $map["openid"] = $Openid;
         $sinfo=M("wxs_bindwx")->where($map)->find();//读出活动的信息
         return $sinfo;
    }

    //绑定api
    public function bindapi()
    {
        $openid = trim(I('openid'));
        $password = trim(I('password'));
        $username = trim(I('username'));

        $this->Bindwxinfo($username, $password, $openid);//"输入了账号".$Account."输入了密码".$Password;//
    }

    private function Bindwxinfo($Account , $Password, $Openid)
    {
         //用户想进行绑定操作时。判定该账户是否已绑定
         $map["openid"] = $Openid;
         $sinfo=M("wxs_bindwx")->where($map)->find();//读出活动的信息
         if($sinfo)
         {
            $this->ReturnJson(0,"对不起，您的微信号已绑定了企业，请解绑后才能操作","");
             exit();
         }

         //首先通过账号密码查询到相关的账号
         $login_pass = md5($Password);
         $user["login_pass"] = $login_pass;
         $user["login_name_full"] = $Account;
         $userinfo=M("sys_user")->where($user)->find();//读出账号信息
         if(empty($userinfo))
         {
             $this->ReturnJson(0,"对不起，您输入的账号或密码错误","");
         }


         //存入用户id
         $user_id = $userinfo["user_id"];
		 $data = array();
         $data["user_id"] = $user_id;

         //查询企业信息
         $user_type = $userinfo["user_type"];
         $data["user_type"] = $user_type;

         if($user_type == 3)
         {
             //企业端
            $user_id = $userinfo["enterprise_id"];
            $data["enterprise_id"] = $user_id;
            $data["proxy_id"] = 0;
         }
         else
         {
            //  $this->ReturnJson(0,"对不起，本系统仅供企业账号查询","");
            //  exit();

            //  //1运营，2代理
             $user_id = $userinfo["proxy_id"];
             $data["enterprise_id"] = 0;
             $data["proxy_id"] = $user_id;
         }

        // $enterprise["user_type"] = $user_type;
        // if($user_type == 1)
        // {
        //     //代理商
        //    $enterprise["proxy_id"] = $sinfo["proxy_id"];
        // }
        // else
        // {
        //      //企业端
        //     $enterprise["enterprise_id"] = $sinfo["enterprise_id"];
        // }
        // $enterpriseinfo = M("enterprise")->where($enterprise)->find();//读出企业的信息
        // $enterprise_name = $enterpriseinfo["enterprise_name"];
        // //  //判断当前企业是否绑定了微信号 企业一对多
        // $sinfo = M("wxs_bindwx")->where($data)->find();//读出活动的信息
        // if($sinfo)
        // {
        //     $this->ReturnJson(0,"对不起，该企业已经绑定了公众号，");
        //     exit();
        // }

	    $data['create_date']=date("Y-m-d H:i:s" ,time());
        $data["openid"] = $Openid;
	    if(!M("wxs_bindwx")->add($data)){
            $this->ReturnJson(0,"服务器维护中，绑定失败","");
            exit();
		}
        else
        {
            if($user_type == 3)
            {
                $this->ReturnJson(1,"您的企业账户绑定成功！","");
            }
            else if($user_type == 2)
            {
                $this->ReturnJson(1,"您的代理商账户绑定成功！","");
            }
            else if($user_type == 1)
            {
                $this->ReturnJson(1,"您的运营商账户绑定成功！","");
            }
             exit();
         }
    }
	  //返回值
    protected function ReturnJson($status, $msg = '', $data = '') {
      $array = array('status' => $status, 'msg' => $msg, 'data' => $data, );

       $this->ajaxReturn($array);
    }


    //home
    private function homeindex(){
      // $p_user_id = 163;
      $data = session("enterprise_key");
      if ($data != null) {
        $strArray = localdecode($data);
        $InfoArray = explode(",",$strArray);
        $p_user_id = $InfoArray[1];
      }else{
        $this->logout();
      }
      $user_info = json_encode(D("SysUser")->get_user_info($InfoArray[0]));
      $user_info_data_array = json_decode($user_info,true);
      $user_info_data = $user_info_data_array['info'];
      // 企业id
      $this->assign('p_user_id',$p_user_id);
      // 账户余额
      $this->assign('account_balance',$user_info_data['account_balance']);
      // 累计存款
      $this->assign('deposit_sum',$user_info_data['deposit_sum']);
      // 冻结余额
      $this->assign('freeze_money',$user_info_data['freeze_money']);
      // 企业名称
      $this->assign('enterprise_name',$user_info_data['enterprise_name']);
      $this -> display("Home/index");
    }

    //home
    private function ophomeindex(){

        //获取当前用户所属平台  返回类型:1=>尚通运营端,2=>代理商端,3=>企业端
        // $user_type = 1;
        // $proxy_id = 2;
        $user_type = getuser_type();//=>尚通运营端,2=>代理商端,3=>企业端
        $proxy_id  = getenterprise_id();//企业id
        
        if (getuser_id() == null) {
            $this->display('Public:404');
        }
        $this->assign('has_cw_right',0);
        if (getroletype() == 1) {
            $this->assign('has_cw_right',1);
        }
        //当天开始时间和结束时间
        $beginToday = date("Y-m-d 00:00:00");
        $endToday = date("Y-m-d 23:59:59");
        //公共时间搜索条件为当天
        $where['create_date'] = array("between",array($beginToday,$endToday));
        $where['approve_status'] = $where2['approve_status'] = 1;   //读取审核成功的代理商
        $where['status'] = $where2['status'] = array("neq",2);   //读取审核成功的代理商
        //判断除企业之外的代理商显示
        if($user_type == 2){
            $where['top_proxy_id'] = $where2['top_proxy_id'] = $proxy_id;
        }
        if($user_type == 1){
            $where['proxy_id'] = array("neq",1);
            $where2['proxy_id'] = array("neq",1);
        }
        //计算当天新增的代理商
        $this->assign('proxy_same_day',M("proxy")->where($where)->count());
        //计算总共的代理商
        $this->assign('proxy_total',M("proxy")->where($where2)->count());
        //计算当天新增的企业
        $this->assign('enterprise_same_day',M("enterprise")->where($where)->count());
        //计算总共的企业
        $this->assign('enterprise_total',M("enterprise")->where($where2)->count());
        $this->display("OPHome/index");
    }

    
}
