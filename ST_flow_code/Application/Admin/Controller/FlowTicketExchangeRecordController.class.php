<?php
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;

/**
 * 流量充值
 */
class FlowTicketExchangeRecordController extends CommonController {

    public function index(){
        D("SysUser")->sessionwriteclose();
        $user_type = ((int)D('SysUser')->self_user_type())-1;
        $proxy_id = intval(D('SysUser')->self_proxy_id());
        $enterprise_id = intval(D('SysUser')->self_enterprise_id());

        $mobile = trim(I('mobile'));
        $operator_id = trim(I('operator_id'));
        $product_name = trim(I('product_name'));
        $start_datetime = trim(I('start_datetime'));
        $end_datetime = trim(I('end_datetime'));
        $status = trim(I('status'));

        $condition = array();
        $condition['te.user_type'] = $user_type;
        if($user_type==1){
            $condition['te.proxy_id'] = $proxy_id;
        }elseif($user_type==2){
            $condition['te.enterprise_id'] = $enterprise_id;
        }
        if($status != 9){
            switch ($status) {
                case 1:
                    $where1['o.order_status'] = array("exp","is null");
                    $where1['te.order_id'] = array("neq","");
                    $where1['_logic'] = "and";
                    $condition[]=$where1;
                    break;
                case 2:
                    $where1['o.order_status'] = array("in","2,5");
                    $where1['te.order_id'] = array('neq',"");
                    $where1['_logic']="and";
                    $condition[]=$where1;
                    break;
                case 3:
                    $where1['o.order_status']= 6;
                    $where1['te.order_id'] = array("eq","");
                    $where1['_logic'] = "or";
                    $condition[] = $where1;
                    break;
                default:
                    # code...
                    break;
            }
        }

        if ($mobile) {
            # code...
            $condition['te.mobile'] = array("like","%".$mobile."%");
        }

        // dump('operator_id = '.$operator_id);
        // exit();
        if ($operator_id != null && $operator_id != 9) {
            # code...
            $condition['te.operator_id'] = $operator_id;
        }

        if($product_name){
            $condition['p.product_name']=array('like','%'.$product_name.'%');
        }

        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $condition['te.exchange_time'] = array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $condition['te.exchange_time'] = array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $condition['te.exchange_time'] = array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            //$end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $end_datetime= strtotime($start_datetime)-2592000;
            $e_time=start_time(date('Y-m-d',$end_datetime));
            $condition['te.exchange_time'] = array('between',array($e_time,$start_datetime));
        }
        $record_count = M("ticket_exchange as te")
            ->join('left join t_flow_product as p on p.product_id =  te.product_id')
            ->join('left join t_flow_order as o on o.order_code = te.order_id')
            ->where($condition)
            ->count();
        $Page = new \Think\Page($record_count, 20);
        $show = $Page->show();

        $ticketExchangeRecordList = M("ticket_exchange as te")
            ->join('left join t_flow_scene_user_activity as ua on ua.user_activity_id = te.user_activity_id')
            ->join('left join t_flow_scene_activity as sa on ua.activity_id = sa.activity_id')
            ->join('left join t_flow_product as p on te.product_id = p.product_id')
            ->join('left join t_flow_order as o on o.order_code = te.order_id')
            ->field('te.*, sa.activity_name,ua.user_activity_name,p.product_name, o.order_status')
            ->where($condition)
            ->order('te.exchange_time desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        // dump($ticketExchangeRecordList);

        $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
        $end_datetime= strtotime($start_datetime)-2592000;
        $e_time=start_time(date('Y-m-d',$end_datetime));
        $this->assign('default_end',$start_datetime);
        $this->assign('default_start',$e_time);

        $resultRecordList = array('list' => get_sort_no($ticketExchangeRecordList, $Page->firstRow), 'page' => $show);
        $this->assign('list', $resultRecordList['list']);
        $this->assign('page', $resultRecordList['page']);
        $this->display();
    }

    public function show(){

        $id = trim(I('get.redeem_id'));
        $condition = array();
        $condition['te.redeem_id'] = $id;

        $user_type = ((int)D('SysUser')->self_user_type())-1;
        $proxy_id = intval(D('SysUser')->self_proxy_id());
        $enterprise_id = intval(D('SysUser')->self_enterprise_id());
        $condition['te.user_type'] = $user_type;
        if($user_type==1){
            $condition['te.proxy_id'] = $proxy_id;
        }elseif($user_type==2){
            $condition['te.enterprise_id'] = $enterprise_id;
        }


        // $ticketExchangeDetialInfo = M("scene_user_activity as ua")
        //     ->field('te.*, p.product_name, sa.activity_name, o.order_status')
        //     ->where($condition)
        //     ->join('inner join t_flow_ticket_exchange as te on ua.user_activity_id = te.user_activity_id')
        //     ->join('left join t_flow_scene_activity as sa on ua.activity_id = sa.activity_id')
        //     ->join('left join t_flow_product as p on te.product_id = p.product_id')
        //     ->join('left join t_flow_order as o on o.order_code = te.order_id')
        //     ->find();
        $ticketExchangeDetialInfo = M("ticket_exchange as te")
            ->join('left join t_flow_scene_user_activity as ua on ua.user_activity_id = te.user_activity_id')
            ->join('left join t_flow_scene_activity as sa on ua.activity_id = sa.activity_id')
            ->join('left join t_flow_product as p on te.product_id = p.product_id')
            ->join('left join t_flow_order as o on o.order_code = te.order_id')
            ->field('te.*, p.product_name, sa.activity_name,ua.user_activity_name,o.order_status')
            ->where($condition)
            ->find();

        $this->assign('info', $ticketExchangeDetialInfo);
        $this->display();
    }

