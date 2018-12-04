<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Upload;
class CommonController extends Controller {

	public $business_licence_upload_Error;  //营业执照上传的错误信息

    public function __construct() {
        parent::__construct();
        //session_start();//将session打开
        //读取JS、CSS版本号
        $this->assign('menu_user_type',D('SysUser')->self_user_type());
        $this->assign('version_number', C('VERSION_NUMBER'));
        $this->assign('volistempty', "<tr><td colspan='20' style='text-align: center;'>对不起，没有找到相关数据！</td></tr>");

    }

    public function _initialize(){

        //判断是否有SESSION
            if(D('SysUser')->self_id()){

                //公有方法 不判断权限问题
                $public = array(
                    'index/index',      //主题框架
                    'index/main',       //主页面统计
                    'index/main_ajax',  //主页面统计JS读取方法
                    'index/account_list',//读取代理商账户
                    'index/get_notice_one',//读取下一条未读公告
                    'index/get_sysnotice_index_one',
                    'index/ajax_username',  //读取代理商和企业信息
                    'index/ajax_channel', //读取通道信息
                    'user/set',         //用户个人设置
                    'user/set_password',//设置密码
                    'role/get_role_function', //获取角色已有功能ID数组
                    'enterprise/set_enterprise',    //超级管理员设置自己的企业信息
                    'proxy/set_proxy',          //超级管理员设置自己的代理商信息
                    'flow/read_operation',      //通过选择运营商和地区列出产品、通过产品列出通道
                    'flowrecharge/operation',    //验证手机号码和读取流量包的操作
                	'flowrecharge/commit_flow_recharge',  //检查并提交充值数据
                    'flowrechargetest/commit_flow_recharge',  //检查并提交通道测试充值数据
                	'flowrecharge/get_op_product',  //根据运营商ID和省份ID获取相应销售产品
                	'flowrecharge/get_p_discount',  //根据运营商ID和省份ID获取相应的折扣
                    'enterprisewithdrawals/enterprise_approve',
                    'proxywithdrawals/approve',
                    'proxyrecharge/insert',
                    'proxyrecharge/approve',
                    'enterprisewithdrawals/insert',
                    'enterpriserecharge/insert',
                    'enterpriseaccount/transfer',
                    'enterpriserecharge/enterprise_approve',
                    'cashrecord/detailed',
                	'discount/searchpe', //查询代理商、企业名字
                    'discount/check_user',
                    'discount/set',
                    'productdiscount/searchpe', //查询代理商、企业名字
                    'productdiscount/check_user',
                    'productdiscount/set',
                    //开发者中心
                    'apiconfiguration/faq',
                    'apiconfiguration/document',
                    'apiconfiguration/respcode',
                    'orderrefund/approve',
                    'orderrefund/insert',
                    'rechargerecord/insert',
                    'objectremind/me_index',
                    'objectremind/me_show',
                    'objectremind/handle',//阅读单个事务功能
                    'objectremind/handle_read_all', //全部阅读事务功能
                    'sysnotices/show',
                    'scenebase/img_download',
                    'sysnotices/me_index',
                    'sysnotices/me_show',
                    'sysnotices/me_read',
                    //定时任务调用发送事务的处理方法(Admin同级文件TimedTask项目里)
                    'earlywarning/channel_account_warning',
                    'earlywarning/channel_quota_warning',
                    //部门管理
                    'depart/ajax_depart_manager_name',
                    'userset/img_download',   //用户设置，图片下载
                    'topcontract/download',   //上游合同下载
                    'operatorinfo/ajax_operator_info_name',   //资源方联想
                    'operatorinfo/set_operator_info_user_rights_list',   //资源方权限列表
                    'operatorinfo/set_operator_info_user_rights_list_ajax',
                    'operatorinfo/set_operator_info_add_all_rights',
                    'operatorinfo/set_operator_info_add_some_rights',
                    'operatorinfo/set_operator_info_del_all_rights',
                    'operatorinfo/set_operator_info_del_some_rights',
                    'operatorinfo/set_operator_info_user_rights_list',   //资源方权限列表
                    'channelrole/set_channel_info_user_rights_list',   //通道权限列表
                    'channelrole/set_channel_info_user_rights_list_ajax', //获取用户所拥有的通道信息
                    'channelrole/set_channel_info_add_all_rights',
                    'channelrole/set_channel_info_add_some_rights',
                    'channelrole/set_channel_info_del_all_rights',
                    'channelrole/set_channel_info_del_some_rights',
                    'channelrole/set_channel_info_user_rights_list',   //资源方权限列表
                    'flowscorebase/img_download',
		            'collectionset/old_enterprise',
					'authorize/wx_source_chart_ajax', //微信平台数据图表
                    'sceneactivity/img_download',
                    'proxyticket/download',
                    'operatorinfo/ajax_username',    //资源方记录人联想
                    'authorize/wx_source_local',
                    'authorize/wx_source',
                    'topticket/delete',     //开票删除
                    'index/ajax_server',
                    'flowticketexchangerecord/index',
                    'domain/download',      //logo下载
                    'index/ajax_city',  //获取市
                    //通道用户管理设置
                    'channeluser/set_channel_info_allot_user_rights_list_ajax', //点击通道，展示用户情况
                    'channeluser/set_channel_info_allot_user_rights_list',  //分配后的刷新
                    'channeluser/set_channel_info_allot_add_some_rights',  //添加用户
                    'channeluser/set_channel_info_allot_del_some_rights',  //删除用户
                    'channeluser/ajax_get_users_by_channel',           //ajax点击通道获取该通道的用户
                    'discount/get_proxy_discount',
                    'productdiscount/get_proxy_discount',
                    //企业充值管理下载图片
                    'enterpriserecharge/voucher',
                    'stat/province_order_pro',
                    'stat/city_order_pro',
                    'stat/province_order_pro_excel',
                    'stat/city_order_pro_excel',
                    'enterpriseaccount/index_proxy',
                    'rechargerecord/export_excel_selected',
                    'channel/channel_msg_show',
                    'channeluser/set_user_info_channel_btn',
                    'flowcodeset/img_download'
                    );

                //判断控制器路径是否有该方法
                $model = CONTROLLER_NAME;
                $action = ACTION_NAME;

                $url = strtolower($model.'/'.$action);
                if(!in_array($url,$public)){
                    if( !in_array( $url , D('SysUser')->getfunctionlist() ) ){
                        if(IS_AJAX){
                            $this->ajaxReturn(array('msg'=>'权限不足','status'=>'error','data'=>array()));
                        }else{
                            $this->error('权限不足');
                        }
                    }
                }

                if(method_exists($this, 'start')){
                    $this->start();
                }

                $province = S('province');
                if(empty($province)) {
	                $province = D("ChannelProduct")->provinceall();//读取省份
	                S('province', $province, 2592000);
                }
                $this->assign('province', $province);

            }else{
                echo '<script>parent.parent.location.href="/index.php/Admin/Public/login.html"</script>';
                //$this->redirect('Public/Login');
            }

    }


