<?php
/**
 *
 * 企业提现审核表
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class EnterpriseWithdrawalsController extends CommonController{
    /**
     *企业提现管理
     */
    public function index(){
        D("SysUser")->sessionwriteclose();
        $user_type=D('SysUser')->self_user_type();//获取自身的用户类型是运营平台，代理商，企业
	    $enterprise_name = trim(I('get.enterprise_name')); //企业名称
        $enterprise_code = trim(I('get.enterprise_code')); //企业名称
        $apply_code=trim(I('get.apply_code')); //申请编号
	    $start_datetime = trim(I('get.start_datetime'));   //开始时间
	    $end_datetime = trim(I('get.end_datetime'));   //结束时间
	    $approve_status=trim(I('get.approve_status'));   //审核状态
        $is_play_money=trim(I('get.is_play_money'));//是否打款
	    if($enterprise_name){
	         $where['p.enterprise_name']=array('like','%'.$enterprise_name.'%');
	    }
        if($enterprise_code){
            $where['p.enterprise_code']=array('like','%'.$enterprise_code.'%');
        }
       if($apply_code){
          $where['ap.apply_code']=array('like','%'.$apply_code.'%');
       }
        if($is_play_money!="" && $is_play_money!=9){
            if($is_play_money==0){
                $where['ap.is_play_money'] = array(0,array("exp","is null"),"or");
            }else{
                $where['ap.is_play_money'] = $is_play_money;
            }
        }
       if($approve_status!=""){
           if($approve_status!=9){
               $where['ap.approve_status'] = $approve_status;
           }else{
               $where['ap.approve_status']=array('neq',1);
           }
       }else{
            $where['ap.approve_status']=array('neq',1);
       }
	    /*if($start_datetime && $end_datetime){
	         $where['ap.create_date'] = array('between',array(start_time($start_datetime),end_time($end_datetime)));
	    }*/
        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['ap.create_date']=array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }else if($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['ap.create_date']= array('between',array(start_time($start_datetime),$end_datetime));
            }else if($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['ap.create_date']=array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $where['ap.create_date'] = array('between',array($start_datetime,$end_datetime));
        }
        if($user_type==2){
            $ids=D("Enterprise")->enterprise_ids();//获取当前代理商下可操作企业
            if($ids){
                $where['p.enterprise_id']=array("in",$ids);
            }else{
                $where['p.enterprise_id']=-1;
            }
        }
        $where['p.status']=1;
        $count=M("enterprise_withdraw_apply as ap")
           ->join('left join t_flow_enterprise as p on p.enterprise_id=ap.enterprise_id')
           ->where($where)
           ->count();
        $Page       = new Page($count,20);
        $show     = $Page->show();

        $proxyw_list =M("enterprise_withdraw_apply as ap")
         ->join('left join t_flow_enterprise as p on p.enterprise_id = ap.enterprise_id')
         ->field('ap.*,p.enterprise_name,p.enterprise_code')
         ->where($where)
         ->order('ap.approve_status asc,ap.create_date desc')
         ->limit($Page->firstRow.','.$Page->listRows)
         ->select();
        $sum_results=0;
        if($user_type!=3){
                $sum_results =M("enterprise_withdraw_apply as ap")
                    ->join('left join t_flow_enterprise as p on p.enterprise_id = ap.enterprise_id')
                    ->where($where)
                    ->field('sum(ap.apply_money) as sum_money_one' )
                    ->find();
        }
        $this->assign('sum_results',$sum_results);
        $this->assign('enterprise_list',get_sort_no($proxyw_list,$Page->firstRow));  //数据列表
        $this->assign('d_sdata',date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))));  //默认开始时间
        $this->assign('d_edata',date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))));  //默认结束时间
        $this->assign('page',$show);//分页
        $this->assign("user_type",$user_type);
        $this->display('index');
    }

    /*弹出1审界面*/
    public function  approve_c(){
        $msg = '系统错误！';
        $status = 'error';
        if(I('download')){
            $where['apply_id'] = array('eq',trim(I('get.apply_id')));
            $enterprise = M('enterprise_withdraw_apply')->where($where)->find();
            parent::download('.'.$enterprise['credential']);
        }else{
            $operate=trim(I('operate'));
            $where['apply_code']=trim(I('apply_code'));
            $list=D('EnterpriseAccount')->withdraw_detailed($where);
            if($list['approve_status']!=2){
                $this->ajaxReturn(array('msg'=>"请勿重复审核！",'status'=>$status));
            }
            $this->assign("list",$list);
            $this->assign("type","1");
            $this->display('approve');
        }
    }
    //弹出2审界面
    public function approve_t(){
        $msg = '系统错误！';
        $status = 'error';
        if(I('download')){
            $where['apply_id'] = array('eq',trim(I('get.apply_id')));
            $enterprise = M('enterprise_withdraw_apply')->where($where)->find();
            parent::download('.'.$enterprise['credential']);
        }else{
            $operate=trim(I('operate'));
            $where['apply_code']=trim(I('apply_code'));
            $list=D('EnterpriseAccount')->withdraw_detailed($where);
            if($list['approve_status']==2){
                $this->ajaxReturn(array('msg'=>"请初审通过后在进行复审！",'status'=>$status));
            }
            if($list["approve_status"]>4){
                $this->ajaxReturn(array('msg'=>"请勿重复审核！",'status'=>$status));
            }
            $this->assign("list",$list);
            $this->assign("type","2");
            $this->display('approve');
        }
    }

    //弹出3审界面
    public function approve_h(){
        $msg = '系统错误！';
        $status = 'error';
        if(I('download')){
            $where['apply_id'] = array('eq',trim(I('get.apply_id')));
            $enterprise = M('enterprise_withdraw_apply')->where($where)->find();
            parent::download('.'.$enterprise['credential']);
        }else{
            $operate=trim(I('operate'));
            $where['apply_code']=trim(I('apply_code'));
            $list=D('EnterpriseAccount')->withdraw_detailed($where);
            if($list["approve_status"]<5 || $list['approve_status']==6){
              $this->ajaxReturn(array('msg'=>"请等待前面审核都通过后在进行打款操作！",'status'=>$status));
            }
            if($list['approve_status']>6){
                $this->ajaxReturn(array('msg'=>"请勿重复操作！",'status'=>$status));
            }
            $this->assign("list",$list);
            $this->display('approve');
        }
    }

    /*审核的方法*/
      public function  enterprise_approve(){
          $msg = '系统错误';
          $status = 'error';
          $where['apply_id']=trim(I('apply_id'));
          $model=M('enterprise_withdraw_apply');
          $model->startTrans();
          $approve_status=trim(I('approve_status'));
          $type=trim(I("type"));

          if($approve_status==1){
            //审核过程表状态
            $erp['approve_status']=1;
            //初审通过
            if($type==1){
              //审核过程表记录阶段
              $erp['approve_stage']=1;

              //审核信息表状态
              $data['approve_status']=3;
            //复审通过
            }elseif($type==2){
              //审核过程表记录阶段
              $erp['approve_stage']=2;

              //复审信息表状态
              $data['approve_status']=5;
            // 打款通过
            }else{
                $list= M('enterprise_withdraw_apply ')->where($where)->find();
              //审核过程表记录阶段
              $erp['approve_stage']=3;
                if($list["is_play_money"]==1 || $list['is_play_money']==2){
                    $this->ajaxReturn(array('msg'=>"请勿重复操作！",'status'=>$status));
                }
              if($list["approve_status"]<5 || $list['approve_status']==6){
                $this->ajaxReturn(array('msg'=>"请等待前面审核都通过后在进行打款操作！",'status'=>$status));
              }
              if($list['approve_status']>6){
                $this->ajaxReturn(array('msg'=>"请勿重复操作！",'status'=>$status));
              }
              if(trim(I('transaction_number'))==""){
                $this->ajaxReturn(array('msg'=>'请输入付款交易号','status'=>$status));
              }
              if(trim(I('payment_account'))==""){
                $this->ajaxReturn(array('msg'=>'请输入付款账号','status'=>$status));
              }
              if(trim(I('payment_money'))==""){
                $this->ajaxReturn(array('msg'=>'请输入付款金额','status'=>$status));
              }
              $data['transaction_number']=trim(I("transaction_number"));
              $data['payment_account']=trim(I("payment_account"));
              $data['payment_money']=trim(I("payment_money"));
              $data['payment_bank']=trim(I("payment_bank"));
              $data['payment_date']=date('Y-m-d H:i:s',time());
              //审核信息表状态
              //$data['approve_status']=7;
                $data['is_play_money']=1;
              $apply=M("enterprise_withdraw_apply")->where($where)->field("apply_money")->find();
              if(I("get.tran")){
                $da['apply_id']=trim(I('apply_id'));
                $da['approve_status']=trim(I('approve_status'));
                $da['transaction_number']=trim(I("transaction_number"));
                $da['payment_account']=trim(I("payment_account"));
                $da['payment_money']=trim(I("payment_money"));
                $da['payment_bank']=trim(I("payment_bank"));
                $da['enterprise_id']=trim(I('enterprise_id'));
                $enterprise=M("enterprise")->where(array('enterprise_id'=>$da['enterprise_id']))->field("enterprise_name")->find();       
                $msg="确定是否向【".$enterprise['enterprise_name']."】提现".$apply['apply_money']."元？";
                $msg="企业【".$enterprise['enterprise_name']."】申请提现".$apply['apply_money']."元，确定已经向该企业打款".$da['payment_money']."元吗？";
                 $this->ajaxReturn(array('msg'=>$msg,'status'=>'success','info'=>$da));
              }
              $map['enterprise_id']=trim(I('enterprise_id'));
              $account=M("enterprise_account")->where($map)->field("account_balance,account_id,freeze_money")->find();
              //以下是记录账单流水的
              $where['proxy_id']=D('SysUser')->self_proxy_id();
              $Balance=M('proxy_account')->where($where)->find();
              $condition['top_account_id']=$Balance['account_id']; //上级代理上账户id
              $condition['top_account_balance']=$Balance['account_balance']; //上级代理上账户余额
              $condition['top_operate_type']=5; //收回-上级代理商
              $condition['top_balance_type']=1;//收入-上级代理商
              $condition['top_user_type']=1;
              $condition['apply_money']=$apply['apply_money'];   //需要操作的金额
              $condition['operate_account_id']=$account['account_id'];//收入-下级企业
              $condition['freeze_money']=$account['freeze_money'];//冻结余额
              $condition['operate_account_balance']=$account['account_balance'];//要操作的企业账户余额
              $condition['operate_enterprise_id']=trim(I('enterprise_id')); //要操作的企业账户ID
              $condition['operate_operate_type']=3; //充值-下级企业
              $condition['operate_balance_type']=2;//支出-下级企业
              $condition['operate_user_type']=2;
              $success_msg="，并已经打款【".$apply['apply_money']."】元，打款交易号【".$data['transaction_number']."】";
              if(!(D('EnterpriseAccount')->account_back($condition)>0)){
                $msg ='审核失败！';
                $model->rollback();
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
              }
            }
          }else{
              if(trim(I('approve_remark'))==""){
                  $this->ajaxReturn(array('msg'=>'请填写审核驳回原因','status'=>$status));
              }
              //审核过程表状态
              $erp['approve_status']=2;

              if($type==1){
                //审核过程表记录阶段
                $erp['approve_stage']=1;

                //审核信息表状态
                $data['approve_status']=4;
              }elseif($type==2){
                //审核过程表记录阶段
                $erp['approve_stage']=2;

                //审核信息表状态
                $data['approve_status']=6;
              }else{
                //审核过程表记录阶段
                $erp['approve_stage']=3;
                
                //审核信息表状态
                //$data['approve_status']=8;
                  $data['is_play_money']=2;
              }
              //冻结余额转回用户余额
              $map['enterprise_id']=trim(I('enterprise_id'));
              $account=M("enterprise_account")->where($map)->field("account_balance,account_id,freeze_money")->find();
              $apply=M("enterprise_withdraw_apply")->where($where)->field("apply_money")->find();
              $acc['account_balance']=$account['account_balance']+$apply['apply_money'];
              $acc['freeze_money']=$account['freeze_money']-$apply['apply_money'];
              $acc['account_id']=$account['account_id'];
              $record['operater_before_balance']   = $account['account_balance'];  //操作前金额
              $record['operater_after_balance']    = $account['account_balance']+$apply['apply_money']; //操作后金额
              $record['operater_price']            = $apply['apply_money'];  //返还金额
              $record['operate_type']              = 5; //返还
              $record['balance_type']              = 1;//收入
              $record['record_date']               = date('Y-m-d H:i:s',time());
              $record['user_id']                   = D('SysUser')->self_id();
              $record['operation_date']            = date('Y-m-d H:i:s',time());
              $record['user_type']                 = 2;
              $record['enterprise_id']              = $map['enterprise_id'];
              $record['obj_user_type']             = 1;
              $record['obj_proxy_id']             = D('SysUser')->self_proxy_id();
              $record['device_name']               = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));
              $rr=M('account_record')->add($record);
              $re=M("enterprise_account")->save($acc);
              if(!($re>0&&$rr>0)){
                  $msg ='审核失败！';
                  $model->rollback();
                  $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
              }
          }
          //审核过程记录表数据
          $erp['apply_id']=trim(I('apply_id'));
          $erp['approve_user_id']=D('SysUser')->self_id();
          $erp['approve_date']=date('Y-m-d H:i:s',time());
          $erp['approve_remark']=trim(I("approve_remark"));
          $res=M("enterprise_withdraw_process")->add($erp);

          //修改审核记录表
          $data['apply_id']=trim(I('apply_id'));
          $data['last_approve_date']=date('Y-m-d H:i:s',time());

          $apply_res=$model->save($data);
          $title="";
          if($approve_status==1){
              if($apply_res>0 && $res>0){
                  $model->commit();
                  if($data['approve_status']==3){
                      $msg = '提现申请单初审成功！';
                      $n_msg="提现申请单初审成功";
                      $title="代理商提现申请单初审";
                  }else if($data['approve_status']==5 &&$erp['approve_stage']==2){
                      $msg = '提现申请单复审成功！';
                      $n_msg="提现申请单复审成功";
                      $title="代理商提现申请单复审";
                  }else if($data['is_play_money']==1 && $erp['approve_stage']==3){
                      $msg="提现申请单打款成功！";
                      $n_msg="提现申请单打款成功";
                      $title="代理商提现申请单打款";
                      $success_msg="，并已经打款【".$apply['apply_money']."】元，打款交易号【".$data['transaction_number']."】";
                  }
                  $status = 'success';
              }else{
                  if($data['approve_status']==3){
                      $msg = '提现申请单初审失败！';
                      $n_msg="提现申请单初审失败";
                      $title="代理商提现申请单初审";
                  }else if($data['approve_status']==5 &&$erp['approve_stage']==2){
                      $msg = '提现申请单复审失败！';
                      $n_msg="提现申请单复审失败";
                      $title="代理商提现申请单复审";
                  }else if($data['is_play_money']==1 && $erp['approve_stage']==3){
                      $msg="提现申请单打款失败！";
                      $n_msg="提现申请单打款失败";
                      $title="代理商提现申请单打款";
                  }
                  $model->rollback();
              }
          }else{
              if($apply_res>0 && $res>0){
                  $model->commit();
                  if($data['approve_status']==4){
                      $msg = '提现申请单初审驳回成功！';
                      $title="代理商提现申请单初审";
                      $n_msg="提现申请单初审驳回成功";
                  }else if($data['approve_status']==6 &&$erp['approve_stage']==2){
                      $msg = '提现申请单复审驳回成功！';
                      $title="代理商提现申请单复审";
                      $n_msg="提现申请单复审驳回成功";
                  }else if($data['is_play_money']==2 &&$erp['approve_stage']==3){
                      $msg="提现申请单打款驳回成功！";
                      $title="代理商提现申请单打款";
                      $n_msg="提现申请单打款驳回成功";
                  }
                  $status = 'success';
              }else{
                  if($data['approve_status']==4){
                      $msg = '提现申请单初审驳回失败！';
                      $n_msg="提现申请单初审驳回失败";
                      $title="代理商提现申请单初审";
                  }else if($data['approve_status']==6 &&$erp['approve_stage']==2){
                      $msg = '提现申请单复审驳回失败！';
                      $n_msg="提现申请单初审驳回失败";
                      $title="代理商提现申请单复审";
                  }else if($data['is_play_money']==2 &&$erp['approve_stage']==3){
                      $msg="提现申请单打款驳回失败！";
                      $n_msg="提现申请单打款驳回失败";
                      $title="代理商提现申请单打款";
                  }
                  $model->rollback();
              }
          }
          $ewa=M("enterprise_withdraw_apply")->where(array("apply_id"=>$where['apply_id']))->find();
          $info=M("enterprise")->where(array("enterprise_id"=>$ewa['enterprise_id']))->find();
          $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$where['apply_id']."】，企业【".$info['enterprise_name']."(".$info['enterprise_code'].")】的提现申请单【".$ewa['apply_code']."】".$n_msg.$success_msg;
          $this->sys_log($title,$note);
          $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));

    }

    public function show(){
      $apply_id = I('get.apply_id');
      $where['pa.apply_id'] = $apply_id;
      $proxyw= M('enterprise_withdraw_apply as pa')->join("t_flow_enterprise as p on p.enterprise_id=pa.enterprise_id","left")->where($where)->field("pa.*,p.enterprise_name,p.enterprise_code")->find();
      if($proxyw){
            $process = M("enterprise_withdraw_process")->where(array('apply_id'=>$apply_id))->select();
            if(!$process){
                $process = "";
            }
        $this->assign("proxyw",$proxyw);
        $this->assign("process",$process);
        $this->display('detailed');
      }else{
        $this->ajaxReturn(array('msg'=>"订单不存在"));
      }
    }

    public function export_excel(){
        $user_type=D('SysUser')->self_user_type();//获取自身的用户类型是运营平台，代理商，企业
        $enterprise_name = trim(I('get.enterprise_name')); //企业名称
        $enterprise_code = trim(I('get.enterprise_code')); //企业编号
        $apply_code=trim(I('get.apply_code')); //申请编号
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        $approve_status=trim(I('get.approve_status'));   //审核状态
        $is_play_money=trim(I('get.is_play_money'));//是否打款
        if($enterprise_name){
            $where['p.enterprise_name']=array('like','%'.$enterprise_name.'%');
        }
        if($enterprise_code){
            $where['p.enterprise_code']=array('like','%'.$enterprise_code.'%');
        }
        if($apply_code){
            $where['ap.apply_code']=array('like','%'.$apply_code.'%');
        }
        if($is_play_money!="" && $is_play_money!=9){
            if($is_play_money==0){
                $where['ap.is_play_money'] = array(0,array("exp","is null"),"or");
            }else{
                $where['ap.is_play_money'] = $is_play_money;
            }
        }
        if($approve_status!=""){
            if($approve_status!=9){
                $where['ap.approve_status'] = $approve_status;
            }else{
                $where['ap.approve_status']=array('neq',1);
            }
        }else{
            $where['ap.approve_status']=array('neq',1);
        }
        /*if($start_datetime && $end_datetime){
             $where['ap.create_date'] = array('between',array(start_time($start_datetime),end_time($end_datetime)));
        }*/
        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['ap.create_date']=array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }else if($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['ap.create_date']= array('between',array(start_time($start_datetime),$end_datetime));
            }else if($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['ap.create_date']=array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $where['ap.create_date'] = array('between',array($start_datetime,$end_datetime));
        }
        if($user_type==2){
            $ids=D("Enterprise")->enterprise_ids();//获取当前代理商下可操作企业
            if($ids){
                $where['p.enterprise_id']=array("in",$ids);
            }else{
                $where['p.enterprise_id']=-1;
            }
        }
        $where['p.status']=1;
        $list =M("enterprise_withdraw_apply as ap")
            ->join('left join t_flow_enterprise as p on p.enterprise_id = ap.enterprise_id')
            ->field('ap.*,p.enterprise_name,p.enterprise_code')
            ->where($where)
            ->limit(3000)
            ->order('ap.approve_status asc,ap.create_date desc')
            ->select();
        $data=array();
        foreach ($list as $v) {
          $rech=array();
          $rech['enterprise_code']=$v['enterprise_code'];
          $rech['enterprise_name']=$v['enterprise_name'];
          $rech['apply_code']=$v['apply_code'];
          $rech['apply_money']=$v['apply_money'];
          $rech['create_date'] =$v['create_date'];
          $rech['approve_status'] =get_apply_status($v['approve_status']);
          if($v['is_play_money']==1){
              $rech['is_play_money']="已打款";
          }elseif($v['is_play_money']==2){
              $rech['is_play_money']="打款驳回";
          }else{
              $rech['is_play_money']="未打款";
          }
          $rech['last_approve_date']=$v['last_approve_date'];
          array_push($data,$rech);
        }
      $headArr=array("企业编号","企业名称","申请编号","提现金额(元)","申请时间","审核状态","是否打款","审核时间");
      ExportEexcel("企业提现管理",$headArr,$data);
    }
}