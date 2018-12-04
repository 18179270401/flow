<?php

/*
 * UserController.class.php
 * 用户操作控制器
 *
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class LoginlogController extends Controller {

    /*
     * 登入日志
     */
    public function __construct() {
        parent::__construct();
    }
    public function index(){
        //调用分页类
        D("SysUser")->sessionwriteclose();
        $model=M('sys_login_log as l');
        $login_user_name = trim(I('login_user_name'));
        $login_name_full = trim(I('login_name_full'));
        $user_type = trim(I('user_type'));
        if($login_user_name){
            $map['l.login_user_name'] = array('like','%'.$login_user_name.'%');
        }
        if($login_name_full){
            $map['l.login_name_full'] = array('like','%'.$login_name_full.'%');
        }
        if($user_type!=9 && $user_type!=''){
            $map['u.user_type'] = $user_type;
        }
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $map['l.login_date']=array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }else if($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $map['l.login_date']= array('between',array(start_time($start_datetime),$end_datetime));
            }else if($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $map['l.login_date']=array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $map['l.login_date'] = array('between',array($start_datetime,$end_datetime));
        }

        $join='t_flow_sys_user as u on u.user_id=l.login_user_id ';

        $count      =$model->join($join)->where($map)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();

        //获取所有日志列表
        $loginlog_list =$model
            ->join($join)
            ->field('l.*,u.user_type')
            ->where($map)
            ->order('l.login_date desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        //加载模板
        $this->assign('loginlog_list',get_sort_no($loginlog_list,$Page->firstRow));  //数据列表
        $this->assign('page',$show);            //分页
        $this->assign('start_datetime',$start_datetime);
        $this->assign('end_datetime',$end_datetime);
        $this->display('index');

    }


    /**
     * 导出excel
     */
    public function export_excel() {
        $map=array();
        $model=M('sys_login_log as l');
        $login_user_name = trim(I('login_user_name'));
        $login_name_full = trim(I('login_name_full'));
        $user_type = trim(I('user_type'));
        if($login_user_name){
            $map['l.login_user_name'] = array('like','%'.$login_user_name.'%');
        }
        if($login_name_full){
            $map['l.login_name_full'] = array('like','%'.$login_name_full.'%');
        }
        if($user_type!=9 && $user_type!=''){
            $map['u.user_type'] = $user_type;
        }
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $map['l.login_date']=array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }else if($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $map['l.login_date']= array('between',array(start_time($start_datetime),$end_datetime));
            }else if($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $map['l.login_date']=array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $map['l.login_date'] = array('between',array($start_datetime,$end_datetime));
        }
        $join='t_flow_sys_user as u on u.user_id=l.login_user_id ';
        //获取所有日志列表
        $arr_record =$model
            ->join($join)
            ->field('l.*,u.user_type')
            ->where($map)
            ->order('l.login_date desc')
            ->limit(3000)
            ->select();
        $title='登录日志';
        $list=array();
        $headArr=array("登录用户名","登录全称","用户类型","IP地址","登录时间");
        foreach($arr_record as $k=>$v){
            $list[$k]['login_user_name'] =$v['login_user_name'];
            $list[$k]['login_name_full'] =$v['login_name_full'];
            if($v['user_type']==1){
                $list[$k]['user_type']='尚通端';
            }else if($v['user_type']==2){
                $list[$k]['user_type']='代理商端';
            }else{
                $list[$k]['user_type']='企业端';
            }
            if(strstr($v['ip_addr'],'.')){
                $list[$k]['ip_addr'] =$v['ip_addr'];
            }else{
                $list[$k]['ip_addr'] =long2ip($v['ip_addr']);
            }
            $list[$k]['login_date'] =$v['login_date'];
        }
        ExportEexcel($title,$headArr,$list);
    }


}