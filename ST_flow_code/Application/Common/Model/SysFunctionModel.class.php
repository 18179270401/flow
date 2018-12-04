<?php

namespace Common\Model;
use Think\Model;

class SysFunctionModel extends Model{

	/*
     * 获取节点详细信息
	 */
	public function functioninfo($function_id){
		$info = D('SysFunction')->find($function_id);
		if($info['status'] == 2){
			return '';
		}else{
			return $info;
		}
	}


	/*
	 * 更新角色的权限
	 */
	public function update_function($role_id,$function_ids){

		$model = M('');
		//开启事务
		$model->startTrans();

		//删除当前用户所有角色
		$delete = $this->delete_function($role_id);

		//批量添加
		$add = $this->add_function($role_id,$function_ids);

		if($delete && $add){
			//提交事务
			$model->commit();
			return true;

		}else{
			//事务回滚
			$model->rollback();
			return false;
		}

	}


	/**
	 * 批量删除角色的所有权限 中间表
	 */
	public function delete_function($role_id) {
		$map['role_id'] = array('eq',$role_id);
		//如果当前角色没有权限 则直接退出删除功能
		$count = M('Sys_role_function')->where($map)->count();
		if($count == 0){
			return true;
		} else {
			//当删除的数据与所有的数据总数相同说明全部删除 
			if($count == M('Sys_role_function')->where($map)->delete()){
				return true;
			}else{
				return false;
			}
		}
	}


	/*
	 * 批量添加角色的权限
	 */
	public function add_function($role_id,$function_ids){
		//统计添加的个数
		$count = count($function_ids);

		if($count == 0 ){

			return true;

		}else{
			//执行批量添加
			$add = array();
			$time = date('Y-m-d H:i:s',time());

			//组装批量数组
			foreach ($function_ids as $key => $value) {

				$add[] = array(
					'role_id'=>$role_id,
					'function_id'=>$value,
					'create_user_id'=>D('SysUser')->self_id(),
					'create_date'=>$time,
					);

			}

			//执行批量添加
			if(M('Sys_role_function')->addAll($add) == $count ){
				return true;

			}else{

				return false;

			}
		}

	}



	/*
	 * 批量删除包含该权限的所有角色权限信息
	 */
	public function delete_role($function_id){

		$map['function_id'] = array('eq',$function_id);

		//如果当前角色没有权限 则直接退出删除功能
		$count = M('Sys_role_function')->where($map)->count();

		if($count == 0){
			return true;
		}else{

			//当删除的数据与所有的数据总数相同说明全部删除 
			if($count == M('Sys_role_function')->where($map)->delete()){

				return true;

			}else{

				return false;
			}
		}

	}



	/*
	 * 逻辑删除菜单下所有节点
	 */
	public function menu_delete_function($menu_id){

		$map['menu_id'] = array('eq',$menu_id);
        $map['status'] = array('neq',2);
        $count = M('Sys_function')->where($map)->count();
        if($count){
            $list = M('Sys_function')->field('function_id')->where($map)->select();
            $ids = '';
            foreach($list as $k=>$v){
                $ids .= $v['function_id'].',';
            }
            $ids = substr($ids,0,-1);
            $map = array();

            $map['function_id'] = array('in',$ids);
            $edit = array(
                'status'=>2
                );
            if(M('Sys_function')->where($map)->save($edit) == $count){
                return true;
            }else{
                return false;
            }

        }else{
            return true;
        }

	}


	
	/**
	 * 根据role_id获取对应的function_id数组（除管理员）
	 * @return array 1D
	 */
	public function get_functions_by_roleid($role_id) {
		$arrfuncid = array();
		$arrfuncidT = M('sys_role_function')->where(array('role_id'=>array('eq', $role_id)))->select();
		if(!empty($arrfuncidT) && is_array($arrfuncidT)) {
			foreach ($arrfuncidT as $k => $v) {
				$arrfuncid[] = $v['function_id'];
			}
		}
		return $arrfuncid;
	}
	
	/**
	 * 获取当前用户所属平台端所有功能点
	 * @return array 1D
	 */
	public function get_functions_by_usertype() {
		$ret = $arr_tmenuid = array();
		$user_type = D('SysUser')->self_user_type();
		$arr_menu = D('sys_menu')->where(array('status'=>1,'sys_type'=>$user_type))->field('menu_id')->order('order_num asc,modify_date desc,menu_id asc')->select();
		if(!empty($arr_menu) && is_array($arr_menu)) {
			foreach ($arr_menu as $k => $v) {
				$arr_tmenuid[] = $v['menu_id'];
			}
			$arr_funid = M('sys_function')->where(array('status'=>1,'menu_id'=>array('in',implode(',', $arr_tmenuid))))
						->field('function_id')->order('order_num asc,modify_date desc,function_id asc')->select();
			if(!empty($arr_funid) && is_array($arr_funid)) {
				foreach ($arr_funid as $k2 => $v2) {
					$ret[] = $v2['function_id'];
				}
			}
		}
		return $ret;
	}

