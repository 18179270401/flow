<?php
namespace OutInterfaces\Controller;
use Think\Controller;

class OrderController extends CommonController {

    public function getRecordRecordStat(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
            'info' => new \stdClass()
        );
        $token = trim(I('post.token'));
        $start_date = trim(I('post.start_date'));
        $end_date = trim(I('post.end_date'));
        $json_filter = trim(I('post.json_filter'));
        $json_filter = htmlspecialchars_decode($json_filter);
        $json_filter = json_decode($json_filter,true);
        $operator_id = $json_filter[C('FILTER_OPERATOR_ID')];
        $province_id = $json_filter[C('FILTER_PROVINCE_ID')];
        $channel_id = $json_filter[C('FILTER_CHANNEL_ID')];
        $bc_channel_id = $json_filter[C('FILTER_BC_CHANNEL_ID')];
        $order_status = $json_filter[C('FILTER_ORDER_STATUS')];

        $new_m = date('Ym',strtotime("-3 month"));     //历史最新月份

        if(empty($token) || empty($start_date) || empty($end_date)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }
        $start_date = date('Y-m-d 00:00:00',strtotime($start_date));
        $end_date = date('Y-m-d 23:59:59',strtotime($end_date));
        $the_m = 2;     //数据表
        $start_m = date('Ym',strtotime($start_date));
        $end_m = date('Ym',strtotime($end_date));
        if( $start_m >= $new_m ){
            $the_m = 2;
        }else{
            $the_m = $start_m;
            if($start_m != $end_m){
                $result['ret'] = '301';
                $result['msg'] = '开始时间与结束时间必须在同一月份';
                return return_tidy_result($result);
            }
        }

        $user_info = array();
        $is_right = $this->is_token_right($token,$result,$user_info);
        $user_id = $user_info['user_id'];

        if($is_right){
            $operator_id = !empty($operator_id) ? $operator_id : -1 ;  //运营商
            $upper_role = $this->upper_role($user_id);
            if($upper_role['in']){
                $channel_id =!empty($upper_role['channel_id']) ? $upper_role['channel_id'] : '0' ; //通道
                $bc_channel_id =!empty($upper_role['channel_id']) ? '' : '0'; //备用通道
            }else{
                $channel_id = !empty($channel_id) ? $channel_id : '' ; //通道
                $bc_channel_id = !empty($bc_channel_id) ? $bc_channel_id : '' ; //备用通道
            }
            $province_id = !empty($province_id) ? $province_id : -1 ;  //省份
            $order_status = !empty($order_status) ? $order_status : '' ;  //订单状态


            $list = D('Order')->order_storing_process($the_m,$user_id,'','','','',$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_date,$end_date,'',1,-1);

            $stat = $list['count'];

            $info['success_count'] = empty($stat['@p_success_count'])?0:$stat['@p_success_count'];
            $info['success_price'] = empty($stat['@p_success_price'])?0:$stat['@p_success_price'];
            $info['success_amount'] = empty($stat['@p_success_amount'])?0:$stat['@p_success_amount'];

            $info['faile_count'] = empty($stat['@p_faile_count'])?0:$stat['@p_faile_count'];      //失败数
            $info['faile_price'] = empty($stat['@p_faile_price'])?0:$stat['@p_faile_price'];     //原价
            $info['faile_amount'] = empty($stat['@p_faile_amount'])?0:$stat['@p_faile_amount']; //折后价

            $info['submit_success_count'] = 0;
            $info['submit_success_amount'] = 0;
            $info['submit_success_price'] = 0;

            $info['wait_count'] = 0;
            $info['wait_amount'] = 0;
            $info['wait_price'] = 0;

            if( $the_m == 2 ){
                $list2 = D('Order')->order_storing_process(1,$user_id,'','','','',$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_date,$end_date,'',1,-1);

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



            $result['ret'] = '200';
            $result['msg'] = '操作成功';
            $result['info'] = $info;
        }

        return return_tidy_result($result);
    }

    /**
     * @return bool
     * 运营商列表
     */
    public function getOperatorList(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
            'info' => array()
        );
        $token = trim(I('post.token'));
        if(empty($token)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }

        $user_info = array();
        $is_right = $this->is_token_right($token,$result,$user_info);

