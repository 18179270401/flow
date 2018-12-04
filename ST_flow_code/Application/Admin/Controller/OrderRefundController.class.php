<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class OrderRefundController extends CommonController{
	/*
	 * 订单退款表
	 */
        public function index(){
            set_time_limit(0);
            D("SysUser")->sessionwriteclose();
            $start_datetime = I('start_datetime');
            $end_datetime = I('end_datetime');
            //判断时间是否在一个月内
            if($start_datetime!="" && $end_datetime!=""){
                if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400){
                    $this->display('index');
                    echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过31天！'});history.back(); </script>";exit;
                }
            }
            $start_o_date = I('start_o_date');
            $end_o_date = I('end_o_date');
            if( $start_o_date!="" && $end_o_date!=""){
                $e_d=date("Ym",strtotime($end_o_date));
                $s_d=date("Ym",strtotime($start_o_date));
                if($e_d!=$s_d){
                    $this->display('index');
                    echo "<script>alertbox({'status':'error','msg':'查询时间需在同一个月！'});history.back(); </script>";exit;
                }
            }
            if($start_datetime or $end_datetime){
                if($start_datetime && $end_datetime){
                    $map['od.create_date'] =array('between',array(start_time($start_datetime),end_time($end_datetime)));
                }elseif($start_datetime){
                    $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                    $map['od.create_date'] = array('between',array(start_time($start_datetime),$end_datetime));
                }elseif($end_datetime){
                    $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                    $map['od.create_date'] =array('between',array($start_datetime,end_time($end_datetime)));
                }
            }else{
               /* $end_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $s_datetime= strtotime($end_datetime)-2592000;
                $start_datetime=start_time(date('Y-m-d',$s_datetime));
                $d_sdata=$start_datetime;
                $d_edata=$end_datetime;
                $map['od.create_date']= array('between',array($start_datetime,$end_datetime));*/
            }
            //加上充值时间搜索
            if($start_o_date && $end_o_date){
                    $map['o.order_date'] =array('between',array(start_time($start_o_date),end_time($end_o_date)));
            }elseif($start_o_date){
                $end_o_date = end_time(msubstr($start_o_date,0,10,"utf-8",false));
                $map['o.order_date'] = array('between',array(start_time($start_o_date),$end_o_date));
            }elseif($end_o_date){
                $start_o_date = start_time(msubstr($end_o_date,0,10,"utf-8",false));
                $map['o.order_date'] =array('between',array($start_o_date,end_time($end_o_date)));
            }else{
                /*$start_o_date = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
                $end_o_date = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
                $map['o.order_date']= array('between',array($start_o_date,end_time($end_o_date)));*/
            }
            if($start_o_date){
                $this->assign('start_o_date',$start_o_date);
                $this->assign('end_o_date',$end_o_date);
                $d=date("Ym", strtotime("-2 month"));
                $d2=date("Ym",strtotime($start_o_date));
                if($d2>$d){
                    $table="order";
                }else{
                    $rs = M()->query("SHOW TABLES LIKE 't_flow_order_".$d2."'");
                    if(!$rs){
                        $table="order";
                    }else{
                        $table="order_".$d2;
                    }
                }
            }else{
                $table="order";
            }
            $user_type=D('SysUser')->self_user_type();
            $self_enterprise_id = D('SysUser')->self_enterprise_id();
            $self_proxy_id=D('SysUser')->self_proxy_id();//当前代理商
            $refund_status = 1;
            if($user_type==3){
                $map['od.enterprise_id'] = array('in',$self_enterprise_id);
                $map['od.user_type'] =2;
                $refund_status = M('enterprise')->where("enterprise_id = $self_enterprise_id")
                    ->getField('refund_status');
            }else if($user_type==2){
                $map['od.proxy_id']=D('SysUser')->self_proxy_id();
                $map['od.user_type'] =1;
                $refund_status = M('proxy')->where("proxy_id = $self_proxy_id")
                    ->getField('refund_status');
            }else{
                $os_proxy_ids = $self_proxy_id.','.D('Proxy')->proxy_child_ids();
                $os_enterprise_ids = D('Enterprise')->enterprise_child_ids();
                if($os_proxy_ids!='' && $os_enterprise_ids!=''){
                    $map1['od.proxy_id']=array('in',$os_proxy_ids);
                    $map1['od.enterprise_id']=array('in',$os_enterprise_ids);
                    $map1['_logic'] = 'or';
                    $map['_complex'] = $map1;
                }else {
                    if($os_proxy_ids!=''){
                        $map['od.proxy_id']=array('in',$os_proxy_ids);
                    }else if($os_enterprise_ids!=''){
                        $map['od.enterprise_id']=array('in',$os_enterprise_ids);
                    }
                }
            }
            $mobile = trim(I('mobile'));
            $status =trim(I('status'));
            $user_name=trim(I('user_name'));
            if(!empty($user_name)){
                $map_2['r.proxy_name']=array('like','%'.$user_name.'%');
                $map_2['e.enterprise_name']=array('like','%'.$user_name.'%');
                $map_2['_logic'] = 'or';
                $map['_complex'] = $map_2;
            }
            if(!empty($mobile))$map['od.mobile'] = array("like","%{$mobile}%");
            if($status!='' ){
                $map['od.status']=$status;
            }

                $join = array(
                    C('DB_PREFIX').'proxy as r ON od.proxy_id=r.proxy_id',
                    C('DB_PREFIX').'enterprise as e ON od.enterprise_id=e.enterprise_id',
                    C('DB_PREFIX').$table. ' as o ON od.order_id=o.order_id',
                );
                $count      = M('order_refund as od')->where($map)->join($join,"left")->count();

                $Page       = new Page($count,20);
                $show       = $Page->show();
                //获取所有退款表
                $oder_list = M('order_refund as od')->where($map)->join($join,"left")->field("od.*,r.proxy_name,e.enterprise_name,o.channel_id as o_channel_id,o.channel_code,o.back_channel_id as o_back_channel_id,o.back_channel_code,o.order_status as o_order_status,o.order_date,o.order_code")->order('od.modify_date desc,od.status asc')->limit($Page->firstRow.','.$Page->listRows)->select();
                //var_dump($oder_list);
                //加载模板
                $oder_all_list = M('order_refund as od')->where($map)->join($join,"left")->field("sum(od.discount_price) as all_price,count(od.refund_code) as order_count")->select();
                $this->assign('oder_all_list',$oder_all_list[0]);
                $this->assign('order_list',get_sort_no($oder_list,$Page->firstRow));  //数据列表
                $this->assign('page',$show);
                $this->assign('usr_type',D('SysUser')->self_user_type());
                //$this->assign('d_sdata',$d_sdata);  //默认开始时间
                //$this->assign('d_edata',$d_edata);  //默认结束时间
                $this->assign('refund_status',$refund_status);  //是否可以退款
                $this->display();
}
/*导出功能还未完善，如果做完退款请修改下*/
    public function export_excel(){
        $start_datetime = I('start_datetime');
        $end_datetime = I('end_datetime');
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                $this->display('index');
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }
        $start_o_date = I('start_o_date');
        $end_o_date = I('end_o_date');
        if( $start_o_date!="" && $end_o_date!=""){
            $e_d=date("Ym",strtotime($end_o_date));
            $s_d=date("Ym",strtotime($start_o_date));
            if($e_d!=$s_d){
                $this->display('index');
                echo "<script>alertbox({'status':'error','msg':'查询时间需在同一个月！'});history.back(); </script>";exit;
            }
        }
        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $map['od.create_date'] =array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $map['od.create_date'] = array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $map['od.create_date'] =array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
           /* $end_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            $s_datetime= strtotime($end_datetime)-2592000;
            $start_datetime=start_time(date('Y-m-d',$s_datetime));
            $map['od.create_date']= array('between',array($start_datetime,$end_datetime));*/
        }

        //加上充值时间搜索
        $start_o_date = I('start_o_date');
        $end_o_date = I('end_o_date');
        //加上充值时间搜索
        if($start_o_date && $end_o_date){
            $map['o.order_date'] =array('between',array(start_time($start_o_date),end_time($end_o_date)));
        }elseif($start_o_date){
            $end_o_date = end_time(msubstr($start_o_date,0,10,"utf-8",false));
            $map['o.order_date'] = array('between',array(start_time($start_o_date),$end_o_date));
        }elseif($end_o_date){
            $start_o_date = start_time(msubstr($end_o_date,0,10,"utf-8",false));
            $map['o.order_date'] =array('between',array($start_o_date,end_time($end_o_date)));
        }else{
           /* $start_o_date = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_o_date = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $map['o.order_date']= array('between',array($start_o_date,end_time($end_o_date)));*/
        }
        if($start_o_date){
            $this->assign('start_o_date',$start_o_date);
            $this->assign('end_o_date',$end_o_date);
            $d=date("Ym", strtotime("-2 month"));
            $d2=date("Ym",strtotime($start_o_date));
            if($d2>$d){
                $table="order";
            }else{
                $rs = M()->query("SHOW TABLES LIKE 't_flow_order_".$d2."'");
                if(!$rs){
                    $table="order";
                }else{
                    $table="order_".$d2;
                }
            }
        }else{
            $table="order";
        }

        $user_type=D('SysUser')->self_user_type();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        $self_proxy_id=D('SysUser')->self_proxy_id();//当前代理商
        if($user_type==3){
            $map['od.enterprise_id'] = array('in',$self_enterprise_id);
            $map['od.user_type'] =2;
        }else if($user_type==2){
            $map['od.proxy_id']=D('SysUser')->self_proxy_id();
            $map['od.user_type'] =1;
        }else{
            $os_proxy_ids = $self_proxy_id.','.D('Proxy')->proxy_child_ids();
            $os_enterprise_ids = D('Enterprise')->enterprise_child_ids();
            if($os_proxy_ids!='' && $os_enterprise_ids!=''){
                $map1['od.proxy_id']=array('in',$os_proxy_ids);
                $map1['od.enterprise_id']=array('in',$os_enterprise_ids);
                $map1['_logic'] = 'or';
                $map['_complex'] = $map1;
            }else {
                if($os_proxy_ids!=''){
                    $map['od.proxy_id']=array('in',$os_proxy_ids);
                }else if($os_enterprise_ids!=''){
                    $map['od.enterprise_id']=array('in',$os_enterprise_ids);
                }
            }
        }
        $mobile = trim(I('mobile'));
        $status =trim(I('status'));
        $user_name=trim(I('user_name'));
        if(!empty($user_name)){
            $map_2['r.proxy_name']=array('like','%'.$user_name.'%');
            $map_2['e.enterprise_name']=array('like','%'.$user_name.'%');
            $map_2['_logic'] = 'or';
            $map['_complex'] = $map_2;
        }
        if(!empty($mobile))$map['od.mobile'] = array("like","%{$mobile}%");
        if($status!='' ){
            $map['od.status']=$status;
        }

        $join = array(
            C('DB_PREFIX').'proxy as r ON od.proxy_id=r.proxy_id',
            C('DB_PREFIX').'enterprise as e ON od.enterprise_id=e.enterprise_id',
            C('DB_PREFIX').$table.' as o ON od.order_id=o.order_id',
        );
        //获取所有订单表
        $order_list = M('order_refund as od')
            ->where($map)
            ->join($join,"left")
            ->field("od.*,r.proxy_name,e.enterprise_name,o.channel_id as o_channel_id,o.channel_code,o.back_channel_id as o_back_channel_id,o.back_channel_code,o.order_status as o_order_status，refund_cause,o.order_date,o.order_code")
            ->order('od.modify_date desc,od.status asc')
            ->limit(0,3000)
            ->select();
        $title='流量退款管理';
        $list=array();
        $user_type=D('SysUser')->self_user_type();
        if($user_type==1){
            $headArr=array("用户名称","退款编号","手机号","支付类型","退款金额(元)","通道编码","退款原因","退款状态","申请时间","充值时间","订单号");
        }else{
            $headArr=array("用户名称","退款编号","手机号","支付类型","退款金额(元)","退款原因","退款状态","申请时间","充值时间","订单号");
        }

        foreach($order_list as $k=>$v){
            if($v['proxy_name']!=''){
                $list[$k]['name']=$v['proxy_name'];
            }else {
                $list[$k]['name']=$v['enterprise_name'];
            }
            $list[$k]['refund_code'] =$v['refund_code'];
            $list[$k]['mobile'] =" ".$v['mobile'];

            if($v['pay_type']==1){
                $list[$k]['pay_type']='余额充值';
            }else {
                $list[$k]['pay_type']='微信支付';
            }
            $list[$k]['discount_price'] =$v['discount_price'];
            if($user_type==1){
                $list[$k]['channel_code']=$v['o_order_status']>3?$v['back_channel_code']:$v['channel_code'];
            }
            $list[$k]['refund_cause']=get_refund_cause($v['refund_cause']);
            switch ($v['status'])
            {
                case 1:
                    $list[$k]['status']='待审核';
                 break;
                case 2:
                    $list[$k]['status']='初审通过';
                break;
                case 3:
                    $list[$k]['status']='初审驳回';
                    break;
                case 4:
                    $list[$k]['status']='退款完成';
                    break;
                case 5:
                    $list[$k]['status']='复审驳回';
                    break;

            }
            $list[$k]['create_date'] =$v['create_date'];
            $list[$k]['order_date'] =substr($v['order_date'],0,19);
            $list[$k]['order_code']=$v['order_code'];
        }
        ExportEexcel($title,$headArr,$list);

    }


        public function add(){
                $user_type=D('SysUser')->self_user_type();
                if($user_type=="1"){
                }elseif($user_type=="2"){
                        $where['proxy_id']=D('SysUser')->self_proxy_id();
                        $info=M('proxy')->where($where)->find();
                }elseif($user_type=="3"){
                        $where['enterprise_id']=D('SysUser')->self_enterprise_id();
                        $info=M('enterprise')->where($where)->find();
                }
                $this->assign("refund_cause_list",get_refund_cause());
                $this->assign('user_type',$user_type);
                $this->assign($info);
                $this->display('add');
        }


        public function insert(){
                $user_type=D('SysUser')->self_user_type();
                $msg = '系统错误!';
                $status = 'error';
                $data = array();
               // $start=$time=date("Y-m-d H:i:s",strtotime("-3 day"));
          //  $end=date('Y-m-d H:i:s');
          if($user_type=="2"){
              $proxy_chd=D('Proxy')->proxy_child();
              if(empty($proxy_chd)){
                  $proxy_chd=array();
              }
              array_unshift($proxy_chd,D('SysUser')->self_proxy_id());
              $enterprise_chd= explode(',',D('Enterprise')->enterprise_ids());
              if(is_array($proxy_chd) && is_array($enterprise_chd)){
                  $map['enterprise_id'] = array('in',$enterprise_chd);
                  $map['proxy_id'] = array('in',$proxy_chd);
                  $map['_logic'] = 'or';
                  $where['_complex'] = $map;
              }else if(is_array($proxy_chd)){
                  $where['proxy_id']=array('in',$proxy_chd);
              }else if(is_array($enterprise_chd)){
                  $where['enterprise_id']=array('in',$enterprise_chd);
              }
            }elseif($user_type=="3"){
                $where['enterprise_id']=D('SysUser')->self_enterprise_id();
            }
            $model=M('order_refund');

            $where['order_code']=trim(I('order_code'));
          //  $where['complete_time']=array('between',array($start,$end));
            $order = M('order')->where($where)->find();
            $orderfund = $model->where(array('order_code'=>$where['order_code']))->find();
                if(I('discount_price')==""){
                        $msg = '请输入退款金额！';
                }elseif(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', I('discount_price'))){
                        $msg = '退款金额请输入数字！';
                }elseif(I('discount_price')<0){
                        $msg = '退款金额需大于零！';
                }elseif(I("order_code")==""){
                    $msg = '请输入订单号！';
                }elseif(I("refund_cause")=="") {
                    $msg='请选择退款原因！';
                }elseif(!$order){
                    $msg = '对不起,您没有操作该订单的权限或订单号输入错误,请重试!';
                }/*elseif(strtotime($order['complete_time'])<strtotime($start)){
                    $msg = '对不起，该订单退款时间已过，请联系客服！';
                }*/elseif($orderfund){
                    $msg = '请勿重复提交申请退款！';
                }elseif(I('discount_price')!=$order['discount_price']){
                    $msg = '退款金额与退款订单金额不匹配！';
                }elseif(I('mobile')==""){
                    $msg = '请输入退款手机号！';
                }elseif(I('mobile')!==$order['mobile']){
                    $msg = '请核对退款手机号！';
                }elseif(in_array($order['order_status'],array(0,1,3,4,6))){
                    $msg = '非成功订单状态不能申请退款！';
                }
                elseif(I("remark")==""){
                    $msg = '请输入退款理由！';
                }elseif($_FILES['file']['name']==""){
                    $msg = '请上传附件凭证！';
                }
                else{
                    $model->startTrans();

                        if($_FILES['file']['name']==null || $_FILES['file']['name']==""){
                                $icense_img = "";
                        }else{
                                $fileinfo = $this->business_licence_upload(C('ORDER_REFUND'));
                                if($fileinfo['file']){
                                        $icense_img = substr(C('UPLOAD_DIR').$fileinfo['file']['savepath'].$fileinfo['file']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['file']['savepath'].$fileinfo['file']['savename'])-1);
                                }else{
                                        $msg = $this->business_licence_upload_Error['file'];
                                        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                                }
                        }


                            $data['proxy_id']=$order['proxy_id'];
                            $data['enterprise_id']=$order['enterprise_id'];

               /*             $data['proxy_id']=D('SysUser')->self_proxy_id();
                            $data['enterprise_id']=D('SysUser')->self_enterprise_id();*/
                        $data['user_type']=$order['user_type'];
                        $data['create_user_id']=D('SysUser')->self_id();
                        $apply_code="TKSQD".date('Ymdhis',time());
                        $data['refund_code']=$apply_code;
                        $data['price']=$order['price'];
                        $data['discount_price']=$order['discount_price'];
                        $data['credential_one']=$icense_img;
                        $data['mobile']=trim(I('mobile'));
                        $data['order_id']=$order['order_id'];
                        $data['order_code']=trim(I('order_code'));
                        $data['refund_cause']=I("refund_cause");
                        $data['channel_order_code']=$order['channel_order_code'];
                    if($order['order_status']==2){
                        $data['channel_id']=$order['channel_id'];
                        $data['channel_product_id']=$order['channel_product_id'];
                    }elseif($order['order_status']==5){
                        $data['channel_id']=$order['back_channel_id'];
                        $data['channel_product_id']=$order['back_channel_product_id'];
                    }
                        $data['remark']=trim(I('remark'));
                        $data['pay_type']=1;
                        $data['create_date']=date('Y-m-d H:i:s',time());
                        $data['create_user_id']=D('SysUser')->self_id();
                        $data['modify_user_id']=D('SysUser')->self_id();
                        $data['modify_date']=date('Y-m-d H:i:s',time());
                        $data['status']=1;
                        $data['refund_type']=1;
                        $data['last_approve_date']= date('Y-m-d H:i:s',time());
                        //执行添加
                        $id = M('order_refund')->add($data);
                        $order_infos['refund_id']=$id;
                        $order_info=M('order')->where('order_id='.$order['order_id'])->save($order_infos);
                        if($order['user_type']==1){
                            $info=$this->order_info($order['proxy_id'],1); //代理商
                            $type_name='代理商';
                        }else{
                            $info=$this->order_info($order['enterprise_id'],2);//企业
                            $type_name='企业';
                        }
                        if($id && $order_info){
                            $model->commit();
                             $msg = '新增退款成功！';
                             $n_msg='退款成功';
                             $status = 'success';
                            $remind_content='代理商/企业【'.$info['name'].'】退款申请单已提交，请进行初审！';
                            R('ObjectRemind/send_user',array(13,$remind_content,array($info['sale_id'])));
                        }else{
                             $msg = '新增退款失败！';
                             $n_msg='退款失败';
                            $model->rollback();
                        }
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$id.'】，新增退款申请单，'.$type_name.'【'.$info['name'].'('.$info['code'].')】，退款编号【'.$apply_code.'】，退款金额【'.money_format2($data['price']).'】元，手机号【'.$data['mobile'].'】，订单编号【'.$data['order_code'].'】'.$n_msg;
                    $this->sys_log('新增退款申请单',$note);
                }

                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
        public function show(){
            if(I('download')){
                $where['refund_id'] = array('eq',trim(I('get.refund_id')));
                $orderrefund = M('order_refund')->where($where)->find();
                parent::download('.'.$orderrefund['credential_one']);
            }else {
                $user_type = D('SysUser')->self_user_type();
                $where['od.refund_id'] = trim(I('refund_id'));//充值
                $map['refund_id'] = trim(I('refund_id'));//充值
                $i=M("order")->where($map)->find();
                if($i){
                    $table="order";
                }else{
                    $i=2; //当前月份两个月后进来历史表
                    while(1){
                        $d=date("Ym", strtotime("-$i month"));
                        $rs = M()->query("SHOW TABLES LIKE 't_flow_order_".$d."'");
                        if(!$rs){
                            $table="order";
                            break;
                        }
                        $data=M("order_$d")->where($map)->find();
                        if($data){
                            $table="order_".$d;
                            break;
                        }
                        $i++;
                    }
                }
                $join = array(
                    C('DB_PREFIX') . 'proxy as r ON od.proxy_id=r.proxy_id',
                    C('DB_PREFIX') . 'enterprise as e ON od.enterprise_id=e.enterprise_id',
                    C('DB_PREFIX') .$table .' as o ON od.refund_id=o.refund_id',
                );
                $list = M('order_refund as od')->where($where)->join($join, "left")->field("od.*,r.proxy_name,e.enterprise_name,o.channel_id as o_channel_id,o.channel_code,o.back_channel_id as o_back_channel_id,o.back_channel_code,o.order_status as o_order_status,o.product_name,o.province_name,o.city_name,o.operator_id as o_operator_id,o.channel_order_code as o_channel_order_code,o.back_content,od.refund_cause")->find();
                if (!empty($list)) {
                    $process = M("order_refund_process")->where(array('refund_id' => trim(I('refund_id'))))->select();
                    empty($process) && $process = array();
                }
                $this->assign("orderrefund", $list);
                $this->assign('user_type', $user_type);
                $this->assign("process", $process);
                $this->display('detailed');
            }
        }

    /*弹出审核界面*/
    public function approve(){
        $msg = '系统错误！';
        $status = 'error';
        if(I('download')){
            $where['refund_id'] = array('eq',trim(I('get.refund_id')));
            $orderrefund = M('order_refund')->where($where)->find();
            parent::download('.'.$orderrefund['credential_one']);
        }else{
            $operate=trim(I('operate'));
            $where['od.refund_id']=trim(I('refund_id'));//充值
            $join = array(
                C('DB_PREFIX').'proxy as r ON od.proxy_id=r.proxy_id',
                C('DB_PREFIX').'enterprise as e ON od.enterprise_id=e.enterprise_id',
                C('DB_PREFIX').'order as o ON od.refund_id=o.refund_id',

            );
            $list=M('order_refund as od')->where($where)->join($join,"left")->field("od.*,r.proxy_name,e.enterprise_name,o.channel_id as o_channel_id,o.channel_code,o.back_channel_id as o_back_channel_id,o.back_channel_code,o.order_status as o_order_status,o.product_name,o.province_name,o.city_name,o.operator_id as o_operator_id,o.channel_order_code as o_channel_order_code,od.refund_cause")->find();
            //读取审核过程
            if($list){
                $process = M("order_refund")->where(array('refund_id'=>$list['refund_id']))->select();
                if(!$process){
                    $process = "";
                }
            }
            if(trim(I('approve_f'))=='orderrefund_approve_c'){
                if(in_array($list['status'],array(2,3,4,5))){
                    $this->ajaxReturn(array('msg'=>'不可重复审核！','status'=>$status));
                }
                $this->assign('type',1);
            }else{
                if($list['status']<2){
                    $this->ajaxReturn(array('msg'=>'请等待初审完成！','status'=>$status));
                }
                if($list['status']==3){
                    $this->ajaxReturn(array('msg'=>'初审驳回,不可复审！','status'=>$status));
                }
                if(in_array($list['status'],array(4,5))){
                    $this->ajaxReturn(array('msg'=>'不可重复审核！','status'=>$status));
                }
            }
            $this->assign('orderrefund',$list);
            $this->assign("process",$process);
            $this->display('approve');

        }
    }
    /*审核的方法*/
    //初审
    public function orderrefund_approve_c(){
        $msg = '系统错误';
        $status = 'error';
        $refund_id=trim(I('refund_id'));
        $approve_status=trim(I('approve_status'));
        $approve_remark=trim(I('approve_remark'));

        //  $where['complete_time']=array('between',array($start,$end));
        $map['refund_id']=$refund_id;
        $order = M('order_refund')->where($map)->find();
        if(empty($order)){
            $this->$order(array('msg'=>'对不起,没有该订单！','status'=>$status));
        }
        /*$start=$time=date("Y-m-d H:i:s",strtotime("-3 day"));
        $con['refund_id']=$refund_id;
        $order_info=$this->orderinfo($con);
        if(strtotime($order_info['complete_time'])<strtotime($start)){
            $this->ajaxReturn(array('msg'=>'对不起，该订单退款时间已过，请联系客服！','status'=>$status));
        }*/
        $model=M('order_refund');
        $model->startTrans();
        //读取申请信息
        $refund = $model->where(array('refund_id'=>$refund_id))->find();
        if(in_array($refund['status'],array(2,3,4,5))){
            $this->ajaxReturn(array('msg'=>'不可重复审核','status'=>$status));
        }
        if($approve_status==2 && $approve_remark==""){
            $this->ajaxReturn(array('msg'=>'请填写审核驳回原因','status'=>$status));
        }

        //修改申请表信息
        $edit['refund_id'] = $refund_id;
        $edit['status'] = $approve_status==2?"3":"2";
        $edit['last_approve_date'] = date("Y-m-d H:i:s",time());
        $refund_res=$model->where(array('refund_id'=>$refund_id))->save($edit);
        //添加审核信息
        $add['refund_id'] = $refund_id;
        $add['approve_status'] = $approve_status;
        $add['approve_remark'] = $approve_remark;
        $add['approve_date']=date('Y-m-d H:i:s',time());
        $add['approve_user_id']=D('SysUser')->self_id();
        $add['approve_stage']=1;
        $process = M("order_refund_process")->add($add);

        if($refund_res && $process){
            $model->commit();
            $msg = $approve_status==2?'退款申请单初审驳回成功！':'退款申请单初审成功！';
            $n_msg=$approve_status==2?'初审驳回成功':'初审成功';
            $status = 'success';
            if($order['user_type']==1){
                $info=$this->refund_info($order['proxy_id'],1); //代理商
            }else{
                $info=$this->refund_info($order['enterprise_id'],2);//企业
            }
            if($approve_status==1){
                $remind_content='代理商/企业【'.$info['name'].'】退款申请单已初审通过，请进行复审！';
                R('ObjectRemind/send_user',array(14,$remind_content ));
            }else{
                $remind_content='您提交的退款申请单号为【'.$order['refund_code'].'】退款申请单已初审驳回，请知晓！';
               // $remind_content='代理商/企业【'.$info['name'].'】退款申请单已初审驳回，请知晓！';
                R('ObjectRemind/send_user',array(14,$remind_content,array($info['user_id']) ));
            }
        }else{
            $model->rollback();
            $msg = $approve_status==2?'退款申请单初审驳回失败！':'退款申请单初审失败！';
            $n_msg=$approve_status==2?'初审驳回失败':'初审失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$refund_id."】，审核退款申请单【".$order['refund_code']."】".$n_msg;
        $this->sys_log('退款申请单初审',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    public function orderinfo($where){
        $info=M('order_refund as of')
            ->join('left join t_flow_order as o on of.order_id=o.order_id')
            ->field('o.complete_time')
            ->where($where)
            ->find();
        return $info;
    }
    //复审
    public function  orderrefund_approve(){
        $msg = '系统错误';
        $status = 'error';
        $refund_id=trim(I('refund_id'));
        $approve_status=trim(I('approve_status'));
        $approve_remark=trim(I('approve_remark'));
        /*$start=$time=date("Y-m-d H:i:s",strtotime("-3 day"));
        $con['refund_id']=$refund_id;
        $order_info=$this->orderinfo($con);
        if(strtotime($order_info['complete_time'])<strtotime($start)){
            $this->ajaxReturn(array('msg'=>'对不起，该订单退款时间已过，请联系客服！','status'=>$status));
        }*/
        $map['refund_id']=$refund_id;
        $order = M('order_refund')->where($map)->find();
        if(empty($order)){
            $this->$order(array('msg'=>'对不起,没有该订单！','status'=>$status));
        }
        if($approve_status=="1"){
            if(I("get.tran")){
                //记录数据
                $da['refund_id']=trim(I('refund_id'));
                $da['approve_status']=trim(I('approve_status'));
                $da['approve_remark']=trim(I('approve_remark'));
                //sql条件
                $ta['ra.refund_id']=trim(I('refund_id'));
                $result=M('order_refund as ra')
                    ->join('t_flow_proxy as p on p.proxy_id=ra.proxy_id','left')
                    ->join('t_flow_enterprise as e on e.enterprise_id=ra.enterprise_id','left')
                    ->field('ra.discount_price,p.proxy_name,e.enterprise_name')
                    ->where($ta)
                    ->find();
                if($result['proxy_name']==""){
                    $msg="确定是否向【".$result['enterprise_name']."】退款".$result['discount_price']."元？";
                }else{
                $msg="确定是否向【".$result['proxy_name']."】退款".$result['discount_price']."元？";
                }
                $this->ajaxReturn(array('msg'=>$msg,'status'=>'success','info'=>$da));
            }
        }
        $model=M('order_refund');
        $model->startTrans();

        //读取申请信息
        $refund = $model->where(array('refund_id'=>$refund_id))->find();
        if($refund['status']==1){
            $this->ajaxReturn(array('msg'=>'请等待初审完成！','status'=>$status));
        }
        if($refund['status']==3){
            $this->ajaxReturn(array('msg'=>'初审驳回,不可复审！','status'=>$status));
        }
        if($approve_status==2 && $approve_remark==""){
            $this->ajaxReturn(array('msg'=>'请填写审核驳回原因！','status'=>$status));
        }
        if(in_array($refund['status'],array(4,5))){
            $this->ajaxReturn(array('msg'=>'不可重复审核!','status'=>$status));
        }
        $edit['refund_id'] = $refund_id;
        $edit['status'] = $approve_status==2?"5":"4";
        $edit['last_approve_date'] = date("Y-m-d H:i:s",time());
        $refund_res=$model->where(array('refund_id'=>$refund_id))->save($edit);
        //添加审核信息
        $add['refund_id'] = $refund_id;
        $add['approve_status'] = $approve_status;
        $add['approve_remark'] = $approve_remark;
        $add['approve_date']=date('Y-m-d H:i:s',time());
        $add['approve_user_id']=D('SysUser')->self_id();
        $add['approve_stage']=2;
        $process = M("order_refund_process")->add($add);
        $ms_mag='';
        if($refund_res && $process){
            if($approve_status==2){
                $model->commit();
                $msg = '复审驳回成功！';
                $status = 'success';
                $ms_mag='复审驳回';
                $n_msg= '复审驳回成功';
            }else{
                if(!empty($refund['proxy_id'])){
                    //读取上级代理商金额
                    $Balance = M("ProxyAccount")->where(array('proxy_id'=>1))->find();
                    /*记录账户流水 上级代理商为支出 */
                    $map['proxy_id']=$refund['proxy_id'];
                    $account=D('ProxyAccount')->account($map);  //读取代理商账户
                    $condition['top_account_id']=$Balance['account_id']; //上级代理商账户id
                    $condition['top_account_balance']=$Balance['account_balance']; //上级代理商账户余额
                    $condition['top_operate_type']=7; //退款-上级代理商
                    $condition['top_balance_type']=2;//支出-上级代理商
                    $condition['top_user_type']=1;  //操作者类型
                    $condition['apply_money']=$refund['discount_price'];   //需要操作的金额
                    $condition['operate_proxy_id']=$account['proxy_id'];//收入-下级代理商
                    $condition['operate_account_balance']=$account['account_balance'];//要操作的代理商账户余额
                    $condition['operate_account_id']=$account['account_id']; //要操作的代理商账户ID
                    $condition['operate_operate_type']=5; //充值-下级代理商
                    $condition['operate_balance_type']=1;//收入-下级代理商
                   // $condition['top_user_type']=1;  //操作用户类型
                    $condition['operate_user_type']=1;
                    $res=D('ProxyAccount')->order_refund($condition);
                }else{
                    //读取上级代理商金额
                    $Balance = M("ProxyAccount")->where(array('proxy_id'=>1))->find();
                    /*记录账户流水 上级代理商为支出 */
                    $map['enterprise_id']=$refund['enterprise_id'];
                    $account=D('EnterpriseAccount')->account($map);  //读取账户
                    $condition['top_account_id']=$Balance['account_id']; //上级代理商账户id
                    $condition['top_account_balance']=$Balance['account_balance']; //上级代理商账户余额
                    $condition['top_operate_type']=7; //退款-上级代理商
                    $condition['top_balance_type']=2;//支出-上级代理商
                    $condition['top_user_type']=1;  //操作者类型
                    $condition['apply_money']=$refund['discount_price'];   //需要操作的金额
                    $condition['operate_enterprise_id']=$account['enterprise_id'];//收入-下级企业operate_enterprise_id
                    $condition['operate_account_balance']=$account['account_balance'];//要操作的代理商账户余额
                    $condition['operate_account_id']=$account['account_id']; //要操作的代理商账户ID
                    $condition['operate_operate_type']=5; //充值-下级
                    $condition['operate_balance_type']=1;//收入-下级
                    $condition['operate_user_type']=2;  //操作用户类型
                    $res=D('EnterpriseAccount')->order_refund($condition);
                }
                if($res){
                    $model->commit();
                    $msg = '退款申请单复审成功！';
                    $status = 'success';
                    $ms_mag='已退款成功';
                    $n_msg= '退款成功';
                    $success_msg="，并退款【".$refund['discount_price']."】元";

                }else{
                    $model->rollback();
                    $msg ='退款申请单复审失败！';
                    $n_msg= '复审失败';
                }
            }
            if($status == 'success'){
                $remind_content='您提交的退款申请单号为【'.$order['refund_code'].'】'.$ms_mag.'，请注意查看！';
                if($order['user_type']==1){
                    $info=$this->refund_info($order['proxy_id'],1); //代理商
                    R('ObjectRemind/send_user',array(15,$remind_content,array($info['user_id']) ));
                }else{
                    $info=$this->refund_info($order['enterprise_id'],2);//企业
                    R('ObjectRemind/send_user',array(16,$remind_content,array($info['user_id']) ));
                }
            }
        }else{
            $model->rollback();
            $msgs = $approve_status==2?'退款申请单复审驳回失败！':'退款申请单复审失败！';
            $msg =$msgs;
            $n_msg= $approve_status==2?'复审驳回失败':'复审失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$refund_id."】，审核退款申请单【".$order['refund_code']."】".$n_msg.$success_msg;
        $this->sys_log('退款申请单复审',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    public function delete(){
        $user_type=D('SysUser')->self_user_type();
        $msg = '系统错误！';
        $status = 'error';
        $refund_id =trim(I('refund_id'));
        if($user_type==2){
            $map['proxy_id']=D('SysUser')->self_proxy_id();
        }elseif($user_type==3){
            $map['enterprise_id']=D('SysUser')->self_enterprise_id();
        }
        $map['refund_id']=$refund_id;
        $refund_apply = M("order_refund")->where($map)->find();
        if(in_array($refund_apply['approve_status'],array(2,4))){
            $this->ajaxReturn(array('msg'=>'已经审核通过，请勿删除！','status'=>$status));
        }
        if($refund_apply){
            $delete['refund_id'] = $refund_apply['refund_id'];
            if(M("order_refund_process")->where($delete)->delete()){
                M("order_refund")->where(array('refund_id'=>$refund_apply['refund_id']))->delete();
                $edit['refund_id']='';
                M("order")->where(array('order_id'=>$refund_apply['order_id']))->save($edit);
                $msg = '删除退款申请单成功！';
                $status = 'success';
                $n_msg='成功';
            }else{
                $msg = '删除退款申请单失败！';
                $n_msg='失败';
            }
        }else{
            $msg = '数据读取错误！';
            $n_msg='失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id())."】，ID【".$refund_id."】，删除退款申请单【".$refund_apply['refund_code']."】".$n_msg;
        $this->sys_log('删除退款申请单',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }
    function  order_info($id,$type){
        $info=array();
        if($type==1){
            $info=M('proxy')->where('proxy_id='.$id)->field('sale_id,proxy_name as name,proxy_code as code')->find();
        }else{
            $info=M('enterprise')->where('enterprise_id='.$id)->field('sale_id,enterprise_name as name,enterprise_code as code')->find();
        }
        return $info;
    }


    function  refund_info($id,$type){
        $where=array();
        if($type==1){
            $where['p.proxy_id']=$id;
            $where['u.is_manager']=1;
            $info=M('proxy as p')->join('t_flow_sys_user as u on p.proxy_id =u.proxy_id','left')->where($where)->field('p.sale_id,u.user_id,p.proxy_name as name')->find();
        }else{
            $where['e.enterprise_id']=$id;
            //$where['u.is_manager']=1;
            $info=M('enterprise as e')->join('t_flow_sys_user as u on e.enterprise_id =u.enterprise_id','left')->where($where)->field('e.sale_id,u.user_id,e.enterprise_name as name')->find();
        }
        return $info;
    }
}
?>