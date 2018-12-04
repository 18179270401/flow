<?php
return array(
	//'配置项'=>'配置值'
    
	// APPID => "wxf72845676fe9134b",
	// APPSECRET => "568832fc8db536b5e3f375af99a218f6",

	APPID => "wx4ba92063598c7663",
	APPSECRET => "435f456c4a69dad3e9758168b6672295",

    
	axtoken=>"aixunliuliang400",
	open_AppID=>"wxf72845676fe9134b",
	open_AppSecret=>"568832fc8db536b5e3f375af99a218f6",

	// 报错提示
	// 账户不存在
	Account_Not_Exit=>'10001',
	Account_Not_Exit_Msg=>'该账户不存在',
	// 企业不存在
	Enterprise_Not_Exit=>'10002',
	Enterprise_Not_Exit_Msg=>'该企业不存在',
	Time_Must_In_A_Month=>10010,
	Time_Must_In_A_Month_Msg=>'开始时间与结束时间必须在同一月份',
	Time_More_Than_A_Month=>10011,
	Time_More_Than_A_Month_Msg=>'查询时间间隔请勿超过31天',
    Param_Error=>'301',
    Param_Error_Msg=>'参数错误',
    No_Login_Right=>'302',
    No_Login_Right_Msg=>'权限不足',

    ///状态信息
    'RETURN_RET_STATUS' => array(
        '200' => '操作成功',
        '300' => '系统错误',
        '301' => '参数错误',
        '302' => '用户名或密码错误',
        '303' => 'token过期',
    ),
    
    //充值记录查询条件
    'FILTER_OPERATOR_ID' => 1,  //运营商
    'FILTER_PROVINCE_ID' => 2,  //省
    'FILTER_CHANNEL_ID' => 3,   //主通道
    'FILTER_BC_CHANNEL_ID' => 4, //备用通道
    'FILTER_ORDER_STATUS' => 5,     //订单状态

    'APP_ROLE_RIGHT' => array(47,103),      //app端登录权限---开发端、测试端
    //'APP_ROLE_RIGHT' => array(108),      //app端登录权限---正式端

    'APP_CW_RIGHT' => array(49,106),      //app端财务权限---开发端、测试端
    //'APP_CW_RIGHT' => array(),      //app端财务权限---正式端

	//app信息
    //安卓版本
    'ANDROID_VERSION' => array(
        'versionCode' => '1',
        'forceCode' => '1',
        'url' => 'http://www.eoc.cn/data/ST_Flow_Android.apk',
        'desc' => ''
    ),

    //苹果版本
    'IOS_VERSION' => array(
        'versionCode' => '1',
        'forceCode' => '1',
        'url' => '',
        'desc' => '苹果第一版'
    ),

    //极光推送
    'JG_PUSH_APP_KEY' => '02a4ae6910e2887747c2b4d3',
    'JG_PUSH_MASTER_SECRET' => 'cd4f9227d3b2c2532a09e04b',
    //超过多少条数就得发送
    'JG_PUSH_THRESHOLD_VALUE' => 2000,





    
	// APPID => "wxf72845676fe9134b",
	// APPSECRET => "568832fc8db536b5e3f375af99a218f6",

	APPID => "wx4ba92063598c7663",
	APPSECRET => "435f456c4a69dad3e9758168b6672295",

);


?>