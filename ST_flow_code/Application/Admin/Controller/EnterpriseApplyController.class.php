<?php
/**
 *
 * 企业提现申请
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class EnterpriseApplyController extends CommonController{
	public function index(){
        D("SysUser")->sessionwriteclose();
        $usr_type=D('SysUser')->self_user_type();
        $enterprise_name = trim(I('get.enterprise_name')); //企业名称
        //$source = trim(I('get.source')); //来源
        $apply_code = trim(I('get.apply_code'));  //申请编号
        $start_datetime = trim(I('start_datetime'));   //开始时间
        $end_datetime = trim(I('end_datetime'));   //结束时间
        $approve_status=trim(I('get.approve_status'));   //审核状态
        $beneficiary_name=trim(I('get.beneficiary_name'));
        $mobile=trim(I('get.mobile'));
        $card_number=trim(I('get.card_number'));
        $is_play_money=trim(I('get.is_play_money'));//是否打款
        if($enterprise_name){
            $where['p.enterprise_name']=array('like','%'.$enterprise_name.'%');
        }
        if($beneficiary_name){
            $where['ap.beneficiary_name']=array('like','%'.$beneficiary_name.'%');
        }
        if($mobile){
            $where['ap.mobile']=$mobile;
        }
        if($card_number){
            $where['ap.card_numbe']=$card_number;
        }
        if($is_play_money!="" && $is_play_money!=9){
            if($is_play_money==0){
                $where['ap.is_play_money'] = array(0,array("exp","is null"),"or");
            }else{
                $where['ap.is_play_money'] = $is_play_money;
            }
        }
        if($approve_status!=9 && $approve_status!=''){
            $where['ap.approve_status'] = $approve_status;
        }
        /*	    if($start_datetime && $end_datetime){
                     $where['ap.create_date'] =array('between',array(start_time($start_datetime),end_time($end_datetime)));
                }else{
                    $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
                    $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
                    $where['ap.create_date'] = array('between',array($start_datetime,$end_datetime));
                }*/
        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['ap.create_date'] =array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['ap.create_date'] = array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['ap.create_date'] =array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $where['ap.create_date'] = array('between',array($start_datetime,$end_datetime));
        }
        if($apply_code){
            $where['ap.apply_code'] = array('like','%'.$apply_code.'%');
        }
        $model=M("enterprise_withdraw_apply ap");
        $where['p.enterprise_id']=D('SysUser')->self_enterprise_id();
        $where['p.status']=1;
        $count=$model
           ->join('left join t_flow_enterprise as p on p.enterprise_id=ap.enterprise_id')
           ->where($where)
           ->count();
        $Page       = new Page($count,20);
        $show     = $Page->show();

        $proxyw_list =$model
         ->join('left join t_flow_enterprise as p on p.enterprise_id = ap.enterprise_id')
         ->field('ap.*,p.enterprise_name,p.enterprise_code')
         ->where($where)
         ->order('ap.approve_status asc,ap.create_date desc')
         ->limit($Page->firstRow.','.$Page->listRows)
         ->select();
        $this->assign('enterprise_list',get_sort_no($proxyw_list,$Page->firstRow));  //数据列表
        $this->assign('d_sdata',date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))));  //默认开始时间
        $this->assign('d_edata',date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))));  //默认结束时间
        $this->assign('page',$show);            //分页
        $this->assign('usr_type',$usr_type);
        $this->display('index');
    }

    //企业提现方法
    public function add (){
        $enterprise_id=D('SysUser')->self_enterprise_id();
        $info=D("Enterprise")->list_enterprise_set($enterprise_id);
        $this->assign("info",$info);
        $this->display();
    }

    /*企业提现功能*/
    public function insert(){
        $msg = '系统错误!';
        $status = 'error';
        $data = array();
        $money=M('enterprise_account')->where('enterprise_id='.D('SysUser')->self_enterprise_id())->find();
        //申请单号
        $model=M('enterprise_withdraw_apply');
        if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', I('apply_money'))){
            $this->ajaxReturn(array('msg'=>'金额输入有误！','status'=>$status));
        }
        if(I('apply_money')==""){
            $msg = '请输入提现金额！';
        }else if($money['account_balance']<I('apply_money')){
            $msg = '对不起，您的账户余额不足，请充值后，再操作！';
        }else if(I('beneficiary_name')==''){
            $msg = '请输入开户人姓名！';
        }else if(I('mobile')==''){
            $msg = '请输入开户人电话！';
        }else if(I('card_number')==''){
            $msg = '请输入银行卡号！';
        }else if(I('bank_account')==''){
            $msg = '请输入开户银行！';
        }else if(I('account_opening')==''){
            $msg = '请输入开户省市！';
        }else{
            $map['enterprise_id']=D('SysUser')->self_enterprise_id();
            $number=M('enterprise')->where($map)->field('enterprise_code,top_proxy_id')->find();
            $up_proxy_id=D('SysUser')->up_proxy_info();
            $data['beneficiary_name']=trim(I('beneficiary_name'));
            $data['mobile']=trim(I('mobile'));
            $data['card_number']=trim(I('card_number'));
            $data['bank_account']=trim(I('bank_account'));
            $data['account_opening']=trim(I('account_opening'));

            //获取编号的个数
            $apply_code="TXSQD".date('Ymd',time());
            $app['apply_code']=array('like',$apply_code.'%');
            $applys=M('enterprise_withdraw_apply')->where($app)->order("apply_code desc")->find();
            $applys=substr($applys['apply_code'],13);
            $applys=$applys+1;
            $data['apply_code']=generate_order($applys,2);
            //$data['top_proxy_id']= $up_proxy_id['proxy_id'];
            $data['top_proxy_id']=$number['top_proxy_id'];
            $data['enterprise_id']=D('SysUser')->self_enterprise_id();
            $data['create_user_id']=D('SysUser')->self_id();
            $data['apply_money']=trim(I('apply_money'));
            $data['remark']=trim(I('remark'));
            $data['approve_status']=1;
            $data['create_date']=date('Y-m-d H:i:s',time());
            $id= M('enterprise_withdraw_apply')->add($data);
	        //执行添加
	        if($id){
                $set_add['enterprise_id'] = D('SysUser')->self_enterprise_id();
                $set_add['bank_account'] = trim(I('bank_account'));
                $set_add['account_opening'] = trim(I('account_opening'));
                $set_add['card_number'] = trim(I('card_number'));
                $set_add['beneficiary_name'] = trim(I('beneficiary_name'));
                $set_add['mobile'] = trim(I('mobile'));
                D("Enterprise")->get_enterprise_set($set_add);
	        	//$model->commit();
	            $msg = '新增提现申请单成功';
                $n_msg='成功';
	            $status = 'success';
	        }else{
	            $msg = '新增提现申请单失败!';
                $n_msg='失败';
	            //$model->rollback();
	        }
        }
        $number=M("Enterprise")->where(array("enterprise_id"=>D('SysUser')->self_enterprise_id()))->find();
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】,ID【".$id."】,企业【".$number['enterprise_name']."(".$number['enterprise_code'].")】，申请编号【".$data['apply_code']."】，提现金额【".money_format2($data['apply_money'])."】元".$n_msg;
        $this->sys_log('新增提现申请',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'info'=>$id));

    }



    public function edit(){
        $msg = '系统错误!';
        $status = 'error';
        $data = array();
        $model=M('enterprise_withdraw_apply');
        $model->startTrans();
        $operate=trim(I('get.operate'));
        $operates=trim(I('get.operates'));
        $enterprise_id=D('SysUser')->self_enterprise_id();
        $map['enterprise_id']=$enterprise_id;

        if($operates=='update'){
            $is_apply=D('EnterpriseAccount')->all_apply();

            if($operate=='send'){
                $apply_id=trim(I('id'));
                $apply_code=$model->where('apply_id='.$apply_id)->field('apply_code')->find();
                if(!in_array2($apply_id,$is_apply)){
                    $this->ajaxReturn(array('msg'=>'查询失败','status'=>$status));
                }
                $condition['apply_id']=trim(I('post.id'));
                $apply=$model->where($condition)->find();
                $apply_money=$apply['apply_money'];
                /*提现申请送审送审*/
                $result=M("enterprise_account")->where($map)->field("account_id,account_balance,freeze_money")->find();
                if($result['account_balance']-trim(I('apply_money'))<0){
                    $msg = "对不起，您的账户余额不足，请充值后，再操作！";
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                }
                $da['account_id']=$result['account_id'];
                $da['account_balance']=$result['account_balance']-$apply_money;
                $da['freeze_money']=$result['freeze_money']+$apply_money;
                $res=M('enterprise_account')->where($map)->save($da);
                $en=M("enterprise")->where($map)->find();
                $record['operater_before_balance']=$da['account_balance']+$apply_money;  //操作前金额
                $record['operater_after_balance']= $da['account_balance']; //操作后金额
                $record['operater_price']=$apply_money;  //划拨金额
                $record['operate_type']=3; //充值
                $record['balance_type']=2;//收入
                $record['record_date']=date('Y-m-d H:i:s',time());
                $record['user_id']=D('SysUser')->self_id();
                $record['operation_date']=date('Y-m-d H:i:s',time());
                $record['user_type']=2;
                $record['proxy_id']=null;
                $record['enterprise_id']= D('SysUser')->self_enterprise_id();
                $record['obj_user_type']=1;
                $record['obj_proxy_id']=$en['top_proxy_id'];
                $record['obj_enterprise_id']=null;
                $record['device_name']       = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));
                $rr=M('account_record')->add($record);
                if(!$res){
                    $msg = "系统错误";
                    $model->rollback();
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                }
                $data['approve_status']=2;
                $data['apply_id']=$apply_id;
                $res=$model->save($data);
                //执行添加
                if($res && $rr){
                    $model->commit();
                    $msg = '提现申请单提交审核成功!';
                    $n_msg = '成功';
                    $status = 'success';
                }else{
                    $msg = '提现申请单提交审核失败!';
                    $n_msg = '失败';
                    $model->rollback();
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id())."】,ID【".$apply_id."】,提现申请单【".$apply_code['apply_code']."】提交审核".$n_msg;
                $this->sys_log('提现申请单提交审核',$note);
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }else{
                $apply_id=trim(I('apply_id'));
                if(!in_array2($apply_id,$is_apply)){
                    $this->ajaxReturn(array('msg'=>'查询失败','status'=>$status));
                }
                $money=M('enterprise_account')->where('enterprise_id='.D('SysUser')->self_enterprise_id())->find();
                /*提现申请修改*/
                if(trim(I('apply_money'))==""){
                    $msg = '请输入提现金额！';
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                }else if($money['account_balance']<I('apply_money')){
                        $msg = '对不起，您的账户余额不足，请充值后，再操作！';
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
               }else if(I('beneficiary_name')==''){
                    $msg = '请输入收款人姓名！';
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                }else if(I('mobile')==''){
                    $msg = '请输入手机号！';
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                }else if(I('card_number')==''){
                    $msg = '请输入银行卡号！';
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                }else if(I('bank_account')==''){
                    $msg = '请输入开户行！';
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                }else if(I('account_opening')==''){
                    $msg = '请输入开户省市！';
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                }else{
                    $data['beneficiary_name']=trim(I('beneficiary_name'));
                    $data['mobile']=trim(I('mobile'));
                    $data['card_number']=trim(I('card_number'));
                    $data['bank_account']=trim(I('bank_account'));
                    $data['account_opening']=trim(I('account_opening'));
                    $data['apply_money']=trim(I('post.apply_money'));
                    $data['remark']=trim(I('post.remark'));
                    $data['modify_user_id']=D('SysUser')->self_id();
                    $data['modify_date']=date('Y-m-d H:i:s',time());
                    $data['approve_status']=1;
                    $data['is_play_money']=0;
                    $data['last_approve_date'] =null;
                    $data['apply_id']=$apply_id;
                }
                $info=$model->where(array("apply_id"=>$apply_id))->find();
                $c_item='';
                $c_item.=$data['bank_account']===$info['bank_account']?'':'开户行【'.$data['bank_account'].'】';
                $fg=!empty($c_item)?'，':'';
                $c_item.=$data['account_opening']===$info['account_opening']?'':$fg.'开户省市【'.$data['account_opening'].'】';
                $fg=!empty($c_item)?'，':'';
                $c_item.=$data['card_number']===$info['card_number']?'':$fg.'银行卡号【'.$data['card_number'].'】';
                $fg=!empty($c_item)?'，':'';
                $c_item.=$data['beneficiary_name']===$info['beneficiary_name']?'':$fg.'收款人姓名【'.$data['beneficiary_name'].'】';
                $fg=!empty($c_item)?'，':'';
                $c_item.=$data['mobile']===$info['mobile']?'':$fg.'手机号码【'.$data['mobile'].'】';
                $fg=!empty($c_item)?'，':'';
                $c_item.=$data['apply_money']*100===$info['apply_money']*100?'':$fg.'提现金额【'.$data['apply_money'].'】元';

                $res=$model->save($data);
                $apply_code=$model->where('apply_id='.$apply_id)->field('apply_code')->find();
                //执行添加
                if($res>0){
                    $model->commit();
                    $msg = '编辑提现申请单成功';
                    $note_msg='成功';
                    $status = 'success';
                }else{
                    $msg = '编辑提现申请单失败！';
                    $note_msg='失败';
                    $model->rollback();
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】,ID【'.$apply_id.'】,编辑提现申请单，企业【'.obj_name($enterprise_id,2).'】，申请编号【'.$apply_code['apply_code'].'】：'.$c_item.$note_msg;
                $this->sys_log('编辑提现申请单',$note);
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }

        }else{
            $con['ew.apply_id']=trim(I('apply_id'));
            $list=D('EnterpriseAccount')->withdraw_detailed($con);
            $this->assign($list);
            $this->display();
        }
    }

    /*提现申请删除*/
    public function delete(){
        $msg = '系统错误!';
        $status = 'error';
        $where['apply_id'] = I('id');
        $info=M("enterprise_withdraw_apply")->where($where)->find();
        if($info){
            $res = M('enterprise_withdraw_apply')->where($where)->delete();
        }
        if(empty($res)){
            $this->ajaxReturn(array('msg'=>'对不起，没有找到相关数据，请重试！','status'=>$status));
        }
        if(in_array($res['approve_status'],array(3,5,8))){
            $this->ajaxReturn(array('msg'=>'已经审核通过，请勿删除！','status'=>$status));
        }
        if($res){
            $msg = '删除提现申请单成功！';
            $n_msg='成功';
            $status = 'success';
        }else{
            $msg = '删除提现申请单失败！';
            $n_msg='失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】,ID【".$where['apply_id']."】,删除提现申请单【".$info['apply_code']."】".$n_msg;
        $this->sys_log('删除提现申请单',$note);
        $this->ajaxReturn(array('msg' => $msg, 'status' => $status));

    }
    /*查看提现详情*/
    public function show(){
        if(I('apply_id')){
            $where['ew.apply_id']=trim(I('apply_id'));
            $list=D('EnterpriseAccount')->withdraw_detailed($where); //基本信息
            $map['apply_id']=trim(I('apply_id'));
            $process=D('EnterpriseAccount')->withdraw_process($map);//提现状态
          if($list){
              $this->assign($list);
              $this->assign('process',$process);
              $this->display('detailed');
          }else{
            $this->error("对不起，需要审核的订单不存在");
          }
        }else{
        	$this->error("对不起，需要审核订单信息错误");
        }
    }
    public function export_excel(){
        $usr_type=D('SysUser')->self_user_type();
        $enterprise_name = trim(I('get.enterprise_name')); //企业名称
        //$source = trim(I('get.source')); //来源
        $apply_code = trim(I('get.apply_code'));  //申请编号
        $start_datetime = trim(I('start_datetime'));   //开始时间
        $end_datetime = trim(I('end_datetime'));   //结束时间
        $approve_status=trim(I('get.approve_status'));   //审核状态
        $beneficiary_name=trim(I('get.beneficiary_name'));
        $mobile=trim(I('get.mobile'));
        $is_play_money=trim(I('get.is_play_money'));//是否打款
        $card_number=trim(I('get.card_number'));
        if($enterprise_name){
            $where['p.enterprise_name']=array('like','%'.$enterprise_name.'%');
        }
        if($beneficiary_name){
            $where['ap.beneficiary_name']=array('like','%'.$beneficiary_name.'%');
        }
        if($mobile){
            $where['ap.mobile']=$mobile;
        }
        if($card_number){
            $where['ap.card_numbe']=$card_number;
        }
        if($is_play_money!="" && $is_play_money!=9){
            if($is_play_money==0){
                $where['ap.is_play_money'] = array(0,array("exp","is null"),"or");
            }else{
                $where['ap.is_play_money'] = $is_play_money;
            }
        }
        if($approve_status!=9 && $approve_status!=''){
            $where['ap.approve_status'] = $approve_status;
        }
        /*	    if($start_datetime && $end_datetime){
                     $where['ap.create_date'] =array('between',array(start_time($start_datetime),end_time($end_datetime)));
                }else{
                    $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
                    $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
                    $where['ap.create_date'] = array('between',array($start_datetime,$end_datetime));
                }*/
        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['ap.create_date'] =array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['ap.create_date'] = array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['ap.create_date'] =array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $where['ap.create_date'] = array('between',array($start_datetime,$end_datetime));
        }
        if($apply_code){
            $where['ap.apply_code'] = array('like','%'.$apply_code.'%');
        }
        $model=M("enterprise_withdraw_apply ap");
        $where['p.enterprise_id']=D('SysUser')->self_enterprise_id();
        $where['p.status']=1;
        $list =$model
            ->join('left join t_flow_enterprise as p on p.enterprise_id = ap.enterprise_id')
            ->field('ap.*,p.enterprise_name,p.enterprise_code')
            ->where($where)
            ->order('ap.approve_status asc,ap.create_date desc')
            ->limit(3000)
            ->select();
        $data=array();
        foreach ($list as $v) {
          $rech=array();
            $rech['apply_code'] =$v['apply_code'];
            $rech['apply_money'] =$v['apply_money'];
            $rech['bank_account'] =$v['bank_account'];
            $rech['beneficiary_name'] =$v['beneficiary_name'];
            $rech['mobile'] =$v['mobile'];
            $rech['create_user_id'] =get_user_name($v['create_user_id']);
            $rech['create_date'] =$v['create_date'];
            $rech['approve_status'] =get_apply_status($v['approve_status']);
            if($v['is_play_money']==1){
                $rech['is_play_money']="已打款";
            }elseif($v['is_play_money']==2){
                $rech['is_play_money']="打款驳回";
            }else{
                $rech['is_play_money']="未打款";
            }
            $rech['last_approve_date'] =$v['last_approve_date'];
            array_push($data,$rech);
        }
        $headArr=array("申请编号","提现金额(元)","开户行","收款人姓名","收款人电话","操作人","申请时间","审核状态","是否打款","审核时间");
        ExportEexcel("账户提现申请",$headArr,$data);
    }

}