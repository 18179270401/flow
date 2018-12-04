<?php


/**
 * 尚通测试通道
 */
class Samton{

    /**
     * 数据变量
     */
    private $data;

    private $account = '';

    private $key = '';

    private $submit_url = 'http://api.liuliang.net.cn/Submit.php';

    private $query_url = 'http://api.liuliang.net.cn/Query.php';

    private $date;

    /**
     * 通用构造函数
     * @param null $data
     * @param bool $commit
     */
    public function __construct($data = null, $commit = false) {

        
        $this->date = date('Y-m-d H:i:s');


        if ($data != null) {
            $this->setData($data);
        }
        
        if ($commit == true) {
            $this->Commit();
        }

    }


    /**
     * 通用数据函数
     * @param $data
     */
    public function setData($data) {
        $this->data = $data;
    }



    /**
     * 通用数据函数
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }



    /**
     * 自定义提交函数
     */
    public function Commit() {

        $data = $this->data; 
        
        foreach ($data as $key => &$value){
            $array = array();
            $array['account']           =   $this->account;
            $array['action']            =   'Charge';
            $array['phone']             =   $value['mobile'];
            $array['range']             =   1;
            $array['size']              =   $value['number'];
            $array['timeStamp']         =   $this->date;
            $array['take_effect_time']  =   '0';
            $array['orderNo']           =   $value['order_code'];
            $array['sign']              =   md5($this->key.'account='.$array['account'].'&action='.$array['action'].'&phone='.$array['phone'].'&range='.$array['range'].'&size='.$array['size'].'&timeStamp='.$array['timeStamp'].$this->key);

            $result = $this->http_curl($this->submit_url , $array );

            if( $result['error_code'] == 200)
            {

                $jsondata = json_decode( $result['data'] , true );

                if(  $jsondata['respCode'] == '0000' ) {

                    $value['order_status'] +=1; 
                    $value['back_content'] .= '->尚通科技[省漫游]提交成功:'.date('Y-m-d H:i:s');
                    $value['channel_order_code'] = $jsondata['orderID'];

                }else{

                    $value['order_status'] +=3; 
                    $value['back_content'] .= '->尚通科技[省漫游]提交失败:'.$jsondata['respMsg'].date('Y-m-d H:i:s');

                }

            }
            
            $data[$key] = $value;

        }
        

        $this->data = $data;
    }


    /**
     * 处理好查询返回结果即可。
     */

    public function QueryResult() {

        //获取成员
        $data = $this->data;

        foreach ($data as $key => &$value) {
            
            $array['account']    = $this->account;
            $array['action']     = 'Query';
            $array['orderID']    = $value['order_code'];
            $array['timeStamp']  = $this->date;
            $array['sign']       = md5($this->key.'account='.$array['account'].'&action='.$array['action'].'&orderID='.$array['orderID'].'&timeStamp='.$array['timeStamp'].$this->key );
            
            $result = $this->http_curl($this->query_url , $array );

            if( $result['error_code'] == 200)
            {
                $jsondata = json_decode( $result['data'] , true );

                if ( $jsondata['respCode']  === '0002') {

                $value['order_status'] += 1; //目标是 1->2  4->5
                $value['back_content'] .= '->尚通科技[省漫游]充值成功(查询) '.date("Y-m-d H:i:s");

                }elseif( $jsondata['respCode']  === '0003' ){

                    $value['order_status'] += 2; //目标是 1->2  4->5
                    $value['back_content'] .= '->尚通科技[省漫游]充值失败(查询) '.date("Y-m-d H:i:s");
       
                }

            }
            
            //重组数组
            $data[$key] = $value;
        }

        //设置成员
        $this->data = $data;

        //返回成员
        return $data;
        
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

/*

$array = array(
    123=>array(
        'order_id'      =>      123,
        'order_code'    =>      '137171053601475324532453429',
        'back_content'  =>      '',
        'mobile'        =>      '13717105360',
        'number'        =>      '10M',
        'order_status'  =>      1,
        'channel_id'    =>      2,
        'back_channel_id'=>     2,
        'channel_order_code'=>  'WD201606021136593779'
        ),
    );

$obj = new DingShan($array,true);
echo '<meta charset=utf-8><pre>';var_dump($obj->QueryResult());exit;

*/
