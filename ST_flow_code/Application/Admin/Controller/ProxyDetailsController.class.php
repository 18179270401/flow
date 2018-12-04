<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class ProxyDetailsController extends CommonController{
	/*
	 *账户充值记录
	 */
	public function recharge_record(){
        set_time_limit(0);
        D("SysUser")->sessionwriteclose();
            $proxy_code=trim(I('proxy_code'));
            $proxy_name=trim(I('proxy_name'));
            $start_datetime = trim(I('get.start_datetime'));   //开始时间
            $end_datetime = trim(I('get.end_datetime'));   //结束时间
            //判断时间是否在一个月内
            if($start_datetime!="" && $end_datetime!=""){
                if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                    $this->display('recharge_record');
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

            if($proxy_code){
                    $where['p.proxy_code'] =$proxy_code;
            }
            if($proxy_name){
                    $where['p.proxy_name'] =array('like','%'.$proxy_name.'%');
            }

       $where['ar.operate_type']=array('in',array(2,8));//充值
       $where['ar.user_type']=1;
       $where['ar.obj_user_type']=1;

       //数据权限
       $where['p.proxy_id'] = array('in',D('Proxy')->proxy_child_ids());
       $where['p.top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());


       $list=D('AccountRecord')->account_record($where);
       $this->assign('list',$list['list']);
       $this->assign('page',$list['page']);
        $this->assign('all_income',$this->all_income($where));
        $this->assign('d_sdata',date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))));  //默认开始时间
        $this->assign('d_edata',date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))));  //默认结束时间
       $this->display();        //模板*/
    }
    /*账户提现记录*/
    public function withdraw_record(){
        set_time_limit(0);
        D("SysUser")->sessionwriteclose();
            $proxy_code=trim(I('proxy_code'));
            $proxy_name=trim(I('proxy_name'));
            $obj_proxy_code=trim(I('obj_proxy_code'));
            $obj_proxy_name=trim(I('obj_proxy_name'));
            $start_datetime = trim(I('get.start_datetime'));   //开始时间
            $end_datetime = trim(I('get.end_datetime'));   //结束时间
            //判断时间是否在一个月内
            if($start_datetime!="" && $end_datetime!=""){
                if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                    $this->display('withdraw_record');
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
            if($proxy_code){
                    $where['p.proxy_code'] =$proxy_code;
            }
            if($proxy_name){
                    $where['p.proxy_name'] =array('like','%'.$proxy_name.'%');
            }
            if($obj_proxy_code){
                    $where['op.proxy_code'] =$obj_proxy_code;
            }
            if($obj_proxy_name){
                    $where['op.proxy_name'] =array('like','%'.$obj_proxy_name.'%');
            }
            $where['ar.operate_type']=3;//提现
            $where['ar.user_type']=1;
            $where['ar.obj_user_type']=1;

            //数据权限
            $where['p.proxy_id'] = array('in',D('Proxy')->proxy_child_ids());
            $where['p.top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());


            $list=D('AccountRecord')->account_record($where);
            $this->assign('all_out',$this->all_out($where));
            $this->assign('d_sdata', date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))));  //默认开始时间
            $this->assign('d_edata',date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))));  //默认结束时间
            $this->assign('list',$list['list']);
            $this->assign('page',$list['page']);
            $this->display();        //模板*/
    }

   public function show(){
           $where['ar.record_id']=trim(I('record_id'));//充值
           $list=D('AccountRecord')->detailed($where);
           $this->assign($list);
           $this->display('detailed');
   }

    public function all_record(){
        set_time_limit(0);
        D("SysUser")->sessionwriteclose();
        $where=array();
        $proxy_code=trim(I('proxy_code'));
        $proxy_name=trim(I('proxy_name'));
        $operate_type=trim(I('operate_type'));
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                $this->display('all_record');
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }
        if($proxy_code||$proxy_name){
            if($proxy_code){
                $where['p.proxy_code'] =$proxy_code;
            }
            if($proxy_name){
                $where['p.proxy_name'] =array('like',$proxy_name.'%');
            }
            if($operate_type!=''){
                $where['ar.operate_type'] =$operate_type;
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

            //数据权限
            $where['ar.proxy_id'] = array('in',D('SysUser')->self_proxy_id().','.D('Proxy')->proxy_child_ids());
            $list=D('AccountRecord')->allRecordList($where);
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
        $sum_results = M('account_record as ar')
            ->join('left join t_flow_proxy as p on  p.proxy_id=ar.proxy_id')
            ->join('left join  t_flow_proxy as op on ar.obj_proxy_id =op.proxy_id')
            ->where($where)
            ->field('sum(ar.operater_price) as sum_apply_money')
            ->find();
        return $sum_results['sum_apply_money'];
    }

    public function  all_out($where){
        $where['ar.balance_type']=2;
        $sum_results = M('account_record as ar')
            ->join('left join t_flow_proxy as p on  p.proxy_id=ar.proxy_id')
            ->join('left join  t_flow_proxy as op on ar.obj_proxy_id =op.proxy_id')
            ->where($where)
            ->field('sum(ar.operater_price) as sum_apply_money')
            ->find();
        return $sum_results['sum_apply_money'];
    }




    public function all_record_excel(){
        $where=array();
        $proxy_code=trim(I('proxy_code'));
        $proxy_name=trim(I('proxy_name'));
        $operate_type=trim(I('operate_type'));
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                $this->display('all_record');
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }
        if($proxy_code||$proxy_name){
            if($proxy_code){
                $where['p.proxy_code'] =$proxy_code;
            }
            if($proxy_name){
                $where['p.proxy_name'] =array('like','%'.$proxy_name.'%');
            }
            if($operate_type!=''){
                $where['ar.operate_type'] =$operate_type;
            }
            $where['p.status'] =array('neq',2);
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

        //数据权限
        $where['p.proxy_id'] = array('in',D('SysUser')->self_proxy_id().','.D('Proxy')->proxy_child_ids());
        $title='代理商收支明细表';
        $headArr=array("代理商编号","代理商名称","接收者编号","接收者名称","接收用户类型","用途","操作金额(元)",//"操作前余额(元)","操作后余额(元)",
            "操作时间");

        if($proxy_code || $proxy_name){
            $dataList=D('AccountRecord')->all_record_excel($where);
            foreach($dataList as $k=>$v){
                if($v['operate_type']==6){
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
                    $list[$k]['proxy_code'] =$v['proxy_code'];
                    $list[$k]['proxy_name'] =$v['proxy_name'];
                    if($v['operate_type']==9||$v['operate_type']==10){
                        $list[$k]['obj_user_type']='--';
                    }else{
                        $list[$k]['obj_user_type']=$v['user_type']==1?"代理商":'企业';
                    }
                }else{
                    $list[$k]['proxy_code'] =$v['proxy_code'];
                    $list[$k]['proxy_name'] =$v['proxy_name'];
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

                }
                if($v['operate_type']==9||$v['operate_type']==10){
                    $list[$k]['obj_user_type']='--';
                }else{
                    $list[$k]['obj_user_type']=$v['obj_user_type']==1?"代理商":'企业';
                }
                $list[$k]['operate_type'] =get_operate_type($v['operate_type']);
                if( $v['balance_type']==2){
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

    public function recharge_excel(){
        $proxy_code=trim(I('proxy_code'));
        $proxy_name=trim(I('proxy_name'));
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        $where=array();
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                $this->display('recharge_record');
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
            if($proxy_code){
                $where['p.proxy_code'] =$proxy_code;
            }
            if($proxy_name){
                $where['p.proxy_name'] =array('like','%'.$proxy_name.'%');
            }

            $where['ar.operate_type']=array('in',array(2,8));//充值
            $where['ar.user_type']=1;
            $where['ar.obj_user_type']=1;
             //数据权限
            $where['p.proxy_id'] = array('in',D('Proxy')->proxy_child_ids());
            $where['p.top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());

            $list=D('AccountRecord')->record_excel($where);
            $title='代理商充值明细表';
            $headArr=array("代理商编号","代理商名称","操作金额(元)"//,"操作前余额(元)","操作后余额(元)"
            ,"操作时间");
        ExportEexcel($title,$headArr,$list);
    }

    public function withdraw_excel(){
        $proxy_code=trim(I('proxy_code'));
        $proxy_name=trim(I('proxy_name'));
        $obj_proxy_code=trim(I('obj_proxy_code'));
        $obj_proxy_name=trim(I('obj_proxy_name'));
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        $where=array();
        //判断时间是否在一个月内
        if($start_datetime!="" && $end_datetime!=""){
            if(strtotime($end_datetime) - strtotime($start_datetime) > 2678400*3){
                $this->display('withdraw_record');
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

            if($proxy_code){
                $where['p.proxy_code'] =$proxy_code;
            }
            if($proxy_name){
                $where['p.proxy_name'] =array('like','%'.$proxy_name.'%');
            }
            if($obj_proxy_code){
                $where['op.proxy_code'] =$obj_proxy_code;
            }
            if($obj_proxy_name){
                $where['op.proxy_name'] =array('like','%'.$obj_proxy_name.'%');
            }

            $where['ar.operate_type']=3;//充值
            $where['ar.user_type']=1;
            $where['ar.obj_user_type']=1;

            //数据权限
            $where['p.proxy_id'] = array('in',D('Proxy')->proxy_child_ids());
            $where['p.top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());


            $list=D('AccountRecord')->record_excel($where);
            $title='代理商提现明细表';
            $headArr=array("代理商编号","代理商名称","上级代理编号","上级代理名称","操作金额(元)",//"操作前余额(元)","操作后余额(元)",
                "操作时间");

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
                $this->display('all_record');
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }

        $where['p.status'] =array('neq',2);
        if($operate_type!=''){
            $where['ar.operate_type'] =$operate_type;
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

        $enterprise_name = trim(I('enterprise_name'));
        $proxy_name = trim(I('proxy_name'));

        if($enterprise_name){
            $where['e.enterprise_name'] = array('like',"%$enterprise_name%");
        }

        if($proxy_name){
            $where['pr.proxy_name'] = array('like',"%$proxy_name%");
        }

        //数据权限
        $where['p.proxy_id'] = D('SysUser')->self_proxy_id();
        
        $list=D('AccountRecord')->allRecordList($where);
        $this->assign('list',$list['list']);
        $this->assign('page',$list['page']);
        /*$this->assign('all_income',$this->all_income($where));
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
                $this->display('all_record');
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }

        $where['p.status'] =array('neq',2);
        if($operate_type!=''){
            $where['ar.operate_type'] =$operate_type;
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

        //数据权限
        $where['p.proxy_id'] = D('SysUser')->self_proxy_id();
        $title='代理商收支明细表';
        $headArr=array("接收者编号","接收者名称","接收用户类型","用途","操作人","操作金额(元)",//"操作前余额(元)","操作后余额(元)",
            "操作时间");

        $dataList=D('AccountRecord')->all_record_excel($where);
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
            if($v['operate_type']==9||$v['operate_type']==10){
                $list[$k]['obj_user_type']='--';
            }else{
                $list[$k]['obj_user_type']=$v['obj_user_type']==1?"代理商":'企业';
            }
            $list[$k]['operate_type'] =get_operate_type($v['operate_type']);
            $list[$k]['user_id'] =get_user_name($v['user_id']);
            if( $v['balance_type']==2){
                $list[$k]['operater_price'] ='-'.$v['operater_price'];
            }else{
                $list[$k]['operater_price'] =$v['operater_price'];
            }
            //$list[$k]['operater_before_balance'] =$v['operater_before_balance'];
            //$list[$k]['operater_after_balance'] =$v['operater_after_balance'];
            $list[$k]['record_date'] =$v['record_date'];
        }
        ExportEexcel($title,$headArr,$list);
    }

}
?>