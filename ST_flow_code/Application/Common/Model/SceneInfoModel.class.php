<?php

namespace Common\Model;
use Think\Model;

class SceneInfoModel extends Model {

    /**
     * 获取场景基本信息表数据(没有就新增一条记录) 1D
     */
    public function get_scene_info($user_type, $proxy_id, $enterprise_id) {
		$ret = array();
        if(is_numeric($user_type)) {
            $proxy_id       = intval($proxy_id);
            $enterprise_id  = intval($enterprise_id);

            $cond  = array(
                'user_type'     => $user_type,
                'proxy_id'      => $proxy_id,
                'enterprise_id' => $enterprise_id,
            );
            $ret = M('scene_info')->where($cond)->find();
            if(empty($ret)) {
                $self_user_id = D('SysUser')->self_id();
                $peid = (1 == $user_type) ? $proxy_id : $enterprise_id;
                $ins = array(
                    'user_type'         => $user_type,
                    'proxy_id'          => $proxy_id,
                    'enterprise_id'     => $enterprise_id,
                    'propagandat_img'   => '/Public/Uploads/./Enterprise_scene/2016-05-10/231cee0aceab9.png',
                    'logo_img'          => '/Public/Uploads/./Enterprise_scene/2016-05-10/570cee323eab9.png',
                    'background_img'    => '/Public/Uploads/./Enterprise_scene/2016-05-10/232cee0aceab9.png',
                    'redpack_address'   => gethostwithhttp()."/index.php/Sdk/FlowRed/index/user_type/{$user_type}/user_id/{$peid}",
                    'recharge_address'  => gethostwithhttp()."/index.php/Sdk/WxFlowPayment/index/user_type/{$user_type}/user_id/{$peid}",
                    'create_user_id'    => $self_user_id,
                    'create_date'       => date('Y-m-d H:i:s'),
                    'modify_user_id'    => $self_user_id,
                    'modify_date'       => date('Y-m-d H:i:s'),
                );
                $info_id = M('scene_info')->add($ins);
                if(!empty($info_id)) {
                    $ins['info_id'] = $info_id;
                } else {
                    write_error_log(array(__METHOD__.':'.__LINE__, '添加场景基本信息错误 sql== '.M()->getLastSql()));
                }
                $ret = $ins;
            }
        }
        return $ret;
	}

    /**
     * 获取场景收款设置数据(没有就新增一条记录) 1D
     */
    public function get_scene_user_set($user_type, $proxy_id, $enterprise_id) {
        $ret = array();
        if(is_numeric($user_type)) {
            $proxy_id       = intval($proxy_id);
            $enterprise_id  = intval($enterprise_id);

            $cond  = array(
                'user_type'     => $user_type,
                'proxy_id'      => $proxy_id,
                'enterprise_id' => $enterprise_id,
            );
            $ret = M('user_set')->where($cond)->find();
            if(empty($ret)) {
                $self_user_id = D('SysUser')->self_id();
                $ins = array(
                    'user_type'         => $user_type,
                    'proxy_id'          => $proxy_id,
                    'enterprise_id'     => $enterprise_id,
                    'wx_appid'          => '',
                    'wx_appsecret'      => '',
                    'wx_mchid'          => '',
                    'wx_key'            => '',
                    'wx_pem_file_one'   => '',
                    'wx_pem_file_two'   => '',
                    'alipay_partner'    => '',
                    'alipay_key'        => '',
                    'alipay_pem_file'   => '',
                    'create_user_id'    => $self_user_id,
                    'create_date'       => date('Y-m-d H:i:s'),
                    'modify_user_id'    => $self_user_id,
                    'modify_date'       => date('Y-m-d H:i:s'),
                    'payment_type'      => 2, //收款方式（1：表示运营方收款、2：表示企业收款）
                );
                $account_id = M('user_set')->add($ins);
                if(!empty($account_id)) {
                    $ins['account_id'] = $account_id;
                } else {
                    write_error_log(array(__METHOD__.':'.__LINE__, '添加场景收款设置错误 sql== '.M()->getLastSql()));
                }
                $ret = $ins;
            }
        }
        return $ret;
    }

