<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
class EnterprisePayBackController extends CommonController {
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
        $user_type = D("SysUser")->self_user_type();
        $model = M('enterprise_repaymen  as re');
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
            if($approve_status!=''){
                $map['re.approve_status'] = $approve_status;
            }
        if($start_datetime && $end_datetime){
            $map['re.repayment_date'] = array('between',array(start_time($start_datetime),end_time($end_datetime)));
        }elseif($start_datetime){
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            $map['re.repayment_date'] = array('between',array(start_time($start_datetime),$end_datetime));
        }elseif($end_datetime){
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
            $map['re.repayment_date'] = array('between',array($start_datetime,end_time($end_datetime)));
        }

        if($user_type == 3){
            $map['enterprise.enterprise_id']=D("SysUser")->self_enterprise_id();
        }

        if($user_type != 3) {
            $where['re.enterprise_id'] = array(array('in',$this->os_enterprise_ids),array('in',D('Enterprise')->enterprise_ids2()),'or') ;
            $map['_complex'] = $where;
        }

        if($top_proxy_id && (in_array($top_proxy_id,explode(',',$this->os_proxy_ids)) or ($top_proxy_id == D('SysUser')->self_proxy_id()))){
            $ids = M('')->query("select getProxyChildList('$top_proxy_id') as ids");
            $map['enterprise.top_proxy_id'] = array('in',$ids[0]['ids']);
        }

        if($user_type != 3) {
            if(D('SysUser')->is_top_proxy_admin() == false){
                $map['enterprise.top_proxy_id'] = D('SysUser')->self_proxy_id();
            }
        }
        $join=array(
            't_flow_enterprise as enterprise on enterprise.enterprise_id=re.enterprise_id',
            't_flow_enterprise_loan as el on re.loan_id = el.loan_id ',
            't_flow_sys_user as user on user.user_id = enterprise.sale_id and user.status = 1',
        );
        $count = $model
            ->join($join,'left')
            ->where($map)
            ->count();

        $Page       = new Page($count,20);
        $show       = $Page->show();

        $list = $model
            ->field('re.*,el.loan_code,el.loan_money,el.loan_date,el.approve_status as el_approve_status ,el.is_pay_off,enterprise.enterprise_id,enterprise.enterprise_code,enterprise.enterprise_name,user.user_name')
            ->join($join,'left')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('re.modify_date desc,re.approve_status asc')
            ->where($map)
            ->select();
        $sum_results =$model
            ->join($join,'left')
            ->where($map)
            ->field('sum(el.loan_money) as sum_money_one,sum(re.repayment_money) as sum_money_tow' )
            ->find();
        $this->assign('sum_results',$sum_results);
        $this->assign('is_admin',D('SysUser')->is_top_proxy_admin());
        $this->assign('page',$show);
        $this->assign('user_id',D('SysUser')->self_id());
        $this->assign('usr_type',D('SysUser')->self_user_type());
    /*    $this->assign('d_sdata',date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))));  //默认开始时间
        $this->assign('d_edata',date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))));  //默认结束时间*/
        $this->assign('list', get_sort_no($list, $Page->firstRow));
        $this->display();
    }

    /**
     *  新增企业还款
     */
    public function add(){
         $where['el.loan_id']=trim(I('loan_id'));
         $list=$this->detailed($where);
         $this->assign($list);
         $this->display();        //模板
    }

