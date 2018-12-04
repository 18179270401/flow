<?php

/*
 * 参数管理控制器
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class SamtonchannelController extends CommonController {

    private $getall_url = 'http://www.liuliang.net.cn/index.php/Admin/OptionUserApi/get_option_user_channel';

    private $getinfo_url = 'http://www.liuliang.net.cn/index.php/Admin/OptionUserApi/info';

    private $status_url = 'http://www.liuliang.net.cn/index.php/Admin/OptionUserApi/change_option_user_channel_status';

    private $debug = false;


    /*
     * 资源列表
     */
    public function index(){

        if( !isset($_GET['channel_status']) or $_GET['channel_status'] === '' ) $_GET['channel_status'] = 1;
        if( !isset($_GET['use_status']) or $_GET['use_status'] === '' ) $_GET['use_status'] = 1 ;

        $citys=M("sys_city")->select();
        
        $provinces = M('sys_province')->order("order_num asc")->select();

        $this->assign('citys' , $citys);

        $this->assign( 'provinces' , $provinces );
        $array['province_id'] = $_GET['province_id'];
        $array['city_id']     = $_GET['city_id'];
        $array['channel_status'] = $_GET['channel_status'];
        $array['use_status']    = $_GET['use_status'];

        if( $this->debug )
        { 
            $result = array('error_code' => 200 , 'data'=>'[{"channel_id":"1","channel_name":"\u5c1a\u901a\u79d1\u6280-\u6c5f\u897f\uff08\u5168\u56fd\uff09","province_id":"17","city_id":"","yddiscount":"7.2","ltdiscount":"7.5","dxdiscount":"8.4","channel_status":"1","use_status":"1"},{"channel_id":"2","channel_name":"\u5c1a\u901a\u79d1\u6280-\u5e7f\u4e1c\uff08\u5168\u56fd\uff09","province_id":"24","city_id":"","yddiscount":"7.2","ltdiscount":"7.5","dxdiscount":"8.4","channel_status":"0","use_status":"1"},{"channel_id":"3","channel_name":"\u5c1a\u901a\u79d1\u6280-\u5168\u56fd\uff08\u5168\u56fd\uff09","province_id":"1","city_id":"","yddiscount":"9.2","ltdiscount":"10","dxdiscount":"10","channel_status":"1","use_status":"0"}]');
        }else {
            $result = $this->http_curl($this->getall_url, $this->getsign($array));
        }

        if($result['error_code'] === 200)
        {
            $channel_list = json_decode( $result['data'] , true );

            foreach($channel_list as $k=>$v)
            {
                foreach($provinces as $province )
                {
                    if( $province['province_id'] == $v['province_id'] )
                    {
                        $channel_list[$k]['province_name'] = $province['province_name'];
                    }
                }
                foreach( $citys as $city)
                {   
                    $v['city_name'] = '';
                    if( $city['city_id'] == $v['city_id'] )
                    {
                        $channel_list[$k]['city_name'] = $city['city_name'];
                    }
                }
            }
            $this->assign( 'channel_list' , get_sort_no($channel_list) );

        }else
        {
            $this->assign( 'channel_list' , array() );
        }

        $this->display();

    }


    /**
     * 资源详情
     */
    public function show()
    {   
        $channel_id = trim(I('get.channel_id'));
        $array['channel_id']=$channel_id;
        if( $this->debug )
        {
            $result = array('error_code' => 200 , 'data' => '{"channel_id":"1","channel_name":"\u5c1a\u901a\u79d1\u6280-\u6c5f\u897f\uff08\u5168\u56fd\uff09","province_id":"17","city_id":"","yddiscount":"7.2","ltdiscount":"7.5","dxdiscount":"8.4","channel_status":"1","use_status":"1","ydproduct":["10M","30M","70M","150M","500M","1G","2G","3G","4G","6G","11G"],"ltproduct":["20M","50M","100M","200M","500M"],"dxproduct":["5M","10M","30M","50M","100M","200M","500M","1G"]}');

        }else
        {
            $result = $this->http_curl($this->getinfo_url , $this->getsign( $array ) );
        }
  
        if( $result['error_code'] === 200 )
        {
            $channel_info = json_decode( $result['data'] , true );
            $this->assign('channel_info',$channel_info);
        }else
        {
            $this->assign('channel_info' , array() );
        }
        
        $this->display();

    }


    /**
     * 启用|停用资源
     */

    public function status()
    {

        $status = 'error';
        $msg = '网络占线，请稍候重试';

        $type = I('post.use_status');

        $channel_id = I('post.channel_id');

        $array['status'] = $type == 1 ? 'stop' : 'start' ;

        $array['channel_id'] = $channel_id;

        if( $this->debug )
        {
            $result = array('error_code' => 200 , 'data' => '{"status":"success","msg":"操作成功"}');
        }else
        {
            $result = $this->http_curl( $this->status_url , $this->getsign( $array ) );
        }
       
        if( $result['error_code'] === 200 )
        {
            $jsondata = json_decode( $result['data']  , true );

            if( $jsondata['status'] == 'success' )
            {
                if( $type == '1' )
                {
                    $msg = '停用成功';
                    $status = 'success';
                }else
                {
                    $msg = '启用成功';
                    $status = 'success';
                }
            }else
            {
                if( $type == '1' )
                {
                    $msg = '停用失败';
                }else
                {
                    $msg = '启用失败';
                }
            }

        }
        
        $this->ajaxReturn( array('status' => $status , 'msg' => $msg) );

    }


    #获取签名
    private function getsign( &$array = array() )
    {
        $array['account'] = C('API_ACCOUNT');
        $array['sign'] = md5(C('API_KEY').C('API_ACCOUNT').C('API_KEY'));
        return $array;
    }


    /*
     *  接口名称:发送请求
     *  功能描述:CURL套件发送HTTP请求
     *  访问形式：本类调用
     *  参数列表: 请求地址 , 请求参数 (如果附带参数二则为POST请求)
     *  返回值  ：请求后的内容(string|NULL)
     */
    private function http_curl( $url , $data = null , $type = null )
    {   

        if( $type === 'json' )
        {
            $header[] = 'Content-Type: application/json;charset=UTF-8';
        }

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        if (!empty($header)) curl_setopt($curl, CURLOPT_HTTPHEADER, $header ); 

        if (  $data !== null ) {

            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($curl);

        $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE); 

        curl_close($curl);

        return array('error_code' => $httpCode , 'data' => $output );
       

    }



}