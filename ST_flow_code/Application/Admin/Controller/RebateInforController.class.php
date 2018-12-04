<?php

/**
 * RebateInforController.class.php
 * 代理商返利记录 控制器
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;

class RebateInforController extends CommonController {

    /**
     * 代理商返利记录 列表
     */
    public function index() {
		set_time_limit(0);
		D("SysUser")->sessionwriteclose();
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
		//判断时间是否在一个月内
		if($start_datetime!="" && $end_datetime!=""){
			if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
				$this->display('index');
				echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
			}
		}
		if($start_datetime || $end_datetime){
			if($start_datetime && $end_datetime){
				$start_datetime = start_time($start_datetime);
				$end_datetime = end_time($end_datetime);
			}
			if($start_datetime ==""){
				$start_datetime = start_time($start_datetime);
				$end_datetime = end_time($start_datetime);
			}
			if($end_datetime ==""){
				$start_datetime = start_time($end_datetime);
				$end_datetime = end_time($start_datetime);
			}
		}else {
			$end_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
			$s_datetime= strtotime($end_datetime)-2592000;
			$start_datetime=start_time(date('Y-m-d',$s_datetime));
			$d_sdata=$start_datetime;
			$d_edata=$end_datetime;
		}

		$user_type = D('SysUser')->self_user_type(); //1运营平台，2代理商，3企业

        $self_proxy_id = array('-1');
        $keyw = '';
        if(1 == $user_type) {
            $keypcode = I('keypcode', '', 'trim');
            $keypname = I('keypname', '', 'trim');

            $tpids1 = $tpids2 = array();
            if($keypcode) {
                $tpids1 = D('Proxy')->get_proxyid_by_proxycode($keypcode);
            }
            if($keypname) {
                $tpids2 = D('Proxy')->get_proxyid_by_proxyname($keypname);
            }
            $top_proxy_ids = array_merge($tpids1, $tpids2);
            if(!empty($top_proxy_ids)) {
                $self_proxy_id = array_unique($top_proxy_ids);
            }
        } else if(2 == $user_type) {
            $keyw = I("keyw", '', 'trim');
            $self_proxy_id = array(D('SysUser')->self_proxy_id());
        }

        //dump($self_proxy_id);echo '<hr />';dump($keyw);echo '<hr />';
    	$arr_record = D('AccountRecord')->rebateinfo($self_proxy_id, $keyw, $start_datetime, $end_datetime);
    	$this->assign('list', $arr_record['list']);
    	$this->assign('page', $arr_record['page']);
		$this->assign('operater_price_sum', $arr_record['operater_price_sum']);
		$this->assign('d_sdata',$d_sdata);  //默认开始时间
		$this->assign('d_edata',$d_edata);  //默认结束时间
		$this->assign('user_type',$user_type);  //用户类型
		$this->display();        //模板
    }

	/**
	 * 导出excel
	 */
	public function export_excel() {
		set_time_limit(0);
		$start_datetime = trim(I('get.start_datetime'));   //开始时间
		$end_datetime = trim(I('get.end_datetime'));   //结束时间
		//判断时间是否在一个月内
		if($start_datetime!="" && $end_datetime!=""){
			if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
				$this->display('index');
				echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
			}
		}
		if($start_datetime || $end_datetime){
			if($start_datetime && $end_datetime){
				$start_datetime = start_time($start_datetime);
				$end_datetime = end_time($end_datetime);
			}
			if($start_datetime ==""){
				$start_datetime = start_time($start_datetime);
				$end_datetime = end_time($start_datetime);
			}
			if($end_datetime ==""){
				$start_datetime = start_time($end_datetime);
				$end_datetime = end_time($start_datetime);
			}
		}else {
			$end_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
			$s_datetime= strtotime($end_datetime)-2592000;
			$start_datetime=start_time(date('Y-m-d',$s_datetime));
		}

		$user_type = D('SysUser')->self_user_type(); //1运营平台，2代理商，3企业

		$self_proxy_id = array('-1');
		$keyw = '';
		if(1 == $user_type) {
			$keypcode = I('keypcode', '', 'trim');
			$keypname = I('keypname', '', 'trim');

			$tpids1 = $tpids2 = array();
			if($keypcode) {
				$tpids1 = D('Proxy')->get_proxyid_by_proxycode($keypcode);
			}
			if($keypname) {
				$tpids2 = D('Proxy')->get_proxyid_by_proxyname($keypname);
			}
			$top_proxy_ids = array_merge($tpids1, $tpids2);
			if(!empty($top_proxy_ids)) {
				$self_proxy_id = array_unique($top_proxy_ids);
			}
		} else if(2 == $user_type) {
			$keyw = I("keyw", '', 'trim');
			$self_proxy_id = array(D('SysUser')->self_proxy_id());
		}

		$arr_record = D('AccountRecord')->rebate_excel($self_proxy_id, $keyw, $start_datetime, $end_datetime);
		//var_dump($arr_record);
		$title='代理商返利记录';
		$list=array();
		$headArr=array("产品运营商","流量大小","省份","下级用户","基础售价(元)","自身折扣","下级折扣","返利金额(元)","订单号","返利时间");
		foreach($arr_record as $k=>$v){
			$list[$k]['operator_id'] =get_operator_name($v['operator_id']);
			$list[$k]['size'] =$v['size'].'M';
			$list[$k]['province_name'] =$v['province_name'];
			if($user_type==1){
				$list[$k]['proxy_id_name'] =$v['proxy_id_name'];
			}
			$list[$k]['proxy_name'] =$v['proxy_name'].$v['enterprise_name'];
			$list[$k]['price'] =$v['price'];
			$list[$k]['self_dc'] =($v['self_dc']*10);
			$list[$k]['down_dc'] =($v['down_dc']*10);
			$list[$k]['operater_price'] =$v['operater_price'];
			$list[$k]['order_code'] =$v['order_code'];
			$list[$k]['record_date'] =$v['record_date'];
		}
		ExportEexcel($title,$headArr,$list);
	}
    
}