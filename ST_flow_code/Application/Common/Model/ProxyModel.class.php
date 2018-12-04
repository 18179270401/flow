<?php

namespace Common\Model;
use Think\Model;

class ProxyModel extends Model{

    /**
     *  验证企业名称的唯一
     */
    public function check_proxy_name($proxy_name,$self_id = false){
        $map['status'] = array('neq',2);
        $map['proxy_name'] = array('eq',$proxy_name);
        if($self_id){
            $map['proxy_id'] = array('neq',$self_id);
        }
        if(M('Proxy')->where($map)->find()){
            return true;
        }else{
            return false;
        }
    }


    /**
     *  验证企业电话的唯一
     */
    public function check_tel($tel,$self_id = false){
        $map['status'] = array('neq',2);
        $map['tel'] = array('eq',$tel);
        if($self_id){
            $map['proxy_id'] = array('neq',$self_id);
        }
        if(M('Proxy')->where($map)->find()){
            return true;
        }else{
            return false;
        }
    }


    /**
     *  通过ID获取编号
     */
    public function getproxy_code($proxy_id){
        $map['status'] = array('neq',2);
        $map['proxy_id'] = array('eq',$proxy_id);
        $proxy = M('Proxy')->field('proxy_code')->where($map)->find();
        if($proxy){
            return $proxy['proxy_code'];
        }else{
            return 0;
        }
    }

    /**
     * 获取某个代理商数据
     */
    public function getproxy_name_byid($proxy_id) {
        $info = M('Proxy')->where("proxy_id={$proxy_id}")->getField('proxy_name');
        $ret = !empty($info) ? $info : '';
        return $ret;
    }

    /**
     *  通过用户ID获取代理商编号
     */
    public function user_proxy_code($user_id){
        $map['status'] = array('neq',2);
        $map['user_id'] = array('eq',$user_id);
        $user = M('Sys_user')->field('proxy_id')->where($map)->find();
        if($user){
            return $this->getproxy_code($user['proxy_id']);
        }else{
            return 0;
        }
    }

    /**
     * 根据代理商proxy_code获取代理商IDs
     */
    public function get_proxyid_by_proxycode($proxy_code) {
        $ret = array();
        $cond = array(
            'proxy_code'    => array('like', "%{$proxy_code}%"),
        );
        $proxy_ids = M('proxy')->where($cond)->field('proxy_id')->select();
        if(!empty($proxy_ids) && is_array($proxy_ids)) {
            foreach($proxy_ids as $k => $v) {
                $ret[] = $v['proxy_id'];
            }
        }
        return $ret;
    }

    /**
     * 根据代理商proxy_name获取代理商IDs
     */
    public function get_proxyid_by_proxyname($proxy_name) {
        $ret = array();
        $cond = array(
            'proxy_name'    => array('like', "%{$proxy_name}%"),
        );
        $proxy_ids = M('proxy')->where($cond)->field('proxy_id')->select();
        if(!empty($proxy_ids) && is_array($proxy_ids)) {
            foreach($proxy_ids as $k => $v) {
                $ret[] = $v['proxy_id'];
            }
        }
        return $ret;
    }

    /**
     *  验证该企业是否在自己的权限内
     */
    public function is_os_proxy_right($proxy_id = 0){

        if($proxy_id == 0 or $proxy_id == ''){
            return false;
        }
        if(D('SysUser')->is_admin()){
            return true;
        }

        $map['user_id'] = array('eq',D('SysUser')->self_id());
        $map['proxy_id'] = array('eq',$proxy_id);

        if(M('ProxyUser')->where($map)->count()){
            return true;
        }else{
            return false;
        }
    }



