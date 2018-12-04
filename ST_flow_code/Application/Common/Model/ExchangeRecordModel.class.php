<?php

namespace Common\Model;
use Think\Model;

class ExchangeRecordModel extends Model {

    /**
     *  领取积分基本信息表数据(没有就新增一条记录) 1D
     */
    public function get_flow_base($user_type, $proxy_id, $enterprise_id) {
		$ret = array();
        if(is_numeric($user_type)) {
            $proxy_id       = intval($proxy_id);
            $enterprise_id  = intval($enterprise_id);

            $cond  = array(
                'user_type'     => $user_type,
                'proxy_id'      => $proxy_id,
                'enterprise_id' => $enterprise_id,
            );
            $ret = M('wx_enterprise')->where($cond)->find();
            if(empty($ret)) {
                $self_user_id = D('SysUser')->self_id();
                $peid = (1 == $user_type) ? $proxy_id : $enterprise_id;
                $ins = array(
                    'user_type'         => $user_type,
                    'proxy_id'          => $proxy_id,
                    'enterprise_id'     => $enterprise_id,
                    'flowscore_basic_photo'   => '/Public/Uploads/./Enterprise_scene/2016-05-10/2313130aceab9.png',
                    'flowscore_basic_logo'        => '/Public/Uploads/./Enterprise_scene/2016-05-10/570312323eab9.png',
                    'flowscore_basic_background'    => '/Public/Uploads/./Enterprise_scene/2016-05-10/2323120aceab9.png',
                    'create_user_id'    => $self_user_id,
                    'create_date'       => date('Y-m-d H:i:s'),
                    'modify_user_id'    => $self_user_id,
                    'modify_date'       => date('Y-m-d H:i:s'),
                );
                $info_id = M('wx_enterprise')->add($ins);
                if(!empty($info_id)) {
                    $ins['info_id'] = $info_id;
                } else {
                    write_error_log(array(__METHOD__.':'.__LINE__, '添加签到积分错误 sql== '.M()->getLastSql()));
                }
                $ret = $ins;
            }
        }
        return $ret;
	}


