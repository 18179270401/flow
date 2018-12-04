<?php
/**
 * 流量场景 基础设置控制器
 */
namespace Admin\Controller;
use Think\Controller;
//use \Think\Page;

class FlowcodeSetController extends CommonController {
    /**
     * 流量码 基础设置
     */
    public function index() {
        $self_user_type = D('SysUser')->self_user_type(); //1 2 3
        $user_type = $self_user_type - 1; //1：代理商、2：企业
        $self_proxy_id = D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        $type = I('get.type');

        if($type == "operation") {
            $self_user_id = D('SysUser')->self_id();
            //保存场景基本信息
            $post = I("post.");
            $status = "error";
            $msg = "流量码活动设置保存失败！";

            $set_id          = $post['set_id'];

            if (!empty($_FILES)) {
                //write_debug_log(array(__METHOD__.'：'.__LINE__, 111));
                $fileinfo = $this->scene_base_upload(C('ENTERPRISE_SCENE_UPLOAD_DIR'));
                $error = $this->business_licence_upload_Error;
                if($error){
                    if($error['logo_img'] && $error['logo_img'] != '没有文件被上传！'){
                        $msg = '企业LOGO'.$error['logo_img'];
                        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                    }
                    if($error['background_img'] && $error['background_img'] != '没有文件被上传！'){
                        $msg = '企业宣传图'.$error['logo_img'];
                        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                    }
                }
                if($fileinfo['logo_img']){
                    $logo_img = substr(C('UPLOAD_DIR').$fileinfo['logo_img']['savepath'].$fileinfo['logo_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['logo_img']['savepath'].$fileinfo['logo_img']['savename'])-1);
                    /* $imgsize=getimagesize(dirname(__PUBLIC__).$flowscore_basic_logo);
                     $weight=$imgsize["0"];////获取图片的宽
                     $height=$imgsize["1"];///获取图片的高
                    if($weight!=255 || $height!=255){
                         $this->ajaxReturn(array("msg"=>"图片上传有误，请上传255*255的图片","status"=>"error"));
                     }*/
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
            }
            $upd = array(
                'modify_user_id'    => $self_user_id,
                'modify_date'       => date('Y-m-d H:i:s'),
            );
            !empty($logo_img) && $upd['logo_img'] = $logo_img;
            !empty($background_img) && $upd['background_img'] = $background_img;
            !empty($share_img) && $upd['share_img'] = $share_img;
            M("")->startTrans();
            if(empty($post['start_time']) || empty($post['end_time'])){
                $msg="请输入活动的开始时间和结束时间！";
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
            }
            if($post['start_time']!="" && $post['end_time']!="" && $post['start_time']>$post['end_time']){
                $msg="活动的开始时间不能大于结束时间！";
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
            }
            $upd['start_time']=$post['start_time']?$post['start_time']:null;
            $upd['end_time']=$post['end_time']?$post['end_time']:null;
            $upd['share_title']=$post['share_title'];
            $upd['share_content']=$post['share_content'];
            $upd['activity_rule']=$post['activity_rule'];
            $rt = M('flowcode_set')->where("set_id={$set_id}")->save($upd);
            if(false !== $rt) {
                $status = "success";
                M("")->commit();
                $msg = "流量码活动设置保存成功";
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
            $list = D('ExchangeRecord')->get_flowcode_base($user_type, $self_proxy_id, $self_enterprise_id);
            if(empty($list['set_id'])){
                $list['set_id']=1;
            }
            if(empty($list['url'])){
                $data=$this->localencode($user_type.",".$user_id);
                $list['url']=gethostwithhttp()."/index.php/PointValueManage/Api/index?".$data;
            }
            if($list['start_time']=="0000-00-00 00:00:00"){
                $list['start_time']=null;
            }
            if($list['end_time']=="0000-00-00 00:00:00"){
                $list['end_time']=null;
            }
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
        $where['user_type']=$user_type;
        $where['proxy_id']=$self_proxy_id;
        $where['enterprise_id']=$self_enterprise_id;
        $list = M("flowcode_set")->where($where)->find();
        $type = trim(I('get.download'));
        if(in_array($type,array('logo_img','background_img','share_img'))) {
            parent::download('.'.$list[$type]);
        }

    }



}