<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class PayRedRecordController extends CommonController{
    public function _initialize() {
        Vendor("WxPayR.Api");
        Vendor("WxPayR.JsApiPay");
        Vendor("WxPayR.WxPayConfig");
        Vendor('WxPayR.WxPayData');
        Vendor('WxPayR.Exception');
        Vendor('AlipayApp.Notify');
        Vendor('AlipayApp.Corefunction');
        Vendor('AlipayApp.Rsafunction');
        Vendor('AlipayApp.Submit');
        Vendor('WxPayR.AppApi');
        /*Vendor('WxPayApp.WxAppPayConfig');
        Vendor('WxPayApp.Exception');
        Vendor('WxPayApp.Notify');
        Vendor('WxPayApp.NativePay');
        Vendor('WxPayApp.Native_notify');
        Vendor('WxPayApp.JsApiPay');*/
    }
	/*
	 *  红包购买记录
	 */
	public function  index(){
        D("SysUser")->sessionwriteclose();
        $use_t=D("SysUser")->self_user_type();
        $user_type = D('SysUser')->self_user_type()-1;
        $user_id = D('SysUser')->self_id();
        $self_proxy_id = D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        $red_order_code = trim(I('red_order_code'));
        $user_name=trim(I('user_name'));
        $start_datetime = trim(I('start_datetime'));
        $end_datetime = trim(I('end_datetime')) ;
        $payment_type=trim(I('payment_type'));
        $where = array();
        if(!empty($red_order_code)){
            $where["ro.red_order_code"]=array("like","%".$red_order_code."%");
        }
        if($use_t!=3){
            if(!empty($user_name)) {
                $whe1['p.proxy_name'] = array("like","%".$user_name."%");
                $whe1['e.enterprise_name'] = array("like","%".$user_name."%");
                $whe1["_logic"] = "or";
                $where[] = $whe1;
            }
        }
        if(!empty($payment_type) && $payment_type!=9){
            if($payment_type==2){
                $where['ro.payment_type'] =array(2,array("exp","is null"),"or");
            }else{
                $where['ro.payment_type']=$payment_type;
            }
        }
        if($use_t==3) {
            $where['ro.user_type'] = $user_type;
            if ($user_type == '1') {
                //代理商
                $where['ro.proxy_id'] = $self_proxy_id;
            } else if ($user_type == '2') {
                //企业
                $where['ro.enterprise_id'] = $self_enterprise_id;
            }
        }
        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['ro.pay_date'] = array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['ro.pay_date'] = array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['ro.pay_date'] = array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            //$end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $end_datetime= strtotime($start_datetime)-2592000;
            $e_time=start_time(date('Y-m-d',$end_datetime));
            $where['ro.pay_date'] = array('between',array($e_time,$start_datetime));
        }
        $list=D('SceneInfo')->get_pay_red_list($where);
        $list['list']=D('SceneInfo')->get_refund_type($list['list']);
        $this->assign("use",$use_t);
        $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
        $end_datetime= strtotime($start_datetime)-2592000;
        $e_time=start_time(date('Y-m-d',$end_datetime));
        $this->assign('default_end',$start_datetime);
        $this->assign('default_start',$e_time);
        //加载模板
        $this->assign('list',$list['list']);  //数据列表
        $this->assign('page',$list['page']);  //分页  
        $this->display();        //模板
    }
    

    public function show(){
        $id = trim(I('get.red_order_id'));
        $info=M("red_order as ro")
            ->join('left join t_flow_proxy as p on p.proxy_id = ro.proxy_id and ro.user_type=1')
            ->join('left join t_flow_enterprise as e on e.enterprise_id= ro.enterprise_id and ro.user_type=2')
            ->where(array("red_order_id"=>$id))
            ->field("ro.red_order_code,ro.wx_openid,ro.pay_price,ro.pay_date,p.proxy_name,e.enterprise_name,ro.user_type")
            ->find();
        $list = D('SceneInfo')->get_pay_red_record_detail($id,1);
        $use_t=D('SysUser')->self_user_type();
        $this->assign("usr",$use_t);
        $this->assign('list',$list);
        $this->assign('info',$info);
        $this->display();
     }

    public function pay_refund(){
        $msg="系统错误";
        $status="error";
        $red_order_id=I("red_order_id");
        //表示
        $data=D('SceneInfo')->get_pay_red_record_detail($red_order_id,2);
        //表示微信网页
        if($data['pay_type']==1) {
            $html=$this->app_Alipay($data);
            $this->assign("html",$html);
            $this->display("refund");
            exit();
        }elseif($data['pay_type']==3){
            $type=$this->app_wx($data);
        }else{
            $type=$this->wx_refund($data); //$type 1.未上传pem,2.退款成功，3.退款失败
        }
        if($type==1){
            $msg="退款失败,请先上传pem文件！";
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】,退款失败';
            $this->sys_log('退款',$note);
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }elseif($type==2){
            $msg="退款成功！";
            $status="success";
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】,退款成功';
            $this->sys_log('红包退款',$note);
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }else{
            $msg="退款失败！";
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】,退款失败';
            $this->sys_log('红包退款',$note);
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }
    }

    //h5页面退款的功能
    private function wx_refund($data)
    {
        $transaction_id = $data["number"];
        $total_fee = $data["pay_price"] * 100;
        $refund_fee = $data["refund_fee"] * 100;
        if ($data['user_type'] == 1) {
            $user_id = $data['proxy_id'];
        } else {
            $user_id = $data['enterprise_id'];
        }
        $config = $this->wx_getconfig($data['user_type'], $user_id);
        if (empty($config['SSLCERT_PATH']) || empty($config['SSLKEY_PATH'])) {
            return 1;
        }
        $input = new \WxPayRefund;
        $input->SetTransaction_id($transaction_id);
        $input->SetTotal_fee($total_fee);
        $input->SetRefund_fee($refund_fee);
        $input->SetOut_refund_no($config['MCHID'] . date("YmdHis"));
        $input->SetOp_user_id($config['MCHID']);
        $return = \WxPayApi::refund($input, $config);
        if ($return["result_code"] == "SUCCESS" && $return["return_code"] == "SUCCESS") {
            $order['refund_status'] = 2;//退款成功
            $where['red_order_code']=$data['red_order_code'];
            if($data['products']!=","){
                $reds=M('red_order')->where($where)->field("out_packages")->find();
                $pack1=$reds['out_packages'].$data['products'];
                if(empty($reds['out_packages'])){
                    $pack1=substr($pack1,1,strlen($pack1)-2);
                }else{
                    $pack1=substr($pack1,0,strlen($pack1)-1);
                }
                $pack['out_packages']=$pack1;
                M('red_order')->where($where)->save($pack);
            }
            if($data['records']!=","){
                $records=explode(',',$data['records']);
                foreach ($records as $id){
                    $where['red_record_id']=$id;
                    $map['refund_status']=2;
                    M('red_record')->where($where)->save($map);
                }
            }
            return 2;
        } else {
            return 3;
        }
    }
    //h5页面 获取信息config
    private function  wx_getconfig($user_type,$user_id){
        if($user_type==1){
            $map['proxy_id']=$user_id;
        }else{
            $map['enterprise_id']=$user_id;
        }
        $result=M("user_set")->where($map)->find();

        //payment_type  int  1表示运营方收款    2表示企业收款
        $payment_type = $result['payment_type'];
        if($payment_type == 1)
        {
            $paymentmap['enterprise_id'] = 90;
            //运营方尚通科技收款
            $result=M("user_set")->where($paymentmap)->find();
        }
        //默认还是企业端收款
        $config=array();
        $config['APPID']=$result['wx_appid'];
        $config['APPSECRET']=$result['wx_appsecret'];
        $config['MCHID']=$result['wx_mchid'];
        $config['KEY']=$result['wx_key'];
        $config['SSLCERT_PATH']=$result['wx_pem_file_one'];
        $config['SSLKEY_PATH']=$result['wx_pem_file_two'];
        return $config;
    }

    //微信app 退款
    private function app_wx($data)
    {
        $transaction_id = $data['number'];
        if ($data['user_type'] == 1) {
            $map['proxy_id'] = $data['proxy_id'];
        } else {
            $map['enterprise_id'] = $data['enterprise_id'];
        }
        $result = M("user_set")->where($map)->find();
        $config['APPID'] = $result['app_appid'];
        $config['MCHID'] = $result['app_mchid'];
        $config['KEY'] = $result['app_key'];
        $config['SSLCERT_PATH'] = $result['app_pem_file_one'];
        $config['SSLKEY_PATH'] = $result['app_pem_file_two'];
        if (empty($config['SSLCERT_PATH']) || empty($config['SSLKEY_PATH'])) {
            return 1;
        }
        $total_fee = $data["pay_price"] * 100;
        $refund_fee = $data["refund_fee"] * 100;
        $input = new \WxPayRefund;
        //$input->SetOut_trade_no($out_trade_no);
        $input->SetTransaction_id($transaction_id);//订单号

        $input->SetTotal_fee($total_fee);//总金额
        $input->SetRefund_fee($refund_fee);//退款金额
        $input->SetOut_refund_no($config['MCHID'] . date("YmdHis"));//退款订单号
        $input->SetOp_user_id($config['MCHID']);//商户id
        $input->SetAppid($config['APPID']);//公众账号ID
        $input->SetMch_id($config['MCHID']);//商户号
        //$input->SetSign($config['KEY']);//签名
        $xml = $input->ToXml();
        $return = \WxPayAppApi::LExrefund($input, $config);
        if ($return["result_code"] == "SUCCESS" && $return["return_code"] == "SUCCESS") {
            if ($return["result_code"] == "SUCCESS" && $return["return_code"] == "SUCCESS") {
                $order['refund_status'] = 2;//退款成功
                $where['red_order_code'] = $data['red_order_code'];
                if ($data['products'] != ",") {
                    $reds = M('red_order')->where($where)->field("out_packages")->find();
                    $pack1 = $reds['out_packages'] . $data['products'];
                    if (empty($reds['out_packages'])) {
                        $pack1 = substr($pack1, 1, strlen($pack1) - 2);
                    } else {
                        $pack1 = substr($pack1, 0, strlen($pack1) - 1);
                    }
                    $pack['out_packages'] = $pack1;
                    M('red_order')->where($where)->save($pack);
                }
                if ($data['records'] != ",") {
                    $records = explode(',', $data['records']);
                    foreach ($records as $id) {
                        $where['red_record_id'] = $id;
                        $map['refund_status'] = 2;
                        M('red_record')->where($where)->save($map);
                    }
                }
                return 2;
            } else {
                return 3;
            }
        }
    }

    //支付宝退款
    private function app_Alipay($orders){
        $alipay_config =array();
        if ($orders['user_type'] == 1) {
            $map['proxy_id']= $orders['proxy_id'];
        } else {
            $map['enterprise_id'] = $orders['enterprise_id'];
        }
        $result=M("user_set")->where($map)->find();
        $alipay_config['partner']= $result['alipay_key'];   //这里是你在成功申请支付宝接口后获取到的PID；
        $alipay_config['key']=$result['paykey'];//这里是你在成功申请支付宝接口后获取到的Key
        $alipay_config['sign_type'] = strtoupper('RSA');
        $alipay_config['private_key_path'] = $result['alipay_pem_file_two'];
        $alipay_config['public_key_path'] = $result['alipay_pem_file'];
        $alipay_config['input_charset'] = strtolower('utf-8');
        $alipay_config['cacert'] = './Public/key/cacert.pem';
        $alipay_config['transport'] = 'http';
        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $msg = $orders['number'] . '^' . $orders['refund_fee'] . '^充值失败，退款！';
        $batch_no=date('Ymd') . time() . randomY(8);
        $parameter = array(
            "service" => "refund_fastpay_by_platform_pwd",
            "partner" => $alipay_config['partner'],
            "notify_url" => gethostwithhttp()."/index.php/Pay/Api/Refund_alipay",
            //"notify_url" => "http://test.liuliang.net.cn/index.php/Pay/Api/Refund_alipay",
            "seller_email" => $result['alipay_partner'],
            "refund_date" => date('Y-m-d H:i:s'),
            "batch_no" => $batch_no,
            "batch_num" => 1,
            "detail_data" => $msg,
            "_input_charset" => strtolower('utf-8')
        );
        $data['batch_no']=$batch_no;
        M("red_order")->where(array("number"=>$orders['number']))->save($data);//存入退款订单号
        $html_text = $alipaySubmit->buildRequestForm($parameter, "post", "确认");
        return $html_text;
    }

    //导出
    public function  export_excel(){
        $use_t=D("SysUser")->self_user_type();
        $user_type = D('SysUser')->self_user_type()-1;
        $user_id = D('SysUser')->self_id();
        $self_proxy_id = D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        $red_order_code = trim(I('red_order_code'));
        $user_name=trim(I('user_name'));
        $start_datetime = trim(I('start_datetime'));
        $end_datetime = trim(I('end_datetime')) ;
        $payment_type=trim(I('payment_type'));
        $where = array();
        if(!empty($red_order_code)){
            $where["ro.red_order_code"]=array("like","%".$red_order_code."%");
        }
        if($use_t!=3){
            if(!empty($user_name)) {
                $whe1['p.proxy_name'] = array("like","%".$user_name."%");
                $whe1['e.enterprise_name'] = array("like","%".$user_name."%");
                $whe1["_logic"] = "or";
                $where[] = $whe1;
            }
        }
        if(!empty($payment_type) && $payment_type!=9){
            if($payment_type==2){
                $where['ro.payment_type'] =array(2,array("exp","is null"),"or");
            }else{
                $where['ro.payment_type']=$payment_type;
            }
        }
        if($use_t==3) {
            $where['ro.user_type'] = $user_type;
            if ($user_type == '1') {
                //代理商
                $where['ro.proxy_id'] = $self_proxy_id;
            } else if ($user_type == '2') {
                //企业
                $where['ro.enterprise_id'] = $self_enterprise_id;
            }
        }
        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['ro.pay_date'] = array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['ro.pay_date'] = array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['ro.pay_date'] = array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            $end_datetime= strtotime($start_datetime)-2592000;
            $e_time=start_time(date('Y-m-d',$end_datetime));
            $where['ro.pay_date'] = array('between',array($e_time,$start_datetime));
        }
        $list=D('SceneInfo')->get_pay_red_excel($where);
        $datas = array();
        $headArr=array();
        if($use_t !=3){
            $headArr =array_merge($headArr,array("用户类型","用户名称"));
        }
        $headArr= array_merge($headArr,array("购买金额(元)","收款方式","购买时间","支付订单号"));
        foreach ($list as $v) {
            $data=array();
            if($use_t !=3){
                $data['user_type'] = $v['user_type'] == 1?"代理商":"企业";
                $data['user_name'] = $v['user_type'] == 1?$v['proxy_name']:$v['enterprise_name'];
            }
            $data['pay_price'] = $v['pay_price'];
            if($v['payment_type']==1){
                $data['payment_type' ] ="运营方收款";
            }elseif($v['payment_type']==3){
                $data['payment_type' ]="代理商收款";
            }else{
                $data['payment_type' ]="企业收款";
            }
            $data['pay_date'] = $v['pay_date'];
            $data['red_order_code']=$v['red_order_code'];
            array_push($datas,$data);
        }
        $title='红包购买记录';

        ExportEexcel($title,$headArr,$datas);
    }
}
?>