<?php
namespace WxHome\Controller;
use Think\Controller;

class UserController extends CommonController {
    /**
     * @return bool
     * 过滤信息
     */

    public function getFilterInfo() {
            //判断除企业之外的代理商显示
        	$filters = array();
			// 添加运营商
			$operator = array();
			$operator['id'] = C('FILTER_OPERATOR_ID');
			$operator['name'] = '运营商';
			$operatorFilters = array();
			
			// 1移动 2联通 3电信
			$allData['id'] = '0';
			$allData['name'] = '全部';
			$operatorFilters[] = $allData;
			
			// 1移动 2联通 3电信
			$mobileData['id'] = '1';
			$mobileData['name'] = '中国移动';
			$operatorFilters[] = $mobileData;
			
			$unicomData['id'] = '2';
			$unicomData['name'] = '中国联通';
			$operatorFilters[] = $unicomData;
			
			$telecomData['id'] = '3';
			$telecomData['name'] = '中国电信';
			$operatorFilters[] = $telecomData;
			
			$operator['filters'] = $operatorFilters;
			$filters[] = $operator;
			
			// 添加省份
			$filterProVince = array();
			$filterProVince['id'] = C('FILTER_PROVINCE_ID');
			$filterProVince['name'] = '省份';
			
			$province_list =  D("ChannelProduct")->province_list();
//          $info['province_list'] = $province_list;
			$filterProVinceList = array();
			$filterProVinceList[] = array('id'=>'0','name'=>'全部');
			for($i = 0;$i < count($province_list);$i++)
			{
				$provinceData = $province_list[$i];
				$provinceFilterData['id'] = $provinceData['province_id'];
				$provinceFilterData['name'] = $provinceData['province_name'];
				$filterProVinceList[] = $provinceFilterData;
			}
			$filterProVince['filters'] = $filterProVinceList;
			$filters[] =  $filterProVince;

			
			$info['filters'] = $filters;
            
            return show_sucess('获取用户信息',$info);
    }

    public function getRecordRecordStat($operator,$placeid,$start_time,$end_time,$user_id) {
        //  $login_name_full = "admin@100133";
        $sys_user_data = M('sys_user')->where(array('user_id'=>$user_id))->find();
        if($sys_user_data == false)
        {
            // 找不到该账户
            return show_error('Account_Not_Exit');
        }
        $enterpriseid = $sys_user_data['enterprise_id'];
        if(enterpriseid==0)
        {
             $enterprise_data = M('enterprise')->where(array('enterprise_id'=>$enterpriseid))->find();
        }
        else 
        {
            // 找不到该企业
            return show_error('Enterprise_Not_Exit');
        }

        $start_date = $start_time;
        $end_date = $end_time;

        $operator_id = $operator;
        if ($operator_id == 0)
        {
            $operator_id = -1;
        }
       
        $province_id = $placeid;
        if ($province_id == 0)
        {
            $province_id = -1;
        }
        $city_id=-1;
        $channel_id = '';
        $bc_channel_id = '';
        $order_status = '';

        $new_m = date('Ym',strtotime("-3 month"));     //历史最新月份
 
        $start_date = date('Y-m-d 00:00:00',strtotime($start_date));
        $end_date = date('Y-m-d 23:59:59',strtotime($end_date));

            if(strtotime($end_date) - strtotime($start_date) > 2678400){
                return show_error('Time_More_Than_A_Month');
            }

        $the_m = 2; //数据表
        $start_m = date('Ym',strtotime($start_date));
        $end_m = date('Ym',strtotime($end_date));
        if( $start_m >= $new_m ){
            $the_m = 2;
        }else{
            $the_m = $start_m;
            if($start_m != $end_m){
                return show_error('Time_Must_In_A_Month');
            }
        }

        $user_info = array();
        // $user_id = $enterpriseid;

            $operator_id = !empty($operator_id) ? $operator_id : -1 ;  //运营商

            $list = D('Order')->wx_home_order_storing_process($the_m,$user_id,'','','','','','',$operator_id,$province_id,$city_id,$order_status,$start_date,$end_date,'',1,-1);

            $stat = $list['count'];

            $info['success_count'] = empty($stat['@p_success_count'])?0:$stat['@p_success_count'];
            $info['success_price'] = empty($stat['@p_success_price'])?0:$stat['@p_success_price'];
            $info['success_amount'] = empty($stat['@p_success_amount'])?0:$stat['@p_success_amount'];

            $info['faile_count'] = empty($stat['@p_faile_count'])?0:$stat['@p_faile_count']; //失败数
            $info['faile_price'] = empty($stat['@p_faile_price'])?0:$stat['@p_faile_price']; //原价
            $info['faile_amount'] = empty($stat['@p_faile_amount'])?0:$stat['@p_faile_amount']; //折后价

            $info['submit_success_count'] = 0;
            $info['submit_success_amount'] = 0;
            $info['submit_success_price'] = 0;

            $info['wait_count'] = 0;
            $info['wait_amount'] = 0;
            $info['wait_price'] = 0;

            if( $the_m == 2 ){
                $list2 = D('Order')->wx_home_order_storing_process(1,$user_id,'','','','','','',$operator_id,$province_id,$city_id,$order_status,$start_date,$end_date,'',1,-1);

                $list2_count = $list2['count'];
                $p_wait_count = $list2_count['@p_wait_count'];
                $p_faile_count = $list2_count['@p_faile_count'];
                $p_wait_amount = $list2_count['@p_wait_amount'];
                $p_faile_amount = $list2_count['@p_faile_amount'];
                $p_faile_price = $list2_count['@p_faile_price'];
                $p_wait_price = $list2_count['@p_wait_price'];
                $submit_success_count = $list2_count['@submit_success_count'];
                $submit_success_amount = $list2_count['@submit_success_amount'];
                $p_submit_success_price = $list2_count['@p_submit_success_price'];

                $p_wait_count = empty($p_wait_count)?0:$p_wait_count;
                $p_faile_count = empty($p_faile_count)?0:$p_faile_count;
                $p_wait_amount = empty($p_wait_amount)?0:$p_wait_amount;
                $p_faile_amount = empty($p_faile_amount)?0:$p_faile_amount;
                $p_faile_price = empty($p_faile_price)?0:$p_faile_price;
                $p_wait_price = empty($p_wait_price)?0:$p_wait_price;
                $submit_success_count = empty($submit_success_count)?0:$submit_success_count;
                $submit_success_amount = empty($submit_success_amount)?0:$submit_success_amount;
                $p_submit_success_price = empty($p_submit_success_price)?0:$p_submit_success_price;

                $info['wait_count'] = $p_wait_count+$p_faile_count;
                $info['wait_amount'] = $p_wait_amount+$p_faile_amount;
                $info['wait_price'] = $p_wait_price+$p_faile_price;

                $info['submit_success_count'] = $submit_success_count;
                $info['submit_success_amount'] = $submit_success_amount;
                $info['submit_success_price'] = $p_submit_success_price;
            }

            return show_sucess('获取用户信息',$info);
    }

