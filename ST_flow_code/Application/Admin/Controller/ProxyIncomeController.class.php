<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
class ProxyIncomeController extends CommonController {
//代理收入统计列表
    public function index(){
        D("SysUser")->sessionwriteclose();
        $user_type = D('SysUser')->self_user_type();
        $start_datetime = I('start_datetime') ? I('start_datetime') : date('Y-m-d', mktime(0, 0, 0, date('n'), 1, date('Y')));
        $end_datetime = I('end_datetime') ? I('end_datetime') : date('Y-m-d', mktime(0, 0, 0, date('n'), date('j')-1, date('Y')));
        if(1 == date('j')) {
            $start_datetime = I('start_datetime') ? I('start_datetime') : date('Y-m-d', strtotime('-1 month', mktime(0, 0, 0, date('n'), 1, date('Y'))));
            $end_datetime = I('end_datetime') ? I('end_datetime') : date('Y-m-d', mktime(0, 0, 0, date('n',strtotime($start_datetime)), date('t',strtotime($start_datetime)), date('Y',strtotime($start_datetime))));
        }

        //$arr_direct_enterprise = D('Proxy')->get_direct_enterprise();
        $enterprise_code = trim(I('enterprise_code'));
        $enterprise_name = trim(I('enterprise_name'));
        $proxy_type=D('SysUser')->is_self_proxy();
        $expense_sum_total = $cost_sum_total = $profit_sum_total = 0;
        if(2 == $user_type && $proxy_type) {
            $proxy_id=D('SysUser')->self_proxy_id(); //当前代理商id
            $map['rde.rpt_date'] = array('between', array($start_datetime, $end_datetime));
            ($enterprise_code != '') && $map['e.enterprise_code'] = array('like', "%".$enterprise_code."%");
            ($enterprise_name != '') && $map['e.enterprise_name'] = array('like', "%".$enterprise_name."%");
             $map['e.top_proxy_id'] = $proxy_id;

            $count = M('rpt_direct_enterprise rde')
                ->join("INNER JOIN ".C('DB_PREFIX')."enterprise e ON rde.enterprise_id = e.enterprise_id ")
                ->where($map)->count("DISTINCT rde.`enterprise_id`");
            //echo "count== {$count}   ".M()->getLastSql();
            $Page       = new Page($count,20);
            $show       = $Page->show();
            $this->assign('page', $show); //分页

            $list = M('rpt_direct_enterprise rde')
                ->join("INNER JOIN ".C('DB_PREFIX')."enterprise e ON rde.enterprise_id = e.enterprise_id ")
                ->field("e.`enterprise_code`,e.`enterprise_name`,SUM(rde.expense_sum) AS all_expense_sum,SUM(rde.cost_sum) AS all_cost_sum,SUM(rde.rebate_sum) AS all_rebate_sum,SUM(rde.profit_sum) AS all_profit_sum")
                ->where($map)
                ->order("all_profit_sum desc,enterprise_code asc")
                ->limit($Page->firstRow.','.$Page->listRows)
                ->group("rde.`enterprise_id`")->select();
            //write_debug_log(array(__METHOD__.'：'.__LINE__, 'sql== '.M()->getLastSql()));
            if(!empty($list) && is_array($list)) {
                foreach($list as $k => &$v) {
                    $v['all_expense_sum']   = sprintf("%1.2f", $v['all_expense_sum']);
                    $v['all_cost_sum']      = sprintf("%1.2f", $v['all_cost_sum']);
                    $v['all_rebate_sum']    = sprintf("%1.2f", $v['all_rebate_sum']);
                    $v['all_profit_sum']    = sprintf("%1.2f", $v['all_profit_sum']);
                    $v['profit_percent']    = (empty($v['all_expense_sum']) || empty($v['all_profit_sum'])) ? 0 : round($v['all_profit_sum']/$v['all_expense_sum']*100, 2);
                    $v['profit_percent']    = sprintf("%1.2f", $v['profit_percent']);
                }
            }

            $expense_sum_total1=  M('rpt_direct_enterprise rde')
                ->join("INNER JOIN ".C('DB_PREFIX')."enterprise e ON rde.enterprise_id = e.enterprise_id ")
                ->where($map)->field("SUM(rde.expense_sum) AS expense_sum_total")->select();
            $expense_sum_total = $expense_sum_total1[0]['expense_sum_total'];

            $profit_sum_total1=  M('rpt_direct_enterprise rde')
                ->join("INNER JOIN ".C('DB_PREFIX')."enterprise e ON rde.enterprise_id = e.enterprise_id ")
                ->where($map)->field("SUM(rde.profit_sum) AS profit_sum_total")->select();
            $profit_sum_total = $profit_sum_total1[0]['profit_sum_total'];

            $cost_sum_total1=  M('rpt_direct_enterprise rde')
                ->join("INNER JOIN ".C('DB_PREFIX')."enterprise e ON rde.enterprise_id = e.enterprise_id ")
                ->where($map)->field("SUM(rde.cost_sum) AS cost_sum_total")->select();
            $cost_sum_total = $cost_sum_total1[0]['cost_sum_total'];

            $rebate_sum_total1=  M('rpt_direct_enterprise rde')
                ->join("INNER JOIN ".C('DB_PREFIX')."enterprise e ON rde.enterprise_id = e.enterprise_id ")
                ->where($map)->field("SUM(rde.rebate_sum) AS rebate_sum_total")->select();
            $rebate_sum_total = $rebate_sum_total1[0]['rebate_sum_total'];

            $this->assign('list', get_sort_no($list,$Page->firstRow));
        } else {
            $this->assign('page', ''); //分页
            $this->assign('list', array());
        }

        $this->assign('default_start', $start_datetime);
        $this->assign('default_end', $end_datetime);
        $this->assign('expense_sum_total', sprintf("%1.2f", $expense_sum_total));
        $this->assign('cost_sum_total', sprintf("%1.2f", $cost_sum_total));
        $this->assign('profit_sum_total', sprintf("%1.2f", $profit_sum_total));
        $this->assign('rebate_sum_total', sprintf("%1.2f", $rebate_sum_total));

        $this->display();
  }

