<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class EnterpriseRechargeController extends CommonController{
     /*企业充值申请列表*/
     public function index(){
         D("SysUser")->sessionwriteclose();
             $user=D('SysUser')->self_user_type();//获取自身的用户类型是运营平台，代理商，企业
             $enterprise_name = trim(I('enterprise_name')); //代理商名称
             //$source = trim(I('get.source')); //来源
             //$apply_type = trim(I('get.apply_type'));  //操作方式
             $start_datetime = trim(I('start_datetime'));   //开始时间
             $end_datetime = trim(I('end_datetime'));   //结束时间
             $approve_status=trim(I('approve_status'));   //审核状态
             $proxy_code=trim(I('proxy_code'));
             $proxy_name=trim(I('proxy_name'));
             $enterprise_code=trim(I('enterprise_code'));
             $apply_code = trim(I('apply_code'));  //操作方式source
             $source=I('source');//来源

             $where=array();
             if($enterprise_code){
                 $where['e.enterprise_code']=array('like','%'.$enterprise_code.'%');
             }
             if($enterprise_name){ 
                 $where['e.enterprise_name']=array('like','%'.$enterprise_name.'%');
             }
         if($source){
             $where['ea.source'] = $source;
         }
         if($user==1){
             if($proxy_code){
                 $where['up.proxy_code']=array('like','%'.$proxy_code.'%');
             }
             if($proxy_name){
                 $where['up.proxy_name']=array('like','%'.$proxy_name.'%');
             }
         }
             if($user==3){
                if($approve_status!="" && $approve_status!=9){
                  $where['ea.approve_status'] = $approve_status;
                }
             }else{
               if($approve_status!=""){
                  if($approve_status!=9){
                      $where['ea.approve_status'] = $approve_status;
                  }else{
                      $where['ea.approve_status']=array('neq',1);
                  }
              }else{
                  $where['ea.approve_status']=array('neq',1);
              }
            }
            if($apply_code){
                $where['ea.apply_code'] = array('like','%'.$apply_code.'%');
            }
         if($start_datetime or $end_datetime){
             if($start_datetime && $end_datetime){
                 $where['ea.create_date'] =array('between',array(start_time($start_datetime),end_time($end_datetime)));
             }elseif($start_datetime){
                 $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                 $where['ea.create_date'] = array('between',array(start_time($start_datetime),$end_datetime));
             }elseif($end_datetime){
                 $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                 $where['ea.create_date'] =array('between',array($start_datetime,end_time($end_datetime)));
             }
         }else{
             $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
             $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
             $where['ea.create_date'] = array('between',array($start_datetime,$end_datetime));
         }
         if($user==3){
             $where['e.enterprise_id'] = D('SysUser')->self_enterprise_id();
         }else{
             $enterprise_child_ids=D('Enterprise')->enterprise_child_ids();
             $enterprise_ids=D('Enterprise')->enterprise_ids();
             $where['e.enterprise_id'] = array(array('in',$enterprise_child_ids),array('in',$enterprise_ids),'or') ;
         }
         $apply_type = trim(I('get.apply_type'));
         if(!empty($apply_type)){
             $where['ea.apply_type'] = $apply_type;
         }
         $list=D('EnterpriseAccount')->enterprise_apply_list($where);
        // var_dump($list);
               //var_dump($list);
         $proxy_type=D('EnterpriseAccount')->proxy_type();
                 if($user==2 && $proxy_type==1){
                     $proxy_type=1;
                 }else if($user==2 && $proxy_type==0){
                     $proxy_type=2;
                 }else{
                     $proxy_type=3;
                 }
                $this->assign('usr_type',$user);
                $this->assign('list',$list['list']);
                $this->assign('page',$list['page']);
                $this->assign('source_name',get_source_name());
                 $this->assign('proxy_type',$proxy_type);
         $this->assign('d_sdata',date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))));  //默认开始时间
         $this->assign('d_edata',date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))));  //默认结束时间
                 $this->assign('sum_results',$list['sum_results']);
                $this->assign('user_id',D('SysUser')->self_id());
                $this->display();        //模板
               // $this->error('权限不足');
     }
	/*
	 *企业充值页面显示
	 */
	public function voucher(){
        if(I('download')){
            $where['apply_id'] = array('eq',trim(I('get.apply_id')));
            $enterprise = M('enterprise_recharge_apply')->where($where)->find();
            parent::download('.'.$enterprise['credential_one']);
        }else{
            $where['enterprise_id']=D('SysUser')->self_enterprise_id();
            $list=M('enterprise')->where($where)->field('enterprise_name,enterprise_code')->find();
            $this->assign($list);
            $this->assign('source_name',get_source_name());
            $this->assign('proxy_type', D('EnterpriseAccount')->top_proxy_type());
            $this->display();        //模板
        }

    }
    /*代理商给企业充值页面*/
    public function voucher_proxy(){
        $operate=trim(I('get.operate'));
        if($operate!='insert'){
            $ids = D('Enterprise')->enterprise_ids();//获取当前代理商下可操作企业
            $enterprise_child_ids_array = explode(',',$ids);
            $where['enterprise_id']=array("in",$enterprise_child_ids_array);
            //$where['enterprise_id'] = array(array('in',D('Enterprise')->enterprise_child_ids()),array('in',D('Enterprise')->enterprise_ids()),'or') ;
            $where['approve_status']=1;
            $where['status']=1;
            $list=M('enterprise')->where($where)->field('enterprise_name,enterprise_id')->select();
            $this->assign('enterprise',$list);
            $this->assign('source_name',get_source_name());
            $this->assign('proxy_type', D('EnterpriseAccount')->proxy_type());
            $this->display();        //模板
        }else{
            $msg = '系统错误!';
            $status = 'error';
            $data = array();
            $con['proxy_id']=D('SysUser')->self_proxy_id();
            $Balance=M('proxy_account')->where($con)->find();
            if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', trim(I('apply_money')))){
                $this->ajaxReturn(array('msg'=>'请输入正确的付款金额！','status'=>$status));
            }
            if($Balance['account_balance']<trim(I('apply_money'))){
                $this->ajaxReturn(array('msg'=>'对不起，您的账户余额不足，请充值后，再操作！','status'=>$status));
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
                }else {
                    $fileinfo = $this->business_licence_upload(C('PROXY_UPLOAD_DIR'));
                    if($fileinfo['file']){
                        $icense_img = substr(C('UPLOAD_DIR').$fileinfo['file']['savepath'].$fileinfo['file']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['file']['savepath'].$fileinfo['file']['savename'])-1);
                    }else{
                        $msg = $this->business_licence_upload_Error['file'];
                        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                    }
                }
                $enterprise_id=trim(I('enterprise_id'));
                //$map['enterprise_id']=$enterprise_id;
                //$number=M('enterprise')->where($map)->field('enterprise_code')->find();
                //$top_proxy_id=D('SysUser')->up_proxy_info();
                $data['top_proxy_id']=D('SysUser')->self_top_proxy_id($enterprise_id);
                $con['proxy_id']= $data['top_proxy_id'];
                $info=M('proxy')->where($con)->field('proxy_type')->find();
                $data['enterprise_id']=$enterprise_id;
                $data['create_user_id']=D('SysUser')->self_id();

                $apply_code="CZSQD".date('Ymd',time());
                $app['apply_code']=array('like',$apply_code.'%');
                $applys=M('enterprise_recharge_apply')->where($app)->order("apply_code desc")->find();
                $applys=substr($applys['apply_code'],13);
                $applys=$applys+1;
                $data['apply_code']=generate_order($applys,1);

                $data['apply_money']=trim(I('apply_money'));
                $data['credential_one']=$icense_img;
                $data['approve_status']=2;
                $transaction_name='';
                $proxy_type=D('EnterpriseAccount')->proxy_type();
                 if(I("apply_type")==1){
                    $data['source']=trim(I('source'));
                    $data['transaction_number']=trim(I('transaction_number'));
                    $data['transaction_date']=trim(I('transaction_date'));
                    $title='正常充值';
                     if($proxy_type==1){
                         $transaction_name='，付款方式【'.get_source_name($data['source']).'】，'.get_transaction_name($data['source']).'【'.$data['transaction_number'].'】，付款日期【'. $data['transaction_date'].'】';
                     }else{
                         $transaction_name='，付款方式【'.get_source_info($data['source']).'】，'.get_transaction_name($data['source']).'【'.$data['transaction_number'].'】，付款日期【'. $data['transaction_date'].'】';
                     }
                }else{
                     $data['source']=0;
                     $title='测试款';
                }
                $data['remark']=trim(I('remark'));
                $data['create_date']=date('Y-m-d H:i:s',time());
                $data['modify_date']=date('Y-m-d H:i:s',time());
                $data['apply_type']=trim(I('apply_type'));
                $res=M('enterprise_recharge_apply')->add($data);
                //执行添加
                if($res){
                    $msg = '新增充值申请单成功';
                    $status = 'success';
                    $n_msg='成功';
                }else{
                    $msg = '新增充值申请单失败';
                    $n_msg='失败';
                }
                $note = "用户【".get_user_name(D('SysUser')->self_id())."】，ID【".$res."】，新增充值申请单，企业【".obj_name($enterprise_id,2)."】，申请编号【". $data['apply_code']."】，充值类型【".$title."】，充值金额【".money_format2(trim(I('apply_money')))."】元".$transaction_name.$n_msg;
                $this->sys_log('新增企业充值申请',$note);
            }
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));

        }
    }


    /*代理商充值功能*/
    public function insert(){
        $msg = '系统错误!';
        $status = 'error';
        $data = array();
        if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', I('apply_money'))){
            $this->ajaxReturn(array('msg'=>'请输入正确的付款金额！','status'=>$status));
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
            }else {
                $fileinfo = $this->business_licence_upload(C('PROXY_UPLOAD_DIR'));
                if($fileinfo['file']){
                    $icense_img = substr(C('UPLOAD_DIR').$fileinfo['file']['savepath'].$fileinfo['file']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['file']['savepath'].$fileinfo['file']['savename'])-1);
                }else{
                    $msg = $this->business_licence_upload_Error['file'];
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                }
            }
            $enterprise_id=D('SysUser')->self_enterprise_id();
            //$map['enterprise_id']=D('SysUser')->self_enterprise_id();
            //$number=M('enterprise')->where($map)->field('enterprise_code')->find();
            //$top_proxy_id=D('SysUser')->up_proxy_info();
            $data['top_proxy_id']=D('SysUser')->self_top_proxy_id(D('SysUser')->self_enterprise_id());
            $con['proxy_id']= $data['top_proxy_id'];
           // $info=M('proxy')->where($con)->field('proxy_type')->find();
            $data['enterprise_id']=$enterprise_id;
            $data['create_user_id']=D('SysUser')->self_id();

            $apply_code="CZSQD".date('Ymd',time());
            $app['apply_code']=array('like',$apply_code.'%');
            $applys=M('enterprise_recharge_apply')->where($app)->count();
            $applys=$applys+1;   
            $data['apply_code']=generate_order($applys,1);

            $data['apply_money']=trim(I('apply_money'));
            $data['credential_one']=$icense_img;
            $transaction_name='';
            $proxy_type=D('EnterpriseAccount')->top_proxy_type();
            if(I("apply_type")==1){
                $data['source']=trim(I('source'));
                $data['transaction_number']=trim(I('transaction_number'));
                $data['transaction_date']=trim(I('transaction_date'));
                $title='正常充值';
                //$transaction_name='，'.get_transaction_name(trim(I('source'))).'【'. trim(I('transaction_number')).'】'.'，充值时间【'.trim(I('transaction_date')).'】';
                if($proxy_type==1){
                    $transaction_name='，付款方式【'.get_source_name($data['source']).'】，'.get_transaction_name($data['source']).'【'.$data['transaction_number'].'】，付款日期【'. $data['transaction_date'].'】';
                }else{
                    $transaction_name='，付款方式【'.get_source_info($data['source']).'】，'.get_transaction_name($data['source']).'【'.$data['transaction_number'].'】，付款日期【'. $data['transaction_date'].'】';
                }
            }else{
                $data['source']=0;
                $title='测试款';

            }
            $data['approve_status']=1;
            $data['remark']=trim(I('remark'));
            $data['create_date']=date('Y-m-d H:i:s',time());
            $data['modify_date']=date('Y-m-d H:i:s',time());
            $data['apply_type']=trim(I('apply_type'));
            $res=M('enterprise_recharge_apply')->add($data);
            //执行添加
            if($res){
                $msg = '新增充值申请单成功';
                $status = 'success';
                $n_msg='成功';
            }else{
                $msg = '新增充值申请单失败';
                $n_msg='失败';
            }
            $note = "用户【".get_user_name(D('SysUser')->self_id())."】，ID【".$res."】，新增充值申请单，企业【".obj_name($enterprise_id,2)."】，申请编号【". $data['apply_code']."】，充值类型【".$title."】，充值金额【".trim(I('apply_money'))."】元".$transaction_name.$n_msg;
            $this->sys_log('新增企业充值申请单',$note);
        }
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'info'=>$res));

    }


    /*修改代理商给企业充值的信息*/
    public function edit_proxy(){
        $operate=trim(I('get.operate'));
        if($operate!='update'){
            $where['apply_id']=trim(I('apply_id'));
            $list=D('EnterpriseAccount')->recharge_apply_detailed($where);
            if(empty($list)){
                $this->ajaxReturn(array('msg'=>'查询失败','status'=>'error'));
            }
         /*   $ids=D("Enterprise")->enterprise_ids();//获取当前代理商下可操作企业
            $map['enterprise_id']=array("in",$ids);*/
            //$map['enterprise_id'] = array(array('in',D('Enterprise')->enterprise_child_ids()),array('in',D('Enterprise')->enterprise_ids()),'or') ;
            $ids = D('Enterprise')->enterprise_ids();//获取当前代理商下可操作企业
            $enterprise_child_ids_array = explode(',',$ids);
            $map['enterprise_id']=array("in",$enterprise_child_ids_array);
            $map['approve_status']=1;
            $map['status']=1;
            $enterprise_list=M('enterprise')->where($map)->field('enterprise_name,enterprise_id')->select();
            $this->assign('enterprise',$enterprise_list);
            $this->assign('source_name',get_source_name());
            $this->assign('proxy_type', D('EnterpriseAccount')->proxy_type());
            $this->assign($list);
            $this->display();
        }else{
            $msg = '系统错误!';
            $status = 'error';
            $data = array();
            $apply_id=trim(I('apply_id'));
            $con['proxy_id']=D('SysUser')->self_proxy_id();
            $info=M('proxy')->where($con)->field('proxy_type')->find();
            $Balance=M('proxy_account')->where($con)->find();
            $result=M('enterprise_recharge_apply')->where('apply_id='.$apply_id);
            if(empty($result)){
                $this->ajaxReturn(array('msg'=>'查询失败','status'=>$status));
            }
            if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', trim(I('apply_money')))){
                $this->ajaxReturn(array('msg'=>'请输入正确的付款金额！','status'=>$status));
            }
            if($Balance['account_balance']<trim(I('apply_money'))){
                $this->ajaxReturn(array('msg'=>'对不起，您的账户余额不足，请充值后，再操作！','status'=>$status));
            }
            $apply_money=trim(I('apply_money'));
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
                if($_FILES['file']['name']==null || $_FILES['file']['name']==""){
                    $icense_img=trim(I('images'));
                }else {
                    $fileinfo = $this->business_licence_upload(C('PROXY_UPLOAD_DIR'));
                    if($fileinfo['file']){
                        $icense_img = substr(C('UPLOAD_DIR').$fileinfo['file']['savepath'].$fileinfo['file']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['file']['savepath'].$fileinfo['file']['savename'])-1);
                    }else{
                        $msg = $this->business_licence_upload_Error['file'];
                        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                    }
                }

                $data['apply_money']=$apply_money;
                $data['credential_one']=$icense_img;
                $transaction_number=trim(I('transaction_number'));
                $transaction_date=trim(I('transaction_date'));
                $source=trim(I('source'));
                $apply_type=I("apply_type");
                if($apply_type==1){
                    $data['source']=$source;
                    $data['transaction_number']=$transaction_number;
                    $data['transaction_date']=$transaction_date;
                    $title='正常充值';
                }else{
                    $title='测试款';
                    $data['source']=0;
                    $data['transaction_number']=null;
                    $data['transaction_date']=null;
                }
                $data['approve_status']=2;
                $data['remark']=trim(I('remark'));
                $data['create_date']=date('Y-m-d H:i:s',time());
                $data['modify_user_id']=D('SysUser')->self_id();
                $data['modify_date']=date('Y-m-d H:i:s',time());
                $where['apply_id']=$apply_id;
                $data['apply_type']=trim(I('apply_type'));
                $apply_code=M('enterprise_recharge_apply')->where($where)->find();
                $res=M('enterprise_recharge_apply')->where($where)->save($data);

                if($res>0){
                    $msg = '编辑充值申请单成功';
                    $status = 'success';
                    $n_msg='成功';
                }else if($res=0){
                    $msg = '没有编辑内容！';
                    $n_msg='失败';
                }else{
                    $msg = '编辑充值申请单失败';
                    $n_msg='失败';
                }
               $c_item='';
               $c_item.='，充值类型【'.$title.'】';
               $proxy_type= D('EnterpriseAccount')->proxy_type();
               if($source!==$apply_code['source']){
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
                       $fg=!empty($c_item)?'，':'';
                       $c_item.=$fg.'付款方式【'.get_source_info($source).'】';
                       $fg=!empty($c_item)?'，':'';
                       $c_item.=$transaction_number===$apply_code['transaction_number']?'':$fg.get_transaction_name($source).'【'. $transaction_number.'】';
                       $fg=!empty($c_item)?'，':'';
                       $c_item.=strtotime($transaction_date)===strtotime($apply_code['transaction_date'])?'':$fg.'付款日期【'. $transaction_date.'】';
                   }
               }
               $fg=!empty($c_item)?'，':'';
               $c_item.=$apply_money*100===$apply_code['apply_money']*100?'':$fg.'付款金额【'.$apply_money.'】元';

               $note = "用户【".get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，编辑充值申请单，企业【".obj_name($apply_code['enterprise_id'],2)."】，申请编号【".$apply_code['apply_code']."】".$c_item.$n_msg;
               $this->sys_log('编辑企业充值申请单',$note);
            }
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }

    /*代理商端送审*/
    public function send_approve_proxy(){
        $msg = '系统错误!';
        $status = 'error';
        $apply_id=I('id');
        $where['ea.apply_id']=$apply_id;
        $list=D('EnterpriseAccount')->recharge_apply_detailed($where);
        if(empty($list)){
            $this->ajaxReturn(array('msg'=>'查询失败','status'=>$status));
        }
        $where['apply_id'] = $apply_id;
        $data['approve_status'] = 2;
        $res = M('enterprise_recharge_apply')->where($where)->save($data);
        if ($res > 0) {
            $msg = '充值申请单提交成功！';
            $status = 'success';
            $n_msg='提交审核成功';
        } else {
            $msg = '充值申请单提交失败!';
            $n_msg='提交审核失败';
        }
        $note = "用户【".get_user_name(D('SysUser')->self_id())."】，ID【".$list['apply_id']."】，企业【".obj_name($list['enterprise_id'],2)."】，充值申请单【". $list['apply_code']."】提交审核".$n_msg;
        $this->sys_log('充值申请单提交审核',$note);
        $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
    }


    /*修改充值申请*/
    public function edit(){
        $msg = '系统错误!';
        $status = 'error';
        $data = array();
        $operate=trim(I('get.operate'));
        if(trim(I('get.operates'))=='update') {
            $is_apply=D('EnterpriseAccount')->all_recharge_apply();
            if($operate=='sentHear') {
                $apply_id=I('id');
                if(!in_array2($apply_id,$is_apply)){
                    $this->ajaxReturn(array('msg'=>'查询失败','status'=>$status));
                }
                $where['apply_id'] = $apply_id;
                $data['approve_status'] = 2;
                $res = M('enterprise_recharge_apply')->where($where)->save($data);
                if ($res > 0) {
                    $msg = '充值申请单提交审核成功！';
                    $status = 'success';
                    $n_msg='提交审核成功';
                } else {
                    $msg = '充值申请单提交审核失败!';
                    $n_msg='提交审核失败';
                }
                $info=M('enterprise_recharge_apply')->where('apply_id='.$apply_id)->field('apply_code')->find();
                $note = "用户【".get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，充值申请单【".$info['apply_code']."】".$n_msg;
                $this->sys_log('充值申请单提交审核',$note);
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
            }else if($operate=='delete'){
                $where['apply_id'] = I('id');
                $list = M('enterprise_recharge_apply')->where($where)->find();
                if(empty($list)){
                    $this->ajaxReturn(array('msg'=>'对不起，没有找到相关内容，请重试！','status'=>$status));
                }
                if(in_array($list['approve_status'],array(3,5))){
                    $this->ajaxReturn(array('msg'=>'已经审核通过，请勿删除！','status'=>$status));
                }
                $res = M('enterprise_recharge_apply')->where($where)->delete();
                if($res){
                    $msg = '删除充值申请单成功！';
                    $status = 'success';
                    $n_msg='成功';
                }else{
                    $msg = '删除充值申请单失败！';
                    $n_msg='失败';
                }
                $note = "用户【".get_user_name(D('SysUser')->self_id())."】，ID【".$list['apply_id']."】，删除企业充值申请单【".$list['apply_code']."】".$n_msg;
                //$note = "用户【".get_user_name(D('SysUser')->self_id())."】,删除充值申请单【".$list['apply_code']."】".$n_msg;
                $this->sys_log('删除企业充值申请单',$note);
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
            }else{
                $apply_id=trim(I('apply_id'));
                if(!in_array2($apply_id,$is_apply)){
                    $this->ajaxReturn(array('msg'=>'查询失败','status'=>$status));
                }
                $apply_money=trim(I('apply_money'));
                if($apply_money==""){
                    $msg = '请输入付款金额！';
                }elseif($apply_money<=0){
                    $msg = '付款金额不能小于等于0！';
                }else if(I('source')==0 && I("apply_type")==1){
                    $msg = '请选择付款方式！';
                }elseif(I('transaction_date')=="" && I("apply_type")==1){
                    $msg = '请输入付款日期！';
                }elseif(I("transaction_number")=="" && I("apply_type")==1){
                    $msg = '请输入打款户名/支付订单号/交易号！';
                }else{
                    if($_FILES['file']['name']!=null || $_FILES['file']['name']!="") {
                        $fileinfo = $this->business_licence_upload(C('PROXY_UPLOAD_DIR'));
                        if ($fileinfo['file']) {
                            $icense_img = substr(C('UPLOAD_DIR') . $fileinfo['file']['savepath'] . $fileinfo['file']['savename'], 1, strlen(C('UPLOAD_DIR') . $fileinfo['file']['savepath'] . $fileinfo['file']['savename']) - 1);
                            $data['credential_one'] = $icense_img;
                        } else {
                            $msg = $this->business_licence_upload_Error['file'];
                            $this->ajaxReturn(array('msg' => $msg, 'status' => $status, 'data' => $data));
                        }
                    }
                    $data['apply_money']=$apply_money;

                    $data['approve_status']=1;
                    $data['remark']=trim(I('remark'));
                    $source=trim(I('source'));
                    $transaction_date=trim(I('transaction_date'));
                    $transaction_number=trim(I('transaction_number'));
                    if(I("apply_type")==1){
                        $data['source']=$source;
                        $data['transaction_number']=$transaction_number;
                        $data['transaction_date']=$transaction_date;
                        $title='正常充值';
                    }else{
                        $title='测试款';
                        $data['source']=0;
                        $data['transaction_number']=null;
                        $data['transaction_date']=null;
                    }
                    $data['create_date']=date('Y-m-d H:i:s',time());
                    $data['modify_user_id']=D('SysUser')->self_id();
                    $data['modify_date']=date('Y-m-d H:i:s',time());
                    $where['apply_id']=$apply_id;
                    $data['apply_type']=trim(I("apply_type"));
                    $apply_code=M('enterprise_recharge_apply')->where($where)->find();
                    $info=M('proxy')->where($con)->field('proxy_type')->find();
                    $res=M('enterprise_recharge_apply')->where($where)->save($data);

                    if($res>0){
                        $msg = '编辑充值申请单成功';
                        $status = 'success';
                        $n_msg='成功';
                    }else if($res=0){
                        $msg = '没有编辑内容！';
                        $n_msg='失败';
                    }else{
                        $msg = '编辑充值申请单失败';
                        $n_msg='失败';
                    }
                    $c_item='';
                    $c_item.='，充值类型【'.$title.'】';
                    $proxy_type=D('EnterpriseAccount')->top_proxy_type();
                    if($source!==$apply_code['source']){
                        if(I("apply_type")==1){
                            $fg=!empty($c_item)?'，':'';
                            //$c_item.=$fg.'付款方式【'.get_source_info($source).'】';
                            $c_item.=$proxy_type==1?$fg.'付款方式【'.get_source_name($source).'】':$fg.'付款方式【'.get_source_info($source).'】';
                            $fg=!empty($c_item)?'，':'';
                            $c_item.=$fg.get_transaction_name($source).'【'. $transaction_number.'】';
                            $fg=!empty($c_item)?'，':'';
                            $c_item.=$fg.'付款日期【'. $transaction_date.'】';
                        }
                    }else{
                        if(I("apply_type")==1){
                            if($source!==$apply_code['source']){
                                $fg=!empty($c_item)?'，':'';
                                $c_item.=$proxy_type==1?$fg.'付款方式【'.get_source_name($source).'】':$fg.'付款方式【'.get_source_info($source).'】';
                            }
                            $fg=!empty($c_item)?'，':'';
                            $c_item.=$transaction_number==$apply_code['transaction_number']?'':$fg.get_transaction_name($source).'【'. $transaction_number.'】';
                            $fg=!empty($c_item)?'，':'';
                            $c_item.=strtotime($transaction_date)===strtotime($apply_code['transaction_date'])?'':$fg.'付款日期【'. $transaction_date.'】';
                        }
                    }
                    $fg=!empty($c_item)?'，':'';
                    $c_item.=$apply_money*100===$apply_code['apply_money']*100?'':$fg.'付款金额【'.$apply_money.'】元';

                    $note = "用户【".get_user_name(D('SysUser')->self_id())."】，ID【".$apply_id."】，编辑充值申请单，企业【".obj_name($apply_code['enterprise_id'],2)."】，申请编号【".$apply_code['apply_code']."】".$c_item.$n_msg;
                    $this->sys_log('编辑企业充值申请单',$note);
                }
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }


        }else{
            $where['ea.apply_id']=trim(I('apply_id'));
            $list=D('EnterpriseAccount')->recharge_apply_detailed($where);
            $this->assign($list);
            $this->assign('source_name',get_source_name());
            $this->assign('proxy_type', D('EnterpriseAccount')->top_proxy_type());
            $this->display();
        }

    }

    public function proxy_delete(){
            $msg = '系统错误!';
            $status = 'error';
            $apply_id=I('id');
            $where['ea.apply_id']=$apply_id;
            $list=D('EnterpriseAccount')->recharge_apply_detailed($where);
            if(empty($list)){
                $this->ajaxReturn(array('msg'=>'查询失败','status'=>$status));
            }
            if(in_array($list['approve_status'],array(3,5))){
                $this->ajaxReturn(array('msg'=>'已经审核通过，请勿删除！','status'=>$status));
            }
            $map['apply_id'] = I('id');
            $res = M('enterprise_recharge_apply')->where($map)->delete();
            if($res){
                $msg = '删除充值申请单成功！';
                $n_msg='成功';
                $status = 'success';
            }else{
                $msg = '删除充值申请单失败！';
                $n_msg='失败';
            }
            $note = "用户【".get_user_name(D('SysUser')->self_id())."】，ID【".$list['apply_id']."】，删除企业充值申请单【".$list['apply_code']."】".$n_msg;
            $this->sys_log('删除企业充值申请单',$note);
            $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
    }


    /*详情*/
    public function show(){
        $msg = '系统错误！';
        $status = 'error';
        if(I('download')){
            $where['apply_id'] = trim(I('get.apply_id'));
            $enterprise = M('enterprise_apply')->where($where)->find();
            parent::download('.'.$enterprise['credential']);
        }else{
            $user=D('SysUser')->self_user_type();
            $where['ea.apply_id']=trim(I('apply_id'));
            $list=D('EnterpriseAccount')->recharge_apply_detailed($where);
            $map['apply_id']=trim(I('apply_id'));
            $process=D('EnterpriseAccount')->recharge_process($map);//提现状态
            $this->assign('process',$process);
            $this->assign($list);
            $this->assign('usr_type',$user);
            $this->display('detailed');
        }
    }

    /*弹出审核界面*/
    public function  approve_c(){
        $msg = '系统错误！';
        $status = 'error';
        if(I('download')){

            $where['apply_id'] = array('eq',trim(I('get.apply_id')));
            $enterprise = M('enterprise_recharge_apply')->where($where)->find();
            parent::download('.'.$enterprise['credential_one']);
        }else{
            $operate=trim(I('operate'));
            $list=D('EnterpriseAccount')->detailed();
            if($list['approve_status']==3 || $list['approve_status']==4 || $list["approve_status"]>4){
                $this->ajaxReturn(array('msg'=>"请勿重复审核！",'status'=>$status));
            }
            $this->assign("list",$list);
            $this->assign("type","1");
            $this->display('approve');
        }
    }

    /*弹出复审界面*/
    public function  approve_t(){
        $msg = '系统错误！';
        $status = 'error';
        if(I('download')){
            $where['apply_id'] = array('eq',trim(I('get.apply_id')));
            $enterprise = M('enterprise_recharge_apply')->where($where)->find();
            parent::download('.'.$enterprise['credential']);
        }else{
            $operate=trim(I('operate'));
            $list=D('EnterpriseAccount')->detailed();
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

    /*审核的方法*/
      public function  enterprise_approve(){
          $msg = '系统错误';
          $status = 'error';
          $where['apply_id']=trim(I('apply_id'));
          $model=M('enterprise_recharge_apply');
          $model->startTrans();
          $approve_status=trim(I('approve_status'));
          $type=trim(I("type"));
          if($approve_status==1){
            //审核过程表状态
            $erp['approve_status']=1;
            //初审通过
            if(I("type")==1){
              //审核过程表记录阶段
              $erp['approve_stage']=1;
              //审核信息表状态 
              $data['approve_status']=3;
            //复审通过
            }else{
              //审核过程表记录阶段
              $erp['approve_stage']=2;

              $proxyw= M('enterprise_recharge_apply')->where($where)->find();
              if($proxyw['approve_status']==2 || $proxyw['approve_status']==4 ){
                  $this->ajaxReturn(array('msg'=>"请等待初审完成在进行复审！",'status'=>$status));
              }
              if($proxyw['approve_status']==5 || $proxyw['approve_status']==6 ){
                  $this->ajaxReturn(array('msg'=>"请勿重复审核！",'status'=>$status));
              }
              //审核信息表状态
              $data['approve_status']=5;
              $apply=M("enterprise_recharge_apply")->where($where)->field("apply_money,apply_code")->find();
              if(I("get.tran")){
                $da['apply_id']=trim(I("apply_id"));
                $da['apply_type']=trim(I("apply_type"));
                $da['approve_status']=trim(I('approve_status'));
                $da['enterprise_id']=trim(I('enterprise_id'));
                $enterprise=M("enterprise")->where(array('enterprise_id'=>$da['enterprise_id']))->field("enterprise_name")->find();
                //$msg="确定是否向【".$enterprise['enterprise_name']."】充值".$apply['apply_money']."元？";
                $title="";
                if(trim(I('apply_type'))==2){
                    $title='测试款';
                    $da['title']='测试款';
                }
                $msg="确定是否复审通过并向【".$enterprise['enterprise_name']."】充值".$title.$apply['apply_money']."元？";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>'success','info'=>$da));
              }
              $map['enterprise_id']=trim(I('enterprise_id'));
              $account=M("enterprise_account")->lock(true)->where($map)->field("account_balance,account_id,freeze_money")->find();
              //以下是记录账单流水的
              $where['proxy_id']=D('SysUser')->self_proxy_id();
              $Balance=M('proxy_account')->lock(true)->where($where)->find();
              if($Balance['account_balance']<$apply['apply_money']){
                $msg="对不起，您的账户余额不足，请充值后，再操作！";
                  $model->rollback();
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
              }
              $condition['top_account_id']=$Balance['account_id']; //上级代理上账户id
              $condition['top_account_balance']=$Balance['account_balance']; //上级代理上账户余额
              $condition['top_operate_type']=4; //充值-上级代理商
              $condition['top_balance_type']=2;//支出-上级代理商
              $condition['top_user_type']=1;
              $condition['apply_money']=$apply['apply_money'];   //需要操作的金额
              $condition['operate_account_id']=$account['account_id'];//收入-下级企业
              $condition['operate_account_balance']=$account['account_balance'];//要操作的企业账户余额
              $condition['operate_enterprise_id']=trim(I('enterprise_id')); //要操作的企业账户ID
              if(trim(I("apply_type"))==2){
                $condition['operate_operate_type']=8; //测试-下级代理商
              }else{
                $condition['operate_operate_type']=2; //充值-下级代理商
              }   
              $condition['operate_balance_type']=1;//收入-下级代理商
              $condition['operate_user_type']=2;
              if(!(D('EnterpriseAccount')->account_record($condition)>0)){
                $msg ='审核失败！';
                $model->rollback();
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
              }
                /*$note = "用户【".get_user_name(D('SysUser')->self_id())."】向企业【".obj_name(trim(I('enterprise_id')),2)."】账户充值".trim(I('title')).$apply['apply_money']."元";
                $this->sys_log('充值申请成功后打款',$note);*/

            }
          }else{
              if(trim(I('approve_remark'))==""){
                  $this->ajaxReturn(array('msg'=>'请填写审核驳回原因','status'=>$status));
              }
              //审核过程表状态
              $erp['approve_status']=2;

              if(I("type")==1){
                //审核过程表记录阶段
                $erp['approve_stage']=1;

                //审核信息表状态
                $data['approve_status']=4;
              }else{
                //审核过程表记录阶段
                $erp['approve_stage']=2;

                //审核信息表状态
                $data['approve_status']=6;
              }
          }
          //审核过程记录表数据
          $erp['apply_id']=trim(I('apply_id'));
          $erp['approve_user_id']=D('SysUser')->self_id();
          $erp['approve_date']=date('Y-m-d H:i:s',time());
          $erp['approve_remark']=trim(I("approve_remark"));
          $res=M("enterprise_recharge_process")->add($erp);
          $apply_code=M('enterprise_recharge_apply')->where('apply_id='.trim(I('apply_id')))->field('apply_code,apply_money,enterprise_id')->find();
          //修改审核记录表
          $data['apply_id']=trim(I('apply_id'));
          $data['last_approve_date']=date('Y-m-d H:i:s',time());
          $data['modify_user_id']=D('SysUser')->self_id();
          $data['modify_date']=date('Y-m-d H:i:s',time());
          $apply_res=$model->save($data);
          if($approve_status==1){
              if($apply_res>0 && $res>0){
                  $model->commit();
                  if($data['approve_status']==3){
                      $msg = '充值申请单初审成功！';
                      $n_msg='初审成功';
                      $note_type='充值申请单初审';
                  }else if($data['approve_status']==5){
                      $msg = '充值申请单复审成功！';
                      $n_msg='复审成功';
                      $note_type='充值申请单复审';
                      $this->send_recharge(3,$apply_code['enterprise_id'],$apply_code['apply_money'],$account['account_balance']+$apply_code['apply_money']);
                      $success_msg="，并给企业【".obj_name($apply_code['enterprise_id'],2)."】账户打款，打款金额【".$apply_code['apply_money']."】元";
                  }
                  $status = 'success';
              }else{
                  if($data['approve_status']==3){
                      $msg = '充值申请单初审失败！';
                      $n_msg='初审失败';
                      $note_type='企业充值申请单初审';
                  }else if($data['approve_status']==5){
                      $msg = '充值申请单复审失败！';
                      $n_msg='复审失败';
                      $note_type='企业充值申请单复审';
                  }
                  $model->rollback();
              }
          }else{
              if($apply_res>0 && $res>0){
                  $model->commit();
                  if($data['approve_status']==4){
                      $msg = '充值申请单初审驳回成功！';
                      $n_msg='初审驳回成功';
                      $note_type='企业充值申请单初审';
                  }else if($data['approve_status']==6){
                      $msg = '充值申请单复审驳回成功！';
                      $n_msg='复审驳回成功';
                      $note_type='企业充值申请单复审';
                  }

                  $status = 'success';
              }else{
                  if($data['approve_status']==4){
                      $msg = '充值申请单初审驳回失败！';
                      $n_msg='初审驳回失败';
                      $note_type='企业充值申请单初审';
                  }else if($data['approve_status']==6){
                      $msg = '充值申请单复审驳回失败！';
                      $n_msg='复审驳回失败';
                      $note_type='企业充值申请单复审';
                  }
                  $model->rollback();
              }
          }
          $note = "用户【".get_user_name(D('SysUser')->self_id())."】，ID【".$erp['apply_id']."】，企业充值申请单【".$apply_code['apply_code']."】".$n_msg.$success_msg;
          $this->sys_log($note_type,$note);
          $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));

    }

    public function export_excel(){
        $user=D('SysUser')->self_user_type();//获取自身的用户类型是运营平台，代理商，企业
        $enterprise_name = trim(I('enterprise_name')); //代理商名称
        //$source = trim(I('get.source')); //来源
        //$apply_type = trim(I('get.apply_type'));  //操作方式
        $start_datetime = trim(I('start_datetime'));   //开始时间
        $end_datetime = trim(I('end_datetime'));   //结束时间
        $approve_status=trim(I('approve_status'));   //审核状态
        $proxy_code=trim(I('proxy_code'));
        $proxy_name=trim(I('proxy_name'));
        $enterprise_code=trim(I('enterprise_code'));
        $apply_code = trim(I('apply_code'));  //操作方式
        $source=I('source');
        $where=array();
        if($enterprise_code){
            $where['e.enterprise_code']=array('like','%'.$enterprise_code.'%');
        }
        if($enterprise_name) {
            $where['e.enterprise_name'] = array('like', '%' . $enterprise_name . '%');
        }
            if($source){
                $where['ea.source'] = $source;
            }
            if($user==1){
            if($proxy_code){
                $where['up.proxy_code']=array('like','%'.$proxy_code.'%');
            }
            if($proxy_name){
                $where['up.proxy_name']=array('like','%'.$proxy_name.'%');
            }
        }
        if($user==3){
            if($approve_status!="" && $approve_status!=9){
                $where['ea.approve_status'] = $approve_status;
            }
        }else{
            if($approve_status!=""){
                if($approve_status!=9){
                    $where['ea.approve_status'] = $approve_status;
                }else{
                    $where['ea.approve_status']=array('neq',1);
                }
            }else{
                $where['ea.approve_status']=array('neq',1);
            }
        }
        if($apply_code){
            $where['ea.apply_code'] = array('like','%'.$apply_code.'%');
        }
        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['ea.create_date'] =array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['ea.create_date'] = array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['ea.create_date'] =array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $where['ea.create_date'] = array('between',array($start_datetime,$end_datetime));
        }
        if($user==3){
            $where['e.enterprise_id'] = D('SysUser')->self_enterprise_id();
        }else{
            $enterprise_child_ids=D('Enterprise')->enterprise_child_ids();
            $enterprise_ids=D('Enterprise')->enterprise_ids();
            $where['e.enterprise_id'] = array(array('in',$enterprise_child_ids),array('in',$enterprise_ids),'or') ;
        }
        $apply_type = trim(I('get.apply_type'));
        if(!empty($apply_type)){
            $where['ea.apply_type'] = $apply_type;
        }
        $list=D('EnterpriseAccount')->enterprise_apply_excel($where);
        $data=array();
        foreach ($list as $v) {
           $rech=array();
           if($user!=3){
              $rech['enterprise_code']=$v['enterprise_code'];
              $rech['enterprise_name']=$v['enterprise_name'];
           }
            if($user==1){
                $rech['proxy_code']=$v['proxy_code'];
                $rech['proxy_name']=$v['proxy_name'];
            }
           $rech['apply_code']=$v['apply_code'];
           $rech['apply_money']=$v['apply_money'];
           $rech['source'] = '--';
            if($v['source']!=0){
              if($v['proxy_type']==1){
                $rech['source']=get_source_name($v['source']);
              }else{
                if($v['source']==1){
                    $rech['source']="汇款";
                }elseif($v['source']==2){
                    $rech['source']="微信支付";
                }else{
                    $rech['source']="支付宝支付";
                }   
              }
           }
            if($user==2){
                $rech['transaction_number']=$v['transaction_number'];
            }

            if($v['apply_type'] == 1){
                $rech['apply_type'] = '正常充值';
            }elseif($v['apply_type'] == 2){
                $rech['apply_type'] = '测试款';
            }else{
                $rech['apply_type'] = '--';
            }
          if($user!=3){
              $rech['approve_man']=get_approve_people($v['apply_id'],2);
          }
          $rech['transaction_date']=empty($v['transaction_date'])?'--':$v['transaction_date'];
          $rech['approve_status']=get_apply_status($v['approve_status']);
          $rech['create_date']=$v['create_date'];
          array_push($data,$rech);
        }
        if($user==3){
          $headArr =array("申请编号","付款金额(元)","付款方式","充值类型","付款日期","审核状态","申请时间");
        }else if($user==2){
          $headArr =array("企业编号","企业名称","申请编号","付款金额(元)","付款方式","交易号/户名","充值类型","付款日期","复审人","审核状态","申请时间");
        }else{
            $headArr =array("企业编号","企业名称","代理商编号","代理商名称","申请编号","付款金额(元)","付款方式","充值类型","付款日期","复审人","审核状态","申请时间");
        }
       if($user==3){
           $title='账户充值申请';
       }  else{
           $title='企业充值管理';
       }
        ExportEexcel($title,$headArr,$data);
    }

}
?>