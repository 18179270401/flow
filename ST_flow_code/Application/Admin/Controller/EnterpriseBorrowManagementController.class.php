<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
class EnterpriseBorrowManagementController extends CommonController {
    /*企业借款申请*/
    public $os_enterprise_ids;
    public $os_proxy_ids;

    public function start(){
        $this->os_enterprise_ids = D('Enterprise')->enterprise_child_ids();
        $this->os_proxy_ids = D('Proxy')->proxy_child_ids();
    }


    /**
     *  企业借款申请列表
     */
    public function index(){
        D("SysUser")->sessionwriteclose();
        $model = M('enterprise_loan as el');
        $loan_code=trim(I('loan_code'));
        $enterprise_code = trim(I('get.enterprise_code'));
        $enterprise_name = trim(I('get.enterprise_name'));
        $approve_status = trim(I('get.approve_status'));
        $top_proxy_id = trim(I('get.top_proxy_id'));
        $start_datetime =trim(I('start_datetime')) ;
        $end_datetime = trim(I('end_datetime'));
        if($loan_code){
            $map['el.loan_code'] = array('like','%'.$loan_code.'%');
        }
        if($enterprise_code){
            $map['enterprise.enterprise_code'] = array('like','%'.$enterprise_code.'%');
        }
        if($enterprise_name){
            $map['enterprise.enterprise_name'] = array('like','%'.$enterprise_name.'%');
        }

        $map['enterprise.status'] = array('neq',2);
        if($approve_status!=""){
            $where['el.approve_status'] = $approve_status;
        }else{
            $where['el.approve_status']=array('neq',1);
        }

        if($start_datetime && $end_datetime){
            $map['el.loan_date'] = array('between',array(start_time($start_datetime),end_time($end_datetime)));
        }elseif($start_datetime){
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            $map['el.loan_date'] = array('between',array(start_time($start_datetime),$end_datetime));
        }elseif($end_datetime){
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
            $map['el.loan_date'] = array('between',array($start_datetime,end_time($end_datetime)));
        }
        $where['el.enterprise_id'] = array(array('in',$this->os_enterprise_ids),array('in',D('Enterprise')->enterprise_ids2()),'or') ;
        $map['_complex'] = $where;

        if($top_proxy_id && (in_array($top_proxy_id,explode(',',$this->os_proxy_ids)) or ($top_proxy_id == D('SysUser')->self_proxy_id()))){
            $ids = M('')->query("select getProxyChildList('$top_proxy_id') as ids");
            $map['enterprise.top_proxy_id'] = array('in',$ids[0]['ids']);
        }

        if(D('SysUser')->is_top_proxy_admin() == false){
            $map['enterprise.top_proxy_id'] = D('SysUser')->self_proxy_id();
        }

        $join=array(
            't_flow_enterprise as enterprise on enterprise.enterprise_id=el.enterprise_id',
            't_flow_sys_user as user on user.user_id = enterprise.sale_id and user.status = 1'
        );
        $count = $model
            ->join($join,'left')
            ->where($map)
            ->count();

        $Page       = new Page($count,20);
        $show       = $Page->show();

        $list = $model
            ->field('el.loan_id,el.loan_code,el.loan_money,el.loan_date,el.approve_status,el.create_date,el.modify_date,el.is_pay_off,el.last_approve_date,el.repayment_date,enterprise.enterprise_id,enterprise.enterprise_code,enterprise.enterprise_name,user.user_name')
            ->join($join,'left')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('el.approve_status asc,el.create_date desc')
            ->where($map)
            ->select();
        $this->assign('is_top_proxy_admin',D('SysUser')->is_top_proxy_admin());
        $this->assign('page',$show);
        $this->assign('user_id',D('SysUser')->self_id());
        $this->assign('list', get_sort_no($list, $Page->firstRow));
        $this->display();
    }


