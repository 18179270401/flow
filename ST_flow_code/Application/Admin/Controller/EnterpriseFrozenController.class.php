<?php

namespace Admin\Controller;
use Think\Controller;
use \Think\Page;

class EnterpriseFrozenController extends CommonController{

    public $os_enterprise_ids;
    public $os_proxy_ids;

    public function start(){
        $this->os_enterprise_ids = D('Enterprise')->enterprise_child_ids();
        $this->os_proxy_ids = D('Proxy')->proxy_child_ids();
    }

    /*代理商账户冻结管理列表*/
    public function index(){
        D("SysUser")->sessionwriteclose();
        $user=D('SysUser')->self_user_type();//获取自身的用户类型是运营平台，代理商，企业
        $enterprise_code=trim(I('enterprise_code'));
        $enterprise_name = trim(I('enterprise_name')); //代理商名称
        $start_datetime = trim(I('start_datetime'));   //开始时间
        $end_datetime = trim(I('end_datetime'));   //结束时间
        $frozen_approve_status=trim(I('frozen_approve_status'));   //冻结审核状态
        $thaw_approve_status=trim(I('thaw_approve_status'));   //解冻审核状态

        $where=array();
        $model=M('enterprise_frozen_account ea');
        if($enterprise_name){
            $where['e.enterprise_name']=array('like','%'.$enterprise_name.'%');
        }
        if($enterprise_code){
            $where['e.enterprise_code']=array('like','%'.$enterprise_code.'%');
        }
        if(!empty($frozen_approve_status)){
            $where['ea.frozen_approve_status']=$frozen_approve_status;
        }
        if(!empty($thaw_approve_status) ){
            $where['ea.thaw_approve_status']=$thaw_approve_status;
        }

        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['ea.create_date'] =array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['ea.create_date'] = array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['ea.create_date'] =array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $where['ea.create_date'] = array('between',array($start_datetime,$end_datetime));
        }

