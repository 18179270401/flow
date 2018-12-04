<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class OrdersController extends CommonController{
	/*
	 *未支付订单
	 */
	public function unpaid(){
        set_time_limit(0);
        D("SysUser")->sessionwriteclose();
        $where=array();
        $proxy_code=trim(I('proxy_code')) ;
        $enterprise_code=trim(I('enterprise_code')) ;
        $order_code=trim(I('order_code')) ;
        $mobile=trim(I('mobile')) ;
        $start_datetime=trim(I('start_datetime')) ;
        $end_datetime=trim(I('end_datetime')) ;
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                $this->display('index');
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }
        if($start_datetime || $end_datetime){
            if($start_datetime && $end_datetime){
                $where['o.order_date'] = array('between',array($start_datetime,$end_datetime));
            }
            if($start_datetime ==""){
                $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
                $where['o.order_date'] = array('between',array($start_datetime,$end_datetime));
            }
            if($end_datetime ==""){
                $end_datetime = end_time(msubstr($start_datetime,0,10,"utf-8",false));
                $where['o.order_date'] = array('between',array($start_datetime,$end_datetime));
            }
        }else {
            /*$start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            $end_datetime= strtotime($start_datetime)-2592000;
            $e_time=start_time(date('Y-m-d',$end_datetime));
            $d_sdata=$e_time;
            $d_edata=$start_datetime;*/
            $start_datetime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
            $end_datetime = date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('d'), date('Y')));
            $where['o.order_date']= array('between',array($start_datetime,$end_datetime));
        }
        $enterprise_ids = D('Enterprise')->enterprise_ids();
            if($proxy_code){
                    $where['p.proxy_code']=$proxy_code;
            }
            if($enterprise_code ){
                    $where['e.enterprise_code']=$enterprise_code;
            }
        if($order_code){
            $where['o.order_code']=$order_code;
        }
        if($mobile){
            $where['o.mobile']=$mobile;
        }
        //$where['o.user_type']=1; //1代理商用户  2企业用户
        $where['o.is_payment']=0;//是否付款，0未付款，1已付款
        $self_proxy_id=D('SysUser')->self_proxy_id();//当前代理商
        if($self_proxy_id!==""){
            $proxy_child_ids_array = $self_proxy_id.",".D('Proxy')->proxy_child_s();
            if($proxy_child_ids_array!='' && $enterprise_ids!='' ){
                $map['o.proxy_id'] = array('in',$proxy_child_ids_array);
                $map['o.enterprise_id'] = array('in',$enterprise_ids);
                $map['_logic'] = 'or';
                $where['_complex'] = $map;
            }else{
                $where['o.proxy_id'] = array('in',$proxy_child_ids_array);
            }
        }else{
            if($enterprise_ids!==""){
                $map['o.enterprise_id'] = array('in',$enterprise_ids);
                $map['o.proxy_id'] = array('in',$self_proxy_id);
                $map['_logic'] = 'or';
                $where['_complex'] = $map;
            }else{
                $where['o.proxy_id'] =$self_proxy_id;
            }
        }
        $list=D('Order')->orderList($where);
        //加载模板
            //var_dump($list);
        $this->assign('list',$list['list']);  //数据列表
        $this->assign('page',$list['page']);            //分类
        $this->assign('orderStatus',1);
        $this->assign('url','Orders/unpaid_excel');
        $this->assign('record','unpaid');
        $this->assign('d_sdata',date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y'))));  //默认开始时间
        $this->assign('d_edata',date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('d'), date('Y'))));  //默认结束时间
        $this->display('index');      //模板
    }

        /*
        *已完成订单
      */
        public function completed(){
                set_time_limit(0);
                D("SysUser")->sessionwriteclose();
                $user_id=D('SysUser')->self_id();
                $user_type=D('SysUser')->self_user_type();
                $proxy_code=trim(I('proxy_code')) ;
                $order_code=trim(I('order_code')) ;
                $mobile=trim(I('mobile')) ;
                $start_datetime=trim(I('start_datetime')) ;
                $end_datetime=trim(I('end_datetime')) ;
                $sale_name=trim(I('sale_name'));
                //判断时间是否在一个月内
                if($start_datetime!="" && $end_datetime!=""){
                    if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                        $this->display('index');
                        echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
                    }
                }
            $sale_id=-1;
            if($sale_name){
                $con['user_name']=$sale_name;
                $con['user_type']=$user_type;
                $con['status']=1;
                $user_sale_id=M('sys_user')->where($con)->field('user_id')->find();
                $sale_id=empty($user_sale_id)?0:intval($user_sale_id['user_id']);
            }

            if($start_datetime || $end_datetime){
                if($start_datetime ==""){
                    $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
                }
                if($end_datetime ==""){
                    $end_datetime = end_time(msubstr($start_datetime,0,10,"utf-8",false));
                }
            }else {
               /* $end_datetime=date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $e_time= strtotime($end_datetime)-2592000;
                $start_datetime=start_time(date('Y-m-d',$e_time));
                $d_sdata=$start_datetime;
                $d_edata=$end_datetime;*/
                $start_datetime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
                $end_datetime = date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('d'), date('Y')));
            }
            $proxy_code = !empty($proxy_code) ? $proxy_code : '' ;
            $order_code = !empty($order_code) ? $order_code : '' ;
            $proxy_name = !empty($proxy_name) ? $proxy_name : '' ;
            $mobile = !empty($mobile) ? $mobile : '' ;
            $operator_id = !empty($operator_id) ? $operator_id : -1 ;  //运营商
            $channel_id = !empty($channel_id) ? $channel_id : '' ; //通道
            $bc_channel_id = !empty($bc_channel_id) ? $bc_channel_id : '' ; //备用通道
            $province_id = !empty($province_id) ? $province_id : -1 ;  //省份
            //$order_status = !empty($order_status) ? '' : '' ;  //订单状态
            $product_name = !empty($product_name) ? $product_name : '' ;  //产品名称
            $order_status='2,5';//订单状态
            $get_page = I("p")==""?1:I("p");     //获取当前分页数
            $list=D('Order')->order_storing_process(2,$user_id,$proxy_name,$proxy_code,$order_code,$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$get_page,$sale_id);
            //var_dump($list['list']);
            //$discount_price_sum = D('Order')->discount_price_sum($where);
            //$this->assign('discount_price_sum',$discount_price_sum);
            //加载模板
            $Page       = new Page($list['count']['@p_total_count'],20);
            $show       = $Page->show();

            $this->assign('list',get_sort_no($list['list']));  //数据列表
            $this->assign('url','Orders/completed_excel');
            $this->assign('page',$show);
            $this->assign('orderStatus',2);//分类
            $this->assign('record','completed');
            $this->assign("counts",$list['count']);//统计
            $this->assign('d_sdata',date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y'))));  //默认开始时间
            $this->assign('d_edata',date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('d'), date('Y'))));  //默认结束时间
            $this->display('index');        //模板
        }

        /*
           *已取消订单
         */
        public function canceled(){
            set_time_limit(0);
            D("SysUser")->sessionwriteclose();
            $user_type=D('SysUser')->self_user_type();
            $user_id=D('SysUser')->self_id();
            $start_datetime=trim(I('start_datetime')) ;
            $end_datetime=trim(I('end_datetime')) ;
            $sale_name=trim(I('sale_name'));
            $sale_id=-1;
            if($sale_name){
                $con['user_name']=$sale_name;
                $con['user_type']=$user_type;
                $con['status']=1;
                $user_sale_id=M('sys_user')->where($con)->field('user_id')->find();
                $sale_id=empty($user_sale_id)?0:intval($user_sale_id['user_id']);
            }
            //判断时间是否在一个月内
            if($start_datetime!="" && $end_datetime!=""){
                if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                    $this->display('index');
                    echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
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
               /* $end_datetime=date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $e_time= strtotime($end_datetime)-2592000;
                $start_datetime=start_time(date('Y-m-d',$e_time));
                $d_sdata=$start_datetime;
                $d_edata=$end_datetime;*/
                $start_datetime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
                $end_datetime = date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('d'), date('Y')));
            }
            $proxy_code=trim(I('proxy_code')) ;
            $order_code=trim(I('order_code')) ;
            $mobile=trim(I('mobile')) ;
            $proxy_code = !empty($proxy_code) ? $proxy_code : '' ;
            $order_code = !empty($order_code) ? $order_code : '' ;
            $proxy_name = !empty($proxy_name) ? $proxy_name : '' ;
            $mobile = !empty($mobile) ? $mobile : '' ;
            $operator_id = !empty($operator_id) ? $operator_id : -1 ;  //运营商
            $channel_id = !empty($channel_id) ? $channel_id : '' ; //通道
            $bc_channel_id = !empty($bc_channel_id) ? $bc_channel_id : '' ; //备用通道
            $province_id = !empty($province_id) ? $province_id : -1 ;  //省份
            //$order_status = !empty($order_status) ? '' : '' ;  //订单状态
            $product_name = !empty($product_name) ? $product_name : '' ;  //产品名称

            $order_status='6';//订单状态
            $get_page = I("p")==""?1:I("p");     //获取当前分页数
            $list=D('Order')->order_storing_process(2,$user_id,$proxy_name,$proxy_code,$order_code,$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$get_page,$sale_id);
            //var_dump($list['list']);
            $this->assign('url','Orders/canceled_excel');
            //$this->assign('list',$list['list']);  //数据列表

            $Page       = new Page($list['count']['@p_total_count'],20);
            $show       = $Page->show();

            $this->assign('list',get_sort_no($list['list']));  //数据列表
            $this->assign('page',$show);            //分类
            $this->assign("counts",$list['count']);//统计
            $this->assign('orderStatus',3);
            $this->assign('record','canceled');
            $this->assign('d_sdata',date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y'))));  //默认开始时间
            $this->assign('d_edata',date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('d'), date('Y'))));  //默认结束时间
            $this->display('index');        //模板
        }


        public function detailed(){
                $where['o.order_id']=trim(I('order_id'));
                $list=D('Order')->detailed($where);
                $this->assign($list);
                $this->display('detailed');
        }
    /*未支付订单导出*/
    public function unpaid_excel(){
        set_time_limit(0);
        $where=array();
        $proxy_code=trim(I('proxy_code')) ;
        $enterprise_code=trim(I('enterprise_code')) ;
        $order_code=trim(I('order_code')) ;
        $operator_name=trim(I('operator_name')) ;
        $channel_name=trim(I('channel_name')) ;
        $province_name=trim(I('province_name')) ;
        $product_name=trim(I('product_name')) ;
        $mobile=trim(I('mobile')) ;
        $start_datetime=trim(I('start_datetime')) ;
        $end_datetime=trim(I('end_datetime')) ;
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                $this->display('index');
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }
        if($start_datetime || $end_datetime){
            if($start_datetime && $end_datetime){
                $where['o.order_date'] = array('between',array($start_datetime,$end_datetime));
            }
            if($start_datetime ==""){
                $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
                $where['o.order_date'] = array('between',array($start_datetime,$end_datetime));
            }
            if($end_datetime ==""){
                $end_datetime = end_time(msubstr($start_datetime,0,10,"utf-8",false));
                $where['o.order_date'] = array('between',array($start_datetime,$end_datetime));
            }
        }else {
        /*    $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            $end_datetime= strtotime($start_datetime)-2592000;
            $e_time=start_time(date('Y-m-d',$end_datetime));*/
            $start_datetime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
            $end_datetime = date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('d'), date('Y')));
            $where['o.order_date']= array('between',array($start_datetime,$end_datetime));
        }
        $enterprise_ids = D('Enterprise')->enterprise_ids();
        if($proxy_code){
            $where['p.proxy_code']=$proxy_code;
        }
        if($enterprise_code ){
            $where['e.enterprise_code']=$enterprise_code;
        }
        if($order_code){
            $where['o.order_code']=$order_code;
        }
        if($operator_name){
            $where['op.operator_name']=array('like','%'.$operator_name.'%');
        }
        if($channel_name){
            $where['c.channel_name']=array('like','%'.$channel_name.'%');
        }
        if($province_name){
            $where['pr.province_name']=array('like','%'.$province_name.'%');
        }
        if($product_name){
            $where['pp.product_name']=array('like','%'.$product_name.'%');
        }
        if($mobile){
            $where['o.mobile']=$mobile;
        }
        $where['o.is_payment']=0;//是否付款，0未付款，1已付款
        $self_proxy_id=D('SysUser')->self_proxy_id();//当前代理商
        if($self_proxy_id!==""){
            $proxy_child_ids_array = $self_proxy_id.",".D('Proxy')->proxy_child_s();
            if($proxy_child_ids_array!='' && $enterprise_ids!='' ){
                $map['o.proxy_id'] = array('in',$proxy_child_ids_array);
                $map['o.enterprise_id'] = array('in',$enterprise_ids);
                $map['_logic'] = 'or';
                $where['_complex'] = $map;
            }else{
                $where['o.proxy_id'] = array('in',$proxy_child_ids_array);
            }
        }else{
            if($enterprise_ids!==""){
                $map['o.enterprise_id'] = array('in',$enterprise_ids);
                $map['o.proxy_id'] = array('in',$self_proxy_id);
                $map['_logic'] = 'or';
                $where['_complex'] = $map;
            }else{
                $where['o.proxy_id'] =$self_proxy_id;
            }
        }
        $order_list=D('Order')->export_excel($where,1);
        $headArr=array("订单编号","代理商/企业编号","代理商/企业名称","来源","手机号","价格(元)","折后价格(元)","支付方式","操作时间");
        ExportEexcel('未支付订单',$headArr,$order_list);


    }

    //完成订单导出
    public function completed_excel(){
        set_time_limit(0);
        $user_id=D('SysUser')->self_id();
        $start_datetime=trim(I('start_datetime')) ;
        $end_datetime=trim(I('end_datetime')) ;
        $sale_name=trim(I('sale_name'));
        $user_type=D('SysUser')->self_user_type();
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                $this->display('index');
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }
        $sale_id=-1;
        if($sale_name){
            $con['user_name']=$sale_name;
            $con['user_type']=$user_type;
            $con['status']=1;
            $user_sale_id=M('sys_user')->where($con)->field('user_id')->find();
            $sale_id=empty($user_sale_id)?0:intval($user_sale_id['user_id']);
        }
        if($start_datetime || $end_datetime){
            if($start_datetime ==""){
                $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
            }
            if($end_datetime ==""){
                $end_datetime = end_time(msubstr($start_datetime,0,10,"utf-8",false));
            }
        }else {
           /* $end_datetime=date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            $e_time= strtotime($end_datetime)-2592000;
            $start_datetime=start_time(date('Y-m-d',$e_time));*/
            $start_datetime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
            $end_datetime = date("Y-m-d H:i:s", mktime(23, 59, 59, date('m'), date('d'), date('Y')));
        }
        $proxy_code=trim(I('proxy_code')) ;
        $order_code=trim(I('order_code')) ;
        $mobile=trim(I('mobile')) ;
        $proxy_code = !empty($proxy_code) ? $proxy_code : '' ;
        $order_code = !empty($order_code) ? $order_code : '' ;
        $proxy_name = !empty($proxy_name) ? $proxy_name : '' ;
        $mobile = !empty($mobile) ? $mobile : '' ;
        $operator_id = !empty($operator_id) ? $operator_id : -1 ;  //运营商
        $channel_id = !empty($channel_id) ? $channel_id : '' ; //通道
        $bc_channel_id = !empty($bc_channel_id) ? $bc_channel_id : '' ; //备用通道
        $province_id = !empty($province_id) ? $province_id : -1 ;  //省份
        //$order_status = !empty($order_status) ? '' : '' ;  //订单状态
        $product_name = !empty($product_name) ? $product_name : '' ;  //产品名称
        $order_status='2,5';//订单状态
        $order_page=2; //1 未支付订单 、已取消订单  2 已完成订单
        $get_page = 3000;     //获取当前分页数
        $order_list=D('Order')->order_excel_storing_process(2,$user_id,$proxy_name,$proxy_code,$order_code,$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$get_page,$order_page,$sale_id);
        $headArr=array("订单编号","代理商/企业编号","代理商/企业名称","来源","手机号","价格(元)","折后价格(元)","支付方式","操作时间","完成时间");
        ExportEexcel('已完成订单',$headArr,$order_list);


    }
