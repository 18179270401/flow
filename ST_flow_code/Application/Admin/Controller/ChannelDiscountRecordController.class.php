<?php

namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class ChannelDiscountRecordController extends CommonController {

    /*
    * 通道折扣记录列表
    */
    public function index(){
        D("SysUser")->sessionwriteclose();
        $channel_code = trim(I('channel_code'));
        $channel_name = trim(I('channel_name'));
        $operator_id = I('operator_id');
        $province_id = I('province_id');
        $discount_type = I('discount_type');
        $city_id=I('city_id');
        if(!empty($channel_code))$map['c.channel_code'] =array("like","%".$channel_code."%");
        if(!empty($channel_name))$map['c.channel_name'] =array("like","%".$channel_name."%");
        if(!empty($operator_id))$map['cd.operator_id'] = $operator_id;
        if(!empty($city_id)){
            $map['cd.city_id'] = $city_id;
            if($province_id==1){
                $map['cd.province_id'] = $province_id;
            }else{
                $map['cd.province_id']=0;
            }
        }else{
            if(!empty($province_id))$map['cd.province_id'] = $province_id;
            if($province_id==1){
                $map['cd.city_id'] = 0;
            }
        }
        if(!empty($discount_type))$map['cd.discount_type'] = $discount_type;

        $join = array(
            C('DB_PREFIX').'channel as c ON cd.channel_id=c.channel_id',
            C('DB_PREFIX').'sys_operator as o ON cd.operator_id=o.operator_id',
            C('DB_PREFIX').'sys_province as ps ON cd.province_id=ps.province_id',
            C('DB_PREFIX').'sys_city as sc ON cd.city_id=sc.city_id',
            C('DB_PREFIX').'sys_user as u ON cd.create_user_id=u.user_id'
        );
        //调用分页类
        $count      = M('channel_discount_record as cd')->where($map)->join($join,"left")->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $channeldiscount_list = M('channel_discount_record as cd')->where($map)
            ->join($join,"left")->field("cd.*,c.channel_code,c.channel_name,o.operator_name,ps.province_name,sc.city_name,u.user_name")
            ->order('cd.create_date desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        //加载模板
        $this->assign('channeldiscountrecord_list',get_sort_no($channeldiscount_list,$Page->firstRow));  //数据列表
        $this->assign('page',$show);            //分页

        //读取运营商
        $operator = D("ChannelProduct")->operatorall();
        //读取省份
        $province = D("ChannelProduct")->provinceall();

        //读取市
        if(!empty($province_id)){
            if($province_id!=1){
                $where['province_id']=$province_id;
            }
        }
        $citys=M("sys_city")->where($where)->select();
        $this->assign("citys",$citys);

        //读取通道
        $channel = D("Channel")->channelall();
        $this->assign('operator',$operator);
        $this->assign('province',$province);
        $this->assign('channel',$channel);

        $this->display();
    }

    public function export_excel(){
        $channel_name = trim(I('channel_name'));
        $operator_id = I('operator_id');
        $province_id = I('province_id');
        $discount_type = I('discount_type');
        $city_id=I('city_id');
        if(!empty($channel_name))$map['c.channel_name'] =array("like","%".$channel_name."%");
        if(!empty($operator_id))$map['cd.operator_id'] = $operator_id;
        if(!empty($city_id)){
            $map['cd.city_id'] = $city_id;
            if($province_id==1){
                $map['cd.province_id'] = $province_id;
            }else{
                $map['cd.province_id']=0;
            }
        }else{
            if(!empty($province_id))$map['cd.province_id'] = $province_id;
            if($province_id==1){
                $map['cd.city_id'] = 0;
            }
        }
        if(!empty($discount_type))$map['cd.discount_type'] = $discount_type;

        $join = array(
            C('DB_PREFIX').'channel as c ON cd.channel_id=c.channel_id',
            C('DB_PREFIX').'sys_operator as o ON cd.operator_id=o.operator_id',
            C('DB_PREFIX').'sys_province as ps ON cd.province_id=ps.province_id',
            C('DB_PREFIX').'sys_city as sc ON cd.city_id=sc.city_id',
            C('DB_PREFIX').'sys_user as u ON cd.create_user_id=u.user_id'
        );

        $channeldiscount_list = M('channel_discount_record as cd')->where($map)
            ->join($join,"left")->field("c.channel_name,o.operator_name,ps.province_name,cd.city_id,cd.province_id,sc.city_name,cd.discount_type,cd.discount_before,cd.discount_after,u.user_name,cd.create_date")
            ->order('cd.create_date desc')
            ->limit(3000)
            ->select();
        $data=array();
        foreach($channeldiscount_list as &$v){
            $da=array();
            $da['channel_name']=$v['channel_name'];
            $da['operator_name']=$v['operator_name'];
            $da['province_name']=get_city_province_name($v['city_id'],$v['province_id']);
            $da['city_name']=$v['city_name'];
            $da['discount_type'] = $v['discount_type']==1?"普通折扣":"返利折扣";
            $da['discount_before'] = show_discount_ten($v['discount_before']);
            $da['discount_after'] = show_discount_ten($v['discount_after']);
            $da['user_name']=$v['user_name'];
            $da['create_date']=$v['create_date'];
            array_push($data,$da);
        }

        $headArr=array("通道名称","运营商","省市","折扣类型","操作前折扣数","操作后折扣数","操作人","操作时间");
        $title='通道折扣记录';
        ExportEexcel($title,$headArr,$data);

    }



}