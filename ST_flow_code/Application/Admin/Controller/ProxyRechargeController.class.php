<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class ProxyRechargeController extends CommonController{
     /*代理商充值管理表*/
     public function index(){
        //获取自身的用户类型是运营平台，代理商，企业
         D("SysUser")->sessionwriteclose();
        $user=D('SysUser')->self_user_type();
        if($user == 2){
            $start_datetime = trim(I('get.start_datetime'));   //开始时间
            $end_datetime = trim(I('get.end_datetime'));   //结束时间
            $approve_status=trim(I('get.approve_status'));   //审核状态
            $apply_code=trim(I('get.apply_code'));//申请编号
            $source=trim(I('get.source'));//支付方式
            $where=array();
            if($source>0){
                $where['ap.source']=$source;
            }
            if($approve_status!="" && $approve_status!=9){
                $where['ap.approve_status'] = $approve_status;
            }
         /*   if($start_datetime or $end_datetime){
                if($start_datetime && $end_datetime){
                    $where['ap.create_date']=array('between',array(start_time($start_datetime),end_time($end_datetime)));
                }elseif($start_datetime){
                    $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                    $where['ap.create_date']= array('between',array(start_time($start_datetime),$end_datetime));
                }elseif($end_datetime){
                    $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                    $where['ap.create_date']=array('between',array($start_datetime,end_time($end_datetime)));
                }
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
                $where['ap.create_date'] = array('between',array($start_datetime,$end_datetime));
            }


           /* if($start_datetime && $end_datetime){

                $where['ap.create_date'] = array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }*/
            if($apply_code){
                $where['ap.apply_code']=array('like','%'.$apply_code.'%');
            }
            $where['ap.proxy_id']=D('SysUser')->self_proxy_id();//获取自身的代理商ID
            $proxy=M("proxy")->where(array("proxy_id"=>D("SysUser")->self_proxy_id()))->find();
            $this->assign("top_proxy_id",$proxy['top_proxy_id']);
        }else{
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
                $where['ap.create_date'] = array('between',array($start_datetime,$end_datetime));
            }
            $ids=D("Proxy")->proxy_child_ids();//获取该用户可操作的企业号
            $where['p.proxy_id']=array("in",$ids);
            $where['ap.top_proxy_id']=D("SysUser")->self_proxy_id();
        }
        //var_dump($where);die();
         $apply_type = trim(I('get.apply_type'));
         if(!empty($apply_type)){
             $where['ap.apply_type'] = $apply_type;
         }
        $list=D('ProxyAccount')->proxyRechargeList($where);
        $this->assign('usr_type',$user);
        $this->assign('proxy_id',D("SysUser")->self_proxy_id());
        $this->assign('list',$list['list']);
        $this->assign('page',$list['page']);
         $this->assign('d_sdata',date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))));  //默认开始时间
         $this->assign('d_edata',date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))));  //默认结束时间
        $pagehtml = $user==2?"":"st_index";
         $this->assign('sum_results',$list['sum_results']);
        $this->assign('user_id',D("SysUser")->self_id());
        $this->assign('source_name',get_source_name());
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
            $source=trim(I('get.source'));//支付方式
            $where=array();
            if($source>0){
                $where['ap.source']=$source;
            }
            if($approve_status!="" && $approve_status!=9){
                $where['ap.approve_status'] = $approve_status;
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
            }
            if($apply_code){
                $where['ap.apply_code']=array('like','%'.$apply_code.'%');
            }
            $where['ap.proxy_id']=D('SysUser')->self_proxy_id();//获取自身的代理商ID
            $title='代理商充值申请';
            $headArr=array("申请编号","付款金额(元)","付款方式","充值类型","付款日期","审核状态","申请人","申请时间");
        }else{
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
                $where['ap.create_date'] = array('between',array($start_datetime,$end_datetime));
            }
            $ids=D("Proxy")->proxy_child_ids();//获取该用户可操作的企业号
            $where['p.proxy_id']=array("in",$ids);
            $where['ap.top_proxy_id']=D("SysUser")->self_proxy_id();
            $title='代理商充值管理';
            $headArr=array("代理商编号","代理商名称","申请编号","付款金额","付款方式","充值类型","付款日期","复审人","审核状态","申请人","申请时间");
        }
        $apply_type = trim(I('get.apply_type'));
        if(!empty($apply_type)){
            $where['ap.apply_type'] = $apply_type;
        }
        $proxy_recharge_list=D('ProxyAccount')->proxy_export_excel($where);

        $list=array();
        if($user==1){
            $top_id=1;
        }else{
            $proxy=M("proxy")->where(array("proxy_id"=>D("SysUser")->self_proxy_id()))->find();
            $top_id=$proxy['top_proxy_id'];
        }   
        foreach($proxy_recharge_list as $k=>$v){
            if($user == 1){
                $list[$k]['proxy_code'] =$v['proxy_code'];
                $list[$k]['proxy_name'] =$v['proxy_name'];
            }
            $list[$k]['apply_code'] =$v['apply_code'];
            $list[$k]['apply_money'] =$v['apply_money'];
            if($v['source']==0){
                $list[$k]['source']="";
            }elseif($top_id==1){
                $list[$k]['source'] =get_source_name($v['source']);
            }else{
                if($v['source']==1){
                    $list[$k]['source']="汇款";
                }elseif($v['$source']==2){
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
            if($user == 1) {
                $list[$k]['approve_man'] = get_approve_people($v['apply_id'], 1);
            }
            $list[$k]['approve_status'] =get_apply_status($v['approve_status']);
            $list[$k]['create_user_id'] =get_user_name($v['create_user_id']);
            $list[$k]['create_date'] =$v['create_date'];
       }
        ExportEexcel($title,$headArr,$list);
    }

    /*尚通端给代理商提交充值申请*/
   public  function st_voucher(){
       $where['approve_status']=1;
       $where['status']=1;
       $where['proxy_id']=array("neq","1");
       $pids=D('Proxy')->proxy_child_ids();
       if($pids){
           $where['proxy_id']=array("in",$pids);
       }else{
           $where['proxy_id']=array("eq","-1");
       }
       $info=M('proxy')->where($where)->select();
       $this->assign("info",$info);
       $this->assign('source_name',get_source_name());
       $this->display();        //模板
   }


	/*
	 *代理商充值页面显示
	 */
	public function voucher(){
        $where['proxy_id']=D('SysUser')->self_proxy_id();
        $info=M('proxy')->where($where)->find();
        $this->assign($info);
        $this->assign("top_id",$info['top_proxy_id']);
        $this->assign('source_name',get_source_name());
        $this->display();        //模板
    }

    /*企业送审*/
    public function send_approve(){
        $msg = '系统错误！';
        $status = 'error';
        $apply_id = I('id');
        $map['proxy_id']=D('SysUser')->self_proxy_id();
        $map['apply_id']=$apply_id;
        $proxy_apply = M("proxy_recharge_apply")->where($map)->find();
        if($proxy_apply){
            $edit['approve_status'] = 2;
            if(M("proxy_recharge_apply")->where(array('apply_id'=>$proxy_apply['apply_id']))->save($edit)){
                $msg = '充值申请单提交成功！';
                $status = 'success';
                $n_msg='提交审核成功';
            }else{
                $msg = '充值申请单提交失败！';
                $n_msg='提交审核失败';
            }
        }else{
            $msg = '数据读取错误！';
            $n_msg='提交审核失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】,充值申请单【".$proxy_apply['apply_code']."】".$n_msg;
        $this->sys_log('代理商充值申请单提交审核',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    /*代理商充值功能*/
    public function insert(){
        if($_GET['operation']=="giveapply"){
            $msg = '系统错误！';
            $status = 'error';
            $apply_id = I('id');
            $map['proxy_id']=D('SysUser')->self_proxy_id();
            $map['apply_id']=$apply_id;
            $proxy_apply = M("proxy_recharge_apply")->where($map)->find();
            if($proxy_apply){
                $edit['approve_status'] = 2;
                if(M("proxy_recharge_apply")->where(array('apply_id'=>$proxy_apply['apply_id']))->save($edit)){
                    $msg = '充值申请单提交审核成功！';
                    $status = 'success';
                    $n_msg='提交审核成功';
                }else{
                    $msg = '充值申请单提交审核失败！';
                    $n_msg='提交审核失败';
                }
            }else{
                $msg = '数据读取错误！';
                $n_msg='提交审核失败';
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id())."】,充值申请单【".$proxy_apply['apply_code']."】".$n_msg;
            $this->sys_log('代理商充值申请单提交审核',$note);
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }else{
            $msg = '系统错误!';
            $status = 'error';
            $data = array();
            $user_type=D('SysUser')->self_user_type();
            $post_proxy_id=trim(I("proxy_id"));
            if($post_proxy_id=='' && $user_type==1){
                $this->ajaxReturn(array('msg'=>'请输入代理商名称！','status'=>$status));
            }
            if(I('apply_money')==""){
                $msg = '请输入付款金额！';
            }elseif(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', I('apply_money'))){
                $msg = '付款金额请输入数字！';
            }elseif(I('apply_money')<=0){
                $msg = '付款金额不能小于等于0！';
            }else if(I('source')==0 && I("apply_type")==1){
                $msg = '请选择付款方式！';
            }elseif(I('transaction_date')=="" && I("apply_type")==1){
                $msg = '请输入付款日期！';
            }elseif(I("transaction_number")=="" && I("apply_type")==1){
                $msg = '请输入打款户名/支付订单号/交易号！';
            }else{
                if($_FILES['file']['name']==null || $_FILES['file']['name']==""){
                    $icense_img = "";
                }else{
                    $fileinfo = $this->business_licence_upload(C('PROXY_UPLOAD_DIR'));
                    if($fileinfo['file']){
                        $icense_img = substr(C('UPLOAD_DIR').$fileinfo['file']['savepath'].$fileinfo['file']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['file']['savepath'].$fileinfo['file']['savename'])-1);
                    }else{
                        $msg = $this->business_licence_upload_Error['file'];
                        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                    }
                }

                if($post_proxy_id!='' && $user_type==1){
                    $proxy_id=$post_proxy_id;
                    $approve_status=2;
                }else{
                    $proxy_id=D('SysUser')->self_proxy_id();
                    $approve_status=1;
                }
                $map['proxy_id']=$proxy_id;
                $info=M('proxy')->where($map)->find();
                $data['top_proxy_id']=$info['top_proxy_id'];
                $data['proxy_id']=$proxy_id;
                $data['create_user_id']=D('SysUser')->self_id();
                $data['apply_type']=trim(I("apply_type"));

                $apply_code="CZSQD".date('Ymd',time());
                $app['apply_code']=array('like',$apply_code.'%');
                $applys=M('proxy_recharge_apply')->where($app)->order("apply_code desc")->find();
                $applys=substr($applys['apply_code'],13);
                $applys=$applys+1;
                $data['apply_code']=generate_order($applys,1);

                $data['apply_money']=trim(I('apply_money'));
                $data['credential_one']=$icense_img;
                $pay_type='';
                //$proxy_type=D('EnterpriseAccount')->proxy_type();
                if(I("apply_type")==1){
                    $data['source']=trim(I('source'));
                    $data['transaction_number']=trim(I('transaction_number'));
                    $data['transaction_date']=trim(I('transaction_date'));
                    $title='正常充值';
                    if($info['proxy_type']==1){
                        $pay_type='，付款方式【'.get_source_name($data['source']).'】，'.get_transaction_name($data['source']).'【'.$data['transaction_number'].'】，付款日期【'. $data['transaction_date'].'】，';
                    }else{
                        $pay_type='，付款方式【'.get_source_info($data['source']).'】，'.get_transaction_name($data['source']).'【'.$data['transaction_number'].'】，付款日期【'. $data['transaction_date'].'】，';
                    }
                }else{
                    $data['source']=0;
                    $title='测试款';
                }
                $data['remark']=trim(I('remark'));
                $data['create_date']=date('Y-m-d H:i:s',time());
                $data['modify_user_id']=D('SysUser')->self_id();
                $data['modify_date']=date('Y-m-d H:i:s',time());
                $data['approve_status']=$approve_status;
                $id = M('proxy_recharge_apply')->add($data);
                //执行添加
                if($id){
                    $msg = '新增充值申请单成功';
                    $n_msg='成功';
                    $status = 'success';
                    $info2 = $id;
                   // if($user_type!=1){
                        $remind_content='代理商【'.$info['proxy_name'].'】已提交充值申请单，请进行初审！';
                        R('ObjectRemind/send_user',array(5,$remind_content,array($info['sale_id'])));
                   // }
                }else{
                    $msg = '新增充值申请单失败';
                    $n_msg='失败';
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$id."】，新增代理商充值申请单，代理商【".obj_name($proxy_id,1)."】，申请编号【".$data['apply_code']."】，申请类型【".$title."】".$pay_type."申请金额【". money_format2($data['apply_money'])."】元".$n_msg;
                $this->sys_log('新增代理商充值申请单',$note);
            }
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'info'=>$info2));
        }
    }
    
    public function edit(){
        $where['ap.apply_id']=I("apply_id");
        $proxyapply=D('ProxyAccount')->proxy_detailed($where);
        //当菜单不存在时
        if($proxyapply){
            $proxy=M("proxy")->where(array("proxy_id"=>D("SysUser")->self_proxy_id()))->find();
            $this->assign("top_id",$proxy['top_proxy_id']);
            $this->assign('proxyapply',$proxyapply);
            $this->assign('source_name',get_source_name());
            $this->display();
        }else{
            $this->error('数据不存在！');
        }      
    }


    public function st_edit(){
        $operation=I('get.operation');
        if($operation){
            $msg = '系统错误!';
            $status = 'error';
            $data = array();
            $apply_id = I('apply_id');
            $apply_money = I('apply_money');
            $remark = I('remark');
            $user_type=D('SysUser')->self_user_type();
            $post_proxy_id=trim(I("proxy_id"));
            if($post_proxy_id=='' && $user_type==1){
                $this->ajaxReturn(array('msg'=>'请输入代理商名称！','status'=>$status));
            }
            if(I('apply_money')==""){
                $msg = '请输入付款金额！';
            }elseif(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', I('apply_money'))){
                $msg = '付款金额请输入数字！';
            }elseif(I('apply_money')<=0){
                $msg = '付款金额不能小于等于0！';
            }else if(I('source')==0 && I("apply_type")==1){
                $msg = '请选择付款方式！';
            }elseif(I('transaction_date')=="" && I("apply_type")==1){
                $msg = '请输入付款日期！';
            }elseif(I("transaction_number")=="" && I("apply_type")==1){
                $msg = '请输入打款户名/支付订单号/交易号！';
            }else{
                //$map['proxy_id']=$post_proxy_id;
                $map['apply_id']=$apply_id;
                $proxy_apply = M("proxy_recharge_apply")->where($map)->find();
                if($proxy_apply){
                    if(!(empty($_FILES['file']['name']) || $_FILES['file']['name']=='')){
                        $fileinfo = $this->business_licence_upload(C('PROXY_UPLOAD_DIR'));
                        if($fileinfo['file']){
                            $icense_img = substr(C('UPLOAD_DIR').$fileinfo['file']['savepath'].$fileinfo['file']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['file']['savepath'].$fileinfo['file']['savename'])-1);
                            $edit['credential_one'] = $icense_img;
                        }else{
                            $msg = $this->business_licence_upload_Error['file'];
                            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                        }
                    }
                    $edit['proxy_id'] = $post_proxy_id;
                    $edit['apply_money'] = $apply_money;
                    $edit['remark'] = $remark;
                    $source=trim(I('source'));
                    $transaction_number=trim(I('transaction_number'));
                    $transaction_date=trim(I('transaction_date'));
                    $apply_type=I("apply_type");
                    if($apply_type==1){
                        $edit['source']=$source;
                        $edit['transaction_number']=$transaction_number;
                        $edit['transaction_date']=$transaction_date;
                        $title='正常充值';
                    }else{
                        $edit['source']=0;
                        $edit['transaction_number']=null;
                        $edit['transaction_date']=null;
                        $title='测试款';
                    }
                    $data['create_date']=date('Y-m-d H:i:s',time());
                    $edit['modify_user_id'] = D('SysUser')->self_id();
                    $edit['modify_date'] = date('Y-m-d H:i:s',time());
                    $edit['apply_type']=trim(I("apply_type"));
                    $proxy_type=I('proxy_type');
                    if($proxy_apply['approve_status']!=1){
                        $edit['approve_status'] =2;
                        $edit['last_approve_date'] = null;
                    }
                    $where['apply_id']=$apply_id;
                    //$apply_code=M("proxy_recharge_apply")->where($where)->find();
                    if(M("proxy_recharge_apply")->where($where)->save($edit)){
                        $msg = '编辑充值申请单成功';
                        $n_msg='成功';
                        $status = 'success';
                    }else{
                        $msg = '编辑充值申请单失败';
                        $n_msg='失败';
                    }
                    $c_item='';
                    $c_item.='，充值类型【'.$title.'】';

                    if($source!==$proxy_apply['source']){
                        if($apply_type==1){
                            $fg=!empty($c_item)?'，':'';
                            $c_item.=$proxy_type==1?$fg.'付款方式【'.get_source_name($source).'】':$fg.'付款方式【'.get_source_info($source).'】';
                            $fg=!empty($c_item)?'，':'';
                            $c_item.=$fg.get_transaction_name($source).'【'. $transaction_number.'】';
                            $fg=!empty($c_item)?'，':'';
                            $c_item.=$fg.'付款日期【'. $transaction_date.'】';
                        }
                    }else{
                        if($apply_type==1){
                            if($source!==$proxy_apply['source']){
                                $fg=!empty($c_item)?'，':'';
                                $c_item.=$proxy_type==1?$fg.'付款方式【'.get_source_name($source).'】':$fg.'付款方式【'.get_source_info($source).'】';
                            }
                            $fg=!empty($c_item)?'，':'';
                            $c_item.=$transaction_number===$proxy_apply['transaction_number']?'':$fg.get_transaction_name($source).'【'. $transaction_number.'】';
                            $fg=!empty($c_item)?'，':'';
                            $c_item.=strtotime($transaction_date)===strtotime($proxy_apply['transaction_date'])?'':$fg.'付款日期【'. $transaction_date.'】';
                        }
                    }
                    $fg=!empty($c_item)?'，':'';
                    $c_item.=$apply_money*100===$proxy_apply['apply_money']*100?'':$fg.'付款金额【'.$apply_money.'】元';
                    $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，编辑代理商充值申请单，代理商【".obj_name($post_proxy_id,1)."】，充值编号【".$proxy_apply['apply_code']."】".$c_item.$n_msg;
                    $this->sys_log('编辑代理商充值申请单',$note);
                }else{
                    $msg = '数据读取失败！';
                }
            }
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }else{
            $map['approve_status']=1;
            $map['status']=1;
            $map['proxy_id']=array("neq","1");
            $pids=D('Proxy')->proxy_child_ids();
            if($pids){
                $map['proxy_id']=array("in",$pids);
            }else{
                $map['proxy_id']=array("eq","-1");
            }
            $where['ap.apply_id']=I("apply_id");
            $proxyapply=D('ProxyAccount')->proxy_detailed($where);
            //当菜单不存在时
            if($proxyapply){
                $proxy=M("proxy")->where(array("proxy_id"=>$proxyapply['proxy_id']))->find();
                $this->assign("top_id",$proxy['top_proxy_id']);
                $this->assign('proxyapply',$proxyapply);
                $this->assign('source_name',get_source_name());
                $this->display();
            }else{
                $this->error('对不起，数据不存在！');
            }
        }

    }
    
    public function update(){
        $msg = '系统错误!';
        $status = 'error';
        $data = array();
        $apply_id = I('apply_id');
        $apply_money = trim(I('apply_money'));
        $remark = I('remark');
        $user_type=D('SysUser')->self_user_type();
        $post_proxy_id=trim(I("proxy_id"));
        if($post_proxy_id=='' && $user_type==1){
            $this->ajaxReturn(array('msg'=>'请输入代理商名称！','status'=>$status));
            exit;
        }
        if($apply_money==""){
            $msg = '请输入付款金额！';
        }elseif(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $apply_money)){
            $msg = '付款金额请输入数字！';
        }elseif(I('apply_money')<=0){
            $msg = '付款金额不能小于等于0！';
        }else if(I('source')==0 && I("apply_type")==1){
            $msg = '请选择付款方式！';
        }elseif(I('transaction_date')=="" && I("apply_type")==1){
            $msg = '请输入付款日期！';
        }elseif(I("transaction_number")=="" && I("apply_type")==1){
            $msg = '请输入打款户名/支付订单号/交易号！';
        }else{
            if($post_proxy_id!='' && $user_type==1){
                $proxy_id=$post_proxy_id;
                $approve_status=2;
            }else{
                $proxy_id=D('SysUser')->self_proxy_id();
                $approve_status=1;
            }
            $map['proxy_id']=$proxy_id;
            $map['apply_id']=$apply_id;
            $proxy_apply = M("proxy_recharge_apply")->where($map)->find();
            if($proxy_apply){
                if(!(empty($_FILES['file']['name']) || $_FILES['file']['name']=='')){
                    $icense_img = "";
                    $fileinfo = $this->business_licence_upload(C('PROXY_UPLOAD_DIR'));
                    if($fileinfo['file']){
                        $icense_img = substr(C('UPLOAD_DIR').$fileinfo['file']['savepath'].$fileinfo['file']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['file']['savepath'].$fileinfo['file']['savename'])-1);
                        $edit['credential_one'] = $icense_img;
                    }else{
                        $msg = $this->business_licence_upload_Error['file'];
                        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                    }
                }
                $edit['apply_money'] = $apply_money;
                $edit['remark'] = $remark;
                $source=trim(I('source'));
                $transaction_number=trim(I('transaction_number'));
                $transaction_date=trim(I('transaction_date'));
                $apply_type=I("apply_type");
                $proxy_type=I('proxy_type');
                if($apply_type==1){
                    $edit['source']=$source;
                    $edit['transaction_number']=$transaction_number;
                    $edit['transaction_date']=$transaction_date;
                    $title='正常充值';
                }else{
                    $edit['source']=0;
                    $edit['transaction_number']=null;
                    $edit['transaction_date']=null;
                    $title='测试款';
                }
                $data['create_date']=date('Y-m-d H:i:s',time());
                $edit['modify_user_id'] = D('SysUser')->self_id();
                $edit['modify_date'] = date('Y-m-d H:i:s',time());
                $edit['apply_type']=trim(I("apply_type"));

                if($proxy_apply['approve_status']!=1){
                    $edit['approve_status'] =$approve_status;
                    $edit['last_approve_date'] = null;
                }
                if(M("proxy_recharge_apply")->where(array('apply_id'=>$apply_id))->save($edit)){
                    $msg = '编辑充值申请单成功';
                    $n_msg='成功';
                    $status = 'success';
                }else{
                    $msg = '编辑充值申请单失败';
                    $n_msg='失败';
                }

                $c_item='';
                $c_item.='，充值类型【'.$title.'】';
                if($source!==$proxy_apply['source']){
                    if($apply_type==1){
                        $fg=!empty($c_item)?'，':'';
                        $c_item.=$proxy_type==1?$fg.'付款方式【'.get_source_name($source).'】':$fg.'付款方式【'.get_source_info($source).'】';
                        $fg=!empty($c_item)?'，':'';
                        $c_item.=$fg.get_transaction_name($source).'【'. $transaction_number.'】';
                        $fg=!empty($c_item)?'，':'';
                        $c_item.=$fg.'付款日期【'. $transaction_date.'】';
                    }
                }else{
                    if($apply_type==1){
                        if($source!==$proxy_apply['source']){
                            $fg=!empty($c_item)?'，':'';
                            $c_item.=$proxy_type==1?$fg.'付款方式【'.get_source_name($source).'】':$fg.'付款方式【'.get_source_info($source).'】';
                        }
                        $fg=!empty($c_item)?'，':'';
                        $c_item.=$transaction_number===$proxy_apply['transaction_number']?'':$fg.get_transaction_name($source).'【'. $transaction_number.'】';
                        $fg=!empty($c_item)?'，':'';
                        $c_item.=strtotime($transaction_date)===($proxy_apply['transaction_date'])?'':$fg.'付款日期【'. $transaction_date.'】';
                    }
                }
                $fg=!empty($c_item)?'，':'';
                $c_item.=$apply_money*100===$proxy_apply['apply_money']*100?'':$fg.'付款金额【'.$apply_money.'】元';

                $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，编辑代理商充值申请单，代理商【".obj_name($proxy_id,1)."】，充值编号【".$proxy_apply['apply_code']."】".$c_item.$n_msg;
                $this->sys_log('编辑代理商充值申请单',$note);

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
        $user_type=D('SysUser')->self_user_type();
        //$user_id=D('SysUser')->self_id();
        if($user_type!=1 ){
            $map['proxy_id']=D('SysUser')->self_proxy_id();
        }
        $map['apply_id']=$apply_id;
        $proxy_apply = M("proxy_recharge_apply")->where($map)->find();
        if(in_array($proxy_apply['approve_status'],array(3,5))){
            $this->ajaxReturn(array('msg'=>'已经审核通过，请勿删除！','status'=>$status));
        }
        if($proxy_apply){
            $delete['apply_id'] = $proxy_apply['apply_id'];
            if(M("proxy_recharge_apply")->where($delete)->delete()){
                M("proxy_recharge_process")->where(array('apply_id'=>$proxy_apply['apply_id']))->delete();
                $msg = '删除充值申请单成功！';
                $status = 'success';
                $n_msg='成功';
            }else{
                $msg = '删除充值申请单失败！';
                $n_msg='失败';
            }
        }else{
            $msg = '数据读取错误！';
            $n_msg='失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，删除代理商充值申请单【".$proxy_apply['apply_code']."】".$n_msg;
        $this->sys_log('删除代理商充值申请单',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
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
            $approve_f=I('approve_f');
            //读取审核过程
            if($list){
                $process = M("proxy_recharge_process")->where(array('apply_id'=>$list['apply_id']))->select();
                if(!$process){
                    $process = "";
                }
            }
            $this->assign($list);
            if(D('SysUser')->self_user_type()==1){
                $this->assign("top_id",1);
            }else{
                $proxy=M("proxy")->where(array("proxy_id"=>D("SysUser")->self_proxy_id()))->find();
                $this->assign("top_id",$proxy['top_proxy_id']);
            }     
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
        if(in_array($apply['approve_status'],array(3,4,5))){
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

        if($apply_res && $process){
            $model->commit();
            $msg = $approve_status==2?'充值申请单初审驳回成功！':'充值申请单初审成功！';
            $status = 'success';
            $r_msg=$approve_status==2?'初审驳回':'初审成功';
            $n_msg=$r_msg;
            $info=M('proxy_recharge_apply as pr')
                ->join('left join t_flow_proxy  as p on pr.proxy_id=p.proxy_id ')
                ->join('left join t_flow_sys_user  as u on p.proxy_id=u.proxy_id ')
                ->where('apply_id='.$apply_id)
                ->field('pr.apply_code,p.sale_id,p.proxy_name,p.create_user_id,u.user_id')
                ->find();
            $remind_content='代理商充值申请单【'.$info['apply_code'].'】已经【'.$r_msg.'】，请知晓！';
            $use=array($info['sale_id'],$info['user_id']);
            R('ObjectRemind/send_user',array(7,$remind_content,$use));
            if($approve_status==1){
                $remind_content='代理商【'.$info['proxy_name'].'】充值申请单已初审通过，请进行复审！';
                R('ObjectRemind/send_user',array(6,$remind_content));
            }
        }else{
            $model->rollback();
            $msg = $approve_status==2?'充值申请单初审驳回失败！':'充值申请单初审失败！';
            $n_msg=$approve_status==2?'初审驳回失败':'单初审失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，审核代理商充值申请单【".$apply['apply_code']."】".$n_msg;
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
                /*记录账户流水 上级代理商为支出 */
                $map['proxy_id']=$apply['proxy_id'];
                //$account=D('ProxyAccount')->account($map);  //读取代理商账户
                $account=M('proxy_account')->where($map)->find();
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
                    $n_msg=$r_msg;
                    $this->send_recharge(2,$account['proxy_id'],$apply['apply_money'],$account['account_balance']+$apply['apply_money']);
                    $success_msg="，并充值【".$apply['apply_money']."】元";
                }else{
                    $model->rollback();
                    $msg ='充值申请单复审失败！';
                    $r_msg='复审失败';
                    $n_msg=$r_msg;
                }
            }

        }else{
            $model->rollback();
            $msgs = $approve_status==2?'充值申请单复审驳回失败！':'充值申请单复审失败！';
            $msg =$msgs;
            $n_msg=$approve_status==2?'复审驳回失败':'复审失败';
            $r_msg=$approve_status==2?'复审驳回失败':'复审失败';
        }
        $remind_content='代理商充值申请单【'.$info['apply_code'].'】已经【'.$r_msg.'】，请知晓！';
        $use=array($info['sale_id'],$info['user_id']);
        R('ObjectRemind/send_user',array(7,$remind_content,$use));

        $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，审核代理商充值申请单【".$apply['apply_code']."】".$n_msg.$success_msg;
        $this->sys_log('代理商充值申请单复审',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }
}
?>