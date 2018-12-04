<?php

/*
 * 流量场景 活动管理 操作控制器
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;

class SceneActivityController extends CommonController {

    /*
     * 菜单列表
     */
    public function index() {
        $activity_name  = trim(I('activity_name'));
        $status = I('status');
        if(!empty($activity_name)){
            $ma1['b.activity_name'] = array("like", "%{$activity_name}%");
            $ma1['a.user_activity_name']=array("like","%{$activity_name}%");
            $ma1['_logic']="or";
            $map[]=$ma1;
        }
        //列表出状态和全部
        $newtime=date("Y-m-d H:i:s" ,time());
        if($status ==1){
            $map1['a.start_date'] = array("elt",$newtime);
            $map1['a.end_date']=array("gt",$newtime);
            $map1['_logic']="and";
            $map[]=$map1;
        }elseif($status ==2){
            $map['a.start_date']=array('gt',$newtime);
        }elseif($status ==3){
            $map['a.end_date']=array('elt',$newtime);
        }

        $self_user_type = D('SysUser')->self_user_type(); //1 2 3
        $user_type = $self_user_type - 1; //1：代理商、2：企业
        $self_proxy_id = intval(D('SysUser')->self_proxy_id());
        $self_enterprise_id = intval(D('SysUser')->self_enterprise_id());
        $map['a.user_type'] = array('eq', $user_type);
        $map['a.proxy_id'] = array('eq', $self_proxy_id);
        $map['a.enterprise_id'] = array('eq', $self_enterprise_id);
        $map['a.user_activity_type'] = array(array('neq', 2), array('exp','is null'),'or');
        //获取所有角色列表
        $user_activity_list = M('scene_user_activity as a')
            ->where($map)
            ->join(C('DB_PREFIX').'scene_activity as b ON a.activity_id=b.activity_id',"left")
            ->field("a.*,b.activity_name")
            ->order("a.modify_date desc,a.activity_id asc")
            ->select();
        /*if($user_activity_list){
            foreach($user_activity_list as $k=>$v){
                $user_activity_list[$k]['start_date']=date("Y-m-d",strtotime($v['start_date']));
                $user_activity_list[$k]['end_date']=date("Y-m-d",strtotime($v['end_date']));
            }
        }*/
        foreach($user_activity_list as $k=>$v){
            $start=strtotime($v['start_date']);
            $end=strtotime($v['end_date']);
            $now=time();
            if($start>$now){
                $user_activity_list[$k]['stat']="未开始";
            }elseif($end<$now){
                $user_activity_list[$k]['stat']="已结束";
            }else{
                $user_activity_list[$k]['stat']="进行中";
            }
            $id=$v['user_activity_id']."";
            $data=$this->localencode($id);
            $user_activity_list[$k]['activityaddress']=gethostwithhttp()."/index.php/Sdk/Index/aindex?".$data;
        }
        //加载模板
        $this->assign('user_activity_list',get_sort_no($user_activity_list));  //数据列表
        $this->assign('arrfrequency', get_scene_frequency());
        $this->display();         //模板
    }


    public function show() {
        $msg = '系统错误！';
        $status = 'error';
        //查询当前角色信息
        $user_activity_id = I('user_activity_id',0,'int');
        $info = M('scene_user_activity')->where("user_activity_id={$user_activity_id}")->find();
        //读取活动名称
        $activity = M('scene_activity')->where("activity_id={$info['activity_id']}")->find();
        $products=M('scene_configuration as sc')->join("t_flow_product as cp on cp.product_id=sc.product_id")->field("cp.operator_id,cp.product_name")->where("user_activity_id={$user_activity_id}")->select();
        $p_yd=array();//中国移动
        $p_lt=array();//中国联通
        $p_dx=array();//中国电信
        foreach($products as $ps){
              if($ps['operator_id']==1){
                array_push($p_yd,$ps);
            }elseif($ps['operator_id']==2){
                array_push($p_lt,$ps);
            }else{
                array_push($p_dx,$ps);
            }
        }
        $info['activity_name'] = $activity['activity_name'];
        $id=$info['user_activity_id']."";
        $data=$this->localencode($id);
        $info['activity_address']=gethostwithhttp()."/index.php/Sdk/Index/aindex?".$data;
        $info['activity_rule'] = str_replace("\n", "<br />", $info['activity_rule']);
        //当角色不存在时
        if($info) {
            $this->assign("p_yd",$p_yd);
            $this->assign("p_lt",$p_lt);
            $this->assign("p_dx",$p_dx);
            $this->assign($info);
            $this->display();
        }else{
            $this->ajaxReturn(array('msg'=>'活动不存在！','status'=>$status));
        }
    }

    public function localencode($data) {
        $string = "";
        for($i=0;$i<strlen($data);$i++){
            $ord = ord($data[$i]);
            $ord += 20;
            $string = $string.chr($ord);
        }
        $data = base64_encode($string);
        return $data;
    }

    /**
     * 添加模板
     */
    public function add() {
        if(I('op') == '1') {
            $id = I('activity_id');
            $ret = D('SceneInfo')->get_scene_activity_byid($id);
            $this->ajaxReturn($ret);
        } else {
            $arr_activity = D('SceneInfo')->get_scene_activity_all();

            $this->assign('arr_activity', $arr_activity);
            $this->assign('arrfrequency', get_scene_frequency());
            $this->display();
        }
    }

    /**
     * 添加
     */
    public function insert() {
        $msg = '系统错误！';
        $status = 'error';
        if(IS_POST) {
            $user_activity_name=I('post.user_activity_name');
            $activity_id = I('post.activity_id');
            $activity_rule = I('post.activity_rule');
            $start_date = I('post.start_date');
            $end_date = I('post.end_date');
            $frequency = I('post.frequency');
            $number = I('post.number');
            $activity_status = I('post.activity_status');
            $activity_money=I('post.activity_money');
            if(!empty($activity_money)){
                if(!preg_match('/^[0-9]+(.[0-9]{1,2})?$/',$activity_money)){
                    $this->ajaxReturn(array('msg'=>'请输入正确的金额！','status'=>$status));
                }
            }else{
                $activity_money=0;
            }
            if(empty($user_activity_name)){
                $msg="请输入活动名称！";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
            $sua['user_activity_name']=$user_activity_name;
            $sua['enterprise_id']=D('SysUser')->self_enterprise_id();
            $info=M("scene_user_activity")->where($sua)->find();
            if($info){
                $msg="活动名称已存在，请勿重复添加！";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
            if(!empty($activity_id) ){
                if(is_numeric($number) && $number > 0) {
                    if(is_numeric($frequency)) {
                        if($activity_rule != ''){
                            if($start_date != '' && $end_date != ''){
                                if($start_date < $end_date) {
                                    $self_user_id = D('SysUser')->self_id();
                                    $now_date = date('Y-m-d H:i:s');
                                    $self_user_type = D('SysUser')->self_user_type(); //1 2 3
                                    $user_type = $self_user_type - 1; //1：代理商、2：企业
                                    $self_proxy_id = D('SysUser')->self_proxy_id();
                                    $self_enterprise_id = D('SysUser')->self_enterprise_id();

                                    $map['user_type'] = array('eq', $user_type);
                                    $map['proxy_id'] = array('eq', $self_proxy_id);
                                    $map['enterprise_id'] = array('eq', $self_enterprise_id);
                                    $map['activity_id'] = array('eq', $activity_id);

                                    $menuinfo = M('scene_user_activity')->where($map)->find();
                                    $activity_info = M('scene_activity')->where("activity_id={$activity_id}")->find();
                                    $activity_file_name = $activity_info['activity_file_name'];
                                    $arr_afn = explode(',', $activity_file_name);
                                    $need_afn = $arr_afn[1];

                                    // if(!$menuinfo){
                                        $user_id = ($user_type == 1) ? $self_proxy_id : $self_enterprise_id;
                                        $add = array(
                                            'user_activity_name'=>$user_activity_name,
                                            'user_type'         => $user_type,
                                            'proxy_id'          => $self_proxy_id,
                                            'enterprise_id'     => $self_enterprise_id,
                                            'activity_id'       => $activity_id,
                                            'activity_address'  => "",
                                            'activity_rule'     => $activity_rule,
                                            'start_date'        => $start_date,
                                            'end_date'          => $end_date,
                                            'frequency'         => $frequency,
                                            'number'            => $number,
                                            'activity_status'   => $activity_status,
                                            'create_user_id'    => $self_user_id,
                                            'create_date'       => $now_date,
                                            'modify_user_id'    => $self_user_id,
                                            'modify_date'       => $now_date,
                                            'activity_money'    => $activity_money,
                                            'used_money'        =>0,
                                            'propagandat_img'   => '/Public/Uploads/./Enterprise_scene/2016-05-10/231cee0aceab9.png',
                                            'logo_img'          => '/Public/Uploads/./Enterprise_scene/2016-05-10/570cee323eab9.png',
                                            'background_img'    => '/Public/Uploads/./Enterprise_scene/2016-05-10/232cee0aceab9.png',
                                        );
                                        //初始化流量包配置
                                        $id=M('scene_user_activity')->add($add);
                                        if($id){
                                            $d['activity_address']= gethostwithhttp()."/index.php/Sdk/Index/index/mod/{$need_afn}/func/index/aid/{$activity_id}/user_type/{$user_type}/user_id/{$user_id}/user_activity_id/{$id}";
                                            M("scene_user_activity")->where(array("user_activity_id"=>$id))->save($d);
                                            $where=array();
                                            $where['user_type']=$user_type;
                                            $where['proxy_id']=$self_proxy_id;
                                            $where['enterprise_id'] =$self_enterprise_id;
                                            $where['activity_id']=$activity_id;
                                            $this->set_package($where); //初始化流量包
                                            $msg = '新增活动成功！';
                                            $status = 'success';
                                            $u_msg="成功";
                                        }else{
                                            $msg = '新增活动失败！';
                                            $u_msg="失败";
                                            write_error_log(array(__METHOD__.':'.__LINE__, 'sql=='.M()->getLastSql()));
                                        }
                                    // }else{
                                    //     $msg = '你已经选择过此活动,请仔细检查！';
                                    //     $u_msg="失败";
                                    // }
                                } else {
                                    $msg = '开始时间必须早于结束时间！';
                                    $u_msg="失败";
                                }
                            } else {
                                $msg = '开始时间结束时间都不能为空！';
                                $u_msg="失败";
                            }
                        }else{
                            $msg = '请输入活动规则！';
                            $u_msg="失败";
                        }
                    } else {
                        $msg = '请填写正确的参与频率！';
                        $u_msg="失败";
                    }
                } else {
                    $msg = '请填写正确的参与次数！';
                    $u_msg="失败";
                }
            } else {
                $msg = '请选择活动！';
                $u_msg="失败";
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】,新增活动'.$u_msg;
            $this->sys_log('新增活动',$note);
            IS_AJAX && $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }

    public function  set_package($where){
        $self_user_type = D('SysUser')->self_user_type(); //1 2 3
        $user_type = $self_user_type - 1; //1：代理商、2：企业
        $self_proxy_id = D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        $user=M("scene_user_activity")->where($where)->field("user_activity_id")->find();
        $m_pro['province_id']=1;//表示全国
        $m_pro['status']=1;
        $m_pro['user_activity_id']=$user['user_activity_id'];
        $products= M('product as p')->order("p.base_price asc,p.product_name asc")->where($m_pro)->select();
        $products=D("ChannelProduct")->get_products($products);
        $p_yd=array();//中国移动
        $p_lt=array();//中国联通
        $p_dx=array();//中国电信
        foreach($products as $ps){
            if($ps['operator_id']==1){
                array_push($p_yd,$ps);
            }elseif($ps['operator_id']==2){
                array_push($p_lt,$ps);
            }else{
                array_push($p_dx,$ps);
            }
        }
        $probability=array('80','10','5','5');
        $pag_all=array();
        $m_p = array();
        $m_p['user_type'] = $user_type;
        $m_p['proxy_id'] = $self_proxy_id;
        $m_p['enterprise_id'] = $self_enterprise_id;
        $m_p['create_user_id'] = D('SysUser')->self_id();
        $m_p['create_date'] = date('Y-m-d H:i:s');
        $m_p['modify_user_id'] = D('SysUser')->self_id();
        $m_p['modify_date'] = date('Y-m-d H:i:s');
        $m_p['user_activity_id'] = $user['user_activity_id'];
        for($i=0;$i<4;$i++){
            if(count($p_yd)>$i+1){
                $m_yd=$m_p;
                $m_yd['product_id'] = $p_yd[$i]['product_id'];
                $m_yd['probability'] = $probability[$i];
                $m_yd['operator_id'] = 1;
                array_push($pag_all,$m_yd);
            }
            if(count($p_lt)>$i+1){
                $m_lt=$m_p;
                $m_lt['product_id'] = $p_lt[$i]['product_id'];
                $m_lt['probability'] = $probability[$i];
                $m_lt['operator_id'] = 2;
                array_push($pag_all,$m_lt);
            }
            if(count($p_dx)>$i+1){
                $m_dx=$m_p;
                $m_dx['product_id'] = $p_dx[$i]['product_id'];
                $m_dx['probability'] = $probability[$i];
                $m_dx['operator_id'] = 3;
                array_push($pag_all,$m_dx);
            }
        }
        $pd=M("scene_configuration")->addAll($pag_all);
        return $pd;

    }

    public function edit() {
        $user_activity_id = I('user_activity_id', 0, 'intval');
        $info = D('SceneInfo')->get_user_activity_byid($user_activity_id);

        //当菜单不存在时
        if($info){
            $arr_activity = D('SceneInfo')->get_scene_activity_all();

            $this->assign('arr_activity', $arr_activity);
            $this->assign('arrfrequency', get_scene_frequency());
            $this->assign('info',$info);
            $this->display('edit');
        }else{
            $this->error('活动不存在！');
        }
    }

    public function update(){
        $msg = '系统错误！';
        $status = 'error';

        if(IS_POST){
            $user_activity_id = I('post.user_activity_id');
            //$activity_id = I('post.activity_id');
            //$activity_status = I('post.activity_status');
            $user_activity_name=I("post.user_activity_name");
            $activity_rule = I('post.activity_rule');
            $start_date = I('post.start_date');
            $end_date = I('post.end_date');
            $frequency = I('post.frequency');
            $number = I('post.number');
            $activity_money=I('post.activity_money');
            $info=M('scene_user_activity')->where("user_activity_id={$user_activity_id}")->find();
            if($end_date !=$info['end_date'] || $start_date !=$info['start_date']){
                $info['used_money']=0;
            }
            if(empty($user_activity_name)){
                $msg="请输入活动名称！";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
            $ma1['user_activity_id']=array("neq",$user_activity_id);
            $ma1['user_activity_name']=$user_activity_name;
            $ma1["_logic"]="and";
            $ma[]=$ma1;
            $ma["enterprise_id"]=D('SysUser')->self_enterprise_id();
            $in=M('scene_user_activity')->where($ma)->find();
            if($in){
                $msg="活动名称重复请勿重复添加";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
            if(true){
                if(is_numeric($number) && $number > 0 && !strstr($number,".")) {
                    if(is_numeric($frequency)) {
                        if($activity_rule != '') {
                            if($start_date != '' && $end_date != '') {
                                if($start_date < $end_date) {
                                    $self_user_id = D('SysUser')->self_id();
                                    $now_date = date('Y-m-d H:i:s');
                                    //$self_user_type = D('SysUser')->self_user_type(); //1 2 3
                                    //$user_type = $self_user_type - 1; //1：代理商、2：企业
                                    //$self_proxy_id = D('SysUser')->self_proxy_id();
                                    //$self_enterprise_id = D('SysUser')->self_enterprise_id();

                                    $edit = array(
                                        'user_activity_name'=>  $user_activity_name,
                                        'activity_rule'     =>  $activity_rule,
                                        'start_date'        =>  $start_date,
                                        'end_date'          =>  $end_date,
                                        'frequency'         =>  $frequency,
                                        'number'            =>  $number,
                                        'activity_money'    =>  $activity_money,
                                        'used_money'        =>  $info['used_money'],
                                        'modify_user_id'    =>  $self_user_id,
                                        'modify_date'       =>  $now_date,
                                    );
                                    if(M('scene_user_activity')->where("user_activity_id={$user_activity_id}")->save($edit)){
                                        $msg = '编辑活动成功！';
                                        $status = 'success';
                                        $u_msg="成功";
                                    }else{
                                        $msg = '编辑活动失败！';
                                        $u_msg="失败";
                                        write_error_log(array(__METHOD__.':'.__LINE__, 'DB Error...sql=='.M()->getLastSql()));
                                    }
                                } else {
                                    $u_msg="失败";
                                    $msg = '开始时间必须早于结束时间！';
                                }
                            } else {
                                $u_msg="失败";
                                $msg = '开始时间结束时间都不能为空！';
                            }
                        } else {
                            $u_msg="失败";
                            $msg = '请输入活动规则！';
                        }
                    } else {
                        $u_msg="失败";
                        $msg = '请填写正确的参与频率！';
                    }
                } else {
                    $u_msg="失败";
                    $msg = '请填写正确的参与次数！';
                }
            }else{
                $u_msg="失败";
                $msg = '请选择活动！';
            }
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】,编辑活动'.$u_msg;
            $this->sys_log('编辑活动',$note);
            if(IS_AJAX) $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }

    /**
        删除活动
    **/
    public function delete(){
        $msg = '系统错误';
        $status = 'error';
        $user_activity_id=I('user_activity_id');
        $where = array('user_activity_id' => $user_activity_id);
        $info = M('scene_user_activity')->where($where)->find();
        $ms=M("scene_configuration")->where($where)->select();
        if($ms){
            $mp = M("scene_configuration")->where($where)->delete(); //把流量包先删除
        }
        if(empty($info)){
            $this->ajaxReturn(array('msg'=>'对不起没有找到相关信息，请重试！','status'=>'error'));
        }
        $res=M('scene_user_activity')->where($where)->delete();
        if($res){
            $msg='删除活动成功！';
            $u_msg="成功";
            $status = 'success';
        }else{
            $u_msg="失败";
            $msg='删除活动失败！';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id()).'】,删除活动'.$u_msg;
        $this->sys_log('删除活动',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    function toggle_status() {
        $msg = '系统错误！';
        $status = 'error';
        if(IS_POST && IS_AJAX){
            $id = I('post.id',0,'int');
            if(!empty($id)){
                $activity = D('scene_user_activity')->where(array('user_activity_id'=>$id))->find();
                if($activity){
                    $status = ($activity['activity_status'] == 1) ? 2 : 1;
                    $edit = array(
                        'user_activity_id'  => $id,
                        'activity_status'   => $status,
                        'modify_user_id'    => D('SysUser')->self_id(),
                        'modify_date'       => date('Y-m-d H:i:s'),
                    );
                    $edit = M('scene_user_activity')->save($edit);
                    $status_name = ($status == 1) ? "启用" : "禁用";
                    if($edit){
                        $status = 'success';
                        $msg = "活动".$status_name.'成功!';
                        $u_msg="活动".$status_name.'成功';
                    }else{
                        $msg = "活动".$status_name.'失败!';
                        $u_msg="活动".$status_name.'失败';
                    }
                }else{
                    $msg = '数据读取失败!';
                    $u_msg="数据读取失败";
                }
            }else{
                $msg = '传入ID错误!';
                $u_msg="传入ID错误";
            }
        }
        if(IS_AJAX){
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】,'.$u_msg;
            $this->sys_log('启用禁用',$note);
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }

    /**
     *   配置流量包
     */
    public function set(){
        $status="error";
        $msg="系统发生错误";
        $user_activity_id=I("user_activity_id");
        $type=I("get.type");

        if(empty($user_activity_id)||empty($type)) {
            $msg="配置参数错误";
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
        if ($type == "show")
        {
        		// 表示全国
        		$province_where['p.province_id'] = 1;
        		$province_where['p.status'] = 1;
        		$products = M('product as p')->join("t_flow_scene_configuration as t on t.product_id=p.product_id and t.user_activity_id=".$user_activity_id,"left")->where( $province_where )->field("p.*,t.probability")->select();
				$products=D("ChannelProduct")->get_products2($products);
        		// 1:移动 2:联通 3:电信
        		// 移动产品包
        		$mobile_products = array();
        		// 联通产品包
        		$unicom_products = array();
        		// 电信产品包
        		$telecom_products = array();
        		for($i = 0;$i <count($products);$i++)
        		{
        			$product = $products[$i];
        			if($product['operator_id'] == 1)
        			{
        				$mobile_products[] = $product;
        			}
        			else if ($product['operator_id'] == 2)
        			{
        				$unicom_products[] = $product;
        			}
        			else if ($product['operator_id'] == 3)
        			{
        				$telecom_products[] = $product;
        			}
        		}
        		$this->assign("user_activity_id",$user_activity_id);
        		$this->assign("p_yd",$mobile_products);
            $this->assign("p_lt",$unicom_products);
            $this->assign("p_dx",$telecom_products);
        		$this->display();
        }
        else if ($type == "update")
        {
        		// 1 尚通端 ， 2.代理商端  3.企业端
            $self_user_type = D('SysUser')->self_user_type(); //1 2 3
            
            $user_type = $self_user_type - 1; //1：代理商、2：企业
            $self_proxy_id = D('SysUser')->self_proxy_id();
            $self_enterprise_id = D('SysUser')->self_enterprise_id();
            $all_data = array();
            $posts = I("post.");
            $num_mobile = 0;
            $num_unicom = 0;
            $num_telecom = 0;
            $probability_mobile = 0;
            $probability_unicom = 0;
            $probability_telecom = 0;
            foreach ($posts as $key => $value) {
            		$data = array();
                $data['user_type'] = $user_type;
                $data['proxy_id'] = $self_proxy_id;
                $data['enterprise_id'] = $self_enterprise_id;
                $data['create_user_id'] = D('SysUser')->self_id();
                $data['create_date'] = date('Y-m-d H:i:s');
                $data['modify_user_id'] = D('SysUser')->self_id();
                $data['modify_date'] = date('Y-m-d H:i:s');
                $data['user_activity_id'] = $user_activity_id;
                

                //判断移动流量包
                if (substr($key, 0, 3) == "yd_"||substr($key, 0, 3) == "lt_"||substr($key, 0, 3) == "dx_") {
                    $product_id = substr($key, 3);
                    if ($value == 2) {// 表示勾选
                    	    $probability = $posts['probability_' . $product_id];

                    		if(substr($key, 0, 3) == "yd_")
                    		{
                    			$operator_id = 1;
                    			$probability_mobile = $probability_mobile + $probability;
                    			$num_mobile = $num_mobile + 1;
                    		}
                    		else if(substr($key, 0, 3) == "lt_")
                    		{
                    			$operator_id = 2;
                    			$probability_unicom = $probability_unicom + $probability;
                    			$num_unicom = $num_unicom + 1;
                    		}
                    		else if(substr($key, 0, 3) == "dx_")
                    		{
                    			$operator_id = 3;
                    			$num_telecom = $num_telecom + 1;
							$probability_telecom = $probability_telecom + $probability;
                    		}
                        if ($num_mobile > 4) {
                            $msg = "设置个数超过4个！";
                            $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
                        }
                       else if ($num_unicom > 4) {
                            $msg = "设置个数超过4个！";
                            $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
                        }
                        else if ($num_telecom > 4) {
                            $msg = "设置个数超过4个！";
                            $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
                        }
                        if ($probability_mobile > 100) {
                            $msg = "设置移动概率超过100%！";
                            $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
                        }
                        else if ($probability_unicom > 100) {
                            $msg = "设置联通概率超过100%！";
                            $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
                        }
                        else if ($probability_telecom > 100) {
                            $msg = "设置电信概率超过100%！";
                            $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
                        }

                        $data['product_id'] = $product_id;
                        $data['probability'] = $probability;
                        $data['operator_id'] = $operator_id;
                        $all_data[] = $data;
                    }
                }
            	}
            	
            	M("scene_configuration")->startTrans();
            $delete_where['user_activity_id'] = $user_activity_id;
            $ms=M("scene_configuration")->where($delete_where)->select();
            if($ms){
                $scene_configuration = M("scene_configuration")->where($delete_where)->delete(); //把流量包先删除
                if (!$scene_configuration) {
                    M("scene_configuration")->rollback();
                    $msg = "配置流量包失败";
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】,配置流量包失败';
                    $this->sys_log('配置流量包',$note);
                    $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
                }
            }
            if(empty($all_data)){
                M("scene_configuration")->commit();
                $msg = "配置流量包成功！";
                $status="success";
                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】,配置流量包成功';
                $this->sys_log('配置流量包',$note);
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
            }
            $tr=M("scene_configuration")->addAll($all_data);
            if($tr){
                M("scene_configuration")->commit();
                $msg = "配置流量包成功！";
                $status="success";
                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】,配置流量包成功';
                $this->sys_log('配置流量包',$note);
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
            }else{
                M("scene_configuration")->rollback();
                $msg = "配置流量包失败";
                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】,配置流量包失败';
                $this->sys_log('配置流量包',$note);
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
            }
        }
    }

    /**
    *导出EXCEL
    **/
    public function export_excel(){
        $activity_name  = I('activity_name');
        $status = I('status');
        !empty($activity_name) && $map['b.activity_name'] = array("like", "%{$activity_name}%");
        //列表出状态和全部
        $newtime=date("Y-m-d H:i:s" ,time());
        if(empty($status) || $status ==1){
            $map1['a.start_date'] = array("elt",$newtime);
            $map1['a.end_date']=array("gt",$newtime);
            $map1['_logic']="and";
            $map[]=$map1;
        }elseif($status ==2){
            $map['a.start_date']=array('gt',$newtime);
        }elseif($status ==3){
            $map['a.end_date']=array('elt',$newtime);
        }

        $self_user_type = D('SysUser')->self_user_type(); //1 2 3
        $user_type = $self_user_type - 1; //1：代理商、2：企业
        $self_proxy_id = intval(D('SysUser')->self_proxy_id());
        $self_enterprise_id = intval(D('SysUser')->self_enterprise_id());
        $map['a.user_type'] = array('eq', $user_type);
        $map['a.proxy_id'] = array('eq', $self_proxy_id);
        $map['a.enterprise_id'] = array('eq', $self_enterprise_id);

        //获取所有角色列表
        $user_activity_list = M('scene_user_activity as a')
            ->where($map)
            ->join(C('DB_PREFIX').'scene_activity as b ON a.activity_id=b.activity_id',"left")
            ->field("a.*,b.activity_name")
            ->order("a.modify_date desc,a.activity_id asc")
            ->limit(3000)
            ->select();

        $datas = array();
        $headArr=array("活动名称","开始时间","结束时间","参与次数","参与频率");

        foreach ($user_activity_list as $v) {
            $data=array();
            $data['activity_name'] = $v['activity_name'];
            $data['start_date'] = $v['start_date'];
            $data['end_date'] = $v['end_date'];
            $data['number'] = ' ' . $v['number'];
            $data['frequency'] = get_scene_frequency($v['frequency']);
            array_push($datas,$data);
        }

        $title='活动管理';

        ExportEexcel($title,$headArr,$datas);
    }

    public function map_lbs(){
        $status="error";
        $where['user_activity_id']=trim(I('user_activity_id'));
        $type=trim(I("get.type"));
        if($type=="update"){
            $data['point']=trim(I("point"));
            $data['accuracy']=trim(I("accuracy"));
            $data['lbs_status']=trim(I("lbs_status"));
            if(empty($data['point']) && $data['lbs_status']==1){
                $msg="请在地图上选择活动位置!";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
            if(empty($data['accuracy']) && $data['lbs_status']==1){
                $msg="请输入活动范围!";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
            if(!empty($data['accuracy']) && !preg_match('/^[0-9]+(.[0-9]{1,2})?$/',$data['accuracy'])){
                $msg="请输入正确的活动范围!";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
            $data['modify_date']=date('Y-m-d H:i:s');
            $data['modify_user_id']=D('SysUser')->self_id();
            $sua=M("scene_user_activity")->where($where)->save($data);
            if($sua){
                $msg="LBS定制设置成功!";
                $status="success";
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
            }else{
                $msg="LBS定制设置失败!";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
        }else{
            $info=M("scene_user_activity")->where($where)->field("user_activity_id,lbs_status,point,accuracy")->find();
            if(empty($info['lbs_status'])){
                $info['lbs_status']=2;
            }
            $this->assign("info",$info);
            $this->display();
        }
    }

    public function detailed(){
        $user_activity_id=I("user_activity_id");
        $type = I('get.type');
        if($type == "operation") {
            $self_user_id = D('SysUser')->self_id();
            //保存场景基本信息
            $post = I("post.");
            $status = "error";
            $msg = "流量活动设置保存失败！";
            if (!empty($_FILES)) {
                $fileinfo = $this->scene_base_upload(C('ENTERPRISE_SCENE_UPLOAD_DIR'));
                $error = $this->business_licence_upload_Error;
                if($error){
                    if($error['propagandat_img'] && $error['propagandat_img'] != '没有文件被上传！'){
                        $msg = '宣传图'.$error['propagandat_img'];
                        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                    }
                    if($error['logo_img'] && $error['logo_img'] != '没有文件被上传！'){
                        $msg = 'LOGO图片'.$error['logo_img'];
                        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                    }
                    if($error['background_img'] && $error['background_img'] != '没有文件被上传！'){
                        $msg = '背景图'.$error['background_img'];
                        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                    }
                    if($error['share_img'] && $error['share_img'] != '没有文件被上传！'){
                        $msg = '分享图'.$error['share_img'];
                        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                    }
                }

                if($fileinfo['propagandat_img']){
                    $propagandat_img = substr(C('UPLOAD_DIR').$fileinfo['propagandat_img']['savepath'].$fileinfo['propagandat_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['propagandat_img']['savepath'].$fileinfo['propagandat_img']['savename'])-1);
                }else{
                    $propagandat_img = '';
                }
                if($fileinfo['logo_img']){
                    $logo_img = substr(C('UPLOAD_DIR').$fileinfo['logo_img']['savepath'].$fileinfo['logo_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['logo_img']['savepath'].$fileinfo['logo_img']['savename'])-1);
                }else{
                    $logo_img = '';
                }
                if($fileinfo['background_img']){
                    $background_img = substr(C('UPLOAD_DIR').$fileinfo['background_img']['savepath'].$fileinfo['background_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['background_img']['savepath'].$fileinfo['background_img']['savename'])-1);
                }else{
                    $background_img = '';
                }
                if($fileinfo['share_img']){
                    $share_img = substr(C('UPLOAD_DIR').$fileinfo['share_img']['savepath'].$fileinfo['share_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['share_img']['savepath'].$fileinfo['share_img']['savename'])-1);
                }else{
                    $share_img = '';
                }
            } else {
                //write_debug_log(array(__METHOD__.'：'.__LINE__, 222));
            }

            //$user_id = (1 == $user_type) ? $self_proxy_id : $self_enterprise_id;

            $upd = array(
                'modify_user_id'    => $self_user_id,
                'modify_date'       => date('Y-m-d H:i:s'),
            );
            !empty($propagandat_img) && $upd['propagandat_img'] = $propagandat_img;
            !empty($logo_img) && $upd['logo_img'] = $logo_img;
            !empty($background_img) && $upd['background_img'] = $background_img;
            !empty($share_img) && $upd['share_img'] = $share_img;
            $mb_b=I("mb_b");
            if($mb_b){
                $upd['background_img']=get_img($mb_b);
            }
            M("")->startTrans();
            $upd['share_title']=$post['share_title'];
            $upd['share_content']=$post['share_content'];
            $upd['share_url'] = $post['share_url'];
            $rt = M('scene_user_activity')->where("user_activity_id={$user_activity_id}")->save($upd);
            if(false !== $rt) {
                $status = "success";
                M("")->commit();
                $msg = "流量活动设置保存成功";
            } else {
                M("")->rollback();
                write_error_log(array(__METHOD__.':'.__LINE__,'sql== '.M()->getLastSql()));
            }
            IS_AJAX && $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
        } else {
            //读取场景基本信息
            $list = M('scene_user_activity')->where(array('user_activity_id'=>$user_activity_id))->find();
            if(empty($list['propagandat_img'])){
                $self_enterprise_id = D('SysUser')->self_enterprise_id();
                $info=M('scene_info')->where(array("enterprise_id"=>$self_enterprise_id))->find();
                if(empty($info['propagandat_img'])){
                    $list['propagandat_img']='/Public/Uploads/./Enterprise_scene/2016-05-10/231cee0aceab9.png';
                    $list['logo_img']='/Public/Uploads/./Enterprise_scene/2016-05-10/570cee323eab9.png';
                    $list['background_img']='/Public/Uploads/./Enterprise_scene/2016-05-10/232cee0aceab9.png';
                    M('scene_user_activity')->save($list);
                }else{
                    $list['propagandat_img']=$info['propagandat_img'];
                    $list['logo_img']=$info['logo_img'];
                    $list['background_img']=$info['background_img'];
                    $list['share_title']=$info['share_title'];
                    $list['share_content']=$info['share_content'];
                    $list['share_url'] = $info['share_url'];
                    $list['share_img']=$info['share_img'];
                    M('scene_user_activity')->save($list);
                }
            }
            $data=get_img();
            foreach ($data as $k=>$v){
                if($v['img_url']==$list['background_img']){
                    $data[$k]['status']="selected";
                }
            }
            $this->assign("data",$data);
            $this->assign("list",$list);
            $this->display();
        }
    }
    public function img_download() {

        $msg = '系统错误！';
        $status = 'error';
        $mb = I("mb");
        $self_user_type = D('SysUser')->self_user_type(); //1 2 3
        $user_type = $self_user_type - 1; //1：代理商、2：企业
        $self_proxy_id = D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        $user_activity_id=trim(I("get.user_activity_id"));
        $list = M('scene_user_activity')->where(array('user_activity_id'=>$user_activity_id))->find();;
        $type = trim(I('get.download'));
        if(in_array($type,array('propagandat_img','logo_img','background_img','share_img'))) {
            if($type=="background_img" && !empty($mb)){
                $da=get_img($mb);
                parent::download('.'.$da);
            }else {
                parent::download('.'.$list[$type]);
            }
        }

    }
}
