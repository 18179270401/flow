<?php
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class RechargeRecordController extends CommonController{
    /*
             * 订单表
             */
    /*public function index1(){
        set_time_limit(0);
        $proxy_name = trim(I('proxy_name'));
        $mobile = trim(I('mobile'));
        $order_status =I('order_status');
        $start_datetime = I('start_datetime');
        $end_datetime = I('end_datetime');
        $operator_id   = I('operator_id');
        $channel_id   = I('channel_id');
        $province_id = I('province_id');
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400){
                $this->display('index');
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过31天！'});history.back(); </script>";exit;
            }
        }
        //获取要查询的时间
        if($start_datetime || $end_datetime){
            if($start_datetime && $end_datetime){
                $map['od.order_date'] = array('between',array($start_datetime,$end_datetime));
            }
            if($start_datetime ==""){
                $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
                $map['od.order_date'] = array('between',array($start_datetime,$end_datetime));
            }
            if($end_datetime ==""){
                $end_datetime = end_time(msubstr($start_datetime,0,10,"utf-8",false));
                $map['od.order_date'] = array('between',array($start_datetime,$end_datetime));
            }
        }else {
            $default_start = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
            $default_end = date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('d'), date('Y')));
            $map['od.order_date'] = array('between', array($default_start, $default_end));
        }
        //获取当前用户的数据权限
        $user_type=D('SysUser')->self_user_type();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        $self_proxy_id=D('SysUser')->self_proxy_id();//当前代理商
        if($user_type==3&&!empty($self_enterprise_id)){
            $map['od.enterprise_id'] = array('in',$self_enterprise_id);
            $map['od.user_type'] =2;
        }else{
            $os_proxy_ids = $self_proxy_id.','.D('Proxy')->proxy_child_ids();
            if($user_type==1){
                $os_enterprise_ids = D('Enterprise')->enterprise_child_ids();
            }else{
                $os_enterprise_ids = D('Enterprise')->enterprise_ids();
            }
            if($os_proxy_ids!='' && $os_enterprise_ids!=''){
                $map_1['od.proxy_id']=array('in',$os_proxy_ids);
                $map_1['od.enterprise_id']=array('in',$os_enterprise_ids);
                $map_1['_logic'] = 'or';
                $map['_complex'] = $map_1;
            }else {
                if($os_proxy_ids!=''){
                    $map['od.proxy_id']=array('in',$os_proxy_ids);
                }else if($os_enterprise_ids!=''){
                    $map['od.enterprise_id']=array('in',$os_enterprise_ids);
                }
            }
        }
        //整理查询条件
        if(!empty($proxy_name)){
            $map_2['od.proxy_name']=array('like','%'.$proxy_name.'%');
            $map_2['od.enterprise_name']=array('like','%'.$proxy_name.'%');
            $map_2['_logic'] = 'or';
            $map['_complex'] = $map_2;
        }
        if(!empty($mobile)){$map['od.mobile'] = array('like','%'.$mobile.'%');}
        if(!empty($channel_id)){
            $map_3['od.channel_id']=$channel_id;
            $map_3['od.back_channel_id']=$channel_id;
            $map_3['_logic'] = 'or';
            $map['_complex'] = $map_3;
        }
        if(!empty($operator_id)){$map['od.operator_id'] = $operator_id;}
        if(!empty($province_id)){$map['dt.province_id'] = $province_id;}

        if(!empty($order_status)){
            $map['od.order_status'] =array("in",$order_status);
        }else{
            $map['od.order_status'] =array("in",array(2,5,6));
        }

        //连表
        $join = array(
            C('DB_PREFIX').'sys_mobile_dict as dt ON od.mobile=dt.mobile'
        );

        $count = M('Order as od')->where($map)->join($join,"left")->count();
        $Page       = new Page($count,20);
        $show     = $Page->show();

        $oder_list = M('Order as od')->where($map)
            ->join($join,"left")
            ->field("od.order_id,od.proxy_name,od.enterprise_name,od.one_proxy_name,od.mobile,od.operator_id,od.province_id,dt.province_name,dt.city_name,od.channel_product_id,od.discount_price,od.channel_id,od.back_channel_id,od.order_date,od.complete_time,od.order_status,od.refund_id")
            ->order('od.order_date desc, od.order_id desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        if($oder_list){
            foreach($oder_list as &$v){
                //上级代理商ID
                $v['proxy_name'] = !empty($v['proxy_name']) ? $v['proxy_name'] : $v['enterprise_name'];
                $v['top_proxy_name'] = $v['one_proxy_name'];
                //读取运营商
                $v['operator_name'] = get_operator_name($v['operator_id']);
                //读取通道信息
                if(in_array($v['order_status'],array("4","5","6"))){
                    $v['bc_channel_code'] = select_channel($v['back_channel_id']);
                }else{
                    $v['channel_code'] = select_channel($v['channel_id']);
                }
                //读取产品名称
                $v['product_name'] = select_channel_product($v['channel_product_id']);
            }
        }
        $this->assign('order_list',get_sort_no($oder_list,$Page->firstRow));  //数据列表
        $this->assign('page',$show);            //分页

        //计算成功条数和金额与失败条数和金额
        $status_info = $this->status_info($order_status,$map,$join);
        $counts['@p_success_count'] = $status_info['success_data'];
        $counts['@p_success_amount'] = $status_info['success_money'];
        $counts['@p_faile_count'] = $status_info['fail_data'];
        $counts['@p_faile_amount'] = $status_info['fail_money'];
        $this->assign("counts",$counts);

        //读取运营商
        $operator = D("Order")->operatorall();
        //读取省份
        $province = D("Order")->provinceall();
        //给查询状态
        if($user_type==1){
            $status_list['items']['2'] = array('text' => "充值成功", 'order_status' => '2');
            $status_list['items']['3'] = array('text' => "充值失败", 'order_status' =>  '3');
            $status_list['items']['5'] = array('text' => "充值成功(备)", 'order_status' =>  '5');
            $status_list['items']['6'] = array('text' => "充值失败(备)", 'order_status' =>  '6');
        }else{
            $status_list['items']['2,5'] = array('text' => "充值成功", 'order_status' => '2,5');
            $status_list['items']['6,3'] = array('text' => "充值失败", 'order_status' =>  '6,3');
        }
        //给定可操作的代理商和企业
        if($user_type==2){
            $proxy_chd=D('Proxy')->proxy_child();
            if(!is_array($proxy_chd)){
                $proxy_chd=array($self_proxy_id);
            }else{
                array_unshift($proxy_chd,$self_proxy_id);
            }
            $enterprise_chd= explode(',',D('Enterprise')->enterprise_ids());
        }
        if($user_type==3){
            $enterprise_chd= array($self_enterprise_id);
        }
        //读取通道信息
        $channel = D("Order")->channelall();
        $this->assign('user_type',$user_type);
        $this->assign('operator',$operator);
        $this->assign('province',$province);
        $this->assign('channel',$channel);
        $this->assign('proxy_chd',$proxy_chd);
        $this->assign('enterprise_chd',$enterprise_chd);
        $this->assign('order_status_list',$status_list);
        $this->assign('default_end',date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('d'), date('Y'))));
        $this->assign('default_start',date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y'))));
        $this->display('index');
    }*/

    public function index(){
        D("SysUser")->sessionwriteclose();
        $mobile = trim(I('mobile'));
        $order_status =I('order_status');
        $start_datetime = I('start_datetime');
        $end_datetime = I('end_datetime');
        $operator_id   = I('operator_id');
        $channel_id   = I('channel_id');
        $bc_channel_id=I('bc_channel_id');
        $province_id = I('province_id');
        $proxy_name = trim(I('proxy_name'));
        $product_name=trim(I('product_name'));
        $sale_name=trim(I('sale_name'));
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400){
                $this->display('index');
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过31天！'});history.back(); </script>";exit;
            }
        }

        if($start_datetime || $end_datetime){
            if($start_datetime ==""){
                $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
            }
            if($end_datetime ==""){
                $end_datetime = end_time(msubstr($start_datetime,0,10,"utf-8",false));
            }
        }else {
            $start_datetime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
            $end_datetime = date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('d'), date('Y')));
        }
        //读取用户、代理商、企业信息
        $user_type=D('SysUser')->self_user_type();
        $user_id=D('SysUser')->self_id(); //用户id
        $self_proxy_id =  D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        //给定查询条件

        $proxy_name = !empty($proxy_name) ? $proxy_name : '' ;
        $mobile = !empty($mobile) ? $mobile : '' ;
        $operator_id = !empty($operator_id) ? $operator_id : -1 ;  //运营商
        $sale_id=-1;
        if($sale_name){
            $con['user_name']=$sale_name;
            //$con['user_type']=$user_type;
            $con['status']=1;
            $user_sale_id=M('sys_user')->where($con)->field('user_id')->find();
            $sale_id=empty($user_sale_id)?0:intval($user_sale_id['user_id']);
        }
        $upper_role=D('SysRole')->upper_role();
        //  var_dump($upper_role);
        if($upper_role['in']){
            $channel_id =!empty($upper_role['channel_id']) ? $upper_role['channel_id'] : '0' ; //通道
            $bc_channel_id =!empty($upper_role['channel_id']) ? '' : '0'; //备用通道
            $upper_role_info=1;
        }else{
            $channel_id = !empty($channel_id) ? $channel_id : '' ; //通道
            $bc_channel_id = !empty($bc_channel_id) ? $bc_channel_id : '' ; //备用通道
            $upper_role_info=2;
        }
        $province_id = !empty($province_id) ? $province_id : -1 ;  //省份
        $order_status = !empty($order_status) ? $order_status : '' ;  //订单状态
        $product_name = !empty($product_name) ? $product_name : '' ;  //产品名称
        /*  if($order_status=='0'){$map['od.order_status'] =0;}*/
        $get_page = I("p")==""?1:I("p");     //获取当前分页数

        $timea = strtotime(date("Y-m-d 00:00:00",strtotime($start_datetime)));
        $timeb = strtotime(date("Y-m-d 23:59:59",strtotime($end_datetime)));
        $timec = round(($timeb-$timea)/86400);

        //if($timec > 10){
        $oder_list=D('Order')->order_storing_process(2,$user_id,$proxy_name,'','',$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$get_page,$sale_id);
        //}else{
        //$oder_list=D('Order')->order_storing_process2(2,$user_id,$proxy_name,'','',$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$get_page,$sale_id);
        //$oder_list['count'] = $this->order_list_sum(2,$user_id,$proxy_name,'','',$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$get_page,$sale_id);
        //}

        $Page       = new Page($oder_list['count']['@p_total_count'],20);
        $show       = $Page->show();
        //加载模板
        $this->assign('page',$show);            //分页
        $this->assign('upper_role',$upper_role_info);            //为上游角色 ： 1 是   2 不是
        $this->assign("counts",$oder_list['count']);

        //读取运营商
        $operator = D("Order")->operatorall();
        //读取省份
        $province = D("ChannelProduct")->province_list();
        //读取通道
        if($user_type==1){
            $status_list['items']['2'] = array('text' => "充值成功", 'order_status' => '2');
            $status_list['items']['3'] = array('text' => "充值失败", 'order_status' =>  '3');
            $status_list['items']['5'] = array('text' => "充值成功(备)", 'order_status' =>  '5');
            $status_list['items']['6'] = array('text' => "充值失败(备)", 'order_status' =>  '6');
        }else{
            $status_list['items']['2,5'] = array('text' => "充值成功", 'order_status' => '2,5');
            $status_list['items']['6,3'] = array('text' => "充值失败", 'order_status' =>  '6,3');
        }
        if($user_type==2){
            $proxy_chd=D('Proxy')->proxy_child();
            if(!is_array($proxy_chd)){
                $proxy_chd=array($self_proxy_id);
            }else{
                array_unshift($proxy_chd,$self_proxy_id);
            }
            $enterprise_chd= explode(',',D('Enterprise')->enterprise_ids());
        }
        if($user_type==3){
            $enterprise_chd= array($self_enterprise_id);
        }

        $proxy_ids = "";
        $enterprise_ids = "";
        foreach($oder_list['list'] as $o_v){
            if(empty($o_v['enterprise_id'])){
                $proxy_ids .= ',' . $o_v['proxy_id'];
            }else{
                $enterprise_ids .= ',' . $o_v['enterprise_id'];
            }
        }

        $proxy_ids = trim($proxy_ids,',');
        $enterprise_ids = trim($enterprise_ids,',');

        $pr_status = array();
        $er_status = array();

        if(!empty($proxy_ids)){
            $proxy_refund_status = M('proxy')->where("proxy_id in ($proxy_ids)")
                ->field('proxy_id,refund_status')
                ->select();
            foreach($proxy_refund_status as $k => $v){
                $p_id = $v['proxy_id'];
                $pr_status[$p_id] = $v['refund_status'];
            }
        }

        if(!empty($enterprise_ids)){
            $enterprise_refund_status = M('enterprise')->where("enterprise_id in ($enterprise_ids)")
                ->field('enterprise_id,refund_status')
                ->select();
            foreach($enterprise_refund_status as $k => $v){
                $e_id = $v['enterprise_id'];
                $er_status[$e_id] = $v['refund_status'];
            }
        }

        foreach($oder_list['list'] as &$o_v){
            $o_v['refund_status'] = 1;
            if(empty($o_v['enterprise_id'])){
                $o_v['refund_status'] = $pr_status[$o_v['proxy_id']];
            }else{
                $o_v['refund_status'] = $er_status[$o_v['enterprise_id']];
            }
        }

        //读取通道信息
        $this->assign('order_list',get_sort_no($oder_list['list'],$Page->firstRow));  //数据列表
        $channel = D("Order")->channelall(1);
        $this->assign('user_type',$user_type);
        $this->assign('operator',$operator);
        $this->assign('province',$province);
        $this->assign('channel',$channel);
        $this->assign('proxy_chd',$proxy_chd);
        $this->assign('is_role',$this->is_role());
        $this->assign('enterprise_chd',$enterprise_chd);
        $this->assign('order_status_list',$status_list);
        $this->assign('default_end',date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('d'), date('Y'))));
        $this->assign('default_start',date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y'))));
        //是否有高级查询权限
        $is_role = D('SysRole')->user_is_role('query_role');
        $is_role = $is_role? 1 : 0;
        $this->assign('is_rolea',$is_role);//返回页面做判断

        $this->display('index');
    }
    //分段统计总数与金额
    private function order_list_sum($type,$user_id,$proxy_name,$code,$order_code,$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$get_page,$sale_id){
        //将时间转成年月日并进行计算
        $timea = strtotime(date("Y-m-d 00:00:00",strtotime($start_datetime)));
        $timeb = strtotime(date("Y-m-d 23:59:59",strtotime($end_datetime)));
        $timec = ceil (round(($timeb-$timea)/86400)/3);
        //获取查询中的时分秒待用
        $timeha = date("H:i:s",strtotime($start_datetime));
        $timehb = date("H:i:s",strtotime($end_datetime));
        //循环统计订单总数
        for($a = 1;$a <= $timec;$a++){
            //计算要统计的时间
            $i = $a==1?0:$i+3;
            if($a==$timec){
                $timed = strtotime(date("Y-m-d 00:00:00",mktime(0,0,0,date('m',$timea),date('d',$timea)+$i,date('Y',$timea))));
                $j = round(($timeb-$timed)/86400)+$j;
                if($j <= 3)$j--;
            }else{
                $j = $i+2;
            }
            //给定查询中的时分秒
            $hisa = $a==1?$timeha:"00:00:00";
            $hisb = $a==$timec?$timehb.'.9999':"23:59:59.9999";
            //组合开始时间的结束时间
            $start_datetime = date("Y-m-d ".$hisa,mktime(0,0,0,date('m',$timea),date('d',$timea)+$i,date('Y',$timea)));
            $end_datetime = date("Y-m-d ".$hisb,mktime(23,59,59,date('m',$timea),date('d',$timea)+$j,date('Y',$timea)));
            //统计总数
            $oder_list=D('Order')->order_storing_process($type,$user_id,$proxy_name,$code,$order_code,$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$get_page,$sale_id);
            //将数据赋值
            $counts['@p_total_count']       += $oder_list['count']['@p_total_count'];
            $counts['@p_success_count']     += $oder_list['count']['@p_success_count'];
            $counts['@p_success_price']     += $oder_list['count']['@p_success_price'];
            $counts['@p_success_amount']    += $oder_list['count']['@p_success_amount'];
            $counts['@p_faile_count']       += $oder_list['count']['@p_faile_count'];
            $counts['@p_faile_price']       += $oder_list['count']['@p_faile_price'];
            $counts['@p_faile_amount']      += $oder_list['count']['@p_faile_amount'];

            //格式化为3位小数
            $counts['@p_success_price']     = number_format($counts['@p_success_price'], 3);
            $counts['@p_success_amount']    = number_format($counts['@p_success_amount'], 3);
            $counts['@p_faile_price']       = number_format($counts['@p_faile_price'], 3);
            $counts['@p_faile_amount']      = number_format($counts['@p_faile_amount'], 3);
        }
        return $counts;
    }

    /**
     * 历史充值记录，默认3个月前的
     */
    public function index_history(){
        set_time_limit(0);
        D("SysUser")->sessionwriteclose();
        $mobile = trim(I('mobile'));
        $order_status =I('order_status');
        $start_datetime = I('start_datetime');
        $end_datetime = I('end_datetime');
        $operator_id   = I('operator_id');
        $channel_id   = I('channel_id');
        $bc_channel_id=I('bc_channel_id');
        $province_id = I('province_id');
        $proxy_name = trim(I('proxy_name'));
        $product_name=trim(I('product_name'));

        $new_m = date('Ym',strtotime("-2 month"));     //历史最新月份
        $newt_m = date('Y-m',strtotime("-2 month"));     //历史最新月份

        //判断时间是否在三个月前
        if($start_datetime != "" || $end_datetime != ""){
            $s_m = '';      //开始时间的月份
            $e_m = '';      //结束时间的月份
            if(!empty($start_datetime)){
                $s_m = date('Ym',strtotime($start_datetime));
                if($s_m > $new_m){
                    $this->display('index');
                    echo "<script>alertbox({'status':'error','msg':'历史记录请查 $newt_m 以及之前的数据！'});history.back(); </script>";exit;
                }
            }
            if(!empty($end_datetime)){
                $e_m = date('Ym',strtotime($end_datetime));
                if($e_m > $new_m){
                    $this->display('index');
                    echo "<script>alertbox({'status':'error','msg':'历史记录请查 $newt_m 以及之前的数据！'});history.back(); </script>";exit;
                }
            }
        }
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            $s_m = date('Ym',strtotime($start_datetime));
            $e_m = date('Ym',strtotime($end_datetime));
            if($s_m != $e_m){
                $this->display('index');
                echo "<script>alertbox({'status':'error','msg':'查询时间请在同一个月！'});history.back(); </script>";exit;
            }
        }

        if($start_datetime || $end_datetime){
            if($start_datetime ==""){
                $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
            }
            if($end_datetime ==""){
                $end_datetime = end_time(msubstr($start_datetime,0,10,"utf-8",false));
            }
        }else {
            $start_datetime = date('Y-m-d 00:00:00',strtotime(date('Y-m-1')." -1 month -1 day"));
            $end_datetime = date('Y-m-d 23:59:59',strtotime(date('Y-m-1')." -1 month -1 day"));
        }
        //读取用户、代理商、企业信息
        $user_type=D('SysUser')->self_user_type();
        $user_id=D('SysUser')->self_id(); //用户id
        $self_proxy_id =  D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        //给定查询条件

        $proxy_name = !empty($proxy_name) ? $proxy_name : '' ;
        $mobile = !empty($mobile) ? $mobile : '' ;
        $operator_id = !empty($operator_id) ? $operator_id : -1 ;  //运营商
        $upper_role=D('SysRole')->upper_role();
        //  var_dump($upper_role);
        if($upper_role['in']){
            $channel_id =!empty($upper_role['channel_id']) ? $upper_role['channel_id'] : '0' ; //通道
            $bc_channel_id =!empty($upper_role['channel_id']) ? '' : '0'; //备用通道
            $upper_role_info=1;
        }else{
            $channel_id = !empty($channel_id) ? $channel_id : '' ; //通道
            $bc_channel_id = !empty($bc_channel_id) ? $bc_channel_id : '' ; //备用通道
            $upper_role_info=2;
        }
        $province_id = !empty($province_id) ? $province_id : -1 ;  //省份
        $order_status = !empty($order_status) ? $order_status : '' ;  //订单状态
        $product_name = !empty($product_name) ? $product_name : '' ;  //产品名称
        /*  if($order_status=='0'){$map['od.order_status'] =0;}*/
        $get_page = I("p")==""?1:I("p");     //获取当前分页数
        /*       $oder_list = M()->query("CALL p_query_order(2,".$user_id.",'".$proxy_name."','".$mobile."','".$channel_id."','".$bc_channel_id."',".$operator_id.",".$province_id.",'".$order_status."','".$start_datetime."','".$end_datetime."','".$product_name."',".$get_page.",20,1,@p_total_count,@p_success_count,@p_success_amount,@p_faile_count,@p_faile_amount,@p_wait_count,@p_wait_amount,@submit_success_count,@submit_success_amount,@p_success_price,@p_faile_price,@p_wait_price,@p_submit_success_price);");
               //echo  M()->getLastSql();
               $count = M()->query("SELECT @p_total_count,@p_success_count,@p_success_amount,@p_faile_count,@p_faile_amount,@p_wait_count,@p_wait_amount,@submit_success_count,@submit_success_amount,@p_success_price,@p_faile_price,@p_wait_price,@p_submit_success_price;");*/
        $the_m = date('Ym',strtotime($start_datetime));
        $the_table = C('DB_PREFIX')."order_$the_m";
        $is_exist =  M()->query("SELECT COUNT(*) as the_exist FROM information_schema.tables WHERE table_name = '$the_table';");
        $oder_list = array();
        if($is_exist[0]['the_exist'] > 0){
            $oder_list=D('Order')->order_storing_process($the_m,$user_id,$proxy_name,'','',$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$get_page,-1);
        }

        //echo  M()->getLastSql();
        // var_dump($oder_list);

        $Page       = new Page($oder_list['count']['@p_total_count'],20);
        $show       = $Page->show();
        //加载模板
        //var_dump($oder_list);exit;
        $this->assign('order_list',get_sort_no($oder_list['list'],$Page->firstRow));  //数据列表
        $this->assign('page',$show);            //分页
        $this->assign('upper_role',$upper_role_info);            //为上游角色 ： 1 是   2 不是
        $this->assign("counts",$oder_list['count']);

        //读取运营商
        $operator = D("Order")->operatorall();
        //读取省份
        $province = D("ChannelProduct")->province_list();
        //读取通道
        if($user_type==1){
            $status_list['items']['2'] = array('text' => "充值成功", 'order_status' => '2');
            $status_list['items']['3'] = array('text' => "充值失败", 'order_status' =>  '3');
            $status_list['items']['5'] = array('text' => "充值成功(备)", 'order_status' =>  '5');
            $status_list['items']['6'] = array('text' => "充值失败(备)", 'order_status' =>  '6');
        }else{
            $status_list['items']['2,5'] = array('text' => "充值成功", 'order_status' => '2,5');
            $status_list['items']['6,3'] = array('text' => "充值失败", 'order_status' =>  '6,3');
        }
        if($user_type==2){
            $proxy_chd=D('Proxy')->proxy_child();
            if(!is_array($proxy_chd)){
                $proxy_chd=array($self_proxy_id);
            }else{
                array_unshift($proxy_chd,$self_proxy_id);
            }
            $enterprise_chd= explode(',',D('Enterprise')->enterprise_ids());
        }
        if($user_type==3){
            $enterprise_chd= array($self_enterprise_id);
        }
        //读取通道信息
        $channel = D("Order")->channelall(1);
        $this->assign('user_type',$user_type);
        $this->assign('operator',$operator);
        $this->assign('province',$province);
        $this->assign('channel',$channel);
        $this->assign('proxy_chd',$proxy_chd);
        $this->assign('is_role_price',$this->is_role());
        $this->assign('enterprise_chd',$enterprise_chd);
        $this->assign('order_status_list',$status_list);
        $this->assign('default_end',date('Y-m-d 23:59:59',strtotime(date('Y-m-1')." -1 month -1 day")));
        $this->assign('default_start',date('Y-m-d 00:00:00',strtotime(date('Y-m-1')." -1 month -1 day")));

        //是否有高级查询权限
        $is_role = D('SysRole')->user_is_role('query_role');
        $is_role = $is_role? 1 : 0;
        $this->assign('is_role',$is_role);//返回页面做判断

        $this->display('index_history');
    }

    public function export_excel_history(){
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $mobile = trim(I('mobile'));
        $order_status =I('order_status');
        $start_datetime = I('start_datetime');
        $end_datetime = I('end_datetime');
        $operator_id   = I('operator_id');
        $channel_id   = I('channel_id');
        $bc_channel_id=I('bc_channel_id');
        $province_id = I('province_id');
        $proxy_name = trim(I('proxy_name'));
        $product_name = trim(I('product_name'));

        $new_m = date('Ym',strtotime("-2 month"));     //历史最新月份
        $newt_m = date('Y-m',strtotime("-2 month"));     //历史最新月份

        //判断时间是否在三个月前
        if($start_datetime != "" || $end_datetime != ""){
            $s_m = '';      //开始时间的月份
            $e_m = '';      //结束时间的月份
            if(!empty($start_datetime)){
                $s_m = date('Ym',strtotime($start_datetime));
                if($s_m > $new_m){
                    $this->display('index');
                    echo "<script>alertbox({'status':'error','msg':'历史记录请查 $newt_m 以及之前的数据！'});history.back(); </script>";exit;
                }
            }
            if(!empty($end_datetime)){
                $e_m = date('Ym',strtotime($end_datetime));
                if($e_m > $new_m){
                    $this->display('index');
                    echo "<script>alertbox({'status':'error','msg':'历史记录请查 $newt_m 以及之前的数据！'});history.back(); </script>";exit;
                }
            }
        }
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            $s_m = date('Ym',strtotime($start_datetime));
            $e_m = date('Ym',strtotime($end_datetime));
            if($s_m != $e_m){
                $this->display('index');
                echo "<script>alertbox({'status':'error','msg':'查询时间请在同一个月！'});history.back(); </script>";exit;
            }
        }

        if($start_datetime || $end_datetime){
            if($start_datetime ==""){
                $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
            }
            if($end_datetime ==""){
                $end_datetime = end_time(msubstr($start_datetime,0,10,"utf-8",false));
            }
        }else {
            $start_datetime = date('Y-m-d 00:00:00',strtotime(date('Y-m-1')." -1 month -1 day"));
            $end_datetime = date('Y-m-d 23:59:59',strtotime(date('Y-m-1')." -1 month -1 day"));
        }

        $the_m = date('Ym',strtotime($start_datetime));
        $the_table = C('DB_PREFIX')."order_$the_m";
        $is_exist =  M()->query("SELECT COUNT(*) as the_exist FROM information_schema.tables WHERE table_name = '$the_table';");
        if($is_exist[0]['the_exist'] == 0){
            $this->display('index');
            echo "<script>alertbox({'status':'error','msg':'该月没数据！'});history.back(); </script>";exit;
        }


        //给定查询条件
        $user_type=D('SysUser')->self_user_type();
        $user_id=D('SysUser')->self_id(); //用户id
        $proxy_name = !empty($proxy_name) ? $proxy_name : '' ;
        $mobile = !empty($mobile) ? $mobile : '' ;
        $operator_id = !empty($operator_id) ? $operator_id : -1 ;  //运营商
        $upper_role=D('SysRole')->upper_role();
        if($upper_role['in']){
            $channel_id =!empty($upper_role['channel_id']) ? $upper_role['channel_id'] : '0' ; //通道
            $bc_channel_id =!empty($upper_role['channel_id']) ? '' : '0'; //备用通道
        }else{
            $channel_id = !empty($channel_id) ? $channel_id : '' ; //通道
            $bc_channel_id = !empty($bc_channel_id) ? $bc_channel_id : '' ; //备用通道
        }
        $province_id = !empty($province_id) ? $province_id : -1 ;  //省份
        $order_status = !empty($order_status) ? $order_status : '' ;  //订单状态
        $product_name = !empty($product_name) ? $product_name : '' ;  //产品名称

        //信息设置
        $file_url = $_SERVER["DOCUMENT_ROOT"]."/Public/ExcelFile/".date("Ymd")."/";  //文件地址
        if(!file_exists($file_url)) {
            @mkdir($file_url, 0777, true);
            @chmod($file_url, 0777);
        }


        $file_name_prefix = iconv("utf-8","gb2312","历史充值记录");  //前缀
        $file_name_suffix = ".xls";                                   //后缀
        $number_z = 25000;         //每次导出的总条数
        $number_c = 12;           //分批次数
        $zheng_int = 2;           //换文件名和次数
        $file_code = 1;           //名称序号
        $download_file = array();

        //将数据写入excel在下载
        for($oj=1;$oj<=$number_c;$oj++){
            //读取分批的数据
            $oder_list=D('Order')->recharge_excel_storing_process($the_m,$user_id,$proxy_name,'','',$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$oj,$number_z,-1);
            if(count($oder_list) <= 0) break;   //读取的数据为空时跳出循环
            if($oj%$zheng_int==1){
                $fp = '';
                $file_name = $file_name_prefix.date("YmdHis",time()).'('.$file_code.')'.$file_name_suffix; //新建文件名
                $fp = @fopen($file_url . $file_name, "ab"); //打开xls文件，如果不存在则创建
                //获取头部标题信息
                $xls_title = $this->order_list_title($user_type);
                fwrite($fp, $xls_title);                // 写入数据
                unset($xls_title);
            }

            $xls_content = $this->order_list_table($oder_list,$user_type);
            if(!empty($xls_content)) {
                fwrite($fp, $xls_content);                // 写入数据
                if(!in_array($file_name,$download_file)){
                    $download_file[] = $file_name;
                }
                unset($oder_list, $xls_content);             //注销数据变量
            }
            if($oj%$zheng_int==0){
                if($fp)fclose($fp);
                $file_code++;
            }
            if($oj==$number_c) break;               //到达数量时跳出循环
        }
        import('Org.Util.FileToZip');
        // 打包下载
        $handler = opendir($file_url);    //$file_url 文件所在目录
        closedir($handler);
        $scandir=new \traverseDir($file_url,$file_url);    //$save_path zip包文件目录
        //zip文件名
        $zip_name = $file_name_prefix.date("YmdHis",time()); //新建zip文件名
        $scandir->tozip($download_file,$zip_name);
    }


    public function export_txt_history(){
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $mobile = trim(I('mobile'));
        $order_status =I('order_status');
        $start_datetime = I('start_datetime');
        $end_datetime = I('end_datetime');
        $operator_id   = I('operator_id');
        $channel_id   = I('channel_id');
        $bc_channel_id=I('bc_channel_id');
        $province_id = I('province_id');
        $proxy_name = trim(I('proxy_name'));
        $product_name = trim(I('product_name'));
        $field_ids = '';

        $new_m = date('Ym',strtotime("-2 month"));     //历史最新月份
        $newt_m = date('Y-m',strtotime("-2 month"));     //历史最新月份

        //判断时间是否在三个月前
        if($start_datetime != "" || $end_datetime != ""){
            $s_m = '';      //开始时间的月份
            $e_m = '';      //结束时间的月份
            if(!empty($start_datetime)){
                $s_m = date('Ym',strtotime($start_datetime));
                if($s_m > $new_m){
                    $this->display('index');
                    echo "<script>alertbox({'status':'error','msg':'历史记录请查 $newt_m 以及之前的数据！'});history.back(); </script>";exit;
                }
            }
            if(!empty($end_datetime)){
                $e_m = date('Ym',strtotime($end_datetime));
                if($e_m > $new_m){
                    $this->display('index');
                    echo "<script>alertbox({'status':'error','msg':'历史记录请查 $newt_m 以及之前的数据！'});history.back(); </script>";exit;
                }
            }
        }
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            $s_m = date('Ym',strtotime($start_datetime));
            $e_m = date('Ym',strtotime($end_datetime));
            if($s_m != $e_m){
                $this->display('index');
                echo "<script>alertbox({'status':'error','msg':'查询时间请在同一个月！'});history.back(); </script>";exit;
            }
        }

        if($start_datetime || $end_datetime){
            if($start_datetime ==""){
                $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
            }
            if($end_datetime ==""){
                $end_datetime = end_time(msubstr($start_datetime,0,10,"utf-8",false));
            }
        }else {
            $start_datetime = date('Y-m-d 00:00:00',strtotime(date('Y-m-1')." -1 month -1 day"));
            $end_datetime = date('Y-m-d 23:59:59',strtotime(date('Y-m-1')." -1 month -1 day"));
        }

        $the_m = date('Ym',strtotime($start_datetime));
        $the_table = C('DB_PREFIX')."order_$the_m";
        $is_exist =  M()->query("SELECT COUNT(*) as the_exist FROM information_schema.tables WHERE table_name = '$the_table';");
        if($is_exist[0]['the_exist'] == 0){
            $this->display('index');
            echo "<script>alertbox({'status':'error','msg':'该月没数据！'});history.back(); </script>";exit;
        }


        //给定查询条件
        $user_type=D('SysUser')->self_user_type();
        $user_id=D('SysUser')->self_id(); //用户id
        $proxy_name = !empty($proxy_name) ? $proxy_name : '' ;
        $mobile = !empty($mobile) ? $mobile : '' ;
        $operator_id = !empty($operator_id) ? $operator_id : -1 ;  //运营商
        $upper_role=D('SysRole')->upper_role();
        if($upper_role['in']){
            $channel_id =!empty($upper_role['channel_id']) ? $upper_role['channel_id'] : '0' ; //通道
            $bc_channel_id =!empty($upper_role['channel_id']) ? '' : '0'; //备用通道
        }else{
            $channel_id = !empty($channel_id) ? $channel_id : '' ; //通道
            $bc_channel_id = !empty($bc_channel_id) ? $bc_channel_id : '' ; //备用通道
        }
        $province_id = !empty($province_id) ? $province_id : -1 ;  //省份
        $order_status = !empty($order_status) ? $order_status : '' ;  //订单状态
        $product_name = !empty($product_name) ? $product_name : '' ;  //产品名称


        //信息设置
        $file_url = $_SERVER["DOCUMENT_ROOT"]."/Public/TxtFile/".date("Ymd")."/";  //文件地址
        if(!file_exists($file_url)) {
            @mkdir($file_url, 0777, true);
            @chmod($file_url, 0777);
        }
        $file_name = iconv("utf-8","gb2312","历史充值记录").date("YmdHis",time()); //新建文件名
        $file_type = ".txt";      //文件后缀名
        $number_z = 50000;        //每次导出的总条数
        $number_c = 20;           //分批次数

        //将数据写入txt在下载
        $fp = @fopen($file_url . $file_name . $file_type, "ab"); //打开txt文件，如果不存在则创建
        //var_dump($number_c);
        for($oj=1;$oj<=$number_c;$oj++){
            //读取分批的数据
            //echo "2,".$user_id.",".$proxy_name.",".''.",".''.",".$mobile.",".$channel_id.",".$bc_channel_id.",".$operator_id.",".$province_id.",".$order_status.",".$start_datetime.",".$end_datetime.",".$product_name.",".$oj.",".$number_z.",".$sale_id;
            $oder_list=D('Order')->recharge_excel_storing_process($the_m,$user_id,$proxy_name,'','',$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$oj,$number_z,-1);
            if(count($oder_list) <= 0) break;   //读取的数据为空时跳出循环
            if($oj==1){
                //获取头部标题信息
                $txt_title = $this->order_list_title_txt($user_type,1,$field_ids);;
                fwrite($fp, $txt_title);                // 写入数据
                unset($txt_title);
            }
            //获取内容信息
            $txt_content = $this->order_list_table_txt($oder_list,$user_type,$field_ids);
            if(!empty($txt_content)) {
                fwrite($fp, $txt_content);                // 写入数据
            }
            unset($oder_list, $txt_content);             //注销数据变量
            if($oj==$number_c) break;               //到达数量时跳出循环
        }
        if($fp)fclose($fp);
        //下载excel文件
        $filename = $file_url.$file_name.$file_type;
        $filesize = filesize($filename);
        header("content-type:application/octet-stream");
        header("content-disposition:attachment;filename=".$file_name.$file_type);
        header("content-length:{$filesize}");
        readfile($filename);
    }

    /**
     * @param $order_status
     * 读取成功、失败、等待的条数和金额
     */
    private function status_info($order_status,$map,$join){
        //初始值默认为0
        $data['success_data']=$data['success_money']=$data['fail_data']=$data['fail_money']=$data['wait_data']=$data['wait_money']=0;
        if($order_status!==''){
            $info=D('Order')->all_data($map,$order_status,$join);
            if($order_status==='0'){
                $data['wait_data']=$info['count'];
                $data['wait_money']=$info['sum'];
            }else if($order_status==2){
                $data['success_data']=$info['count'];
                $data['success_money']=$info['sum'];
            }else if($order_status==5){
                $data['success_data']=$info['count'];
                $data['success_money']=$info['sum'];
            }else if($order_status==3){
                $data['fail_data']=$info['count'];
                $data['fail_money']=$info['sum'];
            }else if($order_status==6){
                $data['fail_data']=$info['count'];
                $data['fail_money']=$info['sum'];
            }else if($order_status=='6,3'){
                $data['fail_data']=$info['count'];
                $data['fail_money']=$info['sum'];
            }else if($order_status=='2,5'){
                $data['success_data']=$info['count'];
                $data['success_money']=$info['sum'];
            }else if($order_status=='0,1,4'){
                $data['wait_data']=$info['count'];
                $data['wait_money']=$info['sum'];
            }
        }else{
            $info1=D('Order')->all_data($map,'2,5',$join);
            $info2=D('Order')->all_data($map,'6,3',$join);
            $info3=D('Order')->all_data($map,'0,1,4',$join);
            $data['success_data']=$info1['count'];
            $data['success_money']=$info1['sum'];
            $data['fail_data']=$info2['count'];
            $data['fail_money']=$info2['sum'];
            $data['wait_data']=$info3['count'];
            $data['wait_money']=$info3['sum'];
        }
        return $data;
    }

    public function proxy_enterprise(){

        if(D('SysUser')->is_admin() or D('SysUser')->is_all_proxy(D('SysUser')->self_id())){
            $map['status'] = array('neq',2);
            $map['top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
            $map['approve_status']=1;
            $range_list = M('Proxy')->field('proxy_id')->where($map)->select();

        }else{
            //当不是管理员的时候
            $map['user_id'] = array('eq',D('SysUser')->self_id());
            $range_list = M('Proxy_user')->distinct(true)->field('proxy_id')->where($map)->select();
        }

        $proxy_ids = '';
        if($range_list){
            foreach($range_list as $v){
                $proxy_ids .= ','.$v['proxy_id'];
            }

            $proxy_ids = substr($proxy_ids,1,strlen($proxy_ids)-1);
        }
        return $proxy_ids;
    }


    /**
     * 导出excel
     */
    //一个excel文件的方式
    public function export_excel2(){
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $mobile = trim(I('mobile'));
        $order_status =I('order_status');
        $start_datetime = I('start_datetime');
        $end_datetime = I('end_datetime');
        $operator_id   = I('operator_id');
        $channel_id   = I('channel_id');
        $bc_channel_id=I('bc_channel_id');
        $province_id = I('province_id');
        $proxy_name = trim(I('proxy_name'));
        $product_name = trim(I('product_name'));
        $sale_name=trim(I('sale_name'));
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400){
                $this->display('index');
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过31天！'});history.back(); </script>";exit;
            }
        }

        if($start_datetime || $end_datetime){
            if($start_datetime ==""){
                $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
            }
            if($end_datetime ==""){
                $end_datetime = end_time(msubstr($start_datetime,0,10,"utf-8",false));
            }
        }else {
            $start_datetime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
            $end_datetime = date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('d'), date('Y')));
        }
        //给定查询条件
        $user_type=D('SysUser')->self_user_type();
        $user_id=D('SysUser')->self_id(); //用户id
        $proxy_name = !empty($proxy_name) ? $proxy_name : '' ;
        $mobile = !empty($mobile) ? $mobile : '' ;
        $operator_id = !empty($operator_id) ? $operator_id : -1 ;  //运营商
        $upper_role=D('SysRole')->upper_role();
        if($upper_role['in']){
            $channel_id =!empty($upper_role['channel_id']) ? $upper_role['channel_id'] : '0' ; //通道
            $bc_channel_id =!empty($upper_role['channel_id']) ? '' : '0'; //备用通道
        }else{
            $channel_id = !empty($channel_id) ? $channel_id : '' ; //通道
            $bc_channel_id = !empty($bc_channel_id) ? $bc_channel_id : '' ; //备用通道
        }
        $province_id = !empty($province_id) ? $province_id : -1 ;  //省份
        $order_status = !empty($order_status) ? $order_status : '' ;  //订单状态
        $product_name = !empty($product_name) ? $product_name : '' ;  //产品名称
        $sale_id=-1;
        if($sale_name){
            $con['user_name']=$sale_name;
            //$con['user_type']=$user_type;
            $con['status']=1;
            $user_sale_id=M('sys_user')->where($con)->field('user_id')->find();
            $sale_id=empty($user_sale_id)?0:intval($user_sale_id['user_id']);
        }
        //信息设置
        $file_url = $_SERVER["DOCUMENT_ROOT"]."/Public/ExcelFile/".date("Ymd")."/";  //文件地址
        if(!file_exists($file_url)) {
            @mkdir($file_url, 0777, true);
            @chmod($file_url, 0777);
        }
        $file_name = iconv("utf-8","gb2312","已完成充值记录").date("YmdHis",time()); //新建文件名
        $file_type = ".xls";      //文件后缀名
        $number_z = 10000;        //每次导出的总条数
        $number_c = 10;           //分批次数

        //将数据写入excel在下载
        $fp = @fopen($file_url . $file_name . $file_type, "ab"); //打开xls文件，如果不存在则创建
        //var_dump($number_c);
        for($oj=1;$oj<=$number_c;$oj++){
            //读取分批的数据
            //echo "2,".$user_id.",".$proxy_name.",".''.",".''.",".$mobile.",".$channel_id.",".$bc_channel_id.",".$operator_id.",".$province_id.",".$order_status.",".$start_datetime.",".$end_datetime.",".$product_name.",".$oj.",".$number_z.",".$sale_id;
            $oder_list=D('Order')->recharge_excel_storing_process(2,$user_id,$proxy_name,'','',$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$oj,$number_z,$sale_id);
            if(count($oder_list) <= 0) break;   //读取的数据为空时跳出循环
            if($oj==1){
                //获取头部标题信息
                $xls_title = $this->order_list_title($user_type);
                fwrite($fp, $xls_title);                // 写入数据
                unset($xls_title);
            }
            //获取内容信息
            $xls_content = $this->order_list_table($oder_list,$user_type);
            if(!empty($xls_content)) {
                fwrite($fp, $xls_content);                // 写入数据
            }
            unset($oder_list, $xls_content);             //注销数据变量
            if($oj==$number_c) break;               //到达数量时跳出循环
        }
        if($fp)fclose($fp);
        //下载excel文件
        $filename = $file_url.$file_name.$file_type;
        $filesize = filesize($filename);
        header("content-type:application/octet-stream");
        header("content-disposition:attachment;filename=".$file_name.$file_type);
        header("content-length:{$filesize}");
        readfile($filename);
    }

    /* public function detailed(){
         $this->export_excel2();
     }*/
    //打包为zip的方式
    public function export_excel(){
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $mobile = trim(I('mobile'));
        $order_status =I('order_status');
        $start_datetime = I('start_datetime');
        $end_datetime = I('end_datetime');
        $operator_id   = I('operator_id');
        $channel_id   = I('channel_id');
        $bc_channel_id=I('bc_channel_id');
        $province_id = I('province_id');
        $proxy_name = trim(I('proxy_name'));
        $product_name = trim(I('product_name'));
        $sale_name=trim(I('sale_name'));
        $field_ids=trim(I('field_ids'));
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400){
                $this->display('index');
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过31天！'});history.back(); </script>";exit;
            }
        }

        if($start_datetime || $end_datetime){
            if($start_datetime ==""){
                $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
            }
            if($end_datetime ==""){
                $end_datetime = end_time(msubstr($start_datetime,0,10,"utf-8",false));
            }
        }else {
            $start_datetime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
            $end_datetime = date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('d'), date('Y')));
        }
        //给定查询条件
        $user_type=D('SysUser')->self_user_type();
        $user_id=D('SysUser')->self_id(); //用户id
        $proxy_name = !empty($proxy_name) ? $proxy_name : '' ;
        $mobile = !empty($mobile) ? $mobile : '' ;
        $operator_id = !empty($operator_id) ? $operator_id : -1 ;  //运营商
        $upper_role=D('SysRole')->upper_role();
        if($upper_role['in']){
            $channel_id =!empty($upper_role['channel_id']) ? $upper_role['channel_id'] : '0' ; //通道
            $bc_channel_id =!empty($upper_role['channel_id']) ? '' : '0'; //备用通道
        }else{
            $channel_id = !empty($channel_id) ? $channel_id : '' ; //通道
            $bc_channel_id = !empty($bc_channel_id) ? $bc_channel_id : '' ; //备用通道
        }
        $province_id = !empty($province_id) ? $province_id : -1 ;  //省份
        $order_status = !empty($order_status) ? $order_status : '' ;  //订单状态
        $product_name = !empty($product_name) ? $product_name : '' ;  //产品名称
        $sale_id=-1;
        if($sale_name){
            $con['user_name']=$sale_name;
            //$con['user_type']=$user_type;
            $con['status']=1;
            $user_sale_id=M('sys_user')->where($con)->field('user_id')->find();
            $sale_id=empty($user_sale_id)?0:intval($user_sale_id['user_id']);
        }

        //信息设置
        $file_url = $_SERVER["DOCUMENT_ROOT"]."/Public/ExcelFile/".date("Ymd")."/";  //文件地址
        if(!file_exists($file_url)) {
            @mkdir($file_url, 0777, true);
            @chmod($file_url, 0777);
        }

        $file_name_prefix = iconv("utf-8","gb2312","已完成充值记录");  //前缀
        $file_name_suffix = ".xls";                                   //后缀
        $number_z = 25000;         //每次导出的总条数
        $number_c = 12;           //分批次数
        $zheng_int = 2;           //换文件名和次数
        $file_code = 1;           //名称序号
        $download_file = array();

        //将数据写入excel在下载
        for($oj=1;$oj<=$number_c;$oj++){
            //读取分批的数据
            $oder_list=D('Order')->recharge_excel_storing_process(2,$user_id,$proxy_name,'','',$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$oj,$number_z,$sale_id);
            if(count($oder_list) <= 0) break;   //读取的数据为空时跳出循环
            if($oj%$zheng_int==1){
                $fp = '';
                $file_name = $file_name_prefix.date("YmdHis",time()).'('.$file_code.')'.$file_name_suffix; //新建文件名
                $fp = @fopen($file_url . $file_name, "ab"); //打开xls文件，如果不存在则创建
                //获取头部标题信息
                $xls_title = $this->order_list_title($user_type,1,$field_ids);
                fwrite($fp, $xls_title);                // 写入数据
                unset($xls_title);
            }

            $xls_content = $this->order_list_table($oder_list,$user_type,$field_ids);
            if(!empty($xls_content)) {
                fwrite($fp, $xls_content);                // 写入数据
                if(!in_array($file_name,$download_file)){
                    $download_file[] = $file_name;
                }
                unset($oder_list, $xls_content);             //注销数据变量
            }
            if($oj%$zheng_int==0){
                if($fp)fclose($fp);
                $file_code++;
            }
            if($oj==$number_c) break;               //到达数量时跳出循环
        }
        import('Org.Util.FileToZip');
        // 打包下载
        $handler = opendir($file_url);    //$file_url 文件所在目录
        closedir($handler);
        $scandir=new \traverseDir($file_url,$file_url);    //$save_path zip包文件目录
        //zip文件名
        $zip_name = $file_name_prefix.date("YmdHis",time()); //新建zip文件名
        $scandir->tozip($download_file,$zip_name);
    }

    /**
     * 导出txt
     */
    //一个txt文件的方式
    public function export_txt(){
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $mobile = trim(I('mobile'));
        $order_status =I('order_status');
        $start_datetime = I('start_datetime');
        $end_datetime = I('end_datetime');
        $operator_id   = I('operator_id');
        $channel_id   = I('channel_id');
        $bc_channel_id=I('bc_channel_id');
        $province_id = I('province_id');
        $proxy_name = trim(I('proxy_name'));
        $product_name = trim(I('product_name'));
        $sale_name=trim(I('sale_name'));
        $field_ids=trim(I('field_ids'));
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400){
                $this->display('index');
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过31天！'});history.back(); </script>";exit;
            }
        }

        if($start_datetime || $end_datetime){
            if($start_datetime ==""){
                $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
            }
            if($end_datetime ==""){
                $end_datetime = end_time(msubstr($start_datetime,0,10,"utf-8",false));
            }
        }else {
            $start_datetime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
            $end_datetime = date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('d'), date('Y')));
        }
        //给定查询条件
        $user_type=D('SysUser')->self_user_type();
        $user_id=D('SysUser')->self_id(); //用户id
        $proxy_name = !empty($proxy_name) ? $proxy_name : '' ;
        $mobile = !empty($mobile) ? $mobile : '' ;
        $operator_id = !empty($operator_id) ? $operator_id : -1 ;  //运营商
        $upper_role=D('SysRole')->upper_role();
        if($upper_role['in']){
            $channel_id =!empty($upper_role['channel_id']) ? $upper_role['channel_id'] : '0' ; //通道
            $bc_channel_id =!empty($upper_role['channel_id']) ? '' : '0'; //备用通道
        }else{
            $channel_id = !empty($channel_id) ? $channel_id : '' ; //通道
            $bc_channel_id = !empty($bc_channel_id) ? $bc_channel_id : '' ; //备用通道
        }
        $province_id = !empty($province_id) ? $province_id : -1 ;  //省份
        $order_status = !empty($order_status) ? $order_status : '' ;  //订单状态
        $product_name = !empty($product_name) ? $product_name : '' ;  //产品名称
        $sale_id=-1;
        if($sale_name){
            $con['user_name']=$sale_name;
            //$con['user_type']=$user_type;
            $con['status']=1;
            $user_sale_id=M('sys_user')->where($con)->field('user_id')->find();
            $sale_id=empty($user_sale_id)?0:intval($user_sale_id['user_id']);
        }
        //信息设置
        $file_url = $_SERVER["DOCUMENT_ROOT"]."/Public/TxtFile/".date("Ymd")."/";  //文件地址
        if(!file_exists($file_url)) {
            @mkdir($file_url, 0777, true);
            @chmod($file_url, 0777);
        }
        $file_name = iconv("utf-8","gb2312","已完成充值记录").date("YmdHis",time()); //新建文件名
        $file_type = ".txt";      //文件后缀名
        $number_z = 50000;        //每次导出的总条数
        $number_c = 20;           //分批次数

        //将数据写入txt在下载
        $fp = @fopen($file_url . $file_name . $file_type, "ab"); //打开txt文件，如果不存在则创建
        //var_dump($number_c);
        for($oj=1;$oj<=$number_c;$oj++){
            //读取分批的数据
            //echo "2,".$user_id.",".$proxy_name.",".''.",".''.",".$mobile.",".$channel_id.",".$bc_channel_id.",".$operator_id.",".$province_id.",".$order_status.",".$start_datetime.",".$end_datetime.",".$product_name.",".$oj.",".$number_z.",".$sale_id;
            $oder_list=D('Order')->recharge_excel_storing_process(2,$user_id,$proxy_name,'','',$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$oj,$number_z,$sale_id);
            if(count($oder_list) <= 0) break;   //读取的数据为空时跳出循环
            if($oj==1){
                //获取头部标题信息
                $txt_title = $this->order_list_title_txt($user_type,1,$field_ids);;
                fwrite($fp, $txt_title);                // 写入数据
                unset($txt_title);
            }
            //获取内容信息
            $txt_content = $this->order_list_table_txt($oder_list,$user_type,$field_ids);
            if(!empty($txt_content)) {
                fwrite($fp, $txt_content);                // 写入数据
            }
            unset($oder_list, $txt_content);             //注销数据变量
            if($oj==$number_c) break;               //到达数量时跳出循环
        }
        if($fp)fclose($fp);
        //下载excel文件
        $filename = $file_url.$file_name.$file_type;
        $filesize = filesize($filename);
        header("content-type:application/octet-stream");
        header("content-disposition:attachment;filename=".$file_name.$file_type);
        header("content-length:{$filesize}");
        readfile($filename);
    }

    //处理导出txt标题
    private function order_list_title_txt($user_type,$inclube_table=1,$field_ids=''){
        if ($user_type == 1) {
            $upper_role=D('SysRole')->upper_role();
            $is_role=$this->is_role();
            $user_id = D('SysUser')->self_id(); //用户id

            if(in_array($user_id, C('EXCEL_SPECIAL_USER'))){
                $title = array("订单编号", "订单状态", "手机号", "提交时间", "完成时间", "运营商", "归属地", "流量包名称", "原价(元)","折后价格(元)","成本价格(元)","折扣数(折)", "通道编码","用户名称", "顶级代理","下游订单号","上游订单号","订单信息");
            }else{
                if($upper_role['in']){
                    $title = array("订单编号", "订单状态", "手机号", "提交时间", "完成时间", "运营商", "归属地", "流量包名称", "原价(元)","折后价格(元)","下游订单号","订单信息");
                }else{
                    if($is_role){
                        $title = array("订单编号", "订单状态", "手机号", "提交时间", "完成时间", "运营商", "归属地", "流量包名称", "原价(元)","折后价格(元)","成本价格(元)","折扣数(折)", "通道编码","用户名称", "顶级代理","下游订单号","订单信息");
                    }else{
                        $title = array("订单编号", "订单状态", "手机号", "提交时间", "完成时间", "运营商", "归属地", "流量包名称", "原价(元)","折后价格(元)", "通道编码","用户名称", "顶级代理","下游订单号","订单信息");
                    }
                }
            }


        } else {
            $title = array("订单编号", "订单状态", "手机号", "提交时间", "完成时间", "运营商", "归属地", "流量包名称","原价(元)", "折后价格(元)","用户名称","下游企业订单号");
        }
        if($inclube_table == 0){
            return $title;
        }

        if(!empty($field_ids)){
            $field_ids = explode(',', $field_ids);
            foreach($title as $k=>$v){
                if(!in_array($k, $field_ids)){
                    unset($title[$k]);
                }
            }
        }

        return implode(',', $title)."\r\n";
    }

    //处理txt导出内容
    private function order_list_table_txt($oder_list,$user_type,$field_ids=''){
        $tle = "";
        $upper_role=D('SysRole')->upper_role();
        $is_role=$this->is_role();

        if($user_type==1){

            $user_id = D('SysUser')->self_id(); //用户id
            if(in_array($user_id, C('EXCEL_SPECIAL_USER'))){
                foreach($oder_list as $k=>$v){
                    $time = explode(".",$v['order_date']);
                    $timec = explode(".",$v['complete_time']);
                    $channel_code = in_array($v['order_status'],array(4,5,6))?$v['bc_channel_code']:$v['channel_code'];
                    $product_name = $v['province_id']==1?"全国":"省内";
                    $order_status = get_order_status_st($v['order_status']);
                    $v['back_content'] = in_array($v['order_status'],array(2,5))?"":$v['back_content'];
                    $tle_arr = array();
                    $tle_arr[] = "D".$v['order_code'];
                    $tle_arr[] = $order_status;
                    $tle_arr[] = $v['mobile'];
                    $tle_arr[] = $time[0];
                    $tle_arr[] = $timec[0];
                    $tle_arr[] = $v['operator_name'];
                    $tle_arr[] = $v['province_name'].$v['city_name'];
                    $tle_arr[] = $product_name.$v['product_name'];
                    $tle_arr[] = $v['price'];
                    $tle_arr[] = $v['discount_price'];
                    $tle_arr[] = cost_price($v['price'],$v['top_discount'],$v['top_rebate_discount']);
                    $tle_arr[] = float_operate($v['price'],$v['discount_price']);
                    $tle_arr[] = $channel_code;
                    $tle_arr[] = $v['proxy_name'];
                    $tle_arr[] = $v['top_proxy_name'];
                    if(empty($v['orderno_id'])){
                        $tle_arr[] = $v['orderno_id'];
                    }else{
                        $tle_arr[] = "D".$v['orderno_id'];
                    }
                    if(empty($v['channel_order_code'])){
                        $tle_arr[] = "--";
                    }else{
                        $tle_arr[] = "D".$v['channel_order_code'];
                    }
                    $tle_arr[] =str_replace('<','《',str_replace('>','》',$v['back_content']));
                    if(!empty($field_ids)){
                        $field_ids_array = explode(',', $field_ids);
                        foreach($tle_arr as $k=>$v){
                            if(!in_array($k, $field_ids_array)){
                                unset($tle_arr[$k]);
                            }
                        }
                    }
                    $tle .= implode(",", $tle_arr)."\r\n";
                }

            }else{
                if($upper_role['in']){
                    foreach($oder_list as $k=>$v){
                        $time = explode(".",$v['order_date']);
                        $timec = explode(".",$v['complete_time']);
                        $product_name = $v['province_id']==1?"全国":"省内";
                        $order_status = get_order_status_st($v['order_status']);
                        $v['back_content'] = in_array($v['order_status'],array(2,5))?"":$v['back_content'];
                        $tle_arr = array();
                        $tle_arr[] = "D".$v['order_code'];
                        $tle_arr[] = $order_status;
                        $tle_arr[] = $v['mobile'];
                        $tle_arr[] = $time[0];
                        $tle_arr[] = $timec[0];
                        $tle_arr[] = $v['operator_name'];
                        $tle_arr[] = $v['province_name'].$v['city_name'];
                        $tle_arr[] = $product_name.$v['product_name'];
                        $tle_arr[] = $v['price'];
                        $tle_arr[] = $v['discount_price'];
                        if(empty($v['orderno_id'])){
                            $tle_arr[] = $v['orderno_id'];
                        }else{
                            $tle_arr[] = "D".$v['orderno_id'];
                        }
                        $tle_arr[] =str_replace('<','《',str_replace('>','》',$v['back_content']));

                        if(!empty($field_ids)){
                            $field_ids_array = explode(',', $field_ids);
                            foreach($tle_arr as $k=>$v){
                                if(!in_array($k, $field_ids_array)){
                                    unset($tle_arr[$k]);
                                }
                            }
                        }
                        $tle .= implode(",", $tle_arr)."\r\n";
                    }
                }else{
                    foreach($oder_list as $k=>$v){
                        $time = explode(".",$v['order_date']);
                        $timec = explode(".",$v['complete_time']);
                        $channel_code = in_array($v['order_status'],array(4,5,6))?$v['bc_channel_code']:$v['channel_code'];
                        $product_name = $v['province_id']==1?"全国":"省内";
                        $order_status = get_order_status_st($v['order_status']);
                        $v['back_content'] = in_array($v['order_status'],array(2,5))?"":$v['back_content'];
                        $tle_arr = array();
                        $tle_arr[] = "D".$v['order_code'];
                        $tle_arr[] = $order_status;
                        $tle_arr[] = $v['mobile'];
                        $tle_arr[] = $time[0];
                        $tle_arr[] = $timec[0];
                        $tle_arr[] = $v['operator_name'];
                        $tle_arr[] = $v['province_name'].$v['city_name'];
                        $tle_arr[] = $product_name.$v['product_name'];
                        $tle_arr[] = $v['price'];
                        $tle_arr[] = $v['discount_price'];
                        if($is_role){
                            $tle_arr[] = cost_price($v['price'],$v['top_discount'],$v['top_rebate_discount']);
                            $tle_arr[] = float_operate($v['price'],$v['discount_price']);
                        }
                        $tle_arr[] = $channel_code;
                        $tle_arr[] = $v['proxy_name'];
                        $tle_arr[] = $v['top_proxy_name'];
                        if(empty($v['orderno_id'])){
                            $tle_arr[] = $v['orderno_id'];
                        }else{
                            $tle_arr[] = "D".$v['orderno_id'];
                        }
                        $tle_arr[] =str_replace('<','《',str_replace('>','》',$v['back_content']));
                        if(!empty($field_ids)){
                            $field_ids_array = explode(',', $field_ids);
                            foreach($tle_arr as $k=>$v){
                                if(!in_array($k, $field_ids_array)){
                                    unset($tle_arr[$k]);
                                }
                            }
                        }
                        $tle .= implode(",", $tle_arr)."\r\n";
                    }
                }
            }
        }else{
            $preg_number= '/^\d+$/i';
            foreach($oder_list as $k=>$v){
                $z = preg_match($preg_number, $v['orderno_id'])?'D':'';
                $time = explode(".",$v['order_date']);
                $timec = explode(".",$v['complete_time']);
                $product_name = $v['province_id']==1?"全国":"省内";
                $order_status = get_order_status_st($v['order_status']);
                $tle_arr = array();
                $tle_arr[] = "D".$v['order_code'];
                $tle_arr[] = $order_status;
                $tle_arr[] = $v['mobile'];
                $tle_arr[] = $time[0];
                $tle_arr[] = $timec[0];
                $tle_arr[] = $v['operator_name'];
                $tle_arr[] = $v['province_name'].$v['city_name'];
                $tle_arr[] = $product_name.$v['product_name'];
                $tle_arr[] = $v['price'];
                $tle_arr[] = $v['discount_price'];
                $tle_arr[] = $v['proxy_name'];
                $tle_arr[] = $z.$v['orderno_id'];


                if(!empty($field_ids)){
                    $field_ids_array = explode(',', $field_ids);
                    foreach($tle_arr as $k=>$v){
                        if(!in_array($k, $field_ids_array)){
                            unset($tle_arr[$k]);
                        }
                    }
                }
                $tle .= implode(",", $tle_arr)."\r\n";
            }
        }

        return $tle;
    }

    //处理导出标题
    private function order_list_title($user_type,$inclube_table=1,$field_ids=''){
        if ($user_type == 1) {
            $upper_role=D('SysRole')->upper_role();
            $is_role=$this->is_role();
            $user_id = D('SysUser')->self_id(); //用户id

            if(in_array($user_id, C('EXCEL_SPECIAL_USER'))){
                $title = array("订单编号", "订单状态", "手机号", "提交时间", "完成时间", "运营商", "归属地", "流量包名称", "原价(元)","折后价格(元)","成本价格(元)","折扣数(折)", "通道编码","用户名称", "顶级代理","下游订单号","上游订单号","订单信息");
            }else{
                if($upper_role['in']){
                    $title = array("订单编号", "订单状态", "手机号", "提交时间", "完成时间", "运营商", "归属地", "流量包名称", "原价(元)","折后价格(元)","下游订单号","订单信息");
                }else{
                    if($is_role){
                        $title = array("订单编号", "订单状态", "手机号", "提交时间", "完成时间", "运营商", "归属地", "流量包名称", "原价(元)","折后价格(元)","成本价格(元)","折扣数(折)", "通道编码","用户名称", "顶级代理","下游订单号","订单信息");
                    }else{
                        $title = array("订单编号", "订单状态", "手机号", "提交时间", "完成时间", "运营商", "归属地", "流量包名称", "原价(元)","折后价格(元)", "通道编码","用户名称", "顶级代理","下游订单号","订单信息");
                    }
                }
            }


        } else {
            $title = array("订单编号", "订单状态", "手机号", "提交时间", "完成时间", "运营商", "归属地", "流量包名称","原价(元)", "折后价格(元)","用户名称","下游企业订单号");
        }
        if($inclube_table == 0){
            return $title;
        }

        if(!empty($field_ids)){
            $field_ids = explode(',', $field_ids);
            foreach($title as $k=>$v){
                if(!in_array($k, $field_ids)){
                    unset($title[$k]);
                }
            }
        }

        $tle = "<table border='1'><tr>";
        foreach($title as $v){
            $tle .= "<td>".$v."</td>";
        }
        $tle .= "</tr></table>";
        return $tle;
    }

    //处理导出内容
    private function order_list_table($oder_list,$user_type,$field_ids=''){
        $tle = "<table border='1'>";
        $upper_role=D('SysRole')->upper_role();
        $is_role=$this->is_role();

        if($user_type==1){

            $user_id = D('SysUser')->self_id(); //用户id
            if(in_array($user_id, C('EXCEL_SPECIAL_USER'))){
                foreach($oder_list as $k=>$v){
                    $time = explode(".",$v['order_date']);
                    $timec = explode(".",$v['complete_time']);
                    $channel_code = in_array($v['order_status'],array(4,5,6))?$v['bc_channel_code']:$v['channel_code'];
                    $product_name = $v['province_id']==1?"全国":"省内";
                    $order_status = get_order_status_st($v['order_status']);
                    $v['back_content'] = in_array($v['order_status'],array(2,5))?"":$v['back_content'];
                    $tle .= "<tr>";
                    $tle_arr = array();
                    $tle_arr[] = "<td>D".$v['order_code']."</td>";
                    $tle_arr[] = "<td> ".$order_status."</td>";
                    $tle_arr[] = "<td> ".$v['mobile']."</td>";
                    $tle_arr[] = "<td> ".$time[0]."</td>";
                    $tle_arr[] = "<td> ".$timec[0]."</td>";
                    $tle_arr[] = "<td> ".$v['operator_name']."</td>";
                    $tle_arr[] = "<td> ".$v['province_name'].$v['city_name']."</td>";
                    $tle_arr[] = "<td> ".$product_name.$v['product_name']."</td>";
                    $tle_arr[] = "<td> ".$v['price']."</td>";
                    $tle_arr[] = "<td> ".$v['discount_price']."</td>";
                    $tle_arr[] = "<td> ".cost_price($v['price'],$v['top_discount'],$v['top_rebate_discount'])."</td>";
                    $tle_arr[] = "<td> ".float_operate($v['price'],$v['discount_price'])."</td>";
                    $tle_arr[] = "<td> ".$channel_code."</td>";
                    $tle_arr[] = "<td>".$v['proxy_name']."</td>";
                    $tle_arr[] = "<td>".$v['top_proxy_name']."</td>";
                    if(empty($v['orderno_id'])){
                        $tle_arr[] = "<td>".$v['orderno_id']."</td>";
                    }else{
                        $tle_arr[] = "<td>D".$v['orderno_id']."</td>";
                    }
                    if(empty($v['channel_order_code'])){
                        $tle_arr[] = "<td>--</td>";
                    }else{
                        $tle_arr[] = "<td>D".$v['channel_order_code']."</td>";
                    }
                    $tle_arr[] ="<td nowrap='nowrap'>".str_replace('<','《',str_replace('>','》',$v['back_content']))."</td>";
                    if(!empty($field_ids)){
                        $field_ids_array = explode(',', $field_ids);
                        foreach($tle_arr as $k=>$v){
                            if(!in_array($k, $field_ids_array)){
                                unset($tle_arr[$k]);
                            }
                        }
                    }
                    $tle .= implode("", $tle_arr);
                    $tle .= "</tr>";
                }
                
            }else{
                if($upper_role['in']){
                    foreach($oder_list as $k=>$v){
                        $time = explode(".",$v['order_date']);
                        $timec = explode(".",$v['complete_time']);
                        $product_name = $v['province_id']==1?"全国":"省内";
                        $order_status = get_order_status_st($v['order_status']);
                        $v['back_content'] = in_array($v['order_status'],array(2,5))?"":$v['back_content'];
                        $tle .= "<tr>";
                        $tle_arr = array();
                        $tle_arr[] = "<td>D".$v['order_code']."</td>";
                        $tle_arr[] = "<td> ".$order_status."</td>";
                        $tle_arr[] = "<td> ".$v['mobile']."</td>";
                        $tle_arr[] = "<td> ".$time[0]."</td>";
                        $tle_arr[] = "<td> ".$timec[0]."</td>";
                        $tle_arr[] = "<td> ".$v['operator_name']."</td>";
                        $tle_arr[] = "<td> ".$v['province_name'].$v['city_name']."</td>";
                        $tle_arr[] = "<td> ".$product_name.$v['product_name']."</td>";
                        $tle_arr[] = "<td> ".$v['price']."</td>";
                        $tle_arr[] = "<td> ".$v['discount_price']."</td>";
                        if(empty($v['orderno_id'])){
                            $tle_arr[] = "<td>".$v['orderno_id']."</td>";
                        }else{
                            $tle_arr[] = "<td>D".$v['orderno_id']."</td>";
                        }
                        $tle_arr[] ="<td nowrap='nowrap'>".str_replace('<','《',str_replace('>','》',$v['back_content']))."</td>";

                        if(!empty($field_ids)){
                            $field_ids_array = explode(',', $field_ids);
                            foreach($tle_arr as $k=>$v){
                                if(!in_array($k, $field_ids_array)){
                                    unset($tle_arr[$k]);
                                }
                            }
                        }
                        $tle .= implode("", $tle_arr);
                        $tle .= "</tr>";
                    }
                }else{
                    foreach($oder_list as $k=>$v){
                        $time = explode(".",$v['order_date']);
                        $timec = explode(".",$v['complete_time']);
                        $channel_code = in_array($v['order_status'],array(4,5,6))?$v['bc_channel_code']:$v['channel_code'];
                        $product_name = $v['province_id']==1?"全国":"省内";
                        $order_status = get_order_status_st($v['order_status']);
                        $v['back_content'] = in_array($v['order_status'],array(2,5))?"":$v['back_content'];
                        $tle .= "<tr>";
                        $tle_arr = array();
                        $tle_arr[] = "<td>D".$v['order_code']."</td>";
                        $tle_arr[] = "<td> ".$order_status."</td>";
                        $tle_arr[] = "<td> ".$v['mobile']."</td>";
                        $tle_arr[] = "<td> ".$time[0]."</td>";
                        $tle_arr[] = "<td> ".$timec[0]."</td>";
                        $tle_arr[] = "<td> ".$v['operator_name']."</td>";
                        $tle_arr[] = "<td> ".$v['province_name'].$v['city_name']."</td>";
                        $tle_arr[] = "<td> ".$product_name.$v['product_name']."</td>";
                        $tle_arr[] = "<td> ".$v['price']."</td>";
                        $tle_arr[] = "<td> ".$v['discount_price']."</td>";
                        if($is_role){
                            $tle_arr[] = "<td> ".cost_price($v['price'],$v['top_discount'],$v['top_rebate_discount'])."</td>";
                            $tle_arr[] = "<td> ".float_operate($v['price'],$v['discount_price'])."</td>";
                        }
                        $tle_arr[] = "<td> ".$channel_code."</td>";
                        $tle_arr[] = "<td>".$v['proxy_name']."</td>";
                        $tle_arr[] = "<td>".$v['top_proxy_name']."</td>";
                        if(empty($v['orderno_id'])){
                            $tle_arr[] = "<td>".$v['orderno_id']."</td>";
                        }else{
                            $tle_arr[] = "<td>D".$v['orderno_id']."</td>";
                        }
                        $tle_arr[] ="<td nowrap='nowrap'>".str_replace('<','《',str_replace('>','》',$v['back_content']))."</td>";
                        if(!empty($field_ids)){
                            $field_ids_array = explode(',', $field_ids);
                            foreach($tle_arr as $k=>$v){
                                if(!in_array($k, $field_ids_array)){
                                    unset($tle_arr[$k]);
                                }
                            }
                        }
                        $tle .= implode("", $tle_arr);
                        $tle .= "</tr>";
                    }
                }
            }
        }else{
            $preg_number= '/^\d+$/i';
            foreach($oder_list as $k=>$v){
                $z = preg_match($preg_number, $v['orderno_id'])?'D':'';
                $time = explode(".",$v['order_date']);
                $timec = explode(".",$v['complete_time']);
                $product_name = $v['province_id']==1?"全国":"省内";
                $order_status = get_order_status_st($v['order_status']);
                $tle .= "<tr>";
                $tle_arr = array();
                $tle_arr[] = "<td>D".$v['order_code']."</td>";
                $tle_arr[] = "<td> ".$order_status."</td>";
                $tle_arr[] = "<td> ".$v['mobile']."</td>";
                $tle_arr[] = "<td> ".$time[0]."</td>";
                $tle_arr[] = "<td> ".$timec[0]."</td>";
                $tle_arr[] = "<td> ".$v['operator_name']."</td>";
                $tle_arr[] = "<td> ".$v['province_name'].$v['city_name']."</td>";
                $tle_arr[] = "<td> ".$product_name.$v['product_name']."</td>";
                $tle_arr[] = "<td> ".$v['price']."</td>";
                $tle_arr[] = "<td> ".$v['discount_price']."</td>";
                $tle_arr[] = "<td>".$v['proxy_name']."</td>";
                $tle_arr[] = "<td>".$z.$v['orderno_id']."</td>";


                if(!empty($field_ids)){
                    $field_ids_array = explode(',', $field_ids);
                    foreach($tle_arr as $k=>$v){
                        if(!in_array($k, $field_ids_array)){
                            unset($tle_arr[$k]);
                        }
                    }
                }
                $tle .= implode("", $tle_arr);
                $tle .= "</tr>";
            }
        }


        $tle .= "</table>";
        return $tle;
    }

    public function show(){
        $user_type=D('SysUser')->self_user_type();
        $proxy_id = D("SysUser")->self_proxy_id();
        $where['o.order_id']=trim(I('order_id'));//充值
        $list=D('Order')->orderdetail($where);
        if($list['proxy_level']==1){
            $list['top_proxy_id']=$list['proxy_id'];
        }
        $upper_role=D('SysRole')->upper_role();
        $upper_role_info=$upper_role['in']?'1':'2';
        $this->assign("user_proxy_id",$proxy_id);
        $this->assign('upper_role',$upper_role_info);
        $this->assign($list);
        $this->assign('is_role',$this->is_role());
        $this->assign('user_type',$user_type);
        $this->display('detailed');
    }

    public function show_history(){
        $user_type=D('SysUser')->self_user_type();
        $the_m = trim(I('order_date'));

        $where['o.order_id']=trim(I('order_id'));//充值
        $model=M("order_$the_m  o");
        $list =$model
            ->join('left join t_flow_proxy as p on p.proxy_id = o.proxy_id') //代理商
            ->join('left join t_flow_enterprise as e on e.enterprise_id = o.enterprise_id') //企业
            ->join('left join t_flow_sys_operator as op on op.operator_id = o.operator_id')  //运营商
            ->join('left join t_flow_channel as c on c.channel_id = o.channel_id')  //通道
            ->join('left join t_flow_channel as bc on bc.channel_id = o.back_channel_id')  //备用通道
            ->join('left join t_flow_sys_province as pr on pr.province_id = o.province_id')   //省份
            ->join('left join t_flow_channel_product as pp on pp.product_id = o.channel_product_id')  //产品
            ->join ('left join t_flow_sys_mobile_dict as dt on dt.mobile=o.mobile')  //城市
            ->field('o.order_code,o.order_id,o.channel_order_code,o.mobile,o.operator_id,o.top_rebate_discount,o.top_discount,o.source_type,o.back_content,o.one_proxy_name,pr.province_id,o.back_fail_desc,o.price,o.discount_price,o.channel_id,o.order_date,o.complete_time,o.order_status,o.proxy_name,p.proxy_code,p.proxy_level,p.proxy_id,o.enterprise_name,e.enterprise_id,e.enterprise_code,op.operator_name,c.channel_code,bc.channel_code as bc_channel_code,pr.province_name,pp.product_name,dt.city_name,dt.province_name')
            ->where($where)
            ->find();

        if($list['proxy_level']==1){
            $list['top_proxy_id']=$list['proxy_id'];
        }

        $upper_role=D('SysRole')->upper_role();
        $upper_role_info=$upper_role['in']?'1':'2';
        $this->assign('upper_role',$upper_role_info);
        $this->assign($list);
        $this->assign('is_role',$this->is_role());
        $this->assign('user_type',$user_type);
        $this->display('detailed');
    }

    public function callback(){
        $msg = '系统错误！';
        $status = 'error';
        $user_type=D('SysUser')->self_user_type();

        if(IS_POST){
            $order_id=trim(I('order_id'));
            $where['order_id']=trim(I('order_id'));//获取回调订单id
            $order_info=M('order')->field('user_type,order_code,proxy_name,proxy_id,enterprise_name,enterprise_id')->where('order_id='.$order_id)->find();
            /* if($user_type==2){
                 $where_url['proxy_id']=D('SysUser')->self_proxy_id();
             }elseif($user_type==3){
                 $where_url['enterprise_id']=D('SysUser')->self_enterprise_id();
             }elseif($user_type==1){
               if($order_info['user_type']==1){
                   $where_url['proxy_id']=$order_info['proxy_id'];
               }else{
                   $where_url['enterprise_id']=$order_info['enterprise_id'];
               }

             }
            $url='http://58.17.3.136:76/send.php';
            */
            if($order_info['user_type']==1){
                $where_url['proxy_id']=$order_info['proxy_id'];
            }else{
                $where_url['enterprise_id']=$order_info['enterprise_id'];
            }

            $list=M('order_callback_his')->where($where)->find();
            $list_url=M('sys_api')->where($where_url)->find();
            $url=$list_url['api_callback_address'];
            $data=$list['content'];
            $time=$list['times'];
            if($time<=6){
                if(!empty($url)){
                    if(!empty($data)){
                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/json',
                                'Content-Length: ' . strlen($data))
                        );
                        $result = curl_exec($ch);
                        curl_close($ch);
                        $status = 'success';
                        $msg = '推送回调成功！';
                        $n_msg='成功';
                        if($result=="Success"){
                            $save['status']=1;
                        }
                        $save['times']=$time+1;
                        M('order_callback_his')->where( $where)->save($save);
                    }else{
                        $msg = '正在充值中，请稍等！';
                        $n_msg='正在充值中';
                    }
                }else{
                    $msg = '回调地址为空，请检查回调地址！';
                    $n_msg='地址为空';
                }
            }else{
                $msg = '推送回调次数过多！';
                $n_msg='次数过多';
            }
            if(IS_AJAX){
                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】,推送订单【'.$order_info['order_code'].'】回调'.$n_msg;
                $this->sys_log('已完成充值记录推送回调',$note);
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
        }
    }

    public function orderrefund(){
        $where['order_id']=trim(I('order_id'));//退款
        $list=D('Order')->orderdetail($where);
        $this->assign("refund_cause_list",get_refund_cause());
        $this->assign($list);
        $this->display('order_refund');
    }

    public function insert(){
        $user_type=D('SysUser')->self_user_type();
        $msg = '系统错误!';
        $status = 'error';
        $data = array();
        // $start=$time=date("Y-m-d H:i:s",strtotime("-3 day"));
        //  $end=date('Y-m-d H:i:s');
        $where['order_id']=trim(I('order_id'));
        if($user_type=="2"){
            $proxy_chd=D('Proxy')->proxy_child();
            if(empty($proxy_chd)){
                $proxy_chd = array(D('SysUser')->self_proxy_id());
            }else{
                array_unshift($proxy_chd,D('SysUser')->self_proxy_id());
            }
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
        //   $where['complete_time']=array('between',array($start,$end));
        $order = M('order')->where($where)->find();
        $orderfund =$model->where(array('order_code'=>$order['refund_id']))->find();
        if(I('discount_price')==""){
            $msg = '请确定退款金额！';
        }elseif(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', I('discount_price'))){
            $msg = '退款金额请输入数字！';
        }elseif(I('discount_price')<0){
            $msg = '退款金额需大于零！';
        }elseif(I("order_code")==""){
            $msg = '请输入订单号！';
        }elseif(!$order){
            $msg = '对不起,您没有操作该订单的权限或订单号输入错误,请重试!';
        }/*elseif(strtotime($order['complete_time'])<strtotime($start)){
            $msg = '对不起，该订单退款时间已过，请联系客服！';
        }*/
        elseif($orderfund){
            $msg = '请勿重复提交申请退款！';
        }elseif(I('discount_price')!=$order['discount_price']){
            $msg = '退款金额与退款订单金额不匹配！';
        }elseif(I('mobile')==""){
            $msg = '请输入退款手机号！';
        }elseif(I('mobile')!==$order['mobile']){
            $msg = '请核对退款手机号！';
        }elseif(in_array($order['order_status'],array(0,1,3,4,6))){
            $msg = '非成功订单状态不能申请退款！';
        }elseif(I("refund_cause")==""){
            $msg = "请输入退款退款理由";
        }elseif(I("remark")==""){
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

            //if( $user_type==1){
            $data['proxy_id']=$order['proxy_id'];
            $data['enterprise_id']=$order['enterprise_id'];
            /* }else{
                 $data['proxy_id']=D('SysUser')->self_proxy_id();
                 $data['enterprise_id']=D('SysUser')->self_enterprise_id();
             }*/

            $data['create_user_id']=D('SysUser')->self_id();
            $data['user_type']=$order['user_type'];
            $data['order_id']=$order['order_id'];
            $apply_code="TKSQD".date('Ymdhis',time());
            $data['refund_code']=$apply_code;
            $data['price']=$order['price'];
            $data['discount_price']=$order['discount_price'];
            $data['credential_one']=$icense_img;
            $data['mobile']=trim(I('mobile'));
            $data['order_code']=trim(I('order_code'));
            $data['channel_order_code']=$order['channel_order_code'];
            if($order['order_status']==2){
                $data['channel_id']=$order['channel_id'];
                $data['channel_product_id']=$order['channel_product_id'];
            }elseif($order['order_status']==5){
                $data['channel_id']=$order['back_channel_id'];
                $data['channel_product_id']=$order['back_channel_product_id'];
            }
            $data['refund_cause']=I("refund_cause");
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
            $id= M('order_refund')->add($data);
            $order_infos['refund_id']=$id;
            $order_info=M('order')->where('order_id='.$order['order_id'])->save($order_infos);
            if($order['user_type']==1){
                $info=$this->refund_info($order['proxy_id'],1); //代理商
                $type_name='代理商';
            }else{
                $info=$this->refund_info($order['enterprise_id'],2);//企业
                $type_name='企业';
            }
            if($id && $order_info) {
                $model->commit();
                $msg = '新增退款成功！';
                $status = 'success';
                $n_msg='成功';
                $remind_content='代理商/企业【'.$info['name'].'】退款申请单已提交，请进行初审！';
                R('ObjectRemind/send_user',array(13,$remind_content,array($info['sale_id'])));
            }else{
                $model->rollback();
                $msg = '新增退款失败！';
                $n_msg='失败';
            }

            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$id.'】，新增退款申请单，'.$type_name.'【'.$info['name'].'('.$info['code'].')】，退款编号【'.$apply_code.'】，退款金额【'.money_format2($data['price']).'】元，手机号【'.$data['mobile'].'】，订单编号【'.$data['order_code'].'】'.$n_msg;
            $this->sys_log('新增退款申请单',$note);
        }
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    function  refund_info($id,$type){
        $info=array();
        if($type==1){
            $info=M('proxy')->where('proxy_id='.$id)->field('sale_id,proxy_name as name,proxy_code as code')->find();
        }else{
            $info=M('enterprise')->where('enterprise_id='.$id)->field('sale_id,enterprise_name as name,enterprise_code as code')->find();
        }
        return $info;
    }

    //判断用户id或者角色id是否可以显示已完成充值记录的成本价格 ，可以返回true   否则返回false
    public function  is_role(){
        $user_id=D('SysUser')->self_id();
        $role_id=M('sys_user_role')->where('user_id='.$user_id)->field('role_id')->select();
        $role_ids=get_array_column($role_id,'role_id');
        $user_order_set=M('sys_user_order_set')->field('set_orle_id,set_user_id')->select();
        $result=false;
        foreach($user_order_set as $set){
            if($user_id==$set['set_user_id']){
                $result=true;
                break;
            }else{
                $set_orle_id=str_replace('，',',',$set['set_orle_id']);
                $set_orle_id_arr=explode(',',$set_orle_id);
                $res=array_intersect($role_ids,$set_orle_id_arr);
                if(!empty($res)){
                    $result=true;
                    break;
                }
            }
        }
        return $result ;
    }

    /**
     * Excel导入订单号
     */
    public function orders_match(){
        $download = I('download');

        $file_z_name = "orders_match";
        $file_type = ".xls";      //文件后缀名
        if($download){
            $filename = I('filename');
            $filesize = filesize($filename);

            header("content-type:application/octet-stream");
            header("content-disposition:attachment;filename=" . '对账匹配记录' . $file_type);
            header("content-length:{$filesize}");
            readfile($filename);
        }else{
            //上传excel列出订单号
            if ($_FILES["file"] == null) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '文件上传失败'));
            }
            $filetype = array(
                'application/vnd.ms-excel',
                'application/octet-stream',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            );
            if (!in_array($_FILES["file"]['type'],$filetype)) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '请上传 Excel 文档！'));
            }
            $list = readExcel($_FILES["file"]["tmp_name"]);
            $order_nos = array();
            $max_len = 1000;
            $list_count = count($list);
            $j = 0;
            $now_len = $max_len;
            $order_all_list = array();

            //判断导入的Excel是订单号、上游订单号、手机号
            $sel_type = trim($list[0][0]);
            $sel_start_time = trim($list[0][1]);
            $sel_end_time = trim($list[0][2]);

            $for_where = '';
            if($sel_start_time){
                $sel_start_time_arr = explode('-', $sel_start_time);
                $sel_start_time = $sel_start_time_arr[2] . '-' . $sel_start_time_arr[0] . '-' . $sel_start_time_arr[1];
                $sel_start_time = date('Y-m-d 00:00:00',strtotime($sel_start_time));
                $for_where .= ' and order_date>="'. $sel_start_time .'"';
            }

            if($sel_end_time){
                $sel_end_time_arr = explode('-', $sel_end_time);
                $sel_end_time = $sel_end_time_arr[2] . '-' . $sel_end_time_arr[0] . '-' . $sel_end_time_arr[1];
                $sel_end_time = date('Y-m-d 23:59:59',strtotime($sel_end_time));
                $for_where .= ' and order_date<="'. $sel_end_time .'"';
            }

            $field_sel_name = 'order_code';
            if($sel_type == '订单号'){
                $field_sel_name = 'order_code';
            }elseif ($sel_type == '上游订单号'){
                $field_sel_name = 'channel_order_code';
            }else{
                $field_sel_name = 'mobile';
            }


            //每次达到指定长度后就写入文件
            for( $i=1; $i<=$list_count; $i++ ){
                if( $now_len == $i || $list_count == $i){
                    $where_in = '';
                    foreach($order_nos[$j] as $vv){
                        $vv = trim($vv);
                        if($sel_type == '订单号'){
                            $vv = trim($vv,'D');
                        }
                        $where_in .= ",'" . $vv . "'";
                    }
                    unset($vv);
                    $where_in = trim($where_in,',');
                    $sql = "SELECT order_code, mobile, LEFT (order_date, 19) AS order_date, complete_time,
	                    CASE operator_id
                        WHEN 1 THEN
                            '中国移动'
                        WHEN 2 THEN
                            '中国联通'
                        WHEN 3 THEN
                            '中国电信'
                        END operator_name,
                         product_name,
                         price,
                         CASE
                        WHEN order_status IN (0, 1, 2) THEN
                            round(price * (
                                top_discount - IFNULL(top_rebate_discount, 0)
                            ),3)
                        WHEN order_status IN (3, 4, 5, 6) THEN
                            round(price * (
                                back_top_discount - IFNULL(back_top_rebate_discount, 0)
                            ),3)
                        END top_price,order_status ostatus,
                         CASE order_status
                        WHEN 0 THEN
                            '正在送充'
                        WHEN 1 THEN
                            '正在送充'
                        WHEN 2 THEN
                            '充值成功'
                        WHEN 3 THEN
                            '充值失败'
                        WHEN 4 THEN
                            '正在送充'
                        WHEN 5 THEN
                            '充值成功'
                        WHEN 6 THEN
                            '充值失败'
                        END order_status,
                        CASE
                        WHEN order_status IN (0, 1, 2) THEN
                            channel_code
                        WHEN order_status IN (3, 4, 5, 6) THEN
                            back_channel_code
                        END channel_code,
                        back_content
                        FROM
                            t_flow_order where " . $field_sel_name . " in ($where_in) $for_where ";
                    $order_list = M('')->query($sql);

                    if(!empty($order_list)){
                        $order_all_list = array_merge($order_all_list,$order_list);
                        unset($order_list);
                    }
                    $j++;
                    $now_len += $max_len;
                }

                $order_nos[$j][] = $list[$i][0];
            }
            if(empty($order_nos)){
                $this->ajaxReturn(array('status' => 'error', 'msg' => '没有合适的订单号！'));
            }

            //写入Excel
            $file_url = $_SERVER["DOCUMENT_ROOT"]."/Public/ExcelFile/".date("Ymd")."/";  //文件地址
            if(!file_exists($file_url)) {
                @mkdir($file_url, 0777, true);
                @chmod($file_url, 0777);
            }

            $file_name = $file_z_name.date("YmdHis",time()); //新建文件名


            //将数据写入excel在下载
            $fp = @fopen($file_url . $file_name . $file_type, "ab"); //打开xls文件，如果不存在则创建

            //标题
            $xls_title = "<table border='1'><tr><td>订单号</td><td>手机号</td><td>提交时间</td><td>完成时间</td><td>运营商</td><td>流量包名称</td><td>原价</td><td>成本价格</td><td>订单状态</td><td>通道编码</td><td>订单信息</td></tr></table>";
            //$xls_title = iconv("utf-8","gb2312",$xls_title);
            fwrite($fp, $xls_title);                // 写入数据
            unset($xls_title);

            //内容
            $xls_content = "<table border='1'>";
            foreach($order_all_list as $v){
                $v['complete_time'] = substr($v['complete_time'],0,19);
                $v['order_code'] = 'D'.$v['order_code'];
                $v['back_content'] = in_array($v['ostatus'],array(2,5))?"":$v['back_content'];
                $v['back_content'] = str_replace('<','《',str_replace('>','》',$v['back_content']));
                $xls_content .= '<tr><td>'.$v['order_code'].'</td><td>'.$v['mobile'].'</td><td>'.$v['order_date'].
                    '</td><td>'.$v['complete_time'].'</td><td>'.$v['operator_name'].'</td><td>'.$v['product_name'].
                    '</td><td>'.$v['price'].'</td><td>'.$v['top_price'].'</td><td>'.$v['order_status'].'</td><td>'.$v['channel_code'].'</td><td>'.$v['back_content'].'</td></tr>';

            }
            unset($order_all_list);
            $xls_content .= "</table>";
            //$xls_content = iconv("utf-8","gb2312",$xls_content);
            fwrite($fp, $xls_content);                // 写入数据
            unset($xls_content);

            if($fp)fclose($fp);

            //下载excel文件
            $filename = $file_url.$file_name.$file_type;
            echo $filename;
        }





    }

    /*弹出字段选择*/
    public function export_excel_selected(){
        $user_type=D('SysUser')->self_user_type();
        $title = $this->order_list_title($user_type,0);     //0：直接获取title数组

        $str = "<table class='fields_selected'><tr>";
        $len = count($title);
        for($i=0;$i<$len;$i++){
            $str .= '<td class="change"><label class="checkbox checked" value="'.$i.'"><em>'.$title[$i].'</em></label></td>';
            if( ($i+1)%3 == 0 ){
                $str .= '</tr>';
                if($i != $len-1 ){
                    $str .= '<tr>';
                }
            }
        }

        $null_tab = 3-($len%3);
        if($null_tab>0){
            for($j=0;$j<$null_tab;$j++){
                $str .= '<th class="change"></th>';
            }
        }

        $str .= "</tr></table>";
        $this->assign("table_str",$str);
        $this->display('selected_to_excel');
    }






}
?>