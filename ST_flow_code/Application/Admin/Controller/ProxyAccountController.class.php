<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class ProxyAccountController extends CommonController{
     /*代理商账户管理列表*/
     public function index(){
        D("SysUser")->sessionwriteclose();
        $user=D('SysUser')->self_user_type();//获取自身的用户类型是运营平台，代理商，企业
        $proxy_name = trim(I('get.proxy_name')); //代理商名称
        //$source = trim(I('get.source')); //来源
        //$apply_type = trim(I('get.apply_type'));  //操作方式
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        
        $proxy_code=trim(I('get.proxy_code'));
        $where=array();
        if($proxy_code){
            $where['p.proxy_code']=array('like','%'.$proxy_code.'%');
        }
        if($proxy_name){
            $where['p.proxy_name']=array('like','%'.$proxy_name.'%');
        }
    /*    if($start_datetime && $end_datetime){
            $where['pa.create_date'] = array('between',array(start_time($start_datetime),end_time($end_datetime)));
        }*/
         if($start_datetime or $end_datetime){
             if($start_datetime && $end_datetime){
                 $where['pa.create_date']=array('between',array(start_time($start_datetime),end_time($end_datetime)));
             }elseif($start_datetime){
                 $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                 $where['pa.create_date']= array('between',array(start_time($start_datetime),$end_datetime));
             }elseif($end_datetime){
                 $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                 $where['pa.create_date']=array('between',array($start_datetime,end_time($end_datetime)));
             }
         }

         $self_proxy_id =  D('SysUser')->self_proxy_id();
         $proxy_child_ids=D('Proxy')->proxy_child_ids();
         if($user==1){
             $where['p.proxy_id'] = array('in',$proxy_child_ids);
         }else{
             $where['p.proxy_id'] = array(array('in',$proxy_child_ids),array('eq',$self_proxy_id),'or');
         }
         $where['p.approve_status']=1; //审核通过
         $where['p.status']=1;

        $list=D('ProxyAccount')->proxyAccountList($where);
        $this->assign('usr_type',$user);
        $this->assign('list',$list['list']);
        $this->assign('page',$list['page']);
         $this->assign('sum_results',$list['sum_results']);
        $this->assign('is_admin',D('SysUser')->is_admin());
        $this->assign('is_proxy',$self_proxy_id);
        $this->display();        //模板*/
     }

    public function export_excel(){

        $user=D('SysUser')->self_user_type();//获取自身的用户类型是运营平台，代理商，企业
        $proxy_name = trim(I('get.proxy_name')); //代理商名称
        //$source = trim(I('get.source')); //来源
        //$apply_type = trim(I('get.apply_type'));  //操作方式
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间

        $proxy_code=trim(I('get.proxy_code'));
        $where=array();
        if($proxy_code){
            $where['p.proxy_code']=array('like','%'.$proxy_code.'%');
        }
        if($proxy_name){
            $where['p.proxy_name']=array('like','%'.$proxy_name.'%');
        }
        /*    if($start_datetime && $end_datetime){
                $where['pa.create_date'] = array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }*/
        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['pa.create_date']=array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['pa.create_date']= array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['pa.create_date']=array('between',array($start_datetime,end_time($end_datetime)));
            }
        }
        //$proxy_level = I("get.proxy_level");
      /*  if($user==1){
            if($proxy_level==""){
                $where['p.proxy_level']=1;
            }else{
                if($proxy_level==1){
                    $where['p.proxy_level']=1;
                }elseif($proxy_level==2){
                    $where['p.proxy_level']=array("gt",1);
                }
            }
        }*/
        $cache_credit = I("get.cache_credit");
        if($cache_credit){
            if($cache_credit==1){
                $where['pa.cache_credit']=array("gt",0);
            }else{
                $where['pa.cache_credit'] = array(array('eq','0.000'),array("exp", ' is NULL'), 'or');
            }
        }

        $proxy_level = I("get.proxy_level");
        if($proxy_level){
            if($proxy_level==1){
                $where['p.proxy_level']=1;
            }else{
                $where['p.proxy_level']=array("gt",1);
            }
        }
        $self_proxy_id =  D('SysUser')->self_proxy_id();
        $proxy_child_ids=D('Proxy')->proxy_child_ids();
        if($user==1){
            $where['p.proxy_id'] = array('in',$proxy_child_ids);
        }else{
            $where['p.proxy_id'] = array(array('in',$proxy_child_ids),array('eq',$self_proxy_id),'or');
        }

        /*$self_proxy_id =  D('SysUser')->self_proxy_id();
        if(D('SysUser')->is_top_proxy_admin()){
            $map['p.proxy_id'] = array('in',D('Proxy')->proxy_child_ids());
            $map['p.top_proxy_id'] = array('eq',$self_proxy_id);
            $map['_logic'] = 'and';
        }else{
            $stat['p.proxy_id'] = array('in',D('Proxy')->proxy_child_ids());
            $stat['p.top_proxy_id'] = array('eq',$self_proxy_id);
            $stat['_logic'] = 'and';
            $map['_complex'] = $stat;
            $map['_logic'] = 'or';
            $map['p.proxy_id'] = array('eq',$self_proxy_id);
        }
        $where['_complex'] = $map;*/

        $where['p.approve_status']=1; //审核通过
        $where['p.status']=1;
        $list=D('ProxyAccount')->export_excel($where);
        $title='代理商账户管理';
        if($user==1){
            $headArr=array("代理商编号","代理商名称","上级代理编号","上级代理名称","账户余额(元)","授信金额(元)","冻结金额(元)");//,"提醒额度(元)"
        }else{
            $headArr=array("代理商编号","代理商名称","上级代理编号","上级代理名称","账户余额(元)","冻结金额(元)");
        }

        ExportEexcel($title,$headArr,$list);
    }
	/*
	 *代理商账户添加页面
	 */
	/*public function add(){
        $where['p.top_proxy_id']=D('SysUser')->self_proxy_id();
        $this->assign('list',D('ProxyAccount')->allProxy($where));
        $this->display();        //模板
    }*/

    /*代理商充值功能*/
   /* public function insert(){
        $msg = '系统错误!';
        $status = 'error';
        $data = array();
        $map['proxy_id']=trim(I('proxy_id'));
        $find_account=M('proxy_account')->where($map)->find();
        if(I('proxy_id')==0){
            $msg = '请选择需要创建账户的代理商！';
        }else if($find_account){
            $msg = '改代理商已经有账户请不要重复添加！';
        }else{
            $data['proxy_id']=trim(I('proxy_id'));
            $data['create_date']=date('Y-m-d H:i:s',time());
            $data['modify_date']=date('Y-m-d H:i:s',time());
            //执行添加
            if(M('proxy_account')->add($data)){
                $msg = '新增代理商账户成功！';
                $status = 'success';
            }else{
                $msg = '新增代理商账户失败！';
            }
        }
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }*/


    /*代理商账户详细界面*/
    public function  show(){
        $msg = '系统错误！';
        $status = 'error';
        $operate=trim(I('operate'));
        $list=D('ProxyAccount')->proxy_account_detailed();
        $this->assign("type",D("SysUser")->self_user_type());
        $this->assign($list);
        if($operate=='approve'){
            $this->display('approve');
        }else{
            $this->display('detailed');
        }
    }
    /*划拨*/
    public function transfer(){
        $msg = '系统错误';
        $status = 'error';
        $operate=trim(I('operate'));
        $tran=trim(I('get.tran'));
        if($tran){
            $da['proxy_id']=trim(I("proxy_id"));
            $da['account_id']=trim(I("account_id"));
            $da['proxy_name']=trim(I("proxy_name"));
            $da['apply_money']=trim(I("apply_money"));
            $da['remark']=trim(I("remark"));
            $where['proxy_id']=D('SysUser')->self_proxy_id();
            $proxy=M("proxy")->where($where)->field("proxy_name")->find();
            if($da['apply_money']==""){
                $this->ajaxReturn(array('msg'=>'请输入要充值的金额！','status'=>$status));
            }
            if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $da['apply_money'])){
                $this->ajaxReturn(array('msg'=>'充值金额请输入数字！','status'=>$status));
            }
            if(!($da['apply_money']>0)){
                $this->ajaxReturn(array('msg'=>'充值金额不能小于等于0！','status'=>$status));
            }
            $title='';
            if(trim(I('test_models'))==1){
                $title='测试款';
            }
            $data['test_models']=trim(I('test_models'));
            $msg="确定是否向代理商【".$da['proxy_name']."】账户充值".$title.$da['apply_money']."元？";
            $da['test_models']=trim(I('test_models'));
            $this->ajaxReturn(array('msg'=>$msg,"status"=>'success',"info"=>$da));
        }
        if($operate=='show'){
            $list=D('ProxyAccount')->account_detailed();
            $proxy=D('ProxyAccount')->account_detailed_self();
            $this->assign("proxy",$proxy);
            $this->assign($list);
            $this->display();
        }else{
            $apply_money=trim(I('apply_money'));
            $proxy_id = trim(I('proxy_id'));
            $proxy_name = trim(I('proxy_name'));
            $test_models=trim(I('test_models'));
            $title=$test_models==1?'测试款':'正常充值';
            if($apply_money==""){
                $this->ajaxReturn(array('msg'=>'请输入要充值的金额！','status'=>$status));
            }elseif(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $apply_money)){
                $this->ajaxReturn(array('msg'=>'充值金额请输入数字！','status'=>$status));
            }
            if(!($apply_money>0)){
                $this->ajaxReturn(array('msg'=>'充值金额不能小于等于0！','status'=>$status));
            }
            
            $model=M('proxy_account');
            $model->startTrans();
            //读取操作对象代理商信息
            $proxy_list = M("proxy as p")
            ->lock(true)
            ->join("left join t_flow_proxy_account as pa on pa.proxy_id=p.proxy_id")
            ->where(array('p.proxy_id'=>$proxy_id,'p.proxy_name'=>$proxy_name))
            ->field("pa.account_id,pa.account_balance")
            ->find();
            if(!$proxy_list){
                $model->rollback();
                $this->ajaxReturn(array('msg'=>'代理商账户信息读取失败！','status'=>$status));
            }
            //读取当前代理商信息
            $user=D('SysUser')->self_user_type();
            $where['proxy_id']=D('SysUser')->self_proxy_id();
            $Balance=$model->where($where)->find();
            if($Balance['account_balance']<$apply_money && $user!=1){
                $model->rollback();
                $this->ajaxReturn(array('msg'=>'您的账户余额不足，请充值后，再操作！','status'=>$status));
            }
            $condition['top_account_id']=$Balance['account_id']; //上级代理上账户id
            $condition['top_account_balance']=$Balance['account_balance']; //上级代理上账户余额
            $condition['top_operate_type']=4; //划拨-上级代理商
            $condition['top_balance_type']=2;//支出-上级代理商
            $condition['top_user_type']=1;
            $condition['apply_money']=$apply_money;   //需要操作的金额
            $condition['operate_account_id']=$proxy_list['account_id'];//收入-下级代理商
            $condition['operate_account_balance']=$proxy_list['account_balance'];//要操作的代理商账户余额
            $condition['remark']=trim(I("remark"));
            $condition['operate_proxy_id']=$proxy_id; //要操作的代理商账户ID
            $test_models=trim(I('test_models'));
            if($test_models==2){
                $condition['operate_operate_type']=2;//充值-下级代理商
            }else{
                $condition['operate_operate_type']=8; //充值-为测试款
            }
            $condition['operate_balance_type']=1;//收入-下级代理商
            $condition['operate_user_type']=1;
            if(D('ProxyAccount')->account_record($condition)>0){
                $model->commit();
                $msg = '资金充值成功！';
                $status = 'success';
                $n_msg='成功';
            }else{
                $msg ='资金充值失败！';
                $model->rollback();
                $n_msg='失败';
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，代理商账户资金充值， 代理商【'.obj_name($proxy_id,1).'】，充值类型【'.$title.'】，充值金额【'.money_format2($apply_money).'】元'.$n_msg;
            $this->sys_log('代理商账户资金充值',$note);
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }
    
    /**
     * 回收
     */
    public function return_money(){
        $tran=trim(I('get.tran'));
        if($tran){
            $status='error';
            $da['proxy_id']=trim(I("proxy_id"));
            $da['account_id']=trim(I("account_id"));
            $da['proxy_name']=trim(I("proxy_name"));
            $da['apply_money']=trim(I("apply_money"));
            $da['remark']=trim(I("remark"));
            $where['proxy_id']=D('SysUser')->self_proxy_id();
            $proxy=M("proxy")->where($where)->field("proxy_name")->find();
            if($da['apply_money']==""){
                $this->ajaxReturn(array('msg'=>'请输入要收回的金额！','status'=>$status));
            }
            if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $da['apply_money'])){
                $this->ajaxReturn(array('msg'=>'收回金额请输入数字！','status'=>$status));
            }
            if(!($da['apply_money']>0)){
                $this->ajaxReturn(array('msg'=>'收回金额不能小于等于0！','status'=>$status));
            }
            $msg="确定是否从代理商【".$da['proxy_name']."】账户收回".$da['apply_money']."元？";
            $this->ajaxReturn(array('msg'=>$msg,"status"=>'success',"info"=>$da));
        }
        if(isset($_GET['account_id']) && $_GET['account_id'] > 0){
            $list=D('ProxyAccount')->account_detailed();
            $proxy=D('ProxyAccount')->account_detailed_self();
            $this->assign("proxy",$proxy);
            $this->assign($list);
            $this->display();
        }else{
            $msg = '系统错误';
            $status = 'error';
            $account_id=trim(I('account_id'));
            $proxy_id=trim(I('proxy_id'));
            $proxy_name=trim(I('proxy_name'));
            $apply_money=trim(I('apply_money'));
            
            if($apply_money==""){
                $this->ajaxReturn(array('msg'=>'请输入要收回的金额！','status'=>$status));
            }
            if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $apply_money)){
                $this->ajaxReturn(array('msg'=>'收回金额请输入数字！','status'=>$status));
            }
            if(!($apply_money>0)){
                $this->ajaxReturn(array('msg'=>'收回金额不能小于等于0！','status'=>$status));
            }
            $model=M('proxy_account');
            $model->startTrans();
            //读取操作对象代理商信息
            $proxy_list = M("proxy as p")
            ->lock(true)
            ->join("left join t_flow_proxy_account as pa on pa.proxy_id=p.proxy_id")
            ->where(array('p.proxy_id'=>$proxy_id,'p.proxy_name'=>$proxy_name))
            ->field("pa.account_id,pa.account_balance")
            ->find();
            if(!$proxy_list){
                $model->rollback();
                $this->ajaxReturn(array('msg'=>'代理商账户信息读取失败！','status'=>$status));
            }
            if($proxy_list['account_balance'] < $apply_money){
                $model->rollback();
                $this->ajaxReturn(array('msg'=>'所选代理商余额不足收回金额！','status'=>$status));
            }
            //读取当前代理商信息
            $where['proxy_id']=D('SysUser')->self_proxy_id();
            $Balance=$model->where($where)->find();
            
            //减少所选代理商金额
            $edit['account_balance'] = $proxy_list['account_balance'] - $apply_money;
            $edit['modify_user_id']=D('SysUser')->self_id();
            $edit['modify_date']=date('Y-m-d H:i:s',time());
            $res=$model->where(array('account_id'=>$proxy_list['account_id']))->save($edit);
            //增加自身金额
            if(D('SysUser')->self_user_type()==1){
                $edit_top['account_balance'] = 0;
            }else{
                $edit_top['account_balance'] = $Balance['account_balance'] + $apply_money;
            }
            $edit_top['modify_user_id']=D('SysUser')->self_id();
            $edit_top['modify_date']=date('Y-m-d H:i:s',time());
            $res_top=$model->where(array('account_id'=>$Balance['account_id']))->save($edit_top);
            $remark=trim(I("remark"));
            if($res && $res_top){
                //添加流水  传值参数：对象ID号，对象金额，当前代理商ID，当前代理商金额，操作金额
                if($this->return_account_record($proxy_id,$proxy_list['account_balance'],$where['proxy_id'],$Balance['account_balance'],$apply_money,$remark)){
                    $model->commit();
                    $msg = '资金收回成功！';
                    $n_msg='成功';
                    $status = 'success';
                }else{
                    $model->rollback();
                    $msg ='资金收回失败！';
                    $n_msg='失败';
                }
            }else{
                $model->rollback();
                $msg ='资金收回失败！';
                $n_msg='失败';
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，代理商账户资金收回，代理商【'.obj_name($proxy_id,1).'】，收回金额【'.money_format2($apply_money).'】元'.$n_msg;
            $this->sys_log('代理商账户资金收回',$note);
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }

    //冻结
    public function freeze_money(){
        $tran=trim(I('get.tran'));
        if($tran){
            $status='error';
            $da['proxy_id']=trim(I("proxy_id"));
            $da['account_id']=trim(I("account_id"));
            $da['proxy_name']=trim(I("proxy_name"));
            $da['freeze']=trim(I("freeze"));
            $da['apply_money']=trim(I("apply_money"));
            $da['remark']=trim(I("remark"));
            if($da['apply_money']==""){
                $this->ajaxReturn(array('msg'=>'请输入金额！','status'=>$status));
            }
            if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $da['apply_money'])){
                $this->ajaxReturn(array('msg'=>'金额请输入数字！','status'=>$status));
            }
            if(!($da['apply_money']>0)){
                $this->ajaxReturn(array('msg'=>'金额不能小于等于0！','status'=>$status));
            }
            if($da['freeze']==1){
                $msg="确定是否从代理商【".$da['proxy_name']."】账户转入冻结余额".$da['apply_money']."元？";
            }else{
                $msg="确定是否从代理商【".$da['proxy_name']."】账户转出冻结余额".$da['apply_money']."元？";
            }
             $this->ajaxReturn(array('msg'=>$msg,"status"=>'success',"info"=>$da));
        }
        if(isset($_GET['account_id']) && $_GET['account_id'] > 0){
            $list=D('ProxyAccount')->account_detailed();
            $this->assign($list);
            $this->display();
        }else{
            $msg = '系统错误';
            $status = 'error';
            $account_id=trim(I('account_id'));
            $proxy_id=trim(I('proxy_id'));
            $freeze=trim(I("freeze")); //1为转冻结 2为转解冻
            $proxy_name=trim(I('proxy_name'));
            $apply_money=trim(I('apply_money'));

            if($apply_money==""){
                $this->ajaxReturn(array('msg'=>'请输入金额！','status'=>$status));
            }
            if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $apply_money)){
                $this->ajaxReturn(array('msg'=>'金额请输入数字！','status'=>$status));
            }
            if(!($apply_money>0)){
                $this->ajaxReturn(array('msg'=>'金额不能小于等于0！','status'=>$status));
            }
            $model=M('proxy_account');
            $model->startTrans();
            //读取操作对象代理商信息
            $proxy_list = M("proxy as p")
            ->join("left join t_flow_proxy_account as pa on pa.proxy_id=p.proxy_id")
            ->lock(true)
            ->where(array('p.proxy_id'=>$proxy_id,'p.proxy_name'=>$proxy_name))
            ->field("pa.account_id,pa.account_balance,pa.freeze_money,p.top_proxy_id")
            ->find();
            if(!$proxy_list){
                $model->rollback();
                $this->ajaxReturn(array('msg'=>'代理商账户信息读取失败！','status'=>$status));
            }
            if($freeze==1){
                if($proxy_list['account_balance'] < $apply_money){
                    $model->rollback();
                    $this->ajaxReturn(array('msg'=>'所选代理商账户余额不足！','status'=>$status));
                }
            }else{
                 if($proxy_list['freeze_money'] < $apply_money){
                     $model->rollback();
                    $this->ajaxReturn(array('msg'=>'所选代理商冻结余额不足！','status'=>$status));
                }
            }
           
            //读取当前代理商信息
            $where['proxy_id']=D('SysUser')->self_proxy_id();
            $Balance=$model->where($where)->find();
            if($freeze==1){
                $edit['account_balance'] = $proxy_list['account_balance'] - $apply_money;
                $edit['freeze_money'] = $proxy_list['freeze_money'] + $apply_money;
            }else{
                $edit['freeze_money'] = $proxy_list['freeze_money'] - $apply_money;
                $edit['account_balance'] = $proxy_list['account_balance'] + $apply_money;
            }
            
            $edit['modify_user_id']=D('SysUser')->self_id();
            $edit['modify_date']=date('Y-m-d H:i:s',time());
            $res=$model->where(array('account_id'=>$proxy_list['account_id']))->save($edit);
            //$remark=trim(I("remark"));
            $operate_type=$freeze==1?9:10;
            $balance_type=$freeze==1?2:1;
            $account_record=$this->account_record($proxy_id,$proxy_list['account_balance'],$apply_money,$edit['account_balance'],$operate_type,$balance_type);
            if($res && $account_record){
                $status = 'success';
                $msg=$freeze==1?'账户余额转冻结余额成功！':'冻结余额转账户余额成功！';
                $title=$freeze==1?'账户余额转冻结余额':'账户冻结余额转账户余额';
                $n_msg='成功';
                $model->commit();
            }else{
                $msg=$freeze==1?'账户余额转冻结余额失败！':'冻结余额转账户余额失败！';
                $title=$freeze==1?'账户余额转冻结余额':'账户冻结余额转账户余额';
                $n_msg='失败';
                $model->rollback();
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，代理商'.$title.'，代理商【'.obj_name($proxy_id,1).'】，操作金额【'.money_format2($apply_money).'】元'.$n_msg;
            $this->sys_log('代理商'.$title,$note);
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }

    /*代理商账户缓存额度*/
    public function cache_limit(){
        $msg = '系统错误';
        $status = 'error';
        $operate=trim(I('get.operate'));
        if($operate=='ask'){
            $da['proxy_id']=trim(I("proxy_id"));
            $da['account_id']=trim(I("account_id"));
            $da['cache_credit']=trim(I("cache_credit"));
            $da['channel_cache_credit']=trim(I("channel_cache_credit"));
           /* if($da['cache_credit']==""){
                $this->ajaxReturn(array('msg'=>'请输入缓存额度！','status'=>$status));
            }*/
            if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $da['cache_credit']) && $da['cache_credit']>0){
                $this->ajaxReturn(array('msg'=>'缓存额度请输入数字！','status'=>$status));
            }
            if($da['cache_credit']<0){
                $this->ajaxReturn(array('msg'=>'缓存额度不能小于等于0！','status'=>$status));
            }

            if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $da['channel_cache_credit']) && $da['channel_cache_credit']>0){
                $this->ajaxReturn(array('msg'=>'通道缓存额度请输入数字！','status'=>$status));
            }

            $msg="确定是否给代理商【".obj_data($da['proxy_id'],1,'name')."】设置缓存额度".$da['cache_credit']."元，通道缓存额度".$da['channel_cache_credit']."元？";
            $this->ajaxReturn(array('msg'=>$msg,"status"=>'success',"info"=>$da));
        }else if($operate=='run'){
            $account_id=trim(I("account_id"));
            $cache_credit=trim(I('cache_credit'));
            $channel_cache_credit=trim(I('channel_cache_credit'));
            $proxy_id=trim(I('proxy_id'));
            $model=M('proxy_account');
           /* if($cache_credit==""){
                $this->ajaxReturn(array('msg'=>'请输入缓存额度！','status'=>$status));
            }*/
            if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $cache_credit) && $cache_credit>0){
                $this->ajaxReturn(array('msg'=>'缓存额度请输入数字！','status'=>$status));
            }
            if($cache_credit<0){
                $this->ajaxReturn(array('msg'=>'缓存额度不能小于等于0！','status'=>$status));
            }
            if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $channel_cache_credit) && $channel_cache_credit>0){
                $this->ajaxReturn(array('msg'=>'通道缓存额度请输入数字！','status'=>$status));
            }
            $model->startTrans();
            $map['account_id']=$account_id;
            $map['proxy_id']=$proxy_id;
            $account_info=$model->lock(true)->where($map)->find();
            if(empty($account_info)){
                $model->rollback();
                $this->ajaxReturn(array('msg'=>'对不起，没有找到相关数据，请重试！','status'=>$status));
            }
            $edit['account_id']=$account_id;
            $edit['cache_credit']=$cache_credit==''?null:$cache_credit;
            $edit['channel_cache_credit']=$channel_cache_credit==''?null:$channel_cache_credit;
            $res=$model->save($edit);
            if($res || $res==0){
                $msg='代理商账户设置缓存额度成功！';
                $status='success';
                $n_msg='成功';
                $model->commit();
            }else{
                $msg='代理商账户设置缓存额度失败！';
                $n_msg='失败';
                $model->rollback();
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$account_id.'】，代理商账户设置缓存额度，代理商【'.obj_name($proxy_id,1).'】，缓存额度【'.money_format2($cache_credit).'】元，通道缓存额度【'.money_format2($channel_cache_credit).'】元'.$n_msg;
            $this->sys_log('代理商账户设置缓存额度',$note);
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }else{
            $list=D('ProxyAccount')->account_detailed();
            if($list){
                $proxy=D('ProxyAccount')->account_detailed_self();
                $this->assign("proxy",$proxy);
                $this->assign($list);
                $this->display();
            }else{
                $this->ajaxReturn(array('msg'=>'对不起，没有找到相关数据，请重试！','status'=>$status));
            }

        }
    }

    /**
     * 代理商账户设置提醒额度
     */
    public function set_quota_remind(){
        $msg = '系统错误';
        $status = 'error';
        $operate=trim(I('get.operate'));
        if($operate=='ask'){
            $da['proxy_id']=trim(I("proxy_id"));
            $da['account_id']=trim(I("account_id"));
            $da['new_quota_remind']=trim(I("new_quota_remind"));
            if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $da['new_quota_remind']) && $da['new_quota_remind']>0){
                $this->ajaxReturn(array('msg'=>'提醒额度请输入数字！','status'=>$status));
            }
            if($da['new_quota_remind']<0){
                $this->ajaxReturn(array('msg'=>'提醒额度不能小于等于0！','status'=>$status));
            }
            $msg="确定是否给代理商【".obj_data($da['proxy_id'],1,'name')."】设置提醒额度".$da['new_quota_remind']."元？";
            $this->ajaxReturn(array('msg'=>$msg,"status"=>'success',"info"=>$da));
        }else if($operate=='run'){
            $account_id=trim(I("account_id"));
            $new_quota_remind=trim(I('new_quota_remind'));
            $proxy_id=trim(I('proxy_id'));
            $model=M('proxy_account');
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
            $map['proxy_id']=$proxy_id;
            $account_info=$model->lock(true)->where($map)->find();
            if(empty($account_info)){
                $model->rollback();
                $this->ajaxReturn(array('msg'=>'对不起，没有找到相关数据，请重试！','status'=>$status));
            }
            $edit['account_id']=$account_id;
            $edit['new_quota_remind']=$new_quota_remind==''?null:$new_quota_remind;
            $res=$model->save($edit);
            if($res || $res==0){
                $msg='代理商账户设置提醒额度成功！';
                $status='success';
                $n_msg='成功';
                $model->commit();
            }else{
                $msg='代理商账户设置提醒额度失败！';
                $n_msg='失败';
                $model->rollback();
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$account_id.'】，代理商账户设置提醒额度，代理商【'.obj_name($proxy_id,1).'】，提醒额度【'.money_format2($new_quota_remind).'】元'.$n_msg;
            $this->sys_log('代理商账户设置提醒额度',$note);
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }else{
            $list=D('ProxyAccount')->account_detailed();
            if($list){
                $proxy=D('ProxyAccount')->account_detailed_self();
                $this->assign("proxy",$proxy);
                $this->assign($list);
                $this->display();
            }else{
                $this->ajaxReturn(array('msg'=>'对不起，没有找到相关数据，请重试！','status'=>$status));
            }

        }
    }

    /**
     * 添加流水信息
     */
    private function account_record($proxy_id,$account_balance,$apply_money,$operater_after_balance,$operate_type,$balance_type){
        $record['operater_before_balance']   = $account_balance;  //操作前金额
        $record['operater_after_balance']    = $operater_after_balance; //操作后金额
        $record['operater_price']            = $apply_money;  //提现金额
        $record['operate_type']              = $operate_type; //资金操作
        $record['balance_type']              = $balance_type;//支出
        $record['record_date']               = date('Y-m-d H:i:s',time());
        $record['user_id']                   = D('SysUser')->self_id();
        $record['operation_date']            = date('Y-m-d H:i:s',time());
        $record['user_type']                 = 1;
        $record['proxy_id']                  = $proxy_id;
        $record['obj_user_type']             = 1;
        $record['obj_proxy_id']              = $proxy_id;
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
     * 添加流水信息
     */
    private function return_account_record($proxy_id,$account_balance,$top_proxy_id,$top_account_balance,$apply_money,$remark=""){
        if(D('SysUser')->self_user_type()==1){
            $record[0]['operater_before_balance']=0;  //操作前金额
            $record[0]['operater_after_balance']=0; //操作后金额
        }else{
            $record[0]['operater_before_balance']= $top_account_balance;  //操作前金额
            $record[0]['operater_after_balance']=$top_account_balance+$apply_money; //操作后金额
        }
        $record[0]['operater_price']    = $apply_money;  //收回金额
        $record[0]['operate_type']      = 5; //返还
        $record[0]['balance_type']      = 1;//收入
        $record[0]['record_date']       = date('Y-m-d H:i:s',time());
        $record[0]['user_id']           = D('SysUser')->self_id();
        $record[0]['operation_date']    = date('Y-m-d H:i:s',time());
        $record[0]['user_type']         = 1;
        $record[0]['proxy_id']          = $top_proxy_id;
        $record[0]['obj_user_type']     = 1;
        $record[0]['obj_proxy_id']      = $proxy_id;
        $record[0]['remark']            =$remark;
        $record[0]['device_name']       = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));

        /*下级代理商为收入*/
        $record[1]['operater_before_balance']   = $account_balance;  //操作前金额
        $record[1]['operater_after_balance']    = $account_balance - $apply_money; //操作后金额
        $record[1]['operater_price']            = $apply_money;  //提现金额
        $record[1]['operate_type']              = 3; //提现
        $record[1]['balance_type']              = 2;//支出
        $record[1]['record_date']               = date('Y-m-d H:i:s',time());
        $record[1]['user_id']                   = D('SysUser')->self_id();
        $record[1]['operation_date']            = date('Y-m-d H:i:s',time());
        $record[1]['user_type']                 = 1;
        $record[1]['proxy_id']                  = $proxy_id;
        $record[1]['obj_user_type']             = 1;
        $record[1]['obj_proxy_id']              = $top_proxy_id;
        $record[1]['remark']                    = $remark;
        $record[1]['device_name']               = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));
        //添加流水记录
        $recordResult=M('account_record')->addAll($record);
        if($recordResult){
            return true;
        }else{
            return false;
        }
    }
}
?>