    /**
     * 营业执照上传功能
     */
    public function business_licence_upload($savePath){
        $upload = new Upload();// 实例化上传类
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','bmp','ico');// 设置附件上传类型
        $upload->maxSize   =     5242880 ;// 设置附件上传大小       5M
        $upload->rootPath  =     C('UPLOAD_DIR'); // 设置附件上传根目录
        $upload->savePath  =     $savePath; // 设置附件上传（子）目录
        // 上传文件
        foreach($_FILES as $k=>$v){
            $info[$k] = $upload->uploadOne($v);
            if(!$info[$k]){
                //如果失败则设置错误信息
                $this->business_licence_upload_Error[$k] =  $upload->getError();
            }
        }

        return $info;
    }


    /**
     * 合同上传功能
     */
    public function contract_licence_upload($savePath){
        $upload = new Upload();// 实例化上传类
        $upload->exts      =     array('docx','doc','rar','zip','pdf');// 设置附件上传类型
        $upload->maxSize   =     20971520 ;// 设置附件上传大小       20M
        $upload->rootPath  =     C('UPLOAD_DIR'); // 设置附件上传根目录
        $upload->savePath  =     $savePath; // 设置附件上传（子）目录
        // 上传文件
        foreach($_FILES as $k=>$v){
            $info[$k] = $upload->uploadOne($v);
            if(!$info[$k]){
                //如果失败则设置错误信息
                $this->business_licence_upload_Error[$k] =  $upload->getError();
            }
        }

        return $info;
    }

