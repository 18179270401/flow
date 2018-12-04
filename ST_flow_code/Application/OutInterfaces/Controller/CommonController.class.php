<?php
namespace OutInterfaces\Controller;
use Think\Controller;

class CommonController extends Controller {
    /**
     * @param $token
     * @param $result
     * @param $user_info
     * @return bool
     * 判断是否拥有权限
     */
    public function is_token_right($token,&$result,&$user_info){
        if(empty($token)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            write_error_log(array(__METHOD__.'：'.__LINE__, 'token:', $token),'_OutInterfaces');
            return false;
        }
        $token_info = token_decode($token);
        $userName = $token_info['user_name'];
        $tokenMd5 = $token_info['token_md5'];
        //$token_date = $token_info['token_date'];
        $map['u.login_name_full'] = array('eq',$userName);
        $map['u.status'] = array('eq',1);
        $user_info = M('Sys_user as u')
            ->join(C('DB_PREFIX')."sys_login_log as ll on ll.login_user_id=u.user_id and ll.login_type=5")
            ->field('u.*,ll.login_date')
            ->order('ll.login_date desc')
            ->where($map)
            ->find();
        if(!empty($user_info)){
            /***app可以同时多个app同一账号登录，长时间登录，不对登录时间做判断
            $login_date = substr($user_info['login_date'],0,10);    //当天有效
            $login_date_time = substr($user_info['login_date'],0,19);
            if($login_date_time != $token_date){
                write_error_log(array(__METHOD__.'：'.__LINE__, 'token:', $token),'_OutInterfaces');
                write_error_log(array(__METHOD__.'：'.__LINE__, 'userName:', $userName),'_OutInterfaces');
                $result['ret'] = '303';
                $result['msg'] = 'token过期';
                return false;
            }
             ***/
            $passWord = $user_info['login_pass'];
            $dbMd5 = md5($userName.$passWord);
            if($dbMd5 == $tokenMd5){
                return true;
            }
        }
        write_error_log(array(__METHOD__.'：'.__LINE__, 'token:', $token),'_OutInterfaces');
        write_error_log(array(__METHOD__.'：'.__LINE__, 'userName:', $userName),'_OutInterfaces');
        return false;
    }

    /*
	  针对上游端用户
	  通过 $user_id 查询该用户是否拥有上游端的角色
	  返回值: in :true  false   是否拥有
      channel_id :所拥有的通道id
	*/

    public function  upper_role($user_id){
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

    /**
     * @return int
     * 判断app端是否有权限权限
     */
    public function have_app_login_rights($user_id,$type){
        $type = strtoupper($type);			//将指定名称转大写
        $appoint_role = C($type);				//获取指定信息
        $where['role_id'] = array('in',$appoint_role);
        $where['user_id'] = $user_id;
        $role_id_arr = M('sys_user_role')->field('role_id')->where($where)->select();
        if($user_id == 1 || $role_id_arr){
            return true;
        }else{
            return false;
        }
    }
    
}