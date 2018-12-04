<?php

namespace Common\Model;
use Think\Model;
use \Think\Page;
class OrderModel extends Model{

    public function orderList($where){
        $model=M('order as o');
        //$enterprise_ids = D('Enterprise')->get_enterprise_by_tpid(D('SysUser')->self_proxy_id());
            $join=array(
                'left join t_flow_proxy as p on p.proxy_id =o.proxy_id and o.user_type = 1',//代理商
                'left join  t_flow_enterprise as e on e.enterprise_id=o.enterprise_id and o.user_type = 2',//企业
            );
            $field='o.order_id,o.order_code,o.user_type,o.channel_order_code,o.source_type,o.mobile,o.price,o.discount_price,o.pay_type,o.complete_time,o.order_date,p.proxy_name,p.proxy_code,e.enterprise_name,e.enterprise_code';
        $count=$model
            ->join($join) //代理商
            ->where($where)
            ->count();


        $Page       = new \Think\Page($count,20);
        $show   	= $Page->show();

        $list =$model
            ->join($join)
            ->where($where)
            ->field($field)
            ->order('o.order_date desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
       /* foreach($list as &$v){
            $v['complete_time'] = substr($v['complete_time'], 0,19);
            $v['order_date'] = substr($v['order_date'], 0,19);
        }*/
        return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show);
    }

