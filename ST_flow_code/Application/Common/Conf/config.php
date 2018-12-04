<?php


 if( file_exists( dirname(__FILE__).'/system_config.php' ) )
 {
    $system_config = require dirname(__FILE__).'/system_config.php';


 }else
 {
    throw new Exception('缺失系统配置文件');
 }



$config =  array(
	//'配置项'=>'配置值'
    'VERSION_NUMBER'                    =>'1.1.7',      //JS、CSS版本号
    'RBAC_ROLE'                         =>array(30),    //运营商管理没有所有权限的角色
    'UPPER_ROLE'                        =>array(44,93), //上游端所拥有的角色(发开和测试平台)
    //'UPPER_ROLE'                        =>array(90),    //上游端所拥有的角色(正式平台)
    'QUERY_ROLE'                        =>array(44,93,46),  //已完成充值记录通道高级查询指定角色(发开和测试平台)
    //'QUERY_ROLE'                        =>array(109),  //已完成充值记录通道高级查询指定角色(正式平台)
    'BORROW_ROLE'                       =>array(50,104),    //运营端还款管理开发平台,测试平台
    //'BORROW_ROLE'                       =>array(112),    //运营端还款管理正式平台
    'E_BORROW_ROLE'                     =>array(51,105),    //代理商端还款管理开发平台,测试平台
    //'E_BORROW_ROLE'                   =>array(113),       //代理商端还款管理正式平台

    'EXCEL_SPECIAL_USER'                => array(510),
    

    //首页域名跳转
    'DOMAIN_LOGIN_URL'  =>  array(
        'www.liuliang.net.cn' => 'login',
    ),


    "SYSTEM_CONFIG_FILE_PATH"   =>  dirname(__FILE__)."/system_config.php",

    //域名
    'MAIN_WEB_SITE' => array(
        'www.liuliang.net.cn',
        'liuliang.net.cn',
        'web.svnwsw1.com'
    ),

    //登录后LOGO与标题是否做判断
    'JUDGE_LOGIN'          =>false,

    //监控接口同时在线最大人数
    'MONITOR_MAX_LOGIN'    => 10,
    'MONITOR_DOWNLOAD_PACKAGE'    => '/Public/Uploads/Monitor/jkxt.rar',    //监控客户端下载路径
    //监控接口最新版本
    'MONITOR_LAST_VISION'    => array(
        'version' => '1.0.0.1',   //版本号
        'update' => ''       //更新内容
        ),


    //短信提醒配置
    'CHANNEL_ACCOUNT_SEND_IDS'          =>17,           //通道帐户不足事务提醒ID
    'CHANNEL_ACCOUNT_MINIMUM_AMOUNT'    =>10000,        //通道账户最小额度提醒额度
    'CHANNEL_ACCOUNT_SEND_TIMES'        =>1,            //通道账户提醒信息发送次数
    'CHANNEL_ACCOUNT_MSG_TYPE'          =>2,            //通道账户提醒类型
    'CHANNEL_ACCOUNT_CONTENT'           =>'通道账户####可使用金额少于$$$$元，请及时处理',//通道账户提醒信息内容（####=>通道名称、$$$$=>通道提醒金额）

    'CHANNEL_PROVINCE_SEND_IDS'         =>18,           //通道额度不足事务提醒ID
    'CHANNEL_PROVINCE_MINIMUM_AMOUNT'   =>50000,        //通道额度最小额度提醒额度
    'CHANNEL_PROVINCE_SEND_TIMES'       =>1,            //通道额度提醒信息发送次数
    'CHANNEL_PROVINCE_MSG_TYPE'         =>1,            //通道额度提醒类型
    'CHANNEL_PROVINCE_CONTENT'          =>'通道####省份****可使用额度少于$$$$元，请及时处理',//通道额度提醒信息内容（####=>通道名称、****=>通道省份、$$$$=>通道提醒金额）

    'USER_PE_OPEN_AMOUNT'               =>0.01,         //代理商或企业账户开始金额
    'USER_P_MINIMUM_AMOUNT'             =>10000,        //代理商最小额度
    'USER_E_MINIMUM_AMOUNT'             =>10000,        //企业最小额度
    'USER_SEND_TIMES'                   =>1,            //代理商或企业账户提醒信息发送次数
    'USER_SEND_MSG_TYPE'                =>3,            //代理商或企业账户提醒类型
    'USER_SEND_CONTENT'                 =>'您好，您的尚通流量账户余额不足$$$$$元，当前余额$$$$元，为不影响您正常开展业务，请及时加款。详情请登录爱讯流量平台查看',//代理商或企业账户提醒信息内容（$$$$$=>提醒金额、$$$$=>当前账户金额）

    //短信发送配置
    'SEND_STATE'                        =>0,            //发送短信的状态
    'SEND_TIMES'                        =>1,            //发送次数
    'DELETE_STATUS'                     =>0,            //发送状态



);


return array_merge($config,$system_config);