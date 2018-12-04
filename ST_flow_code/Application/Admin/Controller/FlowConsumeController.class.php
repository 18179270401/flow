<?php
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;

class FlowConsumeController extends CommonController{

	/*
	 * 流量消费统计表
	 */
	public function index() {
        D("SysUser")->sessionwriteclose();
        $sel_proxy_id = D('SysUser')->self_proxy_id();
        //$sel_proxy_id = 2;
        $sel_proxy_info = D('Proxy')->proxyinfo($sel_proxy_id);
        $start_datetime = I('start_datetime') ? I('start_datetime') : date('Y-m-d H:i:s', mktime(0, 0, 0, date('n'), 1, date('Y')));
        $end_datetime = I('end_datetime') ? I('end_datetime') : date('Y-m-d H:i:s', mktime(23, 59, 59, date('n'), date('j'), date('Y')));
        $enterprise_code= trim(I('enterprise_code'));
        $enterprise_name= trim(I('enterprise_name'));

        if($start_datetime > $end_datetime) {
            $this->display('index');
            echo "<script>alertbox({'status':'error','msg':'开始时间必须早于结束时间！'});history.back();</script>";exit;
        }
        if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
            $this->display('index');
            echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back();</script>";exit;
        }

        if(is_numeric($sel_proxy_id) && $sel_proxy_id > 0 && 1 == $sel_proxy_info['proxy_type']) {
            $map['o.user_type'] = array('eq', 2);
            $map_enterprise['e.top_proxy_id'] = array('eq', $sel_proxy_id);
            $map['o.order_status'] = array('in', array(2, 5));
            $map['o.complete_time'] = array('between', array($start_datetime, $end_datetime));
            ($enterprise_code != '') && $map_enterprise['e.enterprise_code'] = array('like', "%{$enterprise_code}%");
            ($enterprise_name != '') && $map_enterprise['e.enterprise_name'] = array('like', "%{$enterprise_name}%");

            $map_enterprise['e.status'] = 1;
            $map_enterprise['e.approve_status'] = 1;
            $enterprise_list = M('enterprise as e')
                ->where($map_enterprise)
                ->field('enterprise_id,enterprise_code,enterprise_name')
                ->order('enterprise_id desc')
                ->select();

            $join = array(
                "LEFT JOIN t_flow_order_refund r ON r.`order_id` = o.`order_id` AND r.status = 4"
            );
            $count = count($enterprise_list);

            $Page       = new Page($count,20);
            $show       = $Page->show();
            $this->assign('page', $show); //分页

            $enterprise_ids = array();
            foreach($enterprise_list as $el){
                $enterprise_ids[] = $el['enterprise_id'];
            }
            $enterprise_ids = array_slice($enterprise_ids,$Page->firstRow,$Page->listRows);
            if(empty($enterprise_ids)){
                $this->display();
            }else{
                $map['o.enterprise_id'] = array('in', $enterprise_ids);
            }

            $list1 = M('order o')
                ->field("o.`enterprise_id`,SUM(o.discount_price) AS allodp")
                ->where($map)
                ->order("allodp desc")
                ->group("o.`enterprise_id`")
                ->order("allodp desc")->select();

            $map['r.status'] = 4;
            $list2 = M('order o')
                ->join($join)
                ->field("o.`enterprise_id`,SUM(r.discount_price) AS allrdp")
                ->where($map)
                ->group("o.`enterprise_id`")->select();
            $result_lists = array();
            if(!empty($enterprise_ids) && is_array($enterprise_ids)) {
                foreach($enterprise_ids as $k => $v) {
                    $result_list['enterprise_id'] = $v;
                    $result_list['enterprise_code'] = '';
                    $result_list['enterprise_name'] = '';
                    $result_list['allrdp'] = 0;
                    $result_list['allodp'] = 0;
                    foreach($enterprise_list as $el){
                        if($el['enterprise_id'] == $v){
                            $result_list['enterprise_code'] = $el['enterprise_code'];
                            $result_list['enterprise_name'] = $el['enterprise_name'];
                            break;
                        }
                    }
                    foreach($list1 as $v1){
                        if($v1['enterprise_id'] == $v){
                            $result_list['allodp'] = $v1['allodp'];
                            break;
                        }
                    }

                    foreach($list2 as $v2){
                        if($v2['enterprise_id'] == $v){
                            $result_list['allrdp'] = $v2['allrdp'];
                            break;
                        }
                    }

                    $allodp1 = empty($result_list['allodp']) ? '0.00' : $result_list['allodp'];
                    $allrdp1 = empty($result_list['allrdp']) ? '0.00' : $result_list['allrdp'];
                    $result_list['alldiff'] = $allodp1 - $allrdp1;
                    $result_lists[] = $result_list;
                }
            }
            $this->assign('list', get_sort_no($result_lists,$Page->firstRow));
        } else {
            $this->assign('page', ''); //分页
            $this->assign('list', array());
        }

