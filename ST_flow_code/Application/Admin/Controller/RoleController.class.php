<?php

/**
 * RoleController.class.php
 * 角色操作控制器
 *
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class RoleController extends CommonController {

    /**
     * 角色列表
     */
    public function index() {
    	D("SysUser")->sessionwriteclose();
    	$status = I('status');
        $role_name = '';
        //搜索条件
        if('' == $status || 1 == $status) {
        	$map['role.status'] = array('eq', 1);
        	$status = 1;
        } else if(0 == $status) {
        	$map['role.status'] = array('eq', 0);
        	$status = 0;
        } else {
	        $map['role.status'] = array('neq',2);
        	$status = 9;
        }
        $role_name = trim(I('role_name'));
        ($role_name != '') && $map['role.role_name'] = array('like', "%{$role_name}%");

		$depart_id = trim(I('depart_id','-1','intval'));
		if('-1' != $depart_id) {
			$map['role.depart_id'] = $depart_id;
		}

		$root_user_id = D('SysUser')->root_user_id();
		$arr_depart = D('SysDepart')->get_user_depart($root_user_id);

        //没有删除
        $map['role.user_id'] =  array('eq',D('SysUser')->root_user_id());

        //调用分页类
        $count      = M('Sys_role as role')
                        ->join('t_flow_sys_depart as depart on depart.depart_id = role.depart_id and depart.status = 1','left')
                        ->where($map)
                        ->count();
        $Page       = new Page($count, 20);
        $show       = $Page->show();

        //获取所有角色列表
       $role_list = M('Sys_role as role')
            ->where($map)
            ->order('role.modify_date desc')
            ->field("role.role_name,role.remark,depart.depart_name,role.status,role.role_id")
            ->join('t_flow_sys_depart as depart on depart.depart_id = role.depart_id and depart.status = 1','left')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        if(!empty($role_list) && is_array($role_list)) {
        	foreach ($role_list as $kk => &$vv) {
        		$vv['user_role_sum'] = D('SysRole')->get_user_role_byid($vv['role_id']);
        	}
        }
        
        //加载模板
        $this->assign('role_list', get_sort_no($role_list, $Page->firstRow));  //数据列表
        $this->assign('page',$show);            //分页
        //$this->assign('role_name', $role_name);
        $this->assign('status', $status);
		$this->assign('arr_depart', $arr_depart);
		$this->assign('depart_id', $depart_id);
        $this->display('index');         //模板
    }

	/**
	 * 导出excel
	 */
	public function export_excel() {

		$status = I('status');
        $role_name = '';
        //搜索条件
        if('' == $status || 1 == $status) {
            $map['role.status'] = array('eq', 1);
            $status = 1;
        } else if(0 == $status) {
            $map['role.status'] = array('eq', 0);
            $status = 0;
        } else {
            $map['role.status'] = array('neq',2);
            $status = 9;
        }
        $role_name = trim(I('role_name'));
        ($role_name != '') && $map['role.role_name'] = array('like', "%{$role_name}%");

        $depart_id = trim(I('depart_id','-1','intval'));
        if('-1' != $depart_id) {
            $map['role.depart_id'] = $depart_id;
        }

        $root_user_id = D('SysUser')->root_user_id();
        $arr_depart = D('SysDepart')->get_user_depart($root_user_id);

        //没有删除
        $map['role.user_id'] =  array('eq',D('SysUser')->root_user_id());
		//获取所有角色列表
		$list = M('Sys_role as role')
			->where($map)
			->order('role.modify_date desc')
			->field("role.role_name,depart.depart_name,role.remark")
            ->join('t_flow_sys_depart as depart on depart.depart_id = role.depart_id and depart.status = 1','left')
			->limit(3000)
			->select();

		$title='角色权限';
		$headArr=array("角色名称","所属部门","备注");
		ExportEexcel($title,$headArr,$list);
	}

    
    /**
     * 角色添加功能模版
     */
    public function add() {
	    //部门列表
        $map = array();
        $map['user_id'] = array('eq',D('SysUser')->root_user_id());
        $map['status'] = array('eq',1);
        $depart_list = M('Sys_depart')->where($map)->select();
        $this->assign('depart_list',$depart_list);
    	$this->display();
    }
    
    /**
     * 角色添加功能
     */
    public function insert() {
    	if(IS_POST) {
    		$msg = '系统错误！';
    		$status = 'error';
    
    		$role_name = trim(I('post.role_name'));
    		$remark = trim(I('post.remark'));
            $depart_id = trim(I('post.depart_id'));             //部门ID

            //部门是否在自己的范围内
            if(!empty($depart_id) &&  !D('SysDepart')->is_me_depart($depart_id)){
                $this->ajaxReturn(array('msg'=>'请选择部门!','status'=>$status));
            }

    		//获取我的超级管理员是谁  也就是角色时归属哪个代理商的用户ID
    		$root_user_id = D('SysUser')->root_user_id();
    
    		if($root_user_id) {
    			//角色名称不能为空
    			if(!empty($role_name)){
    
    				//查询没有删除的数据中是否有重复角色名
    				$map['role_name'] = array('eq',$role_name);
    				$map['user_id'] = array('eq',$root_user_id);
    				$map['status'] = array('neq',2);
    
    				$roleinfo = M('Sys_role')->where($map)->find();
    				//var_dump( $roleinfo);exit;
    				if(!$roleinfo){
    
    					//添加数组
    					$add = array(
    							'user_id'			=>  $root_user_id,
    							'role_name'         =>  $role_name,
    							'status'			=>  1, //默认正常启用
    							'remark'            =>  $remark,
                                'depart_id'         =>  $depart_id,
    							'create_user_id'    =>  D('SysUser')->self_id(),
    							'create_date'       =>  date('Y-m-d H:i:s',time()),
    							'modify_user_id'    =>  D('SysUser')->self_id(),
    							'modify_date'       =>  date('Y-m-d H:i:s',time()),
    					);
    
    					//执行添加
						$id=M('Sys_role')->add($add);
    					if($id){
    						$msg = '新增角色成功！';
							$n_msg='成功';
    						$status = 'success';
    					}else{
    						$msg = '新增角色失败！';
							$n_msg='失败';
    
    					}
						$depart_info='';
						if($depart_id){
							$depart_info='，所属部门【'.get_depart_name($depart_id).'】';
						}
						$note='用户【' . get_user_name(D('SysUser')->self_id()) . '】，ID【'.$id.'】，新增角色,角色名称【'.$role_name.'】'.$depart_info.$n_msg;
						$this->sys_log('新增角色',$note);
    				}else{
    					$msg = '角色已存在,请勿重复添加';
    				}
    			}else{
    
    				$msg = '角色名称不能为空';
    			}
    		}
    
    		if(IS_AJAX){
    			$this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    		}
    	} else {
    		write_error_log(array(__METHOD__), '非POST传值');
    	}
    }
    

    /**
     * 获取角色已有功能ID数组
     */
    public function get_role_function() {
    	$role_id = I('role_id');
    	$arr = D('SysFunction')->get_functions_by_roleid($role_id);
    
    	$this->ajaxReturn(array('msg'=>'成功','status'=>'success','funids'=>$arr));
    }

    /**
     * 设置角色权限功能
     */
    public function set_role_page() {
    	if(IS_POST) {
    		$msg = '系统错误！';
    		$status = 'error';
    		
    		$role_id = I('post.role_id',0,'int');
    		$function_ids = I('post.function_ids');
    		
    		if(!empty($role_id)){
    			//查询当前是否有该角色
    			$roleinfo = D('SysRole')->find($role_id);
    			if($roleinfo){
    				$root_user_id = D('SysUser')->root_user_id();
    				if($roleinfo['user_id'] == $root_user_id) {
    					//将字符串转为数组
    					if( !is_array($function_ids)){
    						if(empty($function_ids)){
    							$function_ids = array();
    						}else{
    							$function_ids = explode(',',$function_ids);
    						}
    					}
    					
    					$check = true;
    					if(!empty($function_ids)){
    						$arr_sys_funid = D('SysFunction')->get_functions_by_usertype(); //获取当前用户所属平台端所有功能点
    						foreach ($function_ids as $key => $value) {
    							if(!is_numeric($value) || !in_array($value, $arr_sys_funid)){
    								$check = false;
    								break;
    							}
    						}
    					}
    					
    					if($check){
    						//当数组不为空时 判断权限是否都存在
    						if(count($function_ids) != 0){
    							$map['function_id']  = array('in',implode(',', $function_ids));
    							$map['status'] = array('neq',2);
    							//查询当前是否所有的角色都存在
    							if(count($function_ids) == M('Sys_function')->where($map)->count()){
    								if(D('SysFunction')->update_function($role_id,$function_ids)){
    									$msg = '角色设置权限成功!';
    									$status = 'success';
										$n_msg='成功';
    								}else{
    									$msg = '角色设置权限失败!';
										$n_msg='失败';
    								}
    							} else {
    								$msg = '更新失败,存在非法权限!';
									$n_msg='失败';
    							}
    						}else{
    							//当节点id为空时直接删除
    							if(D('SysFunction')->update_function($role_id,$function_ids)){
    								$msg = '角色设置权限成功!';
									$n_msg='成功';
    								$status = 'success';
    							}else{
    								$msg = '角色设置权限失败!';
									$n_msg='失败';
    							}
    						}
							$note='用户【' . get_user_name(D('SysUser')->self_id()) . '】，ID【'.$role_id.'】，角色【'.$roleinfo['role_name'].'】设置权限'.$n_msg;
							$this->sys_log('角色设置权限',$note);

    					} else {
    						$msg = '权限值非法';
    					}
    				} else {
    					$msg = '非法操作他人数据';
    				}
    			}else{
    				$msg = '不存在该角色';
    			}
    		} else {
    			$msg = '参数错误';
    		}

    		
    		if(IS_AJAX){
    			$this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    		}
    	} else {
    		$arrmenu = D('SysMenu')->getmenuall2(); //获取拥有的顶级菜单
    		if(!empty($arrmenu) && is_array($arrmenu)){
    			foreach ($arrmenu as $k => $v) {
    				foreach ($v['son'] as $kk => $vv) {
    					$arrmenu2ids[] = $vv['menu_id'];
    				}
    			}
    			$arr_function = M('SysFunction')
    							->where(array('status'=>array('eq',1),'menu_id'=>array('in',implode(',', $arrmenu2ids))))
    							->order('order_num asc')
    							->select();
    			if(!empty($arr_function) && is_array($arr_function)) {
    				foreach ($arr_function as $kkk => $vvv) {
    					$arrfun[$vvv['menu_id']][] = $vvv;
    				}
    			}
    			
    			foreach ($arrmenu as $k => &$v) {
    				foreach ($v['son'] as $kk => &$vv) {
    					if(!empty($arrfun[$vv['menu_id']])) {
    						$vv['son2'] = $arrfun[$vv['menu_id']];
    					} else {
    						unset($v['son'][$kk]);
    					}
    				}
    			}
    			 
    			foreach ($arrmenu as $k3 => $v3) {
    				if(empty($v3['son'])) {
    					unset($arrmenu[$k3]);
    				}
    			}
    		}
    		$this->assign('arrmenu', $arrmenu);
    		$this->display();
    	}
    }

    /**
     * 角色修改模板
     */
    public function edit() {
       	$msg = '系统错误！';
       	$status = 'error';
       	
       	//查询当前角色信息
       	$info = D('SysRole')->roleinfo(I('role_id',0,'int'));
       	//当角色不存在时
       	if($info) {
            
            //部门列表
            $map = array();
            $map['user_id'] = array('eq',D('SysUser')->root_user_id());
            $map['status'] = array('eq',1);
            $depart_list = M('Sys_depart')->where($map)->select();


            foreach($depart_list as $k=>$v){
                if($info['depart_id'] == $v['depart_id']){
                    $depart_list[$k]['ischecked'] = 'selected';
                }
            }
            $this->assign('depart_list',$depart_list);
            //获取操作用户的管理员ID

            $user_id = D('SysUser')->root_user_id();

       		//如果当前的管理员ID与数据的所属一致 说明修改自己的数据
       		if($user_id == $info['user_id']) {
       			$this->assign('info',$info);
       			$this->display();
       		}else{
       			$this->ajaxReturn(array('msg'=>'非法操作他人数据！','status'=>$status));
       		}
       	}else{
       		$this->ajaxReturn(array('msg'=>'角色不存在！','status'=>$status));
       	}
    }
	/**
	 * 角色修改模板
	 */
	public function show() {
		$msg = '系统错误！';
		$status = 'error';
		//查询当前角色信息
		$info = D('SysRole')->find(I('role_id',0,'int'));
		//当角色不存在时
		if($info) {
				$this->assign($info);
				$this->display();

		}else{
			$this->ajaxReturn(array('msg'=>'角色不存在！','status'=>$status));
		}
	}



    /**
     * 角色修改
     */
    public function update() {
    	$msg = '系统错误！';
    	$status = 'error';
    
    	if(IS_POST) {
    		$role_id = trim(I('post.role_id',0,'int'));
    		$role_name = trim(I('post.role_name'));
    		$remark = trim(I('post.remark'));
            $depart_id = trim(I('post.depart_id'));
    		 
    		//id不能为0
    		if(!empty($role_id)){
    			 
                //部门是否在自己的范围内
                if($depart_id &&  !D('SysDepart')->is_me_depart($depart_id)){
                    $msg = '部门选择错误！';
					$this->ajaxReturn(array('msg'=>'部门选择错误!','status'=>$status));
                }


    			//判断如果没有数据的话
    			$roleinfo = D('SysRole')->find($role_id);
    			if($roleinfo){
    				//获取操作用户的管理员ID
    				$user_id = D('SysUser')->root_user_id();
    				 
    				//判断当前修改的数据是否在自己的管理员所属
    				if($user_id == $roleinfo['user_id']){
    					//角色名称不能为空
    					if(!empty($role_name)) {
    						//查询是否有重复角色名
    						$map['role_name'] = array('eq',$role_name);
    						$map['role_id'] = array('neq',$role_id);
    						$map['status'] = array('neq',2);
    						$map['user_id'] = array('eq',$user_id);
    						 
    						$roleinfo1 = M('Sys_role')->where($map)->find();
    						if(!$roleinfo1){
    							//修改数组
    							$edit = array(
    									'role_id'           =>  $role_id,
    									'role_name'         =>  $role_name,
                                        'depart_id'         =>  $depart_id,
    									'remark'            =>  $remark,
    									'modify_user_id'    =>  D('SysUser')->self_id(),
    									'modify_date'       =>  date('Y-m-d H:i:s'),
    							);
    							 
    							//执行修改
    							if(M('Sys_role')->save($edit)){
    								$msg = '编辑角色成功！';
    								$status = 'success';
									$n_msg='成功';
    							}else{
    								$msg = '编辑角色失败！';
									$n_msg='失败';
    							}
								$c_item='';
								$c_item.=$role_name===$roleinfo['role_name']?'':'角色名称【'.$role_name.'】';
								if($depart_id){
									$fg=empty($c_item)?'':'，';
									$c_item.=$depart_id===$roleinfo['depart_id']?'':$fg.'所属部门【'.get_depart_name($depart_id).'】';
								}
								$note='用户【' . get_user_name(D('SysUser')->self_id()) . '】，ID【'.$role_id.'】，编辑角色【'.$roleinfo['role_name'].'】：'.$c_item.$n_msg;
								$this->sys_log('编辑角色',$note);
    						}else{
    							$msg = '角色名重复,请重新设置名称!';
    						}
    					}else{
    						$msg = '角色名称不能为空!';
    					}
    				} else {
    					$msg = '非法操作他人数据!';
    				}
    			} else {
    				$msg = '角色不存在!';
    			}
    		} else {
    			$msg = '角色ID不存在!';
    		}
    		 
    		IS_AJAX && $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    	} else {
    		write_error_log(array(__METHOD__), '非POST传值');
    	}
    }

    /**
     * 角色删除功能
     */
    public function delete(){

        $msg = '系统错误！';
        $status = 'error';

        if(IS_POST){
            $role_id = I('post.role_id',0,'int');

            if(!empty($role_id)){
                $roleinfo = D('SysRole')->roleinfo($role_id);
                if($roleinfo){
                    //获取操作用户的管理员ID
                    $user_id = D('SysUser')->root_user_id();

                    //判断是否属于自己的数据
                    if($roleinfo['user_id'] == $user_id){
                        //启动事务
                        $model = M('');
                        $model->startTrans();

						//删除所有包含该角色的用户角色信息
						$delete_user = D('SysRole')->delete_user($role_id);

						//删除所有包含该角色的角色节点信息
						$delete_function = D('SysFunction')->delete_function($role_id);

						//将数据修改为删除状态
                        /*$edit = array(
                            'role_id'       =>$role_id,
                            'status'        => 2
                        );
                        $edit = M('Sys_role')->save($edit);*/
						$edit = M('sys_role')->where("role_id={$role_id}")->delete();

                        //判断事务
                        if($edit && $delete_user && $delete_function){
                            $msg = '删除角色成功！';
                            $status = 'success';
                            $model->commit();
							$n_msg='成功';
                        }else{
                            $msg = '删除角色失败！';
                            $model->rollback();
							$n_msg='失败';
                        }
						$note='用户【' . get_user_name(D('SysUser')->self_id()) . '】，ID【'.$role_id.'】，删除角色【'.$roleinfo['role_name'].'】'.$n_msg;
						$this->sys_log('删除角色',$note);
                    } else {
    					$msg = '非法操作他人数据！';
    				}
                } else {
                	$msg = '此角色不存在！';
                }
            } else {
            	$msg = '非法角色数据！';
            }

            if(IS_AJAX){
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
            
        }
    }
    
    /**
     * 角色状态切换功能
     */
    public function toggle_status(){
    
    	$msg = '系统错误！';
    	$status = 'error';
    
    	if(IS_POST){
    		$role_id = I('post.role_id',0,'int');
    
    		if(!empty($role_id)){
    			$roleinfo = D('SysRole')->roleinfo($role_id);
    			if($roleinfo){
    				//获取操作用户的管理员ID
    				$user_id = D('SysUser')->root_user_id();
    
    				//判断是否属于自己的数据
    				if($roleinfo['user_id'] == $user_id){
    					//启动事务
    					M()->startTrans();
    					//将数据修改为删除状态
    					$edit = array(
    							'role_id'       => $role_id,
    							'status'        => ($roleinfo['status']==0) ? 1 : 0,
                                'modify_date'   => date("Y-m-d H:i:s",time()),
                                'modify_user_id'=>  D('SysUser')->self_id(),
    					);

    					$edit = M('Sys_role')->save($edit);
    					$rd = D('SysRole')->delete_user($role_id);
						$title=$roleinfo['status']===0 ? '启用' : '禁用';
    					//判断事务
    					if($edit !== false && $rd !== false){
    						M()->commit();
    						$msg = "角色".(($roleinfo['status']==0) ? '启用' : '禁用').'成功!';
    						$status = 'success';
							$n_msg='成功';

    					}else{
    						M()->rollback();
    						$msg = '角色状态切换失败!';
							$n_msg='失败';
    					}
						$note='用户【' . get_user_name(D('SysUser')->self_id()) . '】，ID【'.$role_id.'】，'.$title.'角色【'.$roleinfo['role_name'].'】'.$n_msg;
						$this->sys_log('角色状态切换',$note);
    				} else {
    					$msg = '非法操作他人数据';
    				}
    			} else {
    				$msg = '此角色不存在';
    			}
    		} else {
    			$msg = '非法角色';
    		}
    
    		if(IS_AJAX){
    			$this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    		}
    	}
    }

    /**
     * 设置用户角色
     */
    public function set_role(){
        $msg = '系统错误！';
        $status = 'error';

        if(IS_POST){
            $user_id = I('post.user_id',0,'int');
            $role_ids = I('post.role_ids');

            if(!empty($user_id)){

                //查询当前是否有该用户
                $userinfo = M('SysUser')->where('user_id='.$user_id)->find();

                if($userinfo){

                    //判断当前需要修改的用户是否和自己属于同一个管理平台下
                    if(D('SysUser')->self_proxy_id() == $userinfo['proxy_id']){

                        if( !is_array($role_ids) ){
                            if(empty($role_ids)){
                                $role_ids = array();
                            }else{
                                $role_ids = explode(',',$role_ids);
                            }
                            
                        }

                        $check = true;
                        foreach ($role_ids as $key => $value) {
                            if(!intval($value)){
                                $check = false;
                                break;
                            }
                        }

                        if($check){
							$role_name_list='';
                            if(count($role_ids) != 0){
								$role_name=array();
								$where['role_id'] = array('in',$role_ids);
								$role_list_s = M('sys_role')->field('role_name')->where($where)->select();
								foreach($role_list_s as $k=>$v){
									$role_name[]=$v['role_name'];
								}
                                $map['status'] = array('neq',2);
                                $map['user_id'] = array('eq',D('SysUser')->root_user_id());
                                $role_list = M('Sys_role')->field('role_id')->where($map)->select();
                                if($role_list){
                                    $role = array();
                                    foreach($role_list as $k=>$v){
                                        $role[] = $v['role_id'];
                                    }
                                    foreach($role_ids as $rk=>$rv){
                                        if(!in_array($rv,$role)){
                                            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                                        }
                                    }

                                    if(D('SysRole')->update_role($user_id,$role_ids)){

                                        $msg = '角色设置成功！';
                                        $status = 'success';
										$n_msg='成功';
                                    }else{
                                        $msg = '角色设置失败！';
										$n_msg='失败';
                                    }

									$title='设置';
									$role_name_list='设置角色【'.implode(',',$role_name).'】';
                                }

                            }else{

                                if(D('SysRole')->update_role($user_id,$role_ids)){
                                    $msg = '角色信息已更新!';
                                    $status = 'success';
									$n_msg='成功';
                                }else{
                                    $msg = '角色更新失败!';
									$n_msg='失败';
                                }
								$title='更新';
								$role_name_list='清除用户角色';
                            }
							$note='用户【' . get_user_name(D('SysUser')->self_id()) . '】，ID【'.$user_id.'】，给用户【'.get_user_name($user_id).'】'.$role_name_list.$n_msg;
							$this->sys_log($title.'用户角色',$note);
                        } 
                    }
                }
            }

            if(IS_AJAX){
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }

        }else{

            if($user_id = intval(I('get.user_id'))){

                if(D('SysUser')->is_root_admin($user_id)){

                    $msg = '该用户是超级管理员';
                    $status = 'error';
                    $data = array();
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                }else{
                    $map['user_id'] = $user_id;
                    $map['status'] = array('neq',2);

                    $user = M('SysUser')->where($map)->find($user_id);
                    if($user){
                                            //获取所有角色
                        $map = array();
                        $map['status'] = array('eq',1);
                        $map['user_id'] = array('eq',D('SysUser')->root_user_id());
                        $rolealllist = M('Sys_role')->where($map)->select();

                        $map = array();
                        $map['r.status'] = array('eq',1);
                        $map['c.user_id'] = array('eq',$user_id);
                        $role_list = M('')->table('t_flow_sys_user_role as c')->join('t_flow_sys_role as r on c.role_id = r.role_id')->where($map)->select();
                        if($role_list){
                            foreach($rolealllist as $rk => $rv){
                                foreach($role_list as $mk => $mv){
                                    if($mv['role_name'] && $rv['role_id'] == $mv['role_id']){
                                        $rolealllist[$rk]['haschecked'] = 'checked';
                                    }
                                }
                            }

                        }

                        $map = array();
                        $map['depart.status'] = array('eq',1);
                        $map['depart.user_id'] = array('eq',D('SysUser')->root_user_id());
                        $depart_list = M('Sys_depart as depart')->where($map)->select();
                        foreach($depart_list as $k=>$v){
                            if($user['depart_id'] == $v['depart_id']){
                                $depart_list[$k]['ischecked'] = 'selected';
                            }
                        }
                        $this->assign('user',$user);
                        $this->assign('depart_list',$depart_list);

                        $this->assign('rolealllist',$rolealllist);
                        //var_dump($rolealllist);exit;
                        $this->display('set_role');
                    }else{
                        $msg = '用户不存在！';
                    }

                }
            }
            
            
        }
        
    }

}