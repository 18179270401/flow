<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
class EnterpriseBorrowController extends CommonController {
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
                $is_admin=D('SysUser')->is_admin();
                $proxy_code=trim(I('proxy_code'));
                $proxy_name=trim(I('proxy_name'));
                $user_type=D('SysUser')->self_user_type();
                $is_pay_off=I('is_pay_off');
                if($user_type == 3){
                    $map['enterprise.enterprise_id']=D("SysUser")->self_enterprise_id();
                }
                if($loan_code){
                    $map['el.loan_code'] = array('like','%'.$loan_code.'%');
                }
                if($enterprise_code){
                    $map['enterprise.enterprise_code'] = array('like','%'.$enterprise_code.'%');
                }
                if($enterprise_name){
                    $map['enterprise.enterprise_name'] = array('like','%'.$enterprise_name.'%');
                }
                if($user_type==1){
                    if($proxy_code){
                        $where['p.proxy_code']=array('like','%'.$proxy_code.'%');
                    }
                    if($proxy_name){
                        $where['p.proxy_name']=array('like','%'.$proxy_name.'%');
                    }
                }
                if($is_pay_off!=9 && $is_pay_off!=''){
                    $map['el.is_pay_off']=$is_pay_off;
                }
                $user_id=D('SysUser')->self_id();
                $map['enterprise.status'] = array('neq',2);

                    if($approve_status!=''){
                        $map['el.approve_status'] = $approve_status;
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
                if($user_type != 3) {
                    $where['el.enterprise_id'] = array(array('in', $this->os_enterprise_ids), array('in', D('Enterprise')->enterprise_ids2()), 'or');
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
                   't_flow_enterprise as enterprise on enterprise.enterprise_id=el.enterprise_id',
                   't_flow_proxy as p on enterprise.top_proxy_id=p.proxy_id',
                   't_flow_sys_user as user on user.user_id = enterprise.sale_id and user.status = 1',
                 );
                $count = $model
                ->join($join,'left')
                ->where($map)
                ->count();

                $Page       = new Page($count,20);
                $show       = $Page->show();

                $list = $model
                ->field('el.loan_id,el.loan_code,el.loan_money,el.create_user_id,el.loan_date,el.approve_status,el.repayment_date,el.create_date,el.modify_date,el.is_pay_off,el.last_approve_date,el.repayment_date,enterprise.enterprise_id,enterprise.enterprise_code,enterprise.enterprise_name,user.user_name,p.proxy_code,p.proxy_name')
                ->join($join,'left')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('el.modify_date desc,el.approve_status asc')
                ->where($map)
                ->select();

            $sum_results =$model
                ->join($join,'left')
                ->where($map)
                ->field('sum(el.loan_money) as sum_money_one' )
                ->find();
                $u_role['role_id']=array("in",C("E_BORROW_ROLE"));  //还款管理员
                $u_role['user_id']=D("SysUser")->self_id();
                if(M("SysUserRole")->where($u_role)->find()){
                    $role=1;
                }else{
                    $role=2;
                }
                $this->assign("role",$role);
                $this->assign('sum_results',$sum_results);
                $this->assign('is_admin',$is_admin);
                $this->assign('page',$show);
                $this->assign('user_id',$user_id);
                $this->assign('user_type',$user_type);
        /*$this->assign('d_sdata',date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))));  //默认开始时间
        $this->assign('d_edata',date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))));  //默认结束时间*/
                $this->assign('list', get_sort_no($list, $Page->firstRow));
                $this->display();
    }



