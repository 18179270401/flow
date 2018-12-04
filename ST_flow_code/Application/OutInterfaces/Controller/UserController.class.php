<?php
namespace OutInterfaces\Controller;
use Think\Controller;

class UserController extends CommonController {
    /**
     * 对外接口：登录
     * userName 用户名
     * passWord 密码
     */
	public function Login() {
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
            'info' => new \stdClass()
        );

        $login_date = date('Y-m-d H:i:s');
		$userName = trim(I('userName'));
        $passWord = trim(I('passWord'));
        if(empty($userName) || empty($passWord)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }

        $map['user.login_name_full'] = array('eq',$userName);
        $map['user.login_pass'] = array('eq',$passWord);
        $map['user.status'] = array('eq',1);
        $user = M('sys_user as user')
            ->field('user.*,depart.depart_name,p.proxy_code,e.enterprise_code')
            ->where($map)
            ->join('t_flow_sys_depart as depart on depart.depart_id = user.depart_id','left')
            ->join('t_flow_proxy as p on p.proxy_id = user.proxy_id','left')
            ->join('t_flow_enterprise as e on e.enterprise_id = user.enterprise_id','left')
            ->find();

        if(empty($user) || $user['status'] == '0') {
            write_error_log(array(__METHOD__.'：'.__LINE__, 'login_date:', $login_date),'_OutInterfaces');
            write_error_log(array(__METHOD__.'：'.__LINE__, 'userName:', $userName),'_OutInterfaces');
            write_error_log(array(__METHOD__.'：'.__LINE__, 'passWord:', $passWord),'_OutInterfaces');
            $result['ret'] = '302';
            $result['msg'] = '用户名或密码错误';
        }elseif($user['user_type'] != 1){
            write_error_log(array(__METHOD__.'：'.__LINE__, 'login_date:', $login_date),'_OutInterfaces');
            write_error_log(array(__METHOD__.'：'.__LINE__, 'userName:', $userName),'_OutInterfaces');
            write_error_log(array(__METHOD__.'：'.__LINE__, 'passWord:', $passWord),'_OutInterfaces');
            $result['ret'] = '302';
            $result['msg'] = '只适用于尚通端';
        }else{
            $user_id = $user['user_id'];
            $role_rights = $this->have_app_login_rights($user_id, 'APP_ROLE_RIGHT');
            if(!$role_rights){
                write_error_log(array(__METHOD__.'：'.__LINE__, 'login_date:', $login_date),'_OutInterfaces');
                write_error_log(array(__METHOD__.'：'.__LINE__, 'userName:', $userName),'_OutInterfaces');
                write_error_log(array(__METHOD__.'：'.__LINE__, 'passWord:', $passWord),'_OutInterfaces');
                $result['ret'] = '301';
                $result['msg'] = '角色没有权限';
                return return_tidy_result($result);
            }
            $userInfo = array(
                'login_name_full' => $user['login_name_full'],  //登录账号
                'login_name' => $user['login_name'],                  //登录名
                'sex' => $user['sex'],                                  //性别：1：男，2：女
                'user_name' => $user['user_name'],                  //姓名
                'mobile' => $user['mobile'],                            //电话
                'email' => $user['email'],                              //邮箱
                'depart_name' => $user['depart_name'],                  //所属部门
                'posts' => $user['posts']                               //职务
            );
            if(!empty($user['proxy_id'])){
                $userInfo['login_code'] = $user['proxy_code'];
            }else{
                $userInfo['login_code'] = $user['enterprise_code'];
            }

            $cw_rights = $this->have_app_login_rights($user_id, 'APP_CW_RIGHT');    //判断财务权限
            if($cw_rights){
                $userInfo['cw_rights'] = "1";
            }else{
                $userInfo['cw_rights'] = "0";
            }
            //登录日志
            $login_log = array(
                'ip_addr' => get_client_ip2(),
                'login_user_id' => $user['user_id'],
                'login_user_name' => $user['user_name'],
                'login_name_full' => $user['login_name_full'],
                'login_date' => $login_date,
                'login_type' => 5,      //app端登录
            );
            $log_result = M('SysLoginLog')->add($login_log);
            $login_map = array('user_id'=>$user['user_id']);
            $login_save = array(
                'modify_user_id' => $user['user_id'],
                'modify_date' => date('Y-m-d H:i:s')
                );
            $login_type_result = M('sys_user')->where($login_map)->save($login_save);

            if($log_result && $login_type_result !== false){
                $token = token_encode($userName,$passWord,$login_date);
                $result['ret'] = '200';
                $result['msg'] = '登录成功';
                $result['info'] = array('token' => $token,'userInfo' => $userInfo);
            }
        }

