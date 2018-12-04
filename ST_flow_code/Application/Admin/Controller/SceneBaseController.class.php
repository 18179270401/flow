<?php
/**
 * 流量场景 基础设置控制器
 */
namespace Admin\Controller;
use Think\Controller;
//use \Think\Page;

class SceneBaseController extends CommonController {
    /**
     * 流量场景 基础设置
     */
    public function index() {
        $self_user_type = D('SysUser')->self_user_type(); //1 2 3
        $user_type = $self_user_type - 1; //1：代理商、2：企业
        $self_proxy_id = D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        if($user_type==1){
            $user_id=$self_proxy_id;
        }else{
            $user_id=$self_enterprise_id;
        }
        $type = I('get.type');

        if($type == "operation") {
            $self_user_id = D('SysUser')->self_id();
            //保存场景基本信息
            $post = I("post.");
            $status = "error";
            $msg = "微信设置保存失败！";

            $info_id            = $post['info_id'];

            if (!empty($_FILES)) {
                //write_debug_log(array(__METHOD__.'：'.__LINE__, 111));
                $fileinfo = $this->scene_base_upload(C('ENTERPRISE_SCENE_UPLOAD_DIR'));
                $error = $this->business_licence_upload_Error;
                if($error){
                    if($error['propagandat_img'] && $error['propagandat_img'] != '没有文件被上传！'){
                        $msg = '宣传图'.$error['propagandat_img'];
                        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                    }
                    if($error['logo_img'] && $error['logo_img'] != '没有文件被上传！'){
                        $msg = 'LOGO图片'.$error['logo_img'];
                        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                    }
                    if($error['background_img'] && $error['background_img'] != '没有文件被上传！'){
                        $msg = '背景图'.$error['background_img'];
                        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                    }
                    if($error['share_img'] && $error['share_img'] != '没有文件被上传！'){
                        $msg = '分享图'.$error['share_img'];
                        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                    }
                }

                if($fileinfo['propagandat_img']){
                    $propagandat_img = substr(C('UPLOAD_DIR').$fileinfo['propagandat_img']['savepath'].$fileinfo['propagandat_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['propagandat_img']['savepath'].$fileinfo['propagandat_img']['savename'])-1);
                }else{
                    $propagandat_img = '';
                }
                if($fileinfo['logo_img']){
                    $logo_img = substr(C('UPLOAD_DIR').$fileinfo['logo_img']['savepath'].$fileinfo['logo_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['logo_img']['savepath'].$fileinfo['logo_img']['savename'])-1);
                }else{
                    $logo_img = '';
                }
                if($fileinfo['background_img']){
                    $background_img = substr(C('UPLOAD_DIR').$fileinfo['background_img']['savepath'].$fileinfo['background_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['background_img']['savepath'].$fileinfo['background_img']['savename'])-1);
                }else{
                    $background_img = '';
                }
                if($fileinfo['share_img']){
                    $share_img = substr(C('UPLOAD_DIR').$fileinfo['share_img']['savepath'].$fileinfo['share_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['share_img']['savepath'].$fileinfo['share_img']['savename'])-1);
                }else{
                    $share_img = '';
                }
            } else {
                //write_debug_log(array(__METHOD__.'：'.__LINE__, 222));
            }

            //$user_id = (1 == $user_type) ? $self_proxy_id : $self_enterprise_id;

            $upd = array(
                /*'propagandat_img'   => $propagandat_img,
                'logo_img'          => $logo_img,
                'background_img'    => $background_img,*/
               'redpack_address'   => gethostwithhttp()."/index.php/Sdk/FlowRed/index/user_type/{$user_type}/user_id/{$user_id}", //流量红包
                'recharge_address'  => gethostwithhttp()."/index.php/Sdk/WxFlowPayment/index/user_type/{$user_type}/user_id/{$user_id}", //流量充值
                'modify_user_id'    => $self_user_id,
                'modify_date'       => date('Y-m-d H:i:s'),
            );
            !empty($propagandat_img) && $upd['propagandat_img'] = $propagandat_img;
            !empty($logo_img) && $upd['logo_img'] = $logo_img;
            !empty($background_img) && $upd['background_img'] = $background_img;
            !empty($share_img) && $upd['share_img'] = $share_img;
            M("")->startTrans();
            $upd['share_title']=$post['share_title'];
            $upd['share_content']=$post['share_content'];
            $upd['share_url'] = $post['share_url'];
            $upd['active_appid']=$post['active_appid'];
            $upd['active_appsecret']=$post['active_appsecret'];
            $upd['active_wx_type']=$post['wx_type'];
            $upd['active_wx_name']=$post['active_wx_name'];
            $rt = M('scene_info')->where("info_id={$info_id}")->save($upd);
            if(false !== $rt) {
                $status = "success";
                M("")->commit();
                $msg = "微信设置保存成功";
            } else {
                M("")->rollback();
                write_error_log(array(__METHOD__.':'.__LINE__,'sql== '.M()->getLastSql()));
            }
            IS_AJAX && $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
        } else {
            //读取场景基本信息
            if($user_type==1){
                $user_id=$self_proxy_id;
                $us['proxy_id']=$self_proxy_id;
            }else{
                $user_id=$self_enterprise_id;
                $us['enterprise_id']=$self_enterprise_id;
            }
            $list = D('SceneInfo')->get_scene_info($user_type, $self_proxy_id, $self_enterprise_id);
            if(empty($list['active_wx_type'])){
                $list['active_wx_type']=1;
            }
            $data=$this->localencode($user_type.",".$user_id);
            $list['redpack_address']=gethostwithhttp()."/index.php/Sdk/FlowRed/aindex?".$data;
            $list['recharge_address']=gethostwithhttp()."/index.php/Sdk/WxFlowPayment/aindex?".$data;
            $this->assign("list",$list);
            $this->display();
        }
    }
    public function localencode($data) {
    $string = "";
    for($i=0;$i<strlen($data);$i++){
        $ord = ord($data[$i]);
        $ord += 20;
        $string = $string.chr($ord);
    }
    $data = base64_encode($string);
    return $data;
}

    public function img_download() {

        $msg = '系统错误！';
        $status = 'error';

        $self_user_type = D('SysUser')->self_user_type(); //1 2 3
        $user_type = $self_user_type - 1; //1：代理商、2：企业
        $self_proxy_id = D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();

        $list = D('SceneInfo')->get_scene_info($user_type, $self_proxy_id, $self_enterprise_id);

        $type = trim(I('get.download'));
        if(in_array($type,array('propagandat_img','logo_img','background_img','share_img'))) {
            parent::download('.'.$list[$type]);
        }

    }



}