	/**
	 *	刷新操作者权限
	 */
	public function reload_function(){

		//获取操作者权限列表
		$function = $this->functionlist();

		$_SESSION['Admin']['right'] = $function;

		return true;

	}

	/**
	 *	获取操作者所有权限
	 */
	public function functionlist(){

		//获取操作者ID号
		$self_id = D('SysUser')->self_id();

		$model = M('');

		//判断操作者是否是超级管理员
		if(D('SysUser')->is_admin()){

			//获取当前操作者的用户类型
			$self_user_type = D('SysUser')->self_user_type();

			$map['menu.sys_type'] = array('eq',$self_user_type);
			$map['menu.status'] = array('eq',1);
			$map['menu.menu_type'] = array('eq',2);
			$function_list = $model
			->table('t_flow_sys_menu as menu')
			->distinct(true)
			->field('action_url')
			->join('t_flow_sys_function as function on function.menu_id = menu.menu_id and function.status = 1','right')
			->where($map)
			->select();
		}else{

			//获取用户所有角色
			$map['user_role.user_id'] = array('eq',$self_id);
			
			//获取用户的列表
			$function_list = $model
			->table('t_flow_sys_user_role as user_role')
			->distinct(true)
			->field('action_url')
			->join('left join t_flow_sys_role as role on role.role_id = user_role.role_id and role.status = 1')
			->join('right join t_flow_sys_role_function as role_function on role.role_id = role_function.role_id ')
			->join('t_flow_sys_function as function on function.function_id = role_function.function_id and function.status = 1')
			->where($map)
			->select();
				
		}

			//非直营的代理商需要删除的功能
			$proxy_delete = array(
				'flowconsume/index', //流量消费统计
				'ProxyIncome/index', //代理收入统计
				
			/*	"enterpriserecharge/index",
				"enterprisewithdrawals/index"*/

				);
			
			//非一级代理商需要删除的功能
			$proxy_top = array(
				"ProxyIncome/index",
				/*"proxyrecharge/index", */
				);


			$enterprise_delete = array(

				//'proxy/index',			//格式
				/*"enterpriseapply/index",
				"enterpriserecharge/index"*/
				);

			//替换的文字
			$replace = array(
				'edit' 		=> 		'update',
				'add'		=>		'insert',
				'index'		=>		array('show','detailed','eninfo'),	
				);


			$proxy_id = D('SysUser')->self_proxy_id();
			if($proxy_id){
				$is_proxy = D('SysUser')->is_self_proxy();
			}else{
				$is_enterprise = D('SysUser')->is_self_proxy_enterprise();
			}
			$top_proxy = D('SysUser')->up_proxy_info();
			$map = array();
			$map['proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
			$map['top_proxy_id'] = array('eq',$top_proxy['proxy_id']);
			$is_proxy_top = M('Proxy')->where($map)->count();
			foreach($function_list as $k=>$v){

				$val = strtolower($v['action_url']);

				$is_add = true;

				if($proxy_id){

					//是代理商并且不是直营的情况下
					if(D('SysUser')->self_user_type() == 2 && !$is_proxy ){

						foreach($proxy_delete as $pk => $pv){
							if( $val == strtolower($pv) ){
								$is_add = false;
								break;
							}
						}
					}



					//是代理商并且是不是一级代理商的情况下
					if(D('SysUser')->self_user_type() == 2  && !$is_proxy_top ){
						foreach($proxy_top as $sk => $sv){

							if( $val == strtolower($sv) ){
								$is_add = false;
								break;
							}
						}
					}

					

				}else{

					if( D('SysUser')->self_user_type() == 3 && !$is_enterprise ){
						foreach($enterprise_delete as $ek => $ev){
							if($val == $ev){
								$is_add = false;  
								break;
							}
						}
					}

				}

				if($is_add){

					foreach($replace as $rk=>$rv){
						if(!stripos($val,$rk) === false){
							if(is_array($rv)){
								foreach($rv as $v){
									$add_val = str_replace($rk,$v,$val);
									$function[] = $add_val;
								}
								break;
							}else{
									$add_val = str_replace($rk,$rv,$val);
									$function[] = $add_val;
									break;
							}
						}
					}
					//执行添加
					$function[] = $val;
				}
			}
			$enterprise_id=D("SysUser")->self_enterprise_id();
			if(!empty($enterprise_id)) {
				/*foreach ($function as $k=>$v){
					if(substr($v,-5)=="index"){
						unset($function[$k]);
					}
					if(substr($v,-6)=="income" ||  substr($v,-6)=="payout"){
						unset($function[$k]);
					}
				}*/
				$e_list = M("available_menu")->where(array("enterprise_id" => $enterprise_id))->field("menu_url")->select();
				if ($e_list) {
					foreach ($e_list as $e) {
						foreach($function as $k=>$v){
							if($v==strtolower($e['menu_url'])){
									unset($function[$k]);
								}
						}
					}
				}
			}
			return $function;
	}
}