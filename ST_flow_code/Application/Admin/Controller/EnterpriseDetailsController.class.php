<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;

class EnterpriseDetailsController extends CommonController{
	public function recharge_record(){
        set_time_limit(0);
        D("SysUser")->sessionwriteclose();
        $enterprise_code=trim(I('enterprise_code'));
        $enterprise_name=trim(I('enterprise_name'));
        $obj_proxy_code=trim(I('obj_proxy_code'));
        $obj_proxy_name=trim(I('obj_proxy_name'));
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                $this->display("recharge_record");
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }

        if($start_datetime || $end_datetime){
            if($start_datetime && $end_datetime){
                $where['ar.record_date'] = array('between',array($start_datetime,$end_datetime));
            }
            if($start_datetime ==""){
                $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
                $where['ar.record_date']= array('between',array($start_datetime,$end_datetime));
            }
            if($end_datetime ==""){
                $end_datetime = end_time(msubstr($start_datetime,0,10,"utf-8",false));
                $where['ar.record_date'] = array('between',array($start_datetime,$end_datetime));
            }

        }else {
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $where['ar.record_date']= array('between',array($start_datetime,$end_datetime));
        }

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
        $where['ar.operate_type']=array('in',array(2,8)); //表示充值
        if(D('SysUser')->is_top_proxy_admin()){
            $where['e.enterprise_id'] = array('in',D('Enterprise')->enterprise_child_ids());
        }else{
            $where['e.enterprise_id'] = array('in',D('Enterprise')->enterprise_ids());
        }

	   	$list=D('EnterpriseAccount')->recharge_record($where);
        //var_dump($list);exit;
	   	$this->assign('list',$list['list']);
	   	$this->assign('page',$list['page']);
        $this->assign('sum_results',$list['sum_results']);
        $this->assign('d_sdata',date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))));  //默认开始时间
        $this->assign('d_edata',date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))));  //默认结束时间
	   	$this->display("recharge_record");        //模板*/
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
      //判断时间是否在一个月内
      if($start_datetime!="" && $end_datetime!=""){
          if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
              $this->display("recharge_record");
              echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
          }
      }
      if($start_datetime || $end_datetime){
          if($start_datetime && $end_datetime){
              $where['ar.record_date'] = array('between',array($start_datetime,$end_datetime));
          }
          if($start_datetime ==""){
              $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
              $where['ar.record_date']= array('between',array($start_datetime,$end_datetime));
          }
          if($end_datetime ==""){
              $end_datetime = end_time(msubstr($start_datetime,0,10,"utf-8",false));
              $where['ar.record_date'] = array('between',array($start_datetime,$end_datetime));
          }

      }else {
          $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
          $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
          $where['ar.record_date']= array('between',array($start_datetime,$end_datetime));
      }

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

      $where['ar.operate_type']=array('in',array(2,8)); //表示充值
      if(D('SysUser')->is_top_proxy_admin()){

          $where['e.enterprise_id'] = array('in',D('Enterprise')->enterprise_child_ids());
      }else{
          $where['e.enterprise_id'] = array('in',D('Enterprise')->enterprise_ids());
      }
      $list=D('EnterpriseAccount')->recharge_record_excel($where);
     $headArr=array("企业编号","企业名称","上级代理编号","上级代理名称","操作金额(元)",//"操作前余额(元)","操作后余额(元)",
         "操作时间");
     ExportEexcel("企业充值明细表",$headArr,$list);
  }
}

?>