    /**
     * 合同上传功能
     */
    public function ticket_licence_upload($savePath){
        $upload = new Upload();// 实例化上传类
        $upload->exts      =     array('docx','doc','rar','zip','pdf','jpg', 'gif', 'png', 'jpeg','bmp');// 设置附件上传类型
        $upload->maxSize   =     20971520 ;// 设置附件上传大小       20M
        $upload->rootPath  =     C('UPLOAD_DIR'); // 设置附件上传根目录
        $upload->savePath  =     $savePath; // 设置附件上传（子）目录
        // 上传文件
        foreach($_FILES as $k=>$v){
            $info[$k] = $upload->uploadOne($v);
            if(!$info[$k]){
                //如果失败则设置错误信息
                $this->business_licence_upload_Error[$k] =  $upload->getError();
            }
        }

        return $info;
    }


    /**
     * 流量场景>基础设置 图片上传
     */
    public function scene_base_upload($savePath) {
        $upload = new Upload();// 实例化上传类
        $upload->maxSize   =     20971520 ;// 设置附件上传大小       2M
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','bmp');// 设置附件上传类型
        $upload->rootPath  =     C('UPLOAD_DIR'); // 设置附件上传根目录
        $upload->savePath  =     $savePath; // 设置附件上传（子）目录
        // 上传文件
        foreach($_FILES as $k=>$v){
            $info[$k] = $upload->uploadOne($v);
            if(!$info[$k]){
                //如果失败则设置错误信息
                $this->business_licence_upload_Error[$k] =  $upload->getError();
            }
        }

        return $info;
    }

    /**
     * pem上传功能
     */
    public function scene_pem_upload($savePath) {
        $upload = new Upload();// 实例化上传类
        $upload->maxSize   =     7097152 ;// 设置附件上传大小       2M
        $upload->exts      =     array('pem','docx','doc','rar','zip','pdf');// 设置附件上传类型
        $upload->rootPath  =     C('UPLOAD_DIR'); // 设置附件上传根目录
        $upload->savePath  =     $savePath; // 设置附件上传（子）目录
        // 上传文件
        foreach($_FILES as $k=>$v){
            $info[$k] = $upload->uploadOne($v);
            if(!$info[$k]){
                //如果失败则设置错误信息
                $this->business_licence_upload_Error[$k] =  $upload->getError();
            }
        }

        return $info;
    }

    //下载操作
    function download($filename){
            $filepath = $filename;
            $filesize = filesize($filepath);
            header("content-type:application/octet-stream");
            header("content-disposition:attachment;filename=".time().'.jpg');
            header("content-length:{$filesize}");

            readfile($filepath);
    }

    //下载操作
    function download_contract($filename){
        $filepath=$filename;
        $filename =basename($filename);
        $type=explode('.',$filename);
        $filesize = filesize($filepath);
        header("content-type:application/octet-stream");
        header("content-disposition:attachment;filename=".time().'.'.$type[1]);
        header("content-length:{$filesize}");
        readfile($filepath);
    }


   /* 操作日志
        $log_type ----- 日志类型:新增、修改、删除
        $note     ----- 操作概述
   */
    function sys_log($log_type,$note){
      $data['log_type']=$log_type;
      $data['method_url']=MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
      $data['ip_addr']=get_client_ip2();
      $data['note']=$note;
       if(D('SysUser')->self_user_type()==3){
           $data['user_type']=2;
           $data['enterprise_id']=D('SysUser')->self_enterprise_id();
       }else{
           $data['user_type']=1;
           $data['proxy_id']=D('SysUser')->self_proxy_id();
       }
      $data['create_user_id']=D('SysUser')->self_id();
      $data['create_date']=date('Y-m-d H:i:s',time());
      M('sys_log')->add($data);
    }