        //判断除企业之外的代理商显示
        if($is_right){
            $info = D("Order")->operatorall();
            $result['ret'] = '200';
            $result['msg'] = '操作成功';
            $result['info'] = $info;
        }
        return return_tidy_result($result);
    }

    /**
     * @return bool
     * 省份列表
     */
    public function getProvinceList(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
            'info' => array()
        );
        $token = trim(I('post.token'));
        if(empty($token)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }

        $user_info = array();
        $is_right = $this->is_token_right($token,$result,$user_info);

        //判断除企业之外的代理商显示
        if($is_right){
            $info = D("ChannelProduct")->province_list();
            $result['ret'] = '200';
            $result['msg'] = '操作成功';
            $result['info'] = $info;
        }
        return return_tidy_result($result);
    }

    /**
     * @return bool
     * 订单状态信息
     */
    public function getOrderStatus(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
            'info' => array()
        );
        $token = trim(I('post.token'));
        if(empty($token)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }

        $user_info = array();
        $is_right = $this->is_token_right($token,$result,$user_info);

        //判断除企业之外的代理商显示
        if($is_right){
            $info = array(
                array('text' => "充值成功", 'order_status' => '2'),
                array('text' => "充值失败", 'order_status' =>  '3'),
                array('text' => "充值成功(备)", 'order_status' =>  '5'),
                array('text' => "充值失败(备)", 'order_status' =>  '6')
            );
            $result['ret'] = '200';
            $result['msg'] = '操作成功';
            $result['info'] = $info;
        }
        return return_tidy_result($result);
    }

    /**
     * @return bool
     * 获取通道列表
     */
    public function getChannelList(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
            'info' => array()
        );
        $token = trim(I('post.token'));
        if(empty($token)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }

        $user_info = array();
        $is_right = $this->is_token_right($token,$result,$user_info);

        //判断除企业之外的代理商显示
        if($is_right){
            $info = D("Order")->channelall(1);
            $result['ret'] = '200';
            $result['msg'] = '操作成功';
            $result['info'] = $info;
        }
        return return_tidy_result($result);
    }

    /**
     * @return bool
     * 饼图
     */
    public function getCakeData(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
            'info' => new \stdClass()
        );
        $token = trim(I('post.token'));
        $beginDate = trim(I('post.start_date'));
        $endDate = trim(I('post.end_date'));
        if(empty($token) || empty($beginDate) || empty($endDate)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }

        $user_info = array();
        $is_right = $this->is_token_right($token,$result,$user_info);
        $company_id = 1;
        $p_user_type = -1;
        $user_type = $user_info['user_type'];
        switch ($user_type){
            case 1:
                $p_user_type = -1;
                $company_id = 1;
                break;
            case 2:
                $p_user_type = 1;
                $company_id = $user_info['proxy_id'];
                break;
            case 3:
                $p_user_type = 2;
                $company_id = $user_info['enterprise_id'];
        }

        //判断除企业之外的代理商显示
        if($is_right){

            $info_operator = D("ChannelProduct")->operatorall();
            $p_pic_id=0; //0表示饼图
            $order_flow = M()->query("CALL p_get_stat_order_home(".$p_user_type.",".$p_pic_id.",'".$beginDate."','".$endDate."',0,".$company_id.");");
            $operator_name = array();
            $order_list = array();
            foreach ($info_operator as $k=>$v) {
                $i = 1;
                $operator_name[$k] = $v['operator_name'];
                $order_list[$k]['name'] = $v['operator_name'];
                foreach ($order_flow as $vo){
                    if($vo['operator_id'] == $v['operator_name']) {
                        $order_list[$k]['value'] = empty($vo['stat_count']) ? 0:$vo['stat_count'] ;
                        $i = 2;
                        break;
                    }
                }
                if($i==1){
                    $order_list[$k]['value'] = 0;
                }
            }
            $info['order_name'] = $operator_name;
            $app_types = getallheaders();
            $app_type = $app_types['AppType'];
            if($app_type == 'TPOS'){
                $info['order_size'] = $order_list;
            }else{
                $info['order_count'] = $order_list;
            }


            $result['ret'] = '200';
            $result['msg'] = '操作成功';
            $result['info'] = $info;
        }
        return return_tidy_result($result);
    }

    /**
     * @return bool
     * 流量充值曲线图
     */
    public function getLineData(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
            'info' => new \stdClass()
        );
        $token = trim(I('post.token'));
        $beginDate = trim(I('post.start_date'));
        $endDate = trim(I('post.end_date'));


        if(empty($token) || empty($beginDate) || empty($endDate)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }

        $user_info = array();
        $is_right = $this->is_token_right($token,$result,$user_info);
        $company_id = 1;
        $p_user_type = -1;
        $user_type = $user_info['user_type'];
        switch ($user_type){
            case 1:
                $p_user_type = -1;
                $company_id = 1;
                break;
            case 2:
                $p_user_type = 1;
                $company_id = $user_info['proxy_id'];
                break;
            case 3:
                $p_user_type = 2;
                $company_id = $user_info['enterprise_id'];
        }

        //判断除企业之外的代理商显示
        if($is_right){
            $info_operator = D("ChannelProduct")->operatorall();
            $p_pic_id=1; //0表示饼图

            $order_flow = M()->query("CALL p_get_stat_order_home(".$p_user_type.",".$p_pic_id.",'".$beginDate."','".$endDate."',0,".$company_id.");");
            $operator_name = array();
            $order_list = array();
            foreach ($info_operator as $k=>$v) {
                $i = 1;
                $operator_name[$k] = $v['operator_name'];
                $order_list[$k]['name'] = $v['operator_name'];
                foreach ($order_flow as $vo){
                    if($vo['operator_id'] == $v['operator_name']) {
                        $order_list[$k]['value'] = empty($vo['stat_size']) ? 0:$vo['stat_size'] ;
                        $i = 2;
                        break;
                    }
                }
                if($i==1){
                    $order_list[$k]['value'] = 0;
                }
            }
            $info['order_name'] = $operator_name;
            $info['order_size'] = $order_list;

            $result['ret'] = '200';
            $result['msg'] = '操作成功';
            $result['info'] = $info;
        }
        return return_tidy_result($result);
    }

    /**
     * @return bool
     * 柱状图
     */
    public function getPoleData(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
            'info' => n
        );
        $token = trim(I('post.token'));
        $beginDate = trim(I('post.start_date'));
        $endDate = trim(I('post.end_date'));
        $operator = trim(I('post.operator_id'));
        if(empty($token) || empty($beginDate) || empty($endDate) || empty($operator)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }

        $operator = empty($operator)?1:$operator;

        $user_info = array();
        $is_right = $this->is_token_right($token,$result,$user_info);
        $company_id = 1;
        $p_user_type = -1;
        $user_type = $user_info['user_type'];
        switch ($user_type){
            case 1:
                $p_user_type = -1;
                $company_id = 1;
                break;
            case 2:
                $p_user_type = 1;
                $company_id = $user_info['proxy_id'];
                break;
            case 3:
                $p_user_type = 2;
                $company_id = $user_info['enterprise_id'];
        }

        $province = array();
        $order_count = array();
        //判断除企业之外的代理商显示
        if($is_right){

            $p_pic_id=2;
            $order_flow = M()->query("CALL p_get_stat_order_home(".$p_user_type.",".$p_pic_id.",'".$beginDate."','".$endDate."',$operator,".$company_id.");");
            if($order_flow) {
                foreach ($order_flow as $v) {
                    $province[] = $v['province_name'];
                    $order_count[] = $v['stat_count'] > 0 ? $v['stat_count'] : 0;
                }
            }

            $count = count($order_count);
            for($i=0;$i<$count;$i++){
                for($j=$i;$j<$count;$j++){
                    if($order_count[$i] < $order_count[$j]){
                        $temp = $order_count[$i];
                        $order_count[$i] = $order_count[$j];
                        $order_count[$j] = $temp;

                        $temp2 = $province[$i];
                        $province[$i] = $province[$j];
                        $province[$j] = $temp2;
                    }
                }
            }

            $info['order_name'] = $province;
            $app_types = getallheaders();
            $app_type = $app_types['AppType'];
            if($app_type == 'TPOS'){
                $info['order_size'] = $order_count;
            }else{
                $info['order_count'] = $order_count;
            }


            $result['ret'] = '200';
            $result['msg'] = '操作成功';
            $result['info'] = $info;
        }
        return return_tidy_result($result);
    }



}