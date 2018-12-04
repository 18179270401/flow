<?php

namespace Common\Model;
use Think\Model;

class SysUserModel extends Model{

	public function check_login($username,$password){
		$msg = '系统错误！';
		$status = 'error';
		$data = array();
		if(empty($username)){
			$msg = '用户名不能为空';
		}elseif(empty($password)){
			$msg = '密码不能为空';
		}

		$map['login_name_full'] = array('eq',$username);
		//$map['status'] = array('neq',2);
		$user = M('Sys_user')->where($map)->find();

		if(!$user) {
			$msg = '您的用户名或密码输入有误，请重新输入！';
		}elseif($user['status'] == '2'){
			$msg = '该用户已不存在！请联系管理员！';
		}elseif($user['status'] == '0'){
			$msg = '该用户已被禁用！';
		}else{
			if($user['login_pass'] !=  md5($password)){
				$msg ='您的用户名或密码输入有误，请重新输入！';
			}else{
				//获取用户的代理商获取企业
				$map = array();
				$map['status'] = array('neq',2);
				if($user['proxy_id']){
					$map['proxy_id'] = $user['proxy_id'];
					$info = M('Proxy')->where($map)->find();
				}else{
					$map['enterprise_id'] = $user['enterprise_id'];
					$info = M('Enterprise')->where($map)->find();
				}

				if($info){
					if($info['approve_status'] == 1){
						if($info['status'] == 1){
							//计算出该管理平台中的超级管理员用户ID
							if($user['is_manager'] == 1){
								$root_user_id = $user['user_id'];
							}else{
								$map = array();
								$map['proxy_id'] = array('eq',$user['proxy_id']);
								$map['status'] = array('neq',2);
								$map['is_manager'] =  array('eq',1);
								$root_user = M('Sys_user')->where($map)->find();
								$root_user_id = $root_user['user_id'];
							}
							$session = array(
								'user_id' 		=>  $user['user_id'],
								'user_type'  	=>	$user['user_type'],
								'user_name'	 	=>	$user['user_name'],
								'login_name_full'	 	=>	$user['login_name_full'],
								'proxy_id'		=>	$user['proxy_id'],
								'enterprise_id'	=>	$user['enterprise_id'],
								'is_manager'	=>	$user['is_manager'],
								'root_user_id'	=>	$root_user_id,
							);
							if($user['proxy_id']){
								$session['proxy_level'] = $info['proxy_level'];
								$session['proxy_type'] = $info['proxy_type'];
							}
							if($user['enterprise_id']){
								$map = array();
								$map['status'] = array('eq',2);
								$map['proxy_id'] = array('eq',$info['top_proxy_id']);
								$map['proxy_type'] = array('eq',1);
								if(M('Proxy')->where($map)->count()){
									$session['enterprise_type'] = 1;
								}else{
									$session['enterprise_type'] = 0;
								}
							}
							session('Admin',$session);
							$msg = '登录成功！';
							$status = 'success';
							$data= $session;
						}else{
							$type = ($user['proxy_id'])?'代理商':'企业';
							$msg = '该'.$type.'已被停用！';
						}
					}else{
						$type = ($user['proxy_id'])?'代理商':'企业';
						$msg = '该'.$type.'未通过审核！';
					}
				}else{
					$type = ($user['proxy_id'])?'代理商':'企业';
					$msg = '该'.$type.'已不存在！请联系管理员！';
				}
			}
		}
		return array('msg'=>$msg,'status'=>$status,'data'=>$data);
	}


	/**
	 *	更新session
	 */
	public function update_session($update_session = array() ){
		if($update_session){
			$session = session('Admin');
			foreach($update_session as $k=>$v){
				if(isset($session[$k])){
					$session[$k] = $v;
				}
			}
			session('Admin',$session);
		}
	}