    public function showrecharge_record($operator = -1,$placeid = -1,$start_time="",$end_time="")
    {
        if(empty($start_time)||empty($end_time))
        {
            $start_time =  date("Y-m-d").' 00:00:00';
            $end_time =    date("Y-m-d").' 23:59:59';
        }
        
       $data = session("enterprise_key");
        if ($data != null) {
            $strArray = localdecode($data);
            $InfoArray = explode(",",$strArray);
            $user_id = $InfoArray[0];
        }else{
            $this->display('Public:404');
            //  $user_id = 163;
        }
       
        $filterInfoString = json_encode($this->getFilterInfo());

        $recordStatData = $this->getRecordRecordStat($operator,$placeid,$start_time,$end_time,$user_id);
        

        $recordStatString = json_encode($recordStatData);

        $this->assign("filterInfo",$filterInfoString);
        $this->assign("recordStat",$recordStatString);
        $this->assign("operator",$operator);
        $this->assign("placeid",$placeid);
        $this->assign("start_time",$start_time);
        $this->assign("end_time",$end_time);
        $this->display("recharge_record/index");
    }

    public function show_account_manage()
    {
        $data = session("enterprise_key");
        if ($data != null) {
            $strArray = localdecode($data);
            $InfoArray = explode(",",$strArray);
            $user_id = $InfoArray[0];
        }else{
            $this->display('Public:404');
        }

        $user_info = json_encode(D("SysUser")->get_user_info($user_id));//curlFunction($_SERVER['HTTP_HOST']."/WxHome/User/get_user_info",array());
        $user_info_data_array = json_decode($user_info,true);
        $user_info_data = $user_info_data_array['info'];
        // 登录账号
        $this->assign('login_name_full',$user_info_data['login_name_full']);
        // 企业名
        $this->assign('enterprise_name',$user_info_data['enterprise_name']);
        // 账户余额
        $this->assign('account_balance',$user_info_data['account_balance']);
         // 累计存款
        $this->assign('deposit_sum',$user_info_data['deposit_sum']);
        // 累计消费
        $this->assign('consum_sum',$user_info_data['consum_sum']);
         // 冻结余额
        $this->assign('freeze_money',$user_info_data['freeze_money']);
        $this->display('user_info/index');
    }
}
