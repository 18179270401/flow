<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class ProxyTransferController extends CommonController{
    /*代理商资金划拨*/
    public function index(){
        D("SysUser")->sessionwriteclose();
        $apply_code = trim(I('get.apply_code'));
        $pay_proxy = trim(I('get.pay_proxy'));
        $pay_code = trim(I('get.pay_code'));
        $receive_code = trim(I('get.receive_code'));
        $receive_proxy = trim(I('get.receive_proxy'));
        $approve_status = trim(I('get.approve_status'));
        $start_datetime = trim(I('get.start_datetime'));
        $end_datetime = trim(I('get.end_datetime'));
        $map = array();
        if(!empty($apply_code)){
            $map['a.apply_code'] = array('like','%'.$apply_code.'%');
        }

        if(!empty($pay_proxy)){
            $map['b.proxy_name'] = array('like','%'.$pay_proxy.'%');
        }

        if(!empty($receive_proxy)){
            $map['c.proxy_name'] = array('like','%'.$receive_proxy.'%');
        }

        if(!empty($pay_code)){
            $map['b.proxy_code'] = array('like','%'.$pay_code.'%');
        }

        if(!empty($receive_code)){
            $map['c.proxy_code'] = array('like','%'.$receive_code.'%');
        }

        if(!empty($approve_status)){
            $map['a.approve_status'] = $approve_status;
        }

        //判断时间是否在一个月内
        if($start_datetime != "" && $end_datetime != ""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                $this->display();
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }

        if($start_datetime || $end_datetime){
            if($start_datetime && $end_datetime){
                $map['a.apply_date'] = array('between',array($start_datetime,$end_datetime));
            }
            if($start_datetime ==""){
                $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
                $map['a.apply_date'] = array('between',array($start_datetime,$end_datetime));
            }
            if($end_datetime ==""){
                $end_datetime = date("Y-m-d 23:59:59");
                $map['a.apply_date'] = array('between',array($start_datetime,$end_datetime));
            }
        }else {
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            
            $map['a.apply_date']= array('between',array($start_datetime,$end_datetime));
        }
        $map['b.top_proxy_id'] = D('SysUser')->self_proxy_id();

        $model = M('proxy_transfer_apply as a');
        $join = array(
                C('DB_PREFIX').'proxy as b ON a.pay_proxy_id = b.proxy_id',
                C('DB_PREFIX').'proxy as c ON a.receive_proxy_id = c.proxy_id',
            );

        $count = $model->join($join,'left')->where($map)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $sum_results = $model
            ->join($join,'left')->where($map)
            ->field('sum(a.apply_money) as sum_money_one')
            ->find();
        $list = $model->join($join,'left')->where($map)
            ->field('a.apply_id,a.apply_code,a.apply_money,a.pay_proxy_id,a.receive_proxy_id,a.apply_date,a.apply_user_id,a.remark,a.approve_status,a.last_approve_date,a.create_user_id,a.create_date,b.proxy_name as pay_proxy_name,b.proxy_code as pay_proxy_code,c.proxy_name as receive_proxy_name,c.proxy_code as receive_proxy_code')
            ->order('a.create_date desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        $list = get_sort_no($list,$Page->firstRow);
        $this->assign('start_datetime',$start_datetime);
        $this->assign('end_datetime',$end_datetime);
        $this->assign('sum_results',$sum_results);
        $this->assign('approve_status_list',get_contract_status());
        $this->assign('d_sdata',date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))));  //默认开始时间
        $this->assign('d_edata',date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))));  //默认结束时间
        $this->assign('list',$list);
        $this->assign('self_id',D('SysUser')->self_id());
        $this->assign('page',$show);
        $this->display();
    }

    /*新增资金划拨*/
    public function add(){
        $this->display();
    }

    /*新增资金划拨内容*/
    public function insert(){
        $msg = '系统错误!';
        $status = 'error';
        $data = array();
        $apply_money = trim(I('apply_money'));
        $pay_proxy_id = trim(I('pay_proxy_id'));
        $receive_proxy_id = trim(I('receive_proxy_id'));

        if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $apply_money)){
           $this->ajaxReturn(array('msg'=>'请输入正确的操作金额！','status'=>$status));
        }
        if($apply_money==''){
           $this->ajaxReturn(array('msg'=>'请输入的操作金额！','status'=>$status));
        }
        if($apply_money<=0){
           $this->ajaxReturn(array('msg'=>'操作金额需大于零！','status'=>$status));
        }
        if(empty($pay_proxy_id)){
            $this->ajaxReturn(array('msg'=>'请选择需要支出的代理商！','status'=>$status));
        }
        if(empty($receive_proxy_id)){
           $this->ajaxReturn(array('msg'=>'请选择需要接收的代理商！','status'=>$status));
        }


        if($receive_proxy_id == $pay_proxy_id){
           $this->ajaxReturn(array('msg'=>'支出与接收不能是同一个代理商！','status'=>$status));
        }

        $model = M('proxy_transfer_apply');
        $map = array();
        $start_time = date('Y-m-d 00:00:00');
        $end_time = date('Y-m-d 23:59:59');
        $map['create_date'] = array('between',array($start_time,$end_time));
        $today_info = $model->where($map)->order("apply_id desc")->getField('apply_code');
        $today_count = 0;
        if(!empty($today_info)){
            $today_count = substr($today_info,-4);
        }
        $today_count++;
        $apply_code = generate_transfer($today_count,1);

        $data['apply_code'] = $apply_code;
        $data['pay_proxy_id'] = $pay_proxy_id;
        $data['receive_proxy_id'] = $receive_proxy_id;
        $data['apply_money']=$apply_money;  //操作金额
        $data['approve_status']=1; //审核状态：草稿
        $data['remark']=trim(I('remark'));
        $data['apply_user_id']=D('SysUser')->self_id();//申请人
        $data['apply_date']=date('Y-m-d H:i:s',time()); //申请时间
        $data['create_user_id']=D('SysUser')->self_id();//创建人
        $data['create_date']=date('Y-m-d H:i:s',time());  //创建时间
        $data['modify_date']=date('Y-m-d H:i:s',time());  //修改时间
        $res = $model->add($data);
            //执行添加
        if($res){
            $msg = '新增代理商资金划拨申请单成功';
            $n_msg='成功';
            $status = 'success';
        }else{
            $msg = '新增代理商资金划拨申请单失败！';
            $n_msg='失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$res."】，新增代理商资金划拨申请单，支出代理商【".obj_name($pay_proxy_id,1)."】，接收代理商【".obj_name($receive_proxy_id,1)."】，申请单号【". $apply_code."】，操作金额【".money_format2($apply_money)."】元，".$n_msg;
        $this->sys_log('新增代理商资金划拨申请单',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'info'=>$res));
    }

    /*送审*/
    public function send(){
        $msg ="系统错误!";
        $status = 'error';
        
        $apply_id = trim(I('id'));

        $model = M("proxy_transfer_apply");
        //查询是否有该申请单
        $map_apply = array();
        $map_apply['apply_id'] = $apply_id;
        $map_apply['approve_status'] = 1;
        $apply_info = $model->where($map_apply)->find();

        if(empty($apply_info)){
            $this->ajaxReturn(array('msg'=>'查询失败','status'=>$status));
        }

        $apply_money = $apply_info['apply_money'];

        //开启存储过程：1,支付账户冻结金额，2，添加流水，3，修改审核状态
        M()->startTrans();

        $model_account = M("proxy_account");
        $map_proxy = array();
        $map_proxy['proxy_id'] = $apply_info['pay_proxy_id'];
        $pay_proxy_account = $model_account->lock(true)->where($map_proxy)->find();

        $pay_account_balance = $pay_proxy_account['account_balance'];
        $pay_freeze_money = $pay_proxy_account['freeze_money'];

        if($apply_money > $pay_account_balance){
            M()->rollback();
            $msg = "对不起，支付账户余额不足，请充值后，再操作！";
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }        

        //资金冻结
        $data_freeze = array();
        $data_freeze['account_balance'] = $pay_account_balance - $apply_money;
        $data_freeze['freeze_money'] = $pay_freeze_money + $apply_money;

        $result_freeze = $model_account->where($map_proxy)->save($data_freeze);

        //流水记录=================================
        $top_proxy_id=M('proxy')->where('proxy_id='.$apply_info['pay_proxy_id'])->field('top_proxy_id')->find();
        $result_record = $this->return_account_record($apply_info['pay_proxy_id'],$top_proxy_id['top_proxy_id'],$pay_account_balance,$apply_info['apply_money'],$data_freeze['account_balance'],3,2);
        //修改审核状态
        $data_approve = array();
        $data_approve['approve_status'] = 2;
        $data_approve['apply_id'] = $apply_id;
        $result_approve = $model->save($data_approve);


        if($result_freeze && $result_record && $result_approve){
            M()->commit();
            $msg = '代理商资金划拨申请单提交成功!';
            $n_msg = '成功';
            $status = 'success';
        }else{
            M()->rollback();
            $msg = '代理商资金划拨申请单提交失败!';
            $n_msg = '失败';            
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】,ID【".$apply_id."】,代理商资金划拨申请单【".$apply_info['apply_code']."】提交审核".$n_msg;
        $this->sys_log('提现申请单提交审核',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    /*编辑*/
    public function edit(){
        $msg ="系统错误!";
        $status = 'error';
        $map['a.apply_id'] = trim(I('apply_id'));
        $info = $this->detailed($map);
        if(empty($info )){
            $this->ajaxReturn(array('msg'=>'对不起，没有找到相关内容，请重试！','status'=>$status));
        }
        //var_dump($info);die();
        $this->assign($info);
        $this->display();
    }

    public  function update(){

        $msg = '系统错误!';
        $status = 'error';
        $data = array();
        $apply_id = trim(I('apply_id'));
        $apply_money = trim(I('apply_money'));
        $pay_proxy_id = trim(I('pay_proxy_id'));
        $receive_proxy_id = trim(I('receive_proxy_id'));
        if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $apply_money)){
            $this->ajaxReturn(array('msg'=>'请输入正确的操作金额！','status'=>$status));
        }
        if($apply_money==''){
            $this->ajaxReturn(array('msg'=>'请输入操作金额！','status'=>$status));
        }
        if($apply_money<=0){
            $this->ajaxReturn(array('msg'=>'操作金额需大于零！','status'=>$status));
        }
        if(empty($pay_proxy_id)){
            $this->ajaxReturn(array('msg'=>'请选择需要支出的代理商！','status'=>$status));
        }
        if(empty($receive_proxy_id)){
            $this->ajaxReturn(array('msg'=>'请选择需要接收的代理商！','status'=>$status));
        }
        if($receive_proxy_id == $pay_proxy_id){
           $this->ajaxReturn(array('msg'=>'支出与接收不能是同一个代理商！','status'=>$status));
        }
        $model = M("proxy_transfer_apply");
        $map = array();
        $map['apply_id'] = $apply_id;
        $info = $this->detailed($map);

        $data['apply_money'] = $apply_money;
        $data['pay_proxy_id'] = $pay_proxy_id;
        $data['receive_proxy_id'] = $receive_proxy_id;
        $data['approve_status'] = 1; //审核状态
        $data['modify_user_id'] = D('SysUser')->self_id();
        $data['remark']=trim(I('remark'));  //说明
        $data['modify_date'] = date('Y-m-d H:i:s', time());
        $res = $model->where($map)->save($data);
        $c_item='';
        $c_item.=$pay_proxy_id===$info['pay_proxy_id']?'':'支出代理商【'.obj_name($pay_proxy_id,1).'】';
        $fg=!empty($c_item)?'，':'';
        $c_item.=$receive_proxy_id===$info['receive_proxy_id']?'':$fg.'接收代理商【'.obj_name($receive_proxy_id,1).'】';
        $fg=!empty($c_item)?'，':'';
        $c_item.=$apply_money*100===$info['apply_money']*100?'':$fg.'操作金额【'.money_format2($apply_money).'】元';
            //执行添加
        if ($res) {
            $msg = '编辑代理商资金划拨申请单';
            $status = 'success';
            $n_msg='成功';
        } else {
            $msg = '编辑代理商资金划拨申请单';
            $n_msg='失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".trim(I('loan_id'))."】，编辑代理商资金划拨申请单，申请编号【".$info['apply_code']."】：".$c_item.$n_msg;
        $this->sys_log('代理商资金划拨申请单',$note);
        $this->ajaxReturn(array('msg' => $msg.$n_msg, 'status' => $status));

    }

    /*弹出审核界面*/
    public function  approve(){
        $msg = '系统错误';
        $status = 'error';
        $operate = trim(I('get.operate'));
        $apply_id = trim(I('apply_id'));

        $model = M('proxy_transfer_apply');
        $map = array();
        $map['apply_id'] = $apply_id;
        $info = $this->detailed($map);
        if(empty($info)){
            $this->ajaxReturn(array('msg'=>'对不起没有找到相关信息，请重试！','status'=>$status));
        }

        if($info['approve_status'] != 2){
            $this->ajaxReturn(array('msg'=>'该状态不能进行初审！','status'=>$status));
        }

        $pay_top_id = M('proxy')->where('proxy_id='.$info['pay_proxy_id'])->field('top_proxy_id')->find();

        if($operate == 'approve'){
            /*初审开始*/
            $approve_status = trim(I('approve_status'));
            $approve_remark = trim(I('approve_remark'));

            $model->startTrans();
            if($approve_status == 2 && $approve_remark == ""){
                $this->ajaxReturn(array('msg'=>'请填写审核驳回原因','status'=>$status));
            }

            //修改申请表信息
            $edit['apply_id'] = $apply_id;
            $edit['approve_status'] = $approve_status+2;
            $edit['last_approve_date'] = date("Y-m-d H:i:s");
            $apply_res = $model->where($map)->save($edit);

            //添加审核信息
            $add['apply_id'] = $apply_id;
            $add['approve_status'] = $approve_status;
            $add['approve_remark'] = $approve_remark;
            $add['approve_date'] = date('Y-m-d H:i:s');
            $add['approve_user_id'] = D('SysUser')->self_id();
            $add['approve_stage'] = 1;  //1初审；2复审
            $process = M("proxy_transfer_process")->add($add);

            //如果驳回，则把冻结金额返回 + 流水记录
            $result_ufreeze = 1;
            $result_record = 1;

            if($approve_status == 2){
                $model_account = M("proxy_account");
                $map_proxy = array();
                $map_proxy['proxy_id'] = $info['pay_proxy_id'];
                $pay_proxy_account = $model_account->lock(true)->where($map_proxy)->find();
                $pay_account_balance = $pay_proxy_account['account_balance'];
                $pay_freeze_money = $pay_proxy_account['freeze_money'];  
                $apply_money = $info['apply_money'];

                //资金解冻
                $data_ufreeze = array();
                $data_ufreeze['account_balance'] = $pay_account_balance + $apply_money;
                $data_ufreeze['freeze_money'] = $pay_freeze_money - $apply_money;

                $result_ufreeze = $model_account->where($map_proxy)->save($data_ufreeze);

                //流水==================================================
                $result_record = $this->return_account_record($info['pay_proxy_id'],$pay_top_id['top_proxy_id'],$pay_account_balance,$apply_money,$data_ufreeze['account_balance'],11,1);
            }


            if($apply_res && $process && $result_ufreeze && $result_record){
                $model->commit();
                $msg = $approve_status == 2 ? '代理商资金划拨初审驳回成功！':'代理商资金划拨初审成功！';
                $status = 'success';
                $n_msg=$approve_status == 2 ? '初审驳回成功':'初审成功';
            }else{
                $model->rollback();
                $msg = $approve_status == 2?'代理商资金划拨初审驳回失败！':'代理商资金划拨初审失败！';
                $n_msg=$approve_status == 2 ? '初审驳回失败':'初审失败';
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$apply_id.'】，审核代理商资金划拨申请单【'.$info['apply_code'].'】'.$n_msg;
            $this->sys_log('代理商资金划拨申请单初审',$note);
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }else{
            $this->assign("info",$info);
            $this->assign("type","1");
            $this->display('approve');
        }
    }

    /*弹出复审界面*/
    public function  approve_t(){
        $msg = '系统错误';
        $status = 'error';
        $operate = trim(I('get.operate'));
        $apply_id = trim(I('apply_id'));

        $model = M('proxy_transfer_apply');
        
        $map = array();
        $map = array('apply_id' => $apply_id);
        
        $info = $this->detailed($map);
        if(empty($info)){
            $this->ajaxReturn(array('msg'=>'对不起没有找到相关信息，请重试！','status'=>$status));
        }

        if($info['approve_status'] != 3){
            $this->ajaxReturn(array('msg'=>'该状态不能进行复审','status'=>$status));
        }

        $pay_top_id = M('proxy')->where('proxy_id='.$info['pay_proxy_id'])->field('top_proxy_id')->find();
        $receive_top_id = M('proxy')->where('proxy_id='.$info['receive_proxy_id'])->field('top_proxy_id')->find();
        
        if($operate == 'approve'){
            /*初审开始*/
            $type = trim(I('type'));
            $approve_status = trim(I('approve_status'));
            $approve_remark = trim(I('approve_remark'));

            if($approve_status == 2 && $approve_remark == ""){
                $this->ajaxReturn(array('msg'=>'请填写审核驳回原因','status'=>$status));
            }


            $model->startTrans();

            //获取资金信息
            $model_account = M("proxy_account");
            $map_proxy = array();
            $map_proxy['proxy_id'] = $info['pay_proxy_id'];
            $pay_proxy_account = $model_account->lock(true)->where($map_proxy)->find();

            $map_proxy_receive = array();
            $map_proxy_receive['proxy_id'] = $info['receive_proxy_id'];
            $receive_proxy_account = $model_account->lock(true)->where($map_proxy_receive)->find();

            $pay_account_balance = $pay_proxy_account['account_balance'];
            $pay_freeze_money = $pay_proxy_account['freeze_money'];  
            $apply_money = $info['apply_money'];
            $receive_account_balance = $receive_proxy_account['account_balance'];

            if($approve_status == 1 && $pay_freeze_money < $apply_money){
                $model->rollback();
                $this->ajaxReturn(array('msg'=>'冻结资金不足，请驳回！','status'=>$status));
            }

            $sure = trim(I('get.sure'));
            
            if($sure == 'sure' && $approve_status == 1 ){
                $data = array(
                    'apply_id' => $apply_id,
                    'approve_status' => $approve_status,
                    'approve_remark' => $approve_remark
                    );

                $msg="确定是否复审通过并从代理商【".$info['pay_proxy_name']."】向代理商【".$info['receive_proxy_name']."】划拨金额【".$info['apply_money']."】元";
                $model->rollback();
                $this->ajaxReturn(array('msg'=>$msg,"status"=>'success',"info"=>$data));
            }
            
            //修改申请表信息
            $edit['apply_id'] = $apply_id;
            $edit['approve_status'] = $approve_status + 4;
            $edit['last_approve_date'] = date("Y-m-d H:i:s");

            //添加审核信息
            $add['apply_id'] = $apply_id;
            $add['approve_status'] = $approve_status;
            $add['approve_remark'] = $approve_remark;
            $add['approve_date'] = date('Y-m-d H:i:s');
            $add['approve_user_id'] = D('SysUser')->self_id();
            $add['approve_stage'] = 2;

            $apply_res = $model->where($map)->save($edit);
    
            $process = M("proxy_transfer_process")->add($add);
            //如果驳回，则把冻结金额返回 + 流水记录

            $result_ufreeze = 1;
            $result_record = 1;

            if($approve_status == 2){
                //资金解冻
                $data_ufreeze = array();
                $data_ufreeze['account_balance'] = $pay_account_balance + $apply_money;
                $data_ufreeze['freeze_money'] = $pay_freeze_money - $apply_money;

                $result_ufreeze = $model_account->where($map_proxy)->save($data_ufreeze);

                //流水==================================================
                $result_record = $this->return_account_record($info['pay_proxy_id'],$pay_top_id['top_proxy_id'],$pay_account_balance,$apply_money,$data_ufreeze['account_balance'],11,1);
            }else{
                //如果成功，资金划拨过去+流水记录
                //划拨金额
                //支付方扣除冻结资金
                //流水==================================================
                $result_record = $this->account_record($info['pay_proxy_id'],$info['receive_proxy_id'],$apply_money,$pay_top_id['top_proxy_id'],$receive_top_id['top_proxy_id'],$pay_proxy_account,$receive_proxy_account);

            }

            if($apply_res && $process && $result_ufreeze && $result_record){
                $model->commit();
                $msg = $approve_status == 2 ? '代理商资金划拨申请单复审驳回成功！' : '代理商资金划拨申请单复审成功！';
                $status = 'success';
                $n_msg= $approve_status == 2 ? '复审驳回成功' : '复审成功';
            }else{
                $model->rollback();
                $msg = $approve_status == 2 ? '代理商资金划拨申请单复审驳回失败！' : '代理商资金划拨申请单复审失败！';
                $n_msg= $approve_status == 2 ? '复审驳回失败' : '复审失败';
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$apply_id.'】，审核代理商资金划拨申请单【'.$info['apply_code'].'】' .$n_msg;
            $this->sys_log('代理商资金划拨申请单复审',$note);
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));

        }else{
            $this->assign("info",$info);
            $this->display('approve');
        }
    }

    private function detailed($map){
        $model = M('proxy_transfer_apply as a');
        $join = array(
                C('DB_PREFIX').'proxy as b ON a.pay_proxy_id = b.proxy_id',
                C('DB_PREFIX').'proxy as c ON a.receive_proxy_id = c.proxy_id',
                C('DB_PREFIX').'proxy_account as d ON b.proxy_id = d.proxy_id',
                C('DB_PREFIX').'proxy_account as e ON c.proxy_id = e.proxy_id'
            );

        $info = $model->where($map)->
            field('a.apply_id,a.apply_code,a.apply_money,a.pay_proxy_id,a.receive_proxy_id,a.apply_date,a.apply_user_id,a.remark,a.approve_status,a.last_approve_date,a.create_user_id,a.create_date,b.proxy_code as pay_proxy_code,b.proxy_name as pay_proxy_name,c.proxy_code as receive_proxy_code,c.proxy_name as receive_proxy_name,d.account_balance as pay_account_balance,d.freeze_money as pay_freeze_money,e.account_balance as receive_account_balance,e.freeze_money as receive_freeze_money')
            ->join($join,'left')->find();
        return $info;
    }

    /*删除*/
    public function delete(){
        $msg = '系统错误!';
        $status = 'error';
        $apply_id = trim(I('apply_id'));
        $map['apply_id'] = $apply_id;
        $info = $this->detailed($map);
        if(empty($info)){
            $this->ajaxReturn(array('msg'=>'对不起，没有找到相关内容，请重试！','status'=>$status));
        }
        if(in_array($info['approve_status'],array(3,5))){
            $this->ajaxReturn(array('msg'=>'已经审核通过，请勿删除！','status'=>$status));
        }
        $model = M('proxy_transfer_apply');
        $model->startTrans();
        $res = $model->where($map)->delete();
        $del_res = M('proxy_transfer_process')->where($map)->delete();
        if($res && $del_res !== false){
            $model->commit();
            $msg = '删除代理商资金划拨成功！';
            $n_msg='成功';
            $status = 'success';
        }else{
            $model->rollback();
            $msg = '删除代理商资金划拨失败！';
            $n_msg='失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，删除代理商资金划拨，支出代理商【".obj_name($info['pay_proxy_id'],1)."】，接收代理商【".obj_name($info['receive_proxy_id'],1)."】，申请单编号【".$info['apply_code']."】，申请单额度【".$info['apply_money']."】".$n_msg;
        $this->sys_log('删除代理商资金划拨申请单',$note);
        $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
    }

    /*查看*/
    public function show(){
        $msg = '系统错误';
        $status = 'error';
        $operate = trim(I('operate'));
        if($operate == 'account'){
            $proxy_id = trim(I('proxy_id'));
            $map = array();
            $map['proxy_id'] = $proxy_id;
            $account_info = M('proxy_account')->where($map)->find();
            if(empty($account_info)){
                $account_info['account_balance'] = 0;
            }
            $this->ajaxReturn(array('msg'=>'成功','status'=>'success','info'=>$account_info));
            exit();
        }else{
            $apply_id = trim(I('apply_id'));
            $map = array('a.apply_id' => $apply_id);
            $info = $this->detailed($map);
            if(empty($info)){
                $this->ajaxReturn(array('msg'=>'对不起没有找到相关信息，请重试！','status'=>$status));
            }
            $info['approve_status'] = get_contract_status($info['approve_status']);
            $process = M('proxy_transfer_process as a')->where($map)->select();
            $this->assign($info);
            $this->assign('process',$process);
            $this->display();
        }
        
    }

    /**
     * 添加一条流水信息
     */
    private function return_account_record($proxy_id,$obj_proxy_id,$account_balance,$apply_money,$operater_after_balance,$operate_type,$balance_type){
        $record['operater_before_balance']   = $account_balance;  //操作前金额
        $record['operater_after_balance']    = $operater_after_balance; //操作后金额
        $record['operater_price']            = $apply_money;  //提现金额
        $record['operate_type']              = $operate_type; //1：购买流量，2：充值，3：提现，4：划拨，5：返还，6：分红、7退款、8：测试款、9：账户冻结、10：账户解冻
        $record['balance_type']              = $balance_type;//1：收入、2：支出
        $record['record_date']               = date('Y-m-d H:i:s',time());
        $record['user_id']                   = D('SysUser')->self_id();
        $record['operation_date']            = date('Y-m-d H:i:s',time());
        $record['user_type']                 = 1;
        //$record['proxy_id']                  = $proxy_id;
        $record['proxy_id']                  = $proxy_id;
        $record['obj_user_type']             = 1;
        $record['obj_proxy_id']              = $obj_proxy_id;
        //$record['obj_enterprise_id']         = $obj_enterprise_id;
        $record['device_name']               = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));
        //添加流水记录
        $recordResult=M('account_record')->add($record);
        if($recordResult){
            return true;
        }else{
            return false;
        }
    }


    /*流水记录*/
    public function account_record($pay_proxy_id,$receive_proxy_id,$apply_money,$pay_top_id,$receive_top_id,$pay_proxy_account,$receive_proxy_account){

        $model_account = M("proxy_account");

        $map_proxy = array();
        $map_proxy['proxy_id'] = $pay_proxy_id;

        $map_proxy_receive = array();
        $map_proxy_receive['proxy_id'] = $receive_proxy_id;

        

        $pay_account_balance = $pay_proxy_account['account_balance'];
        $pay_freeze_money = $pay_proxy_account['freeze_money'];  
        //$apply_money = $apply_money;
        $receive_account_balance = $receive_proxy_account['account_balance'];

        //支付方扣除冻结资金
        $data_dfreeze = array();
        $data_dfreeze['freeze_money'] = $pay_freeze_money - $apply_money;
        $data_dfreeze['modify_user_id'] = D('SysUser')->self_id();
        $data_dfreeze['modify_date'] = date('Y-m-d H:i:s',time());
        $result_dfreeze = $model_account->where($map_proxy)->save($data_dfreeze);

        /*支出上级代理商账户变更*/
        $pay_top_account = $model_account->lock(true)->where(array('proxy_id'=>$pay_top_id))->find();
        $data_ptop['account_id'] = $pay_top_account['account_id'];
        $data_ptop['account_balance'] = $pay_top_account['account_balance'] + $apply_money;
        $data_ptop['modify_user_id'] = D('SysUser')->self_id();
        $data_ptop['modify_date'] = date('Y-m-d H:i:s',time());
        
        //管理员
        if($receive_top_id == 1){
            $data_ptop['account_balance'] = 0;
            $result_ptop = 1;
        }else{
            $result_ptop = $model_account->save($data_ptop);
        }

        /*接收上级代理商账户变更*/
        $receive_top_account = $model_account->lock(true)->where(array('proxy_id'=>$receive_top_id))->find();


        $data_rtop['account_id'] = $receive_top_account['account_id'];
        $data_rtop['account_balance'] = $receive_top_account['account_balance']-$apply_money;
        $data_rtop['modify_user_id'] = D('SysUser')->self_id();
        $data_rtop['modify_date'] = date('Y-m-d H:i:s',time());
        
        //管理员
        if($receive_top_id == 1){
            $data_rtop['account_balance'] = 0;
            $result_rtop = 1;
        }else{
            $result_rtop = $model_account->save($data_rtop);
        }


        //接收方增加资金
        $data_abalance = array();
        $data_abalance['account_balance'] = $receive_account_balance + $apply_money;
        $data_abalance['modify_user_id'] = D('SysUser')->self_id();
        $data_abalance['modify_date'] = date('Y-m-d H:i:s',time());
        $result_abalance = $model_account->where($map_proxy_receive)->save($data_abalance);

        //流水==================================================
        
       /*支出上级代理商流水*/
        $record[0]['operater_before_balance']   = $pay_top_account['account_balance'];  //操作前金额
        $record[0]['operater_after_balance']    = $data_ptop['account_balance']; //操作后金额
        $record[0]['operater_price']            = $apply_money;  //提现金额
        $record[0]['operate_type']              = 11; //1：购买流量，2：充值，3：提现，4：划拨，5：返还，6：分红、7退款、8：测试款、9：账户冻结、10：账户解冻
        $record[0]['balance_type']              = 1;//1：收入、2：支出
        $record[0]['record_date']               = date('Y-m-d H:i:s',time());
        $record[0]['user_id']                   = D('SysUser')->self_id();
        $record[0]['operation_date']            = date('Y-m-d H:i:s',time());
        $record[0]['user_type']                 = 1;
        $record[0]['proxy_id']                  = $pay_top_id;
        $record[0]['enterprise_id']             = null;
        $record[0]['obj_user_type']             = 1;
        $record[0]['obj_proxy_id']              = $pay_proxy_id;
        $record[0]['obj_enterprise_id']         = null;
        $record[0]['device_name']               = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));

        /*接收上级代理商流水*/
        $record[1]['operater_before_balance']   = $receive_top_account['account_balance'];  //操作前金额
        $record[1]['operater_after_balance']    = $data_rtop['account_balance']; //操作后金额
        $record[1]['operater_price']            = $apply_money;  //提现金额
        $record[1]['operate_type']              = 4; //1：购买流量，2：充值，3：提现，4：划拨，5：返还，6：分红、7退款、8：测试款、9：账户冻结、10：账户解冻
        $record[1]['balance_type']              = 2;//1：收入、2：支出
        $record[1]['record_date']               = date('Y-m-d H:i:s',time());
        $record[1]['user_id']                   = D('SysUser')->self_id();
        $record[1]['operation_date']            = date('Y-m-d H:i:s',time());
        $record[1]['user_type']                 = 1;
        $record[1]['proxy_id']                  = $receive_top_id ;
        $record[1]['enterprise_id']             = null;
        $record[1]['obj_user_type']             = 1;
        $record[1]['obj_proxy_id']              = $receive_proxy_id;
        $record[1]['obj_enterprise_id']         = null;
        $record[1]['device_name']               = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));


        /*接收企业流水*/
        $record[2]['operater_before_balance']   = $receive_proxy_account['account_balance'];  //操作前金额
        $record[2]['operater_after_balance']    = $data_abalance['account_balance']; //操作后金额
        $record[2]['operater_price']            = $apply_money;  //提现金额
        $record[2]['operate_type']              = 2; //1：购买流量，2：充值，3：提现，4：划拨，5：返还，6：分红、7退款、8：测试款、9：账户冻结、10：账户解冻
        $record[2]['balance_type']              = 1;//1：收入、2：支出
        $record[2]['record_date']               = date('Y-m-d H:i:s',time());
        $record[2]['user_id']                   = D('SysUser')->self_id();
        $record[2]['operation_date']            = date('Y-m-d H:i:s',time());
        $record[2]['user_type']                 = 1;
        $record[2]['proxy_id']                  = $receive_proxy_id ;
        $record[2]['enterprise_id']             = null;
        $record[2]['obj_user_type']             = 1;
        $record[2]['obj_proxy_id']              = $receive_top_id;
        $record[2]['obj_enterprise_id']         = null;
        $record[2]['device_name']               = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));

        $recordResult = M('account_record')->addAll($record);
        if($result_dfreeze && $result_ptop && $result_rtop &&  $result_abalance &&$recordResult ){
            return true;
        }else{
            return false;
        }

    }


    public function export_excel(){
        $apply_code = trim(I('get.apply_code'));
        $pay_proxy = trim(I('get.pay_proxy'));
        $pay_code = trim(I('get.pay_code'));
        $receive_code = trim(I('get.receive_code'));
        $receive_proxy = trim(I('get.receive_proxy'));
        $approve_status = trim(I('get.approve_status'));
        $start_datetime = trim(I('get.start_datetime'));
        $end_datetime = trim(I('get.end_datetime'));
        $map = array();

        if(!empty($apply_code)){
            $map['a.apply_code'] = array('like','%'.$apply_code.'%');
        }

        if(!empty($pay_proxy)){
            $map['b.proxy_name'] = array('like','%'.$pay_proxy.'%');
        }

        if(!empty($receive_proxy)){
            $map['c.proxy_name'] = array('like','%'.$receive_proxy.'%');
        }

        if(!empty($pay_code)){
            $map['b.proxy_code'] = array('like','%'.$pay_code.'%');
        }

        if(!empty($receive_code)){
            $map['c.proxy_code'] = array('like','%'.$receive_code.'%');
        }

        if(!empty($approve_status)){
            $map['a.approve_status'] = $approve_status;
        }

        //判断时间是否在一个月内
        if($start_datetime != "" && $end_datetime != ""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                $this->display("index");
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }

        if($start_datetime || $end_datetime){
            if($start_datetime && $end_datetime){
                $map['a.apply_date'] = array('between',array($start_datetime,$end_datetime));
            }
            if($start_datetime ==""){
                $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
                $map['a.apply_date'] = array('between',array($start_datetime,$end_datetime));
            }
            if($end_datetime ==""){
                $end_datetime = end_time(msubstr($start_datetime,0,10,"utf-8",false));
                $map['a.apply_date'] = array('between',array($start_datetime,$end_datetime));
            }
        }else {
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $map['a.apply_date']= array('between',array($start_datetime,$end_datetime));
        }

        $map['b.top_proxy_id'] = D('SysUser')->self_proxy_id();
        $model = M('proxy_transfer_apply as a');
        $join = array(
                C('DB_PREFIX').'proxy as b ON a.pay_proxy_id = b.proxy_id',
                C('DB_PREFIX').'proxy as c ON a.receive_proxy_id = c.proxy_id',
            );


        $list = $model->join($join,'left')->where($map)
            ->field('a.apply_id,a.apply_code,a.apply_money,a.pay_proxy_id,a.receive_proxy_id,a.apply_date,a.apply_user_id,a.remark,a.approve_status,a.last_approve_date,a.create_user_id,a.create_date,b.proxy_name as pay_proxy_name,b.proxy_code as pay_proxy_code,c.proxy_name as receive_proxy_name,c.proxy_code as receive_proxy_code')
            ->order('a.create_date desc')
            ->limit(3000)
            ->select();

        $datas = array();
        
        $headArr=array("支出代理商编号","支出代理商名称","接收代理商编号","接收代理商名称","申请编号","划拨金额(元)","审核状态","申请人","申请时间");

        foreach ($list as $v) {
            $data=array();
            $data['pay_proxy_code'] = $v['pay_proxy_code'];
            $data['pay_proxy_name'] = $v['pay_proxy_name'];
            $data['receive_proxy_code'] = $v['receive_proxy_code'];
            $data['receive_proxy_name'] = $v['receive_proxy_name'];
            $data['apply_code'] = $v['apply_code'];
            $data['apply_money'] = $v['apply_money'];
            $data['approve_status'] = get_contract_status($v['approve_status']);
            $data['apply_user_id'] = get_user_name($v['apply_user_id']);
            $data['apply_date'] = $v['apply_date'];  

            array_push($datas,$data);
        }
            
        $title='代理商资金划拨管理';

        ExportEexcel($title,$headArr,$datas);
    }




    
    
}
?>