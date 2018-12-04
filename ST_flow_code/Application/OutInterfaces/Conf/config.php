<?php
return array(
	//状态信息
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
);
?>