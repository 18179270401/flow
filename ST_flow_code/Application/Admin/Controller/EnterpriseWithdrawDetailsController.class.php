<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;

class EnterpriseWithdrawDetailsController extends CommonController{
	public function widthdraw_record(){
        D("SysUser")->sessionwriteclose();
        $enterprise_code=trim(I('enterprise_code'));
        $enterprise_name=trim(I('enterprise_name'));
        $obj_proxy_code=trim(I('obj_proxy_code'));
        $obj_proxy_name=trim(I('obj_proxy_name'));
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        if($enterprise_code){
                $where['e.enterprise_code'] =array("like","%".$enterprise_code."%");
        }
        if($enterprise_name){
                $where['e.enterprise_name'] =array("like","%".$enterprise_name."%");
        }
        if($obj_proxy_code){
                $where['up.proxy_code'] =array("like","%".$obj_proxy_code."%");
        }
        if($obj_proxy_name){
                $where['up.proxy_name'] =array("like","%".$obj_proxy_name."%");
        }
        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['ar.record_date']=array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['ar.record_date']= array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['ar.record_date'] =array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
                $where['ar.record_date'] = array('between',array($start_datetime,$end_datetime));
        }
	   	$list=D('EnterpriseAccount')->widthdraw_record($where);
	   	$this->assign('list',$list['list']);
	   	$this->assign('page',$list['page']);
        $this->assign('sum_results',$list['sum_results']);
        $this->assign('d_sdata',date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))));  //默认开始时间
        $this->assign('d_edata',date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))));  //默认结束时间
        $this->display("withdrawRecord");        //模板*/
	}

	public function show(){
		$msg="系统错误";
		$status="error";
		if(trim(I('record_id'))){
           	$where['ar.record_id']=trim(I('record_id'));//充值
           	$list=M('AccountRecord as ar')
           		->join('left join t_flow_enterprise as e on e.enterprise_id = ar.enterprise_id')
                ->join('left join t_flow_proxy as up on up.proxy_id = ar.obj_proxy_id')
                ->where($where)
                ->field("ar.*,e.enterprise_name,e.enterprise_code,up.proxy_code as top_proxy_code,up.proxy_name as obj_proxy_name")
                ->find();
            if(empty($list)){
            	$this->ajaxReturn(array('msg'=>"企业信息不存在",'status'=>$status));	
            }
           	$this->assign("list",$list);
           	$this->display('detailed');
       }else{
       	   	$this->ajaxReturn(array('msg'=>"查看详情失败",'status'=>$status));
       }
   }

   public function export_excel(){
    $enterprise_code=trim(I('enterprise_code'));
    $enterprise_name=trim(I('enterprise_name'));
    $obj_proxy_code=trim(I('obj_proxy_code'));
    $obj_proxy_name=trim(I('obj_proxy_name'));
    $start_datetime = trim(I('get.start_datetime'));   //开始时间
    $end_datetime = trim(I('get.end_datetime'));   //结束时间
    if($enterprise_code){
            $where['e.enterprise_code'] =array("like","%".$enterprise_code."%");
    }
    if($enterprise_name){
            $where['e.enterprise_name'] =array("like","%".$enterprise_name."%");
    }
    if($obj_proxy_code){
            $where['up.proxy_code'] =array("like","%".$obj_proxy_code."%");
    }
    if($obj_proxy_name){
            $where['up.proxy_name'] =array("like","%".$obj_proxy_name."%");
    }
       if($start_datetime or $end_datetime){
           if($start_datetime && $end_datetime){
               $where['ar.record_date']=array('between',array(start_time($start_datetime),end_time($end_datetime)));
           }elseif($start_datetime){
               $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
               $where['ar.record_date']= array('between',array(start_time($start_datetime),$end_datetime));
           }elseif($end_datetime){
               $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
               $where['ar.record_date'] =array('between',array($start_datetime,end_time($end_datetime)));
           }
       }else{
           $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
           $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
           $where['ar.record_date'] = array('between',array($start_datetime,$end_datetime));
       }
    $user_type=D('SysUser')->self_user_type();
    $list = array();
    $user_id=D("SysUser")->self_id;
    $model=M('account_record as ar');
    //固定的条件
    $where['up.status']=1; //代理商状态
    $where['e.status']=1;//企业状态
    $where['ar.operate_type']=3; //表示充值
    $where['ar.enterprise_id']=array("gt",0);
    //尚通运营端 
    if(!D('SysUser')->is_admin()){
        $ids=D("Enterprise")->enterprise_child_ids();//获取所有可操作企业号
        $is=M("EnterpriseUser")->where(array("user_id"=>$user_id))->distinct(true)->field("enterprise_id")->select();
        if($is){
            if($ids){
                $ids=$ids.",";
            }
            foreach ($is as $v) {
                $ids.=$v['enterprise_id'];
            }
        }else{
            if(!$ids){
                return;
            }
        }
        $where["e.enterprise_id"]=array("in",$ids);
    }
    $list =$model
        ->join('left join t_flow_enterprise as e on e.enterprise_id = ar.enterprise_id')
        ->join('left join t_flow_proxy as up on up.proxy_id = ar.obj_proxy_id')
        ->where($where)
        ->limit("0","3000")
        ->order('ar.record_id desc')
        //->field("e.enterprise_code,e.enterprise_name,up.proxy_code as top_proxy_code,up.proxy_name as obj_proxy_name,ar.operater_price,ar.operater_before_balance,ar.operater_after_balance,ar.record_date")
        ->field("e.enterprise_code,e.enterprise_name,up.proxy_code as top_proxy_code,up.proxy_name as obj_proxy_name,ar.operater_price,ar.record_date")
        ->select();
    $data=array();
    $row=array();
    $headArr=array("企业编号","企业名称","上级代理编号","上级代理名称","操作金额(元)",//"操作前余额(元)","操作后余额(元)",
        "操作时间");
    ExportEexcel("企业提现明细",$headArr,$list);
  }
}

?>