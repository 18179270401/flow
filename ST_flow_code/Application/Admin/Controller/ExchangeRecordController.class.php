<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class ExchangeRecordController extends CommonController{
	/*
	 *积分兑换记录
	 */
	public function  index(){
        D("SysUser")->sessionwriteclose();
        $use_t=D("SysUser")->self_user_type();
        $user_type = D('SysUser')->self_user_type()-1;
        $user_id = D('SysUser')->self_id();
        $self_proxy_id = D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        $mobile = trim(I('mobile'));
        $user_name=trim(I('user_name'));
        $product_name = trim(I('product_name'));
        $start_datetime = trim(I('start_datetime'));
        $end_datetime = trim(I('end_datetime')) ;
        $operator_id=trim(I('operator_id'));
        $refund_status=trim(I('refund_status')); //状态 ：9全部.1已退款，2未退款
        //状态 0全部；1成功；2失败
        $status = trim(I('status'));
        /*if($status == ''){
            $status = 1;
        }*/
        $where = array();
        if($use_t!=3){
            if(!empty($user_name)) {
                $where1['proxy_name'] = array("like","%".$user_name."%");
                $where1['enterprise_name'] = array("like","%".$user_name."%");
                $where1["_logic"] = "or";
                $where[] = $where1;
            }
        }
        if($status!=9){
            if($status == 1){
                $where1['o.order_status'] =array("exp","is null");
                $where1['er.order_code']=array("neq","");
                $where1['_logic']="and";
                $where[]=$where1;
            }
            if($status == 2){
                $where1['o.order_status']=array("in","2,5");
                $where1['er.order_code']=array('neq',"");
                $where1['_logic']="and";
                $where[]=$where1;
            }
            if($status==3){
                $where1['o.order_status']=6;
                $where1['er.order_code']=array("eq","");
                $where1['_logic']="or";
                $where[]=$where1;
            }
        }
        if($refund_status!=9){
            if($refund_status == 1){
                $where['er.refund_status']=2;
            }
            if($refund_status == 2){
                $map2['o.order_status']=6;
                $map2['er.order_code']=array("eq","");
                $map2['_logic']="or";
                $map1[]=$map2;
                $map1['er.refund_status']=array(1,array('exp',"is null"),"or");
                $map1['_logic']="and";
                $where[]=$map1;
            }
        }
        if($operator_id!=9){
            if($operator_id == 1){
                $where['cp.operator_id'] =1;
            }
            if($operator_id== 2){
                $where['cp.operator_id'] = 2;
            }
            if($operator_id==3){
                $where['cp.operator_id'] = 3;
            }
        }
        if($use_t==3) {
            $where['er.user_type'] = $user_type;
            if ($user_type == '1') {
                //代理商
                $where['er.proxy_id'] = $self_proxy_id;
            } else if ($user_type == '2') {
                //企业
                $where['er.enterprise_id'] = $self_enterprise_id;
            }
        }

        if($mobile){
            $where['er.mobile'] = array("like",'%'.$mobile."%");
        }
        if($product_name){
            $where['cp.product_name']=array('like','%'.$product_name.'%');
        }
        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['er.exchage_time'] = array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['er.exchage_time'] = array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['er.exchage_time'] = array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            //$end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $end_datetime= strtotime($start_datetime)-2592000;
            $e_time=start_time(date('Y-m-d',$end_datetime));
            $where['er.exchage_time']= array('between',array($e_time,$start_datetime));
        }
        $list=D('ExchangeRecord')->get_exchange_list($where);
        $this->assign("use",$use_t);
        //加载模板
        $list['list']=D("ExchangeRecord")->refund_score($list['list']);
        $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
        $end_datetime= strtotime($start_datetime)-2592000;
        $e_time=start_time(date('Y-m-d',$end_datetime));
        $this->assign('default_end',$start_datetime);
        $this->assign('default_start',$e_time);
        $this->assign('list',$list['list']);  //数据列表
        $this->assign('page',$list['page']);  //分页
        $this->display();        //模板
    }

    public function show(){
        $id = trim(I('exchange_score_id'));
        $info = D('ExchangeRecord')->get_exchange_detail($id);
        $use_t=D('SysUser')->self_user_type();
        $this->assign("usr",$use_t);
        $this->assign('info',$info);
        $this->display();
    }

    public function refund_score(){
        $msg="系统错误！";
        $status="error";
        M("")->startTrans();
        $exchange_score_id = trim(I('exchange_score_id'));
        $p=M("exchange_record er")->where(array("er.exchange_score_id"=>$exchange_score_id)) ->join('left join t_flow_order as o on o.order_code= er.order_code')->find();
        if(empty($p['order_code']) && $p['refund_status']!=2 || $p['order_status']==6 && $p['refund_status']!=2){
            $users=M("wx_user")->where(array("wx_user_id"=>$p['wx_user_id']))->find();
            $map1['user_flow_score']=$users['user_flow_score']+$p['exchange_score'];
            $rs=M("wx_user")->where(array("wx_user_id"=>$p['wx_user_id']))->save($map1);
            $map2["refund_status"]=2;
            $rt=M("exchange_record")->where(array("exchange_score_id"=>$p['exchange_score_id']))->save($map2);
            if($rs && $rt){
                M("")->commit();
                $msg="退积分成功！";
                $status="success";
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
            }else{
                M("")->rollback();
                $msg="退积分失败！";
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
            }
        }else{
            $msg="退积分信息有误！";
            M("")->rollback();
            $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
        }

    }
}
?>