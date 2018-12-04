<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class ProxyAccountOperateController extends CommonController{
     /*代理商账户冻结管理列表*/
     public function index(){
         D("SysUser")->sessionwriteclose();
         $user=D('SysUser')->self_user_type();//获取自身的用户类型是运营平台，代理商，企业
         $proxy_name = trim(I('proxy_name')); //代理商名称
         $start_datetime = trim(I('start_datetime'));   //开始时间
         $end_datetime = trim(I('end_datetime'));   //结束时间
         $frozen_approve_status=trim(I('frozen_approve_status'));   //冻结审核状态
         $thaw_approve_status=trim(I('thaw_approve_status'));   //解冻审核状态
         $proxy_code=trim(I('proxy_code'));
         $where=array();
         $model=M('proxy_frozen_account pa');
         if($proxy_name){
             $where['p.proxy_name']=array('like','%'.$proxy_name.'%');
         }
         if($proxy_code){
             $where['p.proxy_code']=array('like','%'.$proxy_code.'%');
         }
         if(!empty($frozen_approve_status)){
             $where['pa.frozen_approve_status']=$frozen_approve_status;
         }
         if(!empty($thaw_approve_status) ){
             $where['pa.thaw_approve_status']=$thaw_approve_status;
         }

         if($start_datetime or $end_datetime){
             if($start_datetime && $end_datetime){
                 $where['pa.create_date'] =array('between',array(start_time($start_datetime),end_time($end_datetime)));
             }elseif($start_datetime){
                 $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                 $where['pa.create_date'] = array('between',array(start_time($start_datetime),$end_datetime));
             }elseif($end_datetime){
                 $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                 $where['pa.create_date'] =array('between',array($start_datetime,end_time($end_datetime)));
             }
         }else{
             $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
             $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
             $where['pa.create_date'] = array('between',array($start_datetime,$end_datetime));
         }

         $join=array(
             't_flow_proxy as p on pa.proxy_id= p.proxy_id',
             't_flow_proxy_account as a on p.proxy_id= a.proxy_id'
         );
         $proxy_ids=D('Proxy')->proxy_child_ids();
         $where['pa.proxy_id'] = array(array('in',$proxy_ids),array('eq',D('SysUser')->self_proxy_id()),'or');
         $count=$model->join($join,'left')->where($where)->count();
         $Page     = new \Think\Page($count,20);
         $show     = $Page->show();
         $list=$model->join($join,'left')
             ->field('pa.apply_id,pa.apply_money,pa.apply_date,pa.create_user_id,pa.thaw_create_user_id,pa.thaw_create_date,pa.opr_type,pa.frozen_date,pa.thaw_date,pa.frozen_approve_status,pa.frozen_last_approve_date,pa.create_date,pa.thaw_approve_status,pa.thaw_last_approve_date,p.proxy_code,p.proxy_name,a.account_balance,a.freeze_money,a.credit_money,a.credit_freeze_money')
             ->order("pa.modify_date desc")
             ->where($where)
             ->limit($Page->firstRow.','.$Page->listRows)
             ->select();
         $sum_results = $model
             ->join($join,'left')->where($where)
             ->field('sum(pa.apply_money) as sum_money_one')
             ->find();
         $this->assign('d_sdata',date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))));  //默认开始时间
         $this->assign('d_edata',date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))));  //默认结束时间
         $this->assign('sum_results',$sum_results);
                $this->assign('usr_type',$user);
                $this->assign('list',get_sort_no($list,$Page->firstRow));
                $this->assign('page',$show);
                $this->assign('user_id',D('SysUser')->self_id());
                $this->display();        //模板

     }

    /*
	 *代理商账户新增冻结显示页面
	 */
    public function add(){
        $where['approve_status']=1;
        $where['status']=1;
        $where['proxy_id']=array("neq","1");
        $pids=D('Proxy')->proxy_child_ids();
        if($pids){
            $where['proxy_id']=array("in",$pids);
        }else{
            $where['proxy_id']=array("eq","-1");
        }
        $info=M('proxy')->where($where)->select();
        $this->assign("info",$info);
        $this->display();        //模板
    }

    public function  insert(){
        $msg = '系统错误!';
        $status = 'error';
        $data = array();
        $apply_money=trim(I('apply_money'));
        $proxy_id=I('proxy_id');
        if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $apply_money)){
            $this->ajaxReturn(array('msg'=>'请输入正确的申请金额！','status'=>$status));
        }
        if($apply_money==''){
            $this->ajaxReturn(array('msg'=>'请输入的申请金额！','status'=>$status));
        }
        if($apply_money<=0){
            $this->ajaxReturn(array('msg'=>'申请金额需大于零！','status'=>$status));
        }
        if($proxy_id==0 || $proxy_id==""){
            $this->ajaxReturn(array('msg'=>'请输入代理商！','status'=>$status));
        }
        $apply_cont=M('proxy_account')->where('proxy_id='.$proxy_id)->field('account_balance')->find();
        if($apply_cont['account_balance']<$apply_money){
            $this->ajaxReturn(array('msg'=>'该代理商账户余额不足，请勿操作！','status'=>$status));
        }
        $proxy_info=$this->proxy_info($proxy_id);
        $data['proxy_id']=$proxy_id;
        $data['account_id']=$proxy_info['account_id'];
        $data['opr_type']=1;
        $data['apply_money']=$apply_money;
        $data['apply_date']=date('Y-m-d H:i:s',time());
       // $data['frozen_date']=date('Y-m-d H:i:s',time());
        $data['frozen_remark']=trim(I('frozen_remark'));
        $data['frozen_approve_status']=1;
        $data['create_user_id']=D('SysUser')->self_id();
        $data['create_date']=date('Y-m-d H:i:s',time());
        $data['modify_date']=date('Y-m-d H:i:s',time());
        $res=M('proxy_frozen_account')->add($data);
        if($res){
            $msg = '新增代理商账户冻结金额成功，请等待审核!';
            $status = 'success';
            $n_msg='成功';
        }else{
            $msg = '新增代理商账户冻结金额失败，请重试!';
            $n_msg='失败';
        }
        $note = "用户【".get_user_name(D('SysUser')->self_id())."】，ID【".$res."】，新增代理商账户冻结金额，代理商【".$proxy_info['proxy_name']."】，冻结金额【".money_format2($apply_money)."】元".$n_msg;
        $this->sys_log('新增代理商账户冻结金额',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    /*冻结编辑*/
    public function edit(){
        $apply_id=I('apply_id');
        $list=$this->frozen_account_info($apply_id);
        if($list){
            $map['approve_status']=1;
            $map['status']=1;
            $map['proxy_id']=array("neq",1);
            $pids=D('Proxy')->proxy_child_ids();
            if($pids){
                $where['proxy_id']=array("in",$pids);
            }else{
                $where['proxy_id']=array("eq","-1");
            }
            $info=M('proxy')->where($map)->select();
            $this->assign("info",$info);
            $this->assign($list);
            $this->display();
        }else{
            $this->ajaxReturn(array('msg'=>"对不起，没有找到该信息，请重试",'status'=>"error"));
        }
    }


    /*编辑的方法*/
    public function update(){
        $msg = '系统错误!';
        $status = 'error';
        $data = array();
        $apply_money=trim(I('apply_money'));
        $proxy_id=I('proxy_id');
        $apply_id=I('apply_id');
        $list=$this->frozen_account_info($apply_id);
        if(empty($list)){
            $this->ajaxReturn(array('msg'=>"对不起，没有找到该信息，请重试",'status'=>$status));
        }
        if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $apply_money)){
            $this->ajaxReturn(array('msg'=>'请输入正确的申请金额！','status'=>$status));
        }
        if($apply_money==''){
            $this->ajaxReturn(array('msg'=>'请输入的申请金额！','status'=>$status));
        }
        if($apply_money<=0){
            $this->ajaxReturn(array('msg'=>'申请金额需大于零！','status'=>$status));
        }
        if($proxy_id==0 || $proxy_id==""){
            $this->ajaxReturn(array('msg'=>'请输入代理商！','status'=>$status));
        }
        $apply_cont=M('proxy_account')->where('proxy_id='.$proxy_id)->field('account_balance')->find();
        if($apply_cont['account_balance']<$apply_money){
            $this->ajaxReturn(array('msg'=>'该代理商账户余额不足，请勿操作！','status'=>$status));
        }
        $proxy_info=$this->proxy_info($proxy_id);
        $data['apply_id']=$apply_id;
        $data['proxy_id']=$proxy_id;
        $data['account_id']=$proxy_info['account_id'];
        $data['opr_type']=1;
        $data['apply_money']=$apply_money;
        $data['apply_date']=date('Y-m-d H:i:s',time());
        // $data['frozen_date']=date('Y-m-d H:i:s',time());
        $data['frozen_remark']=trim(I('frozen_remark'));
        $data['frozen_approve_status']=1;
        $data['modify_user_id']=D('SysUser')->self_id();
        $data['modify_date']=date('Y-m-d H:i:s',time());
        $res=M('proxy_frozen_account')->save($data);
        if($res){
            $msg = '编辑代理商账户冻结金额成功，请等待审核!';
            $status = 'success';
            $n_msg='成功';
        }else{
            $msg = '编辑代理商账户冻结金额失败，请重试!';
            $n_msg='失败';
        }
        $c_item='';
        $c_item.=$proxy_id===$proxy_info['proxy_id']?'':'，代理商【'.obj_name($proxy_id,1).'】';
        $c_item.=$apply_money*100===$proxy_info['apply_money']*100?'':'，冻结金额【'.$apply_money.'】元';

        $note = "用户【".get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，编辑代理商账户冻结金额".$c_item.$n_msg;
        $this->sys_log('编辑代理商账户冻结金额',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    /*冻结初审*/
    public function freeze_approve(){
        $msg = '对不起，没有找到相关内容，请重试！';
        $status = 'error';
        $operate=trim(I('get.operate'));
        if($operate=='approve'){
            /*初审开始*/
            $apply_id=trim(I('apply_id'));
            $approve_status=trim(I('approve_status'));
            $approve_remark=trim(I('approve_remark'));
                $model=M('proxy_frozen_account');
                $model->startTrans();

                if($approve_status==2 && $approve_remark==""){
                    $this->ajaxReturn(array('msg'=>'请填写审核驳回原因','status'=>$status));
                }
                //读取申请信息
                $contract_info = $model->where(array('apply_id'=>$apply_id))->find();
                if(in_array($contract_info['frozen_approve_status'],array(2,3,4,5))){
                    $this->ajaxReturn(array('msg'=>'请勿重复审核！','status'=>$status));
                }
                //修改申请表信息
                $edit['apply_id'] = $apply_id;
                $edit['frozen_approve_status'] = $approve_status==2?"3":"2";
                $edit['frozen_last_approve_date'] = date("Y-m-d H:i:s",time());
                $apply_res=$model->where(array('apply_id'=>$apply_id))->save($edit);
                //添加审核信息
                $add['apply_id'] = $apply_id;
                $add['approve_status'] = $approve_status;
                $add['approve_remark'] = $approve_remark;
                $add['process_type']=1;
                $add['approve_date']=date('Y-m-d H:i:s',time());
                $add['approve_user_id']=D('SysUser')->self_id();
                $add['approve_stage']=1;
                $process = M("proxy_frozen_process")->add($add);

                if($apply_res && $process){
                    $model->commit();
                    $msg = $approve_status==2?'代理商账户冻结初审驳回成功！':'代理商账户冻结初审成功！';
                    $n_msg=$approve_status==2?'初审驳回成功':'初审成功';
                    $status = 'success';
                }else{
                    $model->rollback();
                    $msg = $approve_status==2?'代理商账户冻结初审驳回失败！':'代理商账户冻结初审失败！';
                    $n_msg=$approve_status==2?'初审驳回失败':'初审失败';
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id())."】，审核代理商【".obj_name($contract_info['proxy_id'],1)."】账户冻结，冻结金额【".$contract_info['apply_money']."】元".$n_msg;
                $this->sys_log('代理商账户冻结初审',$note);
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));

        }else{
            $apply_id=I('apply_id');
            $info=$this->frozen_account_info($apply_id);
            if($info){
                if(in_array($info['frozen_approve_status'],array(2,3,4,5))){
                    $this->ajaxReturn(array('msg'=>'请勿重复审核！','status'=>$status));
                }
               $this->assign('type',1);
               $this->assign($info);
               $this->display('approve');
            }else{
                $this->ajaxReturn(array('msg'=>'对不起，没有找到该信息，请重试！','status'=>$status));
            }
        }

    }

    /*复审界面*/
    public function  freeze_approve_c(){
        $msg = '对不起，没有找到相关内容，请重试！';
        $status = 'error';
        $operate=trim(I('get.operate'));
        $tran=trim(I('get.tran'));
        if($operate=='approve'){
            /*初审开始*/
            $apply_id=trim(I('apply_id'));
            $approve_status=trim(I('approve_status'));
            $approve_remark=trim(I('approve_remark'));
            //读取申请信息
            $apply_cont = $this->frozen_account_info($apply_id);
            if($approve_status==1) {
                if ($tran) {
                    //记录数据
                    $da['apply_id'] = $apply_id;
                    $da['approve_status'] = $approve_status;
                    $da['approve_remark'] = $approve_remark;
                    $msg = "确定是否复审通过并向代理商【" . $apply_cont['proxy_name'] . "】账户新增冻结" . $apply_cont['apply_money'] . "元？";
                    $this->ajaxReturn(array('msg' => $msg, 'status' => 'success', 'info' => $da));
                }
            }
            $model=M('proxy_frozen_account');
            $model->startTrans();
            if($approve_status==2 && $approve_remark==""){
                $this->ajaxReturn(array('msg'=>'请填写审核驳回原因','status'=>$status));
            }

            if($apply_cont['frozen_approve_status']==1){
                $this->ajaxReturn(array('msg'=>'请等待初审完成','status'=>$status));
            }
            if($apply_cont['frozen_approve_status']==3){
                $this->ajaxReturn(array('msg'=>'初审驳回,不可复审','status'=>$status));
            }
            if(in_array($apply_cont['frozen_approve_status'],array(4,5))){
                $this->ajaxReturn(array('msg'=>'不可重复审核','status'=>$status));
            }
            //修改申请表信息
            $edit['apply_id'] = $apply_id;
            $edit['frozen_approve_status'] = $approve_status==2?"5":"4";
            if($approve_status==1){
                $edit['frozen_date'] = date("Y-m-d H:i:s",time());
            }
            $edit['frozen_last_approve_date'] = date("Y-m-d H:i:s",time());
            $apply_res=$model->where(array('apply_id'=>$apply_id))->save($edit);

            //添加审核信息
            $add['apply_id'] = $apply_id;
            $add['approve_status'] = $approve_status;
            $add['approve_remark'] = $approve_remark;
            $add['process_type']=1;
            $add['approve_date']=date('Y-m-d H:i:s',time());
            $add['approve_user_id']=D('SysUser')->self_id();
            $add['approve_stage']=2;
            $process = M("proxy_frozen_process")->add($add);

            if($apply_res && $process){
                if($approve_status==2){
                    $model->commit();
                    $msg = '代理商账户冻结复审驳回成功！';
                    $n_msg='复审驳回成功';
                    $status = 'success';
                }else{
                    $account_info=M('proxy_account')->lock(true)->where('proxy_id='.$apply_cont['proxy_id'])->field('account_balance,freeze_money,account_id')->find();
                    $account['account_id']=$account_info['account_id'];
                    $account['account_balance']=$account_info['account_balance']-$apply_cont['apply_money'];
                    $account['freeze_money']=$account_info['freeze_money']+$apply_cont['apply_money'];
                    $account_res=M('proxy_account')->save($account);
                    //$proxy_id=D('SysUser')->self_proxy_id();
                    $account_record=$this->return_account_record($apply_cont['proxy_id'],$account_info['account_balance'],$apply_cont['apply_money'],$account['account_balance'],9,2);
                    if($account_res && $account_record){
                        $model->commit();
                        $status = 'success';
                        $success_mag='，并新增冻结金额【'.$apply_cont['apply_money'].'】元';
                        $n_msg='复审成功';
                        $msg='代理商账户冻结复审成功';
                    }else{
                        $n_msg='复审成功';
                        $model->rollback();
                        $msg='代理商账户冻结复审失败';
                    }
                }
            }else{
                $model->rollback();
                $msg = $approve_status==2?'代理商账户冻结复审驳回失败！':'代理商账户冻结复审失败！';
                $n_msg=$approve_status==2?'复审驳回失败':'复审失败';
            }
           $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，审核代理商【".$apply_cont['proxy_name']."(".$apply_cont['proxy_code'].")】账户冻结，冻结金额【".$apply_cont['apply_money']."】元".$n_msg.$success_mag;
            $this->sys_log('代理商账户冻结复审',$note);
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));

        }else{
            $apply_id=I('apply_id');
            $list=$this->frozen_account_info($apply_id);
            if($list['frozen_approve_status']==1 || $list['frozen_approve_status']==3){
                $this->ajaxReturn(array('msg'=>"请初审通过后再进行复审！",'status'=>$status));
            }
            if($list['frozen_approve_status']==4 || $list['frozen_approve_status']==5){
                $this->ajaxReturn(array('msg'=>"请勿重复审核！",'status'=>$status));
            }
            $this->assign($list);
            $this->display('approve');
        }
    }





    /*详情*/
    public function show(){
        $apply_id=trim(I('apply_id'));
        $list=$this->frozen_account_info($apply_id);
        $map['apply_id']=$apply_id;
        $map['process_type']=1;
        $frozen=M('proxy_frozen_process')->where($map)->select();//冻结
        $where['apply_id']=$apply_id;
        $where['process_type']=2;
        $thaw=M('proxy_frozen_process')->where($where)->select();//解冻
        $this->assign('frozen',$frozen);
        $this->assign('thaw',$thaw);
        $this->assign($list);
        $this->display();
    }

    /*删除详情*/
    public function delete(){
        $msg = '对不起，没有找到相关内容，请重试！';
        $status = 'error';
        $apply_id=trim(I('apply_id'));
        $list=$this->frozen_account_info($apply_id);
        $model=M('proxy_frozen_account');
        if($list){
              if($list['frozen_approve_status']==4 || $list['thaw_approve_status']==4){
                  $msg = '对不起,已经审核通过，请勿删除！';
                  $n_msg='失败';
              }else{
                  $model->startTrans();
                  $where['apply_id']=$apply_id;
                  $info=$model->where($where)->delete();
                  $process=M('proxy_frozen_process')->where($where)->delete();
                  if($info && $process){
                      $model->commit();
                      $msg = '删除代理商账户冻结金额成功！';
                      $n_msg='成功';
                      $status='success';
                  }else{
                      $msg = '删除代理商账户冻结金额失败！';
                      $n_msg='失败';
                      $model->rollback();
                  }
              }
        }else{
            $msg = '对不起，没有找到相关内容，请重试！';
            $n_msg='失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】，，ID【".$apply_id."】，删除代理商账户冻结，代理商【".obj_name($list['proxy_id'],1)."】账户冻结，冻结金额【".$list['apply_money']."】元
".$n_msg;
        $this->sys_log('删除代理商账户冻结',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    /*代理商账户冻结金额解冻*/
    public function  relieve(){
        $msg = '对不起，没有找到相关内容，请重试！';
        $status = 'error';
        $operate=trim(I('get.operate'));
        $tran=trim(I('get.tran'));
        if($operate=='approve'){
            /*初审开始*/
            $apply_id=trim(I('apply_id'));
            $thaw_remark=trim(I('thaw_remark'));
            //读取申请信息
            $apply_cont = $this->frozen_account_info($apply_id);
                if ($tran) {
                    //记录数据
                    $da['apply_id'] = $apply_id;
                    $da['thaw_remark'] = $thaw_remark;
                    $msg = "确定是否解冻代理商【" . $apply_cont['proxy_name'] . "】账户冻结金额" . $apply_cont['apply_money'] . "元？";
                    $this->ajaxReturn(array('msg' => $msg, 'status' => 'success', 'info' => $da));
                }

            $model=M('proxy_frozen_account');
            if(in_array($apply_cont['frozen_approve_status'],array(1,2,3,5))){
                $this->ajaxReturn(array('msg'=>'请等待账户冻结审核完成！','status'=>$status));
            }
            if(in_array($apply_cont['thaw_approve_status'],array(2,4))){
                $this->ajaxReturn(array('msg'=>'请勿重复审核！','status'=>$status));
            }
            //修改申请表信息
            $edit['apply_id'] = $apply_id;
            $edit['opr_type'] = 2;
            $edit['thaw_approve_status'] =1;
            $edit['thaw_remark'] = $thaw_remark;
            $edit['thaw_create_user_id'] =D('SysUser')->self_id();
            $edit['thaw_create_date'] = date("Y-m-d H:i:s",time());
            $edit['thaw_last_approve_date'] = date("Y-m-d H:i:s",time());
            $apply_res=$model->where(array('apply_id'=>$apply_id))->save($edit);
            if($apply_res){
                $msg='代理商账户解冻申请成功！';
                $n_msg='成功';
                $status = 'success';
            }else{
                $msg='代理商账户解冻申请失败！';
                $n_msg='失败';
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，代理商账户解冻申请，代理商【".$apply_cont['proxy_name']."】，解冻金额【".$apply_cont['apply_money']."】元".$n_msg;
            $this->sys_log('代理商账户解冻申请',$note);
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));

        }else{
            $apply_id=I('apply_id');
            $list=$this->frozen_account_info($apply_id);
            if($list['thaw_approve_status']==1){
                $this->ajaxReturn(array('msg'=>"代理商账户冻结的金额已经提交解冻申请，请等待审核！",'status'=>'error'));
            }
            if($list['thaw_approve_status']==2 || $list['thaw_approve_status']==4){
                $this->ajaxReturn(array('msg'=>"请勿重复操作！",'status'=>'error'));
            }
            $this->assign($list);
            $this->display();
        }
    }

    /*解冻初审*/
    public function relieve_approve(){
        $msg = '对不起，没有找到相关内容，请重试！';
        $status = 'error';
        $operate=trim(I('get.operate'));
        if($operate=='approve'){
            /*初审开始*/
            $apply_id=trim(I('apply_id'));
            $approve_status=trim(I('approve_status'));
            $approve_remark=trim(I('approve_remark'));
            $model=M('proxy_frozen_account');
            $model->startTrans();
            //读取申请信息
            $contract_info = $model->where(array('apply_id'=>$apply_id))->find();
            if($approve_status==2 && $approve_remark==""){
                $this->ajaxReturn(array('msg'=>'请填写审核驳回原因','status'=>$status));
            }
            if(in_array($contract_info['frozen_approve_status'],array(1,2,3,5))){
                $this->ajaxReturn(array('msg'=>'请等待账户冻结审核完成！','status'=>$status));
            }
            if(in_array($contract_info['thaw_approve_status'],array(2,3,4,5))){
                $this->ajaxReturn(array('msg'=>'请勿重复审核！','status'=>$status));
            }
            //修改申请表信息
            $edit['apply_id'] = $apply_id;
            $edit['thaw_approve_status'] = $approve_status==2?"3":"2";
            $edit['thaw_last_approve_date'] = date("Y-m-d H:i:s",time());
            $apply_res=$model->where(array('apply_id'=>$apply_id))->save($edit);
            //添加审核信息
            $add['apply_id'] = $apply_id;
            $add['approve_status'] = $approve_status;
            $add['approve_remark'] = $approve_remark;
            $add['process_type']=2;
            $add['approve_date']=date('Y-m-d H:i:s',time());
            $add['approve_user_id']=D('SysUser')->self_id();
            $add['approve_stage']=1;
            $process = M("proxy_frozen_process")->add($add);

            if($apply_res && $process){
                $model->commit();
                $msg = $approve_status==2?'代理商账户金额解冻申请初审驳回成功！':'代理商账户金额解冻申请初审成功！';
                $n_msg=$approve_status==2?'初审驳回成功':'初审成功';
                $status = 'success';
            }else{
                $model->rollback();
                $msg = $approve_status==2?'代理商账户金额解冻申请初审驳回失败！':'代理商账户金额解冻申请初审失败！';
                $n_msg=$approve_status==2?'初审驳回失败':'初审失败';
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，审核代理商【".obj_name($contract_info['proxy_id'],1)."】账户解冻申请，解冻金额【".$contract_info['apply_money']."】元".$n_msg;
            $this->sys_log('代理商账户解冻申请初审',$note);
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));

        }else{
            $apply_id=I('apply_id');
            $info=$this->frozen_account_info($apply_id);
            if($info){
                if(in_array($info['frozen_approve_status'],array(1,2,3,5))){
                    $this->ajaxReturn(array('msg'=>'请等待账户冻结审核完成！','status'=>$status));
                }
                if(in_array($info['thaw_approve_status'],array(2,3,4,5))){
                    $this->ajaxReturn(array('msg'=>'请勿重复审核！','status'=>$status));
                }
                $this->assign('type',1);
                $this->assign($info);
                $this->display('relieve_approve');
            }else{
                $this->ajaxReturn(array('msg'=>'对不起，没有找到该信息，请重试！','status'=>$status));
            }
        }

    }

    /*复审界面*/
    public function  relieve_approve_c(){
        $msg = '对不起，没有找到相关内容，请重试！';
        $status = 'error';
        $operate=trim(I('get.operate'));
        $tran=trim(I('get.tran'));
        if($operate=='approve'){
            /*初审开始*/
            $apply_id=trim(I('apply_id'));
            $approve_status=trim(I('approve_status'));
            $approve_remark=trim(I('approve_remark'));
            //读取申请信息
            $apply_cont = $this->frozen_account_info($apply_id);
            if(in_array($apply_cont['frozen_approve_status'],array(1,2,3,5))){
                $this->ajaxReturn(array('msg'=>'请等待账户冻结审核完成！','status'=>$status));
            }
            if($approve_status==1) {
                if ($tran) {
                    //记录数据
                    $da['apply_id'] = $apply_id;
                    $da['approve_status'] = $approve_status;
                    $da['approve_remark'] = $approve_remark;
                    $msg = "确定是否复审通过并解除代理商【" . $apply_cont['proxy_name'] . "】账户冻结金额的" . $apply_cont['apply_money'] . "元？";
                    $this->ajaxReturn(array('msg' => $msg, 'status' => 'success', 'info' => $da));
                }
            }
            $model=M('proxy_frozen_account');
            $model->startTrans();
            if($approve_status==2 && $approve_remark==""){
                $this->ajaxReturn(array('msg'=>'请填写审核驳回原因','status'=>$status));
            }

            if($apply_cont['thaw_approve_status']==1){
                $this->ajaxReturn(array('msg'=>'请等待初审完成','status'=>$status));
            }
            if($apply_cont['thaw_approve_status']==3){
                $this->ajaxReturn(array('msg'=>'初审驳回,不可复审','status'=>$status));
            }
            if(in_array($apply_cont['thaw_approve_status'],array(4,5))){
                $this->ajaxReturn(array('msg'=>'不可重复审核','status'=>$status));
            }
            //修改申请表信息
            $edit['apply_id'] = $apply_id;
            $edit['thaw_approve_status'] = $approve_status==2?"5":"4";
            if( $edit['thaw_approve_status']==4){
                $edit['thaw_date'] = date("Y-m-d H:i:s",time());
            }
            $edit['thaw_last_approve_date'] = date("Y-m-d H:i:s",time());
            $apply_res=$model->where(array('apply_id'=>$apply_id))->save($edit);

            //添加审核信息
            $add['apply_id'] = $apply_id;
            $add['approve_status'] = $approve_status;
            $add['approve_remark'] = $approve_remark;
            $add['process_type']=2;
            $add['approve_date']=date('Y-m-d H:i:s',time());
            $add['approve_user_id']=D('SysUser')->self_id();
            $add['approve_stage']=2;
            $process = M("proxy_frozen_process")->add($add);

            if($apply_res && $process){
                if($approve_status==2){
                    $model->commit();
                    $msg = '代理商账户解冻申请复审驳回成功！';
                    $n_msg='复审驳回成功';
                    $status = 'success';
                }else{
                    $account_info=M('proxy_account')->lock(true)->where('proxy_id='.$apply_cont['proxy_id'])->field('account_balance,freeze_money,account_id')->find();
                    $account['account_id']=$account_info['account_id'];
                    $account['account_balance']=$account_info['account_balance']+$apply_cont['apply_money'];
                    $account['freeze_money']=$account_info['freeze_money']-$apply_cont['apply_money'];
                    $account_res=M('proxy_account')->save($account);
                    //$proxy_id=D('SysUser')->self_proxy_id();
                    $account_record=$this->return_account_record($apply_cont['proxy_id'],$account_info['account_balance'],$apply_cont['apply_money'],$account['account_balance'],10,1);
                    if($account_res && $account_record){
                        $model->commit();
                        $status = 'success';
                        $success_mag='，并新增解冻金额【'.$apply_cont['apply_money'].'】元';
                        $n_msg='复审成功';
                        $msg='代理商账户解冻申请复审成功';
                    }else{
                        $n_msg='复审失败';
                        $model->rollback();
                        $msg='代理商账户解冻申请复审失败';
                    }
                }
            }else{
                $model->rollback();
                $msg = $approve_status==2?'代理商账户解冻申请复审驳回失败！':'代理商账户解冻申请冻复审失败！';
                $n_msg=$approve_status==2?'复审驳回失败':'复审失败';
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，审核代理商【".obj_name($apply_cont['proxy_id'],1)."】账户解冻，解冻金额【".$apply_cont['apply_money']."】元".$n_msg.$success_mag;
            $this->sys_log('代理商账户解冻申请复审',$note);
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));

        }else{
            $apply_id=I('apply_id');
            $list=$this->frozen_account_info($apply_id);
            if(in_array($list['frozen_approve_status'],array(1,2,3,5))){
                $this->ajaxReturn(array('msg'=>'请等待账户冻结审核完成！','status'=>$status));
            }
            if($list['thaw_approve_status']==1 || $list['thaw_approve_status']==3){
                $this->ajaxReturn(array('msg'=>"请初审通过后再进行复审！",'status'=>$status));
            }
            if($list['thaw_approve_status']==4 || $list['thaw_approve_status']==5){
                $this->ajaxReturn(array('msg'=>"请勿重复审核！",'status'=>$status));
            }
            $this->assign($list);
            $this->display('relieve_approve');
        }
    }
    function proxy_info($id){
        $info=M('proxy as p')->join('left join t_flow_proxy_account as a on a.proxy_id = p.proxy_id')->field('p.proxy_id,p.proxy_name,p.proxy_code,a.account_id,a.account_balance,a.freeze_money,a.credit_money,a.credit_freeze_money')->where('p.proxy_id='.$id)->find();
        return $info;
    }

    function frozen_account_info($id){
        $join=array(
            't_flow_proxy as p on pa.proxy_id= p.proxy_id',
            't_flow_proxy_account as a on p.proxy_id= a.proxy_id'
        );
        $list=M('proxy_frozen_account as pa')
            ->join($join,'left')
            ->field('pa.apply_id,pa.apply_money,pa.apply_date,pa.account_id,pa.create_user_id,pa.opr_type,pa.frozen_date,pa.thaw_date,pa.frozen_approve_status,pa.frozen_last_approve_date,pa.create_date,pa.thaw_create_user_id,pa.thaw_create_date,pa.thaw_approve_status,pa.thaw_last_approve_date,pa.proxy_id,pa.frozen_remark,pa.thaw_remark,p.proxy_code,p.proxy_name,a.account_balance,a.freeze_money,a.credit_money,a.credit_freeze_money')
            ->where('pa.apply_id='.$id)
            ->find();
        return $list;
    }

    /**
    *导出EXCEL
    **/
    public function export_excel(){
        $user = D('SysUser')->self_user_type();//获取自身的用户类型是运营平台，代理商，企业
        $proxy_name = trim(I('proxy_name')); //代理商名称
        $start_datetime = trim(I('start_datetime'));   //开始时间
        $end_datetime = trim(I('end_datetime'));   //结束时间
        $frozen_approve_status=trim(I('frozen_approve_status'));   //冻结审核状态
        $thaw_approve_status=trim(I('thaw_approve_status'));   //解冻审核状态
        $proxy_code=trim(I('proxy_code'));
        $where=array();
        $model=M('proxy_frozen_account pa');
        if($proxy_name){
            $where['p.proxy_name']=array('like','%'.$proxy_name.'%');
        }
        if($proxy_code){
            $where['p.proxy_code']=array('like','%'.$proxy_code.'%');
        }
        if(!empty($frozen_approve_status)){
            $where['pa.frozen_approve_status']=$frozen_approve_status;
        }
        if(!empty($thaw_approve_status) ){
            $where['pa.thaw_approve_status']=$thaw_approve_status;
        }

        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['pa.create_date'] =array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['pa.create_date'] = array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['pa.create_date'] =array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $where['pa.create_date'] = array('between',array($start_datetime,$end_datetime));
        }

        $join=array(
            't_flow_proxy as p on pa.proxy_id= p.proxy_id',
            't_flow_proxy_account as a on p.proxy_id= a.proxy_id'
        );
        $proxy_ids=D('Proxy')->proxy_child_ids();
        $where['pa.proxy_id'] = array(array('in',$proxy_ids),array('eq',D('SysUser')->self_proxy_id()),'or');
        $list=$model->join($join,'left')
            ->field('pa.apply_id,pa.apply_money,pa.apply_date,pa.create_user_id,pa.thaw_create_user_id,pa.thaw_create_date,pa.opr_type,pa.frozen_date,pa.thaw_date,pa.frozen_approve_status,pa.frozen_last_approve_date,pa.create_date,pa.thaw_approve_status,pa.thaw_last_approve_date,p.proxy_code,p.proxy_name,a.account_balance,a.freeze_money,a.credit_money,a.credit_freeze_money')
            ->order("pa.modify_date desc")
            ->where($where)
            ->limit(3000)
            ->select();
                     
        $datas = array();
        $headArr=array("代理商编号","代理商名称","申请金额(元)","类型","冻结审核状态","冻结时间","解冻审核状态","解冻时间","冻结申请时间");

        foreach ($list as $v) {
            $data=array();
            $data['proxy_code'] = ' ' . $v['proxy_code'];
            $data['proxy_name'] = $v['proxy_name'];
            $data['apply_money'] = ' ' . $v['apply_money'];
            $data['opr_type'] = $v['opr_type'] == 1?'冻结':'解冻';
            $data['frozen_approve_status'] = get_frozen_approve_status($v['frozen_approve_status']);
            $data['frozen_date'] = empty($v['frozen_date'])?'--':$v['frozen_date'];
            $data['thaw_approve_status'] = get_thaw_approve_status($v['thaw_approve_status']);
            $data['thaw_date'] = empty($v['thaw_date'])?'--':$v['thaw_date'];
            $data['create_date'] = $v['create_date'];
            array_push($datas,$data);
        }
            
        $title='代理商账户冻结管理';

        ExportEexcel($title,$headArr,$datas);
    }


    /**
     * 添加流水信息
     */
    private function return_account_record($proxy_id,$account_balance,$apply_money,$operater_after_balance,$operate_type,$balance_type){
        $record['operater_before_balance']   = $account_balance;  //操作前金额
        $record['operater_after_balance']    = $operater_after_balance; //操作后金额
        $record['operater_price']            = $apply_money;  //提现金额
        $record['operate_type']              = $operate_type; //资金操作
        $record['balance_type']              = $balance_type;//支出
        $record['record_date']               = date('Y-m-d H:i:s',time());
        $record['user_id']                   = D('SysUser')->self_id();
        $record['operation_date']            = date('Y-m-d H:i:s',time());
        $record['user_type']                 = 1;
        $record['proxy_id']                  = $proxy_id;
        $record['obj_user_type']             = 1;
        $record['obj_proxy_id']              = $proxy_id;
        $record['device_name']               = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));
        //添加流水记录
        $recordResult=M('account_record')->add($record);
        if($recordResult){
            return true;
        }else{
            return false;
        }
    }

}
?>