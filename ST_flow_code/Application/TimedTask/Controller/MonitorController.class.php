<?php
namespace TimedTask\Controller;
use Think\Controller;

class MonitorController extends Controller {
    /**
     * 监控接口：登录
     * userName 用户名
     * passWord 密码
     */
    public function MonitorLogin() {
        $result = array(
            'success' => '-1',
            'token' => '',
            'retCode' => '-100',
        );

        $login_date = date('Y-m-d H:i:s');
        $userName = trim(I('post.userName'));
        $passWord = trim(I('post.passWord'));

        $map['login_name_full'] = array('eq',$userName);
        $map['login_pass'] = array('eq',$passWord);
        $map['status'] = array('eq',1);
        $user = M('sys_user')->where($map)->find();

        if(empty($user)) {
            write_error_log(array(__METHOD__.'：'.__LINE__, 'login_date:', $login_date),'_Monitor');
            write_error_log(array(__METHOD__.'：'.__LINE__, 'userName:', $userName),'_Monitor');
            write_error_log(array(__METHOD__.'：'.__LINE__, 'passWord:', $passWord),'_Monitor');
            $result['retCode'] = '-99';   //密码或用户名错误
        }elseif($user['status'] == '2'){
            write_error_log(array(__METHOD__.'：'.__LINE__, 'login_date:', $login_date),'_Monitor');
            write_error_log(array(__METHOD__.'：'.__LINE__, 'userName:', $userName),'_Monitor');
            write_error_log(array(__METHOD__.'：'.__LINE__, 'passWord:', $passWord),'_Monitor');
            $result['retCode'] = '-100';   //该用户已删除！
        }elseif($user['status'] == '0'){
            write_error_log(array(__METHOD__.'：'.__LINE__, 'login_date:', $login_date),'_Monitor');
            write_error_log(array(__METHOD__.'：'.__LINE__, 'userName:', $userName),'_Monitor');
            write_error_log(array(__METHOD__.'：'.__LINE__, 'passWord:', $passWord),'_Monitor');
            $result['retCode'] = '-100';  //该用户已被禁用
        }elseif($user['user_type'] != 1){
            write_error_log(array(__METHOD__.'：'.__LINE__, 'login_date:', $login_date),'_Monitor');
            write_error_log(array(__METHOD__.'：'.__LINE__, 'userName:', $userName),'_Monitor');
            write_error_log(array(__METHOD__.'：'.__LINE__, 'passWord:', $passWord),'_Monitor');
            $result['retCode'] = '-100';
        }else{
            //判断是否在线人数已满
            $login_sum_map = array(
                'monitor_login_type' => 1,
                'user_type' => 1,
                'status' => 1
            );
            $login_sum = M('sys_user')->where($login_sum_map)->count('user_id');
            $login_max = C('MONITOR_MAX_LOGIN');
            if($user['monitor_login_type'] == 1){
                $login_sum --;
            }

            if($login_sum >= $login_max){
                write_error_log(array(__METHOD__.'：'.__LINE__, 'login_date:', $login_date),'_Monitor');
                write_error_log(array(__METHOD__.'：'.__LINE__, 'userName:', $userName),'_Monitor');
                write_error_log(array(__METHOD__.'：'.__LINE__, 'passWord:', $passWord),'_Monitor');
                $result['retCode'] = '-98';   //已达到最大登录数
                echo json_encode($result);exit();
            }
            //登录日志
            $login_log = array(
                'ip_addr' => get_client_ip2(),
                'login_user_id' => $user['user_id'],
                'login_user_name' => $user['user_name'],
                'login_name_full' => $user['login_name_full'],
                'login_date' => $login_date,
                'login_type' => 4,
            );
            $log_result = M('SysLoginLog')->add($login_log);
            $login_map = array('user_id'=>$user['user_id']);
            $login_save = array(
                'monitor_login_type' => 1,   //1,上线；0，下线
                'modify_user_id' => $user['user_id'],
                'modify_date' => date('Y-m-d H:i:s')
            );
            $login_type_result = M('sys_user')->where($login_map)->save($login_save);

            if($log_result && $login_type_result !== false){
                $result['success'] = '0';
                $login_date2 = substr($login_date,0,10);    //当天有效
                $token = base64_encode(urlencode(md5($userName.$login_date2.$passWord).$login_date.$userName));
                $result['token'] = $token;
                $result['retCode'] = '0';
            }
        }
        //$result = $this->array_value_to_string($result);
        echo json_encode($result);exit();
    }

