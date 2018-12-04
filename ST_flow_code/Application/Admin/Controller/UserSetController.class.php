<?php

/*
 * 系统设置控制器
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class UserSetController extends CommonController {
    /*
     * 系统设置
     */
    public function index(){
        $user_type=D('SysUser')->self_user_type();
        $self_proxy_id =  D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        $user_id = D('SysUser')->self_id();
        $this->assign('user_type',$user_type);

        $type = I('get.type');
        $map = array();
        if($user_type == 2 && !empty($self_proxy_id)){
            $map['proxy_id'] = array('eq',$self_proxy_id);
            $user_type = 1;
            $map['user_type'] = 1;
        }elseif($user_type==3 && !empty($self_enterprise_id)){
            $map['enterprise_id'] = array('eq',$self_enterprise_id);
            $user_type = 2;
            
        }
        $map['user_type'] =$user_type;
        $Model = M("Sys_user_set");
        $info = $Model->where($map)->find();
        if($type == "operation"){
            //保存系统设置
            $post = I("post.");
            $status = "error";
            $msg = "系统设置保存失败!";

            $logo_img = '';
            //图片的上传
            if (!empty($_FILES)) {
                //write_debug_log(array(__METHOD__.'：'.__LINE__, 111));
                $fileinfo = $this->scene_base_upload(C('USER_LOGO_UPLOAD_DIR'));
                $error = $this->business_licence_upload_Error;
                if($error){
                    if($error['logo_img'] && $error['logo_img'] != '没有文件被上传！'){
                        $msg = 'LOGO图片'.$error['logo_img'];
                        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                    }
                }
                if($fileinfo['logo_img']){
                    $logo_img = substr(C('UPLOAD_DIR').$fileinfo['logo_img']['savepath'].$fileinfo['logo_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['logo_img']['savepath'].$fileinfo['logo_img']['savename'])-1);
                }

            } else {
                //write_debug_log(array(__METHOD__.'：'.__LINE__, 222));
            }

            $post['logo_img'] = $logo_img;
            if($info){
                $post['modify_user_id'] = $user_id;
                $post['modify_date'] = date('Y-m-d H:i:s');
                if(empty($logo_img)){
                    unset($post['logo_img']);
                }
                $result = $Model->where($map)->save($post);
                if($result){
                    $status = "success";
                    $msg = "系统设置保存成功!";
                }else{
                    $msg = "系统设置保存失败!";
                }
            }else{
                $post['user_type'] = $user_type;
                $post['proxy_id'] = $self_proxy_id;
                $post['enterprise_id'] = $self_enterprise_id;
                $post['create_user_id'] = $user_id;
                $post['create_date'] = date('Y-m-d H:i:s');
                $result = $Model->add($post);
                if($result){
                    $status = "success";
                    $msg = "系统设置保存成功!";
                }else{
                    $msg = "系统设置保存失败!";
                }
            }
            if(IS_AJAX) $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }else{
            //读取系统设置
            $this->assign("info",$info);
            $this->display();
        }
    }

    public function img_download() {

        $msg = '系统错误！';
        $status = 'error';

        $self_user_type = D('SysUser')->self_user_type(); //1 2 3
        $user_type = $self_user_type - 1; //1：代理商、2：企业
        $self_proxy_id = D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();

        $map = array();
        $map['user_type'] = $user_type;
        if($user_type == 1 && !empty($self_proxy_id)){
            $map['proxy_id'] = array('eq',$self_proxy_id);
            
        }else{
            $map['enterprise_id'] = array('eq',$self_enterprise_id);
        }
        $Model = M("Sys_user_set");
        $info = $Model->where($map)->find();

        $type = trim(I('get.download'));
        if($type == 'logo_img') {
            $down_src = "/Public/Uploads/User_logo/default.png";
            if(!empty($info) && !empty($info[$type])){
                $down_src = $info[$type];
            }
            parent::download('.' . $down_src);
        }

    }

}