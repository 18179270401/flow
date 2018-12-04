<?php
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class MarketingController extends CommonController {
  public function index(){
	  D("SysUser")->sessionwriteclose();
      $where=array();
      $activity_name=trim(I('activity_name'));
	  if($activity_name)
		   {
			  $where['activity_name']=array('like','%'.$activity_name.'%');
			}
		$model=M('scene_activity');
		$count=$model->where($where)->count();
		$Page=new Page($count,20);
		$show=$Page->show();
		$list=$model
		     ->field('activity_id,activity_name,activity_file_name,activity_rule')
		     ->where($where)
		     ->limit($Page->firstRow.','.$Page->listRows)
		     ->order('modify_date desc')
		     ->select();
		     $this->assign('list',get_sort_no($list,$Page->firstRow));
		     $this->assign('page',$show);
		  	 $this->display();
  }
 public function show(){
 	   $model=M('scene_activity');
       $where['activity_id']=trim(I('activity_id'));
       $info=$model
       ->field('activity_id,activity_name,activity_file_name,activity_rule')
       ->where($where)
       ->find();
       if($info['activity_rule']!=""){
       		$info['activity_rule'] = str_replace("\n","<br />",$info['activity_rule']);
       }
       $this->assign('info',$info);
 	   $this->display();
 }

public function add(){
	   $this->display();
}

public function insert()
 {

		$status="error";
	    $msg="系统错误";
	    $activity_name=I(trim('activity_name'));
	    $activity_file_name=I(trim('activity_file_name'));
	    $activity_name=str_replace("，", ",", $activity_name);
	    $activity_rule=I(trim('activity_rule'));
	    if(empty($activity_name))
		    {
		    	$this->ajaxReturn(array('msg'=>'请输入活动名称！','status'=>$status));
		    }
	    if(empty($activity_file_name))
		    {
		    	$this->ajaxReturn(array('msg'=>'请输入活动文件名称！','status'=>$status));
		    }

		if(preg_match("/[\x7f-\xff]/",$activity_file_name)){
			$this->ajaxReturn(array("msg"=>"请勿使用中文，或者全角符号作为活动文件名称",'status'=>$status));
		}

	    if(empty($activity_rule))
		    {
		    	$this->ajaxReturn(array('msg'=>'请输入活动规则！','status'=>$status));
		    }

		$data['activity_name']=$activity_name;
		$data['activity_file_name']=$activity_file_name;
		$data['activity_rule']=$activity_rule;
	    $data['create_user_id']=D('SysUser')->self_id();
	    $data['create_date']=date('Y-m-d H:i:s',time());
	    $data['modify_date']=date('Y-m-d H:i:s',time());
	    $data['modify_user_id']=D('SysUser')->self_id();
	    $info=M('scene_activity')->add($data);
	    if($info){
			$n_msg='成功';
			$msg='新增场景成功！';
			$status='success';
		}else{
			$n_msg='失败';
			$msg='新增场景失败！';
		}
	 $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，新增场景【'.$activity_name.'】'.$n_msg;
	 $this->sys_log('新增场景',$note);
	 $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
 }

public function edit(){
	    $activity_id=I('activity_id');
	    $map['activity_id']=$activity_id;
	 	if(empty($map))
		 	{
		 		$this->error;
		 	}
	 	$info=M('scene_activity')
	 	->where($map)
	 	->field('activity_id,activity_name,activity_file_name,activity_rule')
	 	->find();
	 	$this->assign('activty',$info);
	    $this->display();
}

	public function update(){
	    $msg = '系统错误!';
	    $status = 'error';
	    $activity_id=I('activity_id');
	    $map['activity_id']=$activity_id;
		if(empty($map)){
	 		$this->ajaxReturn(array('msg'=>'对不起没有找到相关信息，请重试！','status'=>'error'));
	 	}

 	    $activity_name=trim(I('activity_name'));
 	    $activity_file_name=trim(I('activity_file_name'));
 	    $activity_name=str_replace("，", ",", $activity_name);
 	    $activity_rule=trim(I('activity_rule'));
 	    if(empty($activity_name)){
	 		$this->ajaxReturn(array("msg"=>"请输入活动名称",'status'=>$status));
	 	}
	 	if(empty($activity_file_name)){
		 	$this->ajaxReturn(array("msg"=>"请输入活动文件名称",'status'=>$status));
		}
 	    if(preg_match("/[\x7f-\xff]/",$activity_file_name)){
			$this->ajaxReturn(array("msg"=>"请勿使用中文，或者全角符号作为活动文件名称",'status'=>$status));
		}
	 	if(empty($activity_rule)){
		 	$this->ajaxReturn(array("msg"=>"请输入活动规则",'status'=>$status));
		}
	 	$data['activity_id']=$activity_id;
	 	$data['activity_name']=$activity_name;
	 	$data['activity_file_name']=$activity_file_name;
	 	$data['activity_rule']=$activity_rule;
        $data['modify_user_id']=D('SysUser')->self_id();
        $data['modify_date']=date('Y-m-d H:i:s',time());
	    $info=M('scene_activity')->save($data);
	    if($info){
			$msg='编辑场景成功！';
			$status='success';
			$n_msg='成功';
		}else{
		    $msg='编辑场景失败！';
			$n_msg='失败';
		}
		$note='用户【'.get_user_name(D('SysUser')->self_id()).'】，编辑场景【'.$activity_name.'】'.$n_msg;
		$this->sys_log('编辑场景',$note);
		$this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
   }

	public function delete (){
		$msg="系统错误";
		$status="error";
		$activity_id=I('activity_id');
		if(empty($activity_id)){
			$msg="对不起没有找到相关信息，请重试！";
			$this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
		}
		$where['activity_id']=$activity_id;
		$ac=M('scene_activity')->where($where)->find();
		if(!$ac){
			$msg="对不起没有找到相关场景，请重试！";
			$this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
		}
		M("")->startTrans();
		$bc=M('scene_user_activity')->where($where)->select();
		if($bc){
			$sc=M('scene_user_activity')->where($where)->delete();
		}
		$sa=M('scene_activity')->where($where)->delete();
		if($sa){
			if($bc && $sc || !$bc){
				M("")->commit();
				$status="success";
				$msg="删除场景成功！";
				$n_msg = '成功';
			}else{
				M("")->rollback();
				$msg="删除场景失败！";
				$n_msg = '失败';
			}
		}else {
			M("")->rollback();
			$msg = "删除场景失败！";
			$n_msg = '失败';
		}
		$note='用户【'.get_user_name(D('SysUser')->self_id()).'】，删除场景【'.$ac['activity_name'].'】'.$n_msg;
		$this->sys_log('删除场景',$note);
		$this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
	}

	/**
    *导出EXCEL
    **/
    public function export_excel(){
        $where=array();
      	$activity_name=trim(I('activity_name'));
	  	if($activity_name){
			$where['activity_name']=array('like','%'.$activity_name.'%');
		}
		$model=M('scene_activity');
		
		$list=$model
		    ->field('activity_id,activity_name,activity_file_name,activity_rule')
		    ->where($where)
		    ->limit(3000)
		    ->order('modify_date desc')
		    ->select();

        $datas = array();
        $headArr=array("活动名称","活动文件名称");

        foreach ($list as $v) {
            $data=array();
            $data['activity_name'] = $v['activity_name'];
            $data['activity_file_name'] = $v['activity_file_name'];
            array_push($datas,$data);
        }
            
        $title='场景管理';

        ExportEexcel($title,$headArr,$datas);
    }



}