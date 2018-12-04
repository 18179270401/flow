<?php
namespace TimedTask\Controller;
use Think\Controller;
class NotificationController extends Controller {
    //使用存储过程发送短信
    public function send_notific(){
        set_time_limit(0);
        $sms_list=M()->query("CALL p_get_commit_sms(30,@count)");
        if(empty($sms_list)){
            return;
        }
        foreach ($sms_list as $vo){
            $mobile = $vo['mobile'];
            $content = "您的账户增加了全国流量".$vo['product_name']."，有效期限本月。【流量服务】";
            $order_id = $vo['order_id'];
            $rt = $this->send_notifica_sms($mobile, $content,$order_id);
            if($rt <= 0) {
                write_error_log(array(__METHOD__.':'.__LINE__, '短信发送失败，错误编号：'.$rt, $vo));
            }
            if($rt>0){
                $rt=0;
            }else{
                $rt=1;
            }
            $st=M()->execute("CALL p_tran_commit_sms(".$vo['order_id'].",$rt,'".$content."',@count)");
            if($st <= 0) {
                write_error_log(array(__METHOD__.':'.__LINE__, '存储过程操作失败，错误编号：'.$rt, $vo));
            }
        }
    }

    //thinkphp版
    public function send_notific_test(){
        set_time_limit(0);
        // 取出状态为0的
        M("sms_pre")->startTrans();
        $where['status']=0;
        $where['is_using']=0;
        //取数据
        $sms_list=M("sms_pre")->where($where)->limit(30)->select();
        if(empty($sms_list)){
            return;
        }
        //修改数据状态
        foreach ($sms_list as $v){
            $v['status']=1;
            $v['modify_date']=date("Y-m-d H:i:s");
            $v['is_using']=1;
            $rt=M("sms_pre")->save($v);
            if(!$rt){
                M("")->rollback();
                exit();
            }
        }
        //发短信
        M("sms_pre")->commit();
        foreach ($sms_list as $vo){
            $mobile = $vo['mobile'];
            $content = "您的账户增加了全国流量".$vo['product_name']."，有效期限本月。";
            $order_id = $vo['order_id'];
            $rt = $this->send_notifica_sms($mobile, $content,$order_id);
            if($rt <= 0) {
                $vo['status']=3;
                write_error_log(array(__METHOD__.':'.__LINE__, '短信发送失败，错误编号：'.$rt, $vo));
            }else{
                $vo['status']=2;
            }
            $vo['create_date']=date("Y-m-d H:i:s");
            $vo['modify_date']=date("Y-m-d H:i:s");
            $vo['is_using']=1;
            $r2=M("sms")->add($vo);
            if($r2>0){
                $r1=M("sms_pre")->where("order_id =".$vo['order_id'])->delete();  //取出的数据 删除
            }
            if($r1 <= 0 || $r2<=0) {
                write_error_log(array(__METHOD__.':'.__LINE__, '数据库操作失败，错误编号：'.$rt, $vo));
            }
        }
    }

    /**
     * 发送流量充值成功短信
     */
    function send_notifica_sms($mobile,$content,$order_id) {
        $username = 'gdst';
        $pwd = 'gdst123';
        $password = md5($username."".md5($pwd));
        $url = "http://120.55.248.18/smsSend.do?";

        $param = http_build_query(
            array(
                'username'	=> $username,
                'password'	=> $password,
                'mobile'	=> $mobile,
                //'content'	=> iconv("GB2312","UTF-8",$content),
                'content'	=> $content,
                'ext'		=> '02',
                'msgid'     => $order_id
            )
        );

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$param);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public function callback_report(){
        $host_http=gethostwithhttp();//获取回调域名
        $host_https=array("http://localhost","http://120.26.74.100","http://120.26.203.198");//存放可用域名
        if(in_array($host_http,$host_https)){
            $post=I("post.");  //post 数据格式：report=号码|状态码|短信ID|扩展码|接收时间;号码|状态码|短信ID|扩展码|接收时间
            if(!empty($post['report'])){
                $reports=explode(";",$post['report']);
                foreach ($reports as $vo) {
                    $repo = explode("|",$vo);  //号码|状态码|短信ID|扩展码|接收时间
                    if (!empty($repo[1]) && $repo[1] != "DELIVRD") {
                        //短信发送失败
                        if (!empty($repo[2])) {
                            $info=M("sms")->where("order_id = " . $repo[2])->find();
                            if($info){
                                $data['status'] = 3;
                                $data['modify_date']=date("Y-m-d H:i:s");
                                M("sms")->where("order_id = " . $repo[2])->save($data);
                            }
                        }
                    }
                }
            }
        }
    }

    //测试回调的
    public function c_post(){
        $data['report']="15070610721|AAA|4105";
        https_request("http://localhost/index.php/TimedTask/Notification/callback_report",$data);
    }
    //插入测试数据
    public function set(){
        $data['mobile']="15070410521";
        $data['price']=5;
        $data['product_name']="30M";
        $data['status']=0;
        $data['order_date']=date("Y-m-d",time());
        $data['complete_time']=date("Y-m-d",time());
        $data['create_date']=date("Y-m-d",time());
        $data['modify_date']=date("Y-m-d",time());
        $i=1;
        while($i<2){
            $i++;
            $data['order_id']=5000+$i;
           M("sms_pre")->add($data);
        }
    }
}