    /**
    *   导出EXCEL
    **/
    public function export_excel()
    {
        $user_type = ((int)D('SysUser')->self_user_type())-1;
        $proxy_id = intval(D('SysUser')->self_proxy_id());
        $enterprise_id = intval(D('SysUser')->self_enterprise_id());

        $mobile = trim(I('mobile'));
        $operator_id = trim(I('operator_id'));
        $product_name = trim(I('product_name'));
        $start_datetime = trim(I('start_datetime'));
        $end_datetime = trim(I('end_datetime'));
        $status = trim(I('status'));

        $condition = array();
        if($user_type==1){
            $condition['te.proxy_id'] = $proxy_id;
        }elseif($user_type==2){
            $condition['te.enterprise_id'] = $enterprise_id;
        }

        if($status != 9){

            switch ($status) {
                case 1:
                    $where['o.order_status'] = array("exp","is null");
                    $where['te.order_id'] = array("neq","");
                    $where['_logic'] = "and";
                    $condition[]=$where;
                    break;
                case 2:
                    $where['o.order_status'] = array("in","2,5");
                    $where['te.order_id'] = array('neq',"");
                    $where['_logic']="and";
                    $condition[]=$where;
                    break;
                case 3:
                    $where['o.order_status']= 6;
                    $where['te.order_id'] = array("eq","");
                    $where['_logic'] = "or";
                    $condition[] = $where;
                    break;
                default:
                    # code...
                    break;
            }
        }

        if ($mobile) {
            # code...
            $condition['te.mobile'] = array("like","%".$mobile."%");
        }

        // dump('operator_id = '.$operator_id);
        // exit();
        if ($operator_id != null && $operator_id != 9) {
            # code...
            $condition['te.operator_id'] = $operator_id;
        }

        if($product_name){
            $condition['p.product_name']=array('like','%'.$product_name.'%');
        }

        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $condition['te.exchange_time'] = array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $condition['te.exchange_time'] = array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $condition['te.exchange_time'] = array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            //$end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $end_datetime= strtotime($start_datetime)-2592000;
            $e_time=start_time(date('Y-m-d',$end_datetime));
            $condition['te.exchange_time'] = array('between',array($e_time,$start_datetime));
        }

        // $ticketExchangeRecordList = M("scene_user_activity as ua")
        //     ->field('te.*, p.product_name, sa.activity_name, o.order_status')
        //     ->where($condition)
        //     ->join('inner join t_flow_ticket_exchange as te on ua.user_activity_id = te.user_activity_id')
        //     ->join('left join t_flow_scene_activity as sa on ua.activity_id = sa.activity_id')
        //     ->join('left join t_flow_product as p on te.product_id = p.product_id')
        //     ->join('left join t_flow_order as o on o.order_code = te.order_id')
        //     ->order('te.exchange_time desc')
        //     ->limit(3000)
        //     ->select();
        $ticketExchangeRecordList = M("ticket_exchange as te")
            ->field('te.*, p.product_name, sa.activity_name,ua.user_activity_name,o.order_status')
            ->where($condition)
            ->join('inner join t_flow_scene_user_activity as ua on ua.user_activity_id = te.user_activity_id')
            ->join('left join t_flow_scene_activity as sa on ua.activity_id = sa.activity_id')
            ->join('left join t_flow_product as p on te.product_id = p.product_id')
            ->join('left join t_flow_order as o on o.order_code = te.order_id')
            ->order('te.exchange_time desc')
            ->limit(3000)
            ->select();

        $datas = array();
        $headArr = array("兑换码", "手机号", "运营商", "流量包", "兑换时间", "充值状态", "活动名称");
        $operators = array("1"=>"中国移动","2"=>"中国联通","3"=>"中国电信");

        foreach ($ticketExchangeRecordList as $v) {
            
            $data = array();
            $data['redeem_code'] = $v['redeem_code'];
            $data['mobile'] = $v['mobile'];
            $data['operator_id'] = $operators[$v['operator_id']];
            $data['product_name'] = $v['product_name'];
            $data['exchange_time'] = $v['exchange_time'];

            $data['order_status'] = "正在送充";
            if(empty($v['order_id']) || $v['order_status'] == 6){
                $data['order_status'] = "充值失败";
            }elseif($v['order_status'] == 2 || $v['order_status'] == 5){
                $data['order_status'] = "充值成功";
            }
            array_push($datas, $data);
        }

        $title = '流量券兑换记录';
        ExportEexcel($title, $headArr, $datas);
    }
}