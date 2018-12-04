<?php
/**
 * 流量场景 收款设置控制器
 */
namespace Admin\Controller;
use Think\Controller;
//use \Think\Page;

class SceneAccountController extends CommonController {
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
            $msg = "收款设置保存失败！";

            $account_id            = $post['account_id'];

            //文件上传*****************************************************
            if (!empty($_FILES)) 
            {
                $fileinfo = $this->scene_account_upload(C('ENTERPRISE_SCENE_UPLOAD_DIR').$account_id."/");
                $error = $this->business_licence_upload_Error;
                if ($error) {
                    if ($error['wx_pem_file_one'] && $error['wx_pem_file_one'] != '没有文件被上传！') {
                        $msg = '微信公众号pem文件1' . $error['wx_pem_file_one'];
                        $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
                    }
                    if ($error['wx_pem_file_two'] && $error['wx_pem_file_two'] != '没有文件被上传！') {
                        $msg = '微信公众号pem文件2' . $error['wx_pem_file_two'];
                        $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
                    }
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
                    //三张轮播图
                    if ($error['scene_uploadone'] && $error['scene_uploadone'] != '没有文件被上传！') {
                        $msg = '轮播图1' . $error['scene_uploadone'];
                        $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
                    }
                    if ($error['scene_uploadtwo'] && $error['scene_uploadtwo'] != '没有文件被上传！') {
                        $msg = '轮播图2' . $error['scene_uploadtwo'];
                        $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
                    }
                    if ($error['scene_uploadthree'] && $error['scene_uploadthree'] != '没有文件被上传！') {
                        $msg = '轮播图3' . $error['scene_uploadthree'];
                        $this->ajaxReturn(array('msg' => $msg, 'status' => $error));
                    }
                }

                //轮播图
                if ($fileinfo['scene_uploadone']) {
                    $scene_uploadone = substr(C('UPLOAD_DIR') . $fileinfo['scene_uploadone']['savepath'] . $fileinfo['scene_uploadone']['savename'], 1, strlen(C('UPLOAD_DIR') . $fileinfo['scene_uploadone']['savepath'] . $fileinfo['scene_uploadone']['savename']) - 1);
                    $data['scene_uploadone']=$fileinfo['scene_uploadone']['savename'];
                } else {
                    $scene_uploadone = '';
                }
                if ($fileinfo['scene_uploadtwo']) {
                    $scene_uploadtwo = substr(C('UPLOAD_DIR') . $fileinfo['scene_uploadtwo']['savepath'] . $fileinfo['scene_uploadtwo']['savename'], 1, strlen(C('UPLOAD_DIR') . $fileinfo['scene_uploadtwo']['savepath'] . $fileinfo['scene_uploadtwo']['savename']) - 1);
                    $data['scene_uploadtwo']=$fileinfo['scene_uploadtwo']['savename'];
                } else {
                    $scene_uploadtwo = '';
                }
                if ($fileinfo['scene_uploadthree']) {
                    $scene_uploadthree = substr(C('UPLOAD_DIR') . $fileinfo['scene_uploadthree']['savepath'] . $fileinfo['scene_uploadthree']['savename'], 1, strlen(C('UPLOAD_DIR') . $fileinfo['scene_uploadthree']['savepath'] . $fileinfo['scene_uploadthree']['savename']) - 1);
                    $data['scene_uploadthree']=$fileinfo['scene_uploadthree']['savename'];
                } else {
                    $scene_uploadthree = '';
                }
                //轮播图
              

                if ($fileinfo['wx_pem_file_one']) {
                    $wx_pem_file_one = substr(C('UPLOAD_DIR') . $fileinfo['wx_pem_file_one']['savepath'] . $fileinfo['wx_pem_file_one']['savename'], 1, strlen(C('UPLOAD_DIR') . $fileinfo['wx_pem_file_one']['savepath'] . $fileinfo['wx_pem_file_one']['savename']) - 1);
                    $data['wx_pem_file_one']=$fileinfo['wx_pem_file_one']['savename'];
                } else {
                    $wx_pem_file_one = '';
                }
                if ($fileinfo['wx_pem_file_two']) {
                    $wx_pem_file_two = substr(C('UPLOAD_DIR') . $fileinfo['wx_pem_file_two']['savepath'] . $fileinfo['wx_pem_file_two']['savename'], 1, strlen(C('UPLOAD_DIR') . $fileinfo['wx_pem_file_two']['savepath'] . $fileinfo['wx_pem_file_two']['savename']) - 1);
                    $data['wx_pem_file_two']=$fileinfo['wx_pem_file_two']['savename'];
                } else {
                    $wx_pem_file_two = '';
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
            //文件上传*****************************************************
            
            //找到原有
            $usescece = M('user_sceneset')->where("user_set_id={$account_id}")->find();
            $jsonuser_headpics = $usescece["user_headpics"];
            $picsinfo = json_decode($jsonuser_headpics);//数组

      
            $jsonuser_headpicitem1 = object_array($picsinfo[0]);
            $jsonuser_headpicitem2 = object_array($picsinfo[1]);
            $jsonuser_headpicitem3 = object_array($picsinfo[2]);
            //更新图片路径
            !empty($scene_uploadone) && $jsonuser_headpicitem1['goroundimgpic'] = $scene_uploadone;
            !empty($scene_uploadtwo) && $jsonuser_headpicitem2['goroundimgpic'] = $scene_uploadtwo;
            !empty($scene_uploadthree) && $jsonuser_headpicitem3['goroundimgpic'] = $scene_uploadthree;
            //轮播图是否显示字符串拼接
            $goroundimgone = trim($post['goroundimgone']);
            $goroundimgtwo = trim($post['goroundimgtwo']);
            $goroundimgthree = trim($post['goroundimgthree']);

            //更新显示
            $jsonuser_headpicitem1["goroundimg"] = $goroundimgone;
            $jsonuser_headpicitem2["goroundimg"] = $goroundimgtwo;
            $jsonuser_headpicitem3["goroundimg"] = $goroundimgthree;
            
            $scenepics = array();
            $scenepics[] = $jsonuser_headpicitem1;
            $scenepics[] = $jsonuser_headpicitem2;
            $scenepics[] = $jsonuser_headpicitem3;
            
            $picstr = json_encode($scenepics);
            $scenesetinfo["user_headpics"] = $picstr;
            //存储t_flow_user_sceneset

            //存储是否需要关注才能充值
            $follow_type = trim($post['follow_type']);
            $scenesetinfo["follow_type"] = $follow_type;
            
            $rt = M('user_sceneset')->where("user_set_id={$account_id}")->save($scenesetinfo);
            //***************************************************************************************

            if($post['payment_type_default']==3){
                $msg='请选择收款方式';
                IS_AJAX && $this->ajaxReturn(array('msg' => $msg, 'status' => $status,'data'=>$data));
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

            !empty($wx_pem_file_one) && $upd['wx_pem_file_one'] = $wx_pem_file_one;
            !empty($wx_pem_file_two) && $upd['wx_pem_file_two'] = $wx_pem_file_two;
            !empty($app_pem_file_one) && $upd['app_pem_file_one'] = $app_pem_file_one;
            !empty($app_pem_file_two) && $upd['app_pem_file_two'] = $app_pem_file_two;
            !empty($alipay_pem_file) && $upd['alipay_pem_file'] = $alipay_pem_file;
            !empty($alipay_pem_file_two) && $upd['alipay_pem_file_two'] = $alipay_pem_file_two;

            $upd['consumer_phone']=trim($post['consumer_phone']);
            $upd['explanation']=trim($post['explanation']);
            $upd['pc_explanation']=trim($post['pc_explanation']);//网页充值说明
            $upd['pc_notice']=trim($post['pc_notice']);//网页公告
            $upd['pub_notice']=trim($post['pub_notice']);//公共公告
            $upd['pc_alipay_account']=trim($post['pc_alipay_account']);
            $upd['pc_alipay_partner']=trim($post['pc_alipay_partner']);
            $upd['pc_alipay_key']=trim($post['pc_alipay_key']);
            !empty($post['wx_appid']) && $upd['wx_appid'] = trim($post['wx_appid']);
            !empty($post['wx_appsecret']) && $upd['wx_appsecret'] = trim($post['wx_appsecret']);
            !empty($post['wx_mchid']) && $upd['wx_mchid'] = trim($post['wx_mchid']);
            !empty($post['wx_key']) && $upd['wx_key'] =trim($post['wx_key']);
            !empty($post['app_appid']) && $upd['app_appid'] = trim($post['app_appid']);
            !empty($post['app_appsecret']) && $upd['_appsecret'] = trim($post['app_appsecret']);
            !empty($post['app_mchid']) && $upd['app_mchid'] = trim($post['app_mchid']);
            !empty($post['app_key']) && $upd['app_key'] = trim($post['app_key']);
            !empty($post['alipay_partner']) && $upd['alipay_partner'] = trim($post['alipay_partner']);
            !empty($post['alipay_key']) && $upd['alipay_key'] = trim($post['alipay_key']);
            !empty($post['third_app_key']) && $upd['third_app_key'] = trim($post['third_app_key']);
            !empty($post['third_app_code']) && $upd['third_app_code'] = trim($post['third_app_code']);
			!empty($post['template_type']) && $upd['template_type'] = trim($post['template_type']);
            //存储t_flow_user_set
            $rt = M('user_set')->where("account_id={$account_id}")->save($upd);


            D('SceneInfo')->get_scene_info($user_type, $self_proxy_id, $self_enterprise_id);
            if(false !== $rt) {
                $status = "success";
                $msg = "收款设置保存成功";
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
            $data= localencode($user_type.",".$user_id);
            if(gethostwithhttp()=="http://liuliang.net.cn"){
                $aa="http://www.liuliang.net.cn";
            }else{
                $aa=gethostwithhttp();
            }
            $list['redpack_address']=$aa."/index.php/Sdk/FlowRed/aindex?".$data;
            $list['recharge_address']=$aa."/index.php/Sdk/WxFlowPayment/aindex?".$data;
            $list['pc_recharge_address']=$aa."/index.php/Sdk/WebPayment/aindex?".$data;
            $list['recharge_record_address']=$aa."/index.php/Sdk/WxRechargeRecord/aindex?".$data;
            // var_dump($list);
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
            if(empty($list['explanation'])){
                $list['explanation']="1、为什么我在充值的时候，提示我“该号码不可充流量”？
根据相关规定，对于部分如非3G号码/欠费/非实名制/运营商黑名单用户，暂时不能使用流量充值服务。对于实名制等业务的办理方法，建议联系归属地运营商。

2、为什么我充值后，使用流量还被运营商扣了额外的费用？
充值完成后，第三方中间服务商可能因为网络原因没有及时将充值电子订单发送至运营商，运营商对您的充值情况不知情。运营商会在收到第三方服务方订单后为您充值，并在成功后为您发送到账短信。请确保您在收到到账短信后再使用流量，以防出现流量包之外的费用。出现流量不到账的时候，建议您先参考信息5，确认您的订单情况。

3、充值的流量可以漫游吗?
充值时请关注页面提示，如果显示“全国可用”表示可以全国漫游。

4、充值的流量有效期是多长?
大部分省份运营商充值流量当月有效，月底失效，部分面额30天内有效及三个月有效，灵活账期用户月结日失效。请以短信通知及在运营商处查询的信息为准。

5、我充值后怎么查看是否到账?
充值后一般10分钟-30分钟内流量会到账，同时会收到运营商官方号码（10086、10010、10000）发来的短信通知，如果收到通知即到到账。
也有部分情况（每个月的5号之前、每个月最后两天）由于运营商BOSS系统延时，会收不到短信（或短信通知较晚）。
出现上述情况时可以有两种方式可明确是否到账：
第一、直接致电运营商官方客服查询即可。
第二、登陆运营商官方网站查询即可。

6、流量充错号码怎么办?
非常抱歉，充错号码后运营商（移动、联通、电信）是不会办理退款的。 由于充值成功后，交易就已经完成，运营商不会将已经充上的流量退还给供货商，所以我们也无法给您办理退款。您可以选择如下几种方式尝试弥补损失： 1、联系实际充值的号码机主，与对方协商是否愿意为此补偿您的流量； 2、联系运营商客服（移动10086、联通10010、电信10000），咨询是否能够退还已经充值成功的流量； 给您带来的不便，尽请谅解！并希望您在下次操作的时候注意核对号码是否正确，谢谢您的支持！

7、充值失败后，为什么我没有收到退款?
通常情况下，充值失败时我们会立即为您办理退款，如果您使用银行卡或者零钱支付，退款会立即退回微信钱包。信用卡退款时间可能会较长，您可以直接致电银行或登录网银查看退款情况。

8、为什么我的号码一直充值失败？
由于联通存在每个面额每个月充值5次的限制，充值超过5次（不限微信平台）将会出现失败退款。建议您充值其他面额的流量。
另外，号码欠费、套餐互斥、非实名认证、运营商黑名单等原因也会导致充值失败退款。

9、充值流量后能取消吗？
不可以。流量充值接近实时交易，支付完成后，交易系统会在数秒中向运营商发起充值请求并且充值到账。充值中的订单也会锁定，无法进行资金回退交易。您需要在充值前确定需要充值流量。     ";
            }

                
            $this->getgoroundinfo($self_enterprise_id);
            $this->assign("list",$list);
            $this->display();
        }
    }

//＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊

    //获取轮播图片
    public function getgoroundinfo($self_enterprise_id)
    {
            //查找到相应企业设置的轮播图
            //获取配置类型
            $map["enterprise_id"] = $self_enterprise_id;
            $usescece =M('user_set as u')
                ->join('left join t_flow_user_sceneset as s on s.user_set_id = u.account_id')
                ->where($map)
                ->find();
            $jsonuser_headpics = $usescece["user_headpics"];
            //json解析
            $user_headpics = json_decode($jsonuser_headpics);
            for($count = 0;$count<3;$count++)
            {
                $picitem = $user_headpics[$count];
                $picitem = object_array($picitem);
                if($count == 0)
                {
                    $obj = "goroundimgonepic";
                    $objstatue = "goroundimgone";
                }
                else if($count == 1)
                {
                    $obj = "goroundimgtwopic";
                    $objstatue = "goroundimgtwo";
                }
                else
                {
                    $obj = "goroundimgthreepic";
                    $objstatue = "goroundimgthree";
                }
                $goroundimgpic = $picitem["goroundimgpic"];
                if(empty($goroundimgpic))
                {
			    	$goroundimgpic = 'http://'.$_SERVER['HTTP_HOST']."/Application/Sdk/View/WxFlowPayment/gdn/images/banner.png";
                    //如果没有图片
                }
                $goroundimg = $picitem["goroundimg"];
                if(empty($goroundimg))
                {
                    //如果没有显示
                    $goroundimg = 2;//默认隐藏  
                }     
                
                $this->assign($obj,$goroundimgpic);
                $this->assign($objstatue,$goroundimg);
            }

            $follow_type = $usescece["follow_type"];
            if(empty($follow_type))
            {
                //默认不需要关注
                $follow_type = 1;
            }
            $this->assign("follow_type",$follow_type);
            //获取当前是否需要关注

    }

    //图片上传
    public function uploadimg()
    {
            $self_user_id = D('SysUser')->self_id();
            //保存场景基本信息
            $post = I("post.");
            $status = "error";
            $msg = "流量活动设置保存失败！";
            if (!empty($_FILES)) {
                $fileinfo = $this->scene_base_upload(C('ENTERPRISE_SCENE_UPLOAD_DIR'));
                $error = $this->business_licence_upload_Error;
            }
    }
//＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊


    public function show(){
        $msg="系统错误！";
        $status="error";
        $type=I("get.type");
        $account_id=I("account_id");
        //多来源标记
        $recharge_sources = I('recharge_sources');

        if(empty($account_id)){
            $msg="参数错误！";
            $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
        }
        $map['account_id']=$account_id;
        if($type=="add"){
            $sources=I("get.sources");//标示是h5还是网页端
            $info=M("user_set")->where($map)->find();
            if($info){
                $info['sources']=$sources;
                $this->assign("info",$info);
                $this->display("add");
            }else{
                $msg="系统错误！";
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
            }
        }else{
            $sources=I("sources");
            $info=M("user_set")->where($map)->find();
            $user_type = $info["user_type"];
            if($user_type == 1)
            {
        	    $user_id = $info["proxy_id"];
                $proxy_id=$info["proxy_id"];
            }
            else {
        	    $user_id = $info["enterprise_id"];
                $enterprise_id=$info['enterprise_id'];
            }
            $data = localencode($user_type.",".$user_id.",".$recharge_sources);
			$seurl = gethostwithhttp();
			if($seurl=="http://liuliang.net.cn"){
                $seurl="http://www.liuliang.net.cn";
            }
            if($sources==2){
                $url = $seurl."/index.php/Sdk/WebPayment/aindex?".$data;
                $ts="网页充值链接";
            }else{
                $url = $seurl."/index.php/Sdk/WxFlowPayment/aindex?".$data;
                $ts="流量充值链接";
            }
            $map=array(
                "user_type" => $user_type,
                "proxy_id" => $proxy_id,
                "enterprise_id"=>$enterprise_id,
                "sources_name" => $recharge_sources,
                "sources_url"  =>  $url,
                "create_user_id" => D("SysUser")->self_id(),
                "create_date" => date('Y-m-d H:i:s')
            );
            $where=array(
                "user_type" => $user_type,
                "enterprise_id"=>$enterprise_id,
                "sources_name" => $recharge_sources,
                "sources_url"  =>  $url
            );
            $c=M("pay_sources_record")->where($where)->find();
            if($c){
                $msg="该渠道名称已被使用请勿重复生成！";
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
                exit();
            }
            $rt=M("pay_sources_record")->add($map);
            if($rt){
                $url=$ts."：<br/>".$url;
                $msg=$ts."生成成功！";
                $status="success";
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status,'data'=>$url));
            }else{
                $msg=$ts."生成失败！";
                $this->ajaxReturn(array('msg' => $msg, 'status' => $status));
            }
        }
    }


}