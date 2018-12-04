<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class EnterpriseRecordController extends CommonController{
    public function index(){
        set_time_limit(0);
        D("SysUser")->sessionwriteclose();
        $where=array();
        $enterprise_code=trim(I('get.enterprise_code'));
        $enterprise_name=trim(I('get.enterprise_name'));
        $operate_type=trim(I('operate_type'));
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                $this->display();
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }
        if($enterprise_code || $enterprise_name){
             if($enterprise_code){
                 $where['e.enterprise_code'] =$enterprise_code;
             }
        if($enterprise_name){
            $where['e.enterprise_name'] =array('like',$enterprise_name.'%');
        }
            if($operate_type!=''){
                $where['ar.operate_type'] =$operate_type;
            }
           // $where['e.status'] =1;

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

            if(D('SysUser')->is_top_proxy_admin()){
                $where['e.enterprise_id'] = array('in',D('Enterprise')->enterprise_child_ids());
            }else{
                $where['e.enterprise_id'] = array('in',D('Enterprise')->enterprise_ids());
            }
            $list=D('EnterpriseAccount')->all_record($where);
            $this->assign('all_income',$this->all_income($where));
            $this->assign('all_out',$this->all_out($where));
            $this->assign('list',$list['list']);
            $this->assign('page',$list['page']);

        }
        $this->assign('d_sdata',date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))));  //默认开始时间
        $this->assign('d_edata',date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))));  //默认结束时间
        $this->display();        //模板*/
    }

    public function all_income($where){
    $where['ar.balance_type']=1;
    $model=M('account_record as ar');
    $join=array('t_flow_enterprise as e on e.enterprise_id = ar.enterprise_id');
    $sum_results = $model
        ->join($join,'left')->where($where)
        ->field('sum(ar.operater_price) as sum_apply_money')
        ->find();
    return $sum_results['sum_apply_money'];
}

    public function  all_out($where){
        $where['ar.balance_type']=2;
        $model=M('account_record as ar');
        $join=array('t_flow_enterprise as e on e.enterprise_id = ar.enterprise_id');
        $sum_results = $model
            ->join($join,'left')->where($where)
            ->field('sum(ar.operater_price) as sum_apply_money')
            ->find();
        return $sum_results['sum_apply_money'];
    }
    public function export_excel(){
        $where=array();
        $enterprise_code=trim(I('get.enterprise_code'));
        $enterprise_name=trim(I('get.enterprise_name'));
        $operate_type=trim(I('operate_type'));
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                $this->display('index');
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }
        if($enterprise_code || $enterprise_name){
            if($enterprise_code){
                $where['e.enterprise_code'] =$enterprise_code;
            }
            if($enterprise_name){
                $where['e.enterprise_name'] =array('like','%'.$enterprise_name.'%');
            }
            if($operate_type!=''){
                $where['ar.operate_type'] =$operate_type;
            }
          //  $where['e.status'] =1;

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
        }

           $title='企业收支明细表';
           $list=array();
           $headArr=array("企业编号","企业名称","上级代理商编号","上级代理商名称","用途","操作金额(元)",//"操作前余额(元)","操作后余额(元)",
               "操作时间");
        if($enterprise_code || $enterprise_name){
            $dataList=D('EnterpriseAccount')->all_record_excel($where);
            foreach($dataList as $k=>$v){
                $list[$k]['enterprise_code'] =$v['enterprise_code'];
                $list[$k]['enterprise_name'] =$v['enterprise_name'];
                if($v['operate_type']==9 ||$v['operate_type']==10){
                    $list[$k]['obj_proxy_code'] ='--';
                    $list[$k]['obj_proxy_name'] ='--';
                }else{
                    if($v['obj_user_type']==1){
                        $list[$k]['obj_proxy_code'] =obj_data($v['obj_proxy_id'],1,'code');
                        $list[$k]['obj_proxy_name'] =obj_data($v['obj_proxy_id'],1,'name');
                    }else{
                        $list[$k]['obj_enterprise_code'] =obj_data($v['obj_enterprise_id'],2,'code');
                        $list[$k]['obj_enterprise_name'] =obj_data($v['obj_enterprise_id'],2,'name');
                    }
                }
                $list[$k]['operate_type'] =get_operate_type($v['operate_type']);
                if($v['balance_type']==2){
                    $list[$k]['operater_price'] ='-'.$v['operater_price'];
                }else{
                    $list[$k]['operater_price'] =$v['operater_price'];
                }
                //$list[$k]['operater_before_balance'] =$v['operater_before_balance'];
                //$list[$k]['operater_after_balance'] =$v['operater_after_balance'];
                $list[$k]['record_date'] =$v['record_date'];
            }
        }

        ExportEexcel($title,$headArr,$list);
    }

    public function self_record(){
        set_time_limit(0);
        $where=array();
        $operate_type=trim(I('operate_type'));
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                $this->display();
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }

        $where['e.enterprise_id'] = D('SysUser')->self_enterprise_id();
        if($operate_type!=''){
            $where['ar.operate_type'] =$operate_type;
        }
        // $where['e.status'] =1;

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

        $list=D('EnterpriseAccount')->all_record($where);
        $this->assign('list',$list['list']);
        $this->assign('page',$list['page']);
       /* $this->assign('all_income',$this->all_income($where));
        $this->assign('all_out',$this->all_out($where));*/
        $this->assign('d_sdata',date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))));  //默认开始时间
        $this->assign('d_edata',date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))));  //默认结束时间
        $this->display();        //模板*/
    }


    public function self_record_export_excel(){
        $where=array();
        $operate_type=trim(I('operate_type'));
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                $this->display();
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }

        $where['e.enterprise_id'] = D('SysUser')->self_enterprise_id();
        if($operate_type!=''){
            $where['ar.operate_type'] =$operate_type;
        }
        // $where['e.status'] =1;

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

        $title='企业收支明细表';
        $list=array();
        $headArr=array("操作者编号","操作者名称","用途","操作人","操作金额(元)","操作前余额(元)","操作后余额(元)","操作时间");

        $dataList=D('EnterpriseAccount')->all_record_excel($where);
        foreach($dataList as $k=>$v){
            if($v['operate_type']==9 ||$v['operate_type']==10){
                $list[$k]['obj_proxy_code'] ='--';
                $list[$k]['obj_proxy_name'] ='--';
            }else{
                if($v['obj_user_type']==1){
                    $list[$k]['obj_proxy_code'] =obj_data($v['obj_proxy_id'],1,'code');
                    $list[$k]['obj_proxy_name'] =obj_data($v['obj_proxy_id'],1,'name');
                }else{
                    $list[$k]['obj_enterprise_code'] =obj_data($v['obj_enterprise_id'],2,'code');
                    $list[$k]['obj_enterprise_name'] =obj_data($v['obj_enterprise_id'],2,'name');
                }
            }
            $list[$k]['operate_type'] =get_operate_type($v['operate_type']);
            $list[$k]['user_id'] =get_user_name($v['user_id']);
            if($v['balance_type']==2){
                $list[$k]['operater_price'] ='-'.$v['operater_price'];
            }else{
                $list[$k]['operater_price'] =$v['operater_price'];
            }
            $list[$k]['operater_before_balance'] =$v['operater_before_balance'];
            $list[$k]['operater_after_balance'] =$v['operater_after_balance'];
            $list[$k]['record_date'] =$v['record_date'];
        }

        ExportEexcel($title,$headArr,$list);
    }

}
?>