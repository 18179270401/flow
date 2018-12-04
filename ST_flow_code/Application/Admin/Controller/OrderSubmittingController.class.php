<?php
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class OrderSubmittingController extends CommonController{
    /*
	 * 订单表
	 */
    public function index(){
        set_time_limit(0);
        D("SysUser")->sessionwriteclose();
        $proxy_name = trim(I('proxy_name'));
        $mobile = trim(I('mobile'));
        $order_status =I('order_status');
        $start_datetime = I('start_datetime');
        $end_datetime = I('end_datetime');
        $operator_id   = I('operator_id');
        $channel_id   = I('channel_id');
        $bc_channel_id=I('bc_channel_id');
        $province_id = I('province_id');
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400){
                $this->display('index');
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过31天！'});history.back();</script>";exit;
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
            $start_datetime = date("Y-m-d 00:00:00", strtotime("-7 day"));
            $end_datetime = date("Y-m-d H:i:s", strtotime("-12 hour"));

        }

        //读取用户信息
        $user_type=D('SysUser')->self_user_type();
        $user_id=D('SysUser')->self_id(); //用户id
        //给定查询条件
        $proxy_name = !empty($proxy_name) ? $proxy_name : '' ;
        $mobile = !empty($mobile) ? $mobile : '' ;
        $operator_id = !empty($operator_id) ? $operator_id :-1 ;  //运营商
        $upper_role=D('SysRole')->upper_role();
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
        $order_status = $order_status != 9 ? $order_status : '0,1,3,4';  //订单状态

        $product_name='';

        $get_page = I("p")==""?1:I("p");     //获取当前分页数
        /*  $order_list = M()->query("CALL p_query_order(1,".$user_id.",'".$proxy_name."','".$mobile."','".$channel_id."','".$bc_channel_id."',".$operator_id.",".$province_id.",'".$order_status."','".$start_datetime."','".$end_datetime."','',".$get_page.",20,1,@p_total_count,@p_success_count,@p_success_amount,@p_faile_count,@p_faile_amount,@p_wait_count,@p_wait_amount,@submit_success_count,@submit_success_amount,@p_success_price,@p_faile_price,@p_wait_price,@p_submit_success_price);");

          $count = M()->query("SELECT @p_total_count,@p_success_count,@p_success_amount,@p_faile_count,@p_faile_amount,@p_wait_count,@p_wait_amount,@submit_success_count,@submit_success_amount,@p_success_price,@p_faile_price,@p_wait_price,@p_submit_success_price;");*/
        $order_list=D('Order')->order_storing_process(1,$user_id,$proxy_name,'','',$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$get_page,-1);
        $Page       = new Page($order_list['count']['@p_total_count'],20);
        $show       = $Page->show();
        $order_list['count']['@p_wait_count']=$order_list['count']['@p_wait_count']+$order_list['count']['@p_faile_count'];
        $order_list['count']['@p_wait_amount']=number_format($order_list['count']['@p_wait_amount']+$order_list['count']['@p_faile_amount'], 3);
        //加载模板
        $this->assign('order_list',get_sort_no($order_list['list'],$Page->firstRow));  //数据列表
        $this->assign('page',$show);            //分页          //分页
        $this->assign('upper_role',$upper_role_info);            //为上游角色 ： 1 是   2 不是
        $this->assign("counts",$order_list['count']);

        //读取运营商
        $operator = D("Order")->operatorall();
        //读取省份
        $province = D("ChannelProduct")->province_list();
        //订单状态列表
        $status_list['items']['0'] = array('text' => "等待提交(主)", 'order_status' =>'0');
        $status_list['items']['1'] = array('text' => "提交成功(主)", 'order_status' =>'1');
        $status_list['items']['3'] = array('text' => "等待提交(备)", 'order_status' =>  '3');
        $status_list['items']['4'] = array('text' => "提交成功(备)", 'order_status' =>  '4');

        //读取通道信息
        $channel = D("Order")->channelall(1);
        //读取成功、失败、等待的条数和金额
        $this->assign('user_type',$user_type);
        $this->assign('operator',$operator);
        $this->assign('province',$province);
        $this->assign('channel',$channel);
        $this->assign('upper_role',$upper_role_info);            //为上游角色 ： 1 是   2 不是
        $this->assign('order_status_list',$status_list);
        $this->assign('default_end',$end_datetime);
        $this->assign('default_start',$start_datetime);

        //是否有高级查询权限
        $is_role = D('SysRole')->user_is_role('query_role');
        $is_role = $is_role? 1 : 0;
        $this->assign('is_role',$is_role);//返回页面做判断

        $this->display('index');
    }

    /**
     * @param $order_status
     * 读取成功、失败、等待的条数和金额
     */
    private function status_info($order_status,$map,$join){
        //初始值默认为0
        $data['success_data']=$data['success_money']=$data['fail_data']=$data['fail_money']=$data['wait_data']=$data['wait_money']=0;
        if($order_status!==''){
            $info=D('Order')->all_pre_data($map,$order_status,$join);
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
            $info1=D('Order')->all_pre_data($map,'2,5',$join);
            $info2=D('Order')->all_pre_data($map,'6,3',$join);
            $info3=D('Order')->all_pre_data($map,'0,1,4',$join);
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
        //$order_status = !empty($order_status) ? $order_status : '0,1,3,4';  //订单状态
        $order_status = $order_status != 9 ? $order_status : '0,1,3,4';  //订单状态
        $product_name='';
        //信息设置
        $file_url = $_SERVER["DOCUMENT_ROOT"]."/Public/ExcelFile/".date("Ymd")."/";  //文件地址
        if(!file_exists($file_url)) {
            @mkdir($file_url, 0777, true);
            @chmod($file_url, 0777);
        }
        $file_name = iconv("utf-8","gb2312","提交中充值记录").date("YmdHis",time()); //新建文件名
        $file_type = ".xls";      //文件后缀名
        $number_z = 5000;        //每次导出的总条数
        $number_c = 10;           //分批次数

        //将数据写入excel在下载
        $fp = @fopen($file_url . $file_name . $file_type, "ab"); //打开xls文件，如果不存在则创建

        for($oj=1;$oj<=$number_c;$oj++){
            //读取分批的数据
            $order_list=D('Order')->recharge_excel_storing_process(1,$user_id,$proxy_name,'','',$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$oj,$number_z,-1);
            /*$order_list = M()->query("CALL p_query_order(1,".$user_id.",'".$proxy_name."','".$mobile."','".$channel_id."','".$bc_channel_id."',".$operator_id.",".$province_id.",'".$order_status."','".$start_datetime."','".$end_datetime."','',".$oj.",".$number_z.",2,@p_total_count,@p_success_count,@p_success_amount,@p_faile_count,@p_faile_amount,@p_wait_count,@p_wait_amount,@submit_success_count,@submit_success_amount,@p_success_price,@p_faile_price,@p_wait_price,@p_submit_success_price);");*/
            if(count($order_list) <= 0) break;   //读取的数据为空时跳出循环
            if($oj==1){
                $xls_title = $this->order_list_title($user_type);
                fwrite($fp, $xls_title);                // 写入数据
                unset($xls_title);
            }

            $xls_content = $this->order_list_table($order_list,$user_type);
            if(!empty($xls_content)) {
                fwrite($fp, $xls_content);                // 写入数据
            }
            unset($order_list, $xls_content);             //注销数据变量
            if($oj==$number_c) break;               //到达数量时跳出循环
        }
        if($fp)fclose($fp);        //关闭文件

        //下载excel文件
        $filename = $file_url.$file_name.$file_type;
        $filesize = filesize($filename);
        header("content-type:application/octet-stream");
        header("content-disposition:attachment;filename=".$file_name.$file_type);
        header("content-length:{$filesize}");
        readfile($filename);
    }

    //处理导出标题
    private function order_list_title($user_type){

        if ($user_type == 1) {
            $upper_role=D('SysRole')->upper_role();
            if($upper_role['in']){
                $headArr = array("订单编号","手机号", "运营商", "归属地", "流量包名称", "折后价格(元)", "提交时间",  "订单状态");
            }else{
                $headArr = array("订单编号","用户名称", "顶级代理", "手机号", "运营商", "归属地", "流量包名称", "折后价格(元)", "主通道编码","备通道编码", "提交时间",  "订单状态");
            }

        } else {
            $headArr = array("用户名称", "手机号", "运营商", "归属地", "流量包名称", "折后价格(元)", "提交时间",  "订单状态");
        }
        $tle = "<table border='1'><tr>";
        foreach($headArr as $v){
            $tle .= "<td>".$v."</td>";
        }
        $tle .= "</tr></table>";
        return $tle;
    }

    //处理导出内容
    private function order_list_table($order_list,$user_type){
        $tle = "<table border='1'>";
        $upper_role=D('SysRole')->upper_role();
        if($user_type==1){
            foreach($order_list as $k=>$v){
                $time = explode(".",$v['order_date']);
                $channel_code = in_array($v['order_status'],array(4,5,6))?$v['bc_channel_code']:$v['channel_code'];
                $product_name = $v['province_id']==1?"全国":"省内";
                $order_status = $this->get_submitting_order_status($v['order_status']);
                $tle .= "<tr>";
                $tle .= "<td>D".$v['order_code']."</td>";
                $tle .= "<td>".$v['proxy_name']."</td>";
                $tle .= "<td>".$v['top_proxy_name']."</td>";
                $tle .= "<td> ".$v['mobile']."</td>";
                $tle .= "<td> ".$v['operator_name']."</td>";
                $tle .= "<td> ".$v['province_name'].$v['city_name']."</td>";
                $tle .= "<td> ".$product_name.$v['product_name']."</td>";
                $tle .= "<td> ".$v['discount_price']."</td>";
                $tle .= "<td> ".$channel_code."</td>";
                $tle .= "<td> ".$v['bc_channel_code']."</td>";
                $tle .= "<td> ".$time[0]."</td>";
                $tle .= "<td> ".$order_status."</td>";
                $tle .= "</tr>";
            }
        }

        $tle .= "</table>";
        return $tle;
    }

    public function show(){
        $where['o.order_id']=trim(I('order_id'));//充值
        $list=D('Order')->order_pre_detail($where);
        if(!empty($list)){
            if($list['proxy_level']==1){
                $list['top_proxy_id']=$list['proxy_id'];
            }
            $upper_role=D('SysRole')->upper_role();
            $upper_role_info=$upper_role['in']?'1':'2';
            $this->assign('upper_role',$upper_role_info);
            $this->assign($list);
            $this->display('detailed');
        }else{
            $this->ajaxReturn(array('msg'=>'对不起，没有找到该数据，请刷新重试！','status'=>'error'));
        }

    }

    /**
     * 批量回调推送
     */
    public function batch_callback(){
        if(IS_POST){
            $order_str=trim(I('order_str'));
            if(!$order_str){
                $this->ajaxReturn(array('msg'=>'请选择需处理的订单！','status'=>'error'));
            }
            $order_arr = explode(',',$order_str);
            foreach($order_arr as $order_id){
                $where['order_id']=$order_id;//获取回调订单id
                $order_info=M('order_pre')->field('user_type,order_code,proxy_name,proxy_id,enterprise_name,enterprise_id,order_status')->where('order_id='.$order_id)->find();

                //对订单进行失败处理和回调处理
                if($order_info['order_status'] == 0 || $order_info['order_status'] == 1 || $order_info['order_status'] == 3 || $order_info['order_status'] == 4){
                    M()->execute('CALL p_tran_noresponse_order('.$order_id.',@flag)');

                    $order_result = M()->query('SELECT @flag AS flag');

                    if($order_result['flag'] == 0){
                        //订单处理失败增加存储过程错误日志
                        write_error_log(array(__METHOD__.':'.__LINE__, '订单失败存储过程处理失败', 'callback'));
                    }
                }
            }
            $this->ajaxReturn(array('msg'=>'批量推送回调处理完成！','status'=>'success'));
        }
    }

    function  refund_info($id,$type){
        $info=array();
        if($type==1){
            $info=M('proxy')->where('proxy_id='.$id)->field('sale_id,proxy_name as name')->find();
        }else{
            $info=M('enterprise')->where('enterprise_id='.$id)->field('sale_id,enterprise_name as name')->find();
        }
        return $info;
    }

    /**
     *	获取当前
     */
    private function get_submitting_order_status($st_id){
        $status = array("0"=>"等待提交","1"=>"提交成功(主)","2"=>"充值成功","3"=>"等待提交(备)","4"=>"提交成功(备)","5"=>"充值成功(备)","6"=>"充值失败(备)");
        return $status[$st_id];
    }

}
?>