<?php

/*
 * CollectionSetController
 * 代理商编辑收款设置
 *
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class CollectionSetController extends CommonController{
    public function index(){
        D("SysUser")->sessionwriteclose();
        $enterprise_code=trim(I("enterprise_code"));
        $enterprise_name=trim(I("enterprise_name"));
        if(!empty($enterprise_code)){
            $where['enterprise_code']=array('like',"%".$enterprise_code."%");
        }
        if(!empty($enterprise_name)){
            $where['enterprise_name']=array('like',"%".$enterprise_name."%");
        }
        $self_proxy_id=D("SysUser")->self_proxy_id();
        $map['top_proxy_id']=$self_proxy_id;
        $map['stauts']=1;
        $map['approve_status']=1;
        $count      = M('Enterprise')
           ->where($map)
           ->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        //获取所有通道列表
        $enterprises=D("Enterprise")->enterprise_ids();//获取该用户可操作的企业号();
        if($enterprises){
            $where['enterprise_id']=array("in",$enterprises);
        }else{
            $where['enterprise_id']=-1;
        }
        $cset_list = M('Enterprise')
            ->where($map)
            ->order("modify_date desc")
            ->field("enterprise_id,enterprise_name,enterprise_code")
            ->limit($Page->firstRow.','.$Page->listRows)
            ->where($where)
            ->select();
        $this->assign('cset_list',get_sort_no($cset_list,$Page->firstRow));
        $this->display();
    }
    public function edit_collection(){
        $msg="系统错误";
        $status="error";
        $type=I('get.type');
        $enterprise_id=I("enterprise_id");
        $self_user_type = D('SysUser')->self_user_type(); //1 2 3
        $user_type = $self_user_type - 1; //1：代理商、2：企业
        $self_user_id = D('SysUser')->self_id();

        //显示企业设置收款页面
        if($type=="show"){
            $where['e.enterprise_id']=$enterprise_id;
            $uset=M("enterprise as e")->join("t_flow_user_set as us on us.enterprise_id=e.enterprise_id","left")->where($where)->field("e.enterprise_id,e.enterprise_name,us.account_id,us.wx_appid,us.wx_appsecret,us.wx_mchid,us.wx_key,us.wx_pem_file_one,us.wx_pem_file_two,us.explanation,us.consumer_phone")->find();
            if($uset){
                if(empty($uset['account_id'])){
                    $us['enterprise_id']=$enterprise_id;
                    $id=M("user_set")->add($us);
                    if(empty($id)){
                        $msg="参数错误！";
                        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                    }
                    $us['account_id']=$id;
                }
                $data=$this->localencode("2,".$enterprise_id);
                $uset['redpack_address']=gethostwithhttp()."/index.php/Sdk/FlowRed/aindex?".$data;
                $uset['recharge_address']=gethostwithhttp()."/index.php/Sdk/WxFlowPayment/aindex?".$data;
                if(empty($uset['explanation'])){
                    $uset['explanation']="1、为什么我在充值的时候，提示我“该号码不可充流量”？
根据相关规定，对于部分如非3G号码/欠费/非实名制/运营商黑名单用户，暂时不能使用流量充值服务。对于实名制等业务的办理方法，建议联系归属地运营商。

2、为什么我充值后，使用流量还被运营商扣了额外的费用？
充值完成后，第三方中间服务商可能因为网络原因没有及时将充值电子订单发送至运营商，运营商对您的充值情况不知情。运营商会在收到第三方服务方订单后为您充值，并在成功后为您发送到账短信。请确保您在收到到账短信后再使用流量，以防出现流量包之外的费用。出现流量不到账的时候，建议您先参考信息5，确认您的订单情况。

3、充值的流量可以漫游吗?
充值时请关注页面提示，如果显示“全国可用”表示可以全国漫游。

4、充值的流量有效期是多长?
大部分省份运营商充值流量当月有效，月底失效，部分面额30天内有效及三个月有效，灵活账期用户月结日失效。请以短信通知及在运营商处查询的信息为准。

5、我充值后怎么查看是否到账?
充值后一般10分钟-30分钟内流量会到账，同时会收到运营商官方号码（10086、10010、10000）发来的短信通知，如果收到通知即到到账。
也有部分情况（每个月的5号之前、每个月最后两天）由于运营商BOSS系统延时，会收不到短信（或短信通知较晚）。
出现上述情况时可以有两种方式可明确是否到账：
第一、直接致电运营商官方客服查询即可。
第二、登陆运营商官方网站查询即可。

6、流量充错号码怎么办?
非常抱歉，充错号码后运营商（移动、联通、电信）是不会办理退款的。 由于充值成功后，交易就已经完成，运营商不会将已经充上的流量退还给供货商，所以我们也无法给您办理退款。您可以选择如下几种方式尝试弥补损失： 1、联系实际充值的号码机主，与对方协商是否愿意为此补偿您的流量； 2、联系运营商客服（移动10086、联通10010、电信10000），咨询是否能够退还已经充值成功的流量； 给您带来的不便，尽请谅解！并希望您在下次操作的时候注意核对号码是否正确，谢谢您的支持！

7、充值失败后，为什么我没有收到退款?
通常情况下，充值失败时我们会立即为您办理退款，如果您使用银行卡或者零钱支付，退款会立即退回微信钱包。信用卡退款时间可能会较长，您可以直接致电银行或登录网银查看退款情况。

8、为什么我的号码一直充值失败？
由于联通存在每个面额每个月充值5次的限制，充值超过5次（不限微信平台）将会出现失败退款。建议您充值其他面额的流量。
另外，号码欠费、套餐互斥、非实名认证、运营商黑名单等原因也会导致充值失败退款。

9、充值流量后能取消吗？
不可以。流量充值接近实时交易，支付完成后，交易系统会在数秒中向运营商发起充值请求并且充值到账。充值中的订单也会锁定，无法进行资金回退交易。您需要在充值前确定需要充值流量。     ";
                }
                $this->assign("uset",$uset);
                $this->display();
            }else{
                $msg="参数错误";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
        }
        if($type=="update") {
            $fileinfo = $this->scene_pem_upload(C('ENTERPRISE_SCENE_UPLOAD_DIR') . "/");
            if ($fileinfo['wx_pem_file_one']) {
                $wx_pem_file_one = substr(C('UPLOAD_DIR') . $fileinfo['wx_pem_file_one']['savepath'] . $fileinfo['wx_pem_file_one']['savename'], 1, strlen(C('UPLOAD_DIR') . $fileinfo['wx_pem_file_one']['savepath'] . $fileinfo['wx_pem_file_one']['savename']) - 1);
                $data['wx_pem_file_one'] = $fileinfo['wx_pem_file_one']['savename'];
            } else {
                $wx_pem_file_one = '';
            }
            if ($fileinfo['wx_pem_file_two']) {
                $wx_pem_file_two = substr(C('UPLOAD_DIR') . $fileinfo['wx_pem_file_two']['savepath'] . $fileinfo['wx_pem_file_two']['savename'], 1, strlen(C('UPLOAD_DIR') . $fileinfo['wx_pem_file_two']['savepath'] . $fileinfo['wx_pem_file_two']['savename']) - 1);
                $data['wx_pem_file_two'] = $fileinfo['wx_pem_file_two']['savename'];
            } else {
                $wx_pem_file_two = '';
            }
            !empty($wx_pem_file_one) && $upd['wx_pem_file_one'] = $wx_pem_file_one;
            !empty($wx_pem_file_two) && $upd['wx_pem_file_two'] = $wx_pem_file_two;
            $upd['wx_appid'] = trim(I('wx_appid'));
            $upd['wx_appsecret'] = trim(I('wx_appsecret'));
            $upd['wx_mchid'] = trim(I('wx_mchid'));
            $upd['wx_key'] = trim(I('wx_key'));
            $account_id = I("account_id");
            $upd['consumer_phone']=trim(I('consumer_phone'));
            $upd['explanation']=trim(I('explanation'));
            $upd['modify_user_id'] = $self_user_id;
            $upd['modify_date'] = date('Y-m-d H:i:s');
            $upd['payment_type']=3;
            if ($account_id>0) {
                $ma['account_id'] = $account_id;
                $us = M("user_set")->where($ma)->save($upd);
            }else{
                $upd['user_type'] = 2;
                $upd['enterprise_id'] = $enterprise_id;
                $upd['create_user_id'] = D('SysUser')->self_id();
                $upd['create_date'] = date('Y-m-d H:i:s');
                $us = M("user_set")->add($upd);
            }
            D('SceneInfo')->get_scene_info(2,"", $enterprise_id);
            if ($us) {
                $status = "success";
                $msg = "设置收款成功！";
                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，设置收款成功';
                $this->sys_log('设置收款',$note);
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status,'data'=>$data));
            } else {
                $msg = "设置收款失败！";
                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，设置收款失败';
                $this->sys_log('设置收款',$note);
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
            }
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
    public function enterprise_role(){
        $msg="系统错误";
        $status="error";
        $type=I('get.type');
        $enterprise_id=I("enterprise_id");
        $self_user_type = D('SysUser')->self_user_type(); //1 2 3
        $user_type = $self_user_type - 1; //1：代理商、2：企业
        $self_user_id = D('SysUser')->self_id();
        if(empty($enterprise_id)){
            $msg="参数错误！";
            $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
        }
        if($type=="show"){
            $map['sm.sys_type']=3;//这里表示是企业
            $map['sm.status']=1;//表示正常的
            $menu_list=M("SysMenu as sm")
                        ->join("t_flow_available_menu as am on am.menu_id=sm.menu_id and am.enterprise_id=".$enterprise_id,"left")
                        ->where($map)
                        ->order("sm.order_num asc")
                        ->field("sm.menu_id,sm.menu_name,sm.page_url,sm.top_menu_id,am.available_menu_id")
                        ->select();
            $m_list=array();
            foreach ($menu_list as $m) {
                if($m['top_menu_id']==0){
                    $m['son']=array();
                    array_push($m_list,$m);
                }
            }
            foreach($menu_list as $p){
                foreach($m_list as $k=>$m){
                    if($m['menu_id']==$p['top_menu_id']){
                        if(empty($p['available_menu_id'])){
                            $m_list[$k]["check"]="checked";  //判断是否打选中
                        }
                        array_push($m_list[$k]['son'],$p);
                    }
                }
            }
            $enterprise=M('Enterprise')->where(array("enterprise_id"=>$enterprise_id))->field("enterprise_id,enterprise_name,enterprise_type")->find();
            $this->assign('enterprise',$enterprise);
            $this->assign("m_list",$m_list);
            $this->display();
        }
        if($type=="update"){
            $map_all = array();
            $posts = I("post.");
            foreach ($posts as $k => $v) {
                $m_p = array();
                $m_p['user_type'] = 2;
                $m_p['enterprise_id'] = $enterprise_id;
                $m_p['create_user_id'] = D('SysUser')->self_id();
                $m_p['create_date'] = date('Y-m-d H:i:s');
                $m_p['modify_user_id'] = D('SysUser')->self_id();
                $m_p['modify_date'] = date('Y-m-d H:i:s');
                //判断权限
                if (substr($k, 0, 5) == "role_") {
                    $menu_id = substr($k, 5);
                    if ($v == 1) {
                        $menu_name = $posts['name_' . $menu_id];
                        $menu_url = $posts['url_' . $menu_id];
                        $m_p['menu_id'] = $menu_id;
                        $m_p['menu_name'] = $menu_name;
                        $m_p['menu_url'] = $menu_url;
                        array_push($map_all, $m_p);
                    }
                }
            }
            M("available_menu")->startTrans();
            $w_d['enterprise_id'] = $enterprise_id;
            $ms=M("available_menu")->where($w_d)->select();
            if($ms){
                $mp = M("available_menu")->where($w_d)->delete(); //把设置权限先删除
                if (!$mp) {
                    M("available_menu")->rollback();
                    $msg = "权限设置失败";
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，权限设置失败';
                    $this->sys_log('权限设置',$note);
                    $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
                }
            }
            if($map_all){
                $tr=M("available_menu")->addAll($map_all);
            }else {
                $tr = true;
            }
            $enterprise_type=I("enterprise_type");
            if(empty($enterprise_type)){
                $enterprise_type=2;
            }
            $enter['enterprise_type']=$enterprise_type;
            $enter['modify_user_id'] = D('SysUser')->self_id();
            $enter['modify_date'] = date('Y-m-d H:i:s');
            $en=M("enterprise")->where(array("enterprise_id"=>$enterprise_id))->save($enter);
            if($tr && $en){
                M("available_menu")->commit();
                $msg = "权限设置成功！";
                $status="success";
                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，权限设置成功';
                $this->sys_log('权限设置',$note);
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
            }else{
                M("available_menu")->rollback();
                $msg = "权限设置失败";
                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，权限设置失败';
                $this->sys_log('权限设置',$note);
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
            }
        }
    }
    //给老企业权限
        public function old_enterprise(){
        $all_ids=M("enterprise")->field("enterprise_id")->select();
        $map['sm.sys_type']=3;//这里表示是企业
        $map['sm.status']=1;//表示正常的
        $map['sm.top_menu_id']=array("neq",0);
        $data=array();
        foreach($all_ids as $v){
            $menu_list=M("SysMenu as sm")
                ->join("t_flow_available_menu as am on am.menu_id=sm.menu_id and am.enterprise_id=".$v["enterprise_id"],"left")
                ->where($map)
                ->order("sm.order_num asc")
                ->field("sm.menu_id,sm.menu_name,sm.page_url,sm.top_menu_id,am.available_menu_id")
                ->select();
            foreach ($menu_list as $m){
                if(empty($m['available_menu_id'])){
                    $m_p = array();
                    $m_p['user_type'] = 2;
                    $m_p['enterprise_id'] = $v['enterprise_id'];
                    $m_p['create_user_id'] = D('SysUser')->self_id();
                    $m_p['create_date'] = date('Y-m-d H:i:s');
                    $m_p['modify_user_id'] = D('SysUser')->self_id();
                    $m_p['modify_date'] = date('Y-m-d H:i:s');
                    $m_p['menu_id'] = $m['menu_id'];
                    $m_p['menu_name'] = $m['menu_name'];
                    $m_p['menu_url'] = $m['page_url'];
                    array_push($data,$m_p);
                }
            }
        }
        $ma=M("available_menu")->select();
        if($ma){
            M("available_menu")->where(1)->delete();
        }
        M("available_menu")->addAll($data);
        echo "success";
    }
}
?>