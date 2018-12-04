<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class CashRecordController extends CommonController{
	/*
	 *现金收入记录
	 */
	public function  income(){
        set_time_limit(0);
            D("SysUser")->sessionwriteclose();
            $user_type=D('SysUser')->self_user_type();
            $operate_type=trim(I('operate_type'));
            $start_datetime=trim(I('start_datetime'));
            $end_datetime=trim(I('end_datetime')) ;
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                $this->display();
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }
        if($start_datetime || $end_datetime){
            if($start_datetime && $end_datetime){
                $where['ar.operation_date'] = array('between',array($start_datetime,$end_datetime));
            }
            if($start_datetime ==""){
                $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
                $where['ar.operation_date'] = array('between',array($start_datetime,$end_datetime));
            }
            if($end_datetime ==""){
                $end_datetime = end_time(msubstr($start_datetime,0,10,"utf-8",false));
                $where['ar.operation_date'] = array('between',array($start_datetime,$end_datetime));
            }
        }else {
            $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            //$end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $end_datetime= strtotime($start_datetime)-2592000;
            $e_time=start_time(date('Y-m-d',$end_datetime));
            $d_sdata=$e_time;
            $d_edata=$start_datetime;
            $where['ar.operation_date']= array('between',array($e_time,$start_datetime));
        }
            if($user_type==2){
                $obj_enterprise_name=trim(I('obj_enterprise_name'));
                $obj_proxy_name=trim(I('obj_proxy_name'));

                if($obj_enterprise_name){
                    $where['oe.enterprise_name']=array("like","%".$obj_enterprise_name."%");
                }
                if($obj_proxy_name){
                    $where['op.proxy_name']=array("like","%".$obj_proxy_name."%");
                }

                    $where['ar.user_type']=1; //用户类型为企业
                    $where['ar.balance_type']=1; //账户收入*/
                    $where['ar.proxy_id']=D('SysUser')->self_proxy_id().','.D('Proxy')->proxy_child_ids(); //代理商为正常


                    //$where['e.approve_status']=1;
            }
            //var_dump($where);
            if($user_type==3){
                    $where['ar.user_type']=2; //用户类型为企业
                    $where['e.status']=1; //企业状态为正常
                    $where['e.approve_status']=1;
                    $where['e.enterprise_id']=D('SysUser')->self_enterprise_id();
            }
        if($operate_type!=''){
            $where['ar.operate_type'] =$operate_type;
        }/*else{
            $where['ar.operate_type']=array('neq',6);//不显示分红
        }*/
        //获取当前用户的数据权限
        $where['ar.balance_type']=1;//收支类型：收入
        $list=D('AccountRecord')->cash_record($where);
       // var_dump($list);
        //加载模板
        $this->assign('sum_results',$list['sum_results']);
        $this->assign('list',$list['list']);  //数据列表
        $this->assign('page',$list['page']);  //分页
        $this->assign('user_type',$user_type);  //平台
        $this->assign('d_sdata',$d_sdata);  //默认开始时间
        $this->assign('d_edata',$d_edata);  //默认结束时间
        $this->assign('url','CashRecord/income_excel');
        /*if($user_type==2){
            $this->assign('proxy_list',D('AccountRecord')->all_proxy());
            $this->assign('enterprise_list',D('AccountRecord')->all_enterprise());
        }*/
        $this->display();        //模板
    }

        //现金支出记录
        public function payout(){
            D("SysUser")->sessionwriteclose();
            $user_type=D('SysUser')->self_user_type();
            $operate_type=trim(I('operate_type'));
            $start_datetime=trim(I('start_datetime')) ;
            $end_datetime=trim(I('end_datetime')) ;

            //判断时间是否在一个月内
            if($start_datetime!="" && $end_datetime!=""){
                if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                    $this->display();
                    echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
                }
            }
            if($start_datetime || $end_datetime){
                if($start_datetime && $end_datetime){
                    $where['ar.operation_date'] = array('between',array($start_datetime,$end_datetime));
                }
                if($start_datetime ==""){
                    $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
                    $where['ar.operation_date'] = array('between',array($start_datetime,$end_datetime));
                }
                if($end_datetime ==""){
                    $end_datetime = end_time(msubstr($start_datetime,0,10,"utf-8",false));
                    $where['ar.operation_date'] = array('between',array($start_datetime,$end_datetime));
                }
            }else {
                $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                //$end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
                $end_datetime= strtotime($start_datetime)-2592000;
                $e_time=start_time(date('Y-m-d',$end_datetime));
                $d_sdata=$e_time;
                $d_edata=$start_datetime;
                $where['ar.operation_date']= array('between',array($e_time,$start_datetime));
            }
            if($user_type==2){
                $obj_enterprise_id=trim(I('obj_enterprise_id'));
                $obj_proxy_id=trim(I('obj_proxy_id'));
                if($obj_enterprise_id){
                    $where['ar.obj_enterprise_id']=$obj_enterprise_id;
                }
                if($obj_proxy_id){
                    $where['ar.obj_proxy_id']=$obj_proxy_id;
                }

                $obj_enterprise_name=trim(I('obj_enterprise_name'));
                $obj_proxy_name=trim(I('obj_proxy_name'));

                if($obj_enterprise_name){
                    $where['oe.enterprise_name']=array("like","%".$obj_enterprise_name."%");
                }
                if($obj_proxy_name){
                    $where['op.proxy_name']=array("like","%".$obj_proxy_name."%");
                }

                $where['ar.user_type']=1; //用户类型为企业
                $where['ar.balance_type']=1; //账户收入*/
              //  $where['p.status']=1; //代理商为正常
                $where['ar.proxy_id']=D('SysUser')->self_proxy_id().','.D('Proxy')->proxy_child_ids(); //代理商为正常
            }
            if($user_type==3){
                $where['ar.user_type']=2; //用户类型为企业
                $where['e.status']=1; //企业状态为正常
               // $where['op.status']=1; //代理商为正常
                $where['e.approve_status']=1;
                $where['e.enterprise_id']=D('SysUser')->self_enterprise_id();
            }
            if($operate_type!=''){
                $where['ar.operate_type'] =$operate_type;
            }/*else{
                $where['ar.operate_type']=array('neq',6);//不显示分红
            }*/
            $where['ar.balance_type']=2;//收支类型：支出

            //获取用户的数据权限
           /*if($user_type==2){
            $map['status'] = array('neq',2);
            $map['top_proxy_id'] = D('SysUser')->self_proxy_id();
            $map['approve_status'] =1;
            $range_list = M('Proxy')->field('proxy_id')->where($map)->select();
            $ids="";
            if($range_list){
                foreach($range_list as $v){
                    $ids.= ','.$v['proxy_id']; 
                }
                $ids = substr($ids,1,strlen($ids)-1);
            }
            if($ids!=""){
                $ids=$ids.","."0";
                $pids="";
                $p_list=D("Proxy")->proxy_child();
                if($p_list){
                foreach($p_list as $v){
                    $pids.= ','.$v; 
                }
                $pids = substr($pids,1,strlen($pids)-1);
                }
                if($pids!=""){
                    $map1["ar.obj_proxy_id"]=array(array('in',$pids),array('not in',$ids),"or");
                }else{
                    $map1['ar.obj_proxy_id']=array('not in',$ids);
                }
            }else{
                 $map1['ar.obj_proxy_id']=array('neq',"0");
            }
            $map1['_logic']="or";
            $eids=D("Enterprise")->enterprise_ids();
            if($eids){
                $map1['ar.obj_enterprise_id']=array('in',$eids);
            }else{
                $map1['ar.obj_enterprise_id']=array('eq',"-1");   
            }
            $where[]=$map1;
        }*/
            $list=D('AccountRecord')->cash_record($where);
            $this->assign('sum_results',$list['sum_results']);
            $this->assign('list',$list['list']);  //数据列表
            $this->assign('page',$list['page']);  //分页
            $this->assign('user_type',$user_type);  //平台
            $this->assign('d_sdata',$d_sdata);  //默认开始时间
            $this->assign('d_edata',$d_edata);  //默认结束时间
            $this->assign('url','CashRecord/payout_excel');
           /* if($user_type==2){
                $this->assign('proxy_list',D('AccountRecord')->all_proxy());
                $this->assign('enterprise_list',D('AccountRecord')->all_enterprise());
            }*/
            $this->display();        //模板

        }

