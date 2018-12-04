<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class ProxyRecController extends CommonController{
     /*代理商充值明细表*/
     public function index(){
         set_time_limit(0);
         D("SysUser")->sessionwriteclose();
        //获取自身的用户类型是运营平台，代理商，企业
        $user=D('SysUser')->self_user_type();
        $proxy_code = trim(I('get.proxy_code'));   //代理商编号
        $proxy_name = trim(I('get.proxy_name'));   //代理商名称
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        $approve_status=trim(I('get.approve_status'));   //审核状态
        $apply_code=trim(I('get.apply_code'));//申请编号
        $source=trim(I('get.source'));//支付方式
        $where=array();
        if($source>0){
            $where['ap.source']=$source;
        }
        if(!empty($proxy_code))$where['p.proxy_code'] = array('like','%'.$proxy_code.'%');
        if(!empty($proxy_name))$where['p.proxy_name'] = array('like','%'.$proxy_name.'%');
        if($apply_code){
            $where['ap.apply_code']=array('like','%'.$apply_code.'%');
        }
         if($approve_status!="" && $approve_status!=9){
             $where['ap.approve_status'] = $approve_status;
         }else{
             $where['ap.approve_status'] = array('neq',1);
         }
       /* if($approve_status!=""){
            if($approve_status!=9){
                $where['ap.approve_status'] = $approve_status;
            }else{
                $where['ap.approve_status']=array('neq',1);
            }
        }else{
            $where['ap.approve_status']=array('neq',1);
        }*/
       /* if($start_datetime && $end_datetime){
            $where['ap.create_date'] = array('between',array(start_time($start_datetime),end_time($end_datetime)));
        }else{
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $where['ap.create_date'] = array('between',array($start_datetime,$end_datetime));
        }*/
         if($start_datetime or $end_datetime){
             if($start_datetime && $end_datetime){
                 $where['ap.create_date']=array('between',array(start_time($start_datetime),end_time($end_datetime)));
             }elseif($start_datetime){
                 $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                 $where['ap.create_date']= array('between',array(start_time($start_datetime),$end_datetime));
             }elseif($end_datetime){
                 $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                 $where['ap.create_date']=array('between',array($start_datetime,end_time($end_datetime)));
             }
         }else{
             $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
             $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
             $where['ap.create_date']= array('between',array($start_datetime,$end_datetime));
         }
        $ids=D("Proxy")->proxy_child_ids();//获取该用户可操作的企业号
        if($ids){
            $where['p.proxy_id']=array("in",$ids);
        }else{
            $where['p.proxy_id']=0;//表示没有
        }
         $apply_type = trim(I('get.apply_type'));
         if(!empty($apply_type)){
             $where['ap.apply_type'] = $apply_type;
         }
        $where['ap.top_proxy_id']=D('SysUser')->self_proxy_id();
       // $where['ap.approve_status']=array('gt',1);
        $list=D('ProxyAccount')->proxyRechargeList($where);
         $this->assign('sum_results',$list['sum_results']);
        $this->assign('usr_type',$user);
        $this->assign('list',$list['list']);
        $this->assign('page',$list['page']);
         $this->assign('d_sdata',date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))));  //默认开始时间
         $this->assign('d_edata',date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))));  //默认结束时间
        $this->assign('source_name',get_source_name());
        $this->display(); 
     }

    public function export_excel(){
        //获取自身的用户类型是运营平台，代理商，企业
        $user=D('SysUser')->self_user_type();
        $proxy_code = trim(I('get.proxy_code'));   //代理商编号
        $proxy_name = trim(I('get.proxy_name'));   //代理商名称
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        $approve_status=trim(I('get.approve_status'));   //审核状态
        $apply_code=trim(I('get.apply_code'));//申请编号
        $source=trim(I('get.source'));//支付方式
        $where=array();
        if($source>0){
            $where['ap.source']=$source;
        }
        if(!empty($proxy_code))$where['p.proxy_code'] = $proxy_code;
        if(!empty($proxy_name))$where['p.proxy_name'] = array('like','%'.$proxy_name.'%');
        if($apply_code){
            $where['ap.apply_code']=array('like','%'.$apply_code.'%');
        }
        if($approve_status!="" && $approve_status!=9){
            $where['ap.approve_status'] = $approve_status;
        }else{
            $where['ap.approve_status'] = array('neq',1);
        }
        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['ap.create_date']=array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['ap.create_date']= array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['ap.create_date']=array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $where['ap.create_date']= array('between',array($start_datetime,$end_datetime));
        }
        $ids=D("Proxy")->proxy_child_ids();//获取该用户可操作的企业号
        if($ids){
            $where['p.proxy_id']=array("in",$ids);
        }else{
            $where['p.proxy_id']=0;//表示没有
        }
        $apply_type = trim(I('get.apply_type'));
        if(!empty($apply_type)){
            $where['ap.apply_type'] = $apply_type;
        }
        $where['ap.top_proxy_id']=D('SysUser')->self_proxy_id();
        $where['ap.approve_status']=array('gt',1);
        $title='代理商充值管理';
        $headArr=array("代理商编号","代理商名称","申请编号","付款金额(元)","付款方式","充值类型","付款日期","复审人","审核状态","申请人","申请时间");
        $proxy_recharge_list=D('ProxyAccount')->proxy_export_excel($where);
        $list=array();

        foreach($proxy_recharge_list as $k=>$v){
            $list[$k]['proxy_code'] =$v['proxy_code'];
            $list[$k]['proxy_name'] =$v['proxy_name'];
            $list[$k]['apply_code'] =$v['apply_code'];
            $list[$k]['apply_money'] =$v['apply_money'];
            if($v['source']==0){
                $list[$k]['source']="";
            }elseif($v['proxy_type']==1){
                $list[$k]['source'] =get_source_name($v['source']);
            }else{
                if($v['source']==1){
                    $list[$k]['source']="汇款";
                }elseif($v['source']==2){
                    $list[$k]['source']="微信支付";
                }else{
                    $list[$k]['source']="支付宝支付";
                }
            }
            
            if($v['apply_type'] == 1){
                $list[$k]['apply_type'] = '正常充值';
            }elseif($v['apply_type'] == 2){
                $list[$k]['apply_type'] = '测试款';
            }else{
                $list[$k]['apply_type'] = '--';
            }
            $list[$k]['transaction_date'] =$v['transaction_date'];
            $list[$k]['approve_man']=get_approve_people($v['apply_id'],1);
            $list[$k]['approve_status'] =get_apply_status($v['approve_status']);
            $list[$k]['create_user_id'] =get_user_name($v['create_user_id']);
            $list[$k]['create_date'] =$v['create_date'];
       }
        ExportEexcel($title,$headArr,$list);
    }

    /*弹出审核界面*/
    public function show(){
        $msg = '系统错误！';
        $status = 'error';
        if(I('download')){
            $where['apply_id'] = array('eq',trim(I('get.apply_id')));
            $enterprise = M('proxy_recharge_apply')->where($where)->find();
            parent::download('.'.$enterprise['credential_one']);
        }else{
            $operate=trim(I('operate'));
            $list=D('ProxyAccount')->detailed();
            //读取审核过程
            if($list){
                $process = M("proxy_recharge_process")->where(array('apply_id'=>$list['apply_id']))->select();
                if(!$process){
                    $process = "";
                }
            }
            $approve_f=I('approve_f');
            $this->assign($list);
            $this->assign("process",$process);
            if($operate=='approve'){
                 if($approve_f=='proxy_approve_c'){
                     if(in_array($list['approve_status'],array(3,4,5,6))){
                         $this->ajaxReturn(array('msg'=>'不可重复审核','status'=>$status));
                     }
                     $this->assign("type","1");
                 }
                if($approve_f=='proxy_approve'){
                    if($list['approve_status']<2){
                        $this->ajaxReturn(array('msg'=>'请等待初审完成！','status'=>$status));
                    }
                    if($list['approve_status']==4){
                        $this->ajaxReturn(array('msg'=>'初审驳回,不可复审！','status'=>$status));
                    }
                    if(in_array($list['approve_status'],array(5,6))){
                        $this->ajaxReturn(array('msg'=>'不可重复审核！','status'=>$status));
                    }
                }

                $this->display('approve');
            }else{
                $this->display('detailed');
            }
        }
    }
    /*审核的方法*/
    //初审
    public function proxy_approve_c(){
        $msg = '系统错误';
        $status = 'error';
        $apply_id=trim(I('apply_id'));
        $approve_status=trim(I('approve_status'));
        $approve_remark=trim(I('approve_remark'));
        
        $model=M('proxy_recharge_apply');
        $model->startTrans();
        if($approve_status==2 && $approve_remark==""){
            $this->ajaxReturn(array('msg'=>'请填写审核驳回原因','status'=>$status));
        }
        //读取申请信息
        $apply = $model->where(array('apply_id'=>$apply_id))->find();
        if(in_array($apply['approve_status'],array(3,4,5,6))){
            $this->ajaxReturn(array('msg'=>'不可重复审核','status'=>$status));
        }
        //修改申请表信息
        $edit['apply_id'] = $apply_id;
        $edit['approve_status'] = $approve_status==2?"4":"3";
        $edit['last_approve_date'] = date("Y-m-d H:i:s",time());
        $apply_res=$model->where(array('apply_id'=>$apply_id))->save($edit);
        //添加审核信息
        $add['apply_id'] = $apply_id;
        $add['approve_status'] = $approve_status;
        $add['approve_remark'] = $approve_remark;
        $add['approve_date']=date('Y-m-d H:i:s',time());
        $add['approve_user_id']=D('SysUser')->self_id();
        $add['approve_stage']=1;
        $process = M("proxy_recharge_process")->add($add);
        $info=M('proxy_recharge_apply as pr')
            ->join('left join t_flow_proxy  as p on pr.proxy_id=p.proxy_id ')
            ->join('left join t_flow_sys_user  as u on p.proxy_id=u.proxy_id ')
            ->where('apply_id='.$apply_id)
            ->field('pr.apply_code,p.sale_id,p.proxy_name,p.create_user_id,u.user_id')
            ->find();
        if($apply_res && $process){
            $model->commit();
            $msg = $approve_status==2?'充值申请单初审驳回成功！':'充值申请单初审成功！';
            $status = 'success';
            $r_msg=$approve_status==2?'初审驳回':'初审成功';
            $remind_content='代理商充值申请单【'.$info['apply_code'].'】已经【'.$r_msg.'】，请知晓！';
            $use=array($info['sale_id'],$info['user_id']);
            R('ObjectRemind/send_user',array(7,$remind_content,$use));
            if($approve_status==1){
                $remind_content='代理商【'.$info['proxy_name'].'】充值申请单已初审通过，请进行复审！';
                R('ObjectRemind/send_user',array(6,$remind_content));
            }
            $n_msg=$approve_status==2?'驳回成功':'成功';
        }else{
            $model->rollback();
            $msg = $approve_status==2?'充值申请单初审驳回失败！':'充值申请单初审失败！';
            $n_msg=$approve_status==2?'驳回失败':'失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，审核代理商充值申请单【".$info['apply_code']."】初审".$n_msg;
        $this->sys_log('代理商充值申请单初审',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }
    //复审
    public function  proxy_approve(){
        $msg = '系统错误';
        $status = 'error';
        $apply_id=trim(I('apply_id'));
        $approve_status=trim(I('approve_status'));
        $approve_remark=trim(I('approve_remark'));
        if($approve_status=="1"){
            if(I("get.tran")){
                //记录数据
                $da['apply_id']=trim(I('apply_id'));
                $da['apply_type']=trim(I('apply_type'));
                $da['approve_status']=trim(I('approve_status'));
                $da['approve_remark']=trim(I('approve_remark'));
                //sql条件
                $ta['ra.apply_id']=trim(I('apply_id'));
                $result=M('proxy_recharge_apply as ra')
                ->join('t_flow_proxy as p on p.proxy_id=ra.proxy_id','left')
                ->field('ra.apply_money,p.proxy_name')
                ->where($ta)
                ->find();
                //$result=M('proxy_recharge_apply as ra')->add();
                //$msg="确定是否向【".$result['proxy_name']."】充值".$result['apply_money']."元？";
                 $title='';
                if(trim(I('apply_type'))==2){
                    $title='测试款';
                }
                $msg="确定是否复审通过并向【".$result['proxy_name']."】充值".$title.$result['apply_money']."元？";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>'success','info'=>$da));
            }
        }


        $model=M('proxy_recharge_apply');
        $model->startTrans();
        //读取申请信息
        $apply = $model->where(array('apply_id'=>$apply_id))->find();
        if($apply['approve_status']==2){
            $this->ajaxReturn(array('msg'=>'请等待初审完成','status'=>$status));
        }
        if($apply['approve_status']==4){
            $this->ajaxReturn(array('msg'=>'初审驳回,不可复审','status'=>$status));
        }
        if($approve_status==2 && $approve_remark==""){
            $this->ajaxReturn(array('msg'=>'请填写审核驳回原因','status'=>$status));
        }
        if(in_array($apply['approve_status'],array(5,6))){
            $this->ajaxReturn(array('msg'=>'不可重复审核','status'=>$status));
        }
        $edit['apply_id'] = $apply_id;
        $edit['approve_status'] = $approve_status==2?"6":"5";
        $edit['last_approve_date'] = date("Y-m-d H:i:s",time());
        $apply_res=$model->where(array('apply_id'=>$apply_id))->save($edit);
        //添加审核信息
        $add['apply_id'] = $apply_id;
        $add['approve_status'] = $approve_status;
        $add['approve_remark'] = $approve_remark;
        $add['approve_date']=date('Y-m-d H:i:s',time());
        $add['approve_user_id']=D('SysUser')->self_id();
        $add['approve_stage']=2;
        $process = M("proxy_recharge_process")->add($add);
        $info=M('proxy_recharge_apply as pr')
            ->join('left join t_flow_proxy  as p on pr.proxy_id=p.proxy_id ')
            ->join('left join t_flow_sys_user  as u on p.proxy_id=u.proxy_id ')
            ->where('apply_id='.$apply_id)
            ->field('pr.apply_code,p.sale_id,p.proxy_name,p.create_user_id,u.user_id')
            ->find();
        if($apply_res && $process){
            if($approve_status==2){
                $model->commit();
                $msg = '复审驳回成功！';
                $status = 'success';
                $r_msg='复审驳回';
                $n_msg='复审驳回';
            }else{
                //读取上级代理商金额
                $Balance = M("ProxyAccount")->lock(true)->where(array('proxy_id'=>$apply['top_proxy_id']))->find();
                if($Balance['account_balance']<$apply['apply_money']){
                    $model->rollback();
                    $msg="对不起，您的账户余额不足，请充值后，再操作！";
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                }
                /*记录账户流水 上级代理商为支出 */
                $map['proxy_id']=$apply['proxy_id'];
                $account=D('ProxyAccount')->account($map);  //读取代理商账户
                $condition['top_account_id']=$Balance['account_id']; //上级代理商账户id
                $condition['top_account_balance']=$Balance['account_balance']; //上级代理商账户余额
                $condition['top_operate_type']=4; //划拨-上级代理商
                $condition['top_balance_type']=2;//支出-上级代理商
                $condition['top_user_type']=1;  //操作者类型
                $condition['apply_money']=$apply['apply_money'];   //需要操作的金额
                $condition['operate_proxy_id']=$account['proxy_id'];//收入-下级代理商
                $condition['operate_account_balance']=$account['account_balance'];//要操作的代理商账户余额
                $condition['operate_account_id']=$account['account_id']; //要操作的代理商账户ID
                if(trim(I('apply_type'))==2){
                    $condition['operate_operate_type']=8; //测试款-下级代理商
                }else{
                    $condition['operate_operate_type']=2; //充值-下级代理商
                }
                $condition['operate_balance_type']=1;//收入-下级代理商
                $condition['operate_user_type']=1;  //操作用户类型
                $res=D('ProxyAccount')->account_record($condition);

                if($res){
                    $model->commit();
                    $msg = '充值申请单复审成功！';
                    $status = 'success';
                    $r_msg='复审成功';
                    $n_msg='成功';
                    $this->send_recharge(2,$account['proxy_id'],$apply['apply_money'],$account['account_balance']+$apply['apply_money']);
                    $success_msg="，并充值【".$apply['apply_money']."】元";
                }else{
                    $model->rollback();
                    $msg ='充值申请单复审失败！';
                    $n_msg='失败';
                    $r_msg='复审失败';
                }
            }
        }else{
            $model->rollback();
            $msgs = $approve_status==2?'充值申请单复审驳回失败！':'充值申请单复审失败！';
            $msg =$msgs;
            $n_msg=$approve_status==2?'复审驳回失败':'复审失败';
            $r_msg=$approve_status==2?'驳回失败':'复审失败';
        }
        $remind_content='代理商充值申请单【'.$info['apply_code'].'】已经【'.$r_msg.'】，请知晓！';
        $use=array($info['sale_id'],$info['user_id']);
        R('ObjectRemind/send_user',array(7,$remind_content,$use));

        $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，审核代理商充值申请单【".$info['apply_code']."】".$n_msg.$success_msg;
        $this->sys_log('代理商充值申请单复审',$note);
    /*    $note='用户【'.get_user_name(D('SysUser')->self_id())."】,向【".$result['proxy_name']."】账户充值".$title.$result['apply_money']."元";
        $this->sys_log('代理商充值申请打款',$note);*/
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

}
?>