    public function orderList_copy($where){
        $model=M('order as o');
        //$enterprise_ids = D('Enterprise')->get_enterprise_by_tpid(D('SysUser')->self_proxy_id());
        $join=array(
            'left join t_flow_proxy as p on p.proxy_id =o.proxy_id and o.user_type = 1',//代理商
            'left join  t_flow_enterprise as e on e.enterprise_id=o.enterprise_id and o.user_type = 2',//企业
            'inner join t_flow_channel_product as zp on  o.channel_product_id=  zp.product_id',//主通道产品
            'inner join t_flow_channel_product as bp on  o.back_channel_product_id= bp.product_id',//备用通道产品
            'inner join t_flow_sys_operator as op on zp.operator_id = op.operator_id',//运营商
            'inner join t_flow_sys_province as pr on zp.province_id = pr.province_id',//省份
            'inner join t_flow_channel as zc  on  zp.channel_id  = zc.channel_id ',//主通道产品
            'inner join t_flow_channel as bc on  bp.channel_id=  bc.channel_id'  //备用通道产品
        );
        $field='o.order_id,o.order_code,o.user_type,o.channel_order_code,o.source_type,o.mobile,o.price,o.discount_price,o.pay_type,o.complete_time,o.order_date,p.proxy_name,p.proxy_code,e.enterprise_name,e.enterprise_code,op.operator_name,zc.channel_code,bc.channel_code,pr.province_name,zp.product_name,bp.product_name';

        $count=$model
            ->join($join) //代理商
            ->where($where)
            ->count();

        $Page       = new \Think\Page($count,20);
        $show   	= $Page->show();

        $list =$model
            ->join($join)
            ->where($where)
            ->field($field)
            ->order('o.order_date desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        /* foreach($list as &$v){
             $v['complete_time'] = substr($v['complete_time'], 0,19);
             $v['order_date'] = substr($v['order_date'], 0,19);
         }*/
        return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show);
    }



    public function discount_price_sum($where){
        $model = M('order as o');
        //$enterprise_ids = D('Enterprise')->get_enterprise_by_tpid(D('SysUser')->self_proxy_id());
        $join = array(
            'left join t_flow_proxy as p on p.proxy_id =o.proxy_id and o.user_type = 1',//代理商
            'left join  t_flow_enterprise as e on e.enterprise_id=o.enterprise_id and o.user_type = 2',//企业
        );
        $result = $model
            ->join($join)
            ->where($where)
            ->sum('discount_price');
        $result = empty($result)?0:$result;
        return $result;
    }


    public function order_pre_list($where){
        $model=M('order_pre as o');
    /*    $enterprise_ids = D('Enterprise')->get_enterprise_by_tpid(D('SysUser')->self_proxy_id());
        if(!empty($enterprise_ids)){*/
            $join=array(
                'left join t_flow_proxy as p on p.proxy_id =o.proxy_id and o.user_type = 1',//代理商
                'left join  t_flow_enterprise as e on e.enterprise_id=o.enterprise_id and o.user_type = 2',//企业
            );
            $field='o.order_id,o.order_code,o.user_type,o.channel_order_code,o.source_type,o.mobile,o.price,o.discount_price,o.pay_type,o.complete_time,o.order_date,p.proxy_name,p.proxy_code,e.enterprise_name,e.enterprise_code';
     /*   }else{
            $join=array(
                'left join t_flow_proxy as p on o.proxy_id = p.proxy_id and o.user_type = 1',//代理商
                'inner join t_flow_channel_product as zp on  o.channel_product_id=  zp.product_id',//主通道产品
                'inner join t_flow_channel_product as bp on  o.back_channel_product_id= bp.product_id',//备用通道产品
                'inner join t_flow_sys_operator as op on zp.operator_id = op.operator_id',//运营商
                'inner join t_flow_sys_province as pr on zp.province_id = pr.province_id',//省份
                'inner join t_flow_channel as zc  on  zp.channel_id  = zc.channel_id ',//主通道产品
                'inner join t_flow_channel as bc on  bp.channel_id=  bc.channel_id'  //备用通道产品
            );
            $field='o.order_id,o.order_code,o.user_type,o.channel_order_code,o.source_type,o.mobile,o.price,o.discount_price,o.pay_type,o.complete_time,o.order_date,p.proxy_name,p.proxy_code,op.operator_name,zc.channel_code,bc.channel_code,pr.province_name,zp.product_name,bp.product_name';
        }*/

        $count=$model
            ->join($join) //代理商
            ->where($where)
            ->count();

        $Page       = new \Think\Page($count,20);
        $show   	= $Page->show();

        $list =$model
            ->join($join)
            ->where($where)
            ->field($field)
            ->order('o.order_date desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show);
    }

    public function export_excel($where,$type){
        $model=M('order as o');
      /*  $enterprise_ids = D('Enterprise')->get_enterprise_by_tpid(D('SysUser')->self_proxy_id());
        if(!empty($enterprise_ids)){*/
            $join=array(
                'left join t_flow_proxy as p on p.proxy_id =o.proxy_id and o.user_type = 1',//代理商
                'left join  t_flow_enterprise as e on e.enterprise_id=o.enterprise_id and o.user_type = 2',//企业
                'inner join t_flow_channel_product as zp on  o.channel_product_id=  zp.product_id',//主通道产品
                'inner join t_flow_channel_product as bp on  o.back_channel_product_id= bp.product_id',//备用通道产品
                'inner join t_flow_sys_operator as op on zp.operator_id = op.operator_id',//运营商
                'inner join t_flow_sys_province as pr on zp.province_id = pr.province_id',//省份
                'inner join t_flow_channel as zc  on  zp.channel_id  = zc.channel_id ',//主通道产品
                'inner join t_flow_channel as bc on  bp.channel_id=  bc.channel_id'  //备用通道产品
            );
            $field='o.order_id,o.order_code,o.user_type,o.channel_order_code,o.source_type,o.mobile,o.price,o.discount_price,o.pay_type,o.complete_time,o.order_date,p.proxy_name,p.proxy_code,e.enterprise_name,e.enterprise_code,op.operator_name,zc.channel_code,bc.channel_code,pr.province_name,zp.product_name,bp.product_name';
   /*     }else{
            $join=array(
                'left join t_flow_proxy as p on o.proxy_id = p.proxy_id and o.user_type = 1',//代理商
                'inner join t_flow_channel_product as zp on  o.channel_product_id=  zp.product_id',//主通道产品
                'inner join t_flow_channel_product as bp on  o.back_channel_product_id= bp.product_id',//备用通道产品
                'inner join t_flow_sys_operator as op on zp.operator_id = op.operator_id',//运营商
                'inner join t_flow_sys_province as pr on zp.province_id = pr.province_id',//省份
                'inner join t_flow_channel as zc  on  zp.channel_id  = zc.channel_id ',//主通道产品
                'inner join t_flow_channel as bc on  bp.channel_id=  bc.channel_id'  //备用通道产品
            );
            $field='o.order_id,o.order_code,o.user_type,o.channel_order_code,o.source_type,o.mobile,o.price,o.discount_price,o.pay_type,o.complete_time,o.order_date,p.proxy_name,p.proxy_code,op.operator_name,zc.channel_code,bc.channel_code,pr.province_name,zp.product_name,bp.product_name';
        }*/

        $order_list =$model
            ->join($join)
            ->where($where)
            ->field($field)
            ->order('o.order_date desc')
            ->limit(3000)
            ->select();
        $list=array();
        foreach($order_list as $k=>$v){
            $list[$k]['order_code'] =" ".$v['order_code'];
            if($v['user_type']==1){
                $list[$k]['proxy_code'] =$v['proxy_code'];
                $list[$k]['proxy_name'] =$v['proxy_name'];
            }else{
                $list[$k]['enterprise_code'] =$v['enterprise_code'];
                $list[$k]['enterprise_name'] =$v['enterprise_name'];
            }

            if($v['source_type']==1){
                $list[$k]['source_type'] ='接口';
            }else if($v['source_type']==2){
                $list[$k]['source_type'] ='平台';

            }else{
                $list[$k]['source_type'] ='网站';
            }
            $list[$k]['mobile'] ="  ".$v['mobile'];
            $list[$k]['price'] =$v['price'];
            $list[$k]['discount_price'] =$v['discount_price'];
            if($v['pay_type']==1){
                $list[$k]['pay_type'] ='账户余额';
            }else if($v['pay_type']==2){
                $list[$k]['pay_type'] ='微信';
            }else{
                $list[$k]['pay_type'] ='支付宝';
            }
            if($type==2){
                $list[$k]['complete_time'] = substr($v['complete_time'],0,19);
            }else{
                $list[$k]['order_date'] = substr($v['order_date'],0,19);;
            }
        }
        return $list;


    }



    public function order_excel($where){
        $model=M('order_pre as o');
      /*  $enterprise_ids = D('Enterprise')->get_enterprise_by_tpid(D('SysUser')->self_proxy_id());
        if(!empty($enterprise_ids)){*/
            $join=array(
                'left join t_flow_proxy as p on p.proxy_id =o.proxy_id and o.user_type = 1',//代理商
                'left join  t_flow_enterprise as e on e.enterprise_id=o.enterprise_id and o.user_type = 2',//企业
                'inner join t_flow_channel_product as zp on  o.channel_product_id=  zp.product_id',//主通道产品
                'inner join t_flow_channel_product as bp on  o.back_channel_product_id= bp.product_id',//备用通道产品
                'inner join t_flow_sys_operator as op on zp.operator_id = op.operator_id',//运营商
                'inner join t_flow_sys_province as pr on zp.province_id = pr.province_id',//省份
                'inner join t_flow_channel as zc  on  zp.channel_id  = zc.channel_id ',//主通道产品
                'inner join t_flow_channel as bc on  bp.channel_id=  bc.channel_id'  //备用通道产品
            );
            $field='o.order_id,o.order_code,o.user_type,o.channel_order_code,o.source_type,o.mobile,o.price,o.discount_price,o.pay_type,o.complete_time,o.order_date,p.proxy_name,p.proxy_code,e.enterprise_name,e.enterprise_code,op.operator_name,zc.channel_code,bc.channel_code,pr.province_name,zp.product_name,bp.product_name';
    /*    }else{
            $join=array(
                'left join t_flow_proxy as p on o.proxy_id = p.proxy_id and o.user_type = 1',//代理商
                'inner join t_flow_channel_product as zp on  o.channel_product_id=  zp.product_id',//主通道产品
                'inner join t_flow_channel_product as bp on  o.back_channel_product_id= bp.product_id',//备用通道产品
                'inner join t_flow_sys_operator as op on zp.operator_id = op.operator_id',//运营商
                'inner join t_flow_sys_province as pr on zp.province_id = pr.province_id',//省份
                'inner join t_flow_channel as zc  on  zp.channel_id  = zc.channel_id ',//主通道产品
                'inner join t_flow_channel as bc on  bp.channel_id=  bc.channel_id'  //备用通道产品
            );
            $field='o.order_id,o.order_code,o.user_type,o.channel_order_code,o.source_type,o.mobile,o.price,o.discount_price,o.pay_type,o.complete_time,o.order_date,p.proxy_name,p.proxy_code,op.operator_name,zc.channel_code,bc.channel_code,pr.province_name,zp.product_name,bp.product_name';
        }*/

        $order_list =$model
            ->join($join)
            ->where($where)
            ->field($field)
            ->order('o.order_date desc')
            ->limit(3000)
            ->select();
        $list=array();
        foreach($order_list as $k=>$v){
            $list[$k]['order_code'] =" ".$v['order_code'];
            if($v['user_type']==1){
                $list[$k]['proxy_code'] =$v['proxy_code'];
                $list[$k]['proxy_name'] =$v['proxy_name'];
            }else{
                $list[$k]['enterprise_code'] =$v['enterprise_code'];
                $list[$k]['enterprise_name'] =$v['enterprise_name'];
            }

            if($v['source_type']==1){
                $list[$k]['source_type'] ='接口';
            }else if($v['source_type']==2){
                $list[$k]['source_type'] ='平台';

            }else{
                $list[$k]['source_type'] ='网站';
            }
            $list[$k]['mobile'] ="  ".$v['mobile'];
            $list[$k]['price'] =$v['price'];
            $list[$k]['discount_price'] =$v['discount_price'];
            if($v['pay_type']==1){
                $list[$k]['pay_type'] ='账户余额';
            }else if($v['pay_type']==2){
                $list[$k]['pay_type'] ='微信';
            }else{
                $list[$k]['pay_type'] ='支付宝';
            }
            $list[$k]['order_date'] =$v['order_date'];
        }
        return $list;
    }



    public function detailed($where){
        $join=array(
            't_flow_proxy as p on p.proxy_id = o.proxy_id',//代理商
            't_flow_enterprise as e on e.top_proxy_id = p.proxy_id',//企业
            't_flow_channel_product as zp on  o.channel_product_id=  zp.product_id',//主通道产品
            't_flow_channel_product as bp on  o.back_channel_product_id= bp.product_id',//备用通道产品
            't_flow_sys_operator as op on zp.operator_id = op.operator_id',//运营商
            't_flow_sys_province as pr on zp.province_id = pr.province_id',//省份
            't_flow_channel as zc  on  zp.channel_id  = zc.channel_id ',//主通道产品
            't_flow_channel as bc on  bp.channel_id=  bc.channel_id'  //备用通道产品
        );
        $model=M('order  o');
        $list =$model
            ->join($join) //代理商
            ->field('o.*,p.proxy_name,p.proxy_code,e.enterprise_name,e.enterprise_code,op.operator_name,zc.channel_code,bc.channel_code,pr.province_name,zp.product_name,bp.product_name')
            ->where($where)
            ->find();
        return $list;
    }


public function orderdetail($where){
        $model=M('Order  o');
        $list =$model
            ->join('left join t_flow_proxy as p on p.proxy_id = o.proxy_id') //代理商
            ->join('left join t_flow_enterprise as e on e.enterprise_id = o.enterprise_id') //企业
            ->join('left join t_flow_sys_operator as op on op.operator_id = o.operator_id')  //运营商
           // ->join('left join t_flow_channel as c on c.channel_id = o.channel_id')  //通道
           // ->join('left join t_flow_channel as bc on bc.channel_id = o.back_channel_id')  //备用通道
            //->join('left join t_flow_sys_province as pr on pr.province_id = o.province_id')   //省份
            //->join('left join t_flow_channel_product as pp on pp.product_id = o.channel_product_id')  //产品
           // ->join ('left join t_flow_sys_mobile_dict as dt on dt.mobile=o.mobile')  //城市
            ->field('o.order_code,o.order_id,o.channel_order_code,o.mobile,o.operator_id,o.top_rebate_discount,o.top_discount,o.source_type,o.back_content,o.one_proxy_name,o.back_fail_desc,o.price,o.discount_price,o.channel_id,o.order_date,o.complete_time,o.order_status,o.proxy_name,p.proxy_level,o.proxy_id,o.enterprise_name,o.enterprise_id,op.operator_name,o.channel_code,o.back_channel_code as bc_channel_code,o.province_name,o.product_name,o.product_province_id, o.city_name,p.proxy_code,e.enterprise_code')
            ->where($where)
            ->find();
        return $list;
    }


    public function order_pre_detail($where,$table=NULL){
        $tablename = $table==""?"order_pre":$table;
        $model=M($tablename.' o');
        $list =$model
            ->join('left join t_flow_proxy as p on p.proxy_id = o.proxy_id') //代理商
            ->join('left join t_flow_enterprise as e on e.enterprise_id = o.enterprise_id') //企业
            ->join('left join t_flow_sys_operator as op on op.operator_id = o.operator_id')  //运营商
            // ->join('left join t_flow_channel as c on c.channel_id = o.channel_id')  //通道
            // ->join('left join t_flow_channel as bc on bc.channel_id = o.back_channel_id')  //备用通道
            //->join('left join t_flow_sys_province as pr on pr.province_id = o.province_id')   //省份
            //->join('left join t_flow_channel_product as pp on pp.product_id = o.channel_product_id')  //产品
            // ->join ('left join t_flow_sys_mobile_dict as dt on dt.mobile=o.mobile')  //城市
            ->field('o.order_code,o.order_id,o.channel_order_code,o.mobile,o.operator_id,o.top_rebate_discount,o.top_discount,o.source_type,o.back_content,o.one_proxy_name,o.back_fail_desc,o.price,o.discount_price,o.channel_id,o.order_date,o.complete_time,o.order_status,o.proxy_name,p.proxy_level,o.proxy_id,o.enterprise_name,o.enterprise_id,op.operator_name,o.channel_code,o.back_channel_code as bc_channel_code,o.province_name,o.product_name,o.product_province_id, o.city_name,p.proxy_code,e.enterprise_code')
            ->where($where)
            ->where($where)
            ->find();
        return $list;
    }
    public function order_pre_detail_copy($where,$table=NULL){
        $tablename = $table==""?"order_pre":$table;
        $model=M($tablename.' o');
        $list =$model
            ->join('left join t_flow_proxy as p on p.proxy_id = o.proxy_id') //代理商
            ->join('left join t_flow_enterprise as e on e.enterprise_id = o.enterprise_id') //企业
            ->join('left join t_flow_sys_operator as op on op.operator_id = o.operator_id')  //运营商
            ->join('left join t_flow_channel as c on c.channel_id = o.channel_id')  //通道
            ->join('left join t_flow_channel as bc on bc.channel_id = o.back_channel_id')  //备用通道
            ->join('left join t_flow_channel_product as pp on pp.product_id = o.channel_product_id')  //产品
            ->join('left join t_flow_sys_province as pr on pr.province_id = o.product_province_id')   //省份
            ->join ('left join t_flow_sys_mobile_dict as dt on dt.mobile=o.mobile')  //城市
            ->field('o.order_code,o.order_id,o.channel_order_code,o.mobile,o.operator_id,o.source_type,o.back_content,o.one_proxy_name,pr.province_id,o.back_fail_desc,o.price,o.discount_price,o.channel_id,o.order_date,o.complete_time,o.order_status,o.proxy_name,p.proxy_code,p.proxy_level,p.proxy_id,o.enterprise_name,e.enterprise_id,e.enterprise_code,op.operator_name,c.channel_code,bc.channel_code as bc_channel_code,pr.province_name,pp.product_name,dt.city_name,dt.province_name')
            ->where($where)
            ->find();
        return $list;
    }

     public function orderinfo($map){
        $orderinfo = D('SysOrder')->where($map)->find();
        if(!$orderinfo){
            return '';
        }else{
            return $orderinfo;
        }
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

    public function  all_data($map,$order_status='',$join){
        //将读取条件写入搜索条件里
        $map['od.order_status']=array('in',$order_status);
        //计算条数
        $data['count'] = M('Order as od')->where($map)->join($join,"left")->count();
        //计算金额
        $data['sum'] = M('Order as od')->where($map)->join($join,"left")->sum('od.discount_price');
        $data['sum'] = $data['sum']==""?0:$data['sum'];
        return  $data;
    }


    public function  all_pre_data($map,$order_status='',$join){
        //将读取条件写入搜索条件里
        $map['od.order_status']=array('in',$order_status);
        //计算条数
        $data['count'] = M('order_pre as od')->where($map)->join($join,"left")->count();
        //计算金额
        $data['sum'] = M('order_pre as od')->where($map)->join($join,"left")->sum('od.discount_price');
        $data['sum'] = $data['sum']==""?0:$data['sum'];
        return  $data;
    }

    //读取所有省份
    public function provinceall(){
        $provinceAll = D('SysProvince')->select();
        if(!$provinceAll){
            return '';
        }else{
            return $provinceAll;
        }
    }

    public function channelall($type=NULL){
        $where['status'] = 1;
        if($type==2){
            $infoall = D('Channel')->where($where)->order('channel_code asc')->select();
        }else{
            $infoall = D('Channel')->where($where)->select();
        }
        if(!$infoall){
            return '';
        }else{
            return $infoall;
        }
    }



    /*
    存过订单  只针对index文件
    $type:1 已完成订单   2未完成订单   3  缓存订单
    $user_id： 用户id
    $proxy_name： 用户名称
    $mobile： 手机号
    $channel_id： 通道
    $bc_channel_id： 备用通道
    $operator_id：运营商
    $province_id：省id
    $order_status：订单状态
    $start_datetime：开始实际
    $end_datetime：结束时间
    $product_name：流量包名称
    $get_page：第几页


    返回：
    list：列表数据
    show：分页信息
    count：统计信息

    */
    public function order_storing_process($type,$user_id,$proxy_name,$code,$order_code,$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$get_page,$sale_id){
        $list = M()->query("CALL p_query_order(".$type.",".$user_id.",'".$proxy_name."','".$code."','".$order_code."','".$mobile."','".$channel_id."','".$bc_channel_id."',".$operator_id.",".$province_id.",'".$order_status."','".$start_datetime."','".$end_datetime."','".$product_name."',".$sale_id.",".$get_page.",20,1,@p_total_count,@p_success_count,@p_success_amount,@p_faile_count,@p_faile_amount,@p_wait_count,@p_wait_amount,@submit_success_count,@submit_success_amount,@p_success_price,@p_faile_price,@p_wait_price,@p_submit_success_price);");

        $count = M()->query("SELECT @p_total_count,@p_success_count,@p_success_amount,@p_faile_count,@p_faile_amount,@p_wait_count,@p_wait_amount,@submit_success_count,@submit_success_amount,@p_success_price,@p_faile_price,@p_wait_price,@p_submit_success_price;");

        //echo  M()->getLastSql();

        $result=array('list'=>$list,'count'=>$count[0]);
        return $result;
    }

    public function order_storing_process2($type,$user_id,$proxy_name,$code,$order_code,$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$get_page,$sale_id){
        $list = M()->query("CALL p_query_order(".$type.",".$user_id.",'".$proxy_name."','".$code."','".$order_code."','".$mobile."','".$channel_id."','".$bc_channel_id."',".$operator_id.",".$province_id.",'".$order_status."','".$start_datetime."','".$end_datetime."','".$product_name."',".$sale_id.",".$get_page.",20,0,@p_total_count,@p_success_count,@p_success_amount,@p_faile_count,@p_faile_amount,@p_wait_count,@p_wait_amount,@submit_success_count,@submit_success_amount,@p_success_price,@p_faile_price,@p_wait_price,@p_submit_success_price);");
        //echo  M()->getLastSql();
        $count = M()->query("SELECT @p_total_count,@p_success_count,@p_success_amount,@p_faile_count,@p_faile_amount,@p_wait_count,@p_wait_amount,@submit_success_count,@submit_success_amount,@p_success_price,@p_faile_price,@p_wait_price,@p_submit_success_price;");

        //echo  M()->getLastSql();
        $result=array('list'=>$list,'count'=>$count[0]);
        return $result;
    }

    /*
存过订单  只针对代理商端未完成订单、已完成订单、已取消订单的导出
$type:1 已完成订单   2未完成订单   3  缓存订单
$user_id： 用户id
$proxy_name： 用户名称
$mobile： 手机号
$channel_id： 通道
$bc_channel_id： 备用通道
$operator_id：运营商
$province_id：省id
$order_status：订单状态
$start_datetime：开始实际
$end_datetime：结束时间
$product_name：流量包名称
$page_num：导出数量
$order_page: 1 未支付订单 、已取消订单  2 已完成订单

返回：
list：列表数据
show：分页信息
count：统计信息

*/
    public function order_excel_storing_process($type,$user_id,$proxy_name,$code,$order_code,$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$page_num,$order_page,$sale_id){
        $list = M()->query("CALL p_query_order(".$type.",".$user_id.",'".$proxy_name."','".$code."','".$order_code."','".$mobile."','".$channel_id."','".$bc_channel_id."',".$operator_id.",".$province_id.",'".$order_status."','".$start_datetime."','".$end_datetime."','".$product_name."',".$sale_id.",1,".$page_num.",0,@p_total_count,@p_success_count,@p_success_amount,@p_faile_count,@p_faile_amount,@p_wait_count,@p_wait_amount,@submit_success_count,@submit_success_amount,@p_success_price,@p_faile_price,@p_wait_price,@p_submit_success_price);");
            $result=array();
            foreach($list as $v){
                $rech['order_code'] ="D".$v['order_code'];
                $rech['proxy_code'] =empty($v['proxy_id'])?enterprise_code_list($v['enterprise_id']):proxy_code_list($v['proxy_id']);
                $rech['proxy_name'] =$v['proxy_name'];
            /*    if($v['source_type']==1){
                    $source_type='接口';
                }else if($v['source_type']==2){
                    $source_type ='平台';

                }else{
                    $source_type ='网站';
                }*/
                $rech['source_type']=$v['source_type'];
                $rech['mobile'] ="".$v['mobile'];
                $rech['price'] =$v['price'];
                $rech['discount_price'] =$v['discount_price'];
             /*   if($v['pay_type']==1){
                    $pay_type='账户余额';
                }else if($v['pay_type']==2){
                    $pay_type ='微信';
                }else{
                    $pay_type='支付宝';
                }*/
                $rech['pay_type'] =$v['pay_type'];
                $rech['order_date']=substr($v['order_date'],0,19);
                if($order_page==2){
                    $rech['complete_time']=substr($v['complete_time'],0,19);
                }
                array_push($result,$rech);
            }

        return $result;
    }

    /*
存过订单  只针对index文件
$type:1 已完成订单   2未完成订单   3  缓存订单
$user_id： 用户id
$proxy_name： 用户名称
$mobile： 手机号
$channel_id： 通道
$bc_channel_id： 备用通道
$operator_id：运营商
$province_id：省id
$order_status：订单状态
$start_datetime：开始实际
$end_datetime：结束时间
$product_name：流量包名称
$page:第几页
$page_num：导出数量

返回：
list：返回列表数据
*/
    public function recharge_excel_storing_process($type,$user_id,$proxy_name,$code,$order_code,$mobile,$channel_id,$bc_channel_id,$operator_id,$province_id,$order_status,$start_datetime,$end_datetime,$product_name,$page,$page_num,$sale_id){
        $list = M()->query("CALL p_query_order(".$type.",".$user_id.",'".$proxy_name."','".$code."','".$order_code."','".$mobile."','".$channel_id."','".$bc_channel_id."',".$operator_id.",".$province_id.",'".$order_status."','".$start_datetime."','".$end_datetime."','".$product_name."',".$sale_id.",".$page.",".$page_num.",0,@p_total_count,@p_success_count,@p_success_amount,@p_faile_count,@p_faile_amount,@p_wait_count,@p_wait_amount,@submit_success_count,@submit_success_amount,@p_success_price,@p_faile_price,@p_wait_price,@p_submit_success_price);");
        return $list;
    }



}