    /**
     * 通道总体信息预览
     * token    访问令牌
     */
    public function MonitorAll() {
        $result = array(
            'success' => '-1',
            'content' => '',
            'retCode' => '-100',
        );
        $token = trim(I('post.token'));
        $province_id = trim(I('post.province'));
        $operator_id = trim(I('post.sp'));
        $channel_id = trim(I('post.mainChan'));

        $province_id = empty($province_id)?-1:$province_id;
        $operator_id = empty($operator_id)?-1:$operator_id;
        $channel_id = empty($channel_id)?-1:$channel_id;

        $is_right = $this->is_token_right($token,$result['retCode']);
        if($is_right){
            $dateTime = date('Ymd');
            $contents = array();
            $statList = M()->query("CALL p_monitor_get_order_channel($dateTime,$dateTime,$channel_id,$operator_id,$province_id);");
            $contents['totalChargeSucNum'] = 0;
            $contents['totalChargeSucMon'] = 0;
            $contents['totalChargeFaiNum'] = 0;
            $contents['totalChargeFaiMon'] = 0;
            $contents['chans'] = array();

            foreach($statList as $v){
                $content = array();
                $content['cid'] = $v['channel_id'];
                $content['chanNo'] = $v['channel_code'];
                $content['chanName'] = $v['channel_name'];
                $content['waitComNum'] = $v['wait_count'];
                $content['waitComMon'] = sprintf("%.2f", round($v['wait_amount'] ,2));
                $content['commitedNum'] = $v['submit_success_count'];
                $content['commitedMon'] = sprintf("%.2f", round($v['submit_success_amount'] ,2));
                $content['chargeSucNum'] = $v['success_count'];
                $content['chargeSucMon'] = sprintf("%.2f", round($v['success_amount'] ,2));
                $content['chargeFaiNum'] = $v['faile_count'];
                $content['chargeFaiMon'] = sprintf("%.2f", round($v['faile_amount'] ,2));
                $contents['chans'][] = $content;

                $contents['totalChargeSucNum'] += $v['success_count'];
                $contents['totalChargeSucMon'] += $v['success_amount'];
                $contents['totalChargeFaiNum'] += $v['faile_count'];
                $contents['totalChargeFaiMon'] += $v['faile_amount'];
            }
            $result['success'] = '0';
            $result['retCode'] = '0';
            $result['content'] = $contents;
        }
        $this->array_value_to_string($result);
        echo json_encode($result);exit();
    }

    /**
     * 全国省份信息监控表
     * token    访问令牌
     */
    public function MonitorAllProvince() {
        $result = array(
            'success' => '-1',
            'content' => '',
            'retCode' => '-100',
        );
        $token = trim(I('post.token'));
        $is_right = $this->is_token_right($token,$result['retCode']);
        if($is_right){
            $dateTime = date('Ymd');
            $contents = array();
            $statList = M()->query("CALL p_monitor_get_order_province($dateTime,$dateTime);");
            $contents['totalChargeSucNum'] = 0;
            $contents['totalChargeSucMon'] = 0;
            $contents['totalChargeFaiNum'] = 0;
            $contents['totalChargeFaiMon'] = 0;
            $contents['provinces'] = array();

            foreach($statList as $v){
                $content = array();
                $content['rank'] = $v['rank'];
                $content['province'] = $v['province_name'];
                $content['waitComNum'] = $v['wait_count'];
                $content['waitComMon'] = sprintf("%.2f", round($v['wait_amount'] ,2));
                $content['commitedNum'] = $v['submit_success_count'];
                $content['commitedMon'] = sprintf("%.2f", round($v['submit_success_amount'] ,2));
                $content['chargeSucNum'] = $v['success_count'];
                $content['chargeSucMon'] = sprintf("%.2f", round($v['success_amount'] ,2));
                $content['chargeFaiNum'] = $v['faile_count'];
                $content['chargeFaiMon'] = sprintf("%.2f", round($v['faile_amount'] ,2));
                $contents['provinces'][] = $content;

                $contents['totalChargeSucNum'] += $v['success_count'];
                $contents['totalChargeSucMon'] += $v['success_amount'];
                $contents['totalChargeFaiNum'] += $v['faile_count'];
                $contents['totalChargeFaiMon'] += $v['faile_amount'];
            }
            $result['success'] = '0';
            $result['retCode'] = '0';
            $result['content'] = $contents;
        }
        $this->array_value_to_string($result);
        echo json_encode($result);exit();
    }

