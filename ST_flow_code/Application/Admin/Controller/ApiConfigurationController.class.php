<?php

/*
 * UserController.class.php
 * 用户操作控制器
 *
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class ApiConfigurationController extends CommonController {

    /*
     *开发者中心
     */

    public function index(){

        $user_type=D('SysUser')->self_user_type();
        $self_proxy_id =  D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        if($user_type==1){
       //     $map['od.user_type'] = array('in','1,2,3');
        }
        elseif($user_type==2&&!empty($self_proxy_id)){
            $map['proxy_id'] = array('eq',$self_proxy_id);
            $map['user_type'] = 1;
        }elseif($user_type==3&&!empty($self_enterprise_id)){
            $map['enterprise_id'] = array('eq',$self_enterprise_id);
            $map['user_type'] =2;
        }

        //获取api信息
        $api_list = M('sys_api')->field('api_id,api_account,api_key,api_callback_address,api_callback_ip')->where($map)->select();
        //加载模板

        $this->assign('api_id', $api_list[0]['api_id']);
        $this->assign('api_account', $api_list[0]['api_account']);
        $this->assign('api_key', $api_list[0]['api_key']);
        $this->assign('api_callback_address',  $api_list[0]['api_callback_address']);
        $this->assign('api_callback_ip',$api_list[0]['api_callback_ip']);

        $this->display('index');

    }

    public function respcode(){
		$this->display('respcode');
	}

    public function document(){
    	
    	$this->assign('apiword', gethostwithhttp().'/Public/Uploads/Document/GDSTAPI.docx');
    	$this->assign('phpdemo', gethostwithhttp().'/Public/Uploads/Document/phpdemo.zip');
    	
        $this->display('document');
    }


    public function faq(){
        $this->display('faq');
    }



    /**
     *回调 ip鉴权保存
     */
    public function update(){
        $msg = '系统错误！';
        $status = 'error';

          $user_type=D('SysUser')->self_user_type();
         $self_proxy_id =  D('SysUser')->self_proxy_id();
         $self_enterprise_id = D('SysUser')->self_enterprise_id();
          if($user_type==1){

          }
          elseif($user_type==2&&!empty($self_proxy_id)){

              $map['proxy_id'] = array('in',$self_proxy_id);
              $map['user_type'] = 1;
          }elseif($user_type==3&&!empty($self_enterprise_id)){
              $map['enterprise_id'] = array('in',$self_enterprise_id);
              $map['user_type'] =2;
          }


        if(IS_POST){
            $api_callback_address = I('post.api_callback_address');       //回调地址
            $api_callback_ip = I('post.api_callback_ip');//鉴权ip
            $api_id = I('post.api_id'); //api id

                    $map['api_id'] = array('eq',$api_id);

                        $edit = array(
                            'api_callback_address'  => $api_callback_address,
                            'api_callback_ip'  => $api_callback_ip,
                        );

                        if(M('sys_api')->where($map)->save($edit)){
                            $msg = '添加成功！';
                            $status = 'success';
                            $n_msg='成功';
                        }else{
                            $msg = '您没有更新，添加失败!';
                            $n_msg='失败';
                        }
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，添加接口信息'.$n_msg;
            $this->sys_log('添加接口信息',$note);

        }
        if(IS_AJAX) $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }



}