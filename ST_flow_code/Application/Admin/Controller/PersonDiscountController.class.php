<?php
/**
 * 设置个人折扣控制器
 */
namespace Admin\Controller;
use Think\Controller;
/*
 *
 $wx_mobile_discount=I("wx_mobile_discount");
 if(empty($wx_mobile_discount)){
 $wx_mobile_discount=10;
 }
 if(!preg_match('/^[0-9]+(.[0-9]{1,2})?$/',$wx_mobile_discount)){
 $msg="微信端请输入正确的全国移动的折扣！";
 $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
 }
 if($wx_mobile_discount>10){
 $msg="折扣不能高于10折";
 $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
 }
 $wx_unicom_discount=I("wx_unicom_discount");
 if(empty($wx_unicom_discount)){
 $wx_unicom_discount=10;
 }
 if(!preg_match('/^[0-9]+(.[0-9]{1,2})?$/',$wx_unicom_discount)){
 $msg="微信端请输入正确的全国联通的折扣！";
 $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
 }
 if($wx_unicom_discount>10){
 $msg="折扣不能高于10折";
 $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
 }
 $wx_telecom_discount=I("wx_telecom_discount");
 if(empty($wx_telecom_discount)){
 $wx_telecom_discount=10;
 }
 if(!preg_match('/^[0-9]+(.[0-9]{1,2})?$/',$wx_telecom_discount)){
 $msg="微信端请输入正确的全国电信的折扣！";
 $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
 }
 if($wx_telecom_discount>10){
 $msg="折扣不能高于10折";
 $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
 }
 $sdk_mobile_discount=I("sdk_mobile_discount");
 if(empty($sdk_mobile_discount)){
 $sdk_mobile_discount=10;
 }
 if(!preg_match('/^[0-9]+(.[0-9]{1,2})?$/',$sdk_mobile_discount)){
 $msg="sdk端请输入正确的全国移动的折扣！";
 $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
 }
 if($sdk_mobile_discount>10){
 $msg="折扣不能高于10折";
 $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
 }
 $sdk_unicom_discount=I("sdk_unicom_discount");
 if(empty($sdk_unicom_discount)){
 $sdk_unicom_discount=10;
 }
 if(!preg_match('/^[0-9]+(.[0-9]{1,2})?$/',$sdk_unicom_discount)){
 $msg="sdk端请输入正确的全国联通的折扣！";
 $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
 }
 if($sdk_unicom_discount>10){
 $msg="折扣不能高于10折";
 $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
 }
 $sdk_telecom_discount=I("sdk_telecom_discount");
 if(empty($sdk_telecom_discount)){
 $sdk_telecom_discount=10;
 }
 if(!preg_match('/^[0-9]+(.[0-9]{1,2})?$/',$sdk_telecom_discount)){
 $msg="sdk端请输入正确的全国电信的折扣！";
 $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
 }
 if($sdk_telecom_discount>10){
 $msg="折扣不能高于10折";
 $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
 }
 M("person_discount")->startTrans();
 $wx_person_discount=I("wx_person_discount");
 $sdk_person_discount=I("sdk_person_discount");
 $m_p['modify_user_id'] = D('SysUser')->self_id();
 $m_p['modify_date'] = date('Y-m-d H:i:s');
 $wxpd=$m_p;
 $sdkpd=$m_p;
 $wxpd['mobile_discount']=$wx_mobile_discount;
 $wxpd['unicom_discount']=$wx_unicom_discount;
 $wxpd['telecom_discount']=$wx_telecom_discount;
 $sdkpd['mobile_discount']=$sdk_mobile_discount;
 $sdkpd['unicom_discount']=$sdk_unicom_discount;
 $sdkpd['telecom_discount']=$sdk_telecom_discount;
 $mp['user_type'] = $user_type;
 $mp['proxy_id'] = $self_proxy_id;
 $mp['enterprise_id'] = $self_enterprise_id;
 $mp['create_user_id'] = D('SysUser')->self_id();
 $mp['create_date'] = date('Y-m-d H:i:s');
 $mp['modify_user_id'] = D('SysUser')->self_id();
 $mp['modify_date'] = date('Y-m-d H:i:s');
 if(empty($wx_person_discount)){
 $wxpd2=$mp;
 $wxpd2['discount_type']=1;
 $wxpd2['mobile_discount']=$wx_mobile_discount;
 $wxpd2['unicom_discount']=$wx_unicom_discount;
 $wxpd2['telecom_discount']=$wx_telecom_discount;
 $rt=M("person_discount")->add($wxpd2);
 }else{
 $wxmap['person_discount_id']=$wx_person_discount;
 $rt=M("person_discount")->where($wxmap)->save($wxpd);
 }
 if(empty($sdk_person_discount)){
 $sdkpd2=$mp;
 $sdkpd2['discount_type']=2;
 $sdkpd2['mobile_discount']=$sdk_mobile_discount;
 $sdkpd2['unicom_discount']=$sdk_unicom_discount;
 $sdkpd2['telecom_discount']=$sdk_telecom_discount;
 $ra=M("person_discount")->add($sdkpd2);
 }else{
 $sdkmap["person_discount_id"]=$sdk_person_discount;
 $ra=M("person_discount")->where($sdkmap)->save($sdkpd);
 }
 if($rt && $ra){
 $msg="设置折扣成功！";
 $status="success";
 M("person_discount")->commit();
 $n_msg='成功';
 }else{
 $msg="设置折扣失败！";
 M("person_discount")->rollback();
 $n_msg='失败';
 }
 $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，折扣设置'.$n_msg;
 $this->sys_log('折扣设置',$note);
 $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));

 * */
