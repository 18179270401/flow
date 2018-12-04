<?php
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;

class ProxyFlowConsumeController extends CommonController{

    /**
     * 代理商流量消费统计表(所有一级代理商DISTINCT o.`one_proxy_id`)
     */
    public function index() {
        D("SysUser")->sessionwriteclose();
        $user_type = D('SysUser')->self_user_type();
        $start_datetime = I('start_datetime') ? I('start_datetime') : date('Y-m-d H:i:s', mktime(0, 0, 0, date('n'), date('d')-7, date('Y')));
        $end_datetime = I('end_datetime') ? I('end_datetime') : date('Y-m-d H:i:s', mktime(23, 59, 59, date('n'), date('j'), date('Y')));
        $proxy_code = trim(I('proxy_code'));
        $proxy_name = trim(I('proxy_name'));

        if($start_datetime > $end_datetime) {
            $this->display('index');
            echo "<script>alertbox({'status':'error','msg':'开始时间必须早于结束时间！'});history.back();</script>";exit;
        }
        if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
            $this->display('index');
            echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back();</script>";exit;
        }

        if(1 == $user_type) {
            $map['o.order_status'] = array('in', array(2, 5)); //成功订单
            $map['o.complete_time'] = array('between', array($start_datetime, $end_datetime));
            if($proxy_code){
                $res = M("proxy")->where('proxy_code='.$proxy_code)->field("proxy_id")->find();
            }
            $proxy_id=!empty($res)?$res['proxy_id']:-1;
            $proxy_name=!empty($proxy_name)?$proxy_name:'';
            $get_page = I("p")==""?1:I("p");     //获取当前分页数

            $list = M()->query("CALL p_query_proxy_consume(".$proxy_id.",'".$proxy_name."','".$start_datetime."','".$end_datetime."',".$get_page.",20,1,@p_total_count);");
            //echo M()->getLastSql();
            $count = M()->query("SELECT @p_total_count;");
            $Page       = new Page($count['@p_total_count'],20);
            $show       = $Page->show();
            //var_dump($list);
            $this->assign('list', get_sort_no($list,$Page->firstRow));
            $this->assign('page', $show);
        } else {
            $this->assign('page', ''); //分页
            $this->assign('list', array());
        }