    /**
     *  判断代理商是否是自己代理商下级的代理商
     */
    public function is_bottom_proxy($proxy_id){

        $map['status'] = array('neq',2);
        $map['proxy_id'] = array('eq',$proxy_id);
        $map['top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
        
        if(M('Proxy')->where($map)->count()){
            return true;
        }else{
            return false;
        }
    }

    public function proxy_child(){
        if(D('SysUser')->is_admin() or D('SysUser')->is_all_proxy(D('SysUser')->self_id())){
            $map['status'] = array('neq',2);
            $map['top_proxy_id'] = D('SysUser')->self_proxy_id();
            $map['approve_status'] =1;
            $range_list = M('Proxy')->field('proxy_id')->where($map)->select();
        }else{
            //当不是管理员的时候
            $map['user_id'] = array('eq',D('SysUser')->self_id());
            $range_list = M('Proxy_user')->distinct(true)->field('proxy_id')->where($map)->select();
        }
        $ids=get_array_column($range_list,'proxy_id');
        if($ids){
            return $ids;
        }else{
            return '';
        }

    }

    public function proxy_child_s(){
        if(D('SysUser')->is_admin() or D('SysUser')->is_all_proxy(D('SysUser')->self_id())){
            $map['status'] = array('neq',2);
            $map['top_proxy_id'] = D('SysUser')->self_proxy_id();
            $map['approve_status'] =1;
            $range_list = M('Proxy')->field('proxy_id')->where($map)->select();
        }else{
            //当不是管理员的时候
            $map['user_id'] = array('eq',D('SysUser')->self_id());
            $range_list = M('Proxy_user')->distinct(true)->field('proxy_id')->where($map)->select();
        }
        //$ids=get_array_column($range_list,'proxy_id');
        $proxy_ids = '';
        if($range_list){
            foreach($range_list as $v){
                $proxy_ids .= ','.$v['proxy_id'];
            }
            $proxy_ids = substr($proxy_ids,1,strlen($proxy_ids)-1);
        }
        if($proxy_ids){
            return $proxy_ids;
        }else{
            return '';
        }
    }

    /**
     *  获取操作用户对代理商可操作的id序列
     */
    public function proxy_child_ids(){
    if(D('SysUser')->is_admin() or D('SysUser')->is_all_proxy(D('SysUser')->self_id())){
        $map['status'] = array('neq',2);
        $map['top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
       // $map['approve_status']=1;
        $range_list = M('Proxy')->field('proxy_id')->where($map)->select();

    }else{
        //当不是管理员的时候
        $map['user_id'] = array('eq',D('SysUser')->self_id());
        $range_list = M('Proxy_user')->distinct(true)->field('proxy_id')->where($map)->select();
    }

    $proxy_ids = '';
    if($range_list){
        foreach($range_list as $v){
            $proxy_ids .= ','.$v['proxy_id'];
        }

        $proxy_ids = substr($proxy_ids,1,strlen($proxy_ids)-1);
    }
    if($proxy_ids){
        $proxy_child_ids = M('')->query("select getProxyChildList('$proxy_ids') as ids");
        return $proxy_child_ids[0]['ids'];
    }else{
        return '';
    }
}
/*获取有操作权限的所有直营代理商*/
    public function proxy_child_direct_ids(){
        $proxy_child_ids = D('Proxy')->proxy_child_ids();
        $where['proxy_id']=array('in',$proxy_child_ids);
        $where['proxy_level']=1;
        $where['proxy_type']=0;
        $where['approve_status']=1;
        $where['status'] = array('neq',2);
        $top_proxy_id=M('proxy')->field('proxy_id')->where($where)->select();
        $top_proxy_ids=get_array_column($top_proxy_id,'proxy_id');
        $top_proxy_ids=implode(',',$top_proxy_ids);
        return $top_proxy_ids;
    }

    /**
     *  通过proxy_id来获取对代理商可操作的id序列
     */
    public function get_proxy_child_ids($proxy_id){
        $map['status'] = array('neq',2);
        $map['top_proxy_id'] = array('eq',$proxy_id);
        $map['approve_status']=1;
        $range_list = M('Proxy')->field('proxy_id')->where($map)->select();
        $proxy_ids = '';
        if($range_list){
            foreach($range_list as $v){
                $proxy_ids .= ','.$v['proxy_id'];
            }

            $proxy_ids = substr($proxy_ids,1,strlen($proxy_ids)-1);
        }
        if($proxy_ids){
            $proxy_child_ids = M('')->query("select getProxyChildList('$proxy_ids') as ids");
            return $proxy_child_ids[0]['ids'];
        }else{
            return '';
        }
    }

    //代理端需要显示未审核的子代理商
    public function proxy_approve_child_ids(){
        if(D('SysUser')->is_admin() or D('SysUser')->is_all_proxy(D('SysUser')->self_id())){
            $map['status'] = array('neq',2);
            $map['top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
            //$map['approve_status']=0;
            $range_list = M('Proxy')->field('proxy_id')->where($map)->select();

        }else{
            //当不是管理员的时候
            $map['user_id'] = array('eq',D('SysUser')->self_id());
            $range_list = M('Proxy_user')->distinct(true)->field('proxy_id')->where($map)->select();
        }

        $proxy_ids = '';
        if($range_list){
            foreach($range_list as $v){
                $proxy_ids .= ','.$v['proxy_id'];
            }

            $proxy_ids = substr($proxy_ids,1,strlen($proxy_ids)-1);
        }
        if($proxy_ids){
            $proxy_child_ids = M('')->query("select getProxyChildList('$proxy_ids') as ids");
            return $proxy_child_ids[0]['ids'];
        }else{
            return '';
        }
    }
    
    /**
     * 获取某个用户所管理的所有企业ID
     * @return array 1D
     */
    public function get_proxyid_by_userid($user_id) {
    	$ret = array();
    	$cond = array(
    			'user_id'	=> $user_id,
    	);
    	$rt = M('proxy_user')->where($cond)->select();
    	if(!empty($rt) && is_array($rt)) {
    		foreach ($rt as $k => $v) {
    			$ret[] = $v['proxy_id'];
    		}
    	}
    	return $ret;
    }
    
    /**
     * 获取某个代理商数据
     */
    public function proxyinfo($proxy_id) {
    	$info = D('Proxy')->where(array('status'=>array('neq',2)))->find($proxy_id);
    	$ret = !empty($info) ? $info : array();
    	return $ret;
    }
    
    /**
     * 获取所有正常状态代理商数据
     */
    public function proxyall() {
    	$where['status'] = 1;
    	$infoall = D('Proxy')->where($where)->select();
    	$ret = !empty($infoall) ? $infoall : array();
    	return $ret;
    }
    
	/**
	 * 获取属于某个代理商下面所有正常状态代理商数据
     * $top_proxy_id:上级代理商id,$type=="discount"表示需要设置数据权限
	 */
	public function get_proxy_by_tpid($proxy_id,$type) {
		$where['status'] = 1; //正常
        $where['approve_status'] = 1; //审核通过
		$where['top_proxy_id'] = $proxy_id;
        if($type=="discount"){
            if(2==D("SysUser")->self_user_type()){
                $ids=D('Proxy')->proxy_child_ids();
                $where['proxy_id']=array("in",$ids);
            }
        }
		$infoall = D('Proxy')->where($where)->select();
		$ret = !empty($infoall) ? $infoall : array();
		return $ret;
	}
	
	/**
	 * 根据关键字查找属于自己名下的代理商ID和名字
	 */
	public function get_proxys_by_name($pekw, $top_proxy_id) {
		$cond = array(
				'status'		=> 1,
				'top_proxy_id'	=> $top_proxy_id,
				'proxy_name'	=> array('like', "%{$pekw}%"),
		);
		$arrproxy = M('proxy')->where($cond)->field('proxy_id,proxy_name')->select();
		return empty($arrproxy) ? array() : $arrproxy;
	}
	
	/**
	 * 获取某代理商/企业专属通道ID
	 */
	public function get_own_channelid($proxy_id, $enterprise_id) {
		$ret = $cond = array();
		if(!empty($proxy_id)) {
			$cond['user_type'] = 1;
			$cond['proxy_id']	= $proxy_id;
		} else {
			$cond['user_type'] = 2;
			$cond['enterprise_id']	= $enterprise_id;
		}
		
		$cuinfo = M('channel_user')->where($cond)->select();
		if(!empty($cuinfo) && is_array($cuinfo)) {
			foreach ($cuinfo as $k => $v) {
				$ret[] = $v['channel_id'];
			}
		}
		
		return $ret;
	}
    
    /**
     *  删除某用户的所有代理商关系
     */
    public function delete_user($user_id){
        $msg = '系统错误!';
        $status = 'error';
        $map['user_id']     =       $user_id;
        $count = M('ProxyUser')->where($map)->count();
        if($count){
            if(M('ProxyUser')->where($map)->delete() == $count){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
        
    }


    /**
     *  删除重复数据
     */
    public function delete_section($user_id,$ids){
        $map['user_id'] = array('eq',$user_id);
        $map['proxy_id']  = array('in',$ids);
        $count = M('Proxy_user')->where($map)->count();
        if($count){
            $delete = M('Proxy_user')->where($map)->delete();
            if($count == $delete){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }


    public function proxy_tree($self,$data){

        foreach($data as $k => $v){
            if($v['top_proxy_id'] == $self['proxy_id']){
                $self['son'][] = $v;
            }
        }

        if($self['son']){

            foreach($self['son'] as $k => $v){
                $self['son'][$k] = $this->proxy_tree($v,$data);
                /*
                if(!isset($self['son'][$k]['son'])){
                    array_push($self['son'],$self['son'][$k]);
                    unset($self['son'][$k]);
                }
                */
            }
        }

        return $self;
    }


    public function tree_html($proxy){
        $type = isset($proxy['son'])? 'folder' : 'file';
        $code = ($proxy['proxy_code'] == 20000)? '' : '('.$proxy['proxy_code'].')';

        $html = '<li><a onclick="loca('.$proxy['proxy_id'].',this)"><span class="'.$type.'"  style="white-space:nowrap;" title="'.$code.$proxy['proxy_name'].'">'.$code.$proxy['proxy_name'].'</span></a>';
        
        if(isset($proxy['son'])){
            $html .= '<ul>';
            foreach($proxy['son'] as $v){
                $html .= $this->tree_html($v);
            }
            $html .= '</ul>';
        }

        $html .='</li>';

        return $html;

    }



    public function is_proxy_type($proxy_id){
        $map['status'] = array('neq',2);
        $map['proxy_id'] = array('eq',$proxy_id);
        $map['proxy_type'] = array('eq',1);

        if(M('Proxy')->where($map)->count()){
            return true;
        }
        return false;
    }

    public function checkoperator($list,$top_proxy_id){
        $map['status'] = array('neq',2);
        $map['proxy_id'] = array('eq',$top_proxy_id);
        $map['approve_status'] = array('eq',1);
        $top_proxy = M('Proxy')->where($map)->find();

        if($top_proxy){
            $top_proxy_arr = explode(',', $top_proxy['operator']);
            if($top_proxy_arr){
                foreach($list as $k=>$v){
                    if(!in_array($v,$top_proxy_arr)){
                        return false;
                    }
                }
            }else{
                return false;
            }
            
        }else{
            return false;
        }
        return true;
    }
    
    /**
     * 添加代理商打款账户
     */
    public function get_proxy_set($post){
        //读取代理商设置
        $list = M("ProxySet")->where(array('proxy_id'=>$post['proxy_id']))->find();
        if($list){
            M("ProxySet")->where(array('set_id'=>$list['set_id']))->save($post);
        }else{
            M("ProxySet")->add($post);
        }
        return true;
    }
    /**
     * 读取代理商打款账户
     */
    public function list_proxy_set($proxy_id){
        //读取代理商设置
        $list = M("ProxySet")->where(array('proxy_id'=>$proxy_id))->find();
        return $list;
    }

    /**
     * 获取所有一级非直营代理商IDs
     */
    public function get_one_nd_proxyinfo() {
        $ret = array();
        $cond = array(
            'proxy_level'   => array('eq', 1), //一级代理商
            'proxy_type'    => array('eq', 0), //0：普通代理商
            'approve_status'=> array('eq', 1), //1：审核通过
            'status'        => array('eq', 1), //1：正常
        );
        $rt = M('proxy')->where($cond)->field('proxy_id')->select();
        if(!empty($rt) && is_array($rt)) {
            foreach($rt as $k => $v) {
                $ret[] = $v['proxy_id'];
            }
        }
        return $ret;
    }

    /**
     * 获取所有直营代理商数据(排除20000)
     */
    public function get_direct_enterprise() {
        $rt = M('proxy')->where("proxy_type=1 and proxy_level <> 0")->field('proxy_id,proxy_name')->select();
        return empty($rt) ? array() : $rt;
    }

    /**
     * 获取所有一级代理商IDs
     */
    public function get_lv1_proxyids() {
        $ret = array();
        $cond = array(
            'proxy_level'   => array('eq', 1), //一级代理商
            'approve_status'=> array('eq', 1), //1：审核通过
            'status'        => array('eq', 1), //1：正常
        );
        $rt = M('proxy')->where($cond)->field('proxy_id')->select();
        if(!empty($rt) && is_array($rt)) {
            foreach($rt as $k => $v) {
                $ret[] = $v['proxy_id'];
            }
        }
        return $ret;
    }

    /**
     * 获取某个代理商所有级别下级代理商IDs(1D)
     */
    public function get_proxy_child_list($proxy_id) {
        $ret = array();
        $proxy_child_ids = M('')->query("select getProxyChildList('{$proxy_id}') as ids");
        $str_ids = $proxy_child_ids[0]['ids'];
        if(!empty($str_ids)) {
            $ret = explode(',', $str_ids);
        }
        return $ret;
    }

    /**
     * 获取某个代理商所有级别下级企业IDs(1D)
     */
    public function get_enterprise_child_list($proxy_id) {
        $ret = array();

        $arr_einfo = M('enterprise')->where("top_proxy_id={$proxy_id}")->field('enterprise_id')->select();
        if(!empty($arr_einfo) && is_array($arr_einfo)) {
            foreach($arr_einfo as $k => $v) {
                $ret[] = $v['enterprise_id'];
            }
        }

        return $ret;
    }

    /**
     * 获取所有一级代理商+直营代理商
     */
    public function get_proxy_one_direct_proxy(){
        $list = M('proxy')->where("approve_status=1 and status=1 and ((proxy_type=0 and proxy_level = 1) or (proxy_type=1 and proxy_level <> 0)) ")
            ->field('proxy_id,proxy_name')->select();
        $proxy_ids = get_array_column($list,'proxy_id');
        $proxy_ids = implode(',',$proxy_ids);
        return $proxy_ids;
    }


}