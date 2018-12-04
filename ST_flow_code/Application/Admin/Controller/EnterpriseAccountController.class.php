<?php 
/**
  * 企业账户管理
  *
  */

namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class EnterpriseAccountController extends CommonController{
     /*企业*/
     public function index(){
            D("SysUser")->sessionwriteclose();
             $user_type=D('SysUser')->self_user_type();//获取自身的用户类型是运营平台，代理商，企业
             $enterprise_name = trim(I('get.enterprise_name')); //企业名称
             $start_datetime = trim(I('get.start_datetime'));   //开始时间
             $end_datetime = trim(I('get.end_datetime'));   //结束时间
             $enterprise_code=trim(I('get.enterprise_code'));
             $top_proxy_code=trim(I('get.top_proxy_code'));
             $top_proxy_name=trim(I('get.top_proxy_name'));

             $top_proxy_ids = $tpids1 = $tpids2 = array();
          /*   if($top_proxy_code) {
                 $tpids1 = D('Proxy')->get_proxyid_by_proxycode($top_proxy_code);
             }
             if($top_proxy_name) {
                 $tpids2 = D('Proxy')->get_proxyid_by_proxyname($top_proxy_name);
             }
             $top_proxy_ids = array_merge($tpids1, $tpids2);
             if(!empty($top_proxy_code) || !empty($top_proxy_name)) {
                 if($top_proxy_ids){
                     $top_proxy_ids = array_unique($top_proxy_ids);
                     $where['e.top_proxy_id'] = array('in', $top_proxy_ids);
                 }else{
                     $where['e.top_proxy_id'] = -1;
                 }
             }*/
             if($top_proxy_code){
                 $where['up.proxy_code']=array('like','%'.$top_proxy_code.'%');
             }
             if($top_proxy_name){
                 $where['up.proxy_name']=array('like','%'.$top_proxy_name.'%');
             }
             if($enterprise_code){
                 $where['e.enterprise_code']=array('like','%'.$enterprise_code.'%');
             }
             if($enterprise_name){
                 $where['e.enterprise_name']=array('like','%'.$enterprise_name.'%');
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
             }
         
             $where['e.approve_status']=1;
            //var_dump($where);exit;
            $list=D("EnterpriseAccount")->enterprise_account_list($where);
            //var_dump($list);exit;
            if($user_type==2){
                $this->assign("is_admin",D("SysUser")->is_admin());
            }
            $this->assign('user_type',$user_type);
            $this->assign('list',$list['list']);
            $this->assign('page',$list['page']);
         if($user_type!=3){
             $this->assign('sum_results',$list['sum_results']);
         }
            if($user_type==2){
                $this->display("index_proxy");
                exit();
            }
            $this->display();        //模板
        }
               /*$where['e.top_proxy_id']=D('SysUser')->self_proxy_id();//获取自身的代理商ID
                $list=D('EnterpriseAccount')->enterprise_account_list($where);
                $this->assign('usr_type',$user);
             if($user==3){
                 $this->assign('list',$list);
             }else{
                 $this->assign('list',$list['list']);
                 $this->assign('page',$list['page']);
             }
                $this->assign('is_top_proxy_admin',D('SysUser')->is_top_proxy_admin());
                $this->assign('is_admin',D('SysUser')->is_admin());
                $this->display();        //模板
               // $this->error('权限不足');
     }*/
	/*
	 *企业充值页面显示
	 */
    public function add(){
        $where['p.top_proxy_id']=D('SysUser')->self_proxy_id();
        $this->assign('list',D('EnterpriseAccount')->allEnterprise($where));
        $this->display();        //模板
    }

    /*代理商充值功能*/
    public function insert(){
        $msg = '系统错误!';
        $status = 'error';
        $data = array();
        if(I('apply_money')==""){
            $msg = '请输入付款金额！';
        }else if(I('source')==0){
            $msg = '请选择付款方式！';
        }else{
                $icense_img='';
            /*$fileinfo = $this->business_licence_upload(C('PROXY_UPLOAD_DIR'));
            if($fileinfo){
                    $icense_img = substr(C('UPLOAD_DIR').$fileinfo['file']['savepath'].$fileinfo['file']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['file']['savepath'].$fileinfo['file']['savename'])-1);
            }else{
                    $msg = $this->business_licence_upload_Error;
                     $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
            }*/
                $map['enterprise_id']=D('SysUser')->self_enterprise_id();
                $number=M('enterprise')->where($map)->field('enterprise_code')->find();
                //$top_proxy_id=D('SysUser')->up_proxy_info();
                $data['top_proxy_id']=D('SysUser')->self_top_proxy_id();
                $data['enterprise_id']=D('SysUser')->self_enterprise_id();
                $data['create_user_id']=D('SysUser')->self_id();
                $data['apply_code']=apply_number($number['enterprise_code'],5);
                $data['apply_money']=trim(I('apply_money'));
                $data['credential']=$icense_img;
                $data['source']=trim(I('source'));
                $data['apply_type']=trim(I('apply_type'));
                $data['remark']=trim(I('remark'));
                $data['create_date']=date('Y-m-d H:i:s',time());
            //执行添加
            $id=M('enterprise_apply')->add($data);
            if($id){
                $msg = '充值成功！';
                $note_msg='成功';
                $status = 'success';
            }else{
                $note_msg='失败';
                $msg = '充值失败！';
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$id."】，向企业【".obj_name( $data['enterprise_id'],2)."】账户充值，充值编号【". $data['apply_code']."】，充值金额【". money_format2($data['apply_money'])."】元".$note_msg;
            $this->sys_log('企业账户充值',$note);
        }
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }



    public function  show(){
        $this->assign(D('EnterpriseAccount')->account_detailed());
        $this->display('detailed');        //模板
    }

    //企业详细
    public function eninfo(){
        $status="error";
        if(I("get.enterprise_id")){
            $map['e.enterprise_id']=I("get.enterprise_id");
            $list=M('Enterprise as e')
            ->join("t_flow_proxy as p on p.proxy_id = e.top_proxy_id")
            ->join("t_flow_sys_user as u on u.user_id = e.approve_user_id")
            ->join("t_flow_enterprise_account as ea on ea.enterprise_id=e.enterprise_id")
            ->field("e.*,p.proxy_name,p.proxy_code,u.user_name as modify_name,ea.account_balance,ea.freeze_money") 
            ->where($map)           
            ->find();
            if(!$list){
                 $this->ajaxReturn(array('msg'=>'企业不存在','status'=>$status));
            }
            $this->assign("list",$list);
            $this->display();
        }else{
            $this->ajaxReturn(array('msg'=>'系统错误','status'=>$status));
        }
    }
    //收回
    public function return_money(){
        $status="error";
        if(I("get.enterprise_id")){
            $map['e.enterprise_id']=I("get.enterprise_id");
            $list=M("Enterprise e")
            ->join("t_flow_enterprise_account as ea on ea.enterprise_id=e.enterprise_id")
            ->where($map)
            ->field("e.*,ea.account_id,ea.account_balance,ea.freeze_money")
            ->find();
            $where['p.proxy_id']=$list['top_proxy_id'];
            $proxy=M('proxy as p')
            ->join("t_flow_proxy_account as ea on ea.proxy_id=p.proxy_id")
            ->where($where)
            ->field("p.*,ea.account_id,ea.account_balance,ea.freeze_money")
            ->find();
            $this->assign("proxy",$proxy);
            $this->assign("type","back");
            $this->assign("list",$list);
            $this->display("transfer");
        }else{
            $this->ajaxReturn(array('msg'=>'系统错误','status'=>$status));
        }
    }
    //充值
    public function recharg_money(){
        $status="error";
        if(I("get.enterprise_id")){
            $map['e.enterprise_id']=I("get.enterprise_id");
            $list=M("Enterprise e")
            ->join("t_flow_enterprise_account as ea on ea.enterprise_id=e.enterprise_id")
            ->where($map)
            ->field("e.*,ea.account_id,ea.account_balance,ea.freeze_money")
            ->find();
            $where['p.proxy_id']=$list['top_proxy_id'];
            $proxy=M('proxy as p')
            ->join("t_flow_proxy_account as ea on ea.proxy_id=p.proxy_id")
            ->where($where)
            ->field("p.*,ea.account_id,ea.account_balance,ea.freeze_money")
            ->find();
            $this->assign("proxy",$proxy);
            $this->assign("list",$list);
            $this->display("transfer");
        }else{
             $this->ajaxReturn(array('msg'=>'系统错误','status'=>$status));
        }
    }

    //冻结
    public function freeze_money(){
        $status="error";
        if(I("get.enterprise_id")){
            $map['e.enterprise_id']=I("get.enterprise_id");
            $list=M("Enterprise e")
            ->join("t_flow_enterprise_account as ea on ea.enterprise_id=e.enterprise_id")
            ->where($map)
            ->field("e.*,ea.account_id,ea.account_balance,ea.freeze_money")
            ->find();
            $this->assign("list",$list);
            $this->display();
        }else{
             $this->ajaxReturn(array('msg'=>'系统错误','status'=>$status));
        }
    }
    public function transfer(){
        $msg = '系统错误';
        $status = 'error';
        if(I("freeze")){
            if(I("get.tran")){
                $status = 'error';
                $da['apply_money']=trim(I("apply_money"));
                $da['freeze']=trim(I('freeze'));
                if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/',$da['apply_money'])){
                    $this->ajaxReturn(array('msg'=>'请输入正确的金额！','status'=>$status));
                }
                 if(!($da['apply_money']>0)){
                    if($da['type']){
                        $this->ajaxReturn(array('msg'=>'金额不能小于等于0！','status'=>$status));
                    }else{
                        $this->ajaxReturn(array('msg'=>'金额不能小于等于0！','status'=>$status));
                    }
                 }
                $da['enterprise_id']=trim(I("enterprise_id"));
                $enterprise=M("enterprise as e ")->join("t_flow_proxy as p on p.proxy_id=e.top_proxy_id","left")->where(array("e.enterprise_id"=>$da['enterprise_id']))->field("e.enterprise_name,p.proxy_name")->find();
                $da['account_id']=trim(I("account_id"));
                $da['apply_type']=trim(I("apply_type"));
                $da['remark']=trim(I('remark'));
                $status="success";
                if($da['freeze']==1){
                    $msg="确定是否从企业【".$enterprise['enterprise_name']."】账户转入冻结余额".$da['apply_money']."元？";
                }else{
                    $msg="确定是否从企业【".$enterprise['enterprise_name']."】账户转出冻结余额".$da['apply_money']."元？";
                }
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'info'=>$da));    
            }
            $enterprise_id=trim(I("enterprise_id"));
            if(empty($enterprise_id)){
                $this->ajaxReturn(array('msg'=>"信息有误","status"=>$status));
            }
            M("enterprise_account")->startTrans();
            $ec=M("enterprise_account")->lock(true)->where(array("enterprise_id"=>$enterprise_id))->find();
            if(empty($ec)){
                M("enterprise_account")->rollback();
               $this->ajaxReturn(array('msg'=>"信息有误","status"=>$status)); 
            }
            $apply_money=trim(I('apply_money'));
            if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $apply_money)){
                M("enterprise_account")->rollback();
                $this->ajaxReturn(array('msg'=>'请输入正确的金额！','status'=>$status));
            }
            if(!($apply_money>0)){
                M("enterprise_account")->rollback();
                $this->ajaxReturn(array('msg'=>'金额不能小于等于0！','status'=>$status));
            }
            $freeze=trim(I("freeze"));
            if($freeze==1){
                if($ec['account_balance']<$apply_money){
                    M("enterprise_account")->rollback();
                    $this->ajaxReturn(array('msg'=>'所选企业账户余额不足！','status'=>$status));
                }else{
                    $edit['account_balance'] = $ec['account_balance'] - $apply_money;
                    $edit['freeze_money'] = $ec['freeze_money'] + $apply_money;
                }
            }else{
                if($ec['freeze_money']<$apply_money){
                    M("enterprise_account")->rollback();
                    $this->ajaxReturn(array('msg'=>'所选企业冻结余额不足！','status'=>$status));
                }else{
                    $edit['freeze_money'] = $ec['freeze_money'] - $apply_money;
                    $edit['account_balance'] = $ec['account_balance'] + $apply_money;
                }
            }
            $edit['modify_user_id']=D('SysUser')->self_id();
            $edit['modify_date']=date('Y-m-d H:i:s',time());
            $res=M("enterprise_account")->where(array('account_id'=>$ec['account_id']))->save($edit);
            $operate_type=$freeze==1?9:10;
            $balance_type=$freeze==1?2:1;
            $account_record=$this->return_account_record($enterprise_id,$ec['account_balance'],$apply_money,$edit['account_balance'],$operate_type,$balance_type);
            if($res && $account_record){
                $status = 'success';
                $msg=$freeze==1?'账户余额转冻结余额成功！':'冻结余额转账户余额成功！';
                $note_z=$freeze==1?'转入':'转出';
                M("enterprise_account")->commit();
                $note_msg='成功';
            }else{
                $msg=$freeze==1?'账户余额转冻结余额失败！':'冻结余额转账户余额失败！';
                $note_z=$freeze==1?'转入':'转出';
                $note_msg='失败';
                M("enterprise_account")->rollback();
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id())."】，从企业【".obj_name($enterprise_id,2)."】账户".$note_z."冻结余额，冻结金额【".money_format2($apply_money)."】元".$note_msg;
            $this->sys_log('企业账户'.$note_z.'冻结余额',$note);
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }else{
            if(I("get.tran")){
                $status = 'error';
                $da['apply_money']=trim(I("apply_money"));
                $da['type']=trim(I("type"));
                if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/',$da['apply_money'])){
                    $this->ajaxReturn(array('msg'=>'请输入正确的金额！','status'=>$status));
                }

                 if(!($da['apply_money']>0)){
                    if($da['type']){
                        $this->ajaxReturn(array('msg'=>'收回金额不能小于等于0！','status'=>$status));
                    }else{
                        $this->ajaxReturn(array('msg'=>'充值金额不能小于等于0！','status'=>$status));
                    }
                 }
                $da['enterprise_id']=trim(I("enterprise_id"));
                $enterprise=M("enterprise as e ")->join("t_flow_proxy as p on p.proxy_id=e.top_proxy_id","left")->where(array("e.enterprise_id"=>$da['enterprise_id']))->field("e.enterprise_name,p.proxy_name")->find();
                $da['account_id']=trim(I("account_id"));
                $da['apply_type']=trim(I("apply_type"));
                $da['remark']=trim(I('remark'));
                $status="success";
                if($da['type']){
                    $msg="确定是否从企业【".$enterprise['enterprise_name']."】账户收回".$da['apply_money']."元？";
                }else{
                    $title='';
                    if(trim(I('test_models'))==1){
                        $title='测试款';
                    }
                    $msg="确定是否向企业【".$enterprise['enterprise_name']."】账户充值".$title.$da['apply_money']."元？";
                }
                $da['test_models']=trim(I('test_models'));
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'info'=>$da));
            }
            $model=M('proxy_account');
            $model->startTrans();
            $enterprise_id=trim(I("enterprise_id"));
            if(empty($enterprise_id)){
                $this->ajaxReturn(array('msg'=>"信息有误","status"=>$status));
            }
            $ec=M("enterprise_account")->lock(true)->where(array("enterprise_id"=>$enterprise_id))->find();
            if(empty($ec)){
               $model->rollback();
               $this->ajaxReturn(array('msg'=>"信息有误","status"=>$status)); 
            }
            $apply_money=trim(I('apply_money'));
            $user=D('SysUser')->self_user_type();
            $en=M("enterprise")->where(array("enterprise_id"=>$enterprise_id))->field("top_proxy_id")->find();
            $where['proxy_id']=$en['top_proxy_id'];
            $Balance=$model->where($where)->find();
            if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $apply_money)){
                $model->rollback();
                $this->ajaxReturn(array('msg'=>'请输入正确的金额！','status'=>$status));
            }

            if(!($apply_money>0)){
                if(I("type")){
                    $model->rollback();
                    $this->ajaxReturn(array('msg'=>'收回金额不能小于等于0！','status'=>$status));
                }else{
                    $model->rollback();
                    $this->ajaxReturn(array('msg'=>'充值金额不能小于等于0！','status'=>$status));
                }
            }
            if(I("type")){
                if($ec['account_balance']<$apply_money){
                    $model->rollback();
                    $this->ajaxReturn(array('msg'=>'对不起，收回金额不能大于账户余额！','status'=>$status));
                }
            }else{
                    if($Balance['account_balance']<$apply_money){
                        $model->rollback();
                        $this->ajaxReturn(array('msg'=>'对不起，您的账户余额不足，请充值后，再操作！','status'=>$status));
                    }
            }
            $condition['top_account_id']=$Balance['account_id']; //上级代理上账户id
            $condition['top_account_balance']=$Balance['account_balance']; //上级代理上账户余额
            if(I("type")){
                $condition['top_operate_type']=5; //收回-上级代理商
                $condition['top_balance_type']=1;//收入-上级代理商
            }else{
                $condition['top_operate_type']=4; //划拨-上级代理商
                $condition['top_balance_type']=2;//支出-上级代理商
            }
            $condition['top_proxy_id']=$en['top_proxy_id'];
            $condition['proxy_type']=$Balance["proxy_type"];
            $condition['top_user_type']=1;
            $condition['apply_money']=$apply_money;   //需要操作的金额
            $condition['operate_account_id']=trim(I('account_id'));//收入-下级代理商
            $condition['operate_account_balance']=$ec['account_balance'];//要操作的代理商账户余额
            $condition['operate_enterprise_id']=trim(I('enterprise_id')); //要操作的代理商账户ID
            $condition['remark']=trim(I('remark'));
            if(I("type")){
                $condition['operate_operate_type']=3; //提现-下级企业
                $condition['operate_balance_type']=2;//支出-下级企业
            }else{
                //$condition['operate_operate_type']=2; //充值-下级企业
                if(trim(I('test_models'))==2){
                    $condition['operate_operate_type']=2; // 充值-下级代理商
                }else{
                    $condition['operate_operate_type']=8; //充值-为测试款
                }
                $condition['operate_balance_type']=1;//收入-下级企业
            }
            $condition['operate_user_type']=2;
            if(I("type")){
                if(D('EnterpriseAccount')->account_sh($condition)>0){
                    $model->commit();
                    $msg = '资金收回成功！';
                    $note_msg='成功';
                    $status = 'success';
                }else{
                    $msg ='资金收回失败！';
                    $note_msg='失败';
                    $model->rollback();
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id())."】，从企业【".obj_name($enterprise_id,2)."】账户收回，回收资金【".money_format2($apply_money)."】元".$note_msg;
                $this->sys_log('企业资金收回',$note);
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }else{
                if(D('EnterpriseAccount')->account_record($condition)>0){
                    $model->commit();
                    $msg = '资金充值成功！';
                    $note_msg='成功';
                    $status = 'success';
                }else{
                    $msg ='资金充值失败！';
                    $note_msg='失败';
                    $model->rollback();
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id())."】，向企业【".obj_name($enterprise_id,2)."】账户充值，充值资金【".money_format2($apply_money)."】元".$note_msg;
                $this->sys_log('企业资金充值',$note);
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
        }
    }
    /*企业账户提醒额度*/
    public function set_quota_remind(){
        $msg = '系统错误';
        $status = 'error';
        $operate=trim(I('get.operate'));
        if($operate=='ask'){
            $da['enterprise_id']=trim(I("enterprise_id"));
            $da['account_id']=trim(I("account_id"));
            $da['new_quota_remind']=trim(I("new_quota_remind"));
            if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $da['new_quota_remind']) && $da['new_quota_remind']>0){
                $this->ajaxReturn(array('msg'=>'提醒额度请输入数字！','status'=>$status));
            }
            if($da['new_quota_remind']<0){
                $this->ajaxReturn(array('msg'=>'提醒额度不能小于等于0！','status'=>$status));
            }
            $msg="确定是否给企业【".obj_data($da['enterprise_id'],2,'name')."】设置提醒额度".$da['new_quota_remind']."元？";
            $this->ajaxReturn(array('msg'=>$msg,"status"=>'success',"info"=>$da));
        }else if($operate=='run'){
            $account_id=trim(I("account_id"));
            $new_quota_remind=trim(I('new_quota_remind'));
            $enterprise_id=trim(I('enterprise_id'));
            $model=M('enterprise_account');
            /* if($cache_credit==""){
                 $this->ajaxReturn(array('msg'=>'请输入缓存额度！','status'=>$status));
             }*/
            if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $new_quota_remind) && $new_quota_remind>0){
                $this->ajaxReturn(array('msg'=>'提醒额度请输入数字！','status'=>$status));
            }
            if($new_quota_remind<0){
                $this->ajaxReturn(array('msg'=>'提醒额度不能小于等于0！','status'=>$status));
            }
            $model->startTrans();
            $map['account_id']=$account_id;
            $map['enterprise_id']=$enterprise_id;
            $account_info=$model->lock(true)->where($map)->find();
            if(empty($account_info)){
                $model->rollback();
                $this->ajaxReturn(array('msg'=>'对不起，没有找到相关数据，请重试！','status'=>$status));
            }
            $edit['account_id']=$account_id;
            $edit['new_quota_remind']=$new_quota_remind==''?null:$new_quota_remind;
            $res=$model->save($edit);
            if($res || $res==0){
                $msg='企业账户设置提醒额度成功！';
                $status='success';
                $n_msg='成功';
                $model->commit();
            }else{
                $msg='企业账户设置提醒额度失败！';
                $n_msg='失败';
                $model->rollback();
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$account_id.'】，企业账户设置提醒额度，企业【'.obj_name($enterprise_id,2).'】，提醒额度【'.money_format2($edit['new_quota_remind']).'】元'.$n_msg;
            $this->sys_log('企业账户设置提醒额度',$note);
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }else{
            $map['e.enterprise_id']=I("get.enterprise_id");
            $list=M("Enterprise e")
                ->join("t_flow_enterprise_account as ea on ea.enterprise_id=e.enterprise_id")
                ->where($map)
                ->field("e.*,ea.account_id,ea.account_balance,ea.freeze_money,ea.new_quota_remind")
                ->find();
            if($list){
                $this->assign("list",$list);
                $this->display();
            }else{
                $this->ajaxReturn(array('msg'=>'对不起，没有找到相关数据，请重试！','status'=>$status));
            }

        }
    }
    public function export_excel(){
         $user_type=D('SysUser')->self_user_type();//获取自身的用户类型是运营平台，代理商，企业
         $enterprise_name = trim(I('get.enterprise_name')); //企业名称
         $start_datetime = trim(I('get.start_datetime'));   //开始时间
         $end_datetime = trim(I('get.end_datetime'));   //结束时间
         $enterprise_code=trim(I('get.enterprise_code'));
        $top_proxy_code = trim(I('get.top_proxy_code'));
        $top_proxy_name = trim(I('get.top_proxy_name'));
         if($enterprise_code){
             $where['e.enterprise_code']=array('like','%'.$enterprise_code.'%');
         }
         if($enterprise_name){
             $where['e.enterprise_name']=array('like','%'.$enterprise_name.'%');
         }
        if($top_proxy_code){
            $where['up.proxy_code']=array('like','%'.$top_proxy_code.'%');
        }
        if($top_proxy_name){
            $where['up.proxy_name']=array('like','%'.$top_proxy_name.'%');
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
        }
        $where['e.approve_status']=1;
        $data=D("EnterpriseAccount")->enterprise_account_excel($where);
        if($user_type==1){
            $headArr=array("企业编号","企业名称","上级代理编号","上级代理名称","账户余额(元)","冻结金额(元)");//,"提醒额度(元)"
        }else{
            $headArr=array("企业编号","企业名称","账户余额(元)","冻结金额(元)");
        }
        ExportEexcel("企业账户管理",$headArr,$data);
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
}
?>