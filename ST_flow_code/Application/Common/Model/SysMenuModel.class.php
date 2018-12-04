<?php

namespace Common\Model;
use Think\Model;

class SysMenuModel extends Model{

	/**
     * 获取节点详细信息
	 */
	public function menuinfo($menu_id){
		$info = D('SysMenu')->find($menu_id);
		if($info['status'] == 2){
			return '';
		}else{
			return $info;
		}
	}


	/*显示信息*/
	public function show($where){
		$list = M('Sys_function as a')
			->join("t_flow_sys_menu as b on a.menu_id=b.menu_id","left")
			->where($where)
			->field("a.*,b.menu_name,b.sys_type")
			->find();
		return $list;
	}

	/**
	 *	将菜单分别以是否管理员为区别形成二叉树放置前端
	 */
	public function getmenuall() {
		//通过用户的类型判断显示的菜单
		$map['sys_type'] = D('SysUser')->self_user_type();
		$map['status'] = array('eq',1);
	
		$menulist = D('SysMenu')->where($map)->select();
		//判断是否是管理员 如果是管理员则直接读取所有的菜单
		
		foreach($menulist as $key=>$value){
			if($value['menu_type'] == 2 && empty($value['page_url'])){
				unset($menulist[$key]);
			}
		}
		
		return $this->tree($menulist);
	}
	
	/**
	 *	将菜单分别以是否管理员为区别形成二叉树放置前端(去掉了无权限的)
	 */
	public function getmenuall2() {
		$sys_type = D('SysUser')->self_user_type(); //获取自身的用户类型是运营平台，代理商，企业
		$need_del = array();
		if($sys_type == 2) { //代理商端需要删掉
			$self_proxy_id = D('SysUser')->self_proxy_id();
			$proxy_info = D("Proxy")->proxyinfo($self_proxy_id);
			if(0 == $proxy_info['proxy_type']) { //普通代理商
				if(1 == $proxy_info['proxy_level']) {
					//$need_del[] = 'ProxyIncome/index';
					//$need_del[] = 'enterprisewithdrawals/index';
				} else if($proxy_info['proxy_level'] > 1){
					//$need_del[] = 'ProxyIncome/index';
					//$need_del[] = 'enterprisewithdrawals/index';
					//$need_del[] = 'proxywithdrawals/index';
					//$need_del[] = 'proxyrecharge/index';
				}
				$need_del[] = 'proxyincome/index';
			}
		}
		
		//通过用户的类型判断显示的菜单
		$map['sys_type'] = $sys_type;
		$map['status'] = array('eq',1);
	
		$menulist = D('SysMenu')->where($map)->order("order_num asc")->select();
		//判断是否是管理员 如果是管理员则直接读取所有的菜单
		
		foreach($menulist as $key => $value){
			if($value['menu_type'] == 2 && empty($value['page_url'])){
				unset($menulist[$key]);
			}
		}
		foreach ($menulist as $key => $value) {
			if(in_array(strtolower($value['page_url']), $need_del)) {
				unset($menulist[$key]);
			}
		}
		
		return $this->tree2($menulist);
	}
    
    /**
     * 读取顶级菜单
     */
    function topmenu(){
        $where['menu_type'] = 1;
        //$where['status'] = array("neq",2);
		$where['status'] = 1;
        $info = D("SysMenu")->where($where)->select();
        if(!$info){
            return '';
        }
        return $info;
    }

	/**
	 *	将菜单分别以是否管理员为区别形成二叉树放置前端
	 */
	public function getmenu(){
		//通过用户的类型判断显示的菜单
    	$map['sys_type'] = D('SysUser')->self_user_type();
    	$map['status'] = array('eq',1);

    	$menulist = D('SysMenu')->where($map)->order('order_num Asc')->select();
    	//判断是否是管理员 如果是管理员则直接读取所有的菜单

		$functionlist = D('SysFunction')->functionlist();

		foreach($menulist as $key=>$value){

			if($value['menu_type'] == 2 && ($value['page_url'] == '' or !in_array(strtolower($value['page_url']),$functionlist) ) ){

				unset($menulist[$key]);
			}
		}
		//运营端的菜单使用三级菜单显示
		if($map['sys_type']==1){
			return $this->tree($menulist);
		}else{
			return $this->tree($menulist);
		}
	}

	public function tree($menulist){
		foreach($menulist as $k=>$v){
			if($v['top_menu_id'] == 0){
				$top[] = $v;
			}
		}
		foreach($top as $tk=>$tv){
			foreach($menulist as $mk=>$mv){
				if($tv['menu_id'] == $mv['top_menu_id']){
					if($mv['group_name']!=""){
						$me[$mv['group_name']][] = $mv;
					}else{
						$me[] = $mv;
					}
				}
			}
			$top[$tk]['son'] = $me;
			unset($me);
			if(!$top[$tk]['son']){
				unset($top[$tk]);
			}
		}
		return $top;
	}


	public function tree2($menulist){
		foreach($menulist as $k=>$v){
			if($v['top_menu_id'] == 0){
				$top[] = $v;
			}
		}
		foreach($top as $tk=>$tv){
			foreach($menulist as $mk=>$mv){
				if($tv['menu_id'] == $mv['top_menu_id']){
					$top[$tk]['son'][] = $mv;
				}
			}
			if(!$top[$tk]['son']){
				unset($top[$tk]);
			}
		}
		return $top;
	}


}