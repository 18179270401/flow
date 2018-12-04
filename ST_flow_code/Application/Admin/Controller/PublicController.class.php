<?php

/*
 * PublicController.class.php
 * 后台登录控制器
 *
 */
namespace Admin\Controller;
use Think\Controller;
use Think\Verify;


class PublicController extends Controller {

	/*
	 * 功能：登录方法
	 */
    public function login(){

    	$msg = '系统错误！';
    	$status= 'error';

    	if(IS_POST && IS_AJAX){
    			//判断验证码是否正确
    			if(!$this->check_verify(I('verify'))){
    				$this->ajaxReturn(array('msg'=>'验证码输入有误，请重新输入','status'=>$status));
	    		}
	    		//验证用户名与密码
	    		$login_check_info = D('SysUser')->check_login(trim(I('post.username')) ,trim(I('post.password')));
	    		//判断状态
	    		if($login_check_info['status'] == 'success'){
                    // 判断用户是否是初始密码
                    if(trim(I('post.password')) == '123456'){
                        $session = session('Admin');
                        $session['initial_password'] = true;
                        session('Admin',$session);
                    }
                    $login_log = array(
                        'ip_addr'=>get_client_ip2(),
                        'login_user_id'=>$login_check_info['data']['user_id'],
                        'login_user_name'=>$login_check_info['data']['user_name'],
                        'login_name_full'=>$login_check_info['data']['login_name_full'],
                        'login_date'=>date('Y-m-d H:i:s',time()),
                        'login_type'=>$login_check_info['data']['user_type'],
                        );
                    D('SysLoginLog')->add($login_log);

                    $msg = $login_check_info['msg'];
	    			$status = $login_check_info['status'];

                    //清空或保存cookie
                    $this->_setcookie(2,$_POST['username'],$_POST['password'],$_POST['remember_pass']);

                    //调用删除 流量充值记录excel 的导出文件->流量充值记录excel 的方法
                    $this->delete_excel_file();
	    		}else{
                    $msg = $login_check_info['msg'];
                }
	    		//反馈信息
	    		$this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    	}else{
            //判断是否有SESSION
            if(D('SysUser')->self_id()){
                $this->redirect('index/index');
            }
            //域名判断
            $host = $_SERVER['SERVER_NAME'];
            $map['domain_name'] = $host;
            $map2['approve_status'] = $map['approve_status'] = 3; //复审通过

            $is_have = M('domain')->field('logo_img,web_name,web_end,ico_img,back_img')->where($map)->find();
            if(empty($is_have)){
                $host2 = trim($host,'www.');
                $map2['domain_name']=array('like',"%$host2%");
                $is_have = M('domain')->field('logo_img,web_name,web_end,ico_img,back_img')->where($map2)->find();
            }
            $display_url = 'login';
            if(!empty($is_have)){
                $domain_url_list = C('DOMAIN_LOGIN_URL');
                foreach($domain_url_list as $k=>$v){
                    if($host == $k){
                        $display_url = $v;
                    }
                }
            }

            if($display_url == 'login'){
                //平台默认参数
                $display_default = C('DOMAIN_LOGIN_DEFAULT_INFO');
                $display_title = empty($is_have['web_name'])?$display_default['display_title']:$is_have['web_name'];
                $display_picture = empty($is_have['logo_img'])?$display_default['display_picture']:$is_have['logo_img'];
                $display_end = empty($is_have['web_end'])?$display_default['display_end']:$is_have['web_end'];
                $display_icon = empty($is_have['ico_img'])?$display_default['display_icon']:$is_have['ico_img'];
                $display_back = empty($is_have['back_img'])?$display_default['display_back']:$is_have['back_img'];
                
                $this->assign('display_title', $display_title);
                $this->assign('display_picture', $display_picture);
                $this->assign('display_end', $display_end);
                $this->assign('display_icon', $display_icon);
                $this->assign('display_back', $display_back);
            }
            //读取cookie
            $this->assign('cookie', $this->_setcookie(1));
            $this->assign('hostname', $host);
            $this->display($display_url);
    	}
    }
    //操作cookie
    private function _setcookie($type,$username=NULL,$password=NULL,$remember_pass=NULL){
        if($type==1){
            $cookie = cookie(md5('cookie_admin'));
            if (!empty($cookie)) {
                $_cookie = json_decode($cookie, true);
                return $_cookie;
            }
            return '';
        }else{
            if ($remember_pass == 1) {
                $cookie = cookie(md5('cookie_admin'));
                if(!empty($cookie)){
                    $_cookie = json_decode($cookie, true);
                    if($_cookie['username']!=$username){
                        cookie(md5("cookie_admin"), json_encode(array('username' => $username, 'password' => $password)),360*24*60);
                    }
					if($_cookie['password']!=$password){
                        cookie(md5("cookie_admin"), json_encode(array('username' => $username, 'password' => $password)),360*24*60);
                    }
                }else{
                    cookie(md5("cookie_admin"), json_encode(array('username' => $username, 'password' => $password)),360*24*60);
                }
            }else{
                cookie(md5("cookie_admin"), null);
            }
            return true;
        }
    }

