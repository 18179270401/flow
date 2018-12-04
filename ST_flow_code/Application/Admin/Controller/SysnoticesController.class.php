<?php

/*
 * SysNoticesController.class.php
 * 公告操作控制器
 *
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class SysnoticesController extends CommonController {

    /*
     * 公告管理
     */

    public function index(){

        //调用分页类
        D("SysUser")->sessionwriteclose();
        $notice_title = trim(I('get.notice_title'));
        $user_name = trim(I('get.user_name'));
        $status = trim(I('get.status'));
        if($notice_title){
            $map['notice.notice_title'] = array('like','%'.$notice_title.'%');
        }
        if($user_name){
            $map['user.user_name'] = array('eq',$user_name);
        }

        if($status == '0'){
           $map['notice.status'] = array('eq',0);

        }elseif($status == '1' or $status == ''){
           $map['notice.status'] = array('eq',1);
           if($status == ''){
                $status = 1;
           }
        }


        $count      = M('Sys_notice as notice')
        ->join("t_flow_sys_user as user on notice.create_user_id = user.user_id","left")
        ->where($map)
        ->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();

        //获取所有公告列表
        $notice_list = M('Sys_notice as notice')
        ->join("t_flow_sys_user as user on notice.create_user_id=user.user_id","left")
        ->where($map)
        ->field("notice.*,user.user_name")
        ->order('notice.create_date desc')
        ->limit($Page->firstRow.','.$Page->listRows)
        ->select();

        $this->assign('status',$status);
        $this->assign('user_name',$user_name);
        $this->assign('notice_title',$notice_title);
        //加载模板
        $this->assign('notice_list',get_sort_no($notice_list,$Page->firstRow));  //数据列表
        $this->assign('page',$show);            //分页
        $this->display('index');

    }

    public function me_index(){

        $self_user_id = D('SysUser')->self_id();
        $self_user_type = D('SysUser')->self_user_type();
        $read_id = D('SysNotice')->get_sysnotice_read($self_user_id); //已读
        $notice_title=trim(I('notice_title'));
        $cond['status']=1;
        $cond['valid_date_begin']=array('elt', date('Y-m-d'));
        $cond['valid_date_end']=array('egt', date('Y-m-d'));
        $cond['scope']=array('like', "%{$self_user_type}%");
   /*     $cond['notice_type']=1;*/
        if(trim(I('status'))==''){
            !empty($read_id) && $cond['notice_id'] = array('not in', $read_id);//未读
        }else{
            !empty($read_id) && $cond['notice_id'] = array('in', $read_id); //已读
        }
         if($notice_title){
             $cond['notice_title']=array('like', "%{$notice_title}%");
         }
        $model = M('sys_notice');
        $count      = $model->where($cond)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();

        $model->where($cond)->order('notice_type asc,create_date asc')->limit($Page->firstRow.','.$Page->listRows);
        $ret = $model->select();
        $this->assign('notice_list',get_sort_no($ret,$Page->firstRow));  //数据列表
        $this->assign('page',$show);            //分页
        $this->assign('read_id',$read_id);
        $this->display();
    }



    /*
        * 部门添加模板
        */
    public function add(){

        $this->display('add');
    }

    /*
     * 新增公告
     */
    public function insert() {
        $msg = '系统错误';
        $status = 'error';
        $notice_type = I('post.notice_type', 2, 'intval');
        $notice_title = trim(I("post.notice_title"));			//公告title
        $notice_content = trim(I("post.notice_content"));//公告内容
        $scope_list    =   I('post.scope_list');
        $valid_date_begin = trim(I('post.valid_date_begin'));
        $valid_date_end = trim(I('post.valid_date_end'));

        if($valid_date_begin>$valid_date_end){
            $this->ajaxReturn(array('msg'=>'公告有效期的结束时间须大于开始时间！','status'=>$status));
        }

        $scope = implode(',',$scope_list);
        if(!empty($notice_title)){
            if(!empty($scope)){
                if(!empty($notice_content)){
                    $add=array(
                        'notice_type'       =>  $notice_type,
                        'notice_title'      =>  $notice_title,
                        'notice_content'    =>  $notice_content,
                        'scope'             =>  $scope,
                        'status'            =>  1,
                        'valid_date_begin'  =>  start_time($valid_date_begin),
                        'valid_date_end'    =>  end_time($valid_date_end),
                        'create_user_id'    =>  D("SysUser")->self_id(),
                        'create_date'       =>  date('Y-m-d H:i:s',time()),
                        'modify_user_id'    =>  D("SysUser")->self_id(),
                        'modify_date'       =>  date('Y-m-d H:i:s',time())
                    );
                    $id=M('Sys_notice')->add($add);
                    if($id){
                        $msg = '新增公告成功！';
                        $status = 'success';
                        $n_msg='成功';
                    }else{
                        $msg = '新增公告失败！';
                        $n_msg='失败';
                    }
                    $scope_arr=array();
                    foreach($scope_list as $v){
                        $scope_arr[]=get_sys_type($v);
                    }
                    $scope_s=implode('，',$scope_arr);
                    $type=$notice_type==1?'紧急公告':'普通公告';
                    $note='用户【' . get_user_name(D('SysUser')->self_id()) . "】，ID【".$id."】，新增公告，公告标题【".$notice_title."】，公告类型【".$type."】，接收范围【".$scope_s."】，公告开始时间【".$valid_date_begin."】，公告结束时间【".$valid_date_end."】，公告内容【".$notice_content."】" . $n_msg;
                    $this->sys_log('新增公告',$note);
                }else{
                    $msg = '公告内容不能为空！';
                }
            }else{
                $msg = '请选择发布范围！';
            }
        }else{
            $msg="公告标题不能为空！";
        }

        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));

    }

    /**
      * 公告查看模板
      */
    public function show() {
        $msg = '系统错误';
        $status = 'error';

        $vtype = intval(I('get.vtype'));
        $notice_id = intval(I('get.notice_id'));
        $infoview = D('Sys_notice')->find($notice_id);
        if($infoview) {
        	if(1 == $vtype) { //底部点击查看公告
	        	$self_user_id = D('SysUser')->self_id();
	        	$cond = array(
	        			'user_id'	=> $self_user_id,
	        			'notice_id'	=> $notice_id,
	        	);
	        	$snr = M('sys_notice_read')->where($cond)->find();
	        	if(empty($snr)) {
	        		$cond['read_time'] = date('Y-m-d H:i:s');
	        		M('sys_notice_read')->add($cond);
	        	}
        	}
        	
            $this->assign('infoview', $infoview);
            $this->display();
        } else {
            $this->error('公告不存在！');
        }
    }

    /**
     * 公告查看模板
     */
    public function me_show() {
        $msg = '系统错误';
        $status = 'error';

        $vtype = intval(I('get.vtype'));
        $notice_id = intval(I('get.notice_id'));
        $infoview = D('Sys_notice')->find($notice_id);
        if($infoview) {
            if(1 == $vtype) { //底部点击查看公告
                $self_user_id = D('SysUser')->self_id();
                $cond = array(
                    'user_id'	=> $self_user_id,
                    'notice_id'	=> $notice_id,
                );
                $snr = M('sys_notice_read')->where($cond)->find();
                if(empty($snr)) {
                    $cond['read_time'] = date('Y-m-d H:i:s');
                    M('sys_notice_read')->add($cond);
                }
            }

            $this->assign('infoview', $infoview);
            $this->display();
        } else {
            $this->error('公告不存在！');
        }
    }

  /**
   * 公告编辑模板
   */
    public function edit(){
        $msg = '系统错误';
        $status = 'error';

        $notice_id = intval(I('get.notice_id'));
        $info = D('Sys_notice')->find($notice_id);
        $scope = $info['scope'];
        $info['s1'] = (strpos($info['scope'], '1') !== false) ? 'checked' : '';
        $info['s2'] = (strpos($info['scope'], '2') !== false) ? 'checked' : '';
        $info['s3'] = (strpos($info['scope'], '3') !== false) ? 'checked' : '';
        $notice_type = $info['notice_type'];
        $info['type1'] = ($notice_type == 1) ? 'checked' : '';
        $info['type2'] = ($notice_type == 2) ? 'checked' : '';
        if($info) {
        	$this->assign('info', $info);
            $this->display();
        }else{
            $this->error('公告不存在！');
        }
    }
    
    /**
     * 公告修改
     */
    public function update(){
        $msg="系统错误";
        $status="error";
        $notice_id 			= I("post.notice_id");
        $notice_type        = I('post.notice_type', 2, 'intval');
        $notice_title 		= I("post.notice_title");
        $notice_content 	= I("post.notice_content");
        $valid_date_begin 	= trim(I('post.valid_date_begin'));
        $valid_date_end 	= trim(I('post.valid_date_end'));
        $scope_list    		= I('post.scope_list');
        $scope = implode(',',$scope_list);
        $notice=M('sys_notice')->where('notice_id='.$notice_id)->find();
        if($notice){
            if($notice_title){
                if($notice_content){
                        $save=array(
                            'notice_id'			=> $notice_id,
                            'notice_type'       => $notice_type,
                            'notice_title'		=> $notice_title,
                            'notice_content'	=> $notice_content,
                            'scope'				=> $scope,
                            'status'			=> 1,
                            'valid_date_begin'	=> start_time($valid_date_begin),
                            'valid_date_end'	=> end_time($valid_date_end),
                            'modify_user_id'	=> D("SysUser")->self_id(),
                            'modify_date'		=> date('Y-m-d H:i:s')
                        );
                        if(M('Sys_notice')->save($save)){
                            $msg = '公告修改成功！';
                            $status = 'success';
                            $n_msg='成功';
                        }else{
                            $msg='公告修改失败！';
                            $n_msg='失败';
                        }
                    $scope_arr=array();
                    foreach($scope_list as $v){
                        $scope_arr[]=get_sys_type($v);
                    }
                    $scope_s=implode('，',$scope_arr);
                    $type=$notice_type==1?'紧急公告':'普通公告';
                    $c_item='';
                    $c_item.=$notice_title===$notice['remind_type_name']?'':'公告标题【'.$notice_title.'】';
                    $fg=!empty($c_item)?'，':'';
                    $c_item.=$notice_type===$notice['notice_type']?'':$fg.'公告类型【'.$type.'】';
                    $fg=!empty($c_item)?'，':'';
                    $c_item.=$scope===$notice['scope']?'':$fg.'提醒范围【'.$scope_s.'】';
                    $fg=!empty($c_item)?'，':'';
                    $c_item.=strtotime(start_time($valid_date_begin))===strtotime($notice['valid_date_begin'])?'':$fg.'公告开始时间【'. $valid_date_begin.'】';
                    $fg=!empty($c_item)?'，':'';
                    $c_item.=strtotime(end_time($valid_date_end))===strtotime($notice['valid_date_end'])?'':$fg.'公告结束时间【'. $valid_date_end.'】';

                    $fg=!empty($c_item)?'，':'';
                    $c_item.=$notice_content===$notice['notice_content']?'':$fg.'提醒内容【'.$notice_content.'】';

                    $note='用户【' . get_user_name(D('SysUser')->self_id()) . '】，ID【'.$notice_id.'】，编辑公告【'.$notice['notice_title'].'】：'.$c_item.$n_msg;
                    $this->sys_log('编辑公告',$note);
                }else{
                    $msg='公告内容不能为空！';  }
            }else{
                $msg="公告名称不能为空！";
            }
        }else{
            $msg="对不起，查询是失败，请重试！！";
        }
        if(IS_AJAX){
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }

    /**
     * 修改公告状态
     */
    public function toggle_status(){
        $msg = '系统错误！';
        $status = 'error';

        if(IS_POST && IS_AJAX){
            $notice_id = I('post.notice_id',0,'int');
            if(!empty($notice_id)){
                $noticeinfo = D('Sys_notice')->find($notice_id);
                if($noticeinfo){
                    $status = $noticeinfo['status'] == 1 ? "0" : "1";
                    $edit = array(
                        'notice_id'=>$notice_id,
                        'status'=> $status,
                    );
                    $edit = M('Sys_notice')->save($edit);
                    $status_name = $status == 1 ? "启用" : "禁用";
                    if($edit){
                        $status = 'success';
                        $msg = $status_name.'成功!';
                    }else{
                        $msg = $status_name.'失败!';
                    }
                }else{
                    $msg = '数据读取失败!';
                }
            }else{
                $msg = '传入ID错误!';
            }
        }
        if(IS_AJAX){
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }

    public function delete() {
        $msg = '系统错误';
        $status = 'error';
        $notice_id = intval(I('post.notice_id'));

        if(is_numeric($notice_id) && $notice_id > 0) {
            $old = M('sys_notice')->where("notice_id={$notice_id}")->find();
            if($old) {
                M('')->startTrans();
                $r1 = M('sys_notice_read')->where("notice_id={$notice_id}")->delete();
                $r2 = M('sys_notice')->where("notice_id={$notice_id}")->delete();
                if(false !== $r1 && false !== $r2) {
                    M('')->commit();
                    $msg = '删除公告成功！';
                    $status = 'success';
                    $n_msg='成功';
                } else {
                    M('')->rollback();
                    $msg = '删除公告失败！';
                    $n_msg='失败';
                }
                $note='用户【' . get_user_name(D('SysUser')->self_id()) . '】，ID【'.$notice_id.'】，删除公告【'.$old['notice_title'].'】' . $n_msg;
                $this->sys_log('删除公告',$note);
            } else {
                $msg = '此公告不存在！';
            }
        }

        /*$map['status'] = array('neq',2);
        $map['notice_id'] = array('eq',$notice_id);
        $noticeinfo = M('sys_notice')->where($map)->find();
        //用户必须存在
        if($noticeinfo) {
            $upd = array(
                'status'    => 2,
            );
            $ret = M('sys_notice')->where("notice_id={$notice_id}")->save($upd);
            if(false !== $ret) {
                $msg = '删除公告成功！';
                $status = 'success';
            } else {
                $msg = '删除公告失败！';
            }
        }*/

        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }


    /**
    *导出EXCEL
    **/
    public function export_excel(){
        $notice_title = trim(I('get.notice_title'));
        $user_name = trim(I('get.user_name'));
        $status = trim(I('get.status'));
        if($notice_title){
            $map['notice.notice_title'] = array('like','%'.$notice_title.'%');
        }
        if($user_name){
            $map['user.user_name'] = array('eq',$user_name);
        }

        if($status == '0'){
           $map['notice.status'] = array('eq',0);

        }elseif($status == '1' or $status == ''){
           $map['notice.status'] = array('eq',1);
           if($status == ''){
                $status = 1;
           }
        }

        //获取所有公告列表
        $notice_list = M('Sys_notice as notice')
        ->join("t_flow_sys_user as user on notice.create_user_id=user.user_id","left")
        ->where($map)
        ->field("notice.*,user.user_name")
        ->order('notice.create_date desc')
        ->limit(3000)
        ->select();

        $datas = array();
        $headArr=array("公告类型","标题","发布范围","发布者","开始时间","结束时间");
        $scope_array = array('1'=>"尚通端",
            '2'=>"代理商端",
            '3'=>"企业端",
            '1,2'=>"尚通端、代理商端",
            '1,3'=>"尚通端、企业端",
            '2,3'=>"代理商端、企业端",
            '1,2,3'=>"全部");

        foreach ($notice_list as $v) {
            $data=array();
            $data['notice_type'] = $v['notice_type'] == 1?"紧急公告":"普通公告";
            $data['notice_title'] = $v['notice_title'];
            $data['scope'] = $scope_array[$v['scope']];
            $data['user_name'] = $v['user_name'];
            $data['valid_date_begin'] = substr($v['valid_date_begin'],0,10);
            $data['valid_date_end'] = substr($v['valid_date_end'],0,10);
            array_push($datas,$data);
        }
            
        $title='公告管理';

        ExportEexcel($title,$headArr,$datas);
    }


}