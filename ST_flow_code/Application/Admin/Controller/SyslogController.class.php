<?php

/*
 * UserController.class.php
 * 用户操作控制器
 *
 */
namespace Admin\Controller;
use Think\Controller;

class SyslogController extends CommonController {

    /*
     * 系统日志
     */

    public function __construct() {
        parent::__construct();
    }


    public function index(){
        //调用分页类
        $where=array();
        D("SysUser")->sessionwriteclose();
        $model=M('sys_log as sl');
        $join=array('t_flow_sys_user as u on  u.user_id=sl.create_user_id');
        $log_type = trim(I('log_type'));
        $user_name=trim(I('user_name'));
        $user_type=trim(I('user_type'));
        $note=trim(I('note'));
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        if($log_type){
            $where['sl.log_type'] = array('like','%'.$log_type.'%');
        }
        if($user_name){
            $where['u.user_name'] = array('like','%'.$user_name.'%');
        }
        if($note){
            $where['sl.note'] = array('like','%'.$note.'%');
        }
        if($user_type!=9 && $user_type!=''){
            $where['u.user_type'] = $user_type;
        }
        if(D('SysUser')->self_user_type()==2){
            $where['sl.user_type'] = 1;
            $where['sl.proxy_id'] = D('SysUser')->self_proxy_id();
        }
        if(D('SysUser')->self_user_type()==3){
            $where['sl.user_type'] = 2;
            $where['sl.enterprise_id'] = D('SysUser')->self_enterprise_id();
        }
        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['sl.create_date']=array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }else if($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['sl.create_date']= array('between',array(start_time($start_datetime),$end_datetime));
            }else if($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['sl.create_date']=array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $where['sl.create_date'] = array('between',array($start_datetime,$end_datetime));
        }
        $count      =$model->join($join)->where($where)->count();

        $Page       = new \Think\Page($count,20);
        $show       = $Page->show();

        //获取所有日志列表
        $list = $model
            ->join($join)
            ->where($where)
            ->order('sl.create_date desc')
            ->field("sl.*,u.user_name,u.user_type")
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();


        //加载模板
        $this->assign('list',get_sort_no($list,$Page->firstRow));  //数据列表
        $this->assign('page',$show);            //分页
        $this->assign('start_datetime',$start_datetime);
        $this->assign('self_user_type',D('SysUser')->self_user_type());
        $this->assign('end_datetime',$end_datetime);
        $this->display();
    }


    /**
     * 导出excel
     */
    public function export_excel() {

        $where=array();
        $model=M('sys_log as sl');
        $join=array('t_flow_sys_user as u on  u.user_id=sl.create_user_id');
        $log_type = trim(I('log_type'));
        $user_name=trim(I('user_name'));
        $user_type=trim(I('user_type'));
        $note=trim(I('note'));
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        if($log_type){
            $where['sl.log_type'] = array('like','%'.$log_type.'%');
        }
        if($user_name){
            $where['u.user_name'] = array('like','%'.$user_name.'%');
        }
        if($user_type!=9 && $user_type!=''){
            $where['u.user_type'] = $user_type;
        }
        if($note){
            $where['sl.note'] = array('like','%'.$note.'%');
        }
        if(D('SysUser')->self_user_type()==2){
            $where['sl.user_type'] = 1;
            $where['sl.proxy_id'] = D('SysUser')->self_proxy_id();
        }
        if(D('SysUser')->self_user_type()==3){
            $where['sl.user_type'] = 2;
            $where['sl.enterprise_id'] = D('SysUser')->self_enterprise_id();
        }
        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['sl.create_date']=array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }else if($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['sl.create_date']= array('between',array(start_time($start_datetime),$end_datetime));
            }else if($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['sl.create_date']=array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $where['sl.create_date'] = array('between',array($start_datetime,$end_datetime));
        }
        $self_user_type=D('SysUser')->self_user_type();
        $arr_record = $model
            ->join($join)
            ->where($where)
            ->order('sl.create_date desc')
            ->field("sl.*,u.user_name,u.user_type")
            ->limit(3000)
            ->select();
        $title='操作日志';
        $list=array();
        if($self_user_type==1){
            $headArr=array("用户名称","用户类型","日志类型","操作概述","操作地址","操作时间");
        }else{
            $headArr=array("用户名称","日志类型","操作概述","操作时间");
        }

        foreach($arr_record as $k=>$v){
            $list[$k]['user_name'] =$v['user_name'];
            if($self_user_type==1){
                $list[$k]['user_type']=get_sys_type($v['user_type']);
            }
            $list[$k]['log_type'] =$v['log_type'];
            $list[$k]['note'] =get_substr($v['note']);
            if($self_user_type==1){
            $list[$k]['method_url'] =$v['method_url'];
            }
            $list[$k]['create_date'] =$v['create_date'];
        }
        ExportEexcel($title,$headArr,$list);
    }


}