/*企业还款方法*/
    public function insert(){
        $operate=trim(I('get.operate'));
        $msg = '系统错误!';
        $status = 'error';
        if($operate =='send'){
            $repaymen_id = I('id');
            $map['repaymen_id']=$repaymen_id;
            $loan = M("enterprise_repaymen")->where($map)->find();
            if($loan){
                $info=M('enterprise_loan')->where('loan_id='.$loan['loan_id'])->field('loan_code')->find();
                $last=last_money($loan['loan_id']);
                $last_money = intval($last * 1000);
                $repayment_money = intval($loan['repayment_money'] * 1000);
                if($repayment_money>$last_money){
                   $msg='您未还款的金额为'.$last.'元，请重新修改还款金额';
                }else{
                    $edit['repaymen_id'] = $loan['repaymen_id'];
                    $edit['approve_status'] = 2;
                    if(M("enterprise_repaymen")->save($edit)){
                        $msg = '还款申请单提交审核成功！';
                        $status = 'success';
                        $n_msg='提交审核成功';
                    }else{
                        $msg = '还款申请单提交审核失败！';
                        $n_msg='提交审核失败';
                    }
                    $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$repaymen_id."】，借款申请单【".$info['loan_code']."】的还款申请单".$n_msg;
                    $this->sys_log('还款申请单提交审核',$note);
                }

            }else{
                $msg ="对不起，没有找到相关内容，请重试！";
            }

            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }else {
            $data = array();
            $repayment_money = trim(I('repayment_money'));
            $enterprise_id = trim(I('enterprise_id'));
            $repayment_date = trim(I('repayment_date'));
            $loan_id = trim(I('loan_id'));
            $con['el.loan_id'] = $loan_id;
            $con['el.enterprise_id'] = $enterprise_id;
            $list = $this->detailed($con);
            $loan_money = intval($list['loan_money'] * 1000);
            $re_repayment_money = intval($list['repayment_money'] * 1000);
            $repayment_money = intval($repayment_money * 1000);
            $surplus=$loan_money-$re_repayment_money-$repayment_money;
            if ($surplus < 0) {
                $this->ajaxReturn(array('msg' => '对不起已经超出还款金额，请修改后，重新提交！', 'status' => $status));
            } else {
                if (empty($list)) {
                    $this->ajaxReturn(array('msg' => '对不起，没有找到相关内容，请重试！', 'status' => $status));
                }
                if (!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $repayment_money)) {
                    $this->ajaxReturn(array('msg' => '请输入正确的还款金额！', 'status' => $status));
                }
                if ($repayment_money == '') {
                    $this->ajaxReturn(array('msg' => '请输入的还款金额！', 'status' => $status));
                }
                if ($repayment_money <= 0) {
                    $this->ajaxReturn(array('msg' => '还款金额需大于零！', 'status' => $status));
                }
                if (empty($repayment_date)) {
                    $this->ajaxReturn(array('msg' => '请输入还款时间！', 'status' => $status));
                }
                if (I('source') == 0) {
                    $this->ajaxReturn(array('msg' => '请选择还款方式！', 'status' => $status));
                }
                if (trim(I("transaction_number")) == "") {
                    $this->ajaxReturn(array('msg' => '请输入打款户名/支付订单号/交易号！', 'status' => $status));
                }
                if ($_FILES['file']['name'] != null || $_FILES['file']['name'] != "") {
                    $fileinfo = $this->business_licence_upload(C('PROXY_UPLOAD_DIR'));
                    if ($fileinfo['file']) {
                        $icense_img = substr(C('UPLOAD_DIR') . $fileinfo['file']['savepath'] . $fileinfo['file']['savename'], 1, strlen(C('UPLOAD_DIR') . $fileinfo['file']['savepath'] . $fileinfo['file']['savename']) - 1);
                        $data['credential_one'] = $icense_img;
                    } else {
                        $msg = $this->business_licence_upload_Error['file'];
                        $this->ajaxReturn(array('msg' => $msg, 'status' => $status, 'data' => $data));
                    }
                }
                $model = M('enterprise_repaymen');
                $data['loan_id'] = $loan_id;  //借款ID
                $data['enterprise_id'] = $enterprise_id;  //企业ID
                $data['repayment_money'] = trim(I('repayment_money'));  //还款金额
                $data['source'] = trim(I('source'));  //还款方式
                $data['transaction_number'] = trim(I('transaction_number')); //交易号
                $data['approve_status'] = 1; //审核状态：草稿
                $data['create_user_id'] = D('SysUser')->self_id();
                $data['remark'] = trim(I('remark'));  //说明
                $data['repayment_date'] = trim(I('repayment_date'));//还款时间
                $data['create_date'] = date('Y-m-d H:i:s', time());  //创建时间
                $data['modify_date'] = date('Y-m-d H:i:s', time());  //修改时间
                $res = $model->add($data);
                //执行添加
                if ($res) {
                    //$model->commit();
                    $msg = '新增还款申请单成功';
                    $n_msg='成功';
                    $status = 'success';
                } else {
                    //$model->rollback();
                    $msg = '新增还款申请单失败！';
                    $n_msg='失败';
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$res."】，新增还款申请单".$n_msg."，企业【".obj_name($enterprise_id,2)."】，借款申请单【".$list['loan_code']."】，交易号【".trim(I('transaction_number'))."】，还款金额【".money_format2($data['repayment_money'])."】元";
                $this->sys_log('新增还款申请单',$note);
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status, 'info' => $res));
            }
        }
    }

    /*企业借款送审*/
    public function send_approve(){
        $msg ="系统错误!";
        $status = 'error';
        $repaymen_id = I('id');
        $map['repaymen_id']=$repaymen_id;
        $loan = M("enterprise_repaymen")->where($map)->find();
        if($loan){
            $info=M('enterprise_loan')->where('loan_id='.$loan['loan_id'])->field('loan_code')->find();
            $last=last_money($loan['loan_id']);
            $last_money = intval($last * 100);
            $repayment_money = intval($loan['repayment_money'] * 100);
            if($repayment_money>$last_money){
                $msg='您未还款的金额为'.$last.'元，请重新修改还款金额';
            }else{
                $edit['repaymen_id'] = $loan['repaymen_id'];
                $edit['approve_status'] = 2;
                if(M("enterprise_repaymen")->save($edit)){
                    $msg = '还款申请单提交成功！';
                    $n_msg='提交审核成功';
                    $status = 'success';
                }else{
                    $msg = '还款申请单提交失败！';
                    $n_msg='提交审核失败';
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$repaymen_id."】，借款申请单【".$info['loan_code']."】的还款申请单".$n_msg;
                $this->sys_log('还款申请单提交审核',$note);
            }
        }else{
            $msg ="对不起，没有找到相关内容，请重试！";
        }


        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }


       /*弹出编辑页面*/
        public function edit(){
        $msg ="对不起，没有找到相关内容，请重试！";
        $status = 'error';
        $where['re.repaymen_id']=trim(I('repaymen_id'));
        $list=$this->info($where);
        if($list){
            $this->assign($list);
            $this->display();
        }else{
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
       }

/*编辑方法*/
    public  function update(){
        $operate=trim(I('get.operate'));
        $msg = '系统错误!';
        $status = 'error';
        if($operate =='send'){
            $repaymen_id = I('id');
            $map['repaymen_id']=$repaymen_id;
            $loan = M("enterprise_repaymen")->where($map)->find();
            if($loan){
                $info=M('enterprise_loan')->where('loan_id='.$loan['loan_id'])->field('loan_code')->find();
                $last=last_money($loan['loan_id']);
                $last_money = intval($last * 1000);
                $repayment_money = intval($loan['repayment_money'] * 1000);
                if($repayment_money>$last_money){
                    $msg='您未还款的金额为'.$last.'元，请重新修改还款金额';
                }else{
                    $edit['repaymen_id'] = $loan['repaymen_id'];
                    $edit['approve_status'] = 2;
                    if(M("enterprise_repaymen")->save($edit)){
                        $msg = '还款申请单提交成功！';
                        $n_msg='提交审核成功';
                        $status = 'success';
                    }else{
                        $msg = '还款申请单提交失败！';
                        $n_msg='提交审核失败';
                    }
                    $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$repaymen_id."】，借款申请单【".$info['loan_code']."】的还款申请单编辑后".$n_msg;
                    $this->sys_log('还款申请单提交审核',$note);
                }

            }else{
                $msg ="对不起，没有找到相关内容，请重试！";
            }

            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }else{
            $data = array();
            $repayment_money=trim(I('repayment_money'));
            $repayment_date=trim(I('repayment_date'));
            $loan_id=trim(I('loan_id'));
            $repaymen_id=trim(I('repaymen_id'));
            $con['re.repaymen_id']=$repaymen_id;
            $con['re.loan_id']=$loan_id;
            $list=$this->info($con);
            $loan_money = intval($list['loan_money'] * 1000);
            $re_repayment_money = intval($list['el_repayment_money'] * 1000);
            $repayment_money = intval($repayment_money * 1000);
            $surplus=$loan_money-$re_repayment_money-$repayment_money;
            if($surplus<0){
                $this->ajaxReturn(array('msg'=>'对不起已经超出还款金额，请修改后，重新提交！','status'=>$status));
            }else{
            if(empty($list)){
                $this->ajaxReturn(array('msg'=>'对不起，没有找到相关内容，请重试！','status'=>$status));
            }
            if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $repayment_money)){
                $this->ajaxReturn(array('msg'=>'请输入正确的还款金额！','status'=>$status));
            }
            if($repayment_money==''){
                $this->ajaxReturn(array('msg'=>'请输入的还款金额！','status'=>$status));
            }
            if($repayment_money<=0){
                $this->ajaxReturn(array('msg'=>'还款金额需大于零！','status'=>$status));
            }
            if(empty($repayment_date)){
                $this->ajaxReturn(array('msg'=>'请输入还款时间！','status'=>$status));
            }
            if(I('source')==0 ){
                $this->ajaxReturn(array('msg'=>'请选择还款方式！','status'=>$status));
            }
            if(trim(I("transaction_number"))==""){
                $this->ajaxReturn(array('msg'=>'请输入打款户名/支付订单号/交易号！','status'=>$status));
            }
            if($_FILES['file']['name']!=null || $_FILES['file']['name']!=""){
                $fileinfo = $this->business_licence_upload(C('PROXY_UPLOAD_DIR'));
                if($fileinfo['file']){
                    $icense_img = substr(C('UPLOAD_DIR').$fileinfo['file']['savepath'].$fileinfo['file']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['file']['savepath'].$fileinfo['file']['savename'])-1);
                    $data['credential_one'] = $icense_img;
                }else{
                    $msg = $this->business_licence_upload_Error['file'];
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                }
            }
            $source=trim(I('source'));
            $transaction_number= trim(I('transaction_number'));
            $repayment_date=  trim(I('repayment_date'));
            $model=M('enterprise_repaymen');
            $map['repaymen_id']=$repaymen_id;  //企业ID
            $data['repayment_money']=trim(I('repayment_money'));  //还款金额
            $data['source']=$source;  //还款方式
            $data['transaction_number']=$transaction_number; //交易号
            $data['approve_status']=1; //审核状态：草稿
            $data['modify_user_id']=D('SysUser')->self_id();
            $data['remark']=trim(I('remark'));  //说明
            $data['repayment_date']=$repayment_date;//还款时间
            $data['create_date']=date('Y-m-d H:i:s',time());  //创建时间
            $data['modify_date']=date('Y-m-d H:i:s',time());  //修改时间
            $res=$model->where($map)->save($data);
            //执行添加
            if($res){
                //$model->commit();
                $msg = '编辑还款申请单成功';
                $n_msg='成功';
                $status = 'success';
            }else{
                //$model->rollback();
                $msg = '编辑还款申请单失败！';
                $n_msg='失败';
            }
                $c_item='';
                $c_item.=$source===$list['source']?'':'，还款方式【'.get_source_info($source).'】';
                $fg=!empty($c_item)?'，':'';
                $c_item.=$repayment_money===$list['repayment_money']*1000?'':$fg.'借款金额【'.trim(I('repayment_money')).'】元';
                $fg=!empty($c_item)?'，':'';
                $c_item.=$transaction_number===$list['transaction_number']?'':$fg.'交易号【'.$transaction_number.'】';

                $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$repaymen_id."】，编辑还款申请单，企业【".$list['enterprise_name']."(".$list['enterprise_code']."】，借款申请单编号【".$list['loan_code']."】，借款金额【".$list['loan_money']."】元".$c_item.$n_msg;
                $this->sys_log('编辑还款申请单',$note);
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'info'=>$res));
        }
        }
    }

    /*审核*/

    public function  approve(){
        $msg = '对不起，没有找到相关内容，请重试！';
        $status = 'error';
        $operate=trim(I('get.operate'));
        if($operate=='approve'){
            $repaymen_id=trim(I('repaymen_id'));
            $approve_status=trim(I('approve_status'));
            $remark=trim(I('remark'));
            $tran=trim(I('get.tran'));
            $conf['re.repaymen_id']=$repaymen_id;
            $contract_info=$this->info($conf);
            if($approve_status==1) {
                $loan_money = intval($contract_info['loan_money'] * 1000);
                $re_repayment_money = intval($contract_info['el_repayment_money'] * 1000);
                $repayment_money = intval($contract_info['repayment_money'] * 1000);
                $surplus=$loan_money-$re_repayment_money-$repayment_money;
                /*if($surplus==0){
                    $this->ajaxReturn(array('msg'=>'对不起该用户的借款金额已经还清，请勿再次审核！','status'=>$status));
                }*/
                if($surplus<0){
                    $this->ajaxReturn(array('msg'=>'对不起已经超出还款金额，请修改后，重新提交！','status'=>$status));
                }
                if ($tran) {
                    //记录数据
                    $da['repaymen_id'] = $repaymen_id;
                    $da['approve_status'] = $approve_status;
                    $da['remark'] = $remark;
                    //sql条件
                    $ta['re.repaymen_id'] = $repaymen_id;
                    $result = M('enterprise_repaymen as re')
                        ->join('t_flow_enterprise as e on e.enterprise_id=re.enterprise_id', 'left')
                        ->field('re.repayment_money,e.enterprise_name')
                        ->where($ta)
                        ->find();
                    $da['loan_id'] = $result['loan_id'];
                    $msg = "确定是否审核通过【" . $result['enterprise_name'] . "】还款" . $result['repayment_money'] . "元？";
                    $this->ajaxReturn(array('msg' => $msg, 'status' => 'success', 'info' => $da));
                }
            }
            /*复审开始*/

            $model=M('enterprise_repaymen');
            $model->startTrans();
            if($approve_status==2 && $remark==""){
                $this->ajaxReturn(array('msg'=>'请填写审核驳回原因','status'=>$status));
            }
            //读取申请信息
           // $contract_info = $model->where(array('repaymen_id'=>$repaymen_id))->find();
            if($contract_info['approve_status']==4){
                $this->ajaxReturn(array('msg'=>'审核驳回,不可重复审核','status'=>$status));
            }
            if($contract_info['approve_status']==3){
                $this->ajaxReturn(array('msg'=>'不可重复审核','status'=>$status));
            }
            //修改申请表信息
            $update['approve_status'] = $approve_status==2?"4":"3";
            $update['remark'] = $remark;
            $update['approve_user_id']=D('SysUser')->self_id();
            $update['approve_date'] = date("Y-m-d H:i:s",time());
            $repay_res=$model->where(array('repaymen_id'=>$repaymen_id))->save($update);

            if($repay_res){
                if($approve_status==2){
                    $model->commit();
                    $msg = $approve_status==2?'还款申请单审核驳回成功！':'还款申请单审核成功！';
                    $status = 'success';
                    $n_msg=$approve_status==2?'审核驳回成功':'审核成功';
                }else{
                    $con['el.enterprise_id']=$contract_info['enterprise_id'];
                    $con['el.loan_id']=$contract_info['loan_id'];
                    $list=$this->detailed_lock($con);
                    $edit['loan_id']=$contract_info['loan_id'];
                    $repayment_money=$contract_info['repayment_money'];
                    $edit['repayment_money']=$repayment_money+$list['repayment_money'];
                    $edit['repayment_number']=$list['repayment_number']+1; //还款次数
                    $last=$list['loan_money']*1000-$repayment_money*1000-$list['repayment_money']*1000;
                    if($last==0){
                        $edit['is_pay_off']=1;
                        $is_pay_off='借款金额已经还清。';
                    }else{
                        $is_pay_off='未还款金额'.($last/1000).'元。';
                    }
                    $edit['last_repayment_date']=date('Y-m-d H:i:s',time());
                    $loan_res=M('enterprise_loan')->save($edit);
                   // $account=M('enterprise_account')->where('enterprise_id='.$contract_info['enterprise_id'])->field('account_balance')->find();
                    if($loan_res){
                        $model->commit();
                        $msg = '还款申请单审核成功！';
                        $status = 'success';
                        $n_msg='审核成功';
                        $this->send_recharge(3,$contract_info['enterprise_id'],$repayment_money,$is_pay_off,2,$list['remark']);
                    }else{
                        $model->rollback();
                        $msg ='还款申请单审核失败！';
                        $n_msg='审核失败';
                    }
                }
            }else{
                $model->rollback();
                $msg = $approve_status==2?'还款申请单审核驳回失败！':'还款申请单审核失败！';
                $n_msg=$approve_status==2?'审核驳回失败！':'审核失败！';
            }
            $note = "用户【".get_user_name(D('SysUser')->self_id())."】，ID【".$repaymen_id."】，审核企业【".$contract_info['enterprise_name']."(".$contract_info['enterprise_code'].")】，借款申请单【".$contract_info['loan_code']."】，还款金额【" . $contract_info['repayment_money'] . "】元，".$n_msg;
            $this->sys_log('还款申请单审核',$note);
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }else{
            $where['re.repaymen_id']=trim(I('repaymen_id'));
            $list=$this->info($where);
            if($list['approve_status']==3 || $list['approve_status']==4){
                $this->ajaxReturn(array('msg'=>"请勿重复审核！",'status'=>$status));
            }
            $this->assign($list);
            $this->display();
        }
    }



    /*删除合同*/
    public function delete(){
        $msg = '系统错误!';
        $status = 'error';
        $repaymen_id=trim(I('repaymen_id'));
        $where['repaymen_id']=$repaymen_id;
        $list=M('enterprise_repaymen')->where($where)->find();
        $loan=M('enterprise_loan')->where('loan_id='.$list['loan_id'])->field('loan_code,enterprise_id')->find();
        if(empty($list)){
            $this->ajaxReturn(array('msg'=>'对不起，没有找到相关内容，请重试！','status'=>$status));
        }
        if(in_array($list['approve_status'],array(3,5))){
            $this->ajaxReturn(array('msg'=>'已经审核通过，请勿删除！','status'=>$status));
        }
        $model=M('enterprise_repaymen');
        $res =$model->where($where)->delete();
        if($res){
            $msg = '删除还款申请单成功！';
            $status = 'success';
            $n_msg='成功';
        }else{
            $msg = '删除还款申请单失败！';
            $n_msg='失败';
        }
        $note = "用户【".get_user_name(D('SysUser')->self_id())."】，ID【".$repaymen_id."】，删除还款申请单，企业【".obj_name($loan['enterprise_id'],2)."】,借款申请单【".$loan['loan_code']."】，还款金额【".$list['repayment_money'] . "】元".$n_msg;
        $this->sys_log('删除还款申请单',$note);
        $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
    }
    /*查看*/
    public function show(){
        if(I('download')){
            $where['repaymen_id'] = trim(I('repaymen_id'));
            $info = M('enterprise_repaymen')->where($where)->find();
            parent::download_contract('.'.$info['credential_one']);
        }else{
            $msg ="对不起，没有找到相关内容，请重试！";
            $status = 'error';
            $where['re.repaymen_id']=trim(I('repaymen_id'));
            $list=$this->info($where);
            if($list){
                $this->assign($list);
                $this->display();
            }else{
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
        }
    }

    public  function detailed($where){
        $where['e.status']=1;
        $list =M('enterprise_loan as  el')
            ->join('left join t_flow_enterprise as e on e.enterprise_id = el.enterprise_id')
            ->field('el.loan_code,el.loan_money,el.loan_date,el.remark,el.repayment_date,el.repayment_money,el.last_repayment_date,el.repayment_number,el.approve_status,el.last_approve_date,el.is_pay_off,el.create_user_id,el.create_date,el.loan_id,el.enterprise_id,e.enterprise_name,e.enterprise_code')
            ->where($where)
            ->find();
        return $list;

    }

    public  function detailed_lock($where){
        $where['e.status']=1;
        $list =M('enterprise_loan as  el')
            ->lock(true)
            ->join('left join t_flow_enterprise as e on e.enterprise_id = el.enterprise_id')
            ->field('el.loan_code,el.loan_money,el.loan_date,el.remark,el.repayment_date,el.repayment_money,el.last_repayment_date,el.repayment_number,el.approve_status,el.last_approve_date,el.is_pay_off,el.create_user_id,el.create_date,el.loan_id,el.enterprise_id,e.enterprise_name,e.enterprise_code')
            ->where($where)
            ->find();
        return $list;

    }


    public function info($where){
        $where['e.status']=1;
        $list =M('enterprise_repaymen  as  re')
            ->join('left join t_flow_enterprise_loan as el on re.loan_id = el.loan_id')
            ->join('left join t_flow_enterprise as e on e.enterprise_id = el.enterprise_id')
            ->field('re.*,el.loan_money,el.loan_date,el.repayment_money as el_repayment_money,el.repayment_date as el_repayment_date,el.loan_code,el.repayment_number,e.enterprise_name,e.enterprise_code')
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

    function  all_pay_money($id){
        $where['loan_id']=$id;
        $list =M('enterprise_loan')->field('loan_money,repayment_money')->where($where)->find();
        $where['approve_status']=3;
        $sum= M('enterprise_repaymen')->where($where)->sum('repayment_money');
        $last_money=$list['loan_money']-$list['repayment_money']-$sum;
        return $last_money;
    }

    /**
    *导出EXCEL
    **/
    public function export_excel(){
        $user_type = D("SysUser")->self_user_type();
        $model = M('enterprise_repaymen  as re');
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
            if($approve_status!=''){
                $map['re.approve_status'] = $approve_status;
            }
        if($start_datetime && $end_datetime){
            $map['re.repayment_date'] = array('between',array(start_time($start_datetime),end_time($end_datetime)));
        }elseif($start_datetime){
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            $map['re.repayment_date'] = array('between',array(start_time($start_datetime),$end_datetime));
        }elseif($end_datetime){
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
            $map['re.repayment_date'] = array('between',array($start_datetime,end_time($end_datetime)));
        }
        if($user_type == 3){
            $map['enterprise.enterprise_id']=D("SysUser")->self_enterprise_id();
        }
        if($user_type != 3){
            $where['re.enterprise_id'] = array(array('in',$this->os_enterprise_ids),array('in',D('Enterprise')->enterprise_ids2()),'or') ;
            $map['_complex'] = $where;
        }
        if($top_proxy_id && (in_array($top_proxy_id,explode(',',$this->os_proxy_ids)) or ($top_proxy_id == D('SysUser')->self_proxy_id()))){
            $ids = M('')->query("select getProxyChildList('$top_proxy_id') as ids");
            $map['enterprise.top_proxy_id'] = array('in',$ids[0]['ids']);
        }
        if($user_type != 3){
            if(D('SysUser')->is_top_proxy_admin() == false){
                $map['enterprise.top_proxy_id'] = D('SysUser')->self_proxy_id();
            }
        }
        $join=array(
            't_flow_enterprise as enterprise on enterprise.enterprise_id=re.enterprise_id',
            't_flow_enterprise_loan as el on re.loan_id = el.loan_id ',
            't_flow_sys_user as user on user.user_id = enterprise.sale_id and user.status = 1',
        );

        $list = $model
            ->field('re.*,el.loan_code,el.loan_money,el.loan_date,el.approve_status as el_approve_status ,el.is_pay_off,enterprise.enterprise_id,enterprise.enterprise_code,enterprise.enterprise_name,user.user_name')
            ->join($join,'left')
            ->limit(3000)
            ->order('re.modify_date desc,re.approve_status asc')
            ->where($map)
            ->select();

        $datas = array();
        $headArr=array("企业编号","企业名称","借款编号","借款金额(元)","借款时间","还款金额(元)","还款方式","打款户名/账号","还款时间","审核人","审核状态","申请时间");

        foreach ($list as $v) {
            $data=array();
            $data['enterprise_code'] = $v['enterprise_code'];
            $data['enterprise_name'] = $v['enterprise_name'];
            $data['loan_code'] = $v['loan_code'];
            $data['loan_money'] = $v['loan_money'];
            $data['loan_date'] = empty($v['loan_date'])?'--':$v['loan_date'];
            $data['repayment_money'] = $v['repayment_money'];

            $data['source'] = '--';
            if($v['source'] == 1){
                $data['source'] = "汇款";
            }elseif($v['source'] == 2){
                $data['source'] = "微信支付";
            }elseif($v['source'] == 3){
                $data['source'] = "支付宝支付";
            }

            $data['transaction_number'] = $v['transaction_number'];
            $data['repayment_date'] = msubstr($v['repayment_date'],0,10,"utf-8",false);
            $data['approve_user_id'] = get_user_name($v['approve_user_id']);
            $data['approve_status'] = get_pay_status($v['approve_status']);
            $data['create_date'] = $v['create_date'];
            array_push($datas,$data);
        }
            
        $title='企业还款管理';

        ExportEexcel($title,$headArr,$datas);
    }



}


