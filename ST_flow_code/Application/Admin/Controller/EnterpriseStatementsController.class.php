<?php
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;

/**
 * 企业结算单
 * @package Admin\Controller
 */
class EnterpriseStatementsController extends CommonController
{
    public function index()
    {
        $enterprise_code = trim(I("enterprise_code"));
        $enterprise_name = trim(I("enterprise_name"));
        $uset_type=D("SysUser")->self_user_type();
        $self_proxy_id = D("SysUser")->self_proxy_id();
        $self_enterprise_id = D("SysUser")->self_enterprise_id();
        if($uset_type==2) {
            $where['e.top_proxy_id']=$self_proxy_id;
        }
        if($uset_type==3) {
            $where['e.enterprise_id']=$self_enterprise_id;
        }
        if(!empty($enterprise_code)){
            $where['e.enterprise_code']=array("like","%$enterprise_code%");
        }
        if(!empty($enterprise_name)){
            $where['e.enterprise_name']=array("like","%$enterprise_name%");
        }
        $where['sp.stat_status'] = "205";//获取成功的订单
        $where['sp.stat_type'] = 2;//表示是企业
        //$where['sp.stat_month'] = array("neq",date("Ym",time()));
        $count = M("stat_product as sp")
            ->join("t_flow_enterprise as e on e.enterprise_id = sp.user_id", "left")
            ->field("e.enterprise_code,sp.user_name,sp.stat_month,sum(sp.discount_price)")
            ->where($where)
            ->group("sp.stat_month,user_id")
            ->count();
        $Page = new Page($count, 20);
        $show = $Page->show();
        $list = M("stat_product as sp")
            ->where($where)
            ->join("t_flow_enterprise as e on e.enterprise_id = sp.user_id", "left")
            ->field("e.enterprise_code,sp.user_name,sp.stat_month,sum(sp.discount_price) as allprice,sp.user_id")
            ->group("sp.stat_month,user_id")
            ->select();
        $this->assign('list', get_sort_no($list, $Page->firstRow));  //数据列表
        $this->assign('show', $show);
        $this->display();
    }

    public function export_excel()
    {
        $user_id = trim(I("user_id"));
        $stat_month=trim(I("stat_month"));
        if (empty($user_id) || empty($stat_month)) {
            $this->error("信息错误！");
        }
        $info = M("enterprise")->where("enterprise_id=" . $user_id)->find();
        $where['stat_month']=$stat_month;
        $where['user_id'] = $user_id;
        $where['stat_status'] = "205";
        $where['stat_type'] = 2;//表示是企业
        $list = M("stat_product")
            ->where($where)
            ->field("`product_name`,SUM(stat_count) AS counts,round(SUM(stat_price),3) AS statprice,`operator_id`,round((discount_price/stat_price),3)AS salediscount,SUM(discount_price) AS allprice,round((stat_price/stat_count),3) AS bprice")
            ->group("stat_month,product_name,operator_id,salediscount")
            ->order("operator_id asc,bprice asc")
            ->select();
        $arr = array();
        foreach ($list as $k=>$v) {
            $list[$k]['dprice']=round($v['allprice']/$v['counts'],3);
            switch ($v['operator_id']) {
                case 1:
                    $arr[1] += $v['allprice'];
                    break;
                case 2:
                    $arr[2] += $v['allprice'];
                    break;
                case 3:
                    $arr[3] += $v['allprice'];
            }
            $arr[4]=$arr[1]+$arr[2]+$arr[3];
        }
        $firstday = date('Y-m-01', strtotime($stat_month."01"));
        $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
        $date=date("Y-m-d",mktime(0,0,0,substr($stat_month,-2,2),1,substr($stat_month,0,4))).'至'.$lastday;
        StatementsExportEexcel("业务对账单",$info,$list,$arr,$date);
    }
}
?>