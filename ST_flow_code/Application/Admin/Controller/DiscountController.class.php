<?php

/**
 * DiscountController.class.php
 * 折扣管理操作控制器
 *
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;

class DiscountController extends CommonController {

    /**
     * 折扣管理列表
     */
    public function index() {
		D("SysUser")->sessionwriteclose();
    	$map = $proxyidname = $enterpriseidname = array();
    	$operator_id = I('operator_id');
    	$province_id = I('province_id');
		$city_id = I("city_id");
    	$user_type 	= I('user_type');
    	$user_code = trim(I('user_code'));
    	$user_type_name = trim(I('user_type_name'));
    	if(!empty($operator_id)) {
    		$map['d.operator_id'] = array('eq', $operator_id);
    	}
		if(!empty($city_id)){
			$map['d.city_id'] = $city_id;
			if($province_id==1){
				$map['d.province_id'] = $province_id;
			}else{
				$map['d.province_id']=0;
			}
		}else{
			if(!empty($province_id))$map['d.province_id'] = $province_id;
			if($province_id==1){
				$map['d.city_id'] = 0;
			}
		}
    	if($user_code != '') {
    		$map['p.proxy_code|e.enterprise_code'] = array('like', "%{$user_code}%");
    	}
    	if($user_type_name != '') {
    		$map['p.proxy_name|e.enterprise_name'] = array('like', "%{$user_type_name}%");
    	}
    	if(!empty($user_type)) {
    		$map['d.user_type'] = array('eq', $user_type);
    	}
		
	    $top_proxy_id = D('SysUser')->self_proxy_id(); //所属代理商
    	$map['p.top_proxy_id|e.top_proxy_id'] = array('eq', $top_proxy_id);
    	$pids=D("Proxy")->proxy_child_ids();
        if($pids){
            $map1['p.proxy_id']=array('in',$pids);
        }else{
            $map1['p.proxy_id']=array("in","0");
        }
        $map1['_logic']="or";
        $eids=D("Enterprise")->enterprise_ids();
        if($eids){
            $map1['e.enterprise_id']=array('in',$eids);
        }else{
            $map1['e.enterprise_id']=array('in',"0");
        }
        $map[]=$map1;
    	$count      = M('discount as d')
    					->join("left join ".C('DB_PREFIX')."proxy p on d.proxy_id = p.proxy_id")
    					->join("left join ".C('DB_PREFIX')."enterprise e on d.enterprise_id = e.enterprise_id")
    					->where($map)->field('d.*')->count();
        $Page       = new Page($count, 20);
        $show       = $Page->show();
        
        $model = M('discount');
    	$model->alias('d');
    	$model->join("left join ".C('DB_PREFIX')."sys_operator so on d.operator_id = so.operator_id");
    	$model->join("left join ".C('DB_PREFIX')."sys_province sp on d.province_id = sp.province_id");
		$model->join("left join ".C('DB_PREFIX')."sys_city sc on d.city_id = sc.city_id");
    	$model->join("left join ".C('DB_PREFIX')."proxy p on d.proxy_id = p.proxy_id");
    	$model->join("left join ".C('DB_PREFIX')."enterprise e on d.enterprise_id = e.enterprise_id");
    	$model->field('d.*,so.operator_name,sp.province_name,sc.city_name,p.proxy_name,p.proxy_code,e.enterprise_name,e.enterprise_code');
        $discount_list = $model->where($map)->limit($Page->firstRow.','.$Page->listRows)->select();
        
        $operator = D("ChannelProduct")->operatorall();//读取运营商
        $province = D("ChannelProduct")->provinceall();//读取省份
		//读取市
		if(!empty($province_id)){
			if($province_id!=1){
				$where['province_id']=$province_id;
			}
		}
		$citys=M("sys_city")->where($where)->select();
		$this->assign("citys",$citys);

        $arruser_type = array(
        		array('user_type_id'=>1, 'user_type_name'=>'代理商'),
        		array('user_type_id'=>2, 'user_type_name'=>'企业'),
        );

        //加载模板
        $this->assign('discount_list', get_sort_no($discount_list, $Page->firstRow));  //数据列表
        $this->assign('page',$show);
        $this->assign('operator',$operator);
        $this->assign('province',$province);
        $this->assign("user_type",D("SysUser")->self_user_type());
        $this->assign('arruser_type', $arruser_type);
        
        $this->display();         //模板
    }



	/**
	 * 导出excel
	 */
	public function export_excel() {
		$map = $proxyidname = $enterpriseidname = array();
		$operator_id = I('operator_id');
		$province_id = I('province_id');
		$user_type 	= I('user_type');
		$user_code = trim(I('user_code'));
		$user_type_name = trim(I('user_type_name'));
		$city_id = I('city_id');
		if(!empty($operator_id)) {
			$map['d.operator_id'] = array('eq', $operator_id);
		}
		if(!empty($city_id)){
			$map['d.city_id'] = $city_id;
			if($province_id==1){
				$map['d.province_id'] = $province_id;
			}else{
				$map['d.province_id'] = $province_id;
			}
		}else{
			if(!empty($province_id))$map['d.province_id'] = $province_id;
			if($province_id==1){
				$map['d.city_id'] = 0;
			}
		}
		if($user_code != '') {
			$map['p.proxy_code|e.enterprise_code'] = array('like', "%{$user_code}%");
		}
		if($user_type_name != '') {
			$map['p.proxy_name|e.enterprise_name'] = array('like', "%{$user_type_name}%");
		}
		if(!empty($user_type)) {
			$map['d.user_type'] = array('eq', $user_type);
		}

		$top_proxy_id = D('SysUser')->self_proxy_id(); //所属代理商
		$map['p.top_proxy_id|e.top_proxy_id'] = array('eq', $top_proxy_id);
        $pids=D("Proxy")->proxy_child_ids();
        if($pids){
            $map1['p.proxy_id']=array('in',$pids);
        }else{
            $map1['p.proxy_id']=array("in","0");
        }
        $map1['_logic']="or";
        $eids=D("Enterprise")->enterprise_ids();
        if($eids){
            $map1['e.enterprise_id']=array('in',$eids);
        }else{
            $map1['e.enterprise_id']=array('in',"0");
        }
        $map[]=$map1;
		$model = M('discount');
		$model->alias('d');
		$model->join("left join ".C('DB_PREFIX')."sys_operator so on d.operator_id = so.operator_id");
		$model->join("left join ".C('DB_PREFIX')."sys_province sp on d.province_id = sp.province_id");
		$model->join("left join ".C('DB_PREFIX')."sys_city sc on d.city_id = sc.city_id");
		$model->join("left join ".C('DB_PREFIX')."proxy p on d.proxy_id = p.proxy_id");
		$model->join("left join ".C('DB_PREFIX')."enterprise e on d.enterprise_id = e.enterprise_id");
		$model->field('d.*,so.operator_name,sp.province_name,sc.city_name,p.proxy_name,p.proxy_code,e.enterprise_name,e.enterprise_code');
		$discount_list = $model->where($map)->limit(3000)->select();
		$title='折扣管理';
		$list=array();
		$headArr=array("用户编号","用户名称","用户类型","运营商","省份","市","折扣数");

		foreach($discount_list as $k=>$v){

			if($v['proxy_code']){
				$list[$k]['code'] =$v['proxy_code'];
			}else{
				$list[$k]['code'] =$v['enterprise_code'];
			}
			if($v['proxy_name']){
				$list[$k]['name'] =$v['proxy_name'];
			}else{
				$list[$k]['name'] =$v['enterprise_name'];
			}
			if($v['user_type']==1){
				$list[$k]['action_url'] ='代理商';
			}else{
				$list[$k]['action_url'] ='企业';
			}
			$list[$k]['operator_name'] = $v['operator_name'];
			$list[$k]['province_name'] = get_city_province_name($v['city_id'],$v['province_id']);
			$list[$k]['city_name']=$v['city_name'];
			$list[$k]['discount_number'] = show_discount_ten($v['discount_number']).' 折';
		}
		ExportEexcel($title,$headArr,$list);

	}
    
    /**
     * 查看折扣数据
     */
    public function show() {
    	$info = D('Discount')->discountdetail(I('discount_id',0,'int'));
    	//当菜单不存在时
    	if($info){
    		$this->assign($info);
    		$this->display('show');
    	}else{
    		$this->error('折扣不存在！');
    	}
    }

    /**
     * 搜索
     */
    public function searchpe() {
    	$top_proxy_id = D('SysUser')->self_proxy_id(); //所属代理商
    	$pekw = trim(I('seape_kw'));
    	$arrproxy = D('Proxy')->get_proxys_by_name($pekw, $top_proxy_id);
    	$arrenterpirse = D('Enterprise')->get_enterprise_by_name($pekw, $top_proxy_id);
    		
    	$sdata = array('arrproxy' => $arrproxy, 'arrenterpirse' => $arrenterpirse);
    	//$sdata = array_merge($arrproxy, $arrenterpirse);
    	write_error_log($sdata);
    	$this->ajaxReturn(array('msg'=>'名字','status'=>'success','data'=>$sdata));
    }
    
    /**
     * 折扣添加模板
     */
    public function add() {

    	$top_proxy_id = D('SysUser')->self_proxy_id(); //所属代理商
        $create_proxy_info = D('Proxy')->proxyinfo($top_proxy_id);
    	$enterprise_list = D('Enterprise')->get_enterprise_by_tpid($top_proxy_id,"discount");
    	$proxy_list = D('Proxy')->get_proxy_by_tpid($top_proxy_id,"discount");
    	
        //读取运营商
        $operator = D("ChannelProduct")->operatorall();
        //读取省份
        $province = D("ChannelProduct")->provinceall();
        //读取通道
        $channel = D("Channel")->channelall();
        
        //$this->assign('proxy_list', $proxy_list);
        //$this->assign('enterprise_list', $enterprise_list);
        $this->assign('agency_list', $proxy_list);
        $this->assign('company_list', $enterprise_list);
        $this->assign('num', count($proxy_list));
        
        $this->assign('operator',$operator);
        $this->assign('province',$province);
        $this->assign('channel',$channel);
        $this->assign('create_proxy_info', $create_proxy_info);
		//读取折扣所需要的省信息
		$this->assign('province_list', province_list());

        $this->display();
    }
    
    /**
     * 折扣添加功能
     */
    public function insert() {
    	$msg = '系统错误！';
    	$status = 'error';
    	if(IS_POST) {
    		$arrins = $insdata = $condold0 = $arroldpn = array();
    		$csign = 1; //是否判断折扣大小
    		
    		$post = I('post.');
    		$discount_operator_id 	= $post['discount_operator_id'];
    		$proxy_ids 				= $post['proxy_ids'];//代理商IDs
    		$enterprise_ids 		= $post['enterprise_ids'];//企业IDs
    		$provinces = D("ChannelProduct")->provinceall(); //所有省份
    		$operator = get_operator_name(); //所有运营商
    		if(!empty($proxy_ids) || !empty($enterprise_ids)) {
    			$arr_province = array();
    			$super_proxy_id 	= M('proxy')->where(array('top_proxy_id'=>0,'status'=>1))->getField('proxy_id');
    			$create_proxy_id 	= D('SysUser')->self_proxy_id();
    			$create_user_id 	= D('SysUser')->self_id();
    			$create_date 		= date('Y-m-d H:i:s');
    			$modify_user_id 	= D('SysUser')->self_id();
    			$modify_date 		= date('Y-m-d H:i:s');
    
    			if($create_proxy_id != $super_proxy_id) {
    				$condold0['proxy_id']	= $create_proxy_id;
    				//$condold0['operator_id']= $discount_operator_id;
    				$arrold0 = M('discount')->where($condold0)->select();
    				if(!empty($arrold0) && is_array($arrold0)) {
    					foreach ($arrold0 as $k1 => $v1) {
    						$oldidx = $v1['operator_id'].$v1['province_id'];
    						$arroldpn[$oldidx] = $v1['discount_number'];
    					}
    				}
    			} else {
    				$csign = 0;
    			}

                $create_proxy_info = D('Proxy')->proxyinfo($create_proxy_id);
    
    			$rp = false; //是否填了折扣值
    			$rb = true; //折扣值大小
                if(1 != $create_proxy_info['proxy_type']) {
                    foreach ($operator as $op_id => $op_name) {
                        foreach ($provinces as $k2 => $v2) {
                            $oldidx = $op_id.$v2['province_id'];
                            $dnid = 'discount_number'.$oldidx;
                            $discount_number = floatval($post[$dnid]/10);
                            if($discount_number > 0 && $discount_number <= 1 && $csign==1) {
                                if(!isset($arroldpn[$oldidx])) {
                                    if($discount_number < 1) {
                                        $rb = false;
                                        break;
                                    }
                                } else {
                                    if( strval($discount_number) < $arroldpn[$oldidx]) {
                                        $rb = false;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }

    			if($rb) {
    				foreach ($operator as $op_id => $op_name) {
    					foreach ($provinces as $k2 => $v2) {
    						$province_id = $v2['province_id'];
    						$dnid = 'discount_number'.$op_id.$province_id;
    						$discount_number = floatval($post[$dnid]/10);
    						if($discount_number > 0 && $discount_number <= 1) {
    							$rp = true;
    							if(count($proxy_ids) >= count($enterprise_ids)) {
    								foreach ($proxy_ids as $k => $v) {
    									$arrins[] = array(
    											'user_type'			=> 1,
    											'proxy_id'			=> $v,
    											'enterprise_id'		=> 0,
    											'operator_id'		=> $op_id,
    											'province_id'		=> $province_id,
    											'discount_number'	=> $discount_number,
    											'create_proxy_id'	=> $create_proxy_id,
    											'create_user_id'	=> $create_user_id,
    											'create_date'		=> $create_date,
    											'modify_user_id'	=> $modify_user_id,
    											'modify_date'		=> $modify_date,
    									);
    									if(isset($enterprise_ids[$k]) && $enterprise_ids[$k] > 0) {
    										$arrins[] = array(
    												'user_type'			=> 2,
    												'proxy_id'			=> 0,
    												'enterprise_id'		=> $enterprise_ids[$k],
    												'operator_id'		=> $op_id,
    												'province_id'		=> $province_id,
    												'discount_number'	=> $discount_number,
    												'create_proxy_id'	=> $create_proxy_id,
    												'create_user_id'	=> $create_user_id,
    												'create_date'		=> $create_date,
    												'modify_user_id'	=> $modify_user_id,
    												'modify_date'		=> $modify_date,
    										);
    									}
    								}
    							} else {
    								foreach ($enterprise_ids as $k => $v) {
    									$arrins[] = array(
    											'user_type'			=> 2,
    											'proxy_id'			=> 0,
    											'enterprise_id'		=> $v,
    											'operator_id'		=> $op_id,
    											'province_id'		=> $province_id,
    											'discount_number'	=> $discount_number,
    											'create_proxy_id'	=> $create_proxy_id,
    											'create_user_id'	=> $create_user_id,
    											'create_date'		=> $create_date,
    											'modify_user_id'	=> $modify_user_id,
    											'modify_date'		=> $modify_date,
    									);
    									if(isset($proxy_ids[$k]) && $proxy_ids[$k] > 0) {
    										$arrins[] = array(
    												'user_type'			=> 1,
    												'proxy_id'			=> $proxy_ids[$k],
    												'enterprise_id'		=> 0,
    												'operator_id'		=> $op_id,
    												'province_id'		=> $province_id,
    												'discount_number'	=> $discount_number,
    												'create_proxy_id'	=> $create_proxy_id,
    												'create_user_id'	=> $create_user_id,
    												'create_date'		=> $create_date,
    												'modify_user_id'	=> $modify_user_id,
    												'modify_date'		=> $modify_date,
    										);
    									}
    								}
    							}
    						}
    					}
    				}
    				 
    				if($rp) {
    					$condold = $delids = array();
    					 
    					!empty($proxy_ids) && $condold['proxy_id'] = array('in', implode(',', $proxy_ids));
    					!empty($enterprise_ids) && $condold['enterprise_id'] = array('in', implode(',', $enterprise_ids));
    					//$condold['operator_id'] = array('eq', $discount_operator_id);
    					$arrold = M('discount')->where($condold)->select();
    					foreach ($arrold as $k6 => $v6) {
    						foreach ($arrins as $k7 => $v7) {
    							if($v6['user_type']==$v7['user_type'] && $v6['proxy_id']==$v7['proxy_id'] && $v6['enterprise_id']==$v7['enterprise_id']
    									&& $v6['operator_id']==$v7['operator_id'] && $v6['province_id']==$v7['province_id'] && $v6['create_proxy_id']==$v7['create_proxy_id']
    							) {
    								$delids[] = $v6['discount_id'];
    							}
    						}
    					}
    					 
    					$model = M('');
    					$model->startTrans();
    					$ru = true;
    					if(!empty($delids) && is_array($delids)) {
    						$ru = M('discount')->where(array('discount_id'=>array('in',implode(',', $delids))))->delete();
    					}
    					$lastid = !empty($arrins) ? M('discount')->addAll($arrins) : true;
    					if(false !== $lastid && false !== $ru) {
    						S('discountall', null); //清缓存
    						$msg = '新增折扣成功！';
							$n_msg='成功';
    						$status = 'success';
    						$model->commit();
    					} else {
    						$msg = '新增折扣失败！';
    						$model->rollback();
							$n_msg='失败';
    					}
						$note='用户【'.get_user_name(D('SysUser')->self_id()).'】，新增折扣'.$n_msg;
						$this->sys_log('新增折扣',$note);
    				} else {
    					$msg = '请填写正确的折扣值(0到1之间)！';
    				}
    			} else {
    				$msg = '请填写不小于自己当前折扣值的折扣值！';
    			}
    		} else {
    			$msg = '请选择代理商或企业！';
    		}
    	}
    	if(IS_AJAX) $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    /*
     * 折扣添加功能
    public function insert1() {
        $msg = '系统错误！';
        $status = 'error';
        if(IS_POST) {
        	$arrins = $insdata = $condold0 = $arroldpn = array();
        	$csign = 1; //是否判断折扣大小
        	
        	$post = I('post.');
        	$discount_operator_id 	= $post['discount_operator_id'];
        	$proxy_ids 				= $post['proxy_ids'];//代理商IDs
        	$enterprise_ids 		= $post['enterprise_ids'];//企业IDs
        	$provinces = D("ChannelProduct")->provinceall();
        	if(!empty($proxy_ids) || !empty($enterprise_ids)) {
        		$arr_province = array();
        		$super_proxy_id 	= M('proxy')->where(array('top_proxy_id'=>0,'status'=>1))->getField('proxy_id');
        		$create_proxy_id 	= D('SysUser')->self_proxy_id();
        		$create_user_id 	= D('SysUser')->self_id();
        		$create_date 		= date('Y-m-d H:i:s');
        		$modify_user_id 	= D('SysUser')->self_id();
        		$modify_date 		= date('Y-m-d H:i:s');
        		
        		if($create_proxy_id != $super_proxy_id) {
	        		$condold0['proxy_id']	= $create_proxy_id;
	        		$condold0['operator_id']= $discount_operator_id;
	        		$arrold0 = M('discount')->where($condold0)->select();
        			if(!empty($arrold0) && is_array($arrold0)) {
        				foreach ($arrold0 as $k1 => $v1) {
        					$arroldpn[$v1['province_id']] = $v1['discount_number'];
        				}
        			}
        		} else {
        			$csign = 0;
        		}
        		
        		$rp = false; //是否填了折扣值
        		$rb = true; //折扣值大小
        		foreach ($provinces as $k2 => $v2) {
        			$province_id = $v2['province_id'];
        			$dnid = 'discount_number'.$province_id;
        			$discount_number = floatval($post[$dnid]);
        			if($discount_number > 0 && $discount_number <= 1 && $csign==1) {
        				if(!isset($arroldpn[$province_id])) {
	        				if($discount_number < 1) {
	        					$rb = false;
	        					break;
	        				}
	        			} else {
	        				if($discount_number < $arroldpn[$province_id]) {
	        					$rb = false;
	        					break;
	        				}
	        			}
        			}
        		}
        		
        		if($rb) {
        			foreach ($provinces as $k2 => $v2) {
        				$province_id = $v2['province_id'];
        				$dnid = 'discount_number'.$province_id;
        				$discount_number = floatval($post[$dnid]);
        				if($discount_number > 0 && $discount_number <= 1) {

        					$arr_province[] = $province_id;
        					$rp = true;
        					if(count($proxy_ids) >= count($enterprise_ids)) {
        						foreach ($proxy_ids as $k => $v) {
        							$arrins[] = array(
        									'user_type'			=> 1,
        									'proxy_id'			=> $v,
        									'enterprise_id'		=> 0,
        									'operator_id'		=> $discount_operator_id,
        									'province_id'		=> $province_id,
        									'discount_number'	=> $discount_number,
        									'create_proxy_id'	=> $create_proxy_id,
        									'create_user_id'	=> $create_user_id,
        									'create_date'		=> $create_date,
        									'modify_user_id'	=> $modify_user_id,
        									'modify_date'		=> $modify_date,
        							);
        							if(isset($enterprise_ids[$k]) && $enterprise_ids[$k] > 0) {
        								$arrins[] = array(
        										'user_type'			=> 2,
        										'proxy_id'			=> 0,
        										'enterprise_id'		=> $enterprise_ids[$k],
        										'operator_id'		=> $discount_operator_id,
        										'province_id'		=> $province_id,
        										'discount_number'	=> $discount_number,
        										'create_proxy_id'	=> $create_proxy_id,
        										'create_user_id'	=> $create_user_id,
        										'create_date'		=> $create_date,
        										'modify_user_id'	=> $modify_user_id,
        										'modify_date'		=> $modify_date,
        								);
        							}
        						}
        					} else {
        						foreach ($enterprise_ids as $k => $v) {
        							$arrins[] = array(
        									'user_type'			=> 2,
        									'proxy_id'			=> 0,
        									'enterprise_id'		=> $v,
        									'operator_id'		=> $discount_operator_id,
        									'province_id'		=> $province_id,
        									'discount_number'	=> $discount_number,
        									'create_proxy_id'	=> $create_proxy_id,
        									'create_user_id'	=> $create_user_id,
        									'create_date'		=> $create_date,
        									'modify_user_id'	=> $modify_user_id,
        									'modify_date'		=> $modify_date,
        							);
        							if(isset($proxy_ids[$k]) && $proxy_ids[$k] > 0) {
        								$arrins[] = array(
        										'user_type'			=> 1,
        										'proxy_id'			=> $proxy_ids[$k],
        										'enterprise_id'		=> 0,
        										'operator_id'		=> $discount_operator_id,
        										'province_id'		=> $province_id,
        										'discount_number'	=> $discount_number,
        										'create_proxy_id'	=> $create_proxy_id,
        										'create_user_id'	=> $create_user_id,
        										'create_date'		=> $create_date,
        										'modify_user_id'	=> $modify_user_id,
        										'modify_date'		=> $modify_date,
        								);
        							}
        						}
        					}
        				}
        			}
        			
        			if($rp) {
        				$condold = $delids = array();
        				 
        				!empty($proxy_ids) && $condold['proxy_id'] = array('in', implode(',', $proxy_ids));
        				!empty($enterprise_ids) && $condold['enterprise_id'] = array('in', implode(',', $enterprise_ids));
        				$condold['operator_id'] = array('eq', $discount_operator_id);
        				$arrold = M('discount')->where($condold)->select();
        				foreach ($arrold as $k6 => $v6) {
        					foreach ($arrins as $k7 => $v7) {
        						if($v6['user_type']==$v7['user_type'] && $v6['proxy_id']==$v7['proxy_id'] && $v6['enterprise_id']==$v7['enterprise_id']
        								&& $v6['operator_id']==$v7['operator_id'] && $v6['province_id']==$v7['province_id'] && $v6['create_proxy_id']==$v7['create_proxy_id']
        						) {
        							$delids[] = $v6['discount_id'];
        						}
        					}
        				}
        				 
        				$model = M('');
        				$model->startTrans();
        				$ru = true;
        				if(!empty($delids) && is_array($delids)) {
        					$ru = M('discount')->where(array('discount_id'=>array('in',implode(',', $delids))))->delete();
        				}
        				$lastid = !empty($arrins) ? M('discount')->addAll($arrins) : true;
        				if(false !== $lastid && false !== $ru) {
        					$msg = '新增折扣成功！';
        					$status = 'success';
        					$model->commit();
        				} else {
        					$msg = '新增折扣失败！';
        					$model->rollback();
        				}
        			} else {
        				$msg = '请填写正确的折扣值(0到1之间)！';
        			}
        		} else {
        			$msg = '请填写不小于自己当前折扣值的折扣值！';
        		}
        	} else {
        		$msg = '请选择代理商或企业！';
        	}
        }
        if(IS_AJAX) $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    } */
    
    /**
     * 修改折扣模版
     */
    public function edit() {
    	$discount_id = I('discount_id',0,'int');
    	//$discountinfo = D('discount')->where(array('discount_id'=>$discount_id))->find();
    	$map = array('discount_id'=>$discount_id);
    	$model = M('discount');
    	$model->alias('d');
    	$model->join("left join ".C('DB_PREFIX')."sys_operator so on d.operator_id = so.operator_id");
    	$model->join("left join ".C('DB_PREFIX')."sys_province sp on d.province_id = sp.province_id");
		$model->join("left join ".C('DB_PREFIX')."sys_city sc on d.city_id = sc.city_id");
    	$model->field('d.*,so.operator_name,sp.province_name');
    	$discountinfo = $model->where($map)->find();
    	empty($discountinfo) && $this->error('此折扣数据不存在！');
		if($discountinfo['city_id']!=0 && $discountinfo['province_id']!=1){
			$city=M("Sys_city")->where(array("city_id"=>$discountinfo['city_id']))->find();
			$discountinfo['province_id']=$city['province_id'];
			$citys=M("Sys_city")->where(array("province_id"=>$city['province_id']))->select();
			$this->assign("citys",$citys);
		}elseif($discountinfo['province_id']!=1){
			$citys=M("Sys_city")->where(array("province_id"=>$discountinfo['province_id']))->select();
			$this->assign("citys",$citys);
		}elseif($discountinfo['province_id']==1){
			$citys=M("Sys_city")->where()->select();
			$this->assign("citys",$citys);
		}
    	if(1 == $discountinfo['user_type']) {
    		$discountinfo['user_type_name'] = M('proxy')->where(array('proxy_id'=>$discountinfo['proxy_id']))->getField('proxy_name');
    	} else {
    		$discountinfo['user_type_name'] = M('enterprise')->where(array('enterprise_id'=>$discountinfo['enterprise_id']))->getField('enterprise_name');
    	}
    	
    	$this->assign('discountinfo', $discountinfo);
        $this->display();
    }

    /**
     * 修改折扣功能
     */
    public function update() {
    	$msg = '系统错误！';
    	$status = 'error';
    
    	if(IS_POST) {
    		$discount_id = I('post.discount_id',0,'int');
    		$discount_number = floatval(I('post.discount_number')/10);
    		$province_id = I('post.province_id');
			$city_id = I('post.city_id');
			$discount_new['discount_number']=$discount_number;
			$discount_new['province_id']=$province_id;
			$discount_new['city_id']=$city_id;
    		//id不能为0
			if(empty($province_id) && empty($city_id)){
				$this->ajaxReturn(array("msg"=>"省份或者市至少选择一个！","status"=>"error"));
			}
    		if(!empty($discount_id)){
    			 
    			//判断如果没有数据的话
    			$discountinfo = D('discount')->find($discount_id);
    			if($discountinfo) {

    				if($discount_number>0 && $discount_number<=2) {
    					$self_proxy_id = D('SysUser')->self_proxy_id();
    					
    					if($self_proxy_id == $discountinfo['create_proxy_id']) {
                            $create_proxy_info = D('Proxy')->proxyinfo($discountinfo['create_proxy_id']);
    						$self_user_type = D('SysUser')->self_user_type();
							$name=$discountinfo['user_type']==1?obj_name($discountinfo['proxy_id'],1):obj_name($discountinfo['enterprise_id'],2);
    						if(1 == $self_user_type || 1 == $create_proxy_info['proxy_type']) {
    							if($discount_number != $discountinfo['discount_number'] || $province_id != $discountinfo['province_id'] || $city_id!= $discountinfo['city_id']) {
									if($city_id){
										$map['city_id'] = array('eq',$city_id);
										if($province_id!=1){
											$province_id=0;
										}
											$map['province_id'] = array('eq',$province_id);
									}else{
										$city_id=0;
										$map['province_id'] = array('eq',$province_id);
										$map['city_id'] = array('eq',$city_id);
									}
									$map['discount_id']=array("neq",$discount_id);
									$map['user_type'] = $discountinfo['user_type'];
									$map['proxy_id'] = $discountinfo['proxy_id'];
									$map['enterprise_id'] = $discountinfo['enterprise_id'];
									$map['operator_id'] = $discountinfo['operator_id'];
									$d = D('discount')->where($map)->find();
									if($d){
										$msg = '编辑折扣失败，省市的折扣已存在！';
										$this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
										exit();
									}
    								$sdata = array(
											'province_id' => $province_id,
											'city_id' => $city_id,
    										'discount_number'	=> $discount_number,
    										'modify_user_id'	=> D('SysUser')->self_id(),
    										'modify_date'		=> date('Y-m-d H:i:s'),
    								);
    								$ret = M('discount')->where(array('discount_id'=>$discount_id))->save($sdata);
    								if($ret) {
    									S('discountall', null); //清缓存
    									$status = 'success';
    									$msg = '编辑折扣成功！';
										$n_msg='成功';
										$this->discount_record($discount_new,$discountinfo,"edit");
    								} else {
    									$msg = '编辑折扣失败！';
										$n_msg='失败';
    								}
    							} else {
    								$status = 'success';
    								$msg = '设置成功！';
									$n_msg='成功';
    							}
								$s_discount_number=$discount_number*10;
								$note='用户【'.get_user_name(D('SysUser')->self_id()).'】，编辑用户【'.$name.'】折扣【'.$s_discount_number.'】'.$n_msg;
								$this->sys_log('编辑折扣',$note);
    						} else {
								//判断是省折扣还是市折扣
								if($city_id){
									$map['city_id'] = array('eq',$city_id);
									if($province_id!=1){
										$province_id=0;
									}
									$map['province_id'] = array('eq',$province_id);
								}else{
									$city_id=0;
									$map['province_id'] = array('eq',$province_id);
									$map['city_id'] = array('eq',$city_id);
								}
    							$selfcond = array('proxy_id'=>$self_proxy_id,'operator_id'=>$discountinfo['operator_id'],'province_id'=>$province_id,"city_id"=>$city_id);
    							$self_discount_number = floatval(M('discount')->where($selfcond)->getField('discount_number'));
    							$self_discount_number = empty($self_discount_number) ? 1 : $self_discount_number;
    							//write_debug_log(array(__METHOD__.':'.__LINE__, 'discountnumber==='.$discount_number, 'self=='.$self_discount_number));
    							if($discount_number >= $self_discount_number) {
									$map['discount_id']=array("neq",$discount_id);
									$map['user_type'] = $discountinfo['user_type'];
									$map['proxy_id'] = $discountinfo['proxy_id'];
									$map['enterprise_id'] = $discountinfo['enterprise_id'];
									$map['operator_id'] = $discountinfo['operator_id'];
									$d = D('discount')->where($map)->find();
									if($d){
										$msg = '编辑折扣失败，省市的折扣已存在！';
										$this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
										exit();
									}
    								if($discount_number != $discountinfo['discount_number'] || $province_id != $discountinfo['province_id'] || $city_id!= $discountinfo['city_id']) {
    									$sdata = array(
												'province_id' => $province_id,
												'city_id' => $city_id,
    											'discount_number'	=> $discount_number,
    											'modify_user_id'	=> D('SysUser')->self_id(),
    											'modify_date'		=> date('Y-m-d H:i:s'),
    									);
    									$ret = M('discount')->where(array('discount_id'=>$discount_id))->save($sdata);
    									if($ret) {
    										S('discountall', null); //清缓存
    										$status = 'success';
    										$msg = '编辑折扣成功！';
											$n_msg='成功';
											$this->discount_record($discount_new,$discountinfo,"edit");
    									} else {
    										$msg = '编辑折扣失败！';
											$n_msg='失败';
    									}
    								} else {
    									$status = 'success';
    									$msg = '设置成功！';
										$n_msg='成功';
    								}
									$s_discount_number=$discount_number*10;
									$note='用户【'.get_user_name(D('SysUser')->self_id()).'】，编辑用户【'.$name.'】折扣【'.$s_discount_number.'】'.$n_msg;
									$this->sys_log('编辑折扣',$note);
    							} else {
    								$msg = '给下级设置折扣不能比自身折扣低！';
    							}
    						}
    					} else {
    						$msg = '非法操作他人数据！';
    					}
    				} else {
    					$msg = '折扣值不合法，要求(0~20]之间！';
    				}
    			} else {
    				$msg = '折扣数据不存在！';
    			}
    		} else {
    			$msg = '折扣ID不存在！';
    		}
    		 
    		IS_AJAX && $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    	} else {
    		write_error_log(array(__METHOD__), '非POST传值');
    	}
    }
    
    public function check_user(){
        $msg = '系统错误！';
        $status = 'error';
        $type = I('post.type');
        $enterprise_ids = I('post.enterprise_ids');
        $proxy_ids = I('post.proxy_ids');
        if($type == 'check'){
            if($enterprise_ids or $proxy_ids){
                if($enterprise_ids){
                    $map['enterprise_id'] = array('in',$enterprise_ids);
                }
                if($proxy_ids){
                    $map['proxy_id'] = array('in',$proxy_ids);
                }
                if($proxy_ids && $enterprise_ids){
                    $map['_logic'] = 'OR';
                }

                $discount_list = M('Discount')
					->field('proxy_id,enterprise_id,operator_id,province_id,city_id,discount_number')->where($map)->select();

                if($discount_list){

                        if($discount_list[0]['proxy_id']){
                            $proxy_id = $discount_list[0]['proxy_id'];
                        }else{
                            $enterprise_id = $discount_list[0]['enterprise_id'];
                        }
                        if($proxy_id){
							//取出所以代理商的折扣信息
                            foreach($discount_list as $v){
                                if($v['proxy_id'] == $proxy_id){
                                    unset($v['proxy_id']);
                                    unset($v['enterprise_id']);
                                    $array[] = $v;
                                }
                            }
                            

                        }else{
							//取出所以企业折扣信息
                            foreach($discount_list as $v){
                                if($v['enterprise_id'] == $enterprise_id){
                                    unset($v['enterprise_id']);
                                    unset($v['proxy_id']);
                                    $array[] = $v;
                                }
                            }
                        }
                        foreach($discount_list as $v){
                            if($v['proxy_id']){
                                $id = $v['proxy_id'];
                                unset($v['enterprise_id']);
                                unset($v['proxy_id']);
                                $allarray['proxy'][$id][] = $v;
                            }else{
                                $id = $v['enterprise_id'];
                                unset($v['enterprise_id']);
                                unset($v['proxy_id']);
                                $allarray['enterprise'][$id][] = $v;
                            }
                        }
                        $is_no = false;
						$kcount = $count = 0;
						//$allarray和$array,其作用就是比较所有的代理商和企业是不是存在折扣不一样的
                        foreach($allarray as $v){
                            foreach($v as $vv){
                                $count++;
                                if($vv != $array){
                                    $is_no = true;
                                }
                            }
                        }
                        if($enterprise_ids){
                            $kcount += count(explode(',',$enterprise_ids));
                        }
                        if($proxy_ids){
                            $kcount +=count(explode(',',$proxy_ids));
                        }

                    if($kcount != $count){
                        $is_no = true;
                    }
                    if($is_no){
                        $msg = 'no';
                    }else{
                        
                        $msg = 'ok';
                    }
                    
                    $status = 'success';
                    $proxy_id = D('SysUser')->self_proxy_id();
                    $where = '(proxy_id = '.$proxy_id.') and ( ';
                        $i = 1;
                    foreach($array as $v){
                        $where.='( operator_id = '.$v['operator_id'].'  and  province_id = '.$v['province_id'].' and city_id='.$v['city_id'].')';
                        if($i != count($array) ){
                            $where .= ' or ';
                        }
                        $i++;

                    }
                    $where .= ')';
                    $discount_list = M('Discount')->where($where)->select();
                    foreach($array as $kk=>$vv){
                        foreach($discount_list as $v){
                            if($vv['province_id'] == $v['province_id'] && $vv['operator_id'] == $v['operator_id'] && $vv['city_id']==$v['city_id']){
                                $array[$kk]['mindiscount'] = $v['discount_number'];
                            }

                        }
                    }
					//加上省市的名称
					foreach($array as $kk=>$vv){
						switch (get_province_name($vv['province_id'])){
							case "新疆维吾尔自治区" :
								$array[$kk]['province_name']="新疆省";
								break;
							case "内蒙古自治区":
								$array[$kk]['province_name']="内蒙古";
								break;
							case "西藏自治区":
								$array[$kk]['province_name']="西藏省";
								break;
							case "广西壮族自治区";
								$array[$kk]['province_name']="广西省";
								break;
							case "宁夏回族自治区":
								$array[$kk]['province_name']="宁夏省";
								break;
							default:
								$array[$kk]['province_name']= get_province_name($vv['province_id']);
						}
						$array[$kk]['city_name']=get_city_name($vv['city_id']);
					}
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$array));
                }else{
                    $msg = 'ok';
                    $status = 'success';
                    $data =  array();
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                }

            }
        }elseif($type=='delete'){
            if($enterprise_ids or $proxy_ids){
                if($enterprise_ids){
                    $map['enterprise_id'] = array('in',$enterprise_ids);
                }
                if($proxy_ids){
                    $map['proxy_id'] = array('in',$proxy_ids);
                }
                if($proxy_ids && $enterprise_ids){
                    $map['_logic'] = 'OR';
                }
            }
			$discount_olds=M("Discount")->where($map)->select();
            $delete = M('Discount')->where($map)->delete();
            if($delete){
				$status = 'success';
				$this->discount_record(I("post."),$discount_olds,"delete");
			}
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>array()));
        }


        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }


    public function set(){

        $msg = '系统错误！';
        $status = 'error';
        $discount = I('post.discount');
        $proxy_ids = trim(I('post.proxy_ids'));
        $enterprise_ids = trim(I('post.enterprise_ids'));
        if($discount){
            foreach($discount as $v){
                if($v['discount_number'] > 20 || $v['discount_number'] < 0){
                    $this->ajaxReturn(array('msg'=>'请填写正确的折扣数，折扣的取值范围(0,20)！','status'=>$status));
                }
            }
            //$proxy_id = D('SysUser')->self_proxy_id();
            /*$where = '(proxy_id = '.$proxy_id.') and ( ';
                $i = 1;
            foreach($discount as $v){
                $where.='( operator_id = '.$v['operator_id'].'  and  province_id = '.$v['province_id'].' and  discount_number > '. $v['discount_number']/10 .'   )';
                if($i != count($discount) ){
                    $where .= ' or ';
                }
                $i++;

            }
            $where .= ')';*/

            $self_proxy_id = D('SysUser')->self_proxy_id();
            $create_proxy_info = D('Proxy')->proxyinfo($self_proxy_id);
            if(1 != $create_proxy_info['proxy_type']) { //不是直营代理商
                $msg = '给下级设置折扣不能比自身折扣低！';
                /*$check_count = M('Discount')->where($where)->count();
                if($check_count){
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                }*/

                $discountall = D('Discount')->discountall(); //所有已设置折扣值
                if(!empty($discountall) && is_array($discountall)) {
                    foreach($discount as $k1 => $v1) {
                        $vdc = 1;
                        foreach($discountall as $ka => $va) {
                            if($self_proxy_id == $va['proxy_id'] && $v1['operator_id']==$va['operator_id'] && $v1['province_id']==$va['province_id']&&$v1['city_id']==$va['city_id']) {
                                $vdc = $va['discount_number'];
                                break;
                            }
                        }
                        if($vdc > strval($v1['discount_number']/10)) {
                            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                        }
                    }
                } else {
                    foreach($discount as $k2 => $v2) {
                        if($v2['discount_number'] < 10){
                            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                        }
                    }
                }
            }

        }

        $model = M('');
        $model->startTrans();
        if($proxy_ids){
            $proxy_list = explode(',', $proxy_ids);
        }
        
        

        if($enterprise_ids){
            $enterprise_list = explode(',', $enterprise_ids);
        }
        if($enterprise_ids or $proxy_ids){
            if($enterprise_ids){
                $map['enterprise_id'] = array('in',$enterprise_ids);
            }
            if($proxy_ids){
                $map['proxy_id'] = array('in',$proxy_ids);
            }
            if($proxy_ids && $enterprise_ids){
                $map['_logic'] = 'OR';
            }
        }
        $count = M('Discount')->where($map)->count();
        $discount_old=M("Discount")->where($map)->select();//查出原来设置的折扣
        if($count){
            $delete_count = M('Discount')->where($map)->delete();
            if($count == $delete_count){
                $delete =  true;
            }else{
                $delete =  false;
            }
        }else{
            $delete = true;
        }

        $add_array = array();
		$proxy_discount=array();
		$enterprise_discount=array();
        $self_id = D('SysUser')->self_id();
        $time = date("Y-m-d H:i:s",time());
        $self_proxy_id = D('SysUser')->self_proxy_id();
        if($proxy_list){
            foreach($proxy_list as $v){
                foreach($discount as $dis){
                    $add_array[] = array(
                    'user_type'         =>      1,
                    'proxy_id'          =>      $v,
                    'enterprise_id'     =>      0,
                    'operator_id'       =>      $dis['operator_id'],
                    'province_id'       =>      $dis['province_id'],
					'city_id'			=>		$dis['city_id'],
                    'discount_number'   =>      $dis['discount_number']/10,
                    'create_proxy_id'   =>      $self_proxy_id,
                    'create_user_id'    =>      $self_id,
                    'create_date'       =>      $time,
                    'modify_user_id'    =>      $self_id,
                    'modify_date'       =>      $time,
                    );
					if($dis['city_id']!=0){
						$s='所属省【'.get_city_province_name($dis['city_id'],$dis['province_id']).'】，所属市【'.get_city_name($dis['city_id']).'】';
					}else{
						$s='所属省【'.get_province_name($dis['province_id']).'】';
					}
					$proxy_discount[]='代理商【'.obj_name($v,1).'】，运营商【'.get_operator_name($dis['operator_id']).'】，'.$s.'，折扣【'.($dis['discount_number']).'】折';
                }
                
            }
			$proxy_discounts=implode('；',$proxy_discount) ;
        }

        if($enterprise_list){
            foreach($enterprise_list as $v){
                foreach($discount as $dis){
                    $add_array[] = array(
                    'user_type'         =>      2,
                    'proxy_id'          =>      0,
                    'enterprise_id'     =>      $v,
                    'operator_id'       =>      $dis['operator_id'],
                    'province_id'       =>      $dis['province_id'],
					'city_id'			=>		$dis['city_id'],
                    'discount_number'   =>      $dis['discount_number']/10,
                    'create_proxy_id'   =>      $self_proxy_id,
                    'create_user_id'    =>      $self_id,
                    'create_date'       =>      $time,
                    'modify_user_id'    =>      $self_id,
                    'modify_date'       =>      $time,
                    );
					if($dis['city_id']!=0){
						$s='所属省【'.get_city_province_name($dis['city_id'],$dis['province_id']).'】，所属市【'.get_city_name($dis['city_id']).'】';
					}else{
						$s='所属省【'.get_province_name($dis['province_id']).'】';
					}
					$enterprise_discount[]='企业【'.obj_name($v,2).'】，运营商【'.get_operator_name($dis['operator_id']).'】，'.$s.'，折扣【'.($dis['discount_number']).'】折';
                }
            }
			$enterprise_discounts=implode('；',$enterprise_discount);
        }

        if($add_array){
            $add_count = M('Discount')->addAll($add_array);
            if($add_count){
                $add = true;
            }else{
                $add = false;
            }
        }else{
            $add = true;
        }

        if($delete && $add){
        	S('discountall', null); //清缓存
            $msg = '折扣设置成功！';
            $status = 'success';
            $model->commit();
			$this->discount_record(I('post.'),$discount_old,"set");
			$n_msg='成功';
        }else{
            $msg = '折扣设置失败！';
            $model->rollback();
			$n_msg='失败';
        }
		$e_data='';
		$e_data.=empty($proxy_discounts)?'':'设置代理商折扣：'.$proxy_discounts;
		$fg=empty($e_data)?'':'。';
		$e_data.=empty($enterprise_discounts)?'':$fg.'设置企业折扣：'.$enterprise_discounts;
		if($e_data=='。'){
            $e_data_msg='清除折扣';
		}else{
			$e_data_msg=$e_data;
		}
		$note='用户【'.get_user_name(D('SysUser')->self_id()).'】，折扣设置：'.$e_data_msg.$n_msg;
		$this->sys_log('折扣设置',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }
	/*
	 * 设置折扣记录:$post 新数据，$discount_old老数据，$type标志哪个操作传来的
	 */
	public function discount_record($post,$discount_old,$type)
	{
		$self_proxy_id = D('SysUser')->self_proxy_id();
		$self_id = D('SysUser')->self_id();
		$time = date("Y-m-d H:i:s", time());
		if ($type == "edit") {
			if ($post['discount_number'] != $discount_old['discount_number'] || (!empty($post['province_id'])||!empty($discount_old['province_id']))&&$post['province_id']!=$discount_old['province_id'] || (!empty($post['city_id'])&&!empty($discount_old['city_id']))&&$post['city_id']!=$discount_old['city_id']) {
				if ($discount_old['user_type'] == 1) {
					$data['proxy_id'] = $discount_old['proxy_id'];
					$data['enterprise_id'] = null;
					$data['user_type'] = 1;
				} else {
					$data['proxy_id'] = null;
					$data['enterprise_id'] = $discount_old['enterprise_id'];
					$data['user_type'] = 2;
				}
				$data['top_proxy_id'] = $self_proxy_id;
				$data['create_proxy_id'] = $self_proxy_id;    //创建代理商ID
				$data['create_user_id'] = $self_id;        //创建人ID
				$data['create_date'] = $time;        //创建时间
				$data['operator_id'] = $discount_old['operator_id'];        //所属运营商
				$data['province_id'] = $post['province_id'];        //所属地区
				$data['city_id'] = $post['city_id'];
				$data['discount_after'] = $post['discount_number'];        //操作后折扣
				$data['discount_before'] = $discount_old['discount_number'];
				if ($data['discount_after'] . "" != $data['discount_before'] . "" || (!empty($post['province_id'])||!empty($discount_old['province_id']))&&$post['province_id']!=$discount_old['province_id'] || (!empty($post['city_id'])&&!empty($discount_old['city_id']))&&$post['city_id']!=$discount_old['city_id']) {
					M("DiscountRecord")->add($data);
					$da[0]=$data;
					if((!empty($post['province_id'])||!empty($discount_old['province_id']))&&$post['province_id']!=$discount_old['province_id'] || (!empty($post['city_id'])&&!empty($discount_old['city_id']))&&$post['city_id']!=$discount_old['city_id']){
						D("Discount")->update_all_user($data['proxy_id'],$da,"discount",$discount_old);
					}else{
						D("Discount")->update_all_user($data['proxy_id'],$da,"discount");
					}
				}
			}
		}elseif ($type == "delete") {
			$all=array();
			foreach ($discount_old as $do){
				$data['proxy_id'] = $do['proxy_id'];
				$data['enterprise_id'] = $do['enterprise_id'];
				$data['user_type'] =$do['user_type'];
				$data['top_proxy_id'] = $self_proxy_id;
				$data['create_proxy_id'] = $self_proxy_id;    //创建代理商ID
				$data['create_user_id'] = $self_id;        //创建人ID
				$data['create_date'] = $time;        //创建时间
				$data['operator_id'] = $do['operator_id'];        //所属运营商
				$data['province_id'] = $do['province_id'];        //所属地区
				$data['city_id']	= $do['city_id'];
				$data['discount_after'] = 1;        //操作后折扣
				$data['discount_before'] = $do['discount_number'];
				array_push($all,$data);
			}
			M("DiscountRecord")->addAll($all);
			$proxy_ids = $post['proxy_ids'];
			D("Discount")->update_all_user($proxy_ids,$all,"discount");//修改下级代理商和企业的折扣
		}else {
			$proxy_ids = $post['proxy_ids'];
			$enterprise_ids = $post['enterprise_ids'];
			$discount = $post['discount'];
			if (!empty($proxy_ids) && $proxy_ids != "") {
				$proxyids = explode(",", $proxy_ids);
			} else {
				$proxyids = "";
			}
			if (!empty($enterprise_ids) && $enterprise_ids != "") {
				$enterpriseids = explode(",", $enterprise_ids);
			} else {
				$enterpriseids = "";
			}
			$map = array();
			//修改代理商所有折扣记录
			if ($proxyids != "") {
				foreach ($proxyids as $v) {
					//修改后不是10折
					foreach ($discount as $d) {
						$data = array();
						$data['proxy_id'] = $v;
						$data['enterprise_id'] = null;
						$data['user_type'] = 1;
						$data['top_proxy_id'] = $self_proxy_id;
						$data['create_proxy_id'] = $self_proxy_id;    //创建代理商ID
						$data['create_user_id'] = $self_id;        //创建人ID
						$data['create_date'] = $time;        //创建时间
						$data['operator_id'] = $d['operator_id'];        //所属运营商
						$data['province_id'] = $d['province_id'];        //所属地区
						$data['city_id']=$d['city_id'];
						//if($city_id>1)
						$data['discount_after'] = $d['discount_number'] / 10;        //操作后折扣
						foreach ($discount_old as $do) {
							if ($d['operator_id'] == $do['operator_id'] && $d['province_id'] == $do['province_id'] && $v == $do['proxy_id']&&$d['city_id']==$do['city_id']) {
								$data['discount_before'] = $do['discount_number'];    //操作前折扣
								break;
							}
						}
						if (empty($data['discount_before'])) {
							$data['discount_before'] = 1;
						}
						if ($data['discount_after']."" != $data['discount_before']."") {
							array_push($map, $data);
						}
					}
					//修改后是10折
					foreach ($discount_old as $do) {
						$data = array();
						$data['proxy_id'] = $v;
						$data['enterprise_id'] = null;
						$data['user_type'] = 1;
						$data['top_proxy_id'] = $self_proxy_id;
						$data['create_proxy_id'] = $self_proxy_id;    //创建代理商ID
						$data['create_user_id'] = $self_id;        //创建人ID
						$data['create_date'] = $time;        //创建时间
						$i = 1;
						$data['operator_id'] = $do['operator_id'];        //所属运营商
						$data['province_id'] = $do['province_id'];        //所属地区
						$data['city_id'] = $do['city_id'];
						$data['discount_after'] = 1;        //操作后折扣
						$data['discount_before'] = $do['discount_number'];
						foreach ($discount as $d) {
							if ($d['operator_id'] == $do['operator_id'] && $d['province_id'] == $do['province_id'] && $d['city_id']==$do['city_id']) {
								$i = 2;
								break;
							}
						}
						if ($i == 1) {
							array_push($map, $data);
						}
					}
				}
			}
			if ($enterpriseids != "") {
				foreach ($enterpriseids as $v) {
					//修改后不是10折
					foreach ($discount as $d) {
						$data = array();
						$data['proxy_id'] = null;
						$data['enterprise_id'] = $v;
						$data['user_type'] = 2;
						$data['top_proxy_id'] = $self_proxy_id;
						$data['create_proxy_id'] = $self_proxy_id;    //创建代理商ID
						$data['create_user_id'] = $self_id;        //创建人ID
						$data['create_date'] = $time;        //创建时间
						$data['operator_id'] = $d['operator_id'];        //所属运营商
						$data['province_id'] = $d['province_id'];        //所属地区
						$data['city_id'] = $d['city_id'];
						$data['discount_after'] = $d['discount_number'] / 10;        //操作后折扣
						foreach ($discount_old as $do) {
							if ($d['operator_id'] == $do['operator_id'] && $d['province_id'] == $do['province_id'] && $v == $do['enterprise_id']&&$d['city_id']==$do['city_id']) {
								$data['discount_before'] = $do['discount_number'];    //操作前折扣
								break;
							}
						}
						if (empty($data['discount_before'])) {
							$data['discount_before'] = 1;
						}
						if ($data['discount_after']."" != $data['discount_before']."") {
							array_push($map, $data);
						}
					}
					//修改后是10折
					foreach ($discount_old as $do) {
						$data = array();
						$data['proxy_id'] = null;
						$data['enterprise_id'] = $v;
						$data['user_type'] = 2;
						$data['top_proxy_id'] = $self_proxy_id;
						$data['create_proxy_id'] = $self_proxy_id;    //创建代理商ID
						$data['create_user_id'] = $self_id;        //创建人ID
						$data['create_date'] = $time;        //创建时间
						$i = 1;
						$data['operator_id'] = $do['operator_id'];        //所属运营商
						$data['province_id'] = $do['province_id'];        //所属地区
						$data['city_id'] = $do['city_id'];
						$data['discount_after'] = 1;        //操作后折扣
						$data['discount_before'] = $do['discount_number'];
						foreach ($discount as $d) {
							if ($d['operator_id'] == $do['operator_id'] && $d['province_id'] == $do['province_id']&& $d['city_id']==$do['city_id']) {
								$i = 2;
								break;
							}
						}
						if ($i == 1) {
							array_push($map, $data);
						}
					}
				}
			}
			//print_r($map);
			M("DiscountRecord")->addAll($map);
			D("Discount")->update_all_user($proxy_ids,$map,"discount");
		}
	}
	public function get_proxy_discount(){
		$province_id= I('post.province_id');
		$city_id = I("post.city_id");
		$operator_id=I("post.operator_id");
		if(empty($city_id)){
			$city_id=0;
		}
		$proxy_id=D("SysUser")->self_proxy_id();
		$map['province_id']=$province_id;
		$map['city_id']=$city_id;
		$map['proxy_id']=$proxy_id;
		$map['operator_id']=$operator_id;
		$mod=M("discount")->where($map)->field("discount_number")->find();
		if($mod){
			$info=$mod['discount_number'];
		}else{
			$info=1;
		}
		$this->ajaxReturn(array("msg"=>"成功","status"=>"success","info"=>$info));
	}
}