    /**
     * 代理商或企业充值成功的发送短信提醒
     * $type    = 2,3(2=>代理商，3=>企业)
     * $id      = 代理商或企业ID
     * $money   = 充值金额
     * $balance = 当前余额
     * $remarks = 充值备注
     */
    public function send_recharge($type,$id,$money,$balance,$co_type=NULL,$remarks=NULL){
        $remarks = $remarks==""?"":"备注：".$remarks;
        if($co_type){
            /*借款*/
           if($co_type==1){
               $content = "您好，您的爱讯流量账户成功借款".$money."元，当前余额".$balance."元。".$remarks." 详情请登录爱讯流量平台查看。";
           }
            /*还款*/
            if($co_type==2){
                $content = "您好，您的爱讯流量账户成功还款".$money."元，".$balance.$remarks." 详情请登录爱讯流量平台查看。";
            }
        }else{
            $content = "您好，您的爱讯流量账户成功充值".$money."元，当前余额".$balance."元。".$remarks." 详情请登录爱讯流量平台查看。";
        }

        /*
        //读取用户手机号
        if($type==2){
            $where['proxy_id'] = $id;       //代理商ID
        }else{
            $where['enterprise_id'] = $id;  //企业ID
        }
        $where['is_manager'] = 1;           //超管用户
        //读取当前用户的电话号码
        $list = M("sys_user")->where($where)->field("mobile")->find();
        */
        $arr=array();
        $mobile_content=array();
        if($type==2){
            $where['proxy_id'] = $id;       //代理商ID
            $map['p.proxy_id']=$id;
            $map1['p.top_proxy_id']=1;
            $map1['_logic']="or";
            $map1['tp.proxy_type']=1;
            $map[]=$map1;
            $info=M("proxy as p")->join("t_flow_proxy as tp on tp.proxy_id = p.top_proxy_id")->where($map)->field("p.*")->find();
            if($info){
                $list = M("proxy")->where($where)->field("contact_tel as mobile")->find();
                array_push($arr,$list);
                $uc['company_id']=$id;
                $uc['company_type']=1;
                $uc['status']=1;
                $c_list = M("user_contact")->where($uc)->field("tel as mobile")->select();
                if($arr && $c_list){
                    $mobile_content = array_merge($arr,$c_list);
                }else{
                    $mobile_content = $arr?$arr:$c_list;
                }
            }
        }else{
            $where['enterprise_id'] = $id;  //企业ID
            $map['e.enterprise_id']=$id;
            $map['p.proxy_type']=1;
            $info=M("enterprise as e")->join("t_flow_proxy as p on p.proxy_id = e.top_proxy_id")->where($map)->field("e.*")->find();
            if($info){
                $list = M("enterprise")->where($where)->field("contact_tel as mobile")->find();
                array_push($arr,$list);
                $uc['company_id']=$id;
                $uc['company_type']=2;
                $uc['status']=1;
                $c_list = M("user_contact")->where($uc)->field("tel as mobile")->select();
                if($arr && $c_list){
                    $mobile_content = array_merge($arr,$c_list);
                }else{
                    $mobile_content = $arr?$arr:$c_list;
                }
            }
        }
        //将电话号码中的横线去掉
        $ym=array("http://www.liuliang.net.cn","http://liuliang.net.cn");
        if(!in_array(gethostwithhttp(),$ym)){
            return true;
        }
        foreach ($mobile_content as $list) {
            $list['mobile'] = str_replace("-", "", $list['mobile']);
            if (isMobile2($list['mobile'])) {
                $rt = send_sms($list['mobile'], $content);
                if ($rt <= 0) {
                    write_error_log(array(__METHOD__ . ':' . __LINE__, '短信发送失败，错误编号：' . $rt, $list['mobile'] . "=>" . $content));
                } else {
                    $mobile_add['msg_type'] = 3;
                    $mobile_add['timing'] = 1;
                    if ($type == 2) {
                        $mobile_add['sys_type'] = $type;
                        $mobile_add['proxy_id'] = $id;
                        $mobile_add['enterprise_id'] = null;
                    } else {
                        $mobile_add['sys_type'] = 3;
                        $mobile_add['proxy_id'] = null;
                        $mobile_add['enterprise_id'] = $id;
                    }
                    $mobile_add['order_time'] = date("Y-m-d H:i:s", time());
                    $mobile_add['send_time'] = date("Y-m-d H:i:s", time());
                    $mobile_add['msg_content'] = $content;
                    $mobile_add['mobile'] = $list['mobile'];
                    $mobile_add['send_times'] = 1;
                    $mobile_add['send_state'] = 1;
                    $mobile_add['delete_status'] = 1;
                    M("sms_send")->add($mobile_add);
                }
            }
        }
        return true;
    }


    /**
     * 收款设置上传功能
     */
    public function scene_account_upload($savePath) {
        $upload = new Upload();// 实例化上传类
        $upload->maxSize   =     7097152 ;// 设置附件上传大小       2M
        $upload->exts      =     array('pem','docx','doc','rar','zip','pdf','jpg', 'gif', 'png', 'jpeg','bmp');// 设置附件上传类型
        $upload->rootPath  =     C('UPLOAD_DIR'); // 设置附件上传根目录
        $upload->savePath  =     $savePath; // 设置附件上传（子）目录
        // 上传文件
        foreach($_FILES as $k=>$v){
            $info[$k] = $upload->uploadOne($v);
            if(!$info[$k]){
                //如果失败则设置错误信息
                $this->business_licence_upload_Error[$k] =  $upload->getError();
            }
        }

        return $info;
    }
}
