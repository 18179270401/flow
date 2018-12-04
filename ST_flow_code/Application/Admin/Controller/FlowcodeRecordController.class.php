<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class FlowcodeRecordController extends CommonController{
    public function index(){
        $flowcode_code = trim(I("flowcode_code"));//流量码号
        $product_name  = trim(I("product_name"));//产品名称
        $phone  = trim(I("phone"));//产品名称
        $status=trim(I("status"));//状态
        $start_datetime=trim(I("start_datetime"));
        $end_datetime=trim(I("end_datetime"));
        $operator_id =trim(I("operator_id"));
        $us_type=D("SysUser")->self_user_type()-1;
        $proxy_id=D("SysUser")->self_proxy_id();
        $enterprise_id=D("SysUser")->self_enterprise_id();
        if($us_type==1){
            $where["f.proxy_id"]=$proxy_id;
        }else{
            $where['f.enterprise_id']=$enterprise_id;
        }
        if(!empty($flowcode_code)){
            $where['f.flowcode_code']=array("like","%$flowcode_code%");
        }
        if(!empty($product_name)){
            $where['f.product_name']=array("like","%$product_name%");
        }
        if(!empty($phone)){
            $where['f.phone']=array("like","%$phone%");
        }
        if(!empty($operator_id)){
            $where['f.operator_id']=$operator_id;
        }
        if($status!=9){
            if($status == 1){
                $where1['o.order_status'] =array("exp","is null");
                $where1['f.order_code']=array("neq","");
                $where1['_logic']="and";
                $where[]=$where1;
            }
            if($status == 2){
                $where1['o.order_status']=array("in","2,5");
                $where1['f.order_code']=array('neq',"");
                $where1['_logic']="and";
                $where[]=$where1;
            }
            if($status==3){
                $where1['o.order_status']=6;
                $where1['f.order_code']=array("eq","");
                $where1['_logic']="or";
                $where[]=$where1;
            }
        }
        if(!empty($start_datetime) || !empty($end_datetime)){
            if(empty($start_datetime)){
                $start_datetime = date('Y-m-d 00:00:00',strtotime($end_datetime));
            }elseif(empty($end_datetime)){
                $end_datetime = date('Y-m-d 23:59:59',strtotime($start_datetime));
            }
            $where['f.order_time'] = array('between',array($start_datetime,$end_datetime));
        }
        $where['f.status']=3;
        $count=M("flowcode as f")
            ->join("t_flow_order as o on f.order_code = o.order_code","left")
            ->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $flow_list=M("flowcode as f")
            ->join("t_flow_order as o on f.order_code = o.order_code","left")
            ->where($where)
            ->field("f.*,o.order_status as orderstatus")
            ->order('modify_date desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        $this->assign("start_datetime",$start_datetime);
        $this->assign("end_datetime",$end_datetime);
        $this->assign('flow_list',get_sort_no($flow_list,$Page->firstRow));  //数据列表
        $this->assign('page',$show);            //分页
        $this->assign('types',get_flowcode_type());//获取属性
        $this->assign('statuss',get_flowcode_status());//获取状态
        $this->assign("operators",get_operator_name());//获取运营商
        $this->display('index');
    }
    

    public function show(){
        $id = trim(I('get.flowcode_id'));
        $info=M("flowcode as f")
            ->join('left join t_flow_order as o on f.order_code = o.order_code',"left")
            ->where(array("flowcode_id"=>$id))
            ->field("f.*,o.order_status as orderstatus")
            ->find();
        $this->assign('info',$info);
        $this->display();
     }
    //导出
    public function  export_excel(){
        $flowcode_code = trim(I("flowcode_code"));//流量码号
        $product_name  = trim(I("product_name"));//产品名称
        $phone  = trim(I("phone"));//产品名称
        $status=trim(I("status"));//状态
        $start_datetime=trim(I("start_datetime"));
        $end_datetime=trim(I("end_datetime"));
        $operator_id =trim(I("operator_id"));
        $us_type=D("SysUser")->self_user_type()-1;
        $proxy_id=D("SysUser")->self_proxy_id();
        $enterprise_id=D("SysUser")->self_enterprise_id();
        if($us_type==1){
            $where["f.proxy_id"]=$proxy_id;
        }else{
            $where['f.enterprise_id']=$enterprise_id;
        }
        if(!empty($flowcode_code)){
            $where['f.flowcode_code']=array("like","%$flowcode_code%");
        }
        if(!empty($product_name)){
            $where['f.product_name']=array("like","%$product_name%");
        }
        if(!empty($phone)){
            $where['f.phone']=array("like","%$phone%");
        }
        if(!empty($operator_id)){
            $where['f.operator_id']=$operator_id;
        }
        if($status!=9){
            if($status == 1){
                $where1['o.order_status'] =array("exp","is null");
                $where1['f.order_code']=array("neq","");
                $where1['_logic']="and";
                $where[]=$where1;
            }
            if($status == 2){
                $where1['o.order_status']=array("in","2,5");
                $where1['f.order_code']=array('neq',"");
                $where1['_logic']="and";
                $where[]=$where1;
            }
            if($status==3){
                $where1['o.order_status']=6;
                $where1['f.order_code']=array("eq","");
                $where1['_logic']="or";
                $where[]=$where1;
            }
        }
        if(!empty($start_datetime) || !empty($end_datetime)){
            if(empty($start_datetime)){
                $start_datetime = date('Y-m-d 00:00:00',strtotime($end_datetime));
            }elseif(empty($end_datetime)){
                $end_datetime = date('Y-m-d 23:59:59',strtotime($start_datetime));
            }
            $where['f.order_time'] = array('between',array($start_datetime,$end_datetime));
        }
        $where['f.status']=3;
        $list =M("flowcode as f")
            ->join("t_flow_order as o on f.order_code = o.order_code","left")
            ->where($where)
            ->field("f.*,o.order_status as orderstatus")
            ->order('modify_date desc')
            ->limit(3000)
            ->select();
        $datas = array();
        $headArr=array();
        $headArr= array_merge($headArr,array("流量码","运营商","流量包名称","手机号","属性","兑换时间","充值状态"));
        foreach ($list as $v) {
            $data=array();
            $data['flowcode_code']=$v['flowcode_code'];
            $data['operator_id'] = get_operator_name($v['operator_id']);
            $data['product_name']=$v['product_name'];
            $data['phone']=$v['phone'];
            $data['type']=get_flowcode_type($v['type']);
            $data['order_time']=$v['order_time'];
            $data['orderstatus'] = "正在送充";
            if(empty($v['order_code']) || $v['orderstatus'] == 6){
                $data['orderstatus'] = "充值失败";
            }elseif($v['orderstatus'] == 2 || $v['orderstatus'] == 5){
                $data['orderstatus'] = "充值成功";
            }
            array_push($datas,$data);
        }
        $title='流量码兑换记录';

        ExportEexcel($title,$headArr,$datas);
    }
}
?>