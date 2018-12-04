<?php
/**
 * 流量场景 基础设置控制器
 */
namespace Admin\Controller;
use Think\Controller;
//use \Think\Page;

class FlowscoreBaseController extends CommonController {
    /**
     * 签到积分 基础设置
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
            $msg = "签到设置保存失败！";

            $flowscore_basic_id           = $post['flowscore_basic_id'];

            if (!empty($_FILES)) {
                //write_debug_log(array(__METHOD__.'：'.__LINE__, 111));
                $fileinfo = $this->scene_base_upload(C('ENTERPRISE_SCENE_UPLOAD_DIR'));
                $error = $this->business_licence_upload_Error;
                if($error){
                    if($error['logo_img'] && $error['logo_img'] != '没有文件被上传！'){
                        $msg = 'LOGO图片'.$error['logo_img'];
                        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                    }
                }
                if($fileinfo['logo_img']){
                    $flowscore_basic_logo = substr(C('UPLOAD_DIR').$fileinfo['logo_img']['savepath'].$fileinfo['logo_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['logo_img']['savepath'].$fileinfo['logo_img']['savename'])-1);
                    $imgsize=getimagesize(dirname(__PUBLIC__).$flowscore_basic_logo);
                    $weight=$imgsize["0"];////获取图片的宽
                    $height=$imgsize["1"];///获取图片的高
                    if($weight!=255 || $height!=255){
                        $this->ajaxReturn(array("msg"=>"图片上传有误，请上传255*255的图片","status"=>"error"));
                    }
                }else{
                    $flowscore_basic_logo = '';
                }

            } else {
                //write_debug_log(array(__METHOD__.'：'.__LINE__, 222));
            }

            //$user_id = (1 == $user_type) ? $self_proxy_id : $self_enterprise_id;

            $upd = array(
                /*'propagandat_img'   => $propagandat_img,
                'logo_img'          => $logo_img,
                'background_img'    => $background_img,*/
                'modify_user_id'    => $self_user_id,
                'modify_date'       => date('Y-m-d H:i:s'),
            );
            !empty($flowscore_basic_logo) && $upd['flowscore_basic_logo'] = $flowscore_basic_logo;
            M("")->startTrans();
            if($post['flowscore_exchange_rate'] && !is_numeric($post['flowscore_exchange_rate'])){
                $msg="积分兑换比必须为整数！";
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
            }
            if($post['daily_score'] && !is_numeric($post['daily_score'])){
                $msg="每日签到积分必须为整数";
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
            }
            if(strtotime($post['start_time'])>strtotime($post['end_time'])){
                $msg="每天签到时间结束时间必须大于开始时间！";
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
            }
            if($post['start_date']!="" && $post['end_date']!="" && $post['start_date']>$post['end_date']){
                $msg="活动的开始时间不能大于结束时间！";
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
            }
            $upd['flowscore_exchange_rate']=$post['flowscore_exchange_rate'];
            $upd['daily_score']=$post['daily_score'];
            $upd['start_time']=$post['start_time'];
            $upd['end_time']=$post['end_time'];
            $upd['start_date']=$post['start_date'];
            $upd['end_date']=$post['end_date'];
            $rt = M('wx_enterprise')->where("flowscore_basic_id={$flowscore_basic_id}")->save($upd);
            if(false !== $rt) {
                $status = "success";
                M("")->commit();
                $msg = "签到设置保存成功";
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
            $list = D('ExchangeRecord')->get_flow_base($user_type, $self_proxy_id, $self_enterprise_id);
            if(empty($list['flowscore_basic_id'])){
                $list['flowscore_basic_id']=1;
            }
            if(empty($list['start_time'])){
                $list['start_time']=date("H:i:s",mktime(0,0,0));
                $list['end_time']=date("H:i:s",mktime(23,59,59));
            }
            if(!empty($list['start_date'])){
                $list['start_date']=date('Y-m-d',strtotime($list['start_date']));
            }
            if(!empty($list['end_date'])){
                $list['end_date']=date('Y-m-d',strtotime($list['end_date']));
            }
            $data=$this->localencode($user_type.",".$user_id);
            $list['flow_address']=gethostwithhttp()."/index.php/PointValueManage/Api/index?".$data;
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
        $list = M("wx_enterprise")->where($where)->find();
        $type = trim(I('get.download'));
        if(in_array($type,array('propagandat_img','flowscore_basic_logo','flowscore_basic_background','share_img'))) {
            parent::download('.'.$list[$type]);
        }

    }



}