    /**
     * 删除前一天导出的流量充值记录excel
     * 每次登录时判断前一天的数据是否存在，存在则删除数据。
     */
    public function delete_excel_file(){
        //获取昨天的年月日
        $times = date("Ymd",strtotime("-1 day"));
        $file_url = $_SERVER["DOCUMENT_ROOT"]."/Public/ExcelFile/".$times."/";
        if(is_dir($file_url)){
            //打开文件夹;
            $dh = opendir($file_url);
            //先删除目录下的文件;
            while($file = readdir($dh)){
                if ($file != "." && $file != ".."){
                    $fullpath = $file_url . "/" . $file;
                    if(!is_dir($fullpath)){
                        unlink($fullpath);
                    }else{
                        deldir($fullpath);
                    }
                }
            }
            //关闭文件夹;
            closedir($dh);
            //删除当前文件夹;
            rmdir($file_url);
        }
    }

    /*
     * 登出功能
     */
    public function logout(){
        //清空用户SESSION
        session('Admin',null);
        //跳转到登录页面
        $this->redirect('Admin/Public/login');
    }


    /**
     * 创建验证码
     * @param 
     * @return 
     */
    public function create_verify(){
    	//验证码规则
	    $config =    array(
		    'fontSize'    =>    30,    // 验证码字体大小
		    'length'      =>    4,     // 验证码位数
		    'useNoise'    =>    false, // 关闭验证码杂点
		);
	    //调用验证码类
    	$Verify = new Verify($config);
    	//设置
    	$Verify->codeSet = '0123456789';
        //$Verify->codeSet = '0'; //测试环境
    	//生成验证码
    	$Verify->entry();
    }


