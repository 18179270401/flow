<?php
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;

/**
 * 上游结算单
 * @package Admin\Controller
 */
class TopStatementsController extends CommonController
{
    public function index()
    {
        $channel_code=trim(I("channel_code"));
        $channel_name=trim(I("channel_name"));
        if(!empty($channel_name)){
            $where['channel_name']=array("like","%$channel_name%");
        }
        if(!empty($channel_code)){
            $where['channel_code']=array("like","%$channel_code%");
        }
        $where['stat_status'] = "205";//获取成功的订单
        $where['stat_month'] = array("neq",date("Ym",time()));
        $count1 = M("stat_product")
            ->field("channel_id,channel_code,channel_name,stat_month,sum(stat_count) as counts,sum(stat_price)as bprice,sum(top_price-rebate_price) as dprice")
            ->where($where)
            ->group("stat_month,channel_id")
            ->select();
        $count=count($count1);
        $Page = new Page($count, 20);
        $show = $Page->show();
        $list = M("stat_product")
            ->field("channel_id,channel_code,channel_name,stat_month,sum(stat_count) as counts,sum(stat_price)as bprice,sum(top_price-rebate_price) as dprice")
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->group("stat_month,channel_id")
            ->select();

        $this->assign('list', get_sort_no($list, $Page->firstRow));  //数据列表
        $this->assign('page', $show);
        $this->display();
    }

    public function export_excel()
    {
        $channel_id = trim(I("user_id"));
        $stat_month=trim(I("stat_month"));
        if (empty($channel_id) || empty($stat_month)) {
            $this->error("信息错误");
        }
        $where['stat_month']=$stat_month;
        $where['channel_id'] =$channel_id;
        $where['stat_status'] = "205";
        $list = M("stat_product")
            ->field("channel_name,product_name,SUM(stat_count) AS counts,round(SUM(stat_price),3) AS statprice,SUM(top_price - rebate_price) AS allprice,round((stat_price/stat_count),3) AS bprice")
            ->where($where)
            ->group("product_name,bprice asc")
            ->select();
        $headArr = array("通道名称","产品名称","订单数(个)","原价(元)","折扣价格(元)");
        foreach ($list as $k=>$v){
            unset($list[$k]['bprice']);
        }
        ExportEexcel("上游结算单",$headArr,$list);
    }
}
?>