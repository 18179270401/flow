<?php
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;

/**
 * 代理商结算单
 * @package Admin\Controller
 */
class ProxyStatementsController extends CommonController
{
    public function index()
    {
        $proxy_code = trim(I("proxy_code"));
        $proxy_name = trim(I("proxy_name"));
        $uset_type=D("SysUser")->self_user_type();
        $self_proxy_id = D("SysUser")->self_proxy_id();
        if($uset_type==2){
            $where1['p.proxy_id']=$self_proxy_id;
            $where1['_logic']="or";
            $where1['p.top_proxy_id']=$self_proxy_id;
            $where[]=$where1;
        }
        if(!empty($proxy_code)){
            $where['p.proxy_code']=array("like","%$proxy_code%");
        }
        if(!empty($proxy_name)){
            $where['p.proxy_name']=array("like","%$proxy_name%");
        }
        $where['sp.stat_status'] = "205";//获取成功的订单
        $where['sp.stat_type'] = 1;//表示是代理商
        //$where['sp.stat_month'] = array("neq",date("Ym",time()));
        $count = M("stat_product as sp")
            ->join("t_flow_proxy as p on p.proxy_id = sp.user_id", "left")
            ->field("p.proxy_code,sp.user_name,sp.stat_month,sum(sp.discount_price)")
            ->where($where)
            ->group("sp.stat_month,sp.user_id")
            ->count();
        $Page = new Page($count, 20);
        $show = $Page->show();
        $list = M("stat_product as sp")
            ->where($where)
            ->join("t_flow_proxy as p on p.proxy_id = sp.user_id", "left")
            ->field("p.proxy_code,sp.user_name,sp.stat_month,sum(sp.discount_price) as allprice,sp.user_id")
            ->group("sp.stat_month,sp.user_id")
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
            $this->error("信息错误");
        }
        $info = M("proxy")->where("proxy_id=" . $user_id)->find();
        $where['stat_month']=$stat_month;
        $where['user_id'] = $user_id;
        $where['stat_status'] = "205";
        $where['stat_type'] = 1;//表示代理商
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
        StatementsExportEexcel("代理结算单",$info,$list,$arr,$date);
    }
}
?>