<?php
namespace WxHome\Controller;
use Think\Controller;

class HomeController extends Controller {
    public function index(){
      // $p_user_id = 163;
      $data = session("enterprise_key");
      if ($data != null) {
        $strArray = localdecode($data);
        $InfoArray = explode(",",$strArray);
        $p_user_id = $InfoArray[1];
      }else{
        $this->display('Public:404');
      }
      $user_info = json_encode(D("SysUser")->get_user_info($InfoArray[0]));
      $user_info_data_array = json_decode($user_info,true);
      $user_info_data = $user_info_data_array['info'];
      // 企业id
      $this->assign('p_user_id',$p_user_id);
      // 账户余额
      $this->assign('account_balance',$user_info_data['account_balance']);
      // 累计存款
      $this->assign('deposit_sum',$user_info_data['deposit_sum']);
      // 冻结余额
      $this->assign('freeze_money',$user_info_data['freeze_money']);
      // 企业名称
      $this->assign('enterprise_name',$user_info_data['enterprise_name']);
      $this->display("index");
    }

    public function get_chart_data(){
      $type = I('type');
      $p_user_id = I('p_user_id');
      // 本月
      $begintime = date("Y-m-d",mktime(0,0,0,date('m'),1,date('Y')));
      $endtime = date("Y-m-d",mktime(23,59,59.999999,date('m'),date('t'),date('Y')));
      if ($type == 1) {
        // 本周
        $begintime = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y')));
        $endtime = date("Y-m-d",mktime(23,59,59.999999,date('m'),date('d')-date('w')+7,date('Y')));
      }
      $p1_records = M()->query("CALL p_get_stat_order_home(2,0,'".$begintime."','".$endtime."',0,".$p_user_id.");");
      $p1_datas = array();
      foreach ($p1_records as $r) {
        $temp['name'] = $r['operator_id'];
        $temp['value'] = $r['stat_count'];
        array_push($p1_datas,$temp);
      }
      if (count($p1_datas) == 0) {
        $p1_datas[0]['name'] = '中国移动';
        $p1_datas[0]['value'] = 0;
        $p1_datas[1]['name'] = '中国联通';
        $p1_datas[1]['value'] = 0;
        $p1_datas[2]['name'] = '中国电信';
        $p1_datas[2]['value'] = 0;
      }
      // 流量充值图表
      $p2_records = M()->query("CALL p_get_stat_order_home(2,1,'".$begintime."','".$endtime."',0,".$p_user_id.");");
      $p2_op_datas = array();
      $p2_value_datas = array();
      foreach ($p2_records as $r) {
        array_push($p2_op_datas,$r['operator_id']);
        array_push($p2_value_datas,$r['stat_size']);
      }
      // 用户地域分布图表
      $p3_records = M()->query("CALL p_get_stat_order_home(2,2,'".$begintime."','".$endtime."',0,".$p_user_id.");");
      $p3_op_datas = array();
      $p3_value_datas = array();
      foreach ($p3_records as $r) {
        array_push($p3_op_datas,$r['province_name']);
        array_push($p3_value_datas,$r['stat_count']);
      }
      $this->ajaxReturn(array('data_p1'=>$p1_datas,
                              'ops_p2'=>$p2_op_datas,
                              'data_p2'=>$p2_value_datas,
                              'ops_p3'=>$p3_op_datas,
                              'data_p3'=>$p3_value_datas));
    }
}