class PersonDiscountController extends CommonController {
	/**
	 * 设置个人折扣
	 */
	public function index() {
		$msg = "系统错误";
		$status = "error";
		$self_user_type = D('SysUser') -> self_user_type();
		$user_type = $self_user_type - 1;
		$self_proxy_id = D('SysUser') -> self_proxy_id();
		$self_enterprise_id = D('SysUser') -> self_enterprise_id();
		$self_user_id = D('SysUser') -> self_id();
		if ($user_type == 1) {
			$map['proxy_id'] = $self_proxy_id;
		} else {
			$map['enterprise_id'] = $self_enterprise_id;
		}
		//获取配置类型
        $usescece =M('user_set as u')
            ->join('left join t_flow_user_sceneset as s on s.user_set_id = u.account_id')
            ->field('s.user_province_type')
            ->where($map)
            ->find();
		$user_province_type = $usescece["user_province_type"];
		if(empty($user_province_type))
		{
			//默认为全国折扣
			$user_province_type = "1";
		}

		$this -> assign("province_type", $user_province_type);




		$pds = M("person_discount") -> where($map) -> select();


		$pdsInfo = Array();
		for ($i = 0; $i < count($pds); $i++) {
			$pdInfo['person_discount_id'] = $pds[$i]['person_discount_id'];
			$pdInfo['discount_type'] = $pds[$i]['discount_type'];
			$pdInfo['mobile_discount'] = $pds[$i]['mobile_discount'];
			$pdInfo['unicom_discount'] = $pds[$i]['unicom_discount'];
			$pdInfo['telecom_discount'] = $pds[$i]['telecom_discount'];
			$pdInfo['province_id'] = $pds[$i]['province_id'];
			$pdInfo['operator_id'] = $pds[$i]['operator_id']; 
			$charge_discount = round($pds[$i]['charge_discount'], 2);
			$charge_discount = number_format($charge_discount, 2, '.', '');
			$pdInfo['charge_discount'] = $charge_discount;
			$pdsInfo[] = $pdInfo;
		}
		$this -> assign("pdsInfo", json_encode($pdsInfo));
		$province = M("sys_province") -> select();
		for ($i = 0; $i < count($province); $i++) {
			switch($province[$i]['province_name']) {
				case '内蒙古自治区' : {
					$province[$i]['province_name'] = '内蒙古';
					break;
				}
				case '新疆维吾尔自治区' : {
					$province[$i]['province_name'] = '新疆';
					break;
				}
				case '西藏自治区' : {
					$province[$i]['province_name'] = '西藏';
					break;
				}
				case '广西壮族自治区' : {
					$province[$i]['province_name'] = '广西';
					break;
				}
				case '宁夏回族自治区' : {
					$province[$i]['province_name'] = '宁夏';
					break;
				}
				case '香港特别行政区' : {
					$province[$i]['province_name'] = '香港';
					break;
				}
				case '澳门特别行政区' : {
					$province[$i]['province_name'] = '澳门';
					break;
				}
			}
		}
		$this -> assign("province", json_encode($province));
		//$discounts = M("person_discount") -> select();
		$this -> display();
	}