        return return_tidy_result($result);
	}

    /**
     * 获取获取企业代理商数
     */
    public function GetMainInfo(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
            'info' => new \stdClass()
        );
        $token = trim(I('post.token'));
        if(empty($token)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }

        $user_info = array();
        $is_right = $this->is_token_right($token,$result,$user_info);

        //获取当前用户所属平台  返回类型:1=>尚通运营端,2=>代理商端,3=>企业端
        $user_type = $user_info['user_type'];
        $proxy_id = $user_info['proxy_id'];

        //当天开始时间和结束时间
        $beginToday = date("Y-m-d 00:00:00");
        $endToday = date("Y-m-d 23:59:59");
        //公共时间搜索条件为当天
        $where['create_date'] = array("between",array($beginToday,$endToday));
        $where['approve_status'] = $where2['approve_status'] = 1;   //读取审核成功的代理商
        $where['status'] = $where2['status'] = array("neq",2);   //读取审核成功的代理商
        //判断除企业之外的代理商显示
        $info = array();
        if($is_right){
            if($user_type == 2){
                $where['top_proxy_id'] = $where2['top_proxy_id'] = $proxy_id;
            }
            if($user_type == 1){
                $where['proxy_id'] = array("neq",1);
                $where2['proxy_id'] = array("neq",1);
            }
            //计算当天新增的代理商
            $info['proxy_same_day'] = M("proxy")->where($where)->count();
            //计算总共的代理商
            $info['proxy_total'] = M("proxy")->where($where2)->count();
            //计算当天新增的企业
            $info['enterprise_same_day'] = M("enterprise")->where($where)->count();
            //计算总共的企业
            $info['enterprise_total'] = M("enterprise")->where($where2)->count();

            $result['ret'] = '200';
            $result['msg'] = '操作成功';
            $result['info'] = $info;
        }

        return return_tidy_result($result);

    }

    /**
     * @return bool
     * 通道收入统计
     */
    public function IncomeSum(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
            'info' => new \stdClass()
        );
        $token = trim(I('post.token'));
        $beginDate = trim(I('post.beginDate'));
        $endDate = trim(I('post.endDate'));

        if(empty($token) || empty($beginDate) || empty($endDate)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }
        $beginDate = date('Y-m-d 00:00:00',strtotime($beginDate));
        $endDate = date('Y-m-d 23:59:59',strtotime($endDate));

        $user_info = array();
        $is_right = $this->is_token_right($token,$result,$user_info);

        //获取当前用户所属平台  返回类型:1=>尚通运营端,2=>代理商端,3=>企业端
        $user_type = $user_info['user_type'];
        $user_id = $user_info['user_id'];

        $info = array();
        if($is_right){
            if($user_type == 1){
                $map['rc.rpt_date'] = array('between', array($beginDate, $endDate));

                $upper_role = $this->upper_role($user_id);
                if($upper_role['in']){
                    $channel_id = !empty($upper_role['channel_id'])?$upper_role['channel_id']:'0';
                    $map['rc.channel_id'] = array('in',$channel_id);
                }

                //消费总额
                $info =  M('rpt_channel rc')
                    ->where($map)
                    ->field(
                        'SUM(rc.expense_sum) AS expense_sum_total,'.    //消费总额
                        'SUM(rc.profit_sum) AS profit_sum_total,'.      //利润总额
                        'SUM(rc.cost_sum) AS cost_sum_total,'.          //成本总额
                        'SUM(rc.rebate_sum) AS rebate_sum_total'        //应收返利
                    )->find();

                //综合毛利率
                $info['expense_sum_total'] = empty($info['expense_sum_total'])?0:$info['expense_sum_total'];
                $info['profit_sum_total'] = empty($info['profit_sum_total'])?0:$info['profit_sum_total'];
                $info['cost_sum_total'] = empty($info['cost_sum_total'])?0:$info['cost_sum_total'];
                $info['rebate_sum_total'] = empty($info['rebate_sum_total'])?0:$info['rebate_sum_total'];
                $info['profit_sum_total_all'] = round($info['profit_sum_total']/$info['expense_sum_total']*100, 2);
                $info['profit_sum_total_all'] .= '%';
            }

            $result['ret'] = '200';
            $result['msg'] = '操作成功';
            $result['info'] = $info;
        }

        return return_tidy_result($result);
    }

    /**
     * @return bool
     * 历史消费总额
     */
    public function IncomeList(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
            'info' => array()
        );
        $token = trim(I('post.token'));
        $beginDate = trim(I('post.beginDate'));
        $endDate = trim(I('post.endDate'));
        $app_types = getallheaders();
        $app_type = $app_types['AppType'];

        if(empty($token) || empty($beginDate) || empty($endDate)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }
        $beginDate = date('Y-m-d 00:00:00',strtotime($beginDate));
        $endDate = date('Y-m-d 23:59:59',strtotime($endDate));

        $user_info = array();
        $is_right = $this->is_token_right($token,$result,$user_info);

        //获取当前用户所属平台  返回类型:1=>尚通运营端,2=>代理商端,3=>企业端
        $user_type = $user_info['user_type'];
        $user_id = $user_info['user_id'];

        $info = array();
        if($is_right){
            if($user_type == 1){
                $map['rc.rpt_date'] = array('between', array($beginDate, $endDate));

                $upper_role = $this->upper_role($user_id);
                if($upper_role['in']){
                    $channel_id = !empty($upper_role['channel_id'])?$upper_role['channel_id']:'0';
                    $map['rc.channel_id'] = array('in',$channel_id);
                }

                $list = M('rpt_channel rc')
                    ->field("rc.`rpt_date`,SUM(rc.expense_sum) AS expense_sum_total,SUM(rc.cost_sum) AS cost_sum_total,SUM(rc.profit_sum) AS profit_sum_total,SUM(rc.rebate_sum) AS  rebate_sum_total")
                    ->where($map)
                    ->order("rpt_date")
                    ->group("rc.`rpt_date`")->select();
                //write_debug_log(array(__METHOD__.'：'.__LINE__, 'sql== '.M()->getLastSql()));
                if(!empty($list) && is_array($list)) {
                    foreach($list as $k => &$v) {

                        /**IOS那边字段
                        profit_sum_total －》替换 cost_sum_total的值
                        cost_sum_total －》替换rebate_sum_total 的值
                        rebate_sum_total －》替换profit_sum_total 的值


                        if($app_type == 'TPOS'){
                            $v['expense_sum_total'] = sprintf("%1.2f", floatval($v['expense_sum_total']));
                            $v['cost_sum_total'] = sprintf("%1.2f", $v['cost_sum_total']);
                            $v['profit_sum_total'] = sprintf("%1.2f", $v['profit_sum_total']);
                            $v['rebate_sum_total'] = sprintf("%1.2f", $v['rebate_sum_total']);
                            $v['profit_sum_total_all'] = (empty($v['expense_sum_total']) || empty($v['profit_sum_total'])) ? 0 : round($v['profit_sum_total']/$v['expense_sum_total']*100, 2);
                            $v['profit_sum_total_all'] = sprintf("%1.2f", $v['profit_sum_total_all']);
                            $v['profit_sum_total_all'] .= '%';
                        }else{
                            $temp1 = $v['rebate_sum_total'];
                            $temp2 = $v['cost_sum_total'];
                            $temp3 = $v['profit_sum_total'];
                            $v['expense_sum_total'] = sprintf("%1.2f", floatval($v['expense_sum_total']));
                            $v['cost_sum_total'] = sprintf("%1.2f", $temp1);
                            $v['profit_sum_total'] = sprintf("%1.2f", $temp2);
                            $v['rebate_sum_total'] = sprintf("%1.2f", $temp3);
                            $v['profit_sum_total_all'] = (empty($v['expense_sum_total']) || empty($v['profit_sum_total'])) ? 0 : round($temp3/$v['expense_sum_total']*100, 2);
                            $v['profit_sum_total_all'] = sprintf("%1.2f", $v['profit_sum_total_all']);
                            $v['profit_sum_total_all'] .= '%';
                        }
                         **/
                        $v['expense_sum_total'] = sprintf("%1.2f", floatval($v['expense_sum_total']));
                        $v['cost_sum_total'] = sprintf("%1.2f", $v['cost_sum_total']);
                        $v['profit_sum_total'] = sprintf("%1.2f", $v['profit_sum_total']);
                        $v['rebate_sum_total'] = sprintf("%1.2f", $v['rebate_sum_total']);
                        $v['profit_sum_total_all'] = (empty($v['expense_sum_total']) || empty($v['profit_sum_total'])) ? 0 : round($v['profit_sum_total']/$v['expense_sum_total']*100, 2);
                        $v['profit_sum_total_all'] = sprintf("%1.2f", $v['profit_sum_total_all']);
                        $v['profit_sum_total_all'] .= '%';
                    }
                }
                $info = $list;
            }

            $result['ret'] = '200';
            $result['msg'] = '操作成功';
            $result['info'] = $info;
        }
    
        return return_tidy_result($result);
    }

    /**
     * @return bool
     * 财务统计列表
     * type 1:通道；2代理；3直营
     */
    public function getStatList(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
            'info' => new \stdClass()
        );
        $type = trim(I('post.type'));   //1:通道；2代理；3直营

        if(empty($type)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }

        if($type == 1){
            return $this->getStatChannel();
        }elseif($type == 2){
            return $this->getStatProxy();
        }else{
            return $this->getStatEnterprise();
        }
    }

    /**
     * @return bool
     * 通道收入统计
     */
    public function getStatChannel(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
            'info' => new \stdClass()
        );
        $token = trim(I('post.token'));
        $beginDate = trim(I('post.beginDate'));
        $endDate = trim(I('post.endDate'));
        $pageIndex = trim(I('post.pageIndex'));
        $pageSize = trim(I('post.pageSize'));
        $code = trim(I('post.code'));
        $name = trim(I('post.name'));

        $pageIndex = empty($pageIndex)?1:($pageIndex);
        $pageSize = empty($pageSize)?10:$pageSize;
        $pageStart = ($pageIndex-1)*$pageSize;
        $has_next_page=0;//判断是否有下一页
        if(empty($token) || empty($beginDate) || empty($endDate)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }

        $user_info = array();
        $is_right = $this->is_token_right($token,$result,$user_info);

        //获取当前用户所属平台  返回类型:1=>尚通运营端,2=>代理商端,3=>企业端
        $user_type = $user_info['user_type'];
        $user_id = $user_info['user_id'];

        $cw_rights = $this->have_app_login_rights($user_id, 'APP_CW_RIGHT');
        if(empty($cw_rights)){
            $result['ret'] = '301';
            $result['msg'] = '没有财务权限';
            return return_tidy_result($result);
        }

        $info = array();

        if($is_right){
            if($user_type == 1){
                $map['rc.rpt_date'] = array('between', array($beginDate, $endDate));
                ($code != '') && $map['c.channel_code'] = array('like', "%{$code}%");
                ($name != '') && $map['c.channel_name'] = array('like', "%{$name}%");

                $upper_role = $this->upper_role($user_id);
                if($upper_role['in']){
                    $channel_id = !empty($upper_role['channel_id'])?$upper_role['channel_id']:'0';
                    $map['rc.channel_id'] = array('in',$channel_id);
                }
                //消费总额
                $info =  M('rpt_channel rc')
                    ->join("INNER JOIN ".C('DB_PREFIX')."channel c ON rc.channel_id = c.channel_id ")
                    ->where($map)
                    ->field(
                        'SUM(rc.expense_sum) AS expense_sum_total,'.    //消费总额
                        'SUM(rc.profit_sum) AS profit_sum_total,'.      //利润总额
                        'SUM(rc.cost_sum) AS cost_sum_total,'.          //成本总额
                        'SUM(rc.rebate_sum) AS rebate_sum_total'        //应收返利
                    )->find();

                //综合毛利率
                $info['expense_sum_total'] = empty($info['expense_sum_total'])?0:$info['expense_sum_total'];
                $info['profit_sum_total'] = empty($info['profit_sum_total'])?0:$info['profit_sum_total'];
                $info['cost_sum_total'] = empty($info['cost_sum_total'])?0:$info['cost_sum_total'];
                $info['rebate_sum_total'] = empty($info['rebate_sum_total'])?0:$info['rebate_sum_total'];
                $info['profit_sum_total_all'] = round($info['profit_sum_total']/$info['expense_sum_total']*100, 2);
                $info['profit_sum_total_all'] .= '%';

                $list = M('rpt_channel rc')
                    ->join("INNER JOIN ".C('DB_PREFIX')."channel c ON rc.channel_id = c.channel_id ")
                    ->field("c.`channel_id` as id,c.`channel_code` as code,c.`channel_name` as name,SUM(rc.expense_sum) AS expense_sum_total,SUM(rc.cost_sum) AS cost_sum_total,SUM(rc.profit_sum) AS profit_sum_total,SUM(rc.rebate_sum) AS rebate_sum_total")
                    ->where($map)
                    ->order("profit_sum_total desc")
                    ->limit($pageStart.','.($pageSize+1))
                    ->group("rc.`channel_id`")->select();

                if(!empty($list) && is_array($list)) {
                    $cut_limit = 0;
                    foreach($list as $k => &$v) {
                        $cut_limit++;
                        if($cut_limit > $pageSize){
                            $has_next_page = 1;
                            unset($list[$k]);
                            break;
                        }
                        $v['expense_sum_total'] = sprintf("%1.2f", floatval($v['expense_sum_total']));
                        $v['cost_sum_total'] = sprintf("%1.2f", $v['cost_sum_total']);
                        $v['profit_sum_total'] = sprintf("%1.2f", $v['profit_sum_total']);
                        $v['rebate_sum_total'] = sprintf("%1.2f", $v['rebate_sum_total']);
                        $v['profit_sum_total_all'] = (empty($v['expense_sum_total']) || empty($v['profit_sum_total'])) ? 0 : round($v['profit_sum_total']/$v['expense_sum_total']*100, 2);
                        $v['profit_sum_total_all'] = sprintf("%1.2f", $v['profit_sum_total_all']);
                        $v['profit_sum_total_all'] .= '%';
                    }
                }
                $info['list'] = $list;
            }

            $result['ret'] = '200';
            $result['msg'] = '操作成功';
            $result['has_next_page']=$has_next_page;
            $result['info'] = $info;
        }

        return return_tidy_result($result);
    }

    /**
     * @return bool
     * 代理收入统计
     */
    public function getStatProxy(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
            'info' => new \stdClass()
        );
        $token = trim(I('post.token'));
        $beginDate = trim(I('post.beginDate'));
        $endDate = trim(I('post.endDate'));
        $pageIndex = trim(I('post.pageIndex'));
        $pageSize = trim(I('post.pageSize'));
        $code = trim(I('post.code'));
        $name = trim(I('post.name'));
        //测试数据
        /*$token = "MmVjNGIyN2QzODE3ZGZhYTg2MDEzZjYyMzQwMjVjY2IyMDE2LTA4LTEwKzE3JTNBMzAlM0EwOWFkbWluJTQwMjAwMDA=";
        $beginDate = "2016-08-01";
        $endDate = "2016-08-11";
        $pageIndex = 1;
        $pageSize = 10;
        $code = trim(I('post.code'));
        $name = trim(I('post.name'));*/

        $pageIndex = empty($pageIndex)?1:($pageIndex);
        $pageSize = empty($pageSize)?10:$pageSize;
        $pageStart = ($pageIndex-1)*$pageSize;
        $has_next_page=0;//判断是否有下一页

        if(empty($token) || empty($beginDate) || empty($endDate)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }

        $user_info = array();
        $is_right = $this->is_token_right($token,$result,$user_info);

        //获取当前用户所属平台  返回类型:1=>尚通运营端,2=>代理商端,3=>企业端
        $user_type = $user_info['user_type'];

        $user_id = $user_info['user_id'];

        $cw_rights = $this->have_app_login_rights($user_id, 'APP_CW_RIGHT');
        if(empty($cw_rights)){
            $result['ret'] = '301';
            $result['msg'] = '没有财务权限';
            return return_tidy_result($result);
        }

        $info = array();
        if($is_right){
            if($user_type == 1){
                $map['rp.rpt_date'] = array('between', array($beginDate, $endDate));
                ($code != '') && $map['p.proxy_code'] = array('like', "%{$code}%");
                ($name != '') && $map['p.proxy_name'] = array('like', "%{$name}%");

                $map['rp.rpt_date']         = array('between', array($beginDate, $endDate));
                $map['p.proxy_level']       = array('eq', 1); //一级代理商
                $map['p.proxy_type']        = array('eq', 0); //代理商类型（0：普通代理商、1：自营代理商）
                $map['p.approve_status']    = array('eq', 1); //1：审核通过
                $map['p.status']            = array('eq', 1); //1：正常

                //消费总额
                $info =  M('rpt_proxy rp')
                    ->join("INNER JOIN ".C('DB_PREFIX')."proxy p ON rp.proxy_id = p.proxy_id ")
                    ->where($map)
                    ->field(
                        'SUM(rp.expense_sum) AS expense_sum_total,'.    //消费总额
                        'SUM(rp.profit_sum) AS profit_sum_total,'.      //利润总额
                        'SUM(rp.cost_sum) AS cost_sum_total,'.          //成本总额
                        'SUM(rp.rebate_sum) AS rebate_sum_total'        //应收返利
                    )->find();
                //综合毛利率
                $info['expense_sum_total'] = empty($info['expense_sum_total'])?0:$info['expense_sum_total'];
                $info['profit_sum_total'] = empty($info['profit_sum_total'])?0:$info['profit_sum_total'];
                $info['cost_sum_total'] = empty($info['cost_sum_total'])?0:$info['cost_sum_total'];
                $info['rebate_sum_total'] = empty($info['rebate_sum_total'])?0:$info['rebate_sum_total'];
                $info['profit_sum_total_all'] = round($info['profit_sum_total']/$info['expense_sum_total']*100, 2);
                $info['profit_sum_total_all'] .= '%';

                $list = $list = M('rpt_proxy rp')
                    ->join("INNER JOIN ".C('DB_PREFIX')."proxy p ON rp.proxy_id = p.proxy_id ")
                    ->field("p.`proxy_id` as id,p.`proxy_code` as code,p.`proxy_name` as name,SUM(rp.expense_sum) AS expense_sum_total,SUM(rp.cost_sum) AS cost_sum_total,SUM(rp.rebate_sum) AS rebate_sum_total,SUM(rp.profit_sum) AS profit_sum_total")
                    ->where($map)
                    ->order("profit_sum_total desc,proxy_code asc")
                    ->limit($pageStart.','.($pageSize+1))
                    ->group("rp.`proxy_id`")->select();
                if(!empty($list) && is_array($list)) {
                    $cut_limit = 0;
                    foreach($list as $k => &$v) {
                        $cut_limit++;
                        if($cut_limit > $pageSize){
                            $has_next_page = 1;
                            unset($list[$k]);
                            break;
                        }
                        $v['expense_sum_total'] = sprintf("%1.2f", floatval($v['expense_sum_total']));
                        $v['cost_sum_total'] = sprintf("%1.2f", $v['cost_sum_total']);
                        $v['profit_sum_total'] = sprintf("%1.2f", $v['profit_sum_total']);
                        $v['rebate_sum_total'] = sprintf("%1.2f", $v['rebate_sum_total']);
                        $v['profit_sum_total_all'] = (empty($v['expense_sum_total']) || empty($v['profit_sum_total'])) ? 0 : round($v['profit_sum_total']/$v['expense_sum_total']*100, 2);
                        $v['profit_sum_total_all'] = sprintf("%1.2f", $v['profit_sum_total_all']);
                        $v['profit_sum_total_all'] .= '%';
                    }
                }
                $info['list'] = $list;
            }

            $result['ret'] = '200';
            $result['msg'] = '操作成功';
            $result['has_next_page']=$has_next_page;
            $result['info'] = $info;
        }

        return return_tidy_result($result);
    }

    /**
     * @return bool
     * 直营企业收入统计
     */
    public function getStatEnterprise(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
            'info' => new \stdClass()
        );
        $token = trim(I('post.token'));
        $beginDate = trim(I('post.beginDate'));
        $endDate = trim(I('post.endDate'));
        $pageIndex = trim(I('post.pageIndex'));
        $pageSize = trim(I('post.pageSize'));
        $code = trim(I('post.code'));
        $name = trim(I('post.name'));
        $top_proxy_id = trim(I('post.topProxyId'));

        $pageIndex = empty($pageIndex)?1:($pageIndex);
        $pageSize = empty($pageSize)?10:$pageSize;
        $pageStart = ($pageIndex-1)*$pageSize;
        $has_next_page=0;//判断是否有下一页

        $top_proxy_id = empty($top_proxy_id)?-1:$top_proxy_id;
        $direct_enterprise_ids = D('Enterprise')->get_direct_enterprise_ids(); //获取所有直营代理商下面的企业IDs

        if(empty($token) || empty($beginDate) || empty($endDate)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }

        $user_info = array();
        $is_right = $this->is_token_right($token,$result,$user_info);

        //获取当前用户所属平台  返回类型:1=>尚通运营端,2=>代理商端,3=>企业端
        $user_type = $user_info['user_type'];

        $user_id = $user_info['user_id'];

        $cw_rights = $this->have_app_login_rights($user_id, 'APP_CW_RIGHT');
        if(empty($cw_rights)){
            $result['ret'] = '301';
            $result['msg'] = '没有财务权限';
            return return_tidy_result($result);
        }

        $info = array();
        if($is_right){
            if($user_type == 1 && !empty($direct_enterprise_ids) && is_array($direct_enterprise_ids)){
                $map['rde.rpt_date'] = array('between', array($beginDate, $endDate));
                ($code != '') && $map['e.enterprise_code'] = array('like', "%{$code}%");
                ($name != '') && $map['e.enterprise_name'] = array('like', "%{$name}%");

                $map['rde.enterprise_id'] = array('in', $direct_enterprise_ids);
                ($top_proxy_id > 0) && $map['e.top_proxy_id'] = $top_proxy_id;

                //消费总额
                $info =  M('rpt_direct_enterprise rde')
                    ->join("INNER JOIN ".C('DB_PREFIX')."enterprise e ON rde.enterprise_id = e.enterprise_id ")
                    ->join("INNER JOIN ".C('DB_PREFIX')."proxy p ON e.top_proxy_id = p.proxy_id ")
                    ->where($map)
                    ->field(
                        'SUM(rde.expense_sum) AS expense_sum_total,'.    //消费总额
                        'SUM(rde.profit_sum) AS profit_sum_total,'.      //利润总额
                        'SUM(rde.cost_sum) AS cost_sum_total,'.          //成本总额
                        'SUM(rde.rebate_sum) AS rebate_sum_total'        //应收返利
                    )->find();

                //综合毛利率
                $info['expense_sum_total'] = empty($info['expense_sum_total'])?0:$info['expense_sum_total'];
                $info['profit_sum_total'] = empty($info['profit_sum_total'])?0:$info['profit_sum_total'];
                $info['cost_sum_total'] = empty($info['cost_sum_total'])?0:$info['cost_sum_total'];
                $info['rebate_sum_total'] = empty($info['rebate_sum_total'])?0:$info['rebate_sum_total'];
                $info['profit_sum_total_all'] = round($info['profit_sum_total']/$info['expense_sum_total']*100, 2);
                $info['profit_sum_total_all'] .= '%';

                $list = M('rpt_direct_enterprise rde')
                    ->join("INNER JOIN ".C('DB_PREFIX')."enterprise e ON rde.enterprise_id = e.enterprise_id ")
                    ->join("INNER JOIN ".C('DB_PREFIX')."proxy p ON e.top_proxy_id = p.proxy_id ")
                    ->field("e.`enterprise_id` as id,e.`enterprise_code` as code,e.`enterprise_name` as name,p.`proxy_name` as top_proxy_name,SUM(rde.expense_sum) AS expense_sum_total,SUM(rde.cost_sum) AS cost_sum_total,SUM(rde.rebate_sum) AS rebate_sum_total,SUM(rde.profit_sum) AS profit_sum_total")
                    ->where($map)
                    ->order("profit_sum_total desc,enterprise_code asc")
                    ->limit($pageStart.','.($pageSize+1))
                    ->group("rde.`enterprise_id`")->select();


                if(!empty($list) && is_array($list)) {
                    $cut_limit = 0;
                    foreach($list as $k => &$v) {
                        $cut_limit++;
                        if($cut_limit > $pageSize){
                            $has_next_page = 1;
                            unset($list[$k]);
                            break;
                        }
                        $v['expense_sum_total'] = sprintf("%1.2f", floatval($v['expense_sum_total']));
                        $v['cost_sum_total'] = sprintf("%1.2f", $v['cost_sum_total']);
                        $v['profit_sum_total'] = sprintf("%1.2f", $v['profit_sum_total']);
                        $v['rebate_sum_total'] = sprintf("%1.2f", $v['rebate_sum_total']);
                        $v['profit_sum_total_all'] = (empty($v['expense_sum_total']) || empty($v['profit_sum_total'])) ? 0 : round($v['profit_sum_total']/$v['expense_sum_total']*100, 2);
                        $v['profit_sum_total_all'] = sprintf("%1.2f", $v['profit_sum_total_all']);
                        $v['profit_sum_total_all'] .= '%';
                    }
                }
                $info['list'] = $list;
            }

            $result['ret'] = '200';
            $result['msg'] = '操作成功';
            $result['has_next_page']=$has_next_page;
            $result['info'] = $info;
        }

        return return_tidy_result($result);
    }

    /**
     * @return bool
     * 直营企业代理商列表
     */
    public function getTopProxyList(){
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
            $info0 = array(
                "proxy_id"=>"0",
                "proxy_name"=>"全部"
            );
            $info = D('Proxy')->get_direct_enterprise();
//          $info = array_merge($info0,$info);
			array_unshift($info,$info0);
//			$info[] = $info0;
            $result['ret'] = '200';
            $result['msg'] = '操作成功';
            $result['info'] = $info;
        }
        return return_tidy_result($result);
    }

    /**
     * @return bool
     * 版本信息
     */
    public function getVersionInfo(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
        );
        $platform = trim(I('post.platform')); //平台类型，1位安卓，2位IOS
        if(empty($platform)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }
        $info = C('ANDROID_VERSION');
        if($platform == 2){
            $info = C('IOS_VERSION');
        }

        $result['ret'] = '200';
        $result['msg'] = '操作成功';
        $result['info'] = $info;


        return return_tidy_result($result);
    }

    /**
     * @return bool
     * 过滤信息
     */
    public function getFilterInfo(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
            'info' => new \stdClass()
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

            /**
			$order_state = array();
			$order_state['id'] = C('FILTER_ORDER_STATUS');
			$order_state['name'] = '订单状态';
			$order_state_filter = array(
			    array('name' => "全部", 'id' => '0'),
                array('name' => "充值成功", 'id' => '2'),
                array('name' => "充值失败", 'id' =>  '3'),
                array('name' => "充值成功(备)", 'id' =>  '5'),
                array('name' => "充值失败(备)", 'id' =>  '6')
            );
			$order_state["filters"] = $order_state_filter;
//			$filters[] = $order_state;
             **/
			$channel_list = D("Order")->channelall(1);
			
			$channel_first_filters['id'] = C('FILTER_CHANNEL_ID');
			$channel_first_filters['name'] = '主充值通道';
			$channel_first_filters_sub = array();
			$channel_first_filters_sub[] = $allData;
			for($i = 0;$i < count($channel_list);$i++)
			{
				$channel_list_info = $channel_list[$i];
				$channel_list_filter['id'] = $channel_list_info['channel_id'];
				$channel_list_filter['name'] = $channel_list_info['channel_code'];
				$channel_first_filters_sub[] = $channel_list_filter;
			}
			$channel_first_filters['filters'] = $channel_first_filters_sub;
			$filters[] = $channel_first_filters;
			$channel_first_filters['id'] = C('FILTER_BC_CHANNEL_ID');
			$channel_first_filters['name'] = '备充值通道';
			$filters[] = $channel_first_filters;
//          $info['channel_list'] =$channel_list;
			
			$info['filters'] = $filters;
            $result['ret'] = '200';
            $result['msg'] = '操作成功';
            $result['info'] = $info;
        }
        return return_tidy_result($result);
    }

    /**
     * @return bool
     * 修改密码
     */
    public function setPassword(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
        );

        $token = trim(I('post.token'));
        $old_password = I('post.oldPassword');
        $new_password = I('post.newPassword');

        if(empty($token) || empty($old_password) || empty($new_password) ){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }

        $user_info = array();
        $is_right = $this->is_token_right($token,$result,$user_info);
        $user_id = $user_info['user_id'];

        if($is_right){

            $map['user_id'] = array('eq',$user_id);
            $map['login_pass'] = array('eq',$old_password);
            $user = M('sys_user')->where($map)->find();
            if(empty($user)){
                $result['ret'] = '301';
                $result['msg'] = '旧密码错误';
                return return_tidy_result($result);
            }else{
                $map_edit = array('user_id' => $user_id);
                $edit = array(
                    'login_pass' => $new_password,
                    'modify_user_id' => $user_id,
                    'modify_date' => date("Y-m-d H:i:s")
                );
                $result_edit = M('sys_user')->where($map_edit)->save($edit);
                if($result_edit !== false){
                    $result['ret'] = '200';
                    $result['msg'] = '操作成功';
                }
            }
        }

        return return_tidy_result($result);
    }

    /**
     * @return bool
     * 用户设置
     */
    public function setUser(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
        );

        $token = trim(I('post.token'));
        $user_name = I('post.userName');
        $login_name = I('post.loginName');
        $sex = I('post.sex');
        $mobile = I('post.mobile');
        $email = I('post.email');

        if( empty($token) || empty($user_name) || empty($login_name) ){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }

        //1男;2女
        if(empty($sex) || !in_array($sex,array(1,2))){
            $result['ret'] = '301';
            $result['msg'] = '性别有误';
            return return_tidy_result($result);
        }

        if(empty($mobile) || !isTel($mobile)){
            $result['ret'] = '301';
            $result['msg'] = '电话有误';
            return return_tidy_result($result);
        }

        if(!empty($email) && !isEmail($email)){
            $result['ret'] = '301';
            $result['msg'] = '邮箱格式有误';
            return return_tidy_result($result);
        }

        $user_info = array();
        $is_right = $this->is_token_right($token,$result,$user_info);
        $user_id = $user_info['user_id'];

        if($is_right){
            $map['user.user_id'] = array('eq',$user_id);
            $user = M('sys_user as user')
                ->field('user.*,depart.depart_name,p.proxy_code,e.enterprise_code')
                ->where($map)
                ->join('t_flow_sys_depart as depart on depart.depart_id = user.depart_id','left')
                ->join('t_flow_proxy as p on p.proxy_id = user.proxy_id','left')
                ->join('t_flow_enterprise as e on e.enterprise_id = user.enterprise_id','left')
                ->find();

            $login_code = '';
            if(!empty($user['proxy_id'])){
                $login_code = $user['proxy_code'];
            }else{
                $login_code = $user['enterprise_code'];
            }

            $login_name_full = $login_name_full = $login_name.'@'.$login_code;
            $edit =array(
                'user_name' => $user_name,
                'login_name' => $login_name,
                'login_name_full' => $login_name_full,
                'mobile' => $mobile,
                'email' => $email,
                'sex' => $sex,
                'user_id' => $user_id,
                'modify_user_id' => $user_id,
                'modify_date' => date("Y-m-d H:i:s" )
            );
            $result_edit = M('Sys_user')->save($edit);
            if($result_edit !== false){
                $result['ret'] = '200';
                $result['msg'] = '操作成功';
            }
        }

        return return_tidy_result($result);
    }

    public function getUserInfo(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
            'info' => new \stdClass()
        );

        $token = trim(I('post.token'));
        if( empty($token)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }

        $user_info = array();
        $is_right = $this->is_token_right($token,$result,$user_info);
        $user_id = $user_info['user_id'];

        if($is_right){
            $map['user.user_id'] = array('eq',$user_id);
            $user = M('sys_user as user')
                ->field('user.*,depart.depart_name,p.proxy_code,e.enterprise_code')
                ->where($map)
                ->join('t_flow_sys_depart as depart on depart.depart_id = user.depart_id','left')
                ->join('t_flow_proxy as p on p.proxy_id = user.proxy_id','left')
                ->join('t_flow_enterprise as e on e.enterprise_id = user.enterprise_id','left')
                ->find();

            $userInfo = array(
                'login_name_full' => $user['login_name_full'],  //登录账号
                'login_name' => $user['login_name'],                  //登录名
                'sex' => $user['sex'],                                  //性别：1：男，2：女
                'user_name' => $user['user_name'],                  //姓名
                'mobile' => $user['mobile'],                            //电话
                'email' => $user['email'],                              //邮箱
                'depart_name' => $user['depart_name'],                  //所属部门
                'posts' => $user['posts']                               //职务
            );
            if(!empty($user['proxy_id'])){
                $userInfo['login_code'] = $user['proxy_code'];
            }else{
                $userInfo['login_code'] = $user['enterprise_code'];
            }

            $result['ret'] = '200';
            $result['msg'] = '操作成功';
            $result['info'] = $userInfo;
        }
        return return_tidy_result($result);
    }

    /**
     * @return bool
     * 更新设备的RegistrationId
     */
    public function updateRegistrationId(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误'
        );

        $token = trim(I('post.token'));
        $registration_id = trim(I('post.registration_id'));
        if( empty($token) || empty($registration_id)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }

        $user_info = array();
        $is_right = $this->is_token_right($token,$result,$user_info);
        $user_id = $user_info['user_id'];

        if($is_right){
            $map['user_id'] = array('eq',$user_id);
            $data = array(
                'registration_id' => $registration_id,
                'modify_user_id' => $user_id,
                'modify_date' => date("Y-m-d H:i:s" )
            );

            $saved = M('sys_user')
                ->where($map)
                ->save($data);
            if($saved !== false){
                $result['ret'] = '200';
                $result['msg'] = '操作成功';
            }
        }
        return return_tidy_result($result);
    }


    /**
     * @return bool
     * 获取菜单权限
     */
    public function getMenuRights(){
        $result = array(
            'ret' => '300',
            'msg' => '系统错误',
            'info' => new \stdClass()
        );
        $token = trim(I('post.token'));

        if(empty($token)){
            $result['ret'] = '301';
            $result['msg'] = '参数错误';
            return return_tidy_result($result);
        }

        $user_info = array();
        $is_right = $this->is_token_right($token,$result,$user_info);

        if($is_right){
            //获取当前用户所属平台  返回类型:1=>尚通运营端,2=>代理商端,3=>企业端
            //$user_type = $user_info['user_type'];
            $user_id = $user_info['user_id'];

            $cw_rights = $this->have_app_login_rights($user_id, 'APP_CW_RIGHT');

            $info = array();
            $info['cw_rights'] = ($cw_rights === false?0:1);

            $result['ret'] = '200';
            $result['msg'] = '操作成功';
            $result['info'] = $info;
        }

        return return_tidy_result($result);
    }

}