//详细信息
    public function  detailed(){
        $where['record_id']=I('record_id');
        $result=D('AccountRecord')->cashRecord_detailed($where);
        $this->assign($result);  //平台
        $this->assign('user_type',D('SysUser')->self_user_type());  //平台
        $this->display();
    }

//现金收入导出
    public function income_excel(){
        //收入
        $cash_type=trim(I("cash_type"));
        $user_type=D('SysUser')->self_user_type();
        $start_datetime=trim(I('start_datetime'));
        $end_datetime=trim(I('end_datetime')) ;
        $operate_type=trim(I('operate_type'));
//判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                $this->display('income');
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }
        if($start_datetime || $end_datetime){
            if($start_datetime && $end_datetime){
                $where['ar.operation_date'] = array('between',array($start_datetime,$end_datetime));
            }
            if($start_datetime ==""){
                $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
                $where['ar.operation_date'] = array('between',array($start_datetime,$end_datetime));
            }
            if($end_datetime ==""){
                $end_datetime = end_time(msubstr($start_datetime,0,10,"utf-8",false));
                $where['ar.operation_date'] = array('between',array($start_datetime,$end_datetime));
            }
        }else {
            $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            //$end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $end_datetime= strtotime($start_datetime)-2592000;
            $e_time=start_time(date('Y-m-d',$end_datetime));
            $where['ar.operation_date']= array('between',array($e_time,$start_datetime));
        }
            if($user_type==2){
               /* $obj_enterprise_id=trim(I('obj_enterprise_id'));
                $obj_proxy_id=trim(I('obj_proxy_id'));
                if($obj_enterprise_id){
                    $where['ar.obj_enterprise_id']=$obj_enterprise_id;
                }
                if($obj_proxy_id){
                    $where['ar.obj_proxy_id']=$obj_proxy_id;
                }*/

                $obj_enterprise_name=trim(I('obj_enterprise_name'));
                $obj_proxy_name=trim(I('obj_proxy_name'));

                if($obj_enterprise_name){
                    $where['oe.enterprise_name']=array("like","%".$obj_enterprise_name."%");
                }
                if($obj_proxy_name){
                    $where['op.proxy_name']=array("like","%".$obj_proxy_name."%");
                }

                $where['ar.user_type']=1; //用户类型为企业
                $where['ar.balance_type']=1; //账户收入*/
               // $where['p.status']=1; //代理商为正常
                $where['ar.proxy_id']=D('SysUser')->self_proxy_id().','.D('Proxy')->proxy_child_ids(); //代理商为正常
            }
            if($user_type==3){
                $where['ar.user_type']=2; //用户类型为企业
                $where['e.status']=1; //企业状态为正常
                //$where['op.status']=1; //代理商为正常
                $where['e.approve_status']=1;
                $where['e.enterprise_id']=D('SysUser')->self_enterprise_id();
            }
            $where['ar.balance_type']=1;//收支类型：收入
        if($operate_type!=''){
            $where['ar.operate_type'] =$operate_type;
        }/*else{
            $where['ar.operate_type']=array('neq',6);//不显示分红
        }*/

         //获取用户的数据权限
        /*if($user_type==2){
            $map['status'] = array('neq',2);
            $map['top_proxy_id'] = D('SysUser')->self_proxy_id();
            $map['approve_status'] =1;
            $range_list = M('Proxy')->field('proxy_id')->where($map)->select();
            $ids="";
            if($range_list){
                foreach($range_list as $v){
                    $ids.= ','.$v['proxy_id']; 
                }
                $ids = substr($ids,1,strlen($ids)-1);
            }
            if($ids!=""){
                $ids=$ids.","."0";
                $pids="";
                $p_list=D("Proxy")->proxy_child();
                if($p_list){
                foreach($p_list as $v){
                    $pids.= ','.$v; 
                }
                $pids = substr($pids,1,strlen($pids)-1);
                }
                if($pids!=""){
                    $map1["ar.obj_proxy_id"]=array(array('in',$pids),array('not in',$ids),"or");
                }else{
                    $map1['ar.obj_proxy_id']=array('not in',$ids);
                }
            }else{
                 $map1['ar.obj_proxy_id']=array('neq',"0");
            }
            $map1['_logic']="or";
            $eids=D("Enterprise")->enterprise_ids();
            if($eids){
                $map1['ar.obj_enterprise_id']=array('in',$eids);
            }else{
                $map1['ar.obj_enterprise_id']=array('eq',"-1");   
            }
            $where[]=$map1;
        }*/
        $list=D('AccountRecord')->cash_excel($where);
        $data=array();
        foreach ($list as $v) {
            if($v['operate_type']==9||$v['operate_type']==10){
                $cash['proxy_code']='--';
                $cash['proxy_name']='--';
                if($user_type!=3){
                    $cash['type']="--";
                }
                $cash['operate_type'] =get_operate_type($v['operate_type']);
            }else{
                if($v['obj_user_type']==1){
                    $cash['proxy_code']=obj_data($v['obj_proxy_id'],1,'code');
                    $cash['proxy_name']=obj_data($v['obj_proxy_id'],1,'name');
                    if($user_type!=3){
                            $cash['type']="代理商";
                    }
                    $cash['operate_type'] =get_operate_type($v['operate_type']);
                }else{
                    $cash['enterprise_code']=obj_data($v['obj_enterprise_id'],2,'code');
                    $cash['enterprise_name']=obj_data($v['obj_enterprise_id'],2,'name');
                    if($user_type!=3){
                        $cash['type']="企业";
                    }
                    $cash['operate_type'] =get_operate_type($v['operate_type'],1);
                }
            }
            $cash['operater_price']=$v['operater_price'];
            //$cash['operater_before_balance']=$v['operater_before_balance'];
            //$cash['operater_after_balance']=$v['operater_after_balance'];
            $cash['operation_date']=$v['operation_date'];
            array_push($data,$cash);
        }
        if($user_type==3){
            $headArr=array("操作者编号","操作者名称","用途","操作金额(元)",//"操作前余额(元)","操作后余额(元)",
                "操作时间");
        }else{
            $headArr=array("操作者编号","操作者名称","操作者用户类型","用途","操作金额(元)",//"操作前余额(元)","操作后余额(元)",
                "操作时间");
        }
          $name="现金收入记录";
        ExportEexcel($name,$headArr,$data);
    }


    //现在进支出记录导出
    public function payout_excel(){

            $cash_type=trim(I("cash_type"));
            $user_type=D('SysUser')->self_user_type();
            $start_datetime=trim(I('start_datetime'));
            $end_datetime=trim(I('end_datetime')) ;
            $operate_type=trim(I('operate_type'));
//判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                $this->display('payout');
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }
        if($start_datetime || $end_datetime){
            if($start_datetime && $end_datetime){
                $where['ar.operation_date'] = array('between',array($start_datetime,$end_datetime));
            }
            if($start_datetime ==""){
                $start_datetime = start_time(msubstr($end_datetime,0,10,"utf-8",false));
                $where['ar.operation_date'] = array('between',array($start_datetime,$end_datetime));
            }
            if($end_datetime ==""){
                $end_datetime = end_time(msubstr($start_datetime,0,10,"utf-8",false));
                $where['ar.operation_date'] = array('between',array($start_datetime,$end_datetime));
            }
        }else {
            $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            //$end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $end_datetime= strtotime($start_datetime)-2592000;
            $e_time=start_time(date('Y-m-d',$end_datetime));
            $where['ar.operation_date']= array('between',array($e_time,$start_datetime));
        }
                if($user_type==2){
                 /*   $obj_enterprise_id=trim(I('obj_enterprise_id'));
                    $obj_proxy_id=trim(I('obj_proxy_id'));
                    if($obj_enterprise_id){
                        $where['ar.obj_enterprise_id']=$obj_enterprise_id;
                    }
                    if($obj_proxy_id){
                        $where['ar.obj_proxy_id']=$obj_proxy_id;
                    }*/

                    $obj_enterprise_name=trim(I('obj_enterprise_name'));
                    $obj_proxy_name=trim(I('obj_proxy_name'));

                    if($obj_enterprise_name){
                        $where['oe.enterprise_name']=array("like","%".$obj_enterprise_name."%");
                    }
                    if($obj_proxy_name){
                        $where['op.proxy_name']=array("like","%".$obj_proxy_name."%");
                    }
                    
                    $where['ar.user_type']=1; //用户类型为企业
                    $where['ar.balance_type']=1; //账户收入*/
                    $where['ar.proxy_id']=D('SysUser')->self_proxy_id().','.D('Proxy')->proxy_child_ids(); //代理商为正常
                }
                if($user_type==3){
                    $where['ar.user_type']=2; //用户类型为企业
                    $where['e.status']=1; //企业状态为正常
                    //$where['op.status']=1; //代理商为正常
                    $where['e.approve_status']=1;
                    $where['e.enterprise_id']=D('SysUser')->self_enterprise_id();
                }
                $where['ar.balance_type']=2;//收支类型：支出

        if($operate_type!=''){
            $where['ar.operate_type'] =$operate_type;
        }/*else{
            $where['ar.operate_type']=array('neq',6);//不显示分红
        }*/

         //获取用户的数据权限
       /* if($user_type==2){
            $map['status'] = array('neq',2);
            $map['top_proxy_id'] = D('SysUser')->self_proxy_id();
            $map['approve_status'] =1;
            $range_list = M('Proxy')->field('proxy_id')->where($map)->select();
            $ids="";
            if($range_list){
                foreach($range_list as $v){
                    $ids.= ','.$v['proxy_id']; 
                }
                $ids = substr($ids,1,strlen($ids)-1);
            }
            if($ids!=""){
                $ids=$ids.","."0";
                $pids="";
                $p_list=D("Proxy")->proxy_child();
                if($p_list){
                foreach($p_list as $v){
                    $pids.= ','.$v; 
                }
                $pids = substr($pids,1,strlen($pids)-1);
                }
                if($pids!=""){
                    $map1["ar.obj_proxy_id"]=array(array('in',$pids),array('not in',$ids),"or");
                }else{
                    $map1['ar.obj_proxy_id']=array('not in',$ids);
                }
            }else{
                 $map1['ar.obj_proxy_id']=array('neq',"0");
            }
            $map1['_logic']="or";
            $eids=D("Enterprise")->enterprise_ids();
            if($eids){
                $map1['ar.obj_enterprise_id']=array('in',$eids);
            }else{
                $map1['ar.obj_enterprise_id']=array('eq',"-1");   
            }
            $where[]=$map1;
        }*/
        $list=D('AccountRecord')->cash_excel($where);
        $data=array();
        foreach ($list as $v) {
            $cash=array();
            if($v['operate_type']==9||$v['operate_type']==10){
                $cash['proxy_code']='--';
                $cash['proxy_name']='--';
                if($user_type!=3){
                    $cash['type']="--";
                }
            }else{
                if($v['obj_user_type']==1){
                    $cash['proxy_code']=obj_data($v['obj_proxy_id'],1,'code');
                    $cash['proxy_name']=obj_data($v['obj_proxy_id'],1,'name');
                    if($user_type!=3){
                            $cash['type']="代理商";
                    }
                }else{
                    $cash['enterprise_code']=obj_data($v['obj_enterprise_id'],2,'code');
                    $cash['enterprise_name']=obj_data($v['obj_enterprise_id'],2,'name');
                    if($user_type!=3){
                            $cash['type']="企业";
                    }
                }
            }
            $cash['operate_type'] =get_operate_type($v['operate_type']);
            $cash['operater_price']=$v['operater_price'];
            //$cash['operater_before_balance']=$v['operater_before_balance'];
            //$cash['operater_after_balance']=$v['operater_after_balance'];
            $cash['operation_date']=$v['operation_date'];
            array_push($data,$cash);
        }
        if($user_type==3){
          $headArr=array("接收者编号","接收者名称","用途","操作金额(元)",//"操作前余额(元)","操作后余额(元)",
              "操作时间");
        }else{
            $headArr=array("接收者编号","接收者名称","接收者用户类型","用途","操作金额(元)",//"操作前余额(元)","操作后余额(元)",
                "操作时间");
        }
       $name="现金支出记录";
        ExportEexcel($name,$headArr,$data);
    }
}
?>