    /**
     * 代理收入统计列表导出
     */
    public function export_excel() {
        $start_datetime = I('start_datetime') ? I('start_datetime') : date('Y-m-d', mktime(0, 0, 0, date('n'), 1, date('Y')));
        $end_datetime = I('end_datetime') ? I('end_datetime') : date('Y-m-d', mktime(0, 0, 0, date('n'), date('j')-1, date('Y')));
        if(1 == date('j')) {
            $start_datetime = I('start_datetime') ? I('start_datetime') : date('Y-m-d', strtotime('-1 month', mktime(0, 0, 0, date('n'), 1, date('Y'))));
            $end_datetime = I('end_datetime') ? I('end_datetime') : date('Y-m-d', mktime(0, 0, 0, date('n',strtotime($start_datetime)), date('t',strtotime($start_datetime)), date('Y',strtotime($start_datetime))));
        }

        $enterprise_code = trim(I('enterprise_code'));
        $enterprise_name = trim(I('enterprise_name'));
        $proxy_id=D('SysUser')->self_proxy_id(); //当前代理商id
        $map['rde.rpt_date'] = array('between', array($start_datetime, $end_datetime));
        ($enterprise_code != '') && $map['e.enterprise_code'] = array('like', "%".$enterprise_code."%");
        ($enterprise_name != '') && $map['e.enterprise_name'] = array('like', "%".$enterprise_name."%");
        $map['e.top_proxy_id'] = $proxy_id;

        $list = M('rpt_direct_enterprise rde')
            ->join("INNER JOIN ".C('DB_PREFIX')."enterprise e ON rde.enterprise_id = e.enterprise_id ")
            ->field("e.`enterprise_code`,e.`enterprise_name`,SUM(rde.expense_sum) AS all_expense_sum,SUM(rde.cost_sum) AS all_cost_sum,SUM(rde.rebate_sum) AS all_rebate_sum,SUM(rde.profit_sum) AS all_profit_sum")
            ->where($map)
            ->limit(3000)
            ->order("all_profit_sum desc,enterprise_code asc")
            ->group("rde.`enterprise_id`")->select();

        if(!empty($list) && is_array($list)) {
            foreach($list as $k => &$v) {
                $v['all_expense_sum']   = sprintf("%1.2f", $v['all_expense_sum']);
                $v['all_cost_sum']      = sprintf("%1.2f", $v['all_cost_sum']);
                $v['all_rebate_sum']    = sprintf("%1.2f", $v['all_rebate_sum']);
                $v['all_profit_sum']    = sprintf("%1.2f", $v['all_profit_sum']);
                $v['profit_percent']    = (empty($v['all_expense_sum']) || empty($v['all_profit_sum'])) ? 0 : round($v['all_profit_sum']/$v['all_expense_sum']*100, 2);
                $v['profit_percent']    = sprintf("%1.2f", $v['profit_percent']).' %';
            }
        }

        $title='代理收入统计';
        $headArr=array("企业编号","企业名称","消费总额(元)","成本总额(元)","应收返利(元)","利润总额(元)","毛利率");
        ExportEexcel($title,$headArr,$list);
    }


}
