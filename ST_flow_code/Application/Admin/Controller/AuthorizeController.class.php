<?php
/*
 * AuthorizeController.class.php
 * 微信公众平台数据授权控制器
 *
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class AuthorizeController extends CommonController{
    /*
     *微信公众平台数据授权页面
     */
    public function index(){
      $where = array(
          'user_type' => ((int)D('SysUser')->self_user_type())-1,
          'proxy_id'		=> intval(D('SysUser')->self_proxy_id()),
          'enterprise_id'	=> intval(D('SysUser')->self_enterprise_id()),
        );
      $wx_auth = M('wxs_auth')->field('auth_status,auth_headimg,auth_wxname,auth_businesspay,auth_service_type,auth_nickname')
                ->where($where)
                ->find();
      // 是否已授权
      if($wx_auth['auth_status'] == 1){
        $this->assign('user_type',((int)D('SysUser')->self_user_type())-1);
        $this->assign('proxy_id',intval(D('SysUser')->self_proxy_id()));
        $this->assign('enterprise_id',intval(D('SysUser')->self_enterprise_id()));
        $this->assign('headimg',$wx_auth['auth_headimg']);
        $this->assign('wxname',$wx_auth['auth_wxname']);
        if ($wx_auth['auth_businesspay'] == 1) {
          $this->wx_source();
          $this->assign('businesspay','是');
        }else{
          $this->assign('businesspay','否');
        }
        if($wx_auth['auth_service_type'] == 2){
          $this->assign('service_type','服务号');
        }else{
          $this->assign('service_type','订阅号');
        }
        $this->assign('nickname',$wx_auth['auth_nickname']);
        $start_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d')-61,date('Y')));
        $end_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
        $where['create_date'] = array("between",array($start_datetime,$end_datetime));
        $records = M('wxs_attention')
                  ->field('attention_new,attention_cancel,attention_grow,attention_total,create_date')
                  ->where($where)
                  ->order("create_date desc")
                  ->select();
        // 昨日数据
        $this->assign('yesterday_new',$records[0]['attention_new']);
        $this->assign('yesterday_cancel',$records[0]['attention_cancel']);
        $this->assign('yesterday_grow',$records[0]['attention_grow']);
        $this->assign('yesterday_total',$records[0]['attention_total']);
        // 月增长率
        $month_data_raw = $this->split_records($records,30);
        $this->assign_data_params('month','new',$month_data_raw);
        $this->assign_data_params('month','cancel',$month_data_raw);
        $this->assign_data_params('month','grow',$month_data_raw);
        $this->assign_data_params('month','total',$month_data_raw);
        // 周增长率
        $week_data_raw = $this->split_records($records,7);
        $this->assign_data_params('week','new',$week_data_raw);
        $this->assign_data_params('week','cancel',$week_data_raw);
        $this->assign_data_params('week','grow',$week_data_raw);
        $this->assign_data_params('week','total',$week_data_raw);
        // 日增长率
        $day_data_raw = $this->split_records($records,1);
        $this->assign_data_params('day','new',$day_data_raw);
        $this->assign_data_params('day','cancel',$day_data_raw);
        $this->assign_data_params('day','grow',$day_data_raw);
        $this->assign_data_params('day','total',$day_data_raw);

        $this->display('wx_report');
      }else{
        $this->display('wx_platform_auth');
      }
    }
    /**
     *报表数据前台传参
     *@param $time_space {'day','month'}
     *@param $type {'new','cancel','grow','total'}
     *@param $data_raw 分割后原始数据
     */
    private function assign_data_params($time_space,$type,$data_raw){
      $a_list = array();//累计
      $b_list = array();
      foreach ($data_raw['a'] as $value) {
        if($value == -1){
          array_push($a_list,0);
        }else{
          switch ($type) {
            case 'new':
              array_push($a_list,((int)$value['attention_new']));
              break;
            case 'cancel':
              array_push($a_list,((int)$value['attention_cancel']));
              break;
            case 'grow':
              array_push($a_list,((int)$value['attention_grow']));
              break;
            case 'total':
              array_push($a_list,((int)$value['attention_total']));
              break;
            default:
              break;
          }
        }
      }
      foreach ($data_raw['b'] as $value) {
        if($value == -1){
          array_push($b_list,0);
        }else{
          switch ($type) {
            case 'new':
              array_push($b_list,((int)$value['attention_new']));
              break;
            case 'cancel':
              array_push($b_list,((int)$value['attention_cancel']));
              break;
            case 'grow':
              array_push($b_list,((int)$value['attention_grow']));
              break;
            case 'total':
              array_push($b_list,((int)$value['attention_total']));
              break;
            default:
              break;
          }
        }
      }
      $persentage = $this->compute_percentage($a_list,$b_list);
      if($persentage > 0){
        $this->assign($time_space.'_'.$type.'_arrow','uparrow');
      }else{
        $this->assign($time_space.'_'.$type.'_arrow','downarrow');
      }

      // $this->assign('month_total',$persentage);
      $this->assign($time_space.'_'.$type,abs($persentage));
    }

    /**
     *根据参数分割数据
     *@param $array 数据
     *@param $a 分割数
     */
    private function split_records($array,$num){
        $a = array();
        $b = array();
        for ($i=0; $i < $num; $i++) {
          if($array[$i] != NULL){
            array_push($a,$array[$i]);
          }else{
            array_push($a,-1);
          }
        }
        for ($i=$num; $i < $num*2; $i++) {
          if($array[$i] != NULL){
            array_push($b,$array[$i]);
          }else{
            array_push($b,-1);
          }
        }
        return array('a' => $a, 'b' => $b);
    }
    /**
     *数据百分比计算
     *@param $a 分子
     *@param $b 分母
     */
    private function compute_percentage($a,$b){
        $va = 0;
        $vb = 0;
        foreach ($a as $value) {
          $va += $value;
        }
        foreach ($b as $value) {
          $vb += $value;
        }
        if($vb == 0){
          return 100;
        }
        return round(($va-$vb)/$vb*100,0);
    }
    /*
     *微信服务器公众平台数据
     */
    private function wx_source(){
        $enterprise_id=D('SysUser')->self_enterprise_id();
        $where['enterprise_id']=$enterprise_id;
        $info=M("wxs_attention")->where($where)->order("create_date desc")->find();
        $id=2;//表示是否要去微信获取数据1。需要，2.不需要
        if($info){
            $oldtime=strtotime($info['create_date'])+86400;
            $time=time();
            if($oldtime<$time){
                $id=1;
            }
        }else{
            $id=1;
        }
        if($id==1){
            $enterprise_id=D("SysUser")->self_enterprise_id();
            $si=M("scene_info")->where(array("enterprise_id"=>$enterprise_id))->find();
            $submiturl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$si['active_appid']."&secret=".$si['active_appsecret'];
            $rt = https_request($submiturl);
            $obj = json_decode($rt,true);
            $accesstoken = $obj['access_token'];
            //  微信数据接口
            //echo("https://api.weixin.qq.com/datacube/getusersummary?access_token=".$accesstoken);//关注人数的增减
            //echo("https://api.weixin.qq.com/datacube/getusercumulate?access_token".$accesstoken);//累计关注人数
            $url="https://api.weixin.qq.com/datacube/getusersummary?access_token=".$accesstoken;
            $url1="https://api.weixin.qq.com/datacube/getusercumulate?access_token=".$accesstoken;
        }
        if($info){
            if(strtotime($info['create_date'])+2592000<time()){
                $info['create_date']=date("Y-m-d",mktime(0,0,0,date('m'),date('d')-30,date('Y')));
            }
            $oldtime=strtotime($info['create_date'])+86400;
            $time=time();
           while($oldtime<$time) {
               $list=array();
               $list1=array();
               $start_datetime = date('Y-m-d', $oldtime);
               $end_datetime = $start_datetime;
               $data1 = '{ "begin_date":"' . $start_datetime . '", "end_date":"' . $end_datetime . '"}';
               $result1 = https_request($url, $data1);
               $result2 = https_request($url1, $data1);
               $list = json_decode($result1, true);
               $list1 = json_decode($result2, true);
               if(empty($list['list'])){
                   $attention_new=0;
                   $attention_cancel=0;
                   $attention_grow=0;
               }else{
                   $attention_new=0;
                   $attention_cancel=0;
                   foreach ($list["list"] as $v){
                       $attention_new=$attention_new+$v['new_user'];
                       $attention_cancel=$attention_cancel+$v['cancel_user'];
                   }
                   $attention_grow=$attention_new-$attention_cancel;
               }
               if (empty($list1['list'][0]['cumulate_user'])) {
                   $list1['list'][0]['cumulate_user'] = 0;
               }
               $data['user_type'] = D('SysUser')->self_user_type() - 1;
               $data['proxy_id'] = D('SysUser')->self_proxy_id();
               $data['enterprise_id'] = D('SysUser')->self_enterprise_id();
               $data['attention_new'] = $attention_new;
               $data['attention_cancel'] = $attention_cancel;
               $data['attention_grow'] = $attention_grow;
               $data['attention_total'] = $list1['list'][0]['cumulate_user'];
               $data['create_user_id'] = D("SysUser")->self_id();
               $data['create_date'] = date("Y-m-d H:i:s", $oldtime);
               $data['modify_user_id'] = D("SysUser")->self_id();
               $data['modify_date'] = date("Y-m-d H:i:s", $oldtime);
               M("wxs_attention")->add($data);
               $oldtime = $oldtime + 86400;
           }
        }else{
            $start_datetime=date("Y-m-d",mktime(0,0,0,date('m'),date('d')-1,date('Y')));
            $end_datetime = date("Y-m-d",mktime(0,0,0,date('m'),date('d')-1,date('Y')));
            $data1='{ "begin_date":"'.$start_datetime.'", "end_date":"'.$end_datetime.'"}';
            $result1=https_request($url,$data1);
            $result2=https_request($url1,$data1);
            $list=json_decode($result1,true);
            $list1=json_decode($result2,true);
            if(empty($list['list'])){
                $attention_new=0;
                $attention_cancel=0;
                $attention_grow=0;
            }else{
                $attention_new=0;
                $attention_cancel=0;
                foreach ($list["list"] as $v){
                    $attention_new=$attention_new+$v['new_user'];
                    $attention_cancel=$attention_cancel+$v['cancel_user'];
                }
                $attention_grow=$attention_new-$attention_cancel;
            }
            if (empty($list1['list'][0]['cumulate_user'])) {
                $list1['list'][0]['cumulate_user'] = 0;
            }
            $data['user_type'] = D('SysUser')->self_user_type() - 1;
            $data['proxy_id'] = D('SysUser')->self_proxy_id();
            $data['enterprise_id'] = D('SysUser')->self_enterprise_id();
            $data['attention_new'] = $attention_new;
            $data['attention_cancel'] = $attention_cancel;
            $data['attention_grow'] = $attention_grow;
            $data['attention_total'] = $list1['list'][0]['cumulate_user'];
            $data['create_user_id'] = D("SysUser")->self_id();
            $data['create_date'] = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d')-1,date('Y')));
            $data['modify_user_id'] = D("SysUser")->self_id();
            $data['modify_date'] = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d')-1,date('Y')));
            M("wxs_attention")->add($data);
        }
    }
    /*
     *公众平台图表数据
     */
    public function wx_source_chart_ajax(){
        $user_type=trim(I('user_type'));
        $proxy_id=trim(I('proxy_id'));
        $enterprise_id=trim(I('enterprise_id'));
        $where = array(
          'user_type' => $user_type,
          'proxy_id'		=> $proxy_id,
          'enterprise_id'	=> $enterprise_id
        );
        // 最近7天数据
        $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d')-7,date('Y')));
        $end_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
        $where['create_date'] = array("between",array($start_datetime,$end_datetime));
        $records = M('wxs_attention')
                  ->field('attention_new,attention_cancel,attention_grow,attention_total,create_date')
                  ->where($where)
                  ->order("create_date asc")
                  ->select();
        $this->ajaxReturn(array('data'=>$records));
    }
    /*
     *公众平台历史数据
     */
    public function wx_source_local(){
        $start_datetime=trim(I('start_datetime'));
        $end_datetime=trim(I('end_datetime')) ;
        $where = array(
          'user_type' => D('SysUser')->self_user_type()-1,
          'proxy_id'		=> intval(D('SysUser')->self_proxy_id()),
          'enterprise_id'	=> intval(D('SysUser')->self_enterprise_id()),
        );
        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['create_date'] = array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['create_date'] = array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['create_date'] = array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            $end_datetime= strtotime($start_datetime)-2592000;
            $e_time=start_time(date('Y-m-d',$end_datetime));
            $where['create_date'] = array('between',array($e_time,$start_datetime));
        }
        $count = M('wxs_attention')
            ->field('attention_new,attention_cancel,attention_grow,attention_total,create_date')
            ->where($where)
            ->order("create_date desc")
            ->count();
        $Page = new \Think\Page($count,9);
        $show = $Page->show();
        $records = M('wxs_attention')
            ->field('attention_new,attention_cancel,attention_grow,attention_total,create_date')
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order("create_date desc")
            ->select();
        $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
        $end_datetime= strtotime($start_datetime)-2592000;
        $e_time=start_time(date('Y-m-d',$end_datetime));
        $this->assign('default_end',$start_datetime);
        $this->assign('default_start',$e_time);
        $this->assign('list',get_sort_no($records,$Page->firstRow));  //数据列表
        $this->assign('page',$show);  //分页
        $this->display("wx_list");
    }
}
?>
