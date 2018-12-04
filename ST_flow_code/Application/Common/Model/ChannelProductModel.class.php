<?php

namespace Common\Model;
use Think\Model;

class ChannelProductModel extends Model{

    public function channelproductinfo($product_id){
		//$info = D('ChannelProduct')->find($product_id);
        $where['product_id']=$product_id;
        $info=M("ChannelProduct as cp")->join("t_flow_channel as c on cp.channel_id=c.channel_id")->where($where)->field("cp.*,c.attribute_id")->find();
		if($info['status'] == 2){
			return '';
		}else{
			return $info;
		}
	}

    public function show($where){
        $model=M('channel_product as cp');
        $join = array(
            C('DB_PREFIX').'channel as c ON cp.channel_id=c.channel_id',
            C('DB_PREFIX').'sys_operator as o ON cp.operator_id=o.operator_id',
            C('DB_PREFIX').'sys_province as ps ON cp.province_id=ps.province_id',
            C('DB_PREFIX').'sys_city as sc ON cp.city_id=sc.city_id'
        );
        $list=$model->where($where)->join($join,'left')->field("cp.*,c.channel_code,c.channel_name,o.operator_name,ps.province_name,sc.city_name,c.attribute_id")->find();
        return $list;
    }

    public function flowShow($where){
        $join = array(
            C('DB_PREFIX').'channel as c ON p.channel_id=c.channel_id',
            C('DB_PREFIX').'sys_operator as o ON p.operator_id=o.operator_id',
            C('DB_PREFIX').'sys_province as ps ON p.province_id=ps.province_id',
            C('DB_PREFIX').'channel as ca ON p.back_channel_id=ca.channel_id'
        );
        //获取所有角色列表
        $list = M('Product as p')
            ->where($where)
            ->join($join,"left")
            ->field("p.*,c.channel_code,c.channel_name,ca.channel_code as channel_code2,ca.channel_name as channel_name2,o.operator_name,ps.province_name")
            ->find();
        $sss= M('Product as p')->getLastSql();
        return $list;
    }

    /**
     * 获取某条件下能使用的流量包数据(channel_product)
     */
    public function get_product_list($cond,$user_id=null,$phoneresult=null) {
        $pros=array("700M","11G");//不包含该2包
        $ret = array();
        if(empty($cond)){
            $cond=array();
        }
        array_push($cond,array("province_id"=>array("neq","0")));
        
        //*******************企业产品可用包

        //产品可用通道数组
        $cond['status']=1;//表示启用的
        if(!empty($user_id)){
            $channel_userwhere['enterprise_id']=$user_id;
            //用户可用通道
            $channel_iduserarray=M("channel_user")->where($channel_userwhere)->field("channel_id")->select();
            if(empty($channel_iduserarray))
              return;
        }
        //*******************企业产品可用包
        //号码对应可用包＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊
        if(!empty($phoneresult))
        {
            //号码对应可用产品通道
            //通过手机号归属省or 市, 全国。的通道 获取通道id 需要在chnnerl_user 的通道id之内。
            $province_id = $phoneresult["province_id"];//手机号所属省
            $city_id = $phoneresult["city_id"];//手机号所属市
            $standard = M('channel as c')
                ->where("status = 1 and ((c.province_id in(1,".$province_id.") and c.city_id = 0) or c.city_id = $city_id )")
                ->field("c.channel_id")
                ->select();
            $str="";
        
            foreach($channel_iduserarray as $channelitem)
            {
                //判断手机产品可用包，在企业产品可用包范围之内
                foreach($standard as $standitem)
                {
                    if($channelitem['channel_id'] == $standitem['channel_id'])
                    {
                        $str=$str.$channelitem['channel_id'].",";
                    }
                }
            }
            $str=substr($str,0,-1);
            
            //3,1327,1,28,39,14,27,26,1330,1329,46,48,44,29,47
            //1,2,4,5,14,15,26,27,28,29,30,31,32,33,34,35,36,37,42,43,46,47,48,1328,1332,1333,1334,1335,1336,1337
   
        }
        else
        {
            //获取可用产品
            foreach($channel_iduserarray as $channelitem)
            {
                $str=$str.$channelitem['channel_id'].",";
            }
            $str=substr($str,0,-1);
        }
        //号码对应可用包＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊


        //判断流量包管理里面是否有该流量包
        $productlist = $this->get_product_info($cond);
     
        if(!empty($str)){
            $cond['channel_id']=array("in",$str);
        }
        else
        {
            //无可用包
            return;
        }
        //用可用通道来搜索通道产品表
        //province_id = 1 全国漫游。省内。
        $ret1 = M('channel_product')->where($cond)->group('province_id,product_name,size,operator_id')->order("size asc")->select();
        if(!empty($ret1) && is_array($ret1) && !empty($standard) && is_array($standard)) {
            foreach($ret1 as $v) {
                    foreach($productlist as $v1) {
                        if($v['product_name']==$v1['product_name'] && $v['operator_id']==$v1['operator_id'] && $v['province_id']==$v1['province_id']) {
                            $ret[] = $v;
                            break;
                        }
                    }
                }
         }

        return $ret;
    }

