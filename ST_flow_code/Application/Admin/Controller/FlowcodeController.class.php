<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class FlowcodeController extends CommonController{
    /*
	 * 订单表
	 */
    public function index(){
        $flowcode_code = trim(I("flowcode_code"));//流量码号
        $product_name  = trim(I("product_name"));//产品名称
        $type=trim(I("type"));//属性
        $status=trim(I("status"));//状态
        $start_datetime=trim(I("start_datetime"));
        $end_datetime=trim(I("end_datetime"));
        $us_type=D("SysUser")->self_user_type()-1;
        $proxy_id=D("SysUser")->self_proxy_id();
        $enterprise_id=D("SysUser")->self_enterprise_id();
        if($us_type==1){
            $where["proxy_id"]=$proxy_id;
        }else{
            $where['enterprise_id']=$enterprise_id;
        }

        if(!empty($flowcode_code)){
            $where['flowcode_code']=array("like","%$flowcode_code%");
        }
        if(!empty($product_name)){
            $where['product_name']=array("like","%$product_name%");
        }
        if(!empty($type)){
            $where['type']=$type;
        }
        if(!empty($status)){
            $where['status']=$status;
        }
        if(!empty($start_datetime) || !empty($end_datetime)){
            if(empty($start_datetime)){
                $start_datetime = date('Y-m-d 00:00:00',strtotime($end_datetime));
            }elseif(empty($end_datetime)){
                $end_datetime = date('Y-m-d 23:59:59',strtotime($start_datetime));
            }
            $where['create_date'] = array('between',array($start_datetime,$end_datetime));
        }
        $count=M("flowcode")->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $flow_list=M("flowcode")->where($where)
            ->order('modify_date desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        $this->assign("start_datetime",$start_datetime);
        $this->assign("end_datetime",$end_datetime);
        $this->assign('flow_list',get_sort_no($flow_list,$Page->firstRow));  //数据列表
        $this->assign('page',$show);            //分页
        $this->assign('types',get_flowcode_type());//获取属性
        $this->assign('statuss',get_flowcode_status());//获取状态
        $this->display('index');
    }

    /**
     * 导出excel
     */
    public function export_excel(){
        $flowcode_code = trim(I("flowcode_code"));//流量码号
        $product_name  = trim(I("product_name"));//产品名称
        $type=trim(I("type"));//属性
        $status=trim(I("status"));//状态
        $us_type=D("SysUser")->self_user_type()-1;
        $proxy_id=D("SysUser")->self_proxy_id();
        $enterprise_id=D("SysUser")->self_enterprise_id();
        if($us_type==1){
            $where["proxy_id"]=$proxy_id;
        }else{
            $where['enterprise_id']=$enterprise_id;
        }
        if(!empty($flowcode_code)){
            $where['flowcode_code']=array("like","%$flowcode_code%");
        }
        if(!empty($product_name)){
            $where['product_name']=array("like","%$product_name%");
        }
        if(!empty($type)){
            $where['type']=$type;
        }
        if(!empty($status)){
            $where['status']=$status;
        }
        if(!empty($start_datetime) || !empty($end_datetime)){
            if(empty($start_datetime)){
                $start_datetime = date('Y-m-d 00:00:00',strtotime($end_datetime));
            }else{
                $end_datetime = date('Y-m-d 23:59:59',strtotime($start_datetime));
            }
            $where['modify_date'] = array('between',array($end_datetime,$start_datetime));
        }
        $list=M("flowcode")->where($where)
            ->order('modify_date desc')
            ->limit(3000)
            ->select();
        $datas = array();
        $headArr=array();
        $headArr=array_merge($headArr,array("流量码","流量包名称","属性","状态","生成时间","截止时间"));
        foreach ($list as $v) {
            $data['flowcode_code'] = $v['flowcode_code'];
            $data['product_name'] = $v['product_name'];
            $data['type'] = get_flowcode_type($v['type']);
            $data['status'] = get_flowcode_status($v['status']);
            $data['create_date'] = $v['create_date'];
            $data['end_time']=$v['end_time']?$v['end_time']:"--";
            array_push($datas,$data);
        }
        $title='流量码生成管理';
        ExportEexcel($title,$headArr,$datas);
    }

    public function add(){
        $this->assign('product_names',get_flowcode_product());//获取可用流量包
        $this->assign('types',get_flowcode_type());//获取流量包属性
        $this->display();
    }
    public function  insert(){
        $msg="系统错误！";
        $status="error";
        $type=trim(I("type"));
        $product_name=trim(I("product_name"));
        $number=trim(I("number"));
        $end_time=trim(I("end_time"));
        $operator_id=trim(I("operator_id"));
        if(empty($type)){
            $msg="请选择流量码属性！";
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
        if(empty($product_name)){
            $msg="请选择流量包！";
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
        if(empty($number)){
            $msg="请输入生成个数！";
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
        $operator_id=get_flowcode_operator($type);
        $i=0;
        $size=substr($product_name,-1)=="G"?substr($product_name,0,strlen($product_name)-1)*1024:substr($product_name,0,strlen($product_name)-1);
        $arr=array();//记录生成的流量码号
        $data=array();//记录存入数据库信息
        $self_user_type = D('SysUser')->self_user_type(); //1 2 3
        $user_type = $self_user_type - 1; //1：代理商、2：企业
        $self_proxy_id = D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        $self_user_id = D('SysUser')->self_id();
        $info=M("enterprise")->where("enterprise_id=".$self_enterprise_id)->find();
        $str=substr($info['enterprise_code'],2,5);
        while($i<$number){
            $flowcode_code=$this->get_flowcode($arr,$str);
            array_push($arr,$flowcode_code);
            $m=array(
                'flowcode_code' => $flowcode_code,
                'product_name'  => $product_name,
                'size'          => $size,
                'type'          => $type,
                'operator_id'   => $operator_id,
                'status'        => 1,
                'end_time'      => $end_time?$end_time:null,
                'user_type'     => $user_type,
                'proxy_id'      => $self_proxy_id,
                'enterprise_id' => $self_enterprise_id,
                'create_user_id'=> $self_user_id,
                'create_date'   => date('Y-m-d H:i:s'),
                'modify_user_id'=> $self_user_id,
                'modify_date'   => date('Y-m-d H:i:s'),
            );
            array_push($data,$m);
            $i++;
        }
        M("flowcode")->startTrans();
        $id=M("flowcode")->addAll($data);
        if($id){
            $msg="流量码生成成功！";
            $status="success";
            M("flowcode")->commit();
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }else{
            $msg="流量码生成失败！";
            M("flowcode")->rollback();
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }
    }
    /**
     * 流量码详细信息
     */
    public function show(){

    }

    /**
     * 激活流量码
     */
    public function activated(){
        $msg="系统错误！";
        $flowcode_id=trim(I("flowcode_id"));
        if(!empty($flowcode_id)){
            $where=array("flowcode_id"=>$flowcode_id,"status"=>1);
            $info=M("flowcode")->where($where)->find();
            if(!empty($info)){
                $info['status']=2;
                $id=M("flowcode")->save($info);
                if($id){
                    $msg="激活成功！";
                    $status="success";
                    $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
                }else{
                    $msg="激活失败！";
                    $status="error";
                    $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
                }
            }else{
                $msg="信息错误！";
                $status="error";
                $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
            }
        }else{
            $msg="请选择流量码！";
            $status="error";
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }
    }

    public function pl_activated(){
        $arr=I("post.");
        $flowcodes=$arr['order_ids'];
        if(empty($flowcodes)){
            $msg="请选择流量码！";
            $status="error";
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }
        $i=0;//失败的个数
        $j=0;//成功的个数
        foreach ($flowcodes as $v){
            $where=array("flowcode_id"=>$v,"status"=>1);
            $info=M("flowcode")->where($where)->find();
            if(!empty($info)){
                $info['status']=2;
                $id=M("flowcode")->save($info);
                if($id){
                    $j++;
                }else{
                    $i++;
                }
            }else{
                $i++;
            }
        }
        if($i>0 && $j>0){
            $msg="流量码激活成功".$j."个，激活失败".$i."个！";
            $status="success";
        }else if($j>0){
            $msg="全部激活成功！";
            $status="success";
        }else{
            $msg="全部激活失败！";
            $status="error";
        }
        $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
    }

    /**
     * 作废流量码
     */
    public function invalid(){
        $msg="系统错误！";
        $flowcode_id=trim(I("flowcode_id"));
        if(!empty($flowcode_id)){
            $where=array("flowcode_id"=>$flowcode_id);
            $info=M("flowcode")->where($where)->find();
            if(!empty($info)){
                if($info['status']==3 || $info['status']==4){
                    $msg="作废失败！";
                    $status="error";
                    $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
                }
                $info['status']=4;
                $id=M("flowcode")->save($info);
                if($id){
                    $msg="作废成功！";
                    $status="success";
                    $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
                }else{
                    $msg="作废失败！";
                    $status="error";
                    $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
                }
            }else{
                $msg="信息错误！";
                $status="error";
                $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
            }
        }else{
            $msg="信息错误！";
            $status="error";
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }
    }

    //获取流量码号,$arr为当前的
    public function get_flowcode($arr,$str){
        D("SysUser")->self_enterprise_id();
        //"a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z",
        $arr=array("1","2","3","4","5","6","7","8","9","0","A","B","C","D","E","F","G","H","J","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
        $len=count($arr)-1;
        $i=0;
        while(1){
            $s=$str;//记录随机数
            while($i<6){
                $s.=$arr[rand(0,$len)];
                $i++;
            }
            if(!in_array($s,$arr)){
                $where['flowcode_code']=$s;
                $info=M("flowcode")->where($where)->find();
                if(!$info){
                    break;
                }
            }
        }
        return $s;
    }
}
?>