	/*
     * 获取管理员基本信息
	 */
	public function userbaseinfo($user_id){
		    $map = array();
            $map['user_id'] = array('eq',$user_id);
            $map['status'] = array('neq',2);
            $info = M('Sys_user')->where($map)->find();
            if($info){
            	return $info;
            }else{
            	return false;
            }
	}
	/*
     * 获取管理员详细信息包含代理商或者企业信息
	 */
	public function userinfo($user_id){

		$info = M('Sys_user')->find($user_id);
		$map['u.user_id'] = array('eq',$user_id);
		if($info['proxy_id']){
			$user = M('')->field('u.user_id,
				u.user_name,
				u.login_name_full,
				u.user_type,
				u.is_manager,
				u.is_all_enterprise,
				u.is_all_proxy,
				u.proxy_id,
				u.enterprise_id,
				u.depart_id,u.status,
				p.proxy_code as code,
				p.proxy_name as name,
				p.tel,
				p.contact_name,
				p.email,
				p.top_proxy_id,
				p.operator,
				p.address,
				p.sale_id,
				p.proxy_level as level')
			->table('t_flow_sys_user as u')
			->join('t_flow_proxy as p on p.proxy_id = u.proxy_id')
			->where($map)
			->find();

			$map = array();
			$map['pa.proxy_id'] = $user['proxy_id'];
			$money = M('ProxyAccount pa')
				->field('pa.account_balance,
                pa.freeze_money,
                sum(ifnull(pl.loan_money,0)-ifnull(pl.repayment_money,0)) as loan_money')
				->join(C('DB_PREFIX').'proxy_loan as pl on pa.proxy_id =pl.proxy_id and pl.is_pay_off=0 and pl.approve_status=5','left')
				->where($map)->find();
			if($money){
				$user['account_balance'] = $money['account_balance']-$money['loan_money'];
				$user['loan_money'] = $money['loan_money'];
				$user['freeze_money'] = $money['freeze_money'];
			}else{
				$user['account_balance'] = 0.00;
				$user['freeze_money'] =	0.00;
			}
		}else{

			$map['u.user_id'] = array('eq',$user_id);
			$user = M('')
			->field('u.user_id,u.user_name,u.login_name_full as login_name,u.user_type,u.is_manager,u.proxy_id,u.enterprise_id,u.depart_id,u.status,e.enterprise_code as code,e.enterprise_name as name,e.tel,e.contact_name,e.email,e.top_proxy_id,e.operator,e.address,e.sale_id')
			->table('t_flow_sys_user as u')
			->join('t_flow_enterprise as e on e.enterprise_id = u.enterprise_id')
			->where($map)
			->find();
			$map = array();
			$map['enterprise_id'] = array('eq',$user['enterprise_id']);
			$money = M('EnterpriseAccount')->where($map)->find();
			if($money){
				$user['account_balance'] = $money['account_balance'];
				$user['freeze_money'] = $money['freeze_money'];
			}else{
				$user['account_balance'] = 0.00;
				$user['freeze_money'] =	0.00;
			}
		}

		if($user['status'] == 2){
			return '';
		}else{
			return $user;
		}
	}


	/**
	 * 获取自身的user_id
	 */
	public function self_id(){

		$self = session('Admin');

		return $self['user_id'];

	}


	/**
	 * 判断自身是否是超级管理员
	 */
	public function is_admin(){
		$self = session('Admin');
		return $self['is_manager'];
	}


	/**
	 * 获取节点列表
	 */
	public function getfunctionlist(){
		$self = session('Admin');
		if(isset($self['right']) ){
			if($self['right']){
				return $self['right'];
			}else{
				return array();
			}
		}else{
			return '';
		}
		
	}

	/**
	 * 获取所有接口管理表数据
	 */
	public function get_sys_api() {
		$ret = M('sys_api')->select();
		return $ret;
	}
	
	/**
	 * 根据条件获取单条接口数据
	 */
	public function get_sys_api_by_id($cond) {
		if(!empty($cond) && is_array($cond)) {
			foreach ($cond as $k=> $v) {
				if(empty($v)) {
					unset($cond[$k]);
				}
			}
		}
		$api_account = M('sys_api')->where($cond)->find();
		return $api_account;
	}
	
	/**
	 * 获取自身账户信息(不论自身是代理商还是企业)
	 */
	public function get_account_info($user_type, $proxy_id, $enterprise_id) {
		$ret = array();
		if(2 == $user_type) {
			$cond = array(
					'ap.proxy_id'	=> array('eq', $proxy_id),
			);
			$ret = D('ProxyAccount')->proxy($cond);
		} else if(3 == $user_type) {
			$cond = array(
					'enterprise_id'	=> array('eq', $enterprise_id),
			);
			$ret = D('EnterpriseAccount')->account($cond);
		}
		return $ret;
	}
	
	/**
	 * 获取自身信息（不论自身是代理商还是企业）
	 */
	public function get_pe_info($user_type, $proxy_id, $enterprise_id) {
		$ret = array();
		if(2 == $user_type) {
			$ret = D('Proxy')->proxyinfo($proxy_id);
		} else if(3 == $user_type) {
			$ret = D('Enterprise')->enterpriseinfo($enterprise_id);
		} else if(1 == $user_type) {
			$ret = D('Proxy')->proxyinfo($proxy_id);
		}
		return $ret;
	}

	/**
	 * 获取自身的用户类型是运营平台，代理商，企业
	 */
	public function self_user_type(){
		
		$self = session('Admin');
		return $self['user_type'];

	}



	/**
	 * 获取自身的代理商ID
	 */
	public function self_proxy_id(){

		$self = session('Admin');

		return $self['proxy_id'];

	}


	/**
	 *	判断是否是自营的代理商
	 */
	public function is_self_proxy(){

		$self = session('Admin');
		if($self['proxy_type']){
			return true;
		}else{
			return false;
		}
	}


	/**
	 *	判断是否是自营的代理商下的企业
	 */
	public function is_self_proxy_enterprise(){

		$map['enterprise.status']  = array('neq',2);
		$map['proxy.proxy_type'] = array('eq',1);
		$enterprise_id = D('SysUser')->self_enterprise_id();
		$map['enterprise.enterprise_id'] = array( 'eq' , $enterprise_id );
		$enterprise = M('Enterprise as enterprise')
		->join('t_flow_proxy as proxy on proxy.proxy_id = enterprise.top_proxy_id')
		->where($map)
		->find();
		if($enterprise){
			return true;
		}else{
			return false;
		}

	}


	/**
	 * 获取自身的企业
	 */
	public function self_enterprise_id(){
		$self = session('Admin');
		return $self['enterprise_id'];
	}


	public function self_proxy_level(){
		$self = session('Admin');
		if($self['proxy_id']){
			return $self['proxy_level'];
		}else{
			return '';
		}
	}


	public function is_all_proxy($user_id){
		$map['status'] = array('neq',2);
		$map['user_id'] = array('eq',$user_id);
		$user = M('Sys_user')->where($map)->find();
		return $user['is_all_proxy'];
	}

	public function is_all_enterprise($user_id){
		$map['status'] = array('neq',2);
		$map['user_id'] = array('eq',$user_id);
		$user = M('Sys_user')->where($map)->find();
		return $user['is_all_enterprise'];
	}

	/*获取自身代理商的顶级代理商*/
	public function self_top_proxys_id($proxy_id){
		$map['status'] = array('neq',2);
		$map['proxy_id'] =$proxy_id;
		$info = M('proxy')->where($map)->find();
		return $info['top_proxy_id'];
	}


	/*获取自身企业的顶级代理商*/
	public function self_top_proxy_id($account_balance){
		$map['status'] = array('neq',2);
		$map['enterprise_id'] =$account_balance;
		$info = M('enterprise')->where($map)->find();
		return $info['top_proxy_id'];
	}

	/*判断是否是企业的顶级代理商*/
	public function is_up_enterprise_proxy(){
		$info = $this->self_up_proxy_info();
		if($this->self_enterprise_id()==$info['proxy_id']){
			return true;
		}else{
			return false;
		}
	}

	/*企业的顶级代理商信息*/

	public function self_up_proxy_info(){
		$map['status'] = array('neq',2);
		$map['enterprise_id'] = array('eq',0);
		$info = M('enterprise')->where($map)->find();
		if($info){
			return $info;
		}else{
			return '';
		}
	}




	/**
	 * 获取当前平台的的超级管理员ID
	 */
	public function root_user_id(){

		//如果当前用户是管理员则取自身
		$self = session('Admin');

		return $self['root_user_id'];
		
	}


	/*
	 * 判断用户的ID是否和自身属于统一管理平台下
	 */
	public function is_balance($user_id){

		$map['status'] = array('neq',2);
		$map['user_id'] = array('eq',$user_id);
		$map['is_manager'] = array('eq',0);

		$info = M('Sys_user')->where($map)->find();

		if($this->self_proxy_id() == $info['proxy_id']){

			return true;

		}else{

			return false;

		}

	}



	/*
	 * 判断自身是否是顶级代理商下的管理员
	 */
	public function is_top_proxy_admin(){

		$info = $this->up_proxy_info();

		//如果顶级代理商的ID 等于我自身的代理商ID 说明我就是顶级下的代理商
		if($this->self_proxy_id() == $info['proxy_id']){
			return true;
		}else{
			return false;
		}
	}
	


	/*
	 * 获取顶级代理商的信息
	 */
	public function up_proxy_info(){

		$map['status'] = array('neq',2);
		$map['top_proxy_id'] = array('eq',0);

		$info = M('Proxy')->where($map)->find();

		if($info){
			return $info;
		}else{
			return '';
		}
	}

	/*
	 * 判断用户ID是否是超级管理员
	 */
	public function is_root_admin($user_id){
		$map['status'] = array('eq',1);
		$map['user_id'] = array('eq',$user_id);
		$info = M('Sys_user')->where($map)->find($user_id);
		if($info['is_manager']){
			return true;
		}
		return false;
	}

	/**
	 *	判断号码唯一性  
	 */
	public function check_mobile($mobile,$user_id = false){
		$map['status'] = array('neq',2);
        $map['mobile'] = array('eq',$mobile);
        if($user_id !== false){
        	$map['user_id'] = array('neq',$user_id);
        }
        if(M('Sys_user')->where($map)->find()){
            return true;
        }else{
            return false;
        }
	}
	

	/**
	 *	判断账号的唯一性
	 */
	public function check_login_name_repeat($login_name_full,$user_id = false){
		$map['status'] = array('neq',2);
		$map['login_name_full'] = array('eq',$login_name_full);	//登录名称等于自己
		if($user_id !== false){
			$map['user_id'] = array('neq',$user_id);
		}

		if(M('Sys_user')->where($map)->count()){
			return true;
		}else{
			return false;
		}
	}


	/**
	 *	判断该用户是否可以作为代理商的客户经理
	 */
	public function is_proxy_sale($proxy_id,$user_id){
		$map['status'] = array('neq',2);
		$map['proxy_id'] = array('eq',$proxy_id);
		$proxy = M('Proxy')->where($map)->find();
		if($proxy){
			$map = array();
			$map['status'] = array('eq',1);
			$map['proxy_id'] = array('eq',$proxy['top_proxy_id']);
			$map['user_id'] = array('eq',$user_id);
			if(M('Sys_user')->where($map)->find()){
				return true;
			}else{
				return false;
			}
		}
		return false;
	}


	/**
	 *	判断该用户是否可以作为企业的客户经理
	 */
	public function is_enterprise_sale($enterprise_id,$user_id){
		$map['status'] = array('neq',2);
		$map['enterprise_id'] = array('eq',$enterprise_id);
		$enterprise = M('Enterprise')->where($map)->find();
		if($enterprise){
			$map = array();
			$map['status'] = array('eq',1);
			$map['proxy_id'] = array('eq',$enterprise['top_proxy_id']);
			$map['user_id'] = array('eq',$user_id);
			if(M('Sys_user')->where($map)->find()){
				return true;
			}else{
				return false;
			}
		}
		return false;
	}

	/**
	*登录api的验证
	**/
	public function check_login_api($username,$password){
		$msg = '系统错误！';
		$status = 'error';
		$data = array();
		if(empty($username)){
			$msg = '用户名不能为空';
		}elseif(empty($password)){
			$msg = '密码不能为空';
		}

		$map['login_name_full'] = array('eq',$username);
		//$map['status'] = array('neq',2);
		$user = M('Sys_user')->where($map)->find();

		if(!$user) {
			$msg = '您的用户名或密码输入有误，请重新输入！';
		}elseif($user['status'] == '2'){
			$msg = '该用户已不存在！请联系管理员！';
		}elseif($user['status'] == '0'){
			$msg = '该用户已被禁用！';
		}else{
			if($user['login_pass'] != $password ){
				$msg ='您的用户名或密码输入有误，请重新输入！';
			}else{
				//获取用户的代理商获取企业
				$map = array();
				$map['status'] = array('neq',2);
				if($user['proxy_id']){
					$map['proxy_id'] = $user['proxy_id'];
					$info = M('Proxy')->where($map)->find();
				}else{
					$map['enterprise_id'] = $user['enterprise_id'];
					$info = M('Enterprise')->where($map)->find();
				}

				if($info){
					if($info['approve_status'] == 1){
						if($info['status'] == 1){
							//计算出该管理平台中的超级管理员用户ID
							if($user['is_manager'] == 1){
								$root_user_id = $user['user_id'];
							}else{
								$map = array();
								$map['proxy_id'] = array('eq',$user['proxy_id']);
								$map['status'] = array('neq',2);
								$map['is_manager'] =  array('eq',1);
								$root_user = M('Sys_user')->where($map)->find();
								$root_user_id = $root_user['user_id'];
							}
							$session = array(
								'user_id' 		=>  $user['user_id'],
								'user_type'  	=>	$user['user_type'],
								'user_name'	 	=>	$user['user_name'],
								'login_name_full'	 	=>	$user['login_name_full'],
								'proxy_id'		=>	$user['proxy_id'],
								'enterprise_id'	=>	$user['enterprise_id'],
								'is_manager'	=>	$user['is_manager'],
								'root_user_id'	=>	$root_user_id,
							);
							if($user['proxy_id']){
								$session['proxy_level'] = $info['proxy_level'];
								$session['proxy_type'] = $info['proxy_type'];
							}
							if($user['enterprise_id']){
								$map = array();
								$map['status'] = array('eq',2);
								$map['proxy_id'] = array('eq',$info['top_proxy_id']);
								$map['proxy_type'] = array('eq',1);
								if(M('Proxy')->where($map)->count()){
									$session['enterprise_type'] = 1;
								}else{
									$session['enterprise_type'] = 0;
								}
							}
							$msg = '登录成功！';
							$status = 'success';
							$data= $session;
						}else{
							$type = ($user['proxy_id'])?'代理商':'企业';
							$msg = '该'.$type.'已被停用！';
						}
					}else{
						$type = ($user['proxy_id'])?'代理商':'企业';
						$msg = '该'.$type.'未通过审核！';
					}
				}else{
					$type = ($user['proxy_id'])?'代理商':'企业';
					$msg = '该'.$type.'已不存在！请联系管理员！';
				}
			}
		}
		return array('msg'=>$msg,'status'=>$status,'data'=>$data);
	}
	function sessionwriteclose(){
		return true;
	}

}