    /**
     * 获取某条件下能使用的流量包数据(product)
     */
    public function get_product_info($cond) {
        //$cond = array('status' => 1);
        $ret = M('product')->where($cond)->select();
        return $ret;
    }

    public function get_products($standard) {
        $ret = array();
        $ret1 = M('channel_product')->select();
        if(!empty($ret1) && is_array($ret1) && !empty($standard) && is_array($standard)) {
            foreach($standard as $k => $v) {
                foreach($ret1 as $k1 => $v1) {
                    if($v['product_name']==$v1['product_name'] && $v['operator_id']==$v1['operator_id'] && $v['province_id']==$v1['province_id']) {
                        $ret[] = $v;
                        break;
                    }
                }
            }
        }
        return $ret;
    }

	public function get_products2($standard) {
        $ret = array();
        
		$user_type = D('SysUser')->self_user_type();
        if($user_type < 3){
            $where['cu.proxy_id'] = D('SysUser')->self_proxy_id();
        }else{
            $where['cu.enterprise_id'] = D('SysUser')->self_enterprise_id();
        }
		$where['cp.status'] = 1;
		$ret1 = M('channel_product as cp')
			->join('t_flow_channel_user as cu on cu.channel_id = cp.channel_id')
			->where( $where )
			->field('cp.product_name,cp.operator_id,cp.province_id')
			->select();
		//$ret1 = M('channel_product')->select();
        if(!empty($ret1) && is_array($ret1) && !empty($standard) && is_array($standard)) {
            foreach($standard as $k => $v) {
                foreach($ret1 as $k1 => $v1) {
                    if($v['product_name']==$v1['product_name'] && $v['operator_id']==$v1['operator_id'] && $v['province_id']==$v1['province_id']) {
                        $ret[] = $v;
						unset($standard[$k]);
                        break;
                    }
                }
            }
        }
        return $ret;
    }

    /**
     * 获取某产品折扣价格
     */
    public function get_product_discount($cond) {
        $rt = M('product')->where($cond)->getField('discount');
        $ret = empty($rt) ? 1 : $rt;
        return floatval($ret);
    }


    //读取所有运营商
    public function operatorall(){
        $operatorAll = D('SysOperator')->select();
        if(!$operatorAll){
            return '';
        }else{
            return $operatorAll;
        }
    }
    
    //读取所有省份
    public function provinceall() {
    	$provinceAll = S('province');
    	if(empty($provinceAll)) {
	        $provinceAll = D('SysProvince')->order("order_num asc")->select();
	        !empty($provinceAll) && S('province', $provinceAll, 2592000); //缓存30天
    	}
    	return empty($provinceAll) ? array() : $provinceAll;
    } 

    //读取不包括全国的省份
    public function province_list() {
        $provinceAll = S('province_list');
        if(empty($provinceAll)) {
            $where['province_id'] = array("gt",1);
            $provinceAll = M('Sys_province')->where($where)->select();
            !empty($provinceAll) && S('province_list', $provinceAll, 2592000); //缓存30天
        }
        return empty($provinceAll) ? array() : $provinceAll;
    }

    //根据product表ID获取t_flow_channel_product对应size
    public function get_size_by_pid($pid,$user_id) {
        $sql = "SELECT DISTINCT cp.size
        FROM t_flow_product p
        INNER JOIN t_flow_channel_product cp ON p.`product_name`=cp.`product_name` AND p.`operator_id`=cp.`operator_id` AND p.`province_id`=cp.province_id
        LEFT JOIN t_flow_channel_user as channel_user on cp.channel_id = channel_user.channel_id
        WHERE p.`product_id`={$pid} and channel_user.`enterprise_id` = {$user_id}";
        $ret = M()->query($sql);
        return !empty($ret) ? $ret[0]['size'] : 0;
    }
    
    public function channelproductinfoFromMap($map){
        $info = M('channel_product')->where($map)->find();
        if(!$info){
            return '';
        }else{
            return $info;
        }
    }


    public function get_price_by_pid($pid,$user_id) {
        $sql = "SELECT DISTINCT cp.price
        FROM t_flow_product p
        INNER JOIN t_flow_channel_product cp ON p.`product_name`=cp.`product_name` AND p.`operator_id`=cp.`operator_id` AND p.`province_id`=cp.province_id
        LEFT JOIN t_flow_channel_user as channel_user on cp.channel_id = channel_user.channel_id
        WHERE p.`product_id`={$pid} and channel_user.`enterprise_id` = {$user_id}";
        $ret = M()->query($sql);
        return !empty($ret) ? $ret[0]['price'] : 0;
    }


}