<?php

namespace Common\Model;
use Think\Model;

/**
 * 折扣相关模型
 */
class DiscountModel extends Model{

	/**
	 * 获取某个折扣数据
	 */
    public function discountinfo($discount_id) {
		$info = D('Discount')->find($discount_id);
		$ret = !empty($info) ? $info : array();
		return $ret;
	}
	
	/**
	 * 获取某个折扣的详细数据
	 */
	public function discountdetail($discount_id) {
		$model = M('discount d');
		$model->join("left join ".C('DB_PREFIX')."proxy p on d.proxy_id = p.proxy_id");
		$model->join("left join ".C('DB_PREFIX')."enterprise e on d.enterprise_id = e.enterprise_id");
		$model->join("left join ".C('DB_PREFIX')."sys_operator so on d.operator_id = so.operator_id");
		$model->join("left join ".C('DB_PREFIX')."sys_province sp on d.province_id = sp.province_id");
		$model->join("left join ".C('DB_PREFIX')."sys_city sc on d.city_id = sc.city_id");
		$model->field('d.*,so.operator_name,sp.province_name,sc.city_name,p.proxy_name,e.enterprise_name,p.proxy_code,e.enterprise_code');
		$model->where(array('d.discount_id'=>$discount_id));
		$ret = $model->find();
		return $ret;
	}
    
	/**
	 * 获取所有折扣数据
	 */
    public function discountall() {
    	$ret = S('discountall');
    	if(empty($ret)) {
	    	$model = M('discount');
	    	$model->alias('d');
	    	$model->join("left join ".C('DB_PREFIX')."sys_operator so on d.operator_id = so.operator_id");
	    	$model->join("left join ".C('DB_PREFIX')."sys_province sp on d.province_id = sp.province_id");
	    	$model->field('d.*,so.operator_name,sp.province_name');
	    	
			$infoall = $model->select();
			$ret = !empty($infoall) ? $infoall : array();
    		!empty($ret) && S('discountall', $ret, 86400); //缓存1天
    	}
		return $ret;
	}

	/**
	 * 获取产品信息
	 */
	public function productdiscountdetail($discount_id) {
		$model = M('discount_product d');
		$model->join("left join ".C('DB_PREFIX')."proxy p on d.proxy_id = p.proxy_id");
		$model->join("left join ".C('DB_PREFIX')."enterprise e on d.enterprise_id = e.enterprise_id");
		$model->join("left join ".C('DB_PREFIX')."sys_operator so on d.operator_id = so.operator_id");
		$model->join("left join ".C('DB_PREFIX')."sys_province sp on d.province_id = sp.province_id");
		$model->join("left join ".C('DB_PREFIX')."sys_city sc on d.city_id = sc.city_id");
		$model->join("left join ".C('DB_PREFIX')."product pr on d.size = pr.size");
		$model->field('d.*,so.operator_name,sp.province_name,sc.city_name,p.proxy_name,e.enterprise_name,p.proxy_code,e.enterprise_code,pr.product_name');
		$model->where(array('d.discount_id'=>$discount_id));
		$ret = $model->find();
		return $ret;
	}

	/**
	 * 获取所有折扣数据
	 */
	public function productdiscountall($province_id,$city_id) {
		$where['d.province_id']=$province_id;
		$where['d.city_id']=$city_id;
		$where['d.proxy_id']=D("SysUser")->self_proxy_id();
		//$ret = S('productdiscountall');
		if(1) {
			$model = M('DiscountProduct');
			$model->alias('d');
			$model->join("left join ".C('DB_PREFIX')."sys_operator so on d.operator_id = so.operator_id");
			$model->join("left join ".C('DB_PREFIX')."sys_province sp on d.province_id = sp.province_id");
			$model->join("left join ".C('DB_PREFIX')."sys_city sc on d.city_id = sc.city_id");
			$model->field('d.*,so.operator_name,sp.province_name,sc.city_name');
			$model->where($where);
			$infoall = $model->select();
			$ret = !empty($infoall) ? $infoall : array();
			//!empty($ret) && S('productdiscountall', $ret, 86400); //缓存1天
		}
		return $ret;
	}