    /**
     *  新增企业授信
     */
    public function add(){
         $this->assign('enterprise',$this->enterprise_chd());
         $this->display();        //模板
    }

/*新增企业授信方法*/
    public function insert(){
        $operate=trim(I('get.operate'));
        $msg = '系统错误!';
        $status = 'error';
        if($operate =='send'){
            $loan_id = I('id');
            $map['loan_id']=$loan_id;
            $loan = M("enterprise_loan")->where($map)->find();
            if($loan){
                $edit['approve_status'] = 2;
                if(M("enterprise_loan")->where(array('loan_id'=>$loan['loan_id']))->save($edit)){
                    $msg = '授信申请单提交审核成功！';
                    $status = 'success';
                    $n_msg='成功';
                }else{
                    $msg = '授信申请单提交审核失败！';
                    $n_msg='失败';
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$loan['loan_id']."】，借款申请单【".$loan['loan_code']."】提交审核".$n_msg;
                $this->sys_log('授信申请单提交审核',$note);
            }else{
                $msg ="对不起，没有找到相关内容，请重试！";
            }
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }else{
            $data = array();
            $loan_money=trim(I('loan_money'));
            $enterprise_id=trim(I('enterprise_id'));
            $repayment_date=trim(I('repayment_date'));
            if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $loan_money)){
                $this->ajaxReturn(array('msg'=>'请输入正确的授信金额！','status'=>$status));
            }
            if($loan_money==''){
                $this->ajaxReturn(array('msg'=>'请输入的授信金额！','status'=>$status));
            }
            if($loan_money<=0){
                $this->ajaxReturn(array('msg'=>'授信金额需大于零！','status'=>$status));
            }
            if($enterprise_id==0 || $enterprise_id==""){
                $this->ajaxReturn(array('msg'=>'请选择企业！','status'=>$status));
            }
            if(empty($repayment_date)){
                $this->ajaxReturn(array('msg'=>'请输入预计还款时间！','status'=>$status));
            }

            $loan_code="JKSQD".date('Ymd',time()); //申请单编号
            $app['loan_code']=array('like',$loan_code.'%');
            $applys=M('enterprise_loan')->order("loan_code desc")->where($app)->find();
            $applys=substr($applys['loan_code'],13);
            $applys=$applys+1;
            $data['loan_code']=generate_loan($applys,1);
            $data['enterprise_id']=$enterprise_id;  //企业ID
            $data['loan_money']=$loan_money;  //授信金额
            $data['approve_status']=1; //审核状态：草稿
            $data['create_user_id']=D('SysUser')->self_id();
            $data['remark']=trim(I('remark'));  //说明
            $data['repayment_date']=trim(I('repayment_date'));//预计还款时间
            $data['loan_date']=date('Y-m-d H:i:s',time()); //授信时间
            $data['create_date']=date('Y-m-d H:i:s',time());  //申请时间
            $data['modify_date']=date('Y-m-d H:i:s',time());  //修改时间
            $res=M('enterprise_loan')->add($data);
            //执行添加
            if($res){
                $msg = '新增授信申请单成功';
                $n_msg='成功';
                $status = 'success';
            }else{
                $msg = '新增授信申请单失败！';
                $n_msg='失败';
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$res."】，新增企业授信申请单，企业【".obj_name($enterprise_id,2)."】，申请单号【". $data['loan_code']."】，申请金额【".money_format2($loan_money)."】元，".$n_msg;
            $this->sys_log('新增授信申请单',$note);
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'info'=>$res));
        }

    }

    /*企业授信送审*/
    public function send_approve(){
        $msg ="系统错误!";
        $status = 'error';
        $loan_id = I('id');
        $map['loan_id']=$loan_id;
        $loan = M("enterprise_loan")->where($map)->find();
        if($loan){
            $edit['approve_status'] = 2;
            if(M("enterprise_loan")->where(array('loan_id'=>$loan['loan_id']))->save($edit)){
                $msg = '授信申请单提交成功';
                $status = 'success';
                $n_msg='成功';
            }else{
                $msg = '授信申请单提交失败！';
                $n_msg='失败';
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$loan['loan_id']."】，授信申请单【".$loan['loan_code']."】提交审核".$n_msg;
            $this->sys_log('授信申请单提交审核',$note);
        }else{
            $msg ="对不起，没有找到相关内容，请重试！";
        }

        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }


       /*弹出编辑页面*/
        public function edit(){
        $msg ="对不起，没有找到相关内容，请重试！";
        $status = 'error';
        $where['el.loan_id']=trim(I('loan_id'));
        $list=$this->detailed($where);
        if($list){
            $this->assign('enterprise',$this->enterprise_chd());
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
        if($operate=='send'){
            $loan_id = I('id');
            $map['loan_id']=$loan_id;
            $loan = M("enterprise_loan")->where($map)->find();
            if($loan){
                $edit['approve_status'] = 2;
                if(M("enterprise_loan")->where(array('loan_id'=>$loan['loan_id']))->save($edit)){
                    $msg = '授信申请单提交成功';
                    $n_msg='成功';
                    $status = 'success';
                }else{
                    $msg = '授信申请单提交失败！';
                    $n_msg='失败';
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$loan['loan_id']."】，借款申请单【".$loan['loan_code']."】提交审核".$n_msg;
                $this->sys_log('授信申请单提交审核',$note);
            }else{
                $msg ="对不起，没有找到相关内容，请重试！";
            }

            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }else {
            $data = array();
            $loan_money=trim(I('loan_money'));
            $enterprise_id=trim(I('enterprise_id'));
            $repayment_date=trim(I('repayment_date'));
            if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $loan_money)){
                $this->ajaxReturn(array('msg'=>'请输入正确的授信金额！','status'=>$status));
            }
            if($loan_money==''){
                $this->ajaxReturn(array('msg'=>'请输入的授信金额！','status'=>$status));
            }
            if($loan_money<=0){
                $this->ajaxReturn(array('msg'=>'授信金额需大于零！','status'=>$status));
            }
            if($enterprise_id==0 || $enterprise_id==""){
                $this->ajaxReturn(array('msg'=>'请选择企业！','status'=>$status));
            }
            if(empty($repayment_date)){
                $this->ajaxReturn(array('msg'=>'请输入预计还款时间！','status'=>$status));
            }
            $where['loan_id'] = trim(I('loan_id'));
            $loan = M("enterprise_loan")->where($where)->field('loan_code,enterprise_id,loan_money')->find();
            $data['loan_money'] = $loan_money;
            $data['enterprise_id'] = $enterprise_id;
            $data['approve_status'] = 1; //审核状态
            $data['modify_user_id'] = D('SysUser')->self_id();
            $data['remark']=trim(I('remark'));  //说明
            $data['repayment_date']=trim(I('repayment_date'));//预计还款时间
            $data['modify_date'] = date('Y-m-d H:i:s', time());
            $res = M('enterprise_loan')->where($where)->save($data);
            $c_item='';
            $c_item.=$enterprise_id===$loan['enterprise_id']?'':'，企业【'.obj_name($enterprise_id,2).'】';
            $c_item.=$loan_money*100===$loan['loan_money']*100?'':'，授信金额【'.$loan_money.'】元';
            //执行添加
            if ($res) {
                $msg = '编辑授信申请单成功';
                $status = 'success';
                $n_msg='成功';
            } else {
                $msg = '编辑授信申请单失败！';
                $n_msg='失败';
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".trim(I('loan_id'))."】，编辑授信申请单，申请编号【".$loan['loan_code']."】".$c_item.$n_msg;
            $this->sys_log('编辑授信申请单',$note);
            $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
        }
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
                    $this->ajaxReturn(array('msg'=>'请填写审核驳回原因','status'=>$status));
                }
                //读取申请信息
                $contract_info = $model->where(array('loan_id'=>$loan_id))->find();
                if(in_array($contract_info['approve_status'],array(3,4))){
                    $this->ajaxReturn(array('msg'=>'不可重复审核','status'=>$status));
                }
                if(in_array($contract_info['approve_status'],array(5,6))){
                    $this->ajaxReturn(array('msg'=>'已经复审，请勿重复审核！','status'=>$status));
                }
                //修改申请表信息
                $edit['loan_id'] = $loan_id;
                $edit['approve_status'] = $approve_status==2?"4":"3";
                $edit['last_approve_date'] = date("Y-m-d H:i:s",time());
                $apply_res=$model->where(array('loan_id'=>$loan_id))->save($edit);
                $loan = M("enterprise_loan")->where('loan_id='.$loan_id)->field('loan_code')->find();
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
                    $msg = $approve_status==2?'授信申请单初审驳回成功！':'授信申请单初审成功！';
                    $n_msg=$approve_status==2?'初审驳回成功':'初审成功';
                    $status = 'success';
                }else{
                    $model->rollback();
                    $msg = $approve_status==2?'授信申请单初审驳回失败！':'授信申请单初审失败！';
                    $n_msg=$approve_status==2?'初审驳回失败':'初审失败';
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$loan_id."】，审核企业借款申请单【".$loan['loan_code']."】".$n_msg;
                $this->sys_log('企业授信申请单初审',$note);
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
                    $msg = "确定是否复审通过并向【" . $result['enterprise_name'] . "】授信" . $result['loan_money'] . "元？";
                    $this->ajaxReturn(array('msg' => $msg, 'status' => 'success', 'info' => $da));
                }
            }
            /*复审开始*/

            $model=M('enterprise_loan');
            $model->startTrans();
            if($approve_status==2 && $approve_remark==""){
                $model->rollback();
                $this->ajaxReturn(array('msg'=>'请填写审核驳回原因','status'=>$status));
            }
            //读取申请信息
            $contract_info = $model->where(array('loan_id'=>$loan_id))->find();
            if($contract_info['approve_status']==2){
                $model->rollback();
                $this->ajaxReturn(array('msg'=>'请等待初审完成','status'=>$status));
            }
            if($contract_info['approve_status']==4){
                $model->rollback();
                $this->ajaxReturn(array('msg'=>'初审驳回,不可复审','status'=>$status));
            }
            if(in_array($contract_info['approve_status'],array(5,6))){
                $model->rollback();
                $this->ajaxReturn(array('msg'=>'不可重复审核','status'=>$status));
            }
            //修改申请表信息
            $edit['loan_id'] = $loan_id;
            $edit['approve_status'] = $approve_status==2?"6":"5";
            $edit['last_approve_date'] = date("Y-m-d H:i:s",time());
            $loan_res=$model->where(array('loan_id'=>$loan_id))->save($edit);
            $loan = M("enterprise_loan")->where('loan_id='.$loan_id)->field('loan_code')->find();
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
                    $msg = $approve_status==2?'授信申请单复审驳回成功！':'授信申请单复审成功！';
                    $n_msg=$approve_status==2?'复审驳回成功':'复审成功';
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
                        $model->rollback();
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
                    $condition['remark']='授信';
                    $res=$this->account_record($condition);
                    if($res){
                        $model->commit();
                        $msg = '授信申请单复审成功！';
                        $n_msg='复审成功';
                        $this->send_recharge(3,$loan['enterprise_id'],$loan['loan_money'],$account['account_balance']+$loan['loan_money'],1,$loan['remark']);
                        $success_msg="，并向企业【".obj_name($loan['enterprise_id'],2)."】授信，授信金额【".$loan['loan_money']."】元";
                        $status = 'success';
                    }else{
                        $model->rollback();
                        $msg ='授信申请单复审失败！';
                        $n_msg='复审失败';
                    }
                }
            }else{
                $model->rollback();
                $msgs = $approve_status==2?'授信申请单复审驳回失败！':'授信申请单复审失败！';
                $msg =$msgs;
                $n_msg=$approve_status==2?'复审驳回失败':'复审失败';
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$loan['loan_id']."】，审核企业借款申请单【".$loan['loan_code']."】".$n_msg.$success_msg;
            $this->sys_log('企业授信申请单复审',$note);
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


    /*删除合同*/
    public function delete(){
        $msg = '系统错误!';
        $status = 'error';
        $loan_id=trim(I('loan_id'));
        $where['loan_id']=$loan_id;
        $list=M('enterprise_loan')->where($where)->find();
        if(empty($list)){
            $this->ajaxReturn(array('msg'=>'对不起，没有找到相关内容，请重试！','status'=>$status));
        }
        if(in_array($list['approve_status'],array(3,5))){
            $this->ajaxReturn(array('msg'=>'已经审核通过，请勿删除！','status'=>$status));
        }
        $model=M('enterprise_loan');
        $model->startTrans();
        $res =$model->where($where)->delete();
        $del_res=M('enterprise_loan_process')->where($where)->delete();
        if($res){
            $model->commit();
            $msg = '删除授信申请单成功！';
            $n_msg='成功';
            $status = 'success';
        }else{
            $model->rollback();
            $msg = '删除授信申请单失败！';
            $n_msg='失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$list['loan_id']."】，删除企业【".obj_name($list['enterprise_id'],2)."】借款申请单【".$list['loan_code']."】".$n_msg;
        $this->sys_log('删除企业授信申请单',$note);
        $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
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
                $this->assign('user_type',D('SysUser')->self_user_type());
                $this->display();
            }else{
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
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
        $res=M('enterprise_account')->lock(true)->where($where)->field('account_balance,account_id,enterprise_id')->find();
        return $res;
    }
    /*企业上级代理商账户账户*/
    public function p_account($where){
        $model=M('enterprise as e');
        $res=$model
            ->lock(true)
            ->join('left join t_flow_proxy_account as p on  e.top_proxy_id=p.proxy_id')
            ->where($where)->field('p.account_balance,p.account_id,p.proxy_id')->find();
        return $res;
    }

    public function process($where){
        $list=M("enterprise_loan_process")->where($where)->select();
        return $list;
    }


    public  function detailed($where){
        $where['e.status']=1;
        $list =M('enterprise_loan as  el')
            ->join('left join t_flow_enterprise as e on e.enterprise_id = el.enterprise_id')
            ->join('left join t_flow_proxy as p on e.top_proxy_id=p.proxy_id')
            ->field('el.*,e.enterprise_name,e.enterprise_code,p.proxy_code,p.proxy_name')
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

    /**
    *导出EXCEL
    **/
    public function export_excel(){
        $model = M('enterprise_loan as el');
        $loan_code=trim(I('loan_code'));
        $enterprise_code = trim(I('get.enterprise_code'));
        $enterprise_name = trim(I('get.enterprise_name'));
        $approve_status = trim(I('get.approve_status'));
        $top_proxy_id = trim(I('get.top_proxy_id'));
        $start_datetime =trim(I('start_datetime')) ;
        $end_datetime = trim(I('end_datetime'));
        $is_admin=D('SysUser')->is_admin();
        $proxy_code=trim(I('proxy_code'));
        $proxy_name=trim(I('proxy_name'));
        $user_type=D('SysUser')->self_user_type();
        $is_pay_off=I('is_pay_off');
        if($user_type == 3){
            $map['enterprise.enterprise_id']=D("SysUser")->self_enterprise_id();
        }
        if($loan_code){
            $map['el.loan_code'] = array('like','%'.$loan_code.'%');
        }
        if($enterprise_code){
            $map['enterprise.enterprise_code'] = array('like','%'.$enterprise_code.'%');
        }
        if($enterprise_name){
            $map['enterprise.enterprise_name'] = array('like','%'.$enterprise_name.'%');
        }
        if($user_type==1){
            if($proxy_code){
                $where['p.proxy_code']=array('like','%'.$proxy_code.'%');
            }
            if($proxy_name){
                $where['p.proxy_name']=array('like','%'.$proxy_name.'%');
            }
        }
        if($is_pay_off!=9 && $is_pay_off!=''){
            $map['el.is_pay_off']=$is_pay_off;
        }
        $user_id=D('SysUser')->self_id();
        $map['enterprise.status'] = array('neq',2);

        if($approve_status!=''){
            $map['el.approve_status'] = $approve_status;
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
        if($user_type != 3) {
            $where['el.enterprise_id'] = array(array('in',$this->os_enterprise_ids),array('in',D('Enterprise')->enterprise_ids2()),'or') ;
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
            't_flow_enterprise as enterprise on enterprise.enterprise_id=el.enterprise_id',
            't_flow_proxy as p on enterprise.top_proxy_id=p.proxy_id',
            't_flow_sys_user as user on user.user_id = enterprise.sale_id and user.status = 1',
        );

        $list = $model
        ->field('el.loan_id,el.loan_code,el.loan_money,el.create_user_id,el.loan_date,el.approve_status,el.repayment_date,el.create_date,el.modify_date,el.is_pay_off,el.last_approve_date,el.repayment_date,enterprise.enterprise_id,enterprise.enterprise_code,enterprise.enterprise_name,user.user_name,p.proxy_code,p.proxy_name')
        ->join($join,'left')
        ->limit(3000)
        ->order('el.modify_date desc,el.approve_status asc')
        ->where($map)
        ->select();

        $datas = array();
        if($user_type==1){
            $headArr=array("企业编号","企业名称","代理商编号","代理商名称","授信编号","授信金额(元)","审核状态","借款时间","未还款金额(元)","是否还清","预计还款时间");

        }else{
            $headArr=array("企业编号","企业名称","授信编号","授信金额(元)","审核状态","授信时间","未还款金额(元)","是否还清","预计还款时间");
        }


        foreach ($list as $v) {
            $data=array();
            $data['enterprise_code'] = $v['enterprise_code'];
            $data['enterprise_name'] = $v['enterprise_name'];
            if($user_type==1){
                $data['proxy_code'] = $v['proxy_code'];
                $data['proxy_name'] = $v['proxy_name'];
            }
            $data['loan_code'] = $v['loan_code'];
            $data['loan_money'] = $v['loan_money'];
            $data['approve_status'] = get_contract_status($v['approve_status']);
            $data['loan_date'] = empty($v['loan_date'])?'--':$v['loan_date'];
            $data['loan_id'] = last_money($v['loan_id']);
            $data['is_pay_off'] = $v['is_pay_off'] == 1?"是":"否";
            $data['repayment_date'] = msubstr($v['repayment_date'],0,10,'utf-8',false);


            array_push($datas,$data);
        }
            
        $title='企业授信管理';

        ExportEexcel($title,$headArr,$datas);
    }


}


