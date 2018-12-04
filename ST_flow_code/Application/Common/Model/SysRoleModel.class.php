<?php

namespace Common\Model;
use Think\Model;

class SysRoleModel extends Model{

	/*
     * 获取角色详细信息
	 */
	public function roleinfo($role_id){
		$info = D('SysRole')->find($role_id);

		if($info['status'] == 2){
			return '';
		}
		
		return $info;
	}


	/*
	 * 更新用户的所有角色
	 */
	public function update_role($user_id,$role_ids){

		$model = M('');
		//开启事务
		$model->startTrans();

		//删除当前用户所有角色
		$delete = $this->delete_role($user_id);
		
		//批量添加
		$add = $this->add_role($user_id,$role_ids);
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


	/*
	 * 批量删除包含用户的所有用户角色信息
	 */
	public function delete_role($user_id){
		$map['user_id'] = array('eq',$user_id);

		//如果当前用户没有角色 则直接退出删除功能
		$count = M('Sys_user_role')->where($map)->count();

		if($count == 0){
			return true;
		}else{
			//当删除的数据与所有的数据总数相同说明全部删除 
			if($count == M('Sys_user_role')->where($map)->delete()){

				return true;

			}else{

				return false;
			}
		}

	}


	/*
	 * 批量添加用户角色
	 */
	public function add_role($user_id,$role_ids){
		//统计添加的个数
		$count = count($role_ids);

		if($count == 0 ){
			return true;
		}else{

			//执行批量添加
			$add = array();
			$time = date('Y-m-d H:i:s',time());
			$self_id = D('SysUser')->self_id();

			//组装批量数组
			foreach ($role_ids as $key => $value) {

				$add[] = array(
					'user_id'=>$user_id,
					'role_id'=>$value,
					'create_user_id'=>$self_id,
					'create_date'=>$time,
					);
			}

			//执行批量添加
			if(M('Sys_user_role')->addAll($add)){
				return true;
			}else{
				return false;
			}
		}

	}



	/**
	 * 批量删除含有该角色的用户角色数据
	 */
	public function delete_user($role_id) {
		$map['role_id'] = array('eq',$role_id);
		//如果当前用户没有角色 则直接退出删除功能
		$count = M('Sys_user_role')->where($map)->count();
		if($count == 0){
			return true;
		}else{
			//当删除的数据与所有的数据总数相同说明全部删除 
			if($count == M('Sys_user_role')->where($map)->delete()){
				return true;
			}else{
				return false;
			}
		}
	}

	/**
	 * 获取所有用户角色表数据
	 */
	public function get_user_role_all() {
		$ret = S('sys_user_role_all');
		if(empty($ret)) {
			$retT = M('sys_user_role')->select();
			$ret = empty($retT) ? array() : $retT;
			S('sys_user_role_all', $ret, 5); //5秒
		}
		
		return $ret;
	}
	
	/**
	 * 获取某个角色底下有多少个用户
	 */
	public function get_user_role_byid($role_id) {
		$ret = 0;
		$urall = $this->get_user_role_all();
		if(!empty($urall) && is_array($urall)) {
			foreach ($urall as $k => $v) {
				if($v['role_id'] == $role_id) {
					$ret++;
				}
			}
		}
		return $ret;
	}

	/**
	 * 获取某个用户的所有角色
	 */
	public function get_the_user_roles() {
		$user_id=D('SysUser')->self_id();

		$urall = $this->get_user_role_all();

		$roles = array();
		if(!empty($urall) && is_array($urall)) {
			foreach ($urall as $k => $v) {
				if($v['user_id'] == $user_id) {
					$roles[] = $v['role_id'];
				}
			}
		}
		return $roles;
	}


	/*
	  针对上游端用户
	  通过 $user_id 查询该用户是否拥有上游端的角色
	  返回值: in :true  false   是否拥有
      channel_id :所拥有的通道id
	*/

	public function  upper_role(){
		$user_id=D('SysUser')->self_id();
		$upper_role=C('UPPER_ROLE');
		$where['role_id']=array('in',$upper_role);
		$where['user_id']=$user_id;
		$role_id_arr=M('sys_user_role')->field('role_id')->where($where)->select();
        if($role_id_arr){
			$role_arr['in']=true;
            $channel_id=M('sys_user_channel')->field('channel_id')->where('user_id='.$user_id)->select();
			if($channel_id){
				$channel_ids=get_array_column($channel_id,'channel_id');
				$role_arr['channel_id']=implode(',', $channel_ids);
			}else{
				$role_arr['channel_id']='';
			}
		}else{
			$role_arr['in']=false;
		}
        return $role_arr;
	}

	/**
	 * 判断当前用户是否在指定的角色内
	 * @$type = 指定名称 (需和config中定义的名称一致)
	 */
	public function user_is_role($type=NULL){
		$user_id=D('SysUser')->self_id();	//获取当前用户ID号
		if($user_id==1){
			return 1;
		}
		$type = strtoupper($type);			//将指定名称转大写
		$appoint_role=C($type);				//获取指定信息
		$where['role_id']=array('in',$appoint_role);
		$where['user_id']=$user_id;
		$role_id_arr=M('sys_user_role')->field('role_id')->where($where)->select();
		if($role_id_arr){
			return $role_id_arr;
		}
		return false;
	}



}