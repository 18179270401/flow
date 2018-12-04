<?php

namespace Common\Model;
use Think\Model;

class EnterpriseModel extends Model{

    /**
     *  验证企业名称的唯一
     */
    public function check_enterprise_name($enterprise_name,$self_id = false){
        $map['status'] = array('neq',2);
        $map['enterprise_name'] = array('eq',$enterprise_name);
        if($self_id){
            $map['enterprise_id'] = array('neq',$self_id);
        }
        if(M('Enterprise')->where($map)->find()){
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
            $map['enterprise_id'] = array('neq',$self_id);
        }
        if(M('Enterprise')->where($map)->find()){
            return true;
        }else{
            return false;
        }
    }


    /**
     *  验证该企业是否在自己的权限内
     */
    public function is_os_enterprise_right($enterprise_id = 0){
        if($enterprise_id == 0 or $enterprise_id == ''){
            return false;
        }
        if(D('SysUser')->is_admin()){
            return true;
        }
        $map['user_id'] = array('eq',D('SysUser')->self_id());
        $map['enterprise_id'] = array('eq',$enterprise_id);
        if(M('EnterpriseUser')->where($map)->count()){
            return true;
        }else{
            return false;
        }
    }


    /**
     *  判断该企业是否是尚通直属的
     */
    public function is_top_enterprise($enterprise_id = 0){
        if($enterprise_id != '' and $enterprise_id != 0){
            $map['status'] = array('neq',2);
            $map['enterprise_id'] = $enterprise_id;
            $enterprise = M('enterprise')->where($map)->find();
            if($enterprise){
                $info = D('SysUser')->up_proxy_info();
                if($info){
                    if($info['proxy_id'] == $enterprise['top_proxy_id']){
                        return true;
                    }
                }
            }
        }
        return false;
    }