	public function save_discount() {
		$self_user_type = D('SysUser') -> self_user_type();
		$user_type = $self_user_type - 1;
		$self_proxy_id = D('SysUser') -> self_proxy_id();
		$self_enterprise_id = D('SysUser') -> self_enterprise_id();
		$self_user_id = D('SysUser') -> self_id();
		$jsonData =  json_decode(htmlspecialchars_decode(I('jsonData')),true);
		for($i = 0;$i < count($jsonData);$i++)
		{
			$typeName = "";
			$operatorName = "";
			$provinceName = $jsonData[$i]['provinceName'];
			if ($jsonData[$i]['type'] == 1)
			{
				$typeName = "微信";
			}
			else
			{
				$typeName = "sdk";
			}
			if($jsonData[$i]['operator'] == 1)
			{
				$operatorName = "移动";
			}
			else if ($jsonData[$i]['operator'] == 2)
			{
				$operatorName = "联通";
			}
			else if ($jsonData[$i]['operator'] == 2)
			{
				$operatorName = "电信";
			}
			if(!preg_match('/^[0-9]+(.[0-9]{1,2})?$/',$jsonData[$i]['discount'])){
				$msg=$typeName."端请输入".$provinceName.$operatorName."的正确的折扣！";
				$this->ajaxReturn(array("msg"=>$msg,"status"=>"error"));
			}
 			if($jsonData[$i]['discount']>10){
				$msg=$typeName."端".$provinceName.$operatorName."的折扣不能高于10折！";
   				$this->ajaxReturn(array("msg"=>$msg,"status"=>"error"));
 			}
		}


		 M("person_discount")->startTrans();
		 M("person_discount")->where($where);
		 $map['user_type'] = $user_type;
		 if ($user_type == 1) {
			$map['proxy_id'] = $self_proxy_id;
		} else {
			$map['enterprise_id'] = $self_enterprise_id;
		}
		
		$pds = 1;
		if (count(M("person_discount")->where ($map)->select())) {
		 	$pds = M("person_discount") -> where($map) -> delete();
		}
		 if($pds)
		 {
		
			//****************************************************
			//保存
			$user_province_type = I("payment_typeselect");
			$provincedata['user_province_type'] = $user_province_type;
					
							//获取配置类型
			$usescece =M('user_set')->field('account_id')->where($map)->find();
			$user_set_id = $usescece["account_id"];
			//获取配置类型
			$scenesetinfo["user_set_id"] = $user_set_id;
			$provincedata["user_set_id"] = $user_set_id;
			$statue = M('user_sceneset')->where($scenesetinfo)->find();
			if(empty($statue))
			{
				$statue = M('user_sceneset')->add($provincedata);
			}
			else
			{
				$statue = M('user_sceneset')->where($scenesetinfo)->save($provincedata);
			}
			//****************************************************

		 	for($i = 0;$i < count($jsonData);$i++)
			{
				$data['user_type'] = $user_type;
				$data['proxy_id'] = $self_proxy_id;
				$data['enterprise_id'] = $self_enterprise_id;
				$data['discount_type'] = $jsonData[$i]['type'];
				$data['create_user_id'] = D('SysUser')->self_id();;
				$data['create_date'] = date('Y-m-d H:i:s');
				$data['modify_user_id'] = D('SysUser')->self_id();
				$data['modify_date'] = date('Y-m-d H:i:s');
				$data['province_id'] = $jsonData[$i]['provinceId'];
				$data['operator_id'] = $jsonData[$i]['operator'];
				$data['charge_discount'] = $jsonData[$i]['discount'];
				$dataList[] = $data;
			}
			$addAll = 1;
			if(count($dataList))
			{
				$addAll = M("person_discount") ->addAll($dataList);
			}
			if($addAll)
			{
				 M("person_discount")->commit();
				 $this->ajaxReturn(array("msg"=>"折扣保存成功","status"=>"success"));
			}
			else
			{
		 		M("person_discount")->rollback();
				$this->ajaxReturn(array("msg"=>"折扣保存失败","status"=>"success"));
			}
		 }
		 else
		 {
		 	M("person_discount")->rollback();
			$this->ajaxReturn(array("msg"=>"折扣保存失败","status"=>"success"));
		 }
	}

}
