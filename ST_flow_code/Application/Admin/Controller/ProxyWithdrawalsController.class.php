<?php
/**
 *
 * 代理商提现明细表
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class ProxyWithdrawalsController extends CommonController{
	public function index(){
        //获取自身的用户类型是运营平台，代理商，企业
        $user=D('SysUser')->self_user_type();
        if($user == 2){
            $start_datetime = trim(I('get.start_datetime'));   //开始时间
            $end_datetime = trim(I('get.end_datetime'));   //结束时间
            $approve_status=trim(I('get.approve_status'));   //审核状态
            $apply_code=trim(I('get.apply_code'));//申请编号
            $is_play_money=trim(I('get.is_play_money'));
            $where=array();
            if($approve_status!="" && $approve_status!=9){
                $where['ap.approve_status'] = $approve_status;
            }
            if($start_datetime && $end_datetime){
                $where['ap.create_date']=array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }else if($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['ap.create_date']= array('between',array(start_time($start_datetime),$end_datetime));
            }else if($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['ap.create_date']=array('between',array($start_datetime,end_time($end_datetime)));
            }else{
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
                $where['ap.create_date'] = array('between',array($start_datetime,$end_datetime));
            }
            if($apply_code){
                $where['ap.apply_code']=array('like','%'.$apply_code.'%');
            }
            if($is_play_money!="" && $is_play_money!=9){
                $where['ap.is_play_money'] = $is_play_money;
            }
            $where['ap.proxy_id']=D('SysUser')->self_proxy_id();//获取自身的代理商ID
        }else{
            $proxy_code = trim(I('get.proxy_code'));   //代理商编号
            $proxy_name = trim(I('get.proxy_name'));   //代理商名称
            $apply_code=trim(I('get.apply_code'));//申请编号
            $start_datetime = trim(I('get.start_datetime'));   //开始时间
            $end_datetime = trim(I('get.end_datetime'));   //结束时间
            $approve_status=trim(I('get.approve_status'));   //审核状态
            $is_play_money=trim(I('get.is_play_money'));
            $where=array();
            if(!empty($proxy_code))$where['p.proxy_code'] = array('like','%'.$proxy_code.'%');
            if(!empty($proxy_name))$where['p.proxy_name'] = array('like','%'.$proxy_name.'%');
            if($apply_code){
                $where['ap.apply_code']=array('like','%'.$apply_code.'%');
            }
            if($is_play_money!="" && $is_play_money!=9){
                $where['ap.is_play_money'] = $is_play_money;
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
            $ids=D("Proxy")->proxy_child_ids();//获取该用户可操作的代理商号
            $where['p.proxy_id']=array("in",$ids);
            $where['ap.top_proxy_id']=D("SysUser")->self_proxy_id();
        }

        $join = array(
            "t_flow_proxy as p on p.proxy_id = ap.proxy_id",
            "t_flow_proxy as up on up.proxy_id = ap.top_proxy_id"
        );
        $count=M("proxy_withdraw_apply as ap")->join($join)->where($where)->count();
        $Page       = new Page($count,20);
        $show     = $Page->show();
        $proxyw_list =M("proxy_withdraw_apply as ap")
         ->join($join)
         ->field('ap.*,p.proxy_name,p.proxy_code,up.proxy_code as proxy_code2,up.proxy_name as proxy_name2')
         ->where($where)
         ->order('ap.modify_date desc,ap.approve_status asc')
         ->limit($Page->firstRow.','.$Page->listRows)
         ->select();
            $sum_results = M("proxy_withdraw_apply as ap")
                ->join($join,'left')->where($where)
                ->field('sum(ap.apply_money) as sum_money_one')
                ->find();
        $this->assign('proxyw_list',get_sort_no($proxyw_list,$Page->firstRow));  //数据列表
        $this->assign("proxy_id",D("SysUser")->self_proxy_id());
        $this->assign('page',$show);
        $this->assign('d_sdata',date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))));  //默认开始时间
        $this->assign('d_edata',date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))));  //默认结束时间
        $pagehtml = $user==2?"":"st_index";            //分页
        $this->assign('sum_results',$sum_results);
        $this->display($pagehtml);
    }


    public function export_excel(){
        //获取自身的用户类型是运营平台，代理商，企业
        $user=D('SysUser')->self_user_type();
        if($user == 2){
            $start_datetime = trim(I('get.start_datetime'));   //开始时间
            $end_datetime = trim(I('get.end_datetime'));   //结束时间
            $approve_status=trim(I('get.approve_status'));   //审核状态
            $apply_code=trim(I('get.apply_code'));//申请编号
            $is_play_money=trim(I('get.is_play_money'));
            $where=array();
            if($approve_status!="" && $approve_status!=9){
                $where['ap.approve_status'] = $approve_status;
            }
                if($start_datetime && $end_datetime){
                    $where['ap.create_date']=array('between',array(start_time($start_datetime),end_time($end_datetime)));
                }else if($start_datetime){
                    $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                    $where['ap.create_date']= array('between',array(start_time($start_datetime),$end_datetime));
                }else if($end_datetime){
                    $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                    $where['ap.create_date']=array('between',array($start_datetime,end_time($end_datetime)));
                }
            if($apply_code){
                $where['ap.apply_code']=array('like','%'.$apply_code.'%');
            }
            if($is_play_money!="" && $is_play_money!=9){
                $where['ap.is_play_money'] = $is_play_money;
            }
            $where['ap.proxy_id']=D('SysUser')->self_proxy_id();//获取自身的代理商ID
            $title='代理商提现申请';
            $headArr=array("申请编号","提现金额(元)","开户行","收款人姓名","收款人电话","操作人","申请时间","审核状态","是否打款","审核时间");
        }else{
            $proxy_code = trim(I('get.proxy_code'));   //代理商编号
            $proxy_name = trim(I('get.proxy_name'));   //代理商名称
            $apply_code=trim(I('get.apply_code'));//申请编号
            $start_datetime = trim(I('get.start_datetime'));   //开始时间
            $end_datetime = trim(I('get.end_datetime'));   //结束时间
            $approve_status=trim(I('get.approve_status'));   //审核状态
            $is_play_money=trim(I('get.is_play_money'));
            $where=array();
            if(!empty($proxy_code))$where['p.proxy_code'] = array('like','%'.$proxy_code.'%');
            if(!empty($proxy_name))$where['p.proxy_name'] = array('like','%'.$proxy_name.'%');
            if($apply_code){
                $where['ap.apply_code']=array('like','%'.$apply_code.'%');
            }
            if($is_play_money!="" && $is_play_money!=9){
                $where['ap.is_play_money'] = $is_play_money;
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
            $ids=D("Proxy")->proxy_child_ids();//获取该用户可操作的代理商号
            $where['p.proxy_id']=array("in",$ids);
            $where['ap.top_proxy_id']=D("SysUser")->self_proxy_id();
            $title='代理商提现管理';
            $headArr=array("代理商编号","代理商名称","申请编号","提现金额","申请时间","审核状态","是否打款","审核时间");
        }
        $join = array(
            "t_flow_proxy as p on p.proxy_id = ap.proxy_id",
            "t_flow_proxy as up on up.proxy_id = ap.top_proxy_id"
        );

        $proxyw_list =M("proxy_withdraw_apply as ap")
            ->join($join)
            ->field('ap.*,p.proxy_name,p.proxy_code,up.proxy_code as proxy_code2,up.proxy_name as proxy_name2')
            ->order('ap.modify_date desc,ap.approve_status asc')
            ->limit(3000)
            ->where($where)
            ->select();
        $list=array();
        if($user == 2){
            foreach($proxyw_list as $k=>$v){
                $list[$k]['apply_code'] =$v['apply_code'];
                $list[$k]['apply_money'] =$v['apply_money'];
                $list[$k]['bank_account'] =$v['bank_account'];
                $list[$k]['beneficiary_name'] =$v['beneficiary_name'];
                $list[$k]['mobile'] =$v['mobile'];
                $list[$k]['create_user_id'] =get_user_name($v['create_user_id']);
                $list[$k]['create_date'] =$v['create_date'];
                $list[$k]['approve_status'] =get_apply_status($v['approve_status']);
                if($v['is_play_money']==1){
                    $list[$k]['is_play_money']="已打款";
                }elseif($v['is_play_money']==2){
                    $list[$k]['is_play_money']="打款驳回";
                }else{
                    $list[$k]['is_play_money']="未打款";
                }
                $list[$k]['last_approve_date'] =$v['last_approve_date'];
            }   
        }else{
            foreach($proxyw_list as $k=>$v){
                $list[$k]['proxy_code'] =$v['proxy_code'];
                $list[$k]['proxy_name'] =$v['proxy_name'];
                $list[$k]['apply_code'] =$v['apply_code'];
                $list[$k]['apply_money'] =$v['apply_money'];
                $list[$k]['create_date'] =$v['create_date'];
                $list[$k]['approve_status'] =get_apply_status($v['approve_status']);
                if($v['is_play_money']==1){
                    $list[$k]['is_play_money']="已打款";
                }elseif($v['is_play_money']==2){
                    $list[$k]['is_play_money']="打款驳回";
                }else{
                    $list[$k]['is_play_money']="未打款";
                }
                $list[$k]['last_approve_date'] =$v['last_approve_date'];

            }
        }

        ExportEexcel($title,$headArr,$list);
    }


    public function add (){
        //读取代理商已有的账户信息
        $proxy_id = D('SysUser')->self_proxy_id();
        $list = D("Proxy")->list_proxy_set($proxy_id);
        $this->assign("list",$list);
        $this->display();
    }
    public function send_approve(){
        $msg = '系统错误!';
        $status = 'error';
        $id = I("id");
        //读取当前申请的金额
        $where['proxy_id'] = D('SysUser')->self_proxy_id();//获取自身的代理商ID
        $where['apply_id'] = $id;
        $where['approve_status'] = 1;
        $apply = M("proxy_withdraw_apply")->where($where)->find();
        if($apply){
            //读取当前代理商的账户信息
            $model=M('proxy_withdraw_apply');
            $model->startTrans();
            $account = M("proxy_account")->lock(true)->where(array('proxy_id'=>$where['proxy_id']))->find();
            if($account['account_balance'] >= $apply['apply_money']){

                //将提现金额加入冻结金额里
                $acc_edit['account_balance'] = $account['account_balance'] - $apply['apply_money'];
                $acc_edit['freeze_money'] = $account['freeze_money'] + $apply['apply_money'];
                $acc_edit['modify_user_id'] = D('SysUser')->self_id();
                $acc_edit['modify_date'] = date('Y-m-d H:i:s',time());
                $accountedit = M("proxy_account")->where(array('account_id'=>$account['account_id']))->save($acc_edit);
                //修改申请信息
                $edit['approve_status'] = 2;
                $edit['is_play_money'] = 0;
                $edit['modify_user_id'] = D('SysUser')->self_id();
                $edit['modify_date'] = date('Y-m-d H:i:s',time());
                $applyedit = $model->where(array('apply_id'=>$apply['apply_id']))->save($edit);
                if($accountedit && $applyedit){
                    //添加提现流水记录
                    if($this->proxy_account_records($apply)){
                        $model->commit();
                        $msg = '提现申请单提交成功！';
                        $status = 'success';
                        $n_msg='提交审核成功';
                    }else{
                        $model->rollback();
                        $msg = '提现申请单提交失败！';
                        $n_msg='提交审核失败';
                    }
                }else{
                    $model->rollback();
                    $msg = '提现申请单提交失败！';
                    $n_msg='提交审核失败';
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$id."】，代理商提现申请单【".$apply['apply_code']."】".$n_msg;
                $this->sys_log('代理商提现申请单提交审核',$note);
            }else{
                $msg = '对不起，账户余额不足！';
            }
        }else{
            $msg = '数据读取错误！';
        }
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }
    /*代理商提现功能*/
    public function insert(){
        if($_GET['operation']=="giveapply"){
            $msg = '系统错误!';
            $status = 'error';
            $id = I("id");
            //读取当前申请的金额
            $where['proxy_id'] = D('SysUser')->self_proxy_id();//获取自身的代理商ID
            $where['apply_id'] = $id;
            $apply = M("proxy_withdraw_apply")->where($where)->find();
            if($apply){
                //读取当前代理商的账户信息
                $account = M("proxy_account")->where(array('proxy_id'=>$where['proxy_id']))->find();
                if($account['account_balance'] >= $apply['apply_money']){
                    $model=M('proxy_withdraw_apply');
                    $model->startTrans();
                    //将提现金额加入冻结金额里
                    $acc_edit['account_balance'] = $account['account_balance'] - $apply['apply_money'];
                    $acc_edit['freeze_money'] = $account['freeze_money'] + $apply['apply_money'];
                    $acc_edit['modify_user_id'] = D('SysUser')->self_id();
                    $acc_edit['modify_date'] = date('Y-m-d H:i:s',time());
                    $accountedit = M("proxy_account")->lock(true)->where(array('account_id'=>$account['account_id']))->save($acc_edit);
                    //修改申请信息
                    $edit['approve_status'] = 2;
                    $edit['is_play_money'] = 0;
                    $edit['modify_user_id'] = D('SysUser')->self_id();
                    $edit['modify_date'] = date('Y-m-d H:i:s',time());
                    $applyedit = $model->where(array('apply_id'=>$apply['apply_id']))->save($edit);
                    if($accountedit && $applyedit){
                        //添加提现流水记录
                        if($this->proxy_account_records($apply)){
                            $model->commit();
                            $msg = '提现申请单提交成功！';
                            $status = 'success';
                            $n_msg='提交审核成功';
                        }else{
                            $model->rollback();
                            $msg = '提现申请单提交失败！';
                            $n_msg='提交审核失败';
                        }
                    }else{
                        $model->rollback();
                        $msg = '提现申请单提交失败！';
                        $n_msg='提交审核失败';
                    }
                    $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$id."】，代理商提现申请单【".$apply['apply_code']."】".$n_msg;
                    $this->sys_log('代理商提现申请单提交审核',$note);
                }else{
                    $msg = '对不起，账户余额不足！';
                }
            }else{
                $msg = '数据读取错误！';
            }

            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }else{
            $msg = '系统错误!';
            $status = 'error';
            $post = I("post.");
            
            if($post['apply_money']==''){
                $msg = '请输入提现金额！';
            }elseif(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $post['apply_money'])){
                $msg = '提现金额请输入数字！';
            }elseif($post['apply_money']<=0){
                $msg = '提现金额需大于零！';
            }elseif($post['bank_account']==''){
                $msg = '请输入开户行！';
            }elseif($post['account_opening']==''){
                $msg = '请输入开户省市！';
            }elseif($post['card_number']==''){
                $msg = '请输入银行卡号！';
            }elseif($post['beneficiary_name']==''){
                $msg = '请输入开户人姓名！';
            }elseif($post['mobile']==''){
                $msg = '请输入开户人电话！';
            }else{
                //获取当前代理商ID号
                $map['proxy_id']=D('SysUser')->self_proxy_id();
                $result=M("proxy_account")->where($map)->field("account_id,account_balance,freeze_money")->find();
                $money = $result['account_balance'] - $post['apply_money'];
                if($money<0){
                    $msg = "对不起，账户余额不足".$post['apply_money']."元！";
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));  
                }
                //读取代理商编号
                $number=M('proxy')->where($map)->field('proxy_code,proxy_name,sale_id')->find();

                //获取上级代理商ID
                $ma['proxy_id']=D("SysUser")->self_proxy_id();
                $up_proxy_id= M('Proxy')->where($ma)->find();
                //$up_proxy_id=D('SysUser')->up_proxy_info();
                $add['proxy_id'] = $map['proxy_id'];
                
                $apply_code="TXSQD".date('Ymd',time());
                $app['apply_code']=array('like',$apply_code.'%');
                $applys=M('proxy_withdraw_apply')->where($app)->order("apply_code desc")->find();
                $applys=substr($applys['apply_code'],13);
                $applys=$applys+1;
                $add['apply_code']=generate_order($applys,2);
            
                $add['apply_money'] = $post['apply_money'];
                $add['bank_account'] = $post['bank_account'];
                $add['account_opening'] = $post['account_opening'];
                $add['card_number'] = $post['card_number'];
                $add['beneficiary_name'] = $post['beneficiary_name'];
                $add['mobile'] = $post['mobile'];
                $add['transaction_date'] = time();
                $add['remark'] = $post['remark'];
                $add['top_proxy_id'] = $up_proxy_id['top_proxy_id'];
                $add['approve_status'] = 1;
                $add['create_user_id'] = D('SysUser')->self_id();
                $add['create_date'] = date('Y-m-d H:i:s',time());
                $add['modify_user_id'] = D('SysUser')->self_id();
                $add['modify_date'] = date('Y-m-d H:i:s',time());
                //执行添加
                if($id = M('proxy_withdraw_apply')->add($add)){
                    $set_add['proxy_id'] = D('SysUser')->self_proxy_id();
                    $set_add['bank_account'] = $post['bank_account'];
                    $set_add['account_opening'] = $post['account_opening'];
                    $set_add['card_number'] = $post['card_number'];
                    $set_add['beneficiary_name'] = $post['beneficiary_name'];
                    $set_add['mobile'] = $post['mobile'];
                    D("Proxy")->get_proxy_set($set_add);
                    $msg = '新增提现申请单成功';
                    $n_msg='成功';
                    $status = 'success';
                    $info = $id;
                    $remind_content='代理商【'.$number['proxy_name'].'】已提交提现申请单，请进行初审！';
                    R('ObjectRemind/send_user',array(8,$remind_content,array($number['sale_id'])));
                }else{
                    $msg = '新增提现申请单失败';
                    $n_msg='失败';
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$id."】，新增提现申请单，代理商【".$number['proxy_name']."(".$number['proxy_code'].")】，申请编号【".$add['apply_code']."】，提现金额【".money_format2($add['apply_money'])."】元".$n_msg;
                $this->sys_log('代理商新增提现申请单',$note);
            }
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'info'=>$info));
        }
    }
    /**
     * 提现时添加代理商提现流水信息
     */
    private function proxy_account_records($apply){
        //读取代理商信息
        $account = M("proxy_account")->where(array('proxy_id'=>$apply['proxy_id']))->find();
        
        $record['operater_before_balance']   = $account['account_balance']+$apply['apply_money'];  //操作前金额
        $record['operater_after_balance']    = $account['account_balance']; //操作后金额
        $record['operater_price']            = $apply['apply_money'];  //提现金额
        $record['operate_type']              = 3; //提现
        $record['balance_type']              = 2;//支出
        $record['record_date']               = date('Y-m-d H:i:s',time());
        $record['user_id']                   = D('SysUser')->self_id();
        $record['operation_date']            = date('Y-m-d H:i:s',time());
        $record['user_type']                 = 1;
        $record['proxy_id']                  = $apply['proxy_id'];
        $record['obj_user_type']             = 1;
        $record['obj_proxy_id']              = $apply['top_proxy_id'];
        
        $recordResult=M('account_record')->add($record);
        if($recordResult){
            return true;
        }else{
            return false;
        }
    }
    
    public function edit(){
        $apply_id = I("apply_id");
        $proxyapply = D('proxy_withdraw_apply')->where(array('apply_id'=>$apply_id))->find();
        //当菜单不存在时
        if($proxyapply){
            $this->assign('proxyapply',$proxyapply);
            $this->display();
        }else{
            $this->error('数据不存在！');
        }    
    }
    
    public function update(){
        $msg = '系统错误!';
        $status = 'error';
        $post = I("post.");
        
        if($post['apply_money']==''){
            $msg = '请输入提现金额！';
        }elseif(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $post['apply_money'])){
            $msg = '提现金额请输入数字！';
        }elseif($post['apply_money']<=0){
            $msg = '提现金额需大于零！';
        }elseif($post['bank_account']==''){
            $msg = '请输入开户行！';
        }elseif($post['account_opening']==''){
            $msg = '请输入开户省市！';
        }elseif($post['card_number']==''){
            $msg = '请输入银行卡号！';
        }elseif($post['beneficiary_name']==''){
            $msg = '请输入开户人姓名！';
        }elseif($post['mobile']==''){
            $msg = '请输入开户人电话！';
        }else{
            $proxy_id=D('SysUser')->self_proxy_id();
            $map['proxy_id']=$proxy_id;
            $map['apply_id']=$post['apply_id'];
            $proxy_apply = M("proxy_withdraw_apply")->where($map)->find();
            if($proxy_apply){
                //获取当前代理商ID号
                M("proxy_account")->startTrans();
                $map['proxy_id']=$proxy_id;
                $result=M("proxy_account")->lock(true)->where($map)->field("account_id,account_balance,freeze_money")->find();
                $money = $result['account_balance'] - $post['apply_money'];
                if($money<0){
                    M("proxy_account")->rollback();
                    $msg = "对不起，您的账户余额不足".$post['apply_money']."元！";
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));  
                }
                $edit['apply_money'] = $post['apply_money'];
                $edit['bank_account'] = $post['bank_account'];
                $edit['account_opening'] = $post['account_opening'];
                $edit['card_number'] = $post['card_number'];
                $edit['beneficiary_name'] = $post['beneficiary_name'];
                $edit['mobile'] = $post['mobile'];
                $edit['remark'] = $post['remark'];
                $edit['modify_user_id'] = D('SysUser')->self_id();
                $edit['modify_date'] = date('Y-m-d H:i:s',time());
                if($proxy_apply['approve_status']!=1){
                    $edit['approve_status'] = 1;
                    $edit['is_play_money']=0;
                    $edit['last_approve_date'] =null;
                }
                //执行修改
                if(M('proxy_withdraw_apply')->where(array('apply_id'=>$proxy_apply['apply_id']))->save($edit)){
                    $set_add['proxy_id'] = D('SysUser')->self_proxy_id();
                    $set_add['bank_account'] = $post['bank_account'];
                    $set_add['account_opening'] = $post['account_opening'];
                    $set_add['card_number'] = $post['card_number'];
                    $set_add['beneficiary_name'] = $post['beneficiary_name'];
                    $set_add['mobile'] = $post['mobile'];
                    D("Proxy")->get_proxy_set($set_add);
                    $msg = '编辑提现申请信息成功';
                    $status = 'success';
                    M("proxy_account")->commit();
                    $n_msg='成功';
                }else{
                    $msg = '编辑提现申请信息失败！';
                    M("proxy_account")->rollback();
                    $n_msg='失败';
                }
                $c_item='';
                $c_item.=$post['bank_account']===$proxy_apply['bank_account']?'':'，开户行【'.$post['bank_account'].'】';
                $fg=!empty($c_item)?'，':'';
                $c_item.=$post['account_opening']===$proxy_apply['account_opening']?'':$fg.'开户省市【'.$post['account_opening'].'】';
                $fg=!empty($c_item)?'，':'';
                $c_item.=$post['card_number']===$proxy_apply['card_number']?'':$fg.'银行卡号【'.$post['card_number'].'】';
                $fg=!empty($c_item)?'，':'';
                $c_item.=$post['beneficiary_name']===$proxy_apply['beneficiary_name']?'':$fg.'收款人姓名【'.$post['beneficiary_name'].'】';
                $fg=!empty($c_item)?'，':'';
                $c_item.=$post['mobile']===$proxy_apply['mobile']?'':$fg.'手机号码【'.$post['mobile'].'】';
                $fg=!empty($c_item)?'，':'';
                $c_item.=$post['apply_money']*100===$proxy_apply['apply_money']*100?'':$fg.'提现金额【'.$post['apply_money'].'】元';

                $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$post['apply_id']."】，编辑代理商提现申请单，代理商【".obj_name($proxy_id,1)."】，申请编号【".$proxy_apply['apply_code']."】".$c_item.$n_msg;
                $this->sys_log('编辑代理商提现申请单',$note);

            }else{
                $msg = '数据读取失败！';
                $n_msg='失败';
            }

        }
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }
    
    public function delete(){
        $msg = '系统错误！';
        $status = 'error';
        $apply_id = I('apply_id');
        $map['proxy_id']=D('SysUser')->self_proxy_id();
        $map['apply_id']=$apply_id;
        $proxy_apply = M("proxy_withdraw_apply")->where($map)->find();
        if(in_array($proxy_apply['approve_status'],array(3,5,8))){
            $this->ajaxReturn(array('msg'=>'已经审核通过，请勿删除！','status'=>$status));
        }
        if($proxy_apply){
            $delete['apply_id'] = $proxy_apply['apply_id'];
            if(M("proxy_withdraw_apply")->where($delete)->delete()){
                M("proxy_withdraw_process")->where(array('apply_id'=>$proxy_apply['apply_id']))->delete();
                $msg = '删除提现申请单成功！';
                $status = 'success';
                $n_msg='成功';
            }else{
                $msg = '删除提现申请单失败！';
                $n_msg='失败';
            }
        }else{
            $msg = '数据读取错误！';
            $n_msg='失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，删除代理商提现申请单【".$proxy_apply['apply_code']."】".$n_msg;
        $this->sys_log('删除代理商提现申请单',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }
    
    /*查看申请详细界面*/
    public function show(){
        $apply_id = I('get.apply_id');
    	$where['pa.apply_id'] = $apply_id;
    	$proxyw= M('proxy_withdraw_apply as pa')->join("t_flow_proxy as p on p.proxy_id=pa.proxy_id","left")->where($where)->field("pa.*,proxy_name,proxy_code")->find();
    	if($proxyw){
            $process = M("proxy_withdraw_process")->where(array('apply_id'=>$proxyw['apply_id']))->select();
            if(!$process){
                $process = "";
            }
    		$this->assign("proxyw",$proxyw);
            $this->assign("process",$process);
    		$this->display('detailed');
    	}else{
    		$this->error("审核申请单不存在");
    	}
    }
    
    /*弹出审核界面*/
    public function detailed(){
        $msg = '系统错误！';
        $status = 'error';
    	$where['pa.apply_id'] = array('eq',trim(I('get.apply_id')));
    	$proxyw= M('proxy_withdraw_apply as pa')->join("t_flow_proxy as p on p.proxy_id=pa.proxy_id","left")->where($where)->field("pa.*,proxy_name,proxy_code")->find();
    	if($proxyw){
            if(trim(I('approve_f'))=='proxy_approve_c'){
                if(in_array($proxyw['approve_status'],array(3,4,5,6,7,8))){
                    $this->ajaxReturn(array('msg'=>'不可重复审核！','status'=>$status));
                }
            }else if(trim(I('approve_f'))=='proxy_approve'){
                if($proxyw['approve_status']<2){
                    $this->ajaxReturn(array('msg'=>'请等待初审完成！','status'=>$status));
                }
                if($proxyw['approve_status']==4){
                    $this->ajaxReturn(array('msg'=>'初审驳回,不可复审！','status'=>$status));
                }
                if(in_array($proxyw['approve_status'],array(5,6,7,8))){
                    $this->ajaxReturn(array('msg'=>'不可重复审核！','status'=>$status));
                }
            }else{
                if($proxyw['approve_status']<2){
                    $this->ajaxReturn(array('msg'=>'请等待初审完成！','status'=>$status));
                }
                if($proxyw['approve_status']==4){
                    $this->ajaxReturn(array('msg'=>'初审驳回,不可审核！','status'=>$status));
                }
                if($proxyw['approve_status']==6){
                    $this->ajaxReturn(array('msg'=>'复审驳回,不可审核！','status'=>$status));
                }
                if($proxyw['approve_status']==7 || $proxyw['approve_status']==8){
                    $this->ajaxReturn(array('msg'=>'不可重复审核！','status'=>$status));
                }
            }
    		$this->assign("proxyw",$proxyw);
            $this->assign('display',I('approve_f'));
    		$this->display("approve");
    	}else{
    		$this->error("数据读取不存在");
    	}
    }

    /**
     * 初审
     */
    public function proxy_approve_c(){
        $msg = '系统错误';
        $status = 'error';
        $apply_id=trim(I('apply_id'));
        $approve_status=trim(I('approve_status'));
        $approve_remark=trim(I('approve_remark'));
        
        $model=M('proxy_withdraw_apply');
        $model->startTrans();
        if($approve_status==2 && $approve_remark==""){
            $this->ajaxReturn(array('msg'=>'请输入审核驳回原因！','status'=>$status));
        }
        //读取申请信息
        $apply = $model->where(array('apply_id'=>$apply_id))->find();
        if(in_array($apply['approve_status'],array(3,4,5,6,7,8))){
            $this->ajaxReturn(array('msg'=>'不可重复审核！','status'=>$status));
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
        $process = M("proxy_withdraw_process")->add($add);
        
        if($apply_res && $process){
            $thisreturn_money = true;
            if($approve_status==2){
                //判断是否将冻结金额返回到余额中
                if(!$this->return_money($apply)){
                    $thisreturn_money = false;
                }
            }
            if($thisreturn_money){
                $model->commit();
                $msg = $approve_status==2?'提现申请单初审驳回成功！':'提现申请单初审成功！';
                $status = 'success';
                $r_msg =$approve_status==2?'初审驳回':'初审成功';
                $n_msg=$approve_status==2?'初审驳回成功':'初审成功';
                $info=$this->proxy_info($apply_id);
                $remind_content='代理商【'.$info['proxy_name'].'】提现申请单【'.$info['apply_code'].'】已经【'.$r_msg.'】，请知晓！';
                R('ObjectRemind/send_user',array(10,$remind_content,array($info['sale_id'],$info['user_id'])));
                if($approve_status==1){
                    $info=$this->proxy_info($apply_id);
                    $remind_content='代理商【'.$info['proxy_name'].'】提现申请单已初审通过，请进行复审！';
                    R('ObjectRemind/send_user',array(9,$remind_content));
                }
            }else{
                $model->rollback();
                $msg = $approve_status==2?'提现申请单初审驳回失败！':'提现申请单初审失败！';
                $n_msg=$approve_status==2?'初审驳回失败':'初审失败';
            }
        }else{
            $model->rollback();
            $msg = $approve_status==2?'提现申请单初审驳回失败！':'提现申请单初审失败！';
            $n_msg=$approve_status==2?'初审驳回失败':'初审失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，审核代理商提现申请单【".$apply['apply_code']."】".$n_msg;
        $this->sys_log('代理商提现申请单初审',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }
    
    /**
     * 复审
     */
    public function proxy_approve(){
        $msg = '系统错误';
        $status = 'error';
        $apply_id=trim(I('apply_id'));
        $approve_status=trim(I('approve_status'));
        $approve_remark=trim(I('approve_remark'));
        
        $model=M('proxy_withdraw_apply');
        $model->startTrans();
        //读取申请信息
        $apply = $model->where(array('apply_id'=>$apply_id))->find();
        if($apply['approve_status']==2){
            $this->ajaxReturn(array('msg'=>'请等待初审完成！','status'=>$status));
        }
        if($apply['approve_status']==4){
            $this->ajaxReturn(array('msg'=>'初审驳回,不可复审！','status'=>$status));
        }
        if($approve_status==2 && $approve_remark==""){
            $this->ajaxReturn(array('msg'=>'请填写审核驳回原因！','status'=>$status));
        }
        if(in_array($apply['approve_status'],array(5,6,7,8))){
            $this->ajaxReturn(array('msg'=>'不可重复审核！','status'=>$status));
        }
        
        //修改申请表信息
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
        $process = M("proxy_withdraw_process")->add($add);
        
        if($apply_res && $process){
            $thisreturn_money = true;
            if($approve_status==2){
                //判断是否将冻结金额返回到余额中
                if(!$this->return_money($apply)){
                    $thisreturn_money = false;
                }
            }
            if($thisreturn_money){
                $model->commit();
                $msg =$approve_status==2?'提现申请单复审驳回成功！':'提现申请单复审成功！';
                $status = 'success';
                $r_msg =$approve_status==2?'复审驳回':'复审成功';
                $n_msg=$approve_status==2?'复审驳回成功':'复审成功';
                $info=$this->proxy_info($apply_id);
                $remind_content='代理商【'.$info['proxy_name'].'】提现申请单【'.$info['apply_code'].'】已经【'.$r_msg.'】，请知晓！';
                R('ObjectRemind/send_user',array(10,$remind_content,array($info['sale_id'],$info['user_id'])));
                if($approve_status==1){
                    $remind_content='代理商【'.$info['proxy_name'].'】提现申请单【'.$info['apply_code'].'】已复审通过，请进行打款！';
                    R('ObjectRemind/send_user',array(11,$remind_content));
                }
            }else{
                $model->rollback();
                $msg = $approve_status==2?'提现申请单复审驳回失败！':'提现申请单复审失败！';
                $n_msg=$approve_status==2?'复审驳回失败':'复审失败';
            }
        }else{
            $model->rollback();
            $msg = $approve_status==2?'提现申请单复审驳回失败！':'提现申请单复审失败！';
            $n_msg=$approve_status==2?'复审驳回失败':'复审失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，审核代理商提现申请单【".$apply['apply_code']."】".$n_msg;
        $this->sys_log('代理商提现申请单复审',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    /**
     * 打款
     */
    public function proxy_remittance(){
        $msg = '系统错误';
        $status = 'error';
        $apply_id=trim(I('apply_id'));
        $approve_status=trim(I('approve_status'));
        $approve_remark=trim(I('approve_remark'));
        $transaction_number=trim(I('transaction_number'));
        $payment_account=trim(I('payment_account'));
        $payment_money=trim(I('payment_money'));
        $payment_bank=trim(I('payment_bank'));

        $model=M('proxy_withdraw_apply');
        $model->startTrans();
        $apply = $model->where(array('apply_id'=>$apply_id))->find();
        if($apply['approve_status']<2){
            $this->ajaxReturn(array('msg'=>'请等待初审完成！','status'=>$status));
        }
        if($apply['approve_status']==4){
            $this->ajaxReturn(array('msg'=>'初审驳回,不可审核！','status'=>$status));
        }
        if($apply['approve_status']==6){
            $this->ajaxReturn(array('msg'=>'复审驳回,不可审核！','status'=>$status));
        }
        if($apply['is_play_money']==1 || $apply['is_play_money']==2){
            $this->ajaxReturn(array('msg'=>'不可重复审核！','status'=>$status));
        }
        if($approve_status==2 && $approve_remark==""){
            $this->ajaxReturn(array('msg'=>'请输入审核驳回原因！','status'=>$status));
        }
        if($approve_status==1 && $transaction_number==""){
            $this->ajaxReturn(array('msg'=>'请输入交易号!','status'=>$status));
        }
        if($approve_status==1 && $payment_account==""){
            $this->ajaxReturn(array('msg'=>'请输入打款账户','status'=>$status));
        }
        if($approve_status==1 && $payment_money==""){
            $this->ajaxReturn(array('msg'=>'请输入打款金额!','status'=>$status));
        }
        //读取申请信息
        if($approve_status==1){
          if(I('get.tran')){
            $da['apply_id']=$apply_id;
            $da['approve_status']=$approve_status;
            $da['approve_remark']=$approve_remark;
            $da['transaction_number']=$transaction_number;
            $da['payment_account']=$payment_account;
            $da['payment_money']=$payment_money;
            $da['payment_bank']=$payment_bank;
            //sql条件
            $ta['ea.apply_id']=$apply_id;;
            $result=M('proxy_withdraw_apply as ea')
            ->join('t_flow_proxy as p on p.proxy_id=ea.proxy_id','left')
            ->field('ea.apply_money,p.proxy_name')
            ->where($ta)
            ->find();
            //$msg="确定是否向【".$result['proxy_name']."】提现".$result['payment_money']."元？";
            //$msg="代理商【".$result['proxy_name']."】申请提现".$payment_money."元，确定已经向该代理商打款".$payment_money."元吗？";
              $msg="代理商【".$result['proxy_name']."】申请提现".$result['apply_money']."元，确定已经向该代理商打款".$payment_money."元吗？";
            $this->ajaxReturn(array('msg'=>$msg,'status'=>'success','info'=>$da));
          }
        }
        //修改申请表信息
        $edit['apply_id'] = $apply_id;
        $edit['is_play_money'] = $approve_status==2?"2":"1";
        $edit['transaction_number'] = $transaction_number;
        //$edit['approve_status']=$approve_status==2?"8":"7";
        $edit['payment_account'] = $payment_account;
        $edit['payment_money'] = $payment_money;
        $edit['payment_bank'] =$payment_bank;
        $edit['payment_date'] = date("Y-m-d H:i:s",time());
        $edit['last_approve_date'] = date("Y-m-d H:i:s",time());
        $apply_res=$model->where(array('apply_id'=>$apply_id))->save($edit);
        //添加审核信息
        $add['apply_id'] = $apply_id;
        $add['approve_status'] = $approve_status==2?"2":"1";
        $add['approve_remark'] = $approve_remark;
        $add['approve_date']=date('Y-m-d H:i:s',time());
        $add['approve_user_id']=D('SysUser')->self_id();
        $add['approve_stage']=3;
        $process = M("proxy_withdraw_process")->add($add);
        $info=$this->proxy_info($apply_id);
        if($apply_res && $process){
            if($approve_status==2){
                //将冻结返还到余额
                if($this->return_money($apply)){
                    $model->commit();
                    $msg ='打款驳回成功！';
                    $status = 'success';
                    $n_msg='打款驳回成功';
                }else{
                    $model->rollback();
                    $msg ='打款驳回失败！';
                    $n_msg='打款驳回失败';
                }
            }else{
                if($this->reduce_money($apply)){
                    if($this->account_records($apply)){
                        $model->commit();
                        $msg ='打款成功！';
                        $status = 'success';
                        $success_msg="，并已经打款【".$payment_money."】元，打款交易号【".$transaction_number."】";
                        $n_msg='打款成功';
                    }else{
                        $model->rollback();
                        $msg ='打款失败！';
                        $n_msg='打款失败';
                    }
                }else{
                    $model->rollback();
                    $msg ='打款失败！';
                    $n_msg='打款失败';
                }
            }

            $r_msg =$approve_status==2?'打款驳回':'打款成功';
            //$n_msg=$approve_status==2?'打款驳回成功':'打款成功';

            $remind_content='代理商【'.$info['proxy_name'].'】提现申请单【'.$info['apply_code'].'】已【'.$r_msg.'】';
            R('ObjectRemind/send_user',array(12,$remind_content,array($info['sale_id'],$info['user_id'])));

        }else{
            $model->rollback();
            $msgs = $approve_status==2?'打款驳回失败！':'打款失败！';
            $msg =$msgs;
            $n_msg=$approve_status==2?'打款驳回失败':'打款失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，代理商【".$info['proxy_name']."(".$info['proxy_code'].")】的提现申请单【".$apply['apply_code']."】".$n_msg.$success_msg;
        $this->sys_log('代理商提现申请单打款',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status)); 
    }
    
    /**
     * 将冻结金额返回到余额中
     */
    private function return_money($apply){
        //读取当前申请提现的金额
        //$apply = M('proxy_withdraw_apply')->where(array('apply_id'=>$apply_id))->find();
        //读取代理商账户信息
        $account = M("proxy_account")->where(array('proxy_id'=>$apply['proxy_id']))->find();
        //将冻结金额加入余额里
        $acc_edit['account_balance'] = $account['account_balance'] + $apply['apply_money'];
        $acc_edit['freeze_money'] = $account['freeze_money'] - $apply['apply_money'];
        $acc_edit['modify_user_id'] = D('SysUser')->self_id();
        $acc_edit['modify_date'] = date('Y-m-d H:i:s',time());
        $accountedit = M("proxy_account")->where(array('account_id'=>$account['account_id']))->save($acc_edit);
        if($accountedit){
            //添加流水信息
            $record['operater_before_balance']   = $account['account_balance'];  //操作前金额
            $record['operater_after_balance']    = $account['account_balance']+$apply['apply_money']; //操作后金额
            $record['operater_price']            = $apply['apply_money'];  //返还金额
            $record['operate_type']              = 5; //返还
            $record['balance_type']              = 1;//收入
            $record['record_date']               = date('Y-m-d H:i:s',time());
            $record['user_id']                   = D('SysUser')->self_id();
            $record['operation_date']            = date('Y-m-d H:i:s',time());
            $record['user_type']                 = 1;
            $record['proxy_id']                  = $apply['proxy_id'];
            $record['obj_user_type']             = 1;
            $record['obj_proxy_id']              = $apply['top_proxy_id'];
            $record['device_name']               = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));
            $recordResult=M('account_record')->add($record);
            if($recordResult){
                return true;
            }else{
                return false;
            }
        }
        return false;
    }
    
    /**
     * 打款成功减去冻结金额
     */
    private function reduce_money($apply){
        //读取当前申请提现的金额
        //$apply = M('proxy_withdraw_apply')->where(array('apply_id'=>$apply_id))->find();
        //读取代理商账户信息
        $account = M("proxy_account")->lock(true)->where(array('proxy_id'=>$apply['proxy_id']))->find();
        //减少冻结金额中提现的金额
        $acc_edit['freeze_money'] = $account['freeze_money'] - $apply['apply_money'];
        $acc_edit['modify_user_id'] = D('SysUser')->self_id();
        $acc_edit['modify_date'] = date('Y-m-d H:i:s',time());
        $accountedit = M("proxy_account")->where(array('account_id'=>$account['account_id']))->save($acc_edit);
        if($accountedit){
            return true;
        }
        return false;
    }
    
    /**
     * 记录账户流水
     */
    private function account_records($apply){
        //读取当前申请提现的数据
        //$apply = M('proxy_withdraw_apply')->where(array('apply_id'=>$apply_id))->find();
        //读取代理商账户信息
        //$account = M("proxy_account")->where(array('proxy_id'=>$apply['proxy_id']))->find();
        //读取上级代理商账户信息
        $account_top = M("proxy_account")->where(array('proxy_id'=>$apply['top_proxy_id']))->find();
        
        if(D('SysUser')->self_user_type()==1){
            $record[0]['operater_before_balance']=0;  //操作前金额
            $record[0]['operater_after_balance']=0; //操作后金额
        }else{
            $record[0]['operater_before_balance']= $account_top['account_balance'];  //操作前金额
            $record[0]['operater_after_balance']=$account_top['account_balance']+$apply['apply_money']; //操作后金额
        }
        $record[0]['operater_price']    = $apply['apply_money'];  //收回金额
        $record[0]['operate_type']      = 5; //返还
        $record[0]['balance_type']      = 1;//收入
        $record[0]['record_date']       = date('Y-m-d H:i:s',time());
        $record[0]['user_id']           = D('SysUser')->self_id();
        $record[0]['operation_date']    = date('Y-m-d H:i:s',time());
        $record[0]['user_type']         = 1;
        $record[0]['proxy_id']          = $apply['top_proxy_id'];
        $record[0]['obj_user_type']     = 1;
        $record[0]['obj_proxy_id']      = $apply['proxy_id'];
        $record[0]['device_name']       = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));
        /*下级代理商为收入*/
        /*
        $record[1]['operater_before_balance']   = $account['account_balance']+$apply['apply_money'];  //操作前金额
        $record[1]['operater_after_balance']    = $account['account_balance']; //操作后金额
        $record[1]['operater_price']            = $apply['apply_money'];  //提现金额
        $record[1]['operate_type']              = 3; //提现
        $record[1]['balance_type']              = 2;//支出
        $record[1]['record_date']               = date('Y-m-d H:i:s',time());
        $record[1]['user_id']                   = D('SysUser')->self_id();
        $record[1]['operation_date']            = date('Y-m-d H:i:s',time());
        $record[1]['user_type']                 = 1;
        $record[1]['proxy_id']                  = $apply['proxy_id'];
        $record[1]['obj_user_type']             = 1;
        $record[1]['obj_proxy_id']              = $apply['top_proxy_id'];
        */
        //添加流水记录
        $recordResult=M('account_record')->addAll($record);
        if($recordResult){
            return true;
        }else{
            return false;
        }
    }


    function  proxy_info($apply_id){
        $info=M('proxy_withdraw_apply as pr')
            ->join('left join t_flow_proxy  as p on pr.proxy_id=p.proxy_id ')
            ->join('left join t_flow_sys_user  as u on p.proxy_id=u.proxy_id ')
            ->where('pr.apply_id='.$apply_id)
            ->field('pr.apply_code,p.sale_id,p.create_user_id,p.proxy_name,p.proxy_code,u.user_id')
            ->find();
        return $info;
    }

}