/*取消订单导出*/
    public function canceled_excel(){
        set_time_limit(0);
        $user_id=D('SysUser')->self_id();
        $start_datetime=trim(I('start_datetime')) ;
        $end_datetime=trim(I('end_datetime')) ;
        $user_sale_id=M('sys_user')->where($con)->field('user_id')->find();
        $sale_id=empty($user_sale_id)?0:intval($user_sale_id['user_id']);
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                $this->display('index');
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
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
        $sale_id=-1;
        if($sale_name){
            $con['user_name']=$sale_name;
            $con['user_type']=$user_type;
            $con['status']=1;
            $user_sale_id=M('sys_user')->where($con)->field('user_id')->find();
            $sale_id=empty($user_sale_id)?0:intval($user_sale_id['user_id']);
        }
        $proxy_code=trim(I('proxy_code')) ;
        $order_code=trim(I('order_code')) ;
        $mobile=trim(I('mobile')) ;
        $proxy_code = !empty($proxy_code) ? $proxy_code : '' ;
        $order_code = !empty($order_code) ? $order_code : '' ;
        $proxy_name = !empty($proxy_name) ? $proxy_name : '' ;
        $mobile = !empty($mobile) ? $mobile : '' ;
        $operator_id = !empty($operator_id) ? $operator_id : -1 ;  //运营商
        $channel_id = !empty($channel_id) ? $channel_id : '' ; //通道
        $bc_channel_id = !empty($bc_channel_id) ? $bc_channel_id : '' ; //备用通道
        $province_id = !empty($province_id) ? $province_id : -1 ;  //省份
        //$order_status = !empty($order_status) ? '' : '' ;  //订单状态
        $product_name = !empty($product_name) ? $product_name : '' ;  //产品名称
        $order_status='6';//订单状态
        $order_page=1; //1 未支付订单 、已取消订单  2 已完成订单
        $get_page = 3000;     //获取当前分页数
        $order_list=D('Order')->order_excel_storing_process(2,$user_id,$proxy_name,$proxy_code,$order_code,$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$get_page,$order_page,$sale_id);
            $headArr=array("订单编号","代理商/企业编号","代理商/企业名称","来源","手机号","价格(元)","折后价格(元)","支付方式","操作时间");
            ExportEexcel('已取消订单',$headArr,$order_list);


   }
}
?>