    //积分兑换记录
    public function get_exchange_list($where){
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
        $where['er.exchange_status']=2;//表示购买成功
        $model=M('exchange_record er');
        $count =$model
            ->join('left join t_flow_channel_product as cp on cp.product_id = er.product_id')
            ->join('left join t_flow_proxy as p on p.proxy_id = er.proxy_id and er.user_type=1')
            ->join('left join t_flow_order as o on o.order_code= er.order_code')
            ->join('left join t_flow_enterprise as e on e.enterprise_id= er.enterprise_id and er.user_type=2')
            ->where($where)
            ->count();
        $Page = new \Think\Page($count,20);
        $show = $Page->show();

        $list =$model
            ->join('left join t_flow_channel_product as cp on cp.product_id = er.product_id')
            ->join('left join t_flow_proxy as p on p.proxy_id = er.proxy_id and er.user_type=1')
            ->join('left join t_flow_order as o on o.order_code= er.order_code')
            ->join('left join t_flow_enterprise as e on e.enterprise_id= er.enterprise_id and er.user_type=2')
            ->field('er.exchange_score_id,er.order_code,er.mobile,er.exchage_time,er.exchange_score,cp.operator_id,cp.product_name,er.user_type,er.wx_user_id,p.proxy_name,e.enterprise_name,o.order_status,er.refund_status')
            ->where($where)
            ->order('er.exchage_time desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show);
    }

    public function get_exchange_detail($exchange_score_id){
        $where['er.exchange_score_id']=$exchange_score_id;
        $exchanges=M("exchange_record as er")
            ->join('left join t_flow_channel_product as cp on cp.product_id = er.product_id')
            ->join('left join t_flow_proxy as p on p.proxy_id = er.proxy_id and er.user_type=1')
            ->join('left join t_flow_order as o on o.order_code= er.order_code')
            ->join('left join t_flow_enterprise as e on e.enterprise_id= er.enterprise_id and er.user_type=2')
            ->field('er.exchange_score_id,er.order_code,er.mobile,er.exchage_time,er.exchange_score,cp.operator_id,cp.product_name,er.user_type,p.proxy_name,e.enterprise_name,o.order_status,er.refund_status')
            ->where($where)
            ->find();
        return $exchanges;
    }

    public function refund_score($list){
        $ma=$list;
        M("")->startTrans();
        $type=1;
        foreach($list as $k=>$p){
            if(empty($p['order_code']) && $p['refund_status']!=2 || $p['order_status']==6 && $p['refund_status']!=2){
                $map1=array();
                $map2=array();
                $users=M("wx_user")->where(array("wx_user_id"=>$p['wx_user_id']))->find();
                $map1['user_flow_score']=$users['user_flow_score']+$p['exchange_score'];
                $rs=M("wx_user")->where(array("wx_user_id"=>$p['wx_user_id']))->save($map1);
                $map2["refund_status"]=2;
                $rt=M("exchange_record")->where(array("exchange_score_id"=>$p['exchange_score_id']))->save($map2);
                $list[$k]['refund_status']=2;
                if(!$rs || !$rt){
                    M("")->rollback();
                    $type=2;
                    break;
                }
            }
        }
        if($type==2){
            return $ma;
        }else{
            M("")->commit();
            return $list;
        }

    }

    /**
     *  流量码基本信息表数据(没有就新增一条记录) 1D
     */
    public function get_flowcode_base($user_type, $proxy_id, $enterprise_id) {
        $ret = array();
        if(is_numeric($user_type)) {
            $proxy_id       = intval($proxy_id);
            $enterprise_id  = intval($enterprise_id);
            if($user_type==1){
                $user_id=$proxy_id ;
            }else{
                $user_id=$enterprise_id;
            }
            $cond  = array(
                'user_type'     => $user_type,
                'proxy_id'      => $proxy_id,
                'enterprise_id' => $enterprise_id,
            );
            $ret = M('flowcode_set')->where($cond)->find();
            $data=$this->localencode1($user_type.",".$user_id);
            if(empty($ret)) {
                $self_user_id = D('SysUser')->self_id();
                $ins = array(
                    'user_type'         => $user_type,
                    'proxy_id'          => $proxy_id,
                    'enterprise_id'     => $enterprise_id,
                    'share_img'         => '/Public/Uploads/./Enterprise_scene/2016-11-16/2313130aceab9.png',
                    'logo_img'          => '/Public/Uploads/./Enterprise_scene/2016-11-16/570312323eab9.png',
                    'background_img'    => '/Public/Uploads/./Enterprise_scene/2016-11-16/2323120aceab9.png',
                    'activity_rule'     => '活动规则',
                    'url'               => gethostwithhttp()."/index.php/Activity/Flowcode/index?".$data,
                    'start_time'        => date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))),
                    'end_time'          => date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))),
                    'create_user_id'    => $self_user_id,
                    'create_date'       => date('Y-m-d H:i:s'),
                    'modify_user_id'    => $self_user_id,
                    'modify_date'       => date('Y-m-d H:i:s'),
                );
                $info_id = M('flowcode_set')->add($ins);
                if(!empty($info_id)) {
                    $ins['info_id'] = $info_id;
                } else {
                    write_error_log(array(__METHOD__.':'.__LINE__, '添加流量码错误 sql== '.M()->getLastSql()));
                }
                $ret = $ins;
            }
        }
        return $ret;
    }
    public function localencode1($data) {
        $string = "";
        for($i=0;$i<strlen($data);$i++){
            $ord = ord($data[$i]);
            $ord += 20;
            $string = $string.chr($ord);
        }
        $data = base64_encode($string);
        return $data;
    }
}