     /**
     * 验证验证码
     * @param 	验证号码
     * @return  true|false
     */
    public function check_verify($code, $id = ''){
    	//调用验证类
    	$verify = new Verify();
    	//验证并返回验证信息
    	return $verify->check($code, $id);
	}
    
    
    /**
     * 找回密码
     */
    public function back_password(){
        $domain_host = C('DOMAIN_LOGIN_DEFAULT_INFO');        //官网配置
        $log_src = $domain_host['display_picture'];  // /Public/Admin/images/logo_09.png
        $this->assign('log_src',$log_src);
        $this->display();
    }
    /**
     * 找回密码的操作
     */
    public function back_password_ajax(){
        $name = I("post.name");
        $email = I("post.email");
        $where['login_name_full'] = $name;
        $where['email'] = $email;
        $user = M("SysUser")->where($where)->field("user_id")->find();
        //echo M()->getLastSql();
        if($user){
            session("admin_user_id",$user['user_id']);
            $msg    = "验证成功";
            $status = "success";
        }else{
            $msg    = "登录名称或E-mail错误！";
            $status = "error";
        }
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }
    /**
     * 修改新密码
     */
    public function back_password_edit(){
        $password = I("post.password");
        $password_new = I("post.password_new");
        $password_strlen = strlen($password);
        if($password==""){
            $msg    = "新密码不能为空！";
            $status = "error";
        }elseif(preg_match("/[\x7f-\xff]/",$password)){
            $msg    = "请误使用中文做为密码！";
            $status = "error";
        }elseif(5 < $password && $password < 21){
            $msg    = "新密码需在6至20位之前！";
            $status = "error";
        }elseif($password!=$password_new){
            $msg    = "两次输入的密码不相同！";
            $status = "error";
        }else{
            $user_id = session("admin_user_id");
            $edit['login_pass'] = md5($password);
            $edit['modify_date'] = date("Y-m-d H:i:s",time());
            if(M("SysUser")->where(array('user_id'=>$user_id))->save($edit)){
                $msg    = "新密码修改成功！";
                $status = "success";
            }else{
                $msg    = "新密码修改失败！";
                $status = "error";
            }
        }
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    /**
     * 功能：登录api
     * 
     */
    public function login_api(){
        $ret = array(
            'status'    => 'error',
            'msg'       => '其他错误,请联系工程师跟进!',
            'data'      => array(),
        );

        $request = I("request.");
        //接口参数
        $user_name = trim($request['username']);
        $password = trim($request['password']);
        $timestamp = trim($request['timestamp']);
        $sign = trim($request['sign']);

        if(empty($user_name) || empty($password) || empty($timestamp) || empty($sign)){
            $ret['msg'] = '参数值非法';
            write_error_log(array(__METHOD__.'：'.__LINE__, '参数值非法', $request));
            echo json_encode($ret);
            exit();
        }

        if($sign != md5($user_name.$timestamp.$password)){
            $ret['msg'] = '签名校验失败';
            write_error_log(array(__METHOD__.'：'.__LINE__, '签名校验失败'));
            echo json_encode($ret);
            exit();
        }

        $login_check_info = D('SysUser')->check_login_api($user_name,$password);
        $ret = $login_check_info;
        //判断状态
        if($login_check_info['status'] == 'error'){
            echo json_encode($ret);
            exit();
        }

        $data = $login_check_info['data'];

        // 判断用户是否是初始密码
        if($password == 'e10adc3949ba59abbe56e057f20f883e'){
            $data['initial_password'] = true;
        }

        //登录日志
        $login_log = array(
            'ip_addr' => get_client_ip2(),
            'login_user_id' => $data['user_id'],
            'login_user_name' => $data['user_name'],
            'login_name_full' => $data['login_name_full'],
            'login_date' => date('Y-m-d H:i:s',time()),
            'login_type' => $data['user_type'],
        );
        D('SysLoginLog')->add($login_log);

        //后期查询用户是否注册url来设定
        $user_type = $data['user_type'];
        $proxy_id = 0;
        $enterprise_id = 0;
        $top_proxy_id = 0;
        $url = $_SERVER['HTTP_HOST'];

        $domain_info = array();
        $top_info = array();
        if($user_type == 2){
            $proxy_id = $data['proxy_id'];
            $map = array();
            $map['user_type'] = 1;
            $map['proxy_id'] = $proxy_id;
            $map['approve_status'] = 3;
            $domain_info = M('Domain')->where($map)->find();

            $top_proxy_id = D('SysUser')->self_top_proxys_id($proxy_id);
        }

        if($user_type == 3){
            $enterprise_id = $data['enterprise_id'];
            $map = array();
            $map['user_type'] = 2;
            $map['enterprise_id'] = $enterprise_id;
            $map['approve_status'] = 3;
            $domain_info = M('Domain')->where($map)->find();

            $top_proxy_id = D('SysUser')->self_top_proxy_id($enterprise_id);
        }
        //var_dump($enterprise_id);
        

        if(!empty($domain_info) && !empty($domain_info['domain_name'])){
            $url = $domain_info['domain_name'];
        }else{
            if(!empty($top_proxy_id)){
                $map = array();
                $map['user_type'] = 1;
                $map['proxy_id'] = $top_proxy_id;
                $map['approve_status'] = 3;
                $top_info = M('Domain')->where($map)->find();
                if(!empty($top_info) && !empty($top_info['domain_name'])){
                    $url = $top_info['domain_name'];
                }
            }
        }
        
        $uri = '/index.php/Admin/Public/login_url';
        $now_time = time();
        $key = md5($user_name.$now_time);
        S($key,$data,300);
        $ret['data'] = array(
                'url' => 'http://' . $url . $uri,
                'key' => $key
            );
        echo json_encode($ret);
        exit();
    }

    /*
     * 功能：跳转
     * 
     */
    public function login_url(){
        $ret = array(
            'status'    => 'error',
            'msg'       => '其他错误,请联系工程师跟进!',
            'data'      => '',
        );

        $request = I("request.");
        //接口参数
        $key = trim($request['key']);
        $sign = trim($request['sign']);
        if(empty($key) || empty($sign)){
            $ret['msg'] = '参数值非法';
            write_error_log(array(__METHOD__.'：'.__LINE__, '参数值非法', $request));
            echo json_encode($ret);
            exit();
        }
        $action = ACTION_NAME;
        if($sign != md5($key.$action)){
            $ret['msg'] = '签名校验失败';
            write_error_log(array(__METHOD__.'：'.__LINE__, '签名校验失败'));
            echo json_encode($ret);
            exit();
        }
        
        $data = S($key);
        if(empty($data)){
            $ret['msg'] = '登录已过期';
            write_error_log(array(__METHOD__.'：'.__LINE__, '登录已过期'));
            echo json_encode($ret);
            exit();
        }
        session('Admin',$data);
        S($key,NULL);
        $url = $_SERVER['HTTP_HOST'];
        $url ='http://' . $url . '/index.php/Admin/index/index.html';
        header("location:".$url);
    }
    /*修改菜单字*/
    public function menu_list_cc(){
        header("Content-Type:text/html;   charset=utf-8");
        $type=1 ;// 适合那个端：'' 所有，1尚通  2代理商  3企业
        $menu_type=2; //菜单类型( '':所有   1：分级菜单，2：功能菜单)
        $group_name=2 ;// 分组名称 空则全部  1 非分组  2分组菜单 3 特定的分组名称
        $status='';//状态（0：已禁用，1：正常，2：已删除）
        $where=array();
        if($type==1){ //尚通端
            $where['sys_type']=1;
        }

        if($type==2){//代理商
            $where['sys_type']=2;
        }

        if($type==3){  //企业
            $where['sys_type']=3;
        }

        if($menu_type==1){ //分级菜单
            $where['menu_type']=1;
        }

        if($menu_type==2){ //功能菜单
            $where['menu_type']=2;
        }

        if($group_name==1){
            $where['group_name']=array('eq','');
        }

        if($group_name==2){
            $where['group_name']=array('neq','');
        }
        if($group_name==3){
            $where['group_name']=array('like','% %');//输入具体的分组名称
        }

        if($status===0){
            $where['status']=0;
        }
        if($status==1){
            $where['status']=1;
        }
        if($status==2){
            $where['status']=2;
        }

        $info=M('sys_menu')->where($where)->select();
        foreach($info as $k=>$v) {
            $data[$k]['menu_id'] =$v['menu_id'];
            $data[$k]['menu_name'] = str_replace("代理", "代理商", $v['menu_name']);
            $data[$k]['group_name'] = str_replace("代理", "代理商", $v['group_name']);
        }
        var_dump($data);
    }



    public function log(){
        header("Content-Type:text/html;charset=utf8");
        $log_list=M('sys_log')->field('log_id,create_user_id')->order('create_date desc')->select();
        foreach($log_list as $u){
            $list=M('sys_user')->field('user_type,proxy_id,enterprise_id')->where('user_id='.$u['create_user_id'])->find();
            echo '代理商：'.$list['proxy_id'].'----企业：'.$list['enterprise_id'].'<br/>';
            $edit['log_id']=$u['log_id'];
            $edit['user_type']=empty($list['proxy_id'])?2:1;
            $edit['proxy_id']=$list['proxy_id'];
            $edit['enterprise_id']=$list['enterprise_id'];
            $ss=M('sys_log')->save($edit);
        }
    }


}