    /**
     * 指定通道情况
     * token    访问令牌
     * cname    通道名称
     */
    public function MonitorOne() {
        $result = array(
            'success' => '-1',
            'content' => '',
            'retCode' => '-100',
        );
        $token = trim(I('post.token'));
        $cid = trim(I('post.cid'));
        $is_right = $this->is_token_right($token,$result['retCode']);
        if($is_right && !empty($cid)){
            $unFinished = array(
                'waitComNum' => 0,
                'waitComMon' => 0,
                'laestTen' => array()
            );

            $map_last['_string'] = " (o.back_channel_id=$cid and o.order_status in(3,4)) or (o.channel_id=$cid and o.order_status in(0,1)) ";

            $map_last['o.order_date'] = array('between',array(date('Y-m-d 00:00:00'),date('Y-m-d 23:59:59')));
            $join_last = array(
                C('DB_PREFIX').'channel as c on c.channel_id=o.channel_id',
                C('DB_PREFIX').'channel_product as p on p.product_id=o.channel_product_id'
            );

            $unFinishedState = M('OrderPre as o')
                ->where($map_last)
                ->field('count(order_id) as nums,
                sum(discount_price) as money')
                ->find();

            $unFinished['waitComNum'] = empty($unFinishedState['nums'])?0:$unFinishedState['nums'];
            $unFinished['waitComMon'] = empty($unFinishedState['money'])?0:$unFinishedState['money'];

            $unFinished['waitComMon'] =sprintf("%.2f", round($unFinished['waitComMon'] ,2));

            $lastTen = M('OrderPre as o')
                ->join($join_last,'left')
                ->join(C('DB_PREFIX').'sys_mobile_dict as md on o.mobile=md.mobile')
                ->where($map_last)
                ->field('o.mobile,o.order_status,
                o.order_date,o.operator_id,
                p.product_name,
                o.price,
                o.discount_price,
                CONCAT(md.province_name,md.city_name) as owned')
                ->order('order_id desc')
                ->limit(10)
                ->select();

            foreach($lastTen as $v){
                $temp = array();
                $temp['phone'] = $v['mobile'];
                $temp['state'] = $v['order_status'];
                $temp['commitTime'] = substr($v['order_date'],0,19);
                $temp['sp'] = $v['operator_id'];
                $temp['product'] = $v['product_name'];
                $temp['price'] = $v['price'];
                $temp['discountPrice'] = $v['discount_price'];
                $temp['owned'] = $v['owned'];
                $unFinished['laestTen'][] = $temp;
            }

            $start_time = date('Ymd00');
            $end_time = date('YmdH');
            $failByHour_where['stat_time'] = array('between',array($start_time,$end_time));
            $failByHour_where['channel_id'] = $cid;
            $failByHours = M('monitor_channel_stat')
                ->field('stat_time,total_count as sum_total_count,faile_count as sum_fail_count')
                ->where($failByHour_where)
                ->order('stat_time asc')
                ->select();

            foreach($failByHours as $value){
                $temp = array();
                $temp['hour'] = (int)substr($value['stat_time'],-2);
                $temp['total'] = (int)$value['sum_total_count'];
                $temp['failPer'] = '-';
                if(!empty($value['sum_total_count'])){
                    $temp['failPer'] = $value['sum_fail_count']/$value['sum_total_count'];
                    $temp['failPer'] = round(($temp['failPer']*100) ,2);
                    $temp['failPer'] .= '%';
                }

                $unFinished['failByHour'][] = $temp;
            }

            $result['success'] = '0';
            $result['retCode'] = '0';
            $result['content'] = $unFinished;
        }
        $this->array_value_to_string($result);
        echo json_encode($result);exit();
    }

    /**
     * 通道异常情况
     * token    访问令牌
     */
    public function MonitorAllByHour() {
        $result = array(
            'success' => '-1',
            'content' => '',
            'retCode' => '-100',
        );
        $token = trim(I('post.token'));
        $is_right = $this->is_token_right($token,$result['retCode']);
        if($is_right){
            $dateTime1 = date('YmdH',strtotime('-1 hour'));
            $dateTime2 = date('YmdH',strtotime('-2 hour'));
            $contents = array();
            $statList = M()->query("CALL p_monitor_get_channel_exception($dateTime2,$dateTime1);");
            $contents['chans'] = array();

            $temp = array();
            foreach($statList as $v){
                $content = array();

                $channel_id = $v['channel_id'];
                if(!array_key_exists($channel_id, $temp)){
                    $content['cid'] = $v['channel_id'];
                    $content['chanNo'] = $v['channel_code'];
                    $content['chanName'] = $v['channel_name'];
                }

                if($dateTime1 == $v['stat_time']){
                    $content['oneHourSuc'] = $v['success_count'];
                    $content['oneHourFai'] = $v['faile_count'];
                    $content['oneHourFaiPer'] = $v['faile_rate'];
                }elseif($dateTime2 == $v['stat_time']){
                    $content['twoHourSuc'] = $v['success_count'];
                    $content['twoHourFai'] = $v['faile_count'];
                    $content['twoHourFaiPer'] = $v['faile_rate'];
                }

                if(array_key_exists($channel_id, $temp)){
                    $temp[$channel_id] = array_merge($temp[$channel_id],$content);
                }else{
                    $temp[$channel_id] = $content;
                }
            }

            foreach($temp as $v){
                if(!array_key_exists('oneHourSuc', $v)){
                    $v['oneHourSuc'] = 0;
                    $v['oneHourFai'] = 0;
                    $v['oneHourFaiPer'] = 0;
                }
                if(!array_key_exists('twoHourSuc', $v)){
                    $v['twoHourSuc'] = 0;
                    $v['twoHourFai'] = 0;
                    $v['twoHourFaiPer'] = 0;
                }
                $contents['chans'][] = $v;
            }
            $result['success'] = '0';
            $result['retCode'] = '0';
            $result['content'] = $contents;
        }
        $this->array_value_to_string($result);
        echo json_encode($result);exit();
    }

    public function MonitorExit(){
        $result = array(
            'success' => '-1',
            'content' => '',
            'retCode' => '-100',
        );
        $token = trim(I('post.token'));
        $is_right = $this->is_token_right($token,$result['retCode']);
        if($is_right){
            $token_info = urldecode(base64_decode($token));
            $userName = substr($token_info,51);
            $map['login_name_full'] = array('eq',$userName);
            $map['status'] = array('eq',1);
            $login_save = array(
                'monitor_login_type' => 0,
                'modify_date' => date('Y-m-d H:i:s')
            ); //1,上线；0，下线
            $login_type_result = M('sys_user')->where($map)->save($login_save);
            if($login_type_result){
                $result['success'] = '0';
                $result['retCode'] = '0';
            }
        }
        echo json_encode($result);exit();
    }

    public function MonitorVersion(){
        $result = array(
            'success' => '0',
            'content' => C('MONITOR_LAST_VISION'),
            'retCode' => 0
        );

        $this->array_value_to_string($result);
        echo json_encode($result);exit();
    }

    public function MonitorDownload(){
        $file_path = C('MONITOR_DOWNLOAD_PACKAGE');
        $this->download_contract('.' . $file_path);
    }

    private function is_token_right($token,&$retCode){
        if(empty($token)){
            $retCode = '-100';
            write_error_log(array(__METHOD__.'：'.__LINE__, 'token:', $token),'_Monitor');
            return false;
        }
        $token_info = urldecode(base64_decode($token));
        $userName = substr($token_info,51);
        $tokenMd5 = substr($token_info,0,32);
        $map['u.login_name_full'] = array('eq',$userName);
        $map['u.status'] = array('eq',1);
        $map['u.monitor_login_type'] = array('eq',1);
        $user_info = M('Sys_user as u')
            ->join(C('DB_PREFIX')."sys_login_log as ll on ll.login_user_id=u.user_id and ll.login_type=4")
            ->field('u.login_pass,u.monitor_login_type,ll.login_date')
            ->order('ll.login_date desc')
            ->where($map)
            ->find();
        if(!empty($user_info)){
            $login_date = substr($user_info['login_date'],0,10);    //当天有效
            $token_date = substr($token_info,32,19);
            $login_date_time = substr($user_info['login_date'],0,19);
            if($login_date_time != $token_date){
                write_error_log(array(__METHOD__.'：'.__LINE__, 'token:', $token),'_Monitor');
                write_error_log(array(__METHOD__.'：'.__LINE__, 'userName:', $userName),'_Monitor');
                $retCode = '-97';       //重复登录
                return false;
            }
            $passWord = $user_info['login_pass'];
            $dbMd5 = md5($userName.$login_date.$passWord);
            if($dbMd5 == $tokenMd5){
                return true;
            }else{
                $login_date = date('Y-m-d',strtotime($login_date.' -1 day'));
                $dbMd5 = md5($userName.$login_date.$passWord);
                if($dbMd5 == $tokenMd5){
                    write_error_log(array(__METHOD__.'：'.__LINE__, 'token:', $token),'_Monitor');
                    write_error_log(array(__METHOD__.'：'.__LINE__, 'userName:', $userName),'_Monitor');
                    return true;
                }
            }
        }
        write_error_log(array(__METHOD__.'：'.__LINE__, 'token:', $token),'_Monitor');
        write_error_log(array(__METHOD__.'：'.__LINE__, 'userName:', $userName),'_Monitor');
        return false;
    }

    private function array_value_to_string(&$array){
        if(!is_array($array)){
            $array = (string)$array;
        }else{
            foreach($array as &$v){
                $this->array_value_to_string($v);
            }
        }

    }

    function download_contract($filename){
        $filepath=$filename;
        $filename =basename($filename);
        $type=explode('.',$filename);
        $filesize = filesize($filepath);
        header("content-type:application/octet-stream");
        header("content-disposition:attachment;filename=".$type[0].'.'.$type[1]);
        header("content-length:{$filesize}");
        readfile($filepath);
    }

    public function MonitorInfo() {
        $result = array(
            'success' => '-1',
            'content' => '',
            'retCode' => '-100',
        );
        $token = trim(I('post.token'));

        $is_right = $this->is_token_right($token,$result['retCode']);
        if($is_right){
            $contents = M('Channel')
                ->where("status=1")
                ->field("channel_id as cid,channel_name as chanName,channel_code as chanNo,
                fail_threshold as failthreshold,block_card_num as waitthreshold")
                ->select();

            foreach($contents as &$v){
                if(!empty($v['failthreshold'])){
                    $v['failThreshold'] = ($v['failthreshold'] * 100) . '%';
                    $v['waitThreshold'] = $v['waitthreshold'];
                }else{
                    $v['failThreshold'] = '';
                    $v['waitThreshold'] = '';
                }
                unset($v['failthreshold']);
                unset($v['waitthreshold']);
            }

            $result['success'] = '0';
            $result['retCode'] = '0';
            $result['content']['chans'] = $contents;
        }

        $this->array_value_to_string($result);
        echo json_encode($result);exit();
    }

    /**
     * 获取通道预警提醒人电话号码
     */
    public function MonitorMobile(){
        $result = array(
            'success' => '-1',
            'retCode' => '-100',
            'content' => '',
        );
        //读取通道预警人的电话号码
        $mobile = M('SysSet')->order("set_id asc")->field('early_warning_people')->find();
        if($mobile['early_warning_people']!=''){
            $result['success'] = '0';
            $result['retCode'] = '0';
            $result['content']['mobile'] = $mobile['early_warning_people'];
        }
        $this->array_value_to_string($result);
        echo json_encode($result);exit();
    }


}