    /*初核界面*/
    public function  approve(){
        $msg = '对不起，没有找到相关内容，请重试！';
        $status = 'error';
        $operate=trim(I('get.operate'));
        if($operate=='approve'){
            /*初审开始*/
            $loan_id=trim(I('loan_id'));
            $type=trim(I('type'));
            $approve_status=trim(I('approve_status'));
            $approve_remark=trim(I('approve_remark'));
            if($type==1){
                $model=M('enterprise_loan');
                $model->startTrans();
                if($approve_status==2 && $approve_remark==""){
                    $model->rollback();
                    $this->ajaxReturn(array('msg'=>'请填写审核驳回原因','status'=>$status));
                }
                //读取申请信息
                $contract_info = $model->where(array('loan_id'=>$loan_id))->find();
                if(in_array($contract_info['approve_status'],array(3,4))){
                    $model->rollback();
                    $this->ajaxReturn(array('msg'=>'不可重复审核','status'=>$status));
                }
                //修改申请表信息
                $edit['loan_id'] = $loan_id;
                $edit['approve_status'] = $approve_status==2?"4":"3";
                $edit['last_approve_date'] = date("Y-m-d H:i:s",time());
                $apply_res=$model->where(array('loan_id'=>$loan_id))->save($edit);

                //添加审核信息
                $add['loan_id'] = $loan_id;
                $add['approve_status'] = $approve_status;
                $add['approve_remark'] = $approve_remark;
                $add['approve_date']=date('Y-m-d H:i:s',time());
                $add['approve_user_id']=D('SysUser')->self_id();
                $add['approve_stage']=1;
                $process = M("enterprise_loan_process")->add($add);

                if($apply_res && $process){
                    $model->commit();
                    $msg = $approve_status==2?'借款申请单初审驳回成功！':'借款申请单初审成功！';
                    $status = 'success';
                }else{
                    $model->rollback();
                    $msg = $approve_status==2?'借款申请单初审驳回失败！':'借款申请单初审失败！';
                }
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
        }else{
            $where['el.loan_id']=trim(I('loan_id'));
            $list=$this->detailed($where);
            if($list['approve_status']==3 || $list['approve_status']==4){
                $this->ajaxReturn(array('msg'=>"请勿重复审核！",'status'=>$status));
            }
            $this->assign("list",$list);
            $this->assign("type","1");
            $this->display('approve');
        }
    }
    /*复审界面*/
    public function  approve_t(){
        $msg = '对不起，没有找到相关内容，请重试！';
        $status = 'error';
        $operate=trim(I('get.operate'));
        $tran=trim(I('get.tran'));
    if($operate=='approve'){
              $loan_id=trim(I('loan_id'));
              $approve_status=trim(I('approve_status'));
              $approve_remark=trim(I('approve_remark'));
        if($approve_status==1) {
            if ($tran) {
                //记录数据
                $da['loan_id'] = $loan_id;
                $da['approve_status'] = $approve_status;
                $da['approve_remark'] = $approve_remark;
                //sql条件
                $ta['el.loan_id'] = $loan_id;
                $result = M('enterprise_loan as el')
                    ->join('t_flow_enterprise as e on e.enterprise_id=el.enterprise_id', 'left')
                    ->field('el.loan_money,e.enterprise_name')
                    ->where($ta)
                    ->find();
                $msg = "确定是否复审通过并向【" . $result['enterprise_name'] . "】借款" . $result['loan_money'] . "元？";
                $this->ajaxReturn(array('msg' => $msg, 'status' => 'success', 'info' => $da));
            }
        }
          /*复审开始*/

            $model=M('enterprise_loan');
            $model->startTrans();
            if($approve_status==2 && $approve_remark==""){
                $this->ajaxReturn(array('msg'=>'请填写审核驳回原因','status'=>$status));
            }
            //读取申请信息
            $contract_info = $model->where(array('loan_id'=>$loan_id))->find();
            if($contract_info['approve_status']==2){
                $this->ajaxReturn(array('msg'=>'请等待初审完成','status'=>$status));
            }
            if($contract_info['approve_status']==4){
                $this->ajaxReturn(array('msg'=>'初审驳回,不可复审','status'=>$status));
            }
            if(in_array($contract_info['approve_status'],array(5,6))){
                $this->ajaxReturn(array('msg'=>'不可重复审核','status'=>$status));
            }
            //修改申请表信息
            $edit['loan_id'] = $loan_id;
            $edit['approve_status'] = $approve_status==2?"6":"5";
            $edit['last_approve_date'] = date("Y-m-d H:i:s",time());
            $loan_res=$model->where(array('loan_id'=>$loan_id))->save($edit);

            //添加审核信息
            $add['loan_id'] = $loan_id;
            $add['approve_status'] = $approve_status;
            $add['approve_remark'] = $approve_remark;
            $add['approve_date']=date('Y-m-d H:i:s',time());
            $add['approve_user_id']=D('SysUser')->self_id();
            $add['approve_stage']=2;
            $process = M("enterprise_loan_process")->add($add);

        if($loan_res && $process){
            if($approve_status==2){
                $model->commit();
                $msg = $approve_status==2?'借款申请单复审驳回成功！':'借款申请单复审成功！';
                $status = 'success';
            }else{
                //读取上级代理商金额
                $loan = M("enterprise_loan")->where(array('loan_id'=>$loan_id))->find();
                $Ba['e.enterprise_id']=$loan['enterprise_id'];
                $Balance=$this->p_account($Ba); /*上级代理商账户 */
                $map['enterprise_id']=$loan['enterprise_id'];
                $account=$this->e_account($map);   /*企业账户 */
                if($Balance['account_balance']<$loan['loan_money']){
                    $msg="对不起，您的账户余额不足，请充值后，再操作！";
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                }
                $condition['top_account_id']=$Balance['account_id']; //上级代理商账户id
                $condition['top_account_balance']=$Balance['account_balance']; //上级代理商账户余额
                $condition['top_proxy_id']=$Balance['proxy_id']; //上级代理商ID
                $condition['top_operate_type']=4; //划拨-上级代理商
                $condition['top_balance_type']=2;//支出-上级代理商
                $condition['top_user_type']=1;  //操作者类型
                $condition['operate_money']=$loan['loan_money'];    //需要操作的金额
                $condition['operate_account_id']=$account['account_id']; //收入-企业
                $condition['operate_account_balance']=$account['account_balance'];//要操作的代理商账户余额
                $condition['operate_enterprise_id']=$account['enterprise_id']; //要操作的代理商账户ID
                $condition['operate_operate_type']=2; //充值-下级代理商
                $condition['operate_balance_type']=1;//收入-企业
                $condition['operate_user_type']=2;  //操作用户类型
                $condition['remark']='借款';
                $res=$this->account_record($condition);
                if($res){
                    $model->commit();
                    $msg = '借款申请单复审成功！';
                    $status = 'success';
                }else{
                    $model->rollback();
                    $msg ='借款申请单复审失败！';
                }
            }
        }else{
            $model->rollback();
            $msgs = $approve_status==2?'借款申请单复审驳回失败！':'借款申请单复审失败！';
            $msg =$msgs;
        }
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));

        }else{
            $where['el.loan_id']=trim(I('loan_id'));
            $list=$this->detailed($where);
            if($list['approve_status']==2 || $list['approve_status']==4){
                $this->ajaxReturn(array('msg'=>"请初审通过后在进行复审！",'status'=>$status));
            }
            if($list['approve_status']==5 || $list['approve_status']==6){
                $this->ajaxReturn(array('msg'=>"请勿重复审核！",'status'=>$status));
            }
            $this->assign("list",$list);
            $this->display('approve');
        }
    }

    /*流水记录*/
    public function account_record($condition){
        $model=M('enterprise_account');
        /*企业账户余额加上操作金额*/
        $map['account_id']= $condition['operate_account_id'];
        $money['account_balance']=$condition['operate_account_balance']+$condition['operate_money'];
        $money['modify_user_id']=D('SysUser')->self_id();
        $money['modify_date']=date('Y-m-d H:i:s',time());
        /*一级代理商减去账户余额*/
        $self_account['account_balance']= $condition['top_account_balance']-$condition['operate_money'];
        if($self_account['account_balance']<0){
            return 0;
        }
        $self_account['modify_user_id']=D('SysUser')->self_id();
        $self_account['modify_date']=date('Y-m-d H:i:s',time());
        $record[0]['operater_before_balance']= $condition['top_account_balance'];  //操作前金额
        $record[0]['operater_after_balance']=$self_account['account_balance']; //操作后金额
        $record[0]['operater_price']=$condition['operate_money'];  //划拨金额
        $record[0]['operate_type']=$condition['top_operate_type']; //划拨
        $record[0]['balance_type']= $condition['top_balance_type'];//支出
        $record[0]['record_date']=date('Y-m-d H:i:s',time());
        $record[0]['user_id']=D('SysUser')->self_id();;
        $record[0]['operation_date']=date('Y-m-d H:i:s',time());
        $record[0]['user_type']=$condition['top_user_type'];
        $record[0]['remark']=$condition['remark'];
        if($condition['top_proxy_id']){
            $record[0]['proxy_id']=$condition['top_proxy_id'];
        }else{
            $record[0]['proxy_id']=D("SysUser")->self_proxy_id();
        }
        $record[0]['enterprise_id']=null;
        $record[0]['obj_user_type']=$condition['operate_user_type'];
        $record[0]['obj_proxy_id']=null;
        $record[0]['obj_enterprise_id']= $condition['operate_enterprise_id'];
        $record[0]['device_name']       = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));

        /*企业为收入*/
        $record[1]['operater_before_balance']=$condition['operate_account_balance'];  //操作前金额
        $record[1]['operater_after_balance']= $money['account_balance']; //操作后金额
        $record[1]['operater_price']=$condition['operate_money'];  //划拨金额
        $record[1]['operate_type']=$condition['operate_operate_type']; //充值
        $record[1]['balance_type']=$condition['operate_balance_type'];//收入
        $record[1]['record_date']=date('Y-m-d H:i:s',time());
        $record[1]['user_id']=D('SysUser')->self_id();
        $record[1]['operation_date']=date('Y-m-d H:i:s',time());
        $record[1]['user_type']=$condition['operate_user_type'];
        $record[1]['remark']=$condition['remark'];
        $record[1]['proxy_id']=null;
        $record[1]['enterprise_id']= $condition['operate_enterprise_id'];
        $record[1]['obj_user_type']=$condition['top_user_type'];
        if($condition['top_proxy_id']){
            $record[1]['obj_proxy_id']=$condition['top_proxy_id'];
        }else{
            $record[1]['obj_proxy_id']=D('SysUser')->self_proxy_id();
        }
        $record[1]['obj_enterprise_id']=null;
        $record[1]['device_name']       = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));
        $where['account_id']=$condition['top_account_id'];
        $res=$model->where($map)->save($money);  //修改企业账户余额  加钱
        $self_res='';
        $self_res=M('proxy_account')->where($where)->save($self_account);  //修改上级代理商账余额  减去
        $recordResult=M('account_record')->addAll($record);
        if(D('SysUser')->self_user_type()==1) {
            if($res>0 && $recordResult>0){
                return 1;
            }else{
                return 0;
            }
        }else{
            if($res>0 && $self_res>0 && $recordResult>0){
                return 1;
            }else{
                return 0;
            }
        }

    }


    /*企业账户*/
    public function e_account($where){
     $res=M('enterprise_account')->where($where)->field('account_balance,account_id,enterprise_id')->find();
        return $res;
    }
    /*企业上级代理商账户账户*/
    public function p_account($where){
        $model=M('enterprise as e');
        $res=$model
            ->join('left join t_flow_proxy_account as p on  e.top_proxy_id=p.proxy_id')
            ->where($where)->field('p.account_balance,p.account_id,p.proxy_id')->find();
        return $res;
    }
    /*查看*/
    public function show(){
        $msg ="对不起，没有找到相关内容，请重试！";
        $status = 'error';
        $where['el.loan_id']=trim(I('loan_id'));
        $list=$this->detailed($where);
        if($list){
            $map['loan_id']=trim(I('loan_id'));
            $this->assign('process',$this->process($map));
            $this->assign('list',$list);
            $this->display();
        }else{
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }

    public function process($where){
        $list=M("enterprise_loan_process")->where($where)->select();
        return $list;
    }


    public  function detailed($where){
        $where['e.status']=1;
        $list =M('enterprise_loan as  el')
            ->join('left join t_flow_enterprise as e on e.enterprise_id = el.enterprise_id')
            ->field('el.*,e.enterprise_name,e.enterprise_code')
            ->where($where)
            ->find();
        return $list;

    }

    public function enterprise_chd(){
        $where['enterprise_id'] = array(array('in',$this->os_enterprise_ids),array('in',D('Enterprise')->enterprise_ids2()),'or') ;
        $where['approve_status']=1;
        $where['status']=1;
        $list=M('enterprise')->where($where)->field('enterprise_name,enterprise_id')->select();
        return $list;
    }
}