	/**
	 * 修改当前代理商的下级的折扣
	 *  $proxys代理商id,$map变动折扣的数组，$type类型：productdiscount(产品折扣),discount（用户折扣）
	 */
	public function  update_all_user($proxy_ids,$map_new,$type,$map_old=null){
		$self_proxy_id = D('SysUser')->self_proxy_id();
		$self_id = D('SysUser')->self_id();
		$time = date("Y-m-d H:i:s", time());
		//只修改企业则直接结束
		if(empty($proxy_ids) || $proxy_ids == ""){
			return true;
		}
		if($type=="product_discount"){
			$model=M("discount_product");
			$modelrecord=M("discount_product_record");
		}else{
			$model=M("discount");
			$modelrecord=M("discount_record");
		}
		$model->startTrans();
		//编辑时专用 开始
		if(!empty($map_old['proxy_id'])){
			$proxy_child_ids = M('')->query("select getProxyChildList('".$map_old['proxy_id']."') as ids");
			$proxychild_ids = explode(",", $proxy_child_ids[0]['ids']);
			$enterprises = "";
			foreach ($proxychild_ids as $pid) {
				$enterprise_ids = M("enterprise")->where(array("top_proxy_id" => $pid))->field("enterprise_id")->select();
				if ($enterprise_ids) {
					foreach ($enterprise_ids as $i) {
						$enterprises = $enterprises.$i['enterprise_id'].",";
					}
				}
			}
			$enterprises = substr($enterprises, 0, strlen($enterprises) - 1);
			$where1['proxy_id']=array("in",$proxy_child_ids[0]['ids']);
			if($enterprises !=""){
				$where1['_logic']="or";
				$where1['enterprise_id']=array("in",$enterprises);
			}
			$where['operator_id'] = $map_old['operator_id'];
			$where['province_id'] = $map_old['province_id'];
			$where['city_id'] = $map_old['city_id'];
			$where[]=$where1;
			if($type=="product_discount"){
				$where['size'] = $map_old['size'];
			}
			$map=array();
			$ts=$model->where($where)->count();
			$dps =$model->where($where)->select();
			foreach ($dps as $d) {
				$m['operator_id']=$map_old['operator_id'];
				$m['province_id']=$map_old['province_id'];
				$m['city_id']=$map_old['city_id'];
				$m['discount_before']= $d['discount_number'];
				$m['proxy_id']=$d['proxy_id'];
				$m['top_proxy_id']=null;
				$m['enterprise_id']=$d['enterprise_id'];
				if($type=="product_discount"){
					$m['size'] = $map_old['size'];
				}
				if($m['proxy_id']!=null && $m['proxy_id']!=0){
					$m['user_type']=1;
				}else{
					$m['user_type']=2;
				}
				$m['create_proxy_id'] = $self_proxy_id;    //创建代理商ID
				$m['create_user_id'] = $self_id;        //创建人ID
				$m['create_date'] = $time;        //创建时间
				$m['discount_after'] =1;        //操作后折扣
				unset($m['record_id']);
				array_push($map,$m);
			}
			$dts=$model->where($where)->delete();//10折则删除下级折扣
			if($dts!=$ts){
				$model->rollback();
				return false;
			}
			$modelrecord->addAll($map);
			$model->commit();
			return true;
		}
		//编辑时专用结束
		
		$proxyids = explode(",", $proxy_ids);
		foreach($proxyids as $proxy_id){
			//获取当前代理商的所有下级代理商
			$proxy_child_ids = M('')->query("select getProxyChildList('$proxy_id') as ids");
			$proxychild_ids = explode(",", $proxy_child_ids[0]['ids']);
			$enterprises = "";
			foreach ($proxychild_ids as $pid) {
				$enterprise_ids = M("enterprise")->where(array("top_proxy_id" => $pid))->field("enterprise_id")->select();
				if ($enterprise_ids) {
					foreach ($enterprise_ids as $i) {
						$enterprises = $enterprises.$i['enterprise_id'].",";
					}
				}
			}
			$enterprises = substr($enterprises, 0, strlen($enterprises) - 1);
			$where1['proxy_id']=array("in",$proxy_child_ids[0]['ids']);
			if($enterprises !=""){
				$where1['_logic']="or";
				$where1['enterprise_id']=array("in",$enterprises);
			}
			foreach ($map_new as $m) {
				$where = array();
				$where['operator_id'] = $m['operator_id'];
				$where['province_id'] = $m['province_id'];
				$where['city_id'] = $m['city_id'];
				if($type=="product_discount"){
					$where['size'] = $m['size'];
				}
				$where[]=$where1;
				if ($m['discount_after'] != 1) {
					$map=array();
					$dps =$model->where($where)->select();
					foreach ($dps as $d) {
						if ($d['discount_number'] < $m['discount_after']) {//如果下级折扣比父亲低则变成和父亲一样大
							$m['discount_before']= $d['discount_number'];
							$d['discount_number'] = $m['discount_after'];
							if(!$model->save($d)){
								$model->rollback();
								return false;
							}
							$m['proxy_id']=$d['proxy_id'];
							if($m['proxy_id']!=null && $m['proxy_id']!=0){
								$m['user_type']=1;
							}else{
								$m['user_type']=2;
							}
							$m['top_proxy_id']=null;
							$m['enterprise_id']=$d['enterprise_id'];
							$m['create_proxy_id'] = $self_proxy_id;    //创建代理商ID
							$m['create_user_id'] = $self_id;        //创建人ID
							$m['create_date'] = $time;        //创建时间
							unset($m['record_id']);
							array_push($map,$m);
						}
					}
					$modelrecord->addAll($map);
				} else {
					$map=array();
					$ts=$model->where($where)->count();
					$dps =$model->where($where)->select();
					foreach ($dps as $d) {
						$m['discount_before']= $d['discount_number'];
						$m['proxy_id']=$d['proxy_id'];
						$m['top_proxy_id']=null;
						$m['enterprise_id']=$d['enterprise_id'];
						if($m['proxy_id']!=null && $m['proxy_id']!=0){
							$m['user_type']=1;
						}else{
							$m['user_type']=2;
						}
						$m['create_proxy_id'] = $self_proxy_id;    //创建代理商ID
						$m['create_user_id'] = $self_id;        //创建人ID
						$m['create_date'] = $time;        //创建时间
						$m['discount_after'] =1;        //操作后折扣
						unset($m['record_id']);
						array_push($map,$m);
					}
					$dts=$model->where($where)->delete();//10折则删除下级折扣
					if($dts!=$ts){
						$model->rollback();
						return false;
					}
					$modelrecord->addAll($map);
				}
			}
		}
		$model->commit();
		return true;
	}
	
}