<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class DepartController extends CommonController{
	/*
	 * 部门列表
	 */
	public function index(){
		D("SysUser")->sessionwriteclose();
		$depart_name=I('depart_name');
        if($depart_name)$map['a.depart_name']=array("like","%{$depart_name}%");
		//获取当前平台超级管理员的ID
        $root_user_id=D("SysUser")->root_user_id();
        $top_depart_id=trim(I('top_depart_id'));
        if($top_depart_id==='0' || $top_depart_id > 0){
            if($top_depart_id==='0'){
                $map['a.top_depart_id']=array('eq','0');    
            }else{
                $map['a.top_depart_id']=array('eq',$top_depart_id);
            }
        }
        $map['a.status']=array('neq',2);	
        $map['a.user_id']=$root_user_id;
		//调用分页类
		$count      = M('Sys_depart a')->where($map)->join('t_flow_sys_depart as b ON a.top_depart_id=b.depart_id',"left")->count();
        $Page       = new Page($count,20);
        $show   	= $Page->show();

        //获取所有部门
        $depart_list = M('Sys_depart a')
        ->where($map)
        ->join('t_flow_sys_depart as b ON a.top_depart_id=b.depart_id',"left")
        ->join('t_flow_sys_user as c on a.user_id=c.user_id','left')
        ->join('t_flow_sys_user as c2 on a.depart_manager=c2.user_id','left')
        ->field("a.*,b.depart_name as bdepart_name,c.user_type,c2.user_name")
        ->order('a.modify_date desc')
        ->limit($Page->firstRow.','.$Page->listRows)
        ->select();

        $topdeparts=D('SysDepart')->topdepart();
    	//加载模板
        $this->assign('depart_list',get_sort_no($depart_list,$Page->firstRow));  //数据列表
        $this->assign('page',$show);//分页
        $this->assign('topdeparts',$topdeparts);
        $this->assign("systype",get_sys_type());
        $this->display('index');         //模板
    }


	/**
	 * 导出excel
	 */
	public function export_excel() {
		$depart_name=I('depart_name');
		if($depart_name)$map['a.depart_name']=array("like","%{$depart_name}%");
		//获取当前平台超级管理员的ID
		$root_user_id=D("SysUser")->root_user_id();
		$top_depart_id=trim(I('top_depart_id'));
		if($top_depart_id==='0' || $top_depart_id > 0){
			if($top_depart_id==='0'){
				$map['a.top_depart_id']=array('eq','0');
			}else{
				$map['a.top_depart_id']=array('eq',$top_depart_id);
			}
		}
		$map['a.status']=array('neq',2);
		$map['a.user_id']=$root_user_id;

		//获取所有部门
		$depart_list = M('Sys_depart a')
			->where($map)
			->join('t_flow_sys_depart as b ON a.top_depart_id=b.depart_id',"left")
			->join('t_flow_sys_user as c on a.user_id=c.user_id','left')
			->join('t_flow_sys_user as c2 on a.depart_manager=c2.user_id','left')
			->field("a.*,b.depart_name as bdepart_name,c.user_type,c2.user_name")
			->order('a.modify_date desc')
			->limit(3000)
			->select();

		$title='部门管理';
		$headArr=array("部门名称","部门负责人","上级部门");
		$list=array();
		foreach($depart_list as $k=>$v){
			$list[$k]['depart_name'] =$v['depart_name'];
			$list[$k]['user_name'] =$v['user_name'];
			if($v['bdepart_name']){
				$list[$k]['bdepart_name'] =$v['bdepart_name'];

			}else{
				$list[$k]['bdepart_name']='顶级部门';
			}
		}
		ExportEexcel($title,$headArr,$list);
	}

    /*
     * 部门添加模板
     */
    public function add(){
        $order_num = M('SysDepart')->order("depart_id desc")->field("depart_id")->find();
        $order_num = $order_num['depart_id']+1;
        $this->assign('order_num',$order_num);
    	$topdepart=D("SysDepart")->topdepart();
    	$this->assign('topdepart',$topdepart);
    	$manager_list = $this->get_manager_list();
    	$this->assign('manager_list',$manager_list);
    	$this->display('add');
    }

    /*
     * 部门添加功能
     */
    public function insert(){
    	$depart_name=I("post.depart_name");			//部门名称
    	$top_depart_id=I("post.top_depart_id");     //上级部门id
        $order_num=I("post.order_num");
        $remark=I("post.remark");
        $depart_manager=I("post.depart_manager");
    	if($depart_name){
			if($depart_manager){
    		$where['user_id']=D("SysUser")->root_user_id();
    		$where['depart_name']=$depart_name;
    		$where['status']=array('neq',2);
    		$depart=M('Sys_depart')->where($where)->select();
    		if(!$depart){
		    	$add=array(
		    			'depart_name'    =>  $depart_name, 
		    			'top_depart_id'  =>  $top_depart_id,
		    			'user_id'        =>  D("SysUser")->root_user_id(),
                        'order_num'      =>  $order_num,
                        'status'         =>  1,
                        'remark'         =>  $remark,
                        'depart_manager' =>  $depart_manager,
		    			'create_user_id' =>  D("SysUser")->self_id(),
		    			'create_date'	 =>  date('Y-m-d H:i:s',time()),
		    			'modify_user_id' =>  D("SysUser")->self_id(),
		    			'modify_date'	 =>  date('Y-m-d H:i:s',time())
		    		);
				$id=M('Sys_depart')->add($add);
		    	if($id){
		            $msg = '新增部门成功！';
		            $status = 'success';
					$n_msg='成功';
		        }else{
		            $msg = '新增部门失败！';
					$n_msg='失败';
		        }
				$info=empty($top_depart_id)?'顶级部门':get_depart_name($top_depart_id);
				$note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$id.'】，新增部门，部门名称【'.$depart_name.'】，上级部门【'.$info.'】，所属用户【'.get_user_name(D("SysUser")->root_user_id()).'】，部门负责人【'.get_user_name($depart_manager).'】'.$n_msg;
				$this->sys_log('新增部门',$note);
		    }else{
		    	$msg = '部门名称重复,请仔细检查！';
		    }
			}else{
				$msg="部门负责人不能为空！";
			}
    	}else{
    		$msg=("部门名称不能为空！");
    	}
    	if(IS_AJAX){
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }

    /*
     * 部门修改模板
     */
	public function edit(){
		$msg = '系统错误';
        $status = 'error';

		$depart_id=intval(I('get.depart_id'));
		$info=D('SysDepart')->departinfo($depart_id);
		if($info){
			if(D('SysDepart')->is_me_depart($depart_id)){
				$this->assign('info',$info);
				$manager_list = $this->get_manager_list();
    			$this->assign('manager_list',$manager_list);
				$this->display();
			}else{
				$this->error("权限不足");
           	}         
        }else{
        	$this->error('部门不存在！');
        }
	}


	/*
	 * 部门详细
	 */
	public function show(){
		$msg = '系统错误';
		$status = 'error';

		$where['a.depart_id']=trim(I('depart_id'));
		$info=D('SysDepart')->show($where);
		if($info){
				$this->assign($info);
				$this->display();
		}else{
			$this->error('部门不存在！');
		}
	}

	/*
	 * 部门修改功能
	 */
	public function update(){
		$msg="系统错误";
		$status="error";
		$depart_id=I("post.depart_id");             
		$depart_name=I("post.depart_name");			//部门名称
    	$order_num=I("post.order_num");             //排序
    	$remark=I("post.remark");					//备注
    	$depart_manager = I("post.depart_manager");
    	if($depart_name){
    		$where['user_id']=D("SysUser")->root_user_id();
    		$where['depart_name']=$depart_name;
    		$where['status']=array('neq',2);
    		$where['depart_id']=array('neq',$depart_id);
    		$depart=M('Sys_depart')->where($where)->select();
    		if(!$depart){
	    		if(D('SysDepart')->is_me_depart($depart_id)){
					$depart_info = M('Sys_depart')->where('depart_id='.$depart_id)->find();
	    			$save=array(
	    					'depart_id'		 =>	 $depart_id,
			    			'depart_name'    =>  $depart_name, 
			    			'order_num'		 =>  $order_num,
			    			'depart_manager' =>  $depart_manager,
			    			'remark'		 =>  $remark,
			    			'modify_user_id' =>  D("SysUser")->self_id(),
			    			'modify_date'	 =>  date('Y-m-d H:i:s',time())
			    		);
	    			if(M('Sys_depart')->save($save)){
	    				$msg = '编辑部门成功！';
			            $status = 'success';
						$n_msg='成功';
	    			}else{
	    				$msg='编辑部门失败！';
						$n_msg='失败';
	    			}
					$c_item='';
					$c_item.=$depart_name===$depart_info['depart_name']?'':'部门名称【'. $depart_name.'】';
					$fg=!empty($c_item)?'，':'';
					$c_item.=$depart_name===$depart_info['depart_name']?'':$fg.'部门负责人【'. get_user_name($depart_name).'】';
					$note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$depart_id.'】，编辑部门【'.$depart_info['depart_name'].'】：'.$c_item.$n_msg;
					$this->sys_log('编辑部门',$note);
	    		}else{
	    			$msg="权限不足";
	    		}
	    	}else{
	    		$msg='部门名称重复,请仔细检查！';
	    	}
    	}else{
    		$msg="部门名称不能为空";
    	}
    	if(IS_AJAX){
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
	}

	public function delete(){
		$msg = '系统错误';
        $status = 'error';
		$depart_id=intval(I('post.depart_id'));
		$info=D('SysDepart')->departinfo($depart_id);
        $conf=I("get.conf");
		if($info){
            if(empty($conf)){
                $dep=D('SysDepart')->depart_user($depart_id);
                if($dep==1){
                    $msg="该部门有下级部门，请先删除下级部门！";
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                }elseif($dep==2){
                    $msg="该部门下面有用户，确定删除该部门吗？";
                    $da['depart_id']=$depart_id;
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'info'=>$da));
                }
            }
			if(D('SysDepart')->is_me_depart($depart_id)){
                $map['depart_id']=$depart_id;
                $model=M('Sys_depart');
                $model->startTrans();
                $edit =$model->where($map)->delete();
                $where['$depart_id']=0;
                $ed=M('Sys_user')->where($map)->save($where);
                if($edit){
                	//$msg="部门（".$info['depart_name']."）删除成功！";
					$msg="删除部门成功！";
                    $model->commit();
                	$status="success";
					$n_msg='成功';
                }else{
                    $model->rollback();
                	$msg="删除部门失败！";
					$n_msg='失败';
                }
				$note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$depart_id.'】，删除部门【'.$info['depart_name'].'】'.$n_msg;
				$this->sys_log('删除部门',$note);
			}else{
				$msg = '权限不足！';
			}
		}else{
			$msg="部门不存在！";
		}
		if(IS_AJAX){
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
	}


	/**
     * 输入框联想使用
     * 获取部门经理
     */
    public function ajax_depart_manager_name(){
        $depart_manager_name = I("depart_manager_name");
        $depart_manager_name = trim($depart_manager_name);
        
        $list = '';
        if(!empty($depart_manager_name)){
        	$map = array();
	        $user_type = D('SysUser')->self_user_type();
	        $self_proxy_id = D('SysUser')->self_proxy_id();
	        $self_enterprise_id = D('SysUser')->self_enterprise_id();
	        $map['user_type'] = $user_type;
	        if($user_type <= 2 && !empty($self_proxy_id)){
	            $map['proxy_id'] = array('eq',$self_proxy_id);
	        }elseif($user_type == 3 && !empty($self_enterprise_id)){
	            $map['enterprise_id'] = array('eq',$self_enterprise_id);
	        }
	        $map['user_name'] = array("like","%".$depart_manager_name."%");
	        $map['status'] = array("eq",1);
	        $list = M("sys_user")->where($map)->field("user_id as id,user_name as name")->limit(0,10)->select();
        }
        $this->ajaxReturn(array('info'=>$list));
    }

    /**
		获取部门联系人
    */
	function get_manager_list(){
    	$map = array();
        $user_type = D('SysUser')->self_user_type();
        $self_proxy_id = D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        $map['user_type'] = $user_type;
        if($user_type <= 2 && !empty($self_proxy_id)){
            $map['proxy_id'] = array('eq',$self_proxy_id);
        }elseif($user_type == 3 && !empty($self_enterprise_id)){
            $map['enterprise_id'] = array('eq',$self_enterprise_id);
        }
        $map['status'] = array("eq",1);
        $list = M("sys_user")->where($map)->field("user_id as id,user_name as name")->order('user_id')->select();
        return $list;
    }

}
?>