        //dump($list);exit;
        $this->assign('default_start', $start_datetime);
        $this->assign('default_end', $end_datetime);
        $this->display();
    }


    public function index_copy() {
        D("SysUser")->sessionwriteclose();
        $user_type = D('SysUser')->self_user_type();
        //$sel_proxy_id = D('SysUser')->self_proxy_id();
        //$sel_proxy_id = 2;
        //$sel_proxy_info = D('Proxy')->proxyinfo($sel_proxy_id);
        $start_datetime = I('start_datetime') ? I('start_datetime') : date('Y-m-d H:i:s', mktime(0, 0, 0, date('n'), date('d')-7, date('Y')));
        $end_datetime = I('end_datetime') ? I('end_datetime') : date('Y-m-d H:i:s', mktime(23, 59, 59, date('n'), date('j'), date('Y')));
        $proxy_code = trim(I('proxy_code'));
        $proxy_name = trim(I('proxy_name'));

        if($start_datetime > $end_datetime) {
            $this->display('index');
            echo "<script>alertbox({'status':'error','msg':'开始时间必须早于结束时间！'});history.back();</script>";exit;
        }
        if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
            $this->display('index');
            echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back();</script>";exit;
        }

        if(1 == $user_type) {
            $map['o.order_status'] = array('in', array(2, 5)); //成功订单
            $map['o.complete_time'] = array('between', array($start_datetime, $end_datetime));
            ($proxy_code != '') && $map_proxy['p.proxy_code'] = array('like', "%{$proxy_code}%");
            ($proxy_name != '') && $map_proxy['p.proxy_name'] = array('like', "%{$proxy_name}%");
            $map['o.one_proxy_discount'] = array('exp', 'is not null');
            $map['o.one_proxy_id'] = array('exp', 'is not null');

            $map_proxy['p.status'] = 1;
            $map_proxy['p.approve_status'] = 1;
            $map_proxy['p.proxy_level'] = array('elt',1);
            $proxy_list = M('proxy as p')
                ->where($map_proxy)
                ->field('proxy_id,proxy_code,proxy_name')
                ->order('proxy_id desc')
                ->select();

            $join = array(
                "LEFT JOIN t_flow_order_refund r ON r.`order_id` = o.`order_id` AND r.status = 4"
            );
            $count = count($proxy_list);

            $Page       = new Page($count,20);
            $show       = $Page->show();
            $this->assign('page', $show); //分页

            $proxy_ids = array();
            foreach($proxy_list as $pl){
                $proxy_ids[] = $pl['proxy_id'];
            }
            $proxy_ids = array_slice($proxy_ids,$Page->firstRow,$Page->listRows);

            if(empty($proxy_ids)){
                $this->display();
            }else{
                $map['o.one_proxy_id'] = array('in', $proxy_ids);
            }

            $list1 = M('order o')
                ->field("o.`one_proxy_id`,SUM(o.discount_price) AS allodp_de")
                ->where($map)
                ->group("o.`one_proxy_id`")->select();

            $map['r.status'] = 4;
            $list2 = M('order o')
                ->join($join)
                ->field("o.`one_proxy_id`,SUM(r.discount_price) AS allrdp_de")
                ->where($map)
                ->group("o.`one_proxy_id`")->select();

            $result_lists = array();
            if(!empty($proxy_ids) && is_array($proxy_ids)) {
                foreach($proxy_ids as $k => $v) {
                    $result_list['one_proxy_id'] = $v;
                    $result_list['proxy_code'] = '';
                    $result_list['proxy_name'] = '';
                    $result_list['allodp_de'] = 0;
                    $result_list['allrdp_de'] = 0;

                    foreach($proxy_list as $pl){
                        if($pl['proxy_id'] == $v){
                            $result_list['proxy_code'] = $pl['proxy_code'];
                            $result_list['proxy_name'] = $pl['proxy_name'];
                            break;
                        }
                    }

                    foreach($list1 as $v1){
                        if($v1['one_proxy_id'] == $v){
                            $result_list['allodp_de'] = $v1['allodp_de'];
                            break;
                        }
                    }

                    foreach($list2 as $v2){
                        if($v2['one_proxy_id'] == $v){
                            $result_list['allrdp_de'] = $v2['allrdp_de'];
                            break;
                        }
                    }
                    $result_list['allodp_de'] = sprintf("%1.2f", $result_list['allodp_de']);
                    $result_list['allrdp_de'] = sprintf("%1.2f", $result_list['allrdp_de']);
                    $result_list['alldiff'] = sprintf("%1.2f", $result_list['allodp_de'] - $result_list['allrdp_de']);
                    $result_lists[] = $result_list;
                }
            }
            $this->assign('list', get_sort_no($result_lists,$Page->firstRow));
        } else {
            $this->assign('page', ''); //分页
            $this->assign('list', array());
        }

        //dump($list);exit;
        $this->assign('default_start', $start_datetime);
        $this->assign('default_end', $end_datetime);
        $this->display();
    }

	/*
	 * 代理商流量消费统计表(20160525版)

	public function index() {

        $user_type = D('SysUser')->self_user_type();
        //$sel_proxy_id = D('SysUser')->self_proxy_id();
        //$sel_proxy_id = 2;
        //$sel_proxy_info = D('Proxy')->proxyinfo($sel_proxy_id);
        $start_datetime = I('start_datetime') ? I('start_datetime') : date('Y-m-d H:i:s', mktime(0, 0, 0, date('n'), 1, date('Y')));
        $end_datetime = I('end_datetime') ? I('end_datetime') : date('Y-m-d H:i:s', mktime(23, 59, 59, date('n'), date('t'), date('Y')));
        $proxy_code = trim(I('proxy_code'));
        $proxy_name = trim(I('proxy_name'));

        if(1 == $user_type) {
            $map['o.user_type'] = array('eq', 1); //代理商
            $map['p.proxy_level'] = array('eq', 1); //一级代理商
            $map['o.order_status'] = array('in', array(2, 5)); //成功订单
            $map['o.complete_time'] = array('between', array($start_datetime, $end_datetime));
            ($proxy_code != '') && $map['p.proxy_code'] = array('like', "%{$proxy_code}%");
            ($proxy_name != '') && $map['p.proxy_name'] = array('like', "%{$proxy_name}%");
            $map['o.one_proxy_discount'] = array('exp', 'is not null');
            $map['o.one_proxy_id'] = array('exp', 'is not null');

            $count = M('order o')
                    ->join("INNER JOIN ".C('DB_PREFIX')."proxy p ON o.proxy_id = p.proxy_id ")
                    ->where($map)->count("DISTINCT o.`proxy_id`");
            //echo "count== {$count}   ".M()->getLastSql();
            $Page       = new Page($count,20);
            $show       = $Page->show();
            $this->assign('page', $show); //分页

            $list = M('order o')
                    ->join("INNER JOIN ".C('DB_PREFIX')."proxy p ON o.proxy_id = p.proxy_id ")
                    ->join("LEFT JOIN ".C('DB_PREFIX')."order_refund r ON r.`order_id` = o.`order_id` AND r.status = 4 ")
                    ->field("p.`proxy_id`,p.`proxy_code`,p.proxy_name,SUM(o.discount_price) AS allodp_self,SUM(r.discount_price) AS allrdp_self")
                    ->where($map)
                    ->order("allodp_self desc")
                    ->limit($Page->firstRow.','.$Page->listRows)
                    ->group("o.`proxy_id`")->select();
            if(!empty($list) && is_array($list)) {
                //write_debug_log(array(__METHOD__.":".__LINE__, 'sql=='.M()->getLastSql(),'list==', $list));
                foreach($list as $k => &$v) {
                    //$pdc = 1; //运营端给一级代理商设置的折扣 //这块是否要根据每个单所属 运营商和省来判断上级给自己的折扣?（这样计算量太大了，讨论优化方案）
                    $sql1 = "SELECT SUM(o.price * o.one_proxy_discount) AS allodp_de,SUM(r.price * o.one_proxy_discount) AS allrdp_de
                                FROM t_flow_order o
                                LEFT JOIN t_flow_order_refund r ON r.`order_id` = o.`order_id` AND r.status = 4
                                WHERE o.user_type=2 AND o.`order_status` IN(2, 5) AND o.`proxy_id`<>o.`one_proxy_id` AND o.one_proxy_id = {$v['proxy_id']}
                                AND o.complete_time BETWEEN '{$start_datetime}' AND '{$end_datetime}'";
                    $dpt = M('')->query($sql1); //下属企业
                    //write_debug_log(array(__METHOD__.":".__LINE__,'sql1=='.M()->getLastSql()));
                    $sql2 = "SELECT SUM(o.price * o.one_proxy_discount) AS allodp_dp,SUM(r.price * o.one_proxy_discount) AS allrdp_dp
                                FROM t_flow_order o
                                LEFT JOIN t_flow_order_refund r ON r.`order_id` = o.`order_id` AND r.status = 4
                                WHERE o.user_type=1 AND o.`order_status` IN(2, 5) AND o.`proxy_id`<>o.`one_proxy_id` AND o.one_proxy_id = {$v['proxy_id']}
                                AND o.complete_time BETWEEN '{$start_datetime}' AND '{$end_datetime}'";
                    $rpt = M('')->query($sql2);
                    //write_debug_log(array(__METHOD__.":".__LINE__,'sql1=='.M()->getLastSql()));
                    $v['allodp'] = floatval($v['allodp_self']) + floatval($dpt[0]['allodp_de']) + floatval($rpt[0]['allodp_dp']);
                    $v['allrdp'] = floatval($v['allrdp_self']) + floatval($dpt[0]['allrdp_de']) + floatval($rpt[0]['allrdp_dp']);
                    $v['alldiff'] = $v['allodp'] - $v['allrdp'];
                }
             }

            $this->assign('list', get_sort_no($list,$Page->firstRow));
        } else {
            $this->assign('page', ''); //分页
            $this->assign('list', array());
        }

        $this->assign('default_start', $start_datetime);
        $this->assign('default_end', $end_datetime);

        $this->display();
	}
    */

    /**
     * 导出excel
     */
    public function export_excel() {
        $start_datetime = I('start_datetime') ? I('start_datetime') : date('Y-m-d H:i:s', mktime(0, 0, 0, date('n'), date('d')-14, date('Y')));
        $end_datetime = I('end_datetime') ? I('end_datetime') : date('Y-m-d H:i:s', mktime(23, 59, 59, date('n'), date('j'), date('Y')));
        $proxy_code = trim(I('proxy_code'));
        $proxy_name = trim(I('proxy_name'));
        if($start_datetime > $end_datetime) {
            $this->display('index');
            echo "<script>alertbox({'status':'error','msg':'开始时间必须早于结束时间！'});history.back();</script>";exit;
        }
       if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
            $this->display('index');
            echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back();</script>";exit;
        }
        $map['o.order_status'] = array('in', array(2, 5)); //成功订单
        $map['o.complete_time'] = array('between', array($start_datetime, $end_datetime));
        if($proxy_code){
            $res = M("proxy")->where('proxy_code='.$proxy_code)->field("proxy_id")->find();
        }
        $proxy_id=!empty($res)?$res['proxy_id']:-1;
        $proxy_name=!empty($proxy_name)?$proxy_name:'';
        //$get_page = I("p")==""?1:I("p");     //获取当前分页数

        $list_res = M()->query("CALL p_query_proxy_consume(".$proxy_id.",'".$proxy_name."','".$start_datetime."','".$end_datetime."',1,3000,1,@p_total_count);");
        $count = M()->query("SELECT @p_total_count;");
        $list=array();
        foreach($list_res as $k=>$v){
            $list[$k]['proxy_code']=$v['proxy_code'];
            $list[$k]['proxy_name']=$v['proxy_name'];
            $list[$k]['stat_price']=$v['stat_price'];
            $list[$k]['stat_refund_price']=$v['stat_refund_price'];
            $list[$k]['stat_count']=get_consume_money($v['stat_price'],$v['stat_refund_price']);
        }
        $title='代理流量消费统计';
        $headArr=array("代理商编号","代理商名称","折后价(元)","退款金额(元)","代理商消费(元)");
        ExportEexcel($title,$headArr,$list);
    }


}
?>