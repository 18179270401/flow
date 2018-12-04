<?php
/**
 * 流量场景 收款设置控制器
 */
namespace Admin\Controller;
use Think\Controller;
//use \Think\Page;

class SceneDeveloperController extends CommonController {
    /**
     * 流量场景 收款设置
     */
    public function index() {
        $self_user_type = D('SysUser')->self_user_type(); //1 2 3
        $user_type = $self_user_type - 1; //1：代理商、2：企业
        $self_proxy_id = D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        $self_user_id = D('SysUser')->self_id();

        $type = I('get.type');
        if($type=="save"){
            $name=I('name');
            $value=I('value');
            if(empty($name) || empty($value)){
                $this->ajaxReturn(array('msg'=>"编辑失败",'status'=>'error'));
            }
            $da[$name]=$value;
            $account_id = I("account_id");
            $rt = M('user_set')->where("account_id={$account_id}")->save($da);
            $this->ajaxReturn(array('msg'=>"编辑成功",'status'=>'success'));
        } else if($type == "operation") {
            //保存场景基本信息
            $post = I("post.");
            $status = "error";
            $msg = "开发者设置保存失败！";

            $account_id            = $post['account_id'];

            if (!empty($_FILES)) {
                $fileinfo = $this->scene_pem_upload(C('ENTERPRISE_SCENE_UPLOAD_DIR').$account_id."/");
                $error = $this->business_licence_upload_Error;
                if ($error) {
                    if ($error['app_pem_file_one'] && $error['app_pem_file_one'] != '没有文件被上传！') {
                        $msg = '微信开发者pem文件1' . $error['app_pem_file_one'];
                        $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
                    }
                    if ($error['app_pem_file_two'] && $error['app_pem_file_two'] != '没有文件被上传！') {
                        $msg = '微信开发者pem文件2' . $error['app_pem_file_two'];
                        $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
                    }
                    if ($error['alipay_pem_file'] && $error['alipay_pem_file'] != '没有文件被上传！') {
                        $msg = '支付宝pem文件' . $error['alipay_pem_file'];
                        $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
                    }
                    if ($error['alipay_pem_file_two'] && $error['alipay_pem_file_two'] != '没有文件被上传！') {
                        $msg = '支付宝pem文件' . $error['alipay_pem_file_two'];
                        $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
                    }
                }
                if ($fileinfo['app_pem_file_one']) {
                    $app_pem_file_one = substr(C('UPLOAD_DIR') . $fileinfo['app_pem_file_one']['savepath'] . $fileinfo['app_pem_file_one']['savename'], 1, strlen(C('UPLOAD_DIR') . $fileinfo['app_pem_file_one']['savepath'] . $fileinfo['app_pem_file_one']['savename']) - 1);
                    $data['app_pem_file_one']=$fileinfo['app_pem_file_one']['savename'];
                } else {
                    $app_pem_file_one = '';
                }
                if ($fileinfo['app_pem_file_two']) {
                    $app_pem_file_two = substr(C('UPLOAD_DIR') . $fileinfo['app_pem_file_two']['savepath'] . $fileinfo['app_pem_file_two']['savename'], 1, strlen(C('UPLOAD_DIR') . $fileinfo['app_pem_file_two']['savepath'] . $fileinfo['app_pem_file_two']['savename']) - 1);
                    $data['app_pem_file_two']=$fileinfo['app_pem_file_two']['savename'];
                } else {
                    $app_pem_file_two = '';
                }
                if ($fileinfo['alipay_pem_file']) {
                    $alipay_pem_file = substr(C('UPLOAD_DIR') . $fileinfo['alipay_pem_file']['savepath'] . $fileinfo['alipay_pem_file']['savename'], 1, strlen(C('UPLOAD_DIR') . $fileinfo['alipay_pem_file']['savepath'] . $fileinfo['alipay_pem_file']['savename']) - 1);
                    $data['alipay_pem_file']=$fileinfo['alipay_pem_file']['savename'];
                } else {
                    $alipay_pem_file = '';
                }
                if ($fileinfo['alipay_pem_file_two']) {
                    $alipay_pem_file_two = substr(C('UPLOAD_DIR') . $fileinfo['alipay_pem_file_two']['savepath'] . $fileinfo['alipay_pem_file_two']['savename'], 1, strlen(C('UPLOAD_DIR') . $fileinfo['alipay_pem_file_two']['savepath'] . $fileinfo['alipay_pem_file_two']['savename']) - 1);
                    $data['alipay_pem_file_two']=$fileinfo['alipay_pem_file_two']['savename'];
                } else {
                    $alipay_pem_file_two = '';
                }
            }
            $upd = array(
               /* 'wx_appid'          => trim($post['wx_appid']),
                'wx_appsecret'      => trim($post['wx_appsecret']),
                'wx_mchid'          => trim($post['wx_mchid']),
                'wx_key'            => trim($post['wx_key']),*/
                //'wx_pem_file_one'   => $wx_pem_file_one,
                //'wx_pem_file_two'   => $wx_pem_file_two,
               /* 'alipay_partner'    => trim($post['alipay_partner']),
                'alipay_key'        => trim($post['alipay_key']),*/
                //'alipay_pem_file'   => $alipay_pem_file,
                'payment_type'      => $post['payment_type_default'],
                'modify_user_id'    => $self_user_id,
                'modify_date'       => date('Y-m-d H:i:s'),
            );

            !empty($app_pem_file_one) && $upd['app_pem_file_one'] = $app_pem_file_one;
            !empty($app_pem_file_two) && $upd['app_pem_file_two'] = $app_pem_file_two;
            !empty($alipay_pem_file) && $upd['alipay_pem_file'] = $alipay_pem_file;
            !empty($alipay_pem_file_two) && $upd['alipay_pem_file_two'] = $alipay_pem_file_two;

            !empty($post['app_appid']) && $upd['app_appid'] = trim($post['app_appid']);
            !empty($post['app_appsecret']) && $upd['app_appsecret'] = trim($post['app_appsecret']);
            !empty($post['app_mchid']) && $upd['app_mchid'] = trim($post['app_mchid']);
            !empty($post['app_key']) && $upd['app_key'] = trim($post['app_key']);
            !empty($post['alipay_partner']) && $upd['alipay_partner'] = trim($post['alipay_partner']);
            !empty($post['alipay_key']) && $upd['alipay_key'] = trim($post['alipay_key']);
            !empty($post['paykey']) && $upd['paykey']=trim($post['paykey']);
            !empty($post['third_app_key']) && $upd['third_app_key'] = trim($post['third_app_key']);
            !empty($post['third_app_code']) && $upd['third_app_code'] = trim($post['third_app_code']);

            $rt = M('user_set')->where("account_id={$account_id}")->save($upd);
            if(false !== $rt) {
                $status = "success";
                $msg = "开发者设置保存成功";
            } else {
                write_error_log(array(__METHOD__.':'.__LINE__,'sql== '.M()->getLastSql()));
            }
            IS_AJAX && $this->ajaxReturn(array('msg' => $msg, 'status' => $status,'data'=>$data));
        } else if($type == "download") {
            $msg = '系统错误！';
            $status = 'error';

            $list = D('SceneInfo')->get_scene_user_set($user_type, $self_proxy_id, $self_enterprise_id);
            $type = trim(I('get.download'));
            if(in_array($type,array('wx_pem_file_one','wx_pem_file_two','alipay_pem_file'))) {
                parent::download_contract('.'.$list[$type]);
            }
        } else {
            //读取场景收款设置

            $list = D('SceneInfo')->get_scene_user_set($user_type, $self_proxy_id, $self_enterprise_id);
            if($user_type==1){
                $user_id=$self_proxy_id;
            }else{
                $user_id=$self_enterprise_id;
            }
            if(empty($list['third_app_key'])){
                if($user_type==1){
                    $third_app_key=$user_type.",".$self_proxy_id.","."jxstkj";
                }else{
                    $third_app_key=$user_type.",".$self_enterprise_id.","."jxstkj";
                }
                $list['third_app_key']=md5($third_app_key);
            }
            if(empty($list['third_app_code'])){
                $str = "0123456789abcdefghijklmnopqrstuvwxyz";//输出字符集
                $n = 16;//输出串长度
                $len = strlen($str)-1;
                $s="";
                for($i=0 ; $i<$n; $i++){
                    $s .= $str[rand(0,$len)];
                }
                $list['third_app_code']=md5($s);
            }
            $this->assign("list",$list);
            $this->display();
        }
    }
    

}