        $this->assign('default_start', $start_datetime);
        $this->assign('default_end', $end_datetime);

        $this->display();
	}

    /**
     * 导出excel
     */
    public function export_excel() {
        $sel_proxy_id = D('SysUser')->self_proxy_id();
        //$sel_proxy_info = D('Proxy')->proxyinfo($sel_proxy_id);
        $start_datetime = I('start_datetime') ? I('start_datetime') : date('Y-m-d H:i:s', mktime(0, 0, 0, date('n'), 1, date('Y')));
        $end_datetime = I('end_datetime') ? I('end_datetime') : date('Y-m-d H:i:s', mktime(23, 59, 59, date('n'), date('j'), date('Y')));
        $enterprise_code= trim(I('enterprise_code'));
        $enterprise_name= trim(I('enterprise_name'));

        if($start_datetime > $end_datetime) {
            $this->display('index');
            echo "<script>alertbox({'status':'error','msg':'开始时间必须早于结束时间！'});history.back();</script>";exit;
        }
        if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
            $this->display('index');
            echo "<script>alertbox({'status':'error','msg':'导出时间间隔请勿超过93天！'});history.back();</script>";exit;
        }

        $map['o.user_type'] = array('eq', 2);
        $map['e.top_proxy_id'] = array('eq', $sel_proxy_id);
        $map['o.order_status'] = array('in', array(2, 5));
        $map['o.complete_time'] = array('between', array($start_datetime, $end_datetime));
        ($enterprise_code != '') && $map['e.enterprise_code'] = array('like', "%{$enterprise_code}%");
        ($enterprise_name != '') && $map['e.enterprise_name'] = array('like', "%{$enterprise_name}%");

        $list = M('order o')
            ->join("INNER JOIN ".C('DB_PREFIX')."enterprise e ON o.enterprise_id = e.enterprise_id ")
            ->join("LEFT JOIN ".C('DB_PREFIX')."order_refund r ON r.`order_id` = o.`order_id` AND r.status = 4 ")
            ->field("e.`enterprise_code`,e.enterprise_name,SUM(o.discount_price) AS allodp,SUM(r.discount_price) AS allrdp")
            ->where($map)
            ->limit(3000)
            ->order("allodp desc")
            ->group("o.`enterprise_id`")->select();
        //write_debug_log(array(__METHOD__.'：'.__LINE__, 'sql== '.M()->getLastSql()));
        if(!empty($list) && is_array($list)) {
            foreach($list as $k => &$v) {
                $allodp1 = empty($v['allodp']) ? '0.00' : $v['allodp'];
                $allrdp1 = empty($v['allrdp']) ? '0.00' : $v['allrdp'];
                $v['allodp'] = $allodp1;
                $v['allrdp'] = $allrdp1;
                $v['alldiff'] = $allodp1 - $allrdp1;
            }
        }

        $title='流量消费统计';
        $headArr=array("企业编号","企业名称","折后价(元)","退款金额(元)","企业消费(元)");
        ExportEexcel($title,$headArr,$list);
    }


}
?>