        $join=array(
            't_flow_enterprise as e on ea.enterprise_id = e.enterprise_id',
            't_flow_enterprise_account as a on e.enterprise_id = a.enterprise_id'
        );
        $proxy_ids=D('Proxy')->proxy_child_ids();
        $where['ea.enterprise_id'] = array(array('in',$this->os_enterprise_ids),array('in',D('Enterprise')->enterprise_ids2()),'or');
        $count=$model->join($join,'left')->where($where)->count();
        $Page     = new \Think\Page($count,20);
        $show     = $Page->show();
        $list=$model->join($join,'left')
            ->field('ea.apply_id,ea.apply_money,ea.apply_date,ea.create_user_id,ea.thaw_create_user_id,ea.thaw_create_date,ea.opr_type,ea.frozen_date,ea.thaw_date,ea.frozen_approve_status,ea.frozen_last_approve_date,ea.create_date,ea.thaw_approve_status,ea.thaw_last_approve_date,e.enterprise_code,e.enterprise_name,a.account_balance,a.freeze_money,a.credit_money,a.credit_freeze_money')
            ->order("ea.modify_date desc")
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        $this->assign('sum_money_one',$this->sum_money(1,$where));
        $this->assign('sum_money_tow',$this->sum_money(2,$where));
        $this->assign('usr_type',$user);
        $this->assign('list',get_sort_no($list,$Page->firstRow));
        $this->assign('page',$show);
        $this->assign('user_id',D('SysUser')->self_id());
        $this->assign('d_sdata',date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))));  //默认开始时间
        $this->assign('d_edata',date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))));  //默认结束时间
        $this->display();        //模板
    }

    public function sum_money($opr_type,$where){
        $model=M('enterprise_frozen_account ea');
        $join=array(
            't_flow_enterprise as e on ea.enterprise_id = e.enterprise_id',
            't_flow_enterprise_account as a on e.enterprise_id = a.enterprise_id'
        );
        $where['opr_type']=$opr_type;
        $sum_results =$model
            ->join($join,'left')
            ->where($where)
            ->field('sum(ea.apply_money) as apply_money' )
            ->find();
        return $sum_results['apply_money'];
    }


    /*
	 *代理商账户新增冻结显示页面
	 */
    public function add(){
        $this->assign('enterprise',$this->enterprise_chd());
        $this->display();
    }

    public function  insert(){
        $msg = '系统错误!';
        $status = 'error';
        $data = array();
        $apply_money=trim(I('apply_money'));
        $enterprise_id=I('enterprise_id');
        if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $apply_money)){
            $this->ajaxReturn(array('msg'=>'请输入正确的申请金额！','status'=>$status));
        }
        if($apply_money==''){
            $this->ajaxReturn(array('msg'=>'请输入的申请金额！','status'=>$status));
        }
        if($apply_money<=0){
            $this->ajaxReturn(array('msg'=>'申请金额需大于零！','status'=>$status));
        }
        if($enterprise_id==0 || $enterprise_id==""){
            $this->ajaxReturn(array('msg'=>'请输入企业！','status'=>$status));
        }
        $apply_cont=M('enterprise_account')->where('enterprise_id='.$enterprise_id)->field('account_balance')->find();
        if($apply_cont['account_balance']<$apply_money){
            $this->ajaxReturn(array('msg'=>'该企业账户余额不足，请勿操作！','status'=>$status));
        }
        $enterprise_info=$this->enterprise_info($enterprise_id);
        $data['enterprise_id']=$enterprise_id;
        $data['account_id']=$enterprise_info['account_id'];
        $data['opr_type']=1;
        $data['apply_money']=$apply_money;
        $data['apply_date']=date('Y-m-d H:i:s',time());
        // $data['frozen_date']=date('Y-m-d H:i:s',time());
        $data['frozen_remark']=trim(I('frozen_remark'));
        $data['frozen_approve_status']=1;
        $data['create_user_id']=D('SysUser')->self_id();
        $data['create_date']=date('Y-m-d H:i:s',time());
        $data['modify_user_id']=D('SysUser')->self_id();
        $data['modify_date']=date('Y-m-d H:i:s',time());
        $res=M('enterprise_frozen_account')->add($data);
        if($res){
            $msg = '新增企业账户冻结金额成功，请等待审核!';
            $status = 'success';
            $n_msg='成功';
        }else{
            $msg = '新增企业账户冻结金额失败，请重试!';
            $n_msg='失败';
        }
        $note = "用户【".get_user_name(D('SysUser')->self_id())."】，ID【".$res."】，新增企业账户冻结金额，企业【".obj_name($enterprise_id,2)."】账户，冻结金额【".money_format2($apply_money)."】元".$n_msg;
        $this->sys_log('新增企业账户冻结金额',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    /*冻结编辑*/
    public function edit(){
        $apply_id=I('apply_id');
        $list=$this->frozen_account_info($apply_id);
        if($list){
            $this->assign('enterprise',$this->enterprise_chd());
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
        $enterprise_id=I('enterprise_id');
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
        if($enterprise_id==0 || $enterprise_id==""){
            $this->ajaxReturn(array('msg'=>'请输入企业！','status'=>$status));
        }
        $apply_cont=M('enterprise_account')->where('enterprise_id='.$enterprise_id)->field('account_balance')->find();
        if($apply_cont['account_balance']<$apply_money){
            $this->ajaxReturn(array('msg'=>'该企业账户余额不足，请勿操作！','status'=>$status));
        }
        $proxy_info=$this->enterprise_info($enterprise_id);
        $data['apply_id']=$apply_id;
        $data['enterprise_id']=$enterprise_id;
        $data['account_id']=$proxy_info['account_id'];
        $data['opr_type']=1;
        $data['apply_money']=$apply_money;
        $data['apply_date']=date('Y-m-d H:i:s',time());
        // $data['frozen_date']=date('Y-m-d H:i:s',time());
        $data['frozen_remark']=trim(I('frozen_remark'));
        $data['frozen_approve_status']=1;
        $data['modify_user_id']=D('SysUser')->self_id();
        $data['modify_date']=date('Y-m-d H:i:s',time());
        $res=M('enterprise_frozen_account')->save($data);
        if($res){
            $msg = '编辑企业账户冻结金额成功，请等待审核！';
            $status = 'success';
            $n_msg='成功';
        }else{
            $msg = '编辑企业账户冻结金额失败，请重试！';
            $n_msg='失败';
        }
        $c_item='';
        $c_item.=$enterprise_id===$proxy_info['enterprise_id']?'':'，企业【'.obj_name($enterprise_id,2).'】';
        $c_item.=$apply_money*100===$proxy_info['apply_money']*100?'':'，冻结金额【'.$apply_money.'】元';
        $note = "用户【".get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，编辑企业【".obj_name($proxy_info['enterprise_id'],2)."】账户冻结金额".$c_item.$n_msg;
        $this->sys_log('编辑企业账户冻结金额',$note);
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
            $model=M('enterprise_frozen_account');
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
            $edit['frozen_approve_status']      = $approve_status==2?"3":"2";
            $edit['frozen_last_approve_date']   = date("Y-m-d H:i:s",time());
            $edit['create_user_id']             = D('SysUser')->self_id();
            $edit['modify_date']                = date('Y-m-d H:i:s');
            $apply_res=$model->where(array('apply_id'=>$apply_id))->save($edit);
            //添加审核信息
            $add['apply_id'] = $apply_id;
            $add['approve_status'] = $approve_status;
            $add['approve_remark'] = $approve_remark;
            $add['process_type']=1;
            $add['approve_date']=date('Y-m-d H:i:s',time());
            $add['approve_user_id']=D('SysUser')->self_id();
            $add['approve_stage']=1;
            $process = M("enterprise_frozen_process")->add($add);

            if($apply_res && $process){
                $model->commit();
                $msg = $approve_status==2?'企业账户冻结初审驳回成功！':'企业账户冻结初审成功！';
                $n_msg=$approve_status==2?'初审驳回成功':'初审成功';
                $status = 'success';
            }else{
                $model->rollback();
                $msg = $approve_status==2?'企业账户冻结初审驳回失败！':'企业账户冻结初审失败！';
                $n_msg=$approve_status==2?'初审驳回失败':'初审失败';
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，审核企业【".obj_name($contract_info['enterprise_id'],2)."】账户冻结金额【".$contract_info['apply_money']."】元".$n_msg;
            $this->sys_log('企业账户冻结初审',$note);
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
                    $msg = "确定是否复审通过并向企业【" . $apply_cont['enterprise_name'] . "】账户新增冻结" . $apply_cont['apply_money'] . "元？";
                    $this->ajaxReturn(array('msg' => $msg, 'status' => 'success', 'info' => $da));
                }
            }
            $model=M('enterprise_frozen_account');
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
            $edit['modify_user_id'] = D('SysUser')->self_id();
            $edit['modify_date']    = date('Y-m-d H:i:s');
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
            $process = M("enterprise_frozen_process")->add($add);

            if($apply_res && $process){
                if($approve_status==2){
                    $model->commit();
                    $msg = '企业账户冻结复审驳回成功！';
                    $n_msg='复审驳回成功';
                    $status = 'success';
                }else{
                    $account_info=M('enterprise_account')->lock(true)->where('enterprise_id='.$apply_cont['enterprise_id'])->field('account_balance,freeze_money,account_id')->find();
                    $account['account_id']=$account_info['account_id'];
                    $account['account_balance']=$account_info['account_balance']-$apply_cont['apply_money'];
                    $account['freeze_money']=$account_info['freeze_money']+$apply_cont['apply_money'];
                    $account_res=M('enterprise_account')->save($account);
                   // $top_proxy_id=D('SysUser')->self_proxy_id();
                    $account_record=$this->return_account_record($apply_cont['enterprise_id'],$account_info['account_balance'],$apply_cont['apply_money'], $account['account_balance'],9,2);
                    if($account_res && $account_record){
                        $model->commit();
                        $status = 'success';
                        $success_mag='，企业【'.obj_name($apply_cont['enterprise_id'],2).'】账户,新增冻结金额【'.$apply_cont['apply_money'].'】元';
                        $n_msg='复审成功';
                        $msg='企业账户冻结复审成功';
                    }else{
                        $n_msg='复审成功';
                        $model->rollback();
                        $msg='企业账户冻结复审失败';
                    }
                }
            }else{
                $model->rollback();
                $msg = $approve_status==2?'企业账户冻结复审驳回失败！':'企业账户冻结复审失败！';
                $n_msg=$approve_status==2?'复审驳回失败':'复审失败';
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，审核企业【".obj_name($apply_cont['enterprise_id'],2)."】账户冻结金额【".$apply_cont['apply_money']."】元".$n_msg.$success_mag;
            $this->sys_log('企业账户冻结复审',$note);
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

    /** 查看详情 */
    public function show() {
        $apply_id=trim(I('apply_id'));
        $list=$this->frozen_account_info($apply_id);
        $map['apply_id']=$apply_id;
        $map['process_type']=1;
        $frozen=M('enterprise_frozen_process')->where($map)->select();//冻结
        $where['apply_id']=$apply_id;
        $where['process_type']=2;
        $thaw=M('enterprise_frozen_process')->where($where)->select();//解冻
        $this->assign('frozen',$frozen);
        $this->assign('thaw',$thaw);
        $this->assign($list);
        $this->display();


        /*$msg ="对不起，没有找到相关内容，请重试！";
        $status = 'error';
        $where['efa.apply_id']=trim(I('apply_id'));
        $list=$this->detailed($where);

        if($list){
            $map['apply_id']=trim(I('apply_id'));
            $this->assign('process',$this->process($map));
            //$this->assign('list',$list);
            $this->assign($list);
            $this->display();
        }else{
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }*/

    }

    /*删除详情*/
    public function delete(){
        $msg = '对不起，没有找到相关内容，请重试！';
        $status = 'error';
        $apply_id=trim(I('apply_id'));
        $list=$this->frozen_account_info($apply_id);
        $model=M('enterprise_frozen_account');
        if($list){
            if($list['frozen_approve_status']==4 || $list['thaw_approve_status']==4){
                $msg = '对不起,已经审核通过，请勿删除！';
                $n_msg='失败';
            }else{
                $model->startTrans();
                $where['apply_id']=$apply_id;
                $info=$model->where($where)->delete();
                $process=M('enterprise_frozen_process')->where($where)->delete();
                if($info && $process){
                    $model->commit();
                    $msg = '删除企业账户冻结金额成功！';
                    $n_msg='成功';
                    $status='success';
                }else{
                    $msg = '删除企业账户冻结金额失败！';
                    $n_msg='失败';
                    $model->rollback();
                }
            }
        }else{
            $msg = '对不起，没有找到相关内容，请重试！';
            $n_msg='失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，删除企业【".obj_name($list['enterprise_id'],2)."】账户冻结金额【".$list['apply_money']."】元".$n_msg;
        $this->sys_log('删除企业账户冻结',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    /*企业账户冻结金额解冻*/
    public function  relieve(){
        $msg = '对不起，没有找到相关内容，请重试！';
        $status = 'error';
        $operate=trim(I('get.operate'));
        $tran=trim(I('get.tran'));
        if($operate=='approve'){
            /*初审开始*/
            $apply_id=I('apply_id');
            $thaw_remark=trim(I('thaw_remark'));
            //读取申请信息
            $apply_cont = $this->frozen_account_info($apply_id);
            if ($tran) {
                //记录数据
                $da['apply_id'] = $apply_id;
                $da['thaw_remark'] = $thaw_remark;
                $msg = "确定是否解冻企业【" . $apply_cont['enterprise_name'] . "】账户冻结金额" . $apply_cont['apply_money'] . "元？";
                $this->ajaxReturn(array('msg' => $msg, 'status' => 'success', 'info' => $da));
            }

            $model=M('enterprise_frozen_account');
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
            $edit['modify_user_id'] = D('SysUser')->self_id();
            $edit['modify_date'] = date('Y-m-d H:i:s');
            $apply_res=$model->where(array('apply_id'=>$apply_id))->save($edit);
            if($apply_res){
                $msg='企业账户金额解冻申请成功！';
                $n_msg='解冻成功';
                $status = 'success';
            }else{
                $msg='企业账户金额解冻失败！';
                $n_msg='失败';
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】， 企业账户金额解冻，企业【".$apply_cont['enterprise_name'].'('.$apply_cont['enterprise_code'].")】账户，冻结金额【".$apply_cont['apply_money']."】元".$n_msg;
            $this->sys_log('企业账户金额解冻',$note);
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }else{
            $apply_id=I('apply_id');
            $list=$this->frozen_account_info($apply_id);
            if($list['thaw_approve_status']==1){
                $this->ajaxReturn(array('msg'=>"企业账户冻结的金额已经解冻，请等待审核！",'status'=>'error'));
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
            $apply_id=I('apply_id');
            $approve_status=trim(I('approve_status'));
            $approve_remark=trim(I('approve_remark'));
            $model=M('enterprise_frozen_account');
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
            $edit['modify_user_id'] = D('SysUser')->self_id();
            $edit['modify_date']    = date('Y-m-d H:i:s');
            $apply_res=$model->where(array('apply_id'=>$apply_id))->save($edit);

            //添加审核信息
            $add['apply_id'] = $apply_id;
            $add['approve_status'] = $approve_status;
            $add['approve_remark'] = $approve_remark;
            $add['process_type']=2;
            $add['approve_date']=date('Y-m-d H:i:s',time());
            $add['approve_user_id']=D('SysUser')->self_id();
            $add['approve_stage']=1;
            $process = M("enterprise_frozen_process")->add($add);

            if($apply_res && $process){
                $model->commit();
                $msg = $approve_status==2?'企业账户金额解冻申请初审驳回成功！':'企业账户金额解冻申请初审成功！';
                $n_msg=$approve_status==2?'初审驳回成功':'初审成功';
                $status = 'success';
            }else{
                $model->rollback();
                $msg = $approve_status==2?'企业账户金额解冻申请初审驳回失败！':'企业账户金额解冻申请初审失败！';
                $n_msg=$approve_status==2?'初审驳回失败':'初审失败';
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，审核企业【".obj_name($contract_info['enterprise_id'],2)."】账户解冻金额【".$contract_info['apply_money']."】元".$n_msg;
            $this->sys_log('企业账户解冻初审',$note);
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
                    $msg = "确定是否复审通过并解除企业【" . $apply_cont['enterprise_name'] . "】账户冻结金额的" . $apply_cont['apply_money'] . "元？";
                    $this->ajaxReturn(array('msg' => $msg, 'status' => 'success', 'info' => $da));
                }
            }
            $model=M('enterprise_frozen_account');
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
            $process = M("enterprise_frozen_process")->add($add);

            if($apply_res && $process){
                if($approve_status==2){
                    $model->commit();
                    $msg = '企业账户解冻申请复审驳回成功！';
                    $n_msg='复审驳回成功';
                    $status = 'success';
                }else{
                    $account_info=M('enterprise_account')->lock(true)->where('enterprise_id='.$apply_cont['enterprise_id'])->field('account_balance,freeze_money,account_id')->find();
                    $account['account_id']=$account_info['account_id'];
                    $account['account_balance']=$account_info['account_balance']+$apply_cont['apply_money'];
                    $account['freeze_money']=$account_info['freeze_money']-$apply_cont['apply_money'];
                    $account_res=M('enterprise_account')->save($account);
                    //$top_proxy_id=D('SysUser')->self_proxy_id();
                    $account_record=$this->return_account_record($apply_cont['enterprise_id'],$account_info['account_balance'],$apply_cont['apply_money'], $account['account_balance'],10,1);
                    if($account_res && $account_record){
                        $model->commit();
                        $status = 'success';
                        $success_mag='，企业【'.$apply_cont['enterprise_name'].'('.$apply_cont['enterprise_code'].')】账户新增解冻金额【'.$apply_cont['apply_money'].'】元';
                        $n_msg='复审成功';
                        $msg='企业账户解冻申请复审成功';
                    }else{
                        $n_msg='复审成功';
                        $model->rollback();
                        $msg='企业账户解冻申请复审失败';
                    }
                }
            }else{
                $model->rollback();
                $msg = $approve_status==2?'企业账户解冻申请复审驳回失败！':'企业账户解冻申请复审失败！';
                $n_msg=$approve_status==2?'复审驳回失败':'复审失败';
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，审核企业【".$apply_cont['enterprise_name']."(".$apply_cont['enterprise_code'].")】账户解冻金额【".$apply_cont['apply_money']."】元".$n_msg.$success_mag;
            $this->sys_log('企业账户解冻复审',$note);
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

    function enterprise_info($id){
        $info=M('enterprise as e')->join('left join t_flow_enterprise_account as a on a.enterprise_id = e.enterprise_id')->field('e.enterprise_id,e.enterprise_name,e.enterprise_code,a.account_id,a.account_balance,a.freeze_money,a.credit_money,a.credit_freeze_money')->where('e.enterprise_id='.$id)->find();
        return $info;
    }

    /**
     * 企业冻结账户信息
     */
    function frozen_account_info($id){
        $join=array(
            't_flow_enterprise as e on ea.enterprise_id= e.enterprise_id',
            't_flow_enterprise_account as a on e.enterprise_id= a.enterprise_id'
        );
        $list=M('enterprise_frozen_account as ea')
            ->join($join,'left')
            ->field('ea.apply_id,ea.apply_money,ea.apply_date,ea.account_id,ea.create_user_id,ea.opr_type,ea.frozen_date,ea.thaw_date,ea.frozen_approve_status,ea.frozen_last_approve_date,ea.create_date,ea.thaw_create_user_id,ea.frozen_remark,ea.thaw_create_date,ea.thaw_approve_status,ea.thaw_last_approve_date,ea.enterprise_id,ea.frozen_remark,ea.thaw_remark,e.enterprise_code,e.enterprise_name,a.account_balance,a.freeze_money,a.credit_money,a.credit_freeze_money')
            ->where('ea.apply_id='.$id)
            ->find();
        return $list;
    }


    /**
     * 子企业
     */
    public function enterprise_chd(){
        $where['enterprise_id'] = array(array('in',$this->os_enterprise_ids),array('in',D('Enterprise')->enterprise_ids2()),'or') ;
        $where['approve_status']=1;
        $where['status']=1;
        $list=M('enterprise')->where($where)->field('enterprise_name,enterprise_id')->select();
        return $list;
    }

    /**
     * 查看详情
     */
    public  function detailed($where){
        $where['e.status']=1;
        $list =M('enterprise_frozen_account as  efa')
            ->join('left join t_flow_enterprise as e on e.enterprise_id = efa.enterprise_id')
            ->field('efa.*,e.enterprise_name,e.enterprise_code')
            ->where($where)
            ->find();
        return $list;
    }

    /**
     * 审核进度
     */
    public function process($where){
        $list=M("enterprise_frozen_process")->where($where)->select();
        return $list;
    }
    /**
     * 添加流水信息
     */
    private function return_account_record($enterprise_id,$account_balance,$apply_money,$operater_after_balance,$operate_type,$balance_type){
        $record['operater_before_balance']   = $account_balance;  //操作前金额
        $record['operater_after_balance']    = $operater_after_balance; //操作后金额
        $record['operater_price']            = $apply_money;  //提现金额
        $record['operate_type']              = $operate_type; //资金操作
        $record['balance_type']              = $balance_type;//支出
        $record['record_date']               = date('Y-m-d H:i:s',time());
        $record['user_id']                   = D('SysUser')->self_id();
        $record['operation_date']            = date('Y-m-d H:i:s',time());
        $record['user_type']                 = 2;
        $record['enterprise_id']             = $enterprise_id;
        $record['obj_user_type']             = 2;
        $record['obj_enterprise_id']         = $enterprise_id;
        $record['device_name']               = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));
        //添加流水记录
        $recordResult=M('account_record')->add($record);
        if($recordResult){
            return true;
        }else{
            return false;
        }
    }

    /**
    *导出EXCEL
    **/
    public function export_excel(){
        $user=D('SysUser')->self_user_type();//获取自身的用户类型是运营平台，代理商，企业
        $enterprise_code=trim(I('enterprise_code'));
        $enterprise_name = trim(I('enterprise_name')); //代理商名称
        $start_datetime = trim(I('start_datetime'));   //开始时间
        $end_datetime = trim(I('end_datetime'));   //结束时间
        $frozen_approve_status=trim(I('frozen_approve_status'));   //冻结审核状态
        $thaw_approve_status=trim(I('thaw_approve_status'));   //解冻审核状态

        $where=array();
        $model=M('enterprise_frozen_account ea');
        if($enterprise_name){
            $where['e.enterprise_name']=array('like','%'.$enterprise_name.'%');
        }
        if($enterprise_code){
            $where['e.enterprise_code']=array('like','%'.$enterprise_code.'%');
        }
        if(!empty($frozen_approve_status)){
            $where['ea.frozen_approve_status']=$frozen_approve_status;
        }
        if(!empty($thaw_approve_status) ){
            $where['ea.thaw_approve_status']=$thaw_approve_status;
        }

        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['ea.create_date'] =array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['ea.create_date'] = array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['ea.create_date'] =array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $where['ea.create_date'] = array('between',array($start_datetime,$end_datetime));
        }

        $join=array(
            't_flow_enterprise as e on ea.enterprise_id = e.enterprise_id',
            't_flow_enterprise_account as a on e.enterprise_id = a.enterprise_id'
        );
        $proxy_ids=D('Proxy')->proxy_child_ids();
        $where['ea.enterprise_id'] = array(array('in',$this->os_enterprise_ids),array('in',D('Enterprise')->enterprise_ids2()),'or');
        
        $list=$model->join($join,'left')
            ->field('ea.apply_id,ea.apply_money,ea.apply_date,ea.create_user_id,ea.thaw_create_user_id,ea.thaw_create_date,ea.opr_type,ea.frozen_date,ea.thaw_date,ea.frozen_approve_status,ea.frozen_last_approve_date,ea.create_date,ea.thaw_approve_status,ea.thaw_last_approve_date,e.enterprise_code,e.enterprise_name,a.account_balance,a.freeze_money,a.credit_money,a.credit_freeze_money')
            ->order("ea.modify_date desc")
            ->where($where)
            ->limit(3000)
            ->select();

        $datas = array();
        $headArr=array("企业编号","企业名称","申请金额(元)","类型","冻结审核状态","冻结时间","解冻审核状态","解冻时间","冻结申请时间");
        foreach ($list as $v) {
            $data=array();
            $data['enterprise_code'] = $v['enterprise_code'];
            $data['enterprise_name'] = $v['enterprise_name'];
            $data['apply_money'] = $v['apply_money'];
            $data['opr_type'] = $v['opr_type'] == 1?"冻结":"解冻";
            $data['frozen_approve_status'] = get_frozen_approve_status($v['frozen_approve_status']);
            $data['frozen_date'] = empty($v['frozen_date'])?'--':$v['frozen_date'];
            $data['thaw_approve_status'] = get_thaw_approve_status($v['thaw_approve_status']);
            $data['thaw_date'] = empty($v['thaw_date'])?'--':$v['thaw_date'];
            $data['create_date'] = $v['create_date'];
            array_push($datas,$data);
        }
            
        $title='企业账户冻结管理';

        ExportEexcel($title,$headArr,$datas);
    }


}
?>