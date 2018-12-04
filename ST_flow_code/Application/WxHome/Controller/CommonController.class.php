<?php
namespace WxHome\Controller;
use Think\Controller;

class CommonController extends Controller {
        /**
     * @return int
     * 判断app端是否有权限权限
     */
    public function have_app_login_rights($user_id,$type){
        return true;
        $type = strtoupper($type);			//将指定名称转大写
        $appoint_role = C($type);				//获取指定信息
        $where['role_id'] = array('in',$appoint_role);
        $where['user_id'] = $user_id;
        $role_id_arr = M('sys_user_role')->field('role_id')->where($where)->select();
        if($role_id_arr){
            return true;
        }else{
            return false;
        }
    }

    /*
	  针对上游端用户
	  通过 $user_id 查询该用户是否拥有上游端的角色
	  返回值: in :true  false   是否拥有
      channel_id :所拥有的通道id
	*/

    public function upper_role($user_id){
        $upper_role = C('UPPER_ROLE');
        $where['role_id'] = array('in',$upper_role);
        $where['user_id'] = $user_id;
        $role_id_arr = M('sys_user_role')->field('role_id')->where($where)->select();
        if($role_id_arr){
            $role_arr['in'] = true;
            $channel_id = M('sys_user_channel')->field('channel_id')->where('user_id='.$user_id)->select();
            if($channel_id){
                $channel_ids = get_array_column($channel_id,'channel_id');
                $role_arr['channel_id'] = implode(',', $channel_ids);
            }else{
                $role_arr['channel_id']='';
            }
        }else{
            $role_arr['in'] = false;
        }
        
        return $role_arr;
    }
}