    /**
     * 获取所有的活动信息基础数据
     */
    public function get_scene_activity_all() {
        $ret = M('scene_activity')->select();
        return $ret;
    }

    public function get_scene_activity_byid($id) {
        $ret = M('scene_activity')->where("activity_id={$id}")->find();
        return $ret;
    }

    /**
     * 根据ID获取用户活动信息
     */
    public function get_user_activity_byid($uaid) {
        $ret = M('scene_user_activity sua')
            ->join("left join ".C('DB_PREFIX')."scene_activity sa on sua.activity_id = sa.activity_id")
            ->where("sua.user_activity_id={$uaid}")
            ->field('sua.*,sa.activity_name')
            ->find();
        return empty($ret) ? array() : $ret;
    }

    /**
     * 获取领取记录列表
     */
    public function get_scene_record_lists($where){
        $use_t=D("SysUser")->self_user_type();
        if($use_t==1){
            $proxys=D('Proxy')->proxy_child_ids();
            if($proxys){
                $where1['p.proxy_id']= array('in',$proxys);
            }else{
                $where1['p.proxy_id']=-1;
            }
            $enterprises=D("Enterprise")->enterprise_child_ids();
            if($enterprises){
                $where1['e.enterprise_id']=array('in',$enterprises);
            }else{
                $where1['e.enterprise_id']=-1;
            }
            $where1['_logic']="or";
            $where[]=$where1;
        }
        if($use_t==2) {
            $proxys=D('Proxy')->proxy_child_ids();
            $self_proxy_id=D('SysUser')->self_proxy_id();
            if($proxys){
                $stat['p.proxy_id'] = array('in',$proxys);
                $stat['p.top_proxy_id'] = array('eq',$self_proxy_id);
                $stat['_logic'] = 'and';
                $map['_complex'] = $stat;
                $map['p.proxy_id'] = array('eq',$self_proxy_id);
                $map['_logic'] = 'or';
                $where1[]=$map;
            }else{
                $where1['p.proxy_id']=$self_proxy_id;
            }
            $enterprises=D("Enterprise")->enterprise_ids();//获取该用户可操作的企业号();
            if($enterprises){
                $where1['e.enterprise_id']=array("in",$enterprises);
            }else{
                $where1['e.enterprise_id']=-1;
            }
            $where1['_logic']= "or";
            $where[]=$where1;
        }
        $model=M('scene_record  sr');
        $count =$model
            ->join('left join t_flow_scene_user_activity as sua on sua.user_activity_id =  sr.user_activity_id')
            ->join('left join t_flow_scene_activity as sa on sa.activity_id = sua.activity_id')
            ->join('left join t_flow_proxy as p on p.proxy_id = sr.proxy_id and sr.user_type=1 and p.status=1 and p.approve_status=1')
            ->join('left join t_flow_enterprise as e on e.enterprise_id= sr.enterprise_id and sr.user_type=2 and e.status=1 and e.approve_status=1')
            ->where($where)
            ->count();
        $Page = new \Think\Page($count,20);
        $show = $Page->show();
        $list =$model
            ->join('left join t_flow_scene_user_activity as sua on sua.user_activity_id=sr.user_activity_id ')
            ->join('left join t_flow_scene_activity as sa on sa.activity_id = sua.activity_id')
            ->join('left join t_flow_proxy as p on p.proxy_id = sr.proxy_id and sr.user_type=1 and p.status=1 and p.approve_status=1')
            ->join('left join t_flow_enterprise as e on e.enterprise_id= sr.enterprise_id and sr.user_type=2 and e.status=1 and e.approve_status=1')
            ->join('left join t_flow_order as o on o.order_code=sr.order_id')
            ->field('sr.record_id,sr.user_type,sua.user_activity_name,sr.proxy_id,p.proxy_name,e.enterprise_name,sr.enterprise_id,sr.order_id,sr.user_activity_id,sr.openid,sr.wx_photo,sr.wx_name,sr.mobile,sr.product_name,sr.receive_date,sa.activity_name,o.operator_id,o.discount_price,o.order_status')
            ->where($where)
            ->order('sr.record_id desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show);
    }

    /**
     * 获取领取记录详情
     */
    public function get_scene_record_detail($id){
        $where['sr.record_id'] = $id;
        $model=M('scene_record  sr');
        $info =$model
            ->join('left join t_flow_scene_user_activity as sua on sua.user_activity_id=sr.user_activity_id ')
            ->join('left join t_flow_scene_activity as sa on sa.activity_id = sua.activity_id')
            ->join('left join t_flow_proxy as p on p.proxy_id = sr.proxy_id and sr.user_type=1 and p.status=1 and p.approve_status=1')
            ->join('left join t_flow_enterprise as e on e.enterprise_id= sr.enterprise_id and sr.user_type=2 and e.status=1 and e.approve_status=1')
            ->join('left join t_flow_order as o on o.order_code=sr.order_id')
            ->field('sr.record_id,sr.user_type,sua.user_activity_name,sr.proxy_id,sr.enterprise_id,sr.order_id,sr.user_activity_id,sr.openid,sr.wx_photo,sr.wx_name,sr.mobile,sr.product_name,sr.receive_date,sa.activity_name,sua.activity_rule,p.proxy_name,e.enterprise_name,o.discount_price,o.order_status')
            ->where($where)
            ->find();
        return $info;
    }

    //购买记录
    public function get_pay_order_list($where){
        $use_t=D("SysUser")->self_user_type();
        if($use_t==1){
            $proxys=D('Proxy')->proxy_child_ids();
            if($proxys){
                $where1['p.proxy_id']= array('in',$proxys);
            }else{
                $where1['p.proxy_id']=-1;
            }
            $enterprises=D("Enterprise")->enterprise_child_ids();
            if($enterprises){
                $where1['e.enterprise_id']=array('in',$enterprises);
            }else{
                $where1['e.enterprise_id']=-1;
            }
            $where1['_logic']="or";
            $where[]=$where1;
        }
        if($use_t==2) {
            $proxys=D('Proxy')->proxy_child_ids();
            $self_proxy_id=D('SysUser')->self_proxy_id();
            if($proxys){
                $stat['p.proxy_id'] = array('in',$proxys);
                $stat['p.top_proxy_id'] = array('eq',$self_proxy_id);
                $stat['_logic'] = 'and';
                $map['_complex'] = $stat;
                $map['p.proxy_id'] = array('eq',$self_proxy_id);
                $map['_logic'] = 'or';
                $where1[]=$map;
            }else{
                $where1['p.proxy_id']=$self_proxy_id;
            }
            $enterprises=D("Enterprise")->enterprise_ids();//获取该用户可操作的企业号();
            if($enterprises){
                $where1['e.enterprise_id']=array("in",$enterprises);
            }else{
                $where1['e.enterprise_id']=-1;
            }
            $where1['_logic']= "or";
            $where[]=$where1;
        }
        $where['pay_status']=2;//表示购买成功
        $model=M('pay_order po');
        $count =$model
            ->join('left join t_flow_channel_product as cp on cp.product_id = po.product_id')
            ->join('left join t_flow_proxy as p on p.proxy_id = po.proxy_id and po.user_type=1')
            ->join('left join t_flow_order as o on o.order_code= po.order_code')
            ->join('left join t_flow_enterprise as e on e.enterprise_id= po.enterprise_id and po.user_type=2')
            ->where($where)
        ->count();
        $Page = new \Think\Page($count,20);
        $show = $Page->show();
        $list =$model
            ->join('left join t_flow_channel_product as cp on cp.product_id = po.product_id')
            ->join('left join t_flow_proxy as p on p.proxy_id = po.proxy_id and po.user_type=1')
            ->join('left join t_flow_enterprise as e on e.enterprise_id= po.enterprise_id and po.user_type=2')
            ->join('left join t_flow_order as o on o.order_code= po.order_code')
            ->field('po.pay_order_id,po.pay_order_code,po.mobile,po.order_code,po.pay_date,po.price,po.discount_price,po.payment_type,po.deduct_price,po.pay_type,cp.operator_id,cp.product_name,po.user_type,p.proxy_name,e.enterprise_name,o.order_status,po.refund_status,po.recharge_sources')
            ->where($where)
            ->order('po.pay_date desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show);
    }

    //查看购买记录
    public function get_pay_order_record_detail($id){
        $where['po.pay_order_id'] = $id;
        $model=M('pay_order po');
        $info =$model
            ->join('left join t_flow_channel_product as cp on cp.product_id = po.product_id')
            ->join('left join t_flow_proxy as p on p.proxy_id = po.proxy_id and po.user_type=1')
            ->join('left join t_flow_enterprise as e on e.enterprise_id= po.enterprise_id and po.user_type=2')
            ->join('left join t_flow_order as o on o.order_code= po.order_code')
            ->field('po.pay_order_id,po.pay_order_code,po.mobile,po.order_code,po.pay_date,po.price,po.discount_price,cp.operator_id,cp.product_name,po.user_type,p.proxy_name,e.enterprise_name,o.order_status,po.remark,po.recharge_sources')
            ->where($where)
            ->find();
        return $info;
    }

    //红包购买记录
    public function get_pay_red_list($where){
        $use_t=D("SysUser")->self_user_type();
        if($use_t==1){
            $proxys=D('Proxy')->proxy_child_ids();
            if($proxys){
                $where1['p.proxy_id']= array('in',$proxys);
            }else{
                $where1['p.proxy_id']=-1;
            }
            $enterprises=D("Enterprise")->enterprise_child_ids();
            if($enterprises){
                $where1['e.enterprise_id']=array('in',$enterprises);
            }else{
                $where1['e.enterprise_id']=-1;
            }
            $where1['_logic']="or";
            $where[]=$where1;
        }
        if($use_t==2) {
            $proxys=D('Proxy')->proxy_child_ids();
            $self_proxy_id=D('SysUser')->self_proxy_id();
            if($proxys){
                $stat['p.proxy_id'] = array('in',$proxys);
                $stat['p.top_proxy_id'] = array('eq',$self_proxy_id);
                $stat['_logic'] = 'and';
                $map['_complex'] = $stat;
                $map['p.proxy_id'] = array('eq',$self_proxy_id);
                $map['_logic'] = 'or';
                $where1[]=$map;
            }else{
                $where1['p.proxy_id']=$self_proxy_id;
            }
            $enterprises=D("Enterprise")->enterprise_ids();//获取该用户可操作的企业号();
            if($enterprises){
                $where1['e.enterprise_id']=array("in",$enterprises);
            }else{
                $where1['e.enterprise_id']=-1;
            }
            $where1['_logic']= "or";
            $where[]=$where1;
        }
        $where['pay_status']=2;//表示购买成功
        $model=M('red_order ro');
        $count =$model
            ->join('left join t_flow_proxy as p on p.proxy_id = ro.proxy_id and ro.user_type=1')
            ->join('left join t_flow_enterprise as e on e.enterprise_id= ro.enterprise_id and ro.user_type=2')
            ->where($where)
            ->count();
        $Page = new \Think\Page($count,20);
        $show = $Page->show();

        $list =$model
            ->join('left join t_flow_proxy as p on p.proxy_id = ro.proxy_id and ro.user_type=1')
            ->join('left join t_flow_enterprise as e on e.enterprise_id= ro.enterprise_id and ro.user_type=2')
            ->field('ro.red_order_id,ro.red_order_code,ro.wx_openid,ro.packages,ro.out_packages,ro.pay_price,ro.payment_type,ro.pay_type,ro.discount_price,ro.pay_date,p.proxy_name,e.enterprise_name')
            ->where($where)
            ->order('ro.pay_date desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show);
    }

    //红包导出
    public function get_pay_red_excel($where){
        $use_t=D("SysUser")->self_user_type();
        if($use_t==1){
            $proxys=D('Proxy')->proxy_child_ids();
            if($proxys){
                $where1['p.proxy_id']= array('in',$proxys);
            }else{
                $where1['p.proxy_id']=-1;
            }
            $enterprises=D("Enterprise")->enterprise_child_ids();
            if($enterprises){
                $where1['e.enterprise_id']=array('in',$enterprises);
            }else{
                $where1['e.enterprise_id']=-1;
            }
            $where1['_logic']="or";
            $where[]=$where1;
        }
        if($use_t==2) {
            $proxys=D('Proxy')->proxy_child_ids();
            $self_proxy_id=D('SysUser')->self_proxy_id();
            if($proxys){
                $stat['p.proxy_id'] = array('in',$proxys);
                $stat['p.top_proxy_id'] = array('eq',$self_proxy_id);
                $stat['_logic'] = 'and';
                $map['_complex'] = $stat;
                $map['p.proxy_id'] = array('eq',$self_proxy_id);
                $map['_logic'] = 'or';
                $where1[]=$map;
            }else{
                $where1['p.proxy_id']=$self_proxy_id;
            }
            $enterprises=D("Enterprise")->enterprise_ids();//获取该用户可操作的企业号();
            if($enterprises){
                $where1['e.enterprise_id']=array("in",$enterprises);
            }else{
                $where1['e.enterprise_id']=-1;
            }
            $where1['_logic']= "or";
            $where[]=$where1;
        }
        $where['pay_status']=2;//表示购买成功
        $model=M('red_order ro');
        $list =$model
            ->join('left join t_flow_proxy as p on p.proxy_id = ro.proxy_id and ro.user_type=1')
            ->join('left join t_flow_enterprise as e on e.enterprise_id= ro.enterprise_id and ro.user_type=2')
            ->field('ro.red_order_id,ro.red_order_code,ro.wx_openid,ro.packages,ro.out_packages,ro.pay_price,ro.payment_type,ro.pay_type,ro.discount_price,ro.pay_date,p.proxy_name,e.enterprise_name')
            ->where($where)
            ->order('ro.pay_date desc')
            ->limit(3000)
            ->select();
        return $list;
    }

    //判断是否需要显示退款按钮  refund_type:1为需要，2为不需要
    public function get_refund_type($list){
        $reds=array();
        foreach($list as $v){
            if(empty($v['out_packages'])){
                $v['refund_type']=1;
                array_push($reds,$v);
                continue;
        }
            $packages=explode(',',$v['packages']);
            $out_packagets=explode(',',$v['out_packages']);
            if(count($packages)!=count($out_packagets)) {
                $v['refund_type'] = 1;
                array_push($reds, $v);
                continue;
            }
            $records=M("red_record as rr")
                ->join('left join t_flow_order as o on o.order_code= rr.order_id')
                ->where(array("rr.red_order_id"=>$v['red_order_id']))
                ->field("rr.order_id,o.order_status,rr.refund_status")
                ->select();
            if(empty($records)){
                $v['refund_type'] =2;
                array_push($reds,$v);
                continue;
            }
            foreach ($records as $r){
                $type=2;
                $v['aa']=$r["refund_status"];
                if($r["refund_status"]!=2){
                    if($r['order_id']== null || $r['order_id']==""){
                        $type=1;
                        break;
                    }elseif($r['order_status']==6){
                        $type=1;
                        break;
                    }
                }
                $v['aaa']=$type;
            }
            if($type==1){
                $v['refund_type'] = 1;
                array_push($reds, $v);
                continue;
            }
            $v['refund_type'] =2;
            array_push($reds,$v);
        }
        return $reds;
    }

    //查看红包记录
    public function get_pay_red_record_detail($red_order_id,$type){
        $msg="系统错误！";
        $status="error";
        $where['red_order_id'] = $red_order_id;
        $ro=M('red_order')->where($where)->find();
        if(empty($ro)){
            return;
        }
        $packages=explode(',',$ro['packages']);
        $out_packages=explode(',',$ro['out_packages']);
        $result=array();
        $i=1;
        $refund_fee=0;
        $products=",";
        $records=",";
        $discount=explode(',',$ro['discount']);
        if(empty($ro['discount'])){
            if($ro['user_type']==1){
                $discountdata['user_type'] = 1;
                $discountdata['proxy_id'] = $ro['proxy_id'];
            }else{
                $discountdata['user_type'] = 2;
                $discountdata['enterprise_id'] = $ro['enterprise_id'];
            }
            $dicounts= M('person_discount')->where($discountdata)->find();
            array_push($discount,$dicounts['mobile_discount']);
            array_push($discount,$dicounts['unicom_discount']);
            array_push($discount,$dicounts['telecom_discount']);
        }
        foreach ($packages as $p) {
            $or=array();
            $where=array();
            $where['product_id']=$p;
            if(in_array($p,$out_packages)) {
                foreach ($out_packages as $k1 => $v1) {
                    if ($v1 == $p) {
                        $out_packages[$k1] = 0;
                        break;
                    }
                }
                $product = M("channel_product")->where($where)->find();
                $where['red_order_id'] = $red_order_id;
                $or['operator_id'] = $product['operator_id'];
                $or['product_name'] = $product["product_name"];
                $or['pay_type']=$ro['pay_type'];
                $num = M("red_record")->where($where)->count();
                $r_id = 0;
                if ($num == 0) {
                    $or['sort_no']=$i;
                    $or['info'] = "1";
                    $or['receive_date'] = "";
                    $or['refund_status'] = "2";
                    $or['status']="6";
                } else{
                    $ids = M("red_record")->where($where)->field("red_record_id")->select();
                    foreach ($ids as $id) {
                        $status = 1;
                        foreach ($result as $r) {
                            if ($id["red_record_id"] == $r['red_record_id']) {
                                $status = 2;
                                break;
                            }
                        }
                        if ($status != 2) {
                            $r_id = $id['red_record_id'];
                            break;
                        }
                    }
                    if ($r_id == 0) {
                        $or['sort_no']=$i;
                        $or['info'] = "1";
                        $or['receive_date'] = "";
                        $or['refund_status'] = "2";
                        $or['status']="6";
                    }else{
                        $map['red_record_id'] = $r_id;
                        $rec = M("red_record")->where($map)->find();
                        if(empty($rec['order_id'])){
                            $or['status']=6;
                        }else{
                            $orders=M("order")->where(array("order_code"=>$rec['order_id']))->find();
                            if(empty($orders['order_status'])){
                                $or['status']=1;
                            }else{
                                $or['status']=$orders['order_status'];
                            }
                        }
                        $or['sort_no']=$i;
                        $or['mobile'] = $rec['mobile'];
                        $or['order_id']=$rec['order_id'];
                        if($or['status']==6 && $rec['refund_status']!=2){
                            if($product['operator_id']==1){
                                $dicountData=$discount[0];
                            }elseif($product['operator_id']==2){
                                $dicountData=$discount[1];
                            }else{
                                $dicountData=$discount[2];
                            }
                            $fee=round($product['price']*$dicountData/10.0,2);
                            $refund_fee=$refund_fee+$fee;
                            $records=$records.$r_id.",";
                        }
                        $or['red_record_id'] = $rec['red_record_id'];
                        $or['info'] = "2";//领取情况 1.未领取 2.已领取
                        $or['receive_date'] = $rec['receive_date'];
                        $or['refund_status'] = $rec['refund_status'];
                    }
                }
            }else{
                $product=M("channel_product")->where($where)->find();
                $where['red_order_id']=$red_order_id;
                $or['sort_no']=$i;
                $or['operator_id']=$product['operator_id'];
                $or['product_name']=$product["product_name"];
                $or['info']="1";
                $or['status']="6";
                $or['receive_date']="";
                $or['pay_type']=$ro['pay_type'];
                $or['refund_status']="1";
                if($product['operator_id']==1){
                    $dicountData=$discount[0];
                }elseif($product['operator_id']==2){
                    $dicountData=$discount[1];
                }else{
                    $dicountData=$discount[2];
                }
                $fee=round($product['price']*$dicountData/10.0,2);
                $refund_fee=$refund_fee+$fee;
                $products=$products.$p.",";
            }
            array_push($result, $or);
            $i++;
        }
        if($type==2){
            $result=array();
            $result['red_order_code']=$ro['red_order_code'];
            $result['pay_price']=$ro['pay_price'];
            $result['number']=$ro['number'];
            $result['user_type']=$ro['user_type'];
            $result['proxy_id']=$ro['proxy_id'];
            $result['pay_type']=$ro['pay_type'];
            $result['enterprise_id']=$ro['enterprise_id'];
            $result['refund_fee']=$refund_fee;
            $result['products']=$products;
            $result['records']=$records;
            return $result;
        }else{
            return $result;
        }
    }
    // 积分兑换记录
    public function get_point_value_pay_order_list($where,$page) {
        $rowCount = 6;
        $model = M('exchange_record er');
        $count = $model ->join('left join t_flow_order as o on er.order_code = o.order_code') -> join('left join t_flow_channel_product as cp on cp.product_id = er.product_id') -> field('er.order_code,er.mobile,er.order_date,o.back_fail_desc,er.exchange_score_id,cp.size') -> where($where) -> order('er.order_date desc') -> count();


        $list = $model -> join('left join t_flow_order as o on er.order_code = o.order_code') -> join('left join t_flow_channel_product as cp on cp.product_id = er.product_id') -> field('er.order_code,er.mobile,er.order_date,o.back_fail_desc,er.exchange_score_id,cp.size,o.order_status,er.exchange_score,er.refund_status') -> where($where) -> order('er.order_date desc') -> limit($rowCount)->page($page) -> select();
        $pageCount = 0;
        $pageCount = (int)($count/$rowCount);
        $pageCount = $pageCount+1;
        if($count%$rowCount == 0)
        {
            $pageCount = $pageCount -1;
        }
        //		$list = $model -> join('left join t_flow_channel_product as cp on cp.product_id = po.product_id') -> join('left join t_flow_proxy as p on p.proxy_id = po.proxy_id and po.user_type=1') -> join('left join t_flow_enterprise as e on e.enterprise_id= po.enterprise_id and po.user_type=2') -> join('left join t_flow_order as o on o.order_code= po.order_code') -> field('po.pay_order_id,po.pay_order_code,po.mobile,po.order_code,po.pay_date,po.price,po.discount_price,po.payment_type,cp.operator_id,cp.product_name,po.user_type,p.proxy_name,e.enterprise_name,o.order_status,po.refund_status') -> where($where) -> order('po.pay_date desc') -> limit($Page -> firstRow . ',' . $Page -> listRows) -> select();
        $resultData['list'] = $list;
        $resultData['pageCount'] = $pageCount;
        $resultData['pageNum'] = $page;
        return $resultData;
    }

}