<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {

    public function index() {
        $session = session('Admin');
        if($session['initial_password']){
            $initial_password = true;
            unset($session['initial_password']);
            session('Admin',$session);
        }
        //更新用户权限信息
        D('SysFunction')->reload_function();
    	//获取用户详细数据
    	$user = D('SysUser')->userinfo(D('SysUser')->self_id());
        //获取菜单详情

        //获取事务提醒
        $map = array();
        $map['unread.user_id'] = D('SysUser')->self_id();
        $receive_count = M('Sys_remind_receive_unread as unread')->where($map)->count();

        $receive_list = M('Sys_remind_receive_unread as unread')
        ->field('unread.*,content.remind_content,content.create_date,menu.page_url,menu.menu_id,menu.menu_name')
        ->join('t_flow_sys_remind_content as content on content.content_id = unread.content_id','left')
        ->join('t_flow_sys_remind_type as type on content.remind_type_id = type.remind_type_id','left')
        ->join('t_flow_sys_menu as menu on menu.menu_id = type.menu_id and menu.status = 1','left')
        ->where($map)->order('content.create_user_id desc')->limit(5)->select();
        foreach($receive_list as $k=>$v){
            $receive_list[$k]['create_date'] = substr($v['create_date'],0,10);
        }
        
        $self_user_id = D('SysUser')->self_id();
        $self_user_type = D('SysUser')->self_user_type();
        $sysnotice = D('SysNotice')->get_sysnotice($self_user_id, $self_user_type);
        $notice_one = D('SysNotice')->get_sysnotice_one($self_user_id, $self_user_type);
        $notice_not_sum = D('SysNotice')->get_sysnotice_sum($self_user_id, $self_user_type);

        $sys_info = $this->get_default_domain_info($self_user_type);
        $sys_name = $sys_info[0];
        $log_src = $sys_info[1];
        $display_icon = $sys_info[2];


        $upper_role=D('SysRole')->upper_role();
        $upper_role_info=$upper_role['in']?1:2;
        $this->assign('upper_role',$upper_role_info);
        $this->assign('sys_name',$sys_name);
        $this->assign('log_src',$log_src);
        $this->assign('display_icon',$display_icon);

        $menulist = D('SysMenu')->getmenu();
        $this->assign('receive_count',$receive_count);
        $this->assign('receive_list',$receive_list);
        $this->assign('initial_password',$initial_password);
        $this->assign('is_admin',D('SysUser')->is_admin());
        $this->assign('menulist',$menulist);
    	$this->assign('user',$user);
    	$this->assign('sysnotice',$sysnotice);
    	$this->assign('notice_one',$notice_one);
    	$this->assign('notice_not_sum',$notice_not_sum);
    	$this->display();
    }

    /**
     * @param $self_user_type
     * @return array
     * 判断自己是否设置了
     * 判断上级是否设置
     * 判断网站域名是否设置
     */
    private function get_default_domain_info($self_user_type){
        $judge = C('JUDGE_LOGIN');
        $domain_host = C('DOMAIN_LOGIN_DEFAULT_INFO');        //官网配置
        $host = $_SERVER['SERVER_NAME'];
        $sys_name = $domain_host['display_title']; //爱讯流量管理平台
        $log_src = $domain_host['logo'];  // /Public/Admin/images/logo_09.png
        $display_icon = $domain_host['display_icon'];

        if($judge){
            $main_web_site = C('MAIN_WEB_SITE');    //官网网址
            if(!in_array($host, $main_web_site)){
                $sys_name = ""; //爱讯流量管理平台
                $log_src = "";  // /Public/Admin/images/logo_09.png
                $display_icon = "";
            }
        }

        //根据域名判断是否设置
        $host2 = trim($host,'www.');
        $map_domain['domain_name'] = array('like',"%$host2%");
        $map_domain['approve_status'] = 3; //复审通过
        $join = array(
            C('DB_PREFIX').'sys_user_set as us on us.enterprise_id=d.enterprise_id and us.proxy_id=d.proxy_id'
        );

        $is_have = M('domain as d')
            ->field('d.logo_img,d.web_name,d.web_end,d.ico_img,d.back_img,us.logo_img as login_logo_img')
            ->join($join,'left')
            ->where($map_domain)
            ->find();
        if(!empty($is_have['ico_img'])){
            $display_icon = $is_have['ico_img'];
        }
        if(!empty($is_have['web_name'])){
            $sys_name = $is_have['web_name'];
        }
        if(!empty($is_have['login_logo_img'])){
            $log_src = $is_have['login_logo_img'];
        }

        $top_proxy_id = 0;
        if($self_user_type > 1){
            $map = array();
            $the_type = $self_user_type - 1;
            $map = array('user_type' => $the_type);
            if($the_type == 1){
                $proxy_id = D("SysUser")->self_proxy_id();
                $map['proxy_id'] = $proxy_id ;

                $top_proxy_id = D('SysUser')->self_top_proxys_id($proxy_id);
            }else{
                $enterprise_id = D("SysUser")->self_enterprise_id();
                $map['enterprise_id'] = $enterprise_id;

                $top_proxy_id = D('SysUser')->self_top_proxy_id($enterprise_id);
            }
            $user_set_info = M('Sys_user_set')->where($map )->find();
            if(!empty($user_set_info)){
                if(!empty($user_set_info['logo_img'])){
                    $log_src = $user_set_info['logo_img'];
                }
                if(!empty($user_set_info['web_name'])){
                    $sys_name = $user_set_info['web_name'];
                }
            }else{
                if(!empty($top_proxy_id)){
                    $map = array();
                    $map['user_type'] = 1;
                    $map['proxy_id'] = $top_proxy_id ;
                    $map['is_sub_use'] = 1 ;
                    $user_set_top_info = M('Sys_user_set')->where($map)->find();

                    if(!empty($user_set_top_info)){
                        if(!empty($user_set_top_info['logo_img'])){
                            $log_src = $user_set_top_info['logo_img'];
                        }
                        if(!empty($user_set_top_info['web_name'])){
                            $sys_name = $user_set_top_info['web_name'];
                        }
                    }
                }
            }
        }
        return array($sys_name,$log_src,$display_icon);
    }

    public function get_sysnotice_index_one(){
        $self_user_id = D('SysUser')->self_id();
        $self_user_type = D('SysUser')->self_user_type();
        $data['info'] =  D('SysNotice')->get_sysnotice_one($self_user_id, $self_user_type);
        $data['res']=D('SysNotice')->get_sysnotice_one_sort($self_user_id, $self_user_type,$data['info']['notice_id'],1);
        $data['pre']=D('SysNotice')->get_sysnotice_one_sort($self_user_id, $self_user_type,$data['info']['notice_id'],2);
        if(empty($data['info'])){
            $data['info']['status']='error';
        }else{
            $data['info']['status']='success';
        }
        $this->ajaxReturn($data);
    }
    public function get_notice_one() {
        $notice_id = I('notice_id');
        $sort=I('sort');
        $self_user_id = D('SysUser')->self_id();
        $self_user_type = D('SysUser')->self_user_type();
        $data['info'] = D('SysNotice')->get_sysnotice_one_sort($self_user_id, $self_user_type,$notice_id,$sort);
        $data['res']=D('SysNotice')->get_sysnotice_one_sort($self_user_id, $self_user_type,$data['info']['notice_id'],$sort);
        if(empty($data['info'])){
            $data['info']['status']='error';
        }else{
            $data['info']['status']='success';
        }
        $this->ajaxReturn($data);
    }
    
    /**
     * 查询代理商或企业账户
     */
    public function account_list(){
        $user_type = D("SysUser")->self_user_type();
        if($user_type==2){
            $where['pa.proxy_id'] = D("SysUser")->self_proxy_id();
            $list = M("proxy_account pa")
                ->join(C('DB_PREFIX').'proxy_loan as pl on pa.proxy_id =pl.proxy_id and pl.is_pay_off=0 and pl.approve_status=5','left')
                ->field('pa.account_balance,
                pa.freeze_money,
                sum(ifnull(pl.loan_money,0)-ifnull(pl.repayment_money,0)) as loan_money')
                ->where($where)
                ->group('pa.proxy_id')
                ->find();
            $data['account_balance'] = $list['account_balance']-$list['loan_money'];
            $data['loan_money'] = $list['loan_money'];
            $data['freeze_money'] = $list['freeze_money'];
        }elseif($user_type==3){
            $where['enterprise_id'] = D("SysUser")->self_enterprise_id();
            $list = M("enterprise_account")->where($where)->find();
            $data['account_balance'] = $list['account_balance'];
            $data['freeze_money'] = $list['freeze_money'];
        }else{
            $data['account_balance'] = 0.00;
            $data['freeze_money'] = 0.00;
        }
        $this->ajaxReturn(array('info'=>$data));
    }
    
    public function main(){
        //获取当前用户所属平台  返回类型:1=>尚通运营端,2=>代理商端,3=>企业端
        $user_type = D('SysUser')->self_user_type();
        //当天开始时间和结束时间
        $beginToday = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
        $endToday = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1);
        //公共时间搜索条件为当天
        $where['create_date'] = array("between",array($beginToday,$endToday));
        $where['approve_status'] = $where2['approve_status'] = 1;   //读取审核成功的代理商
        //判断除企业之外的代理商显示
        if($user_type!=3){
            if($user_type==2){
                $where['top_proxy_id'] = $where2['top_proxy_id'] = D("SysUser")->self_proxy_id();
            }
            if($user_type==1){
                $where['proxy_id'] = array("neq",1);
                $where2['proxy_id'] = array("neq",1);
                $where2['status'] = array("neq",2);
            }
            //计算当天新增的代理商
            $info['proxy_same_day'] = M("proxy")->where($where)->count();
            //计算总共的代理商
            $info['proxy_total'] = M("proxy")->where($where2)->count();
            //计算当天新增的企业
            $info['enterprise_same_day'] = M("enterprise")->where($where)->count();
            //计算总共的企业
            $info['enterprise_total'] = M("enterprise")->where($where2)->count();
        }else{
            $info = NULL;
        }
        if($user_type==3){
            $enterprise_id=D("SysUser")->self_enterprise_id();
            $where['stat_type']=2;
            $where['user_id']=$enterprise_id;
            $where['stat_stauts']='205';
            $arr=M("stat_product")->where($where)->field("sum(discount_price) as user_money")->select();
            $map['ea.enterprise_id']=$enterprise_id;
            $info=M("enterprise_account as ea")
                ->join("t_flow_enterprise as e on e.enterprise_id = ea.enterprise_id")
                ->where($map)->field("ea.account_balance,e.enterprise_name,ea.freeze_money")->find();
            $this->assign("user_money",$arr[0]['user_money']);
            $this->assign("a_balence",$info['account_balance']);
            $this->assign("all_money",$info['account_balance']+$arr[0]['user_money']);
            $this->assign("enterprise_name",$info['enterprise_name']);
            $begintime = date("Y-m-d",mktime(0,0,0,date('m'),1,date('Y')));
            $endtime = date("Y-m-d",mktime(23,59,59.999999,date('m'),date('t'),date('Y')));
            $user_id=D("SysUser")->self_enterprise_id();
            $order_flow = M()->query("CALL p_get_stat_order_home(2,0,'".$begintime."','".$endtime."',0,".$user_id.");");
            $users=0;//用户数
            $products=0;//流量数
            foreach($order_flow as $v){
                $users+=$v['stat_count'];
            }
            $order_flow = M()->query("CALL p_get_stat_order_home(2,1,'".$begintime."','".$endtime."',0,".$user_id.");");
            foreach ($order_flow as $c){
                $products+=$c['stat_size'];
            }
            $this->assign("users",$users);
            $this->assign("products",$products);
        }
        $upper_role=D('SysRole')->upper_role();
        $upper_role_info=$upper_role['in']?1:2;
        $this->assign('upper_role',$upper_role_info);
        $this->assign('info',$info);
        $this->assign('user_type',$user_type);
        $this->display();
    }
    
    /**
     * JS图表读取方法
     * operator => 按运营商读取
     * province => 按省读取
     * date     => 按日期读取
     */

    function main_ajax(){
        session_write_close();
        //D("SysUser")->sessionwriteclose();
        $operation = I("get.operation");    //操作方法
        $date = I("post.date");          //本周/本月
        $operator = I("post.operator");  //运营商

        $cache_time = 21600;    //缓存时间 6小时 S($key,$data,300);
        //缓存的key数值,由$operation，$date，$operator，代理商或者企业id拼接后MD5组成
        $key = $operation.$date.$operator;
        $month=array();
        if($date=="本月"){
            //本月开始时间和结束时间
            $begintime = date("Y-m-d",mktime(0,0,0,date('m'),1,date('Y')));
            $endtime = date("Y-m-d",mktime(23,59,59.999999,date('m'),date('t'),date('Y')));
            //本月天数
            $month_date = date('t', time());
            $a = 0;
            for($i=1;$i<=$month_date;$i++){
                $month[$a]['begintime'] = date("Y-m-d",mktime(0,0,0,date('m'),$i,date('Y')));
                $month[$a]['endtime'] = date("Y-m-d",mktime(23,59,59.999999,date('m'),$i,date('Y')));
                $a++;
            }
        }else{
            //本周开始时间和结束时间
            $begintime = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y')));
            $endtime = date("Y-m-d",mktime(23,59,59.999999,date('m'),date('d')-date('w')+7,date('Y')));
            //本周天数
            $a = 0;
            for($i=1;$i<=7;$i++){
                $month[$a]['begintime'] = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-date('w')+$i,date('Y')));
                $month[$a]['endtime'] = date("Y-m-d",mktime(23,59,59.999999,date('m'),date('d')-date('w')+$i,date('Y')));
                $a++;
            }
        }
        //读取用户所属的平台
        $user_type = D("SysUser")->self_user_type();
        //公共查询条件
        $where="";
        $where.= 'stat_status = 205';   //成功的订单
        if($user_type==2){
            $where .= " and stat_type  = 1";
            $where .= " and  user_id =".D("SysUser")->self_proxy_id();
            $key .= $where['proxy_id'];
        }else{
            if($user_type==3){
                $where .= " and stat_type =2";
                $where .=" and user_id =".D("SysUser")->self_enterprise_id();
                $key .= $where['enterprise_id'];
            }
        }
        //记录所属平台 运营端-1，代理商端1，企业端2
        switch ($user_type){
            case 1:
                $p_user_type=-1;
                $user_id=1;
                break;
            case 2:
                $p_user_type=1;
                $user_id=D("SysUser")->self_proxy_id();
                break;
            case 3:
                $p_user_type=2;
                $user_id=D("SysUser")->self_enterprise_id();
        }
        $key = md5($key);
        if($operation !="date"){
            $where.=" and stat_day between '$begintime' and '$endtime'";//当前时间内容
        }
       $upper_role=D('SysRole')->upper_role();
        if($upper_role['in']){
            $channel_id=!empty($upper_role['channel_id'])?$upper_role['channel_id']:'0';
            $where['cp.channel_id'] = array('in',$channel_id);
            $where.= " and channel_id in ($channel_id)";
        }
        $data = array();//ajax 返回数据
        $arr  = array();// 记录运营商的名字
        $ords = array();//记录运营商和人数
        $status = "error";
        $is_cach_exist = S($key);
        if(1){
            if($operation=="operator"){ //饼图
                //读取所有运营商
                $p_pic_id=0; //0表示饼图
                $info_operator = D("ChannelProduct")->operatorall();
                foreach ($info_operator as $v){
                    array_push($arr,$v['operator_name']);
                }
                $order_flow = M()->query("CALL p_get_stat_order_home(".$p_user_type.",".$p_pic_id.",'".$begintime."','".$endtime."',0,".$user_id.");");
               foreach ($arr as $a){
                   $c=0;
                   foreach ($order_flow as $v){
                       if($v['operator_id']==$a){
                            $c=$v['stat_count'];
                           break;
                       }
                   }
                   array_push($ords,array('name'=>$a,'value'=>$c));
               }
                $status="success";
                $data['order_name']=$arr;
                $data['order_size']=$ords;
                S($key,$data,$cache_time);
            }elseif($operation=="date"){    //柱图
                //读取运营商ID
                $info_operator = D("ChannelProduct")->operatorall();
                foreach ($info_operator as $v){
                    array_push($arr,$v['operator_name']);
                }
                $p_pic_id=1; //1表示柱图
                $order_flow = M()->query("CALL p_get_stat_order_home(".$p_user_type.",".$p_pic_id.",'".$begintime."','".$endtime."',0,".$user_id.");");
                foreach ($arr as $c){
                    $z=0;
                    foreach ($order_flow as $v){
                        if($c==$v['operator_id']){
                            $z=$v['stat_size'];
                        }
                    }
                    array_push($ords,array("name"=>$c,"value"=>$z));
                }
                $status = "success";
                $data['order_name']=$arr;
                $data['order_size']=$ords;
                S($key,$data,$cache_time);
            }elseif($operation=="province"){    //柱桩图
                $info_operator = M("SysOperator")->where(array('operator_name'=>$operator))->field("operator_id")->find();
                $operator = $info_operator['operator_id'] > 0 ? $info_operator['operator_id'] : 1;
                $p_pic_id=2; //2表示柱图
                $order_flow = M()->query("CALL p_get_stat_order_home(".$p_user_type.",".$p_pic_id.",'".$begintime."','".$endtime."',$operator,".$user_id.");");
                foreach ($order_flow as $v){
                    array_push($arr,$v['province_name']);
                    array_push($ords,array('name'=>$v['province_name'],'value'=>$v['stat_count']));
                }
                $status="success";
                $data['order_name']=$arr;
                $data['order_size']=$ords;
                S($key,$data,$cache_time);
            }
        }else{
            $status = "success";
            $data = S($key);
        }

        $this->ajaxReturn(array('info'=>$data,'status'=>$status));
    }

    /**
     * 输入框联想使用
     * 获取代理商名称和ID号
     */
    public function ajax_username(){
        D("SysUser")->sessionwriteclose();
        $usertype = I("usertype");
        $name = I("name");
        $nameall = I("nameall");
        $where['status'] = 1;
        $where['approve_status']=1;
        if($usertype=="proxy"){
            $where2['proxy_name'] = array("like","%".$name."%");
            $where2['proxy_code'] = array("like","%".$name."%");
            $where2['_logic'] = 'or';
            $where['_complex'] = $where2;
            if($nameall=="directChild"){
                $where['status'] = array('neq',2);
                $where['proxy_level']=array('eq', 1);
                $where['proxy_id'] = array('in', D('Proxy')->proxy_child_ids());
            }else if( $nameall!="directChild" && $nameall!="all") {
                $where['proxy_id'] = array('in', D('Proxy')->proxy_child_ids());
                if( $nameall!="allchild") {
                    $where['top_proxy_id'] = array('eq', D("SysUser")->self_proxy_id());
                }
            }
            $list = M("proxy")->where($where)->field("proxy_id as id,proxy_name as name,proxy_code as code")->limit(0,10)->select();
        }else{
            $user_type=D('SysUser')->self_user_type();
            $user_id=D("SysUser")->self_id();
            if($nameall=="directChild"){
                $where['status'] = array('neq',2);
                $direct_enterprise_ids = D('Enterprise')->get_direct_enterprise_ids();
                if(is_array($direct_enterprise_ids)){
                    $where["enterprise_id"] = array("in", implode(',',$direct_enterprise_ids));
                }
            }else if($nameall!="directChild" && $nameall!="all") {
                if ($user_type == 1) {
                    if (!D('SysUser')->is_admin()) {
                        $ids = D("Enterprise")->enterprise_child_ids();//获取所有可操作企业号
                        $is = M("EnterpriseUser")->where(array("user_id" => $user_id))->distinct(true)->field("enterprise_id")->select();
                        if ($is) {
                            if ($ids) {
                                $ids = $ids . ",";
                            }
                            foreach ($is as $v) {
                                $ids .= $v['enterprise_id'];
                            }
                        } else {
                            if (!$ids) {
                                $ids = -1;
                            }
                        }
                        $where["enterprise_id"] = array("in", $ids);
                    }
                } else {
                    $ids = D("Enterprise")->enterprise_ids();//获取该用户可操作的企业号
                    $where['enterprise_id'] = array("in", $ids);
                    if($nameall!="allchild"){
                        $where['top_proxy_id'] = array('eq', D("SysUser")->self_proxy_id());
                    }
                }
            }
            $where2['enterprise_name'] = array("like","%".$name."%");
            $where2['enterprise_code'] = array("like","%".$name."%");
            $where2['_logic'] = 'or';
            $where['_complex'] = $where2;
            $list = M("enterprise")->where($where)->field("enterprise_id as id,enterprise_name as name,enterprise_code as code")->limit(0,10)->select();
        }
        if(!$list){
            $list = '';
        }
        $this->ajaxReturn(array('info'=>$list));
    }

    /**
         * 输入框联想使用
         * 获取通道名称和ID号
         */
    public function ajax_channel(){
        D("SysUser")->sessionwriteclose();

        //过滤已有的通道
        //过滤--网关上游通道
        $is_filter = trim(I("is_filter"));
        if($is_filter == 'gateway'){
            $where_cid=array();
            $where_cid['gc.server_id']=array("gt",0);
            $where_cid['gc.channel_id']=array("gt",0);
            $channel_ids = M('gateway_channel as gc')
                ->join("LEFT JOIN ".C('DB_PREFIX').'channel as c on c.channel_id = gc.channel_id')
                ->join("LEFT JOIN ".C('DB_PREFIX').'gateway_run as gr on gr.server_id = gc.server_id')
                ->where($where_cid)
                ->field("gc.channel_id")
                ->select();

            $channel_ids = get_array_column($channel_ids, 'channel_id');
            $where['c.channel_id']=array("not in",$channel_ids);
        }


        $name = trim(I("name"));
        $is_role = trim(I("is_role"));
        $is_role_define = trim(I("is_role_define"));
        $have_discount = trim(I("have_discount"));
        $ctype=trim(I("ctype"));
        if($ctype=="java"){
            $where["c.platform_id"]=2;
        }
        $is_status=trim(I("is_status"));
        if($is_status!="show"){
            $where['c.status'] = 1;
        }
        if($is_role_define && empty($is_role)){
            $where['c.channel_code']=array("like","%".$name."%");
        }else{
            $where1['c.channel_name']=array("like","%".$name."%");
            $where1['c.channel_code']=array("like","%".$name."%");
            $where1['_logic']="or";
            $where[]=$where1;
        }

        if(!empty($have_discount)){
            $join = array(
                C('DB_PREFIX').'channel_discount as cd ON cd.channel_id=c.channel_id'
            );
            $where_a['c.attribute_id'] = 1;
            $where_a = array_merge($where_a,$where);
            $list_a = M('channel as c')->where($where_a)
                ->join($join,"inner")
                ->field("c.channel_id as id,c.channel_name as name,c.channel_code as code,c.attribute_id as attribute")
                ->order('c.modify_date desc')
                ->limit(0,15)
                ->group("c.channel_id")
                ->select();

            //流量池通道
            $count_b = 20 - count($list_a);
            $where_b['c.attribute_id'] = 2;
            $where_b = array_merge($where_b,$where);
            $list_b = M('channel as c')->where($where_b)
                ->field("c.channel_id as id,c.channel_name as name,c.channel_code as code,c.attribute_id as attribute")
                ->order('c.modify_date desc')
                ->limit(0,$count_b)
                ->select();
            $list = array_merge($list_a,$list_b);
        }else{
            $list = M("Channel as c")->where($where)->field("channel_id as id,channel_name as name,channel_code as code,attribute_id as attribute")->limit(0,20)->select();
        }
        if(!$list){
            $list = '';
        }
        $this->ajaxReturn(array('info'=>$list));
    }

    /**
     * 输入框联想使用
     * 获取网关名称和ID号
     */
    public function ajax_server(){
        D("SysUser")->sessionwriteclose();
        $name = trim(I("name"));
        $where['status'] = 1;
        $where['server_name']=array("like","%".$name."%");
        $list = M("gateway_run")->where($where)->field("server_id as id,server_name as name")->limit(0,20)->select();
        if(!$list){
            $list = '';
        }
        $this->ajaxReturn(array('info'=>$list));
    }
    
    //通过ajax获取所有的市
    public function ajax_city(){
        $province_id = trim(I("province_id"));//省份id
        if($province_id!=1){
            $where["province_id"]=$province_id;
        }
        $list = M("sys_city")->where($where)->field("city_id,city_name")->select();
        if(!$list){
            $list = '';
        }
        $this->ajaxReturn(array('info'=>$list));
    }
}