    /**
     *  判断该企业是否是自己代理商下级的代理商
     */
    public function is_bottom_enterprise($enterprise_id){

        $map['status'] = array('neq',2);
        $map['enterprise_id'] = array('eq',$enterprise_id);
        $map['top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
        
        if(M('Enterprise')->where($map)->count()){
            return true;
        }else{
            return false;
        }
    }


    public function enterprise_child_ids(){
        $proxy_child_ids = D('Proxy')->proxy_child_ids();
        $map['status'] = array('neq',2);
        $map['top_proxy_id'] = array('in',$proxy_child_ids);
        $map['approve_status']=1;
        $enterprise_list = M('Enterprise')->field('enterprise_id')->where($map)->select();
        $enterprise_ids = '';
        if($enterprise_list){
            foreach($enterprise_list as $v){
                $enterprise_ids .= ','.$v['enterprise_id']; 
            }
            $enterprise_ids = substr($enterprise_ids,1,strlen($enterprise_ids)-1);
        }
        
        return $enterprise_ids;
    }

    /*获取有操作权限的所有直营企业*/
    public function enterprise_child_direct_ids(){
        $top_proxy_ids = D('Proxy')->proxy_child_direct_ids();
        $map['status'] = array('neq',2);
        $map['top_proxy_id'] = array('in',$top_proxy_ids);
        $map['approve_status']=1;
        $enterprise_list = M('Enterprise')->field('enterprise_id')->where($map)->select();
        $enterprise_ids = '';
        if($enterprise_list){
            foreach($enterprise_list as $v){
                $enterprise_ids .= ','.$v['enterprise_id'];
            }
            $enterprise_ids = substr($enterprise_ids,1,strlen($enterprise_ids)-1);
        }
        return $enterprise_ids;
    }



    //通过proxy_id 获取其所有企业
    public function get_enterprise_child_ids($proxy_child_ids){
        $map['status'] = array('neq',2);
        $map['top_proxy_id'] = array('in',$proxy_child_ids);
        $map['approve_status']=1;
        $enterprise_list = M('Enterprise')->field('enterprise_id')->where($map)->select();
        $enterprise_ids = '';
        if($enterprise_list){
            foreach($enterprise_list as $v){
                $enterprise_ids .= ','.$v['enterprise_id'];
            }
            $enterprise_ids = substr($enterprise_ids,1,strlen($enterprise_ids)-1);
        }
        return $enterprise_ids;
    }
    
    /**
     *  获取用户对企业可操作的id序列
     */
    public function enterprise_ids(){
        if(!D('SysUser')->is_admin() && !D('SysUser')->is_all_enterprise(D('SysUser')->self_id())){
            //当不是管理员的时候
            $map['user_id'] = array('eq',D('SysUser')->self_id());
            $range_list = M('Enterprise_user')->distinct(true)->field('enterprise_id')->where($map)->select();
            
        }else{
            $map['status'] = array('neq',2);
            $map['top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
            $map['approve_status']=1;
            $range_list = M('Enterprise')->distinct(true)->field('enterprise_id')->where($map)->select();
        }

        $enterprise_ids = '';
            if($range_list){
                foreach($range_list as $v){
                    $enterprise_ids .= ','.$v['enterprise_id']; 
                }
                $enterprise_ids = substr($enterprise_ids,1,strlen($enterprise_ids)-1);
            }

            return $enterprise_ids;
    }
    
    /**
     *  获取用户对企业可操作的id序列
     */
    public function enterprise_ids2(){
    	if(!D('SysUser')->is_admin() && !D('SysUser')->is_all_enterprise(D('SysUser')->self_id())){
    		//当不是管理员的时候
    		$map['user_id'] = array('eq',D('SysUser')->self_id());
    		$range_list = M('Enterprise_user')->distinct(true)->field('enterprise_id')->where($map)->select();
    
    	}else{
    		$map['status'] = array('neq',2);
    		$map['top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
    		//$map['approve_status']=1;
    		$range_list = M('Enterprise')->distinct(true)->field('enterprise_id')->where($map)->select();
    	}
    
    	$enterprise_ids = '';
    	if($range_list){
    		foreach($range_list as $v){
    			$enterprise_ids .= ','.$v['enterprise_id'];
    		}
    		$enterprise_ids = substr($enterprise_ids,1,strlen($enterprise_ids)-1);
    	}
    
    	return $enterprise_ids;
    }

	/**
	 * 获取某个代理商下面所有企业数据
     * $top_proxy_id:上级代理商id,$type=="discount"表示需要设置数据权限
	 */
	public function get_enterprise_by_tpid($top_proxy_id,$type=null) {
		$where['status'] = 1; //正常
        $where['approve_status'] = 1; //审核通过
		$where['top_proxy_id'] = $top_proxy_id;
        //判断代理
        if($type=="discount"){
            if(2==D("SysUser")->self_user_type()){
                $ids=D("Enterprise")->enterprise_ids();//获取该用户可操作的企业号
                $where['enterprise_id']=array("in",$ids);
            }
        }
		$infoall = D('Enterprise')->where($where)->select();
		$ret = !empty($infoall) ? $infoall : array();
		return $ret;
	}

    /**
     * 获取所有直营代理商下面的企业IDs
     */
    public function get_direct_enterprise_ids() {
        $ret = array();
        $sql = "SELECT e.`enterprise_id`
                FROM t_flow_enterprise e
                INNER JOIN t_flow_proxy p ON e.`top_proxy_id`=p.`proxy_id`
                WHERE p.proxy_type = 1";
        $rt = M('')->query($sql);
        if(!empty($rt) && is_array($rt)) {
            foreach($rt as $k => $v) {
                $ret[] = $v['enterprise_id'];
            }
        }
        return $ret;
    }
	
	/**
	 * 获取某个企业数据
	 */
	public function enterpriseinfo($enterprise_id) {
		$info = D('Enterprise')->where(array('status'=>array('neq',2)))->find($enterprise_id);
		$ret = !empty($info) ? $info : array();
		return $ret;
	}
	
	/**
	 * 获取所有正常状态企业数据
	 */
	public function enterpriseall() {
		$where['status'] = 1;
		$infoall = D('Enterprise')->where($where)->select();
		$ret = !empty($infoall) ? $infoall : array();
		return $ret;
	}
	
	/**
	 * 获取某个用户所管理的所有企业ID
	 * @return array 1D
	 */
	public function get_enterpriseid_by_userid($user_id) {
		$ret = array();
		$cond = array(
				'user_id'	=> $user_id,
		);
		$rt = M('enterprise_user')->where($cond)->select();
		if(!empty($rt) && is_array($rt)) {
			foreach ($rt as $k => $v) {
				$ret[] = $v['enterprise_id'];
			}
		}
		return $ret;
	}
	
	/**
	 * 根据关键字查找属于自己名下的企业ID和名字
	 */
	public function get_enterprise_by_name($pekw, $top_proxy_id) {
		$cond = array(
				'status'			=> 1,
				'top_proxy_id'		=> $top_proxy_id,
				'enterprise_name'	=> array('like', "%{$pekw}%"),
		);
		$arrenterprise = M('enterprise')->where($cond)->field('enterprise_id,enterprise_name')->select();
		return empty($arrenterprise) ? array() : $arrenterprise;
	}

    /**
     *  删除某用户的所有企业关系
     */
    public function delete_user($user_id){
        $msg = '系统错误!';
        $status = 'error';
        $map['user_id']     =       $user_id;
        $count = M('Enterprise_user')->where($map)->count();
        if($count){
            if(M('Enterprise_user')->where($map)->delete() == $count){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
        
    }



    /**
     *  删除部分数据
     */
    public function delete_section($user_id,$ids){
        $map['user_id'] = array('eq',$user_id);
        $map['enterprise_id']  = array('in',$ids);
        $count = M('Enterprise_user')->where($map)->count();
        if($count){
            $delete = M('Enterprise_user')->where($map)->delete();
            if($count == $delete){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }





    public function tree_html($proxy){
        $type = isset($proxy['son'])? 'folder' : 'file';
        $code = ($proxy['proxy_code'] == 20000 )? '' : '('.$proxy['proxy_code'].')';
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


    /**
     *  树结构备用
     */
    public function enterprise_tree_bak($self,$data,$enterprise_list){

        $self['type'] = 'proxy';
        foreach($data as $k => $v){
            if($v['top_proxy_id'] == $self['proxy_id']){
                $v['type'] = 'proxy';
                $self['son'][] = $v;
            }
        }

        foreach($enterprise_list as $k => $v){
            if($v['top_proxy_id'] == $self['proxy_id']){
                $v['type'] = 'enterprise';
                $self['son'][] = $v;
            }
        }

        if($self['son']){
            foreach($self['son'] as $k => $v){
                if($v['type'] == 'proxy'){
                    $val = $this->enterprise_tree($v,$data,$enterprise_list);
                    if($val == null){
                        unset($self['son'][$k]);
                    }else{
                        $self['son'][$k] = $val;
                        if(!isset($self['son'][$k]['son'])){
                            array_push($self['son'],$self['son'][$k]);
                            unset($self['son'][$k]);
                        }
                    }
                }
            }
            if(empty($self['son'])){
                return null;
            }

        }else{
            return null;
        }

        return $self;
    }



     public function is_enterprise_type($enterprise_id){
        $map['enterprise.status'] = array('neq',2);
        $map['proxy.proxy_type'] = array('eq',1);
        $map['proxy.status'] = array('neq',2);
        $map['enterprise.enterprise_id'] = array('eq',$enterprise_id);

        if(M('Enterprise as enterprise')
            ->join('t_flow_proxy as proxy on proxy.proxy_id = enterprise.top_proxy_id')
            ->where($map)
            ->count()
            ){
            return true;
        }
        return false;
    }

    /**
     * 添加代理商打款账户
     */
    public function get_enterprise_set($post){
        //读取代理商设置
        $list = M("EnterpriseSet")->where(array('enterprise_id'=>$post['enterprise_id']))->find();
        if($list){
            M("EnterpriseSet")->where(array('set_id'=>$list['set_id']))->save($post);
        }else{
            M("EnterpriseSet")->add($post);
        }
        return true;
    }
    /**
     * 读取代理商打款账户
     */
    public function list_enterprise_set($enterprise_id){
        //读取代理商设置
        $list = M("EnterpriseSet")->where(array('enterprise_id'=>$enterprise_id))->find();
        return $list;
    }

    //初始化企业显示页面菜单
    public function set_enterprise_role($enterprise_id){
        $menu_ids=array();
        //$menu_ids=array(148,59,64,58,118,155,136,157);
        $map['sys_type']=3;//这里表示是企业
        $map['status']=1;//表示正常的
        $map['top_menu_id']=array("neq",0);
        $data=array();
        $menu_list=M("SysMenu")
            ->where($map)
            ->order("order_num asc")
            ->field("menu_id,menu_name,page_url,top_menu_id")
            ->select();
        foreach ($menu_list as $m){
            if(!in_array($m['menu_id'],$menu_ids) && !in_array($m['top_menu_id'],$menu_ids)){
                $m_p = array();
                $m_p['user_type'] = 2;
                $m_p['enterprise_id'] = $enterprise_id;
                $m_p['create_user_id'] = D('SysUser')->self_id();
                $m_p['create_date'] = date('Y-m-d H:i:s');
                $m_p['modify_user_id'] = D('SysUser')->self_id();
                $m_p['modify_date'] = date('Y-m-d H:i:s');
                $m_p['menu_id'] = $m['menu_id'];
                $m_p['menu_name'] = $m['menu_name'];
                $m_p['menu_url'] = $m['page_url'];
                array_push($data,$m_p);
            }
        }
        M("available_menu")->addAll($data);
    }
    

}