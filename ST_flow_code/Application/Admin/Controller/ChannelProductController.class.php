<?php

/*
 * ChannelProductController.class.php
 * 通道产品操作控制器
 *
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class ChannelProductController extends CommonController {

    /**
     * 通道产品列表
     */
    public function index(){
        D("SysUser")->sessionwriteclose();
        $product_name = I('product_name');
        $number = I('number');
        $channel_name = I('channel_name');
        $operator_id = I('operator_id');
        $province_id = I('province_id');
        $data_status = I('status');
        $channel_code = I('channel_code');
        $city_id=I('city_id');
        if(!empty($product_name))$map['cp.product_name'] = array("like","%{$product_name}%");
        if(!empty($number))$map['cp.number'] = array("like","%{$number}%");
        if(!empty($channel_name))$map['c.channel_name'] = array("like","%{$channel_name}%");
        if(!empty($channel_code))$map['c.channel_code'] = $channel_code;
        if(!empty($operator_id))$map['cp.operator_id'] = $operator_id;
        if(!empty($city_id)){
            $map['cp.city_id'] = $city_id;
            if($province_id==1){
                $map['cp.province_id'] = $province_id;
            }else{
                $map['cd.province_id']=0;
            }
        }else{
            if(!empty($province_id))$map['cp.province_id'] = $province_id;
            if($province_id==1){
                $map['cp.city_id'] = 0;
            }
        }
        //列表出状态和全部
        if($data_status == 9){
            $map['cp.status'] = array('neq',2);
        }else{
            $map['cp.status'] = $data_status=== '0' ? $data_status : 1;
        }
        //联表关系
        $join = array(
            C('DB_PREFIX').'channel as c ON cp.channel_id=c.channel_id',
            C('DB_PREFIX').'sys_operator as o ON cp.operator_id=o.operator_id',
            C('DB_PREFIX').'sys_province as ps ON cp.province_id=ps.province_id',
            C('DB_PREFIX').'sys_city as sc ON cp.city_id=sc.city_id'
            );
        //调用分页类
        $count      = M('ChannelProduct as cp')->where($map)->join($join,"left")->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        //获取所有角色列表
        $channelproduct_list = M('ChannelProduct as cp')->where($map)
        						->join($join,"left")->field("cp.*,c.channel_code,c.channel_name,o.operator_name,ps.province_name,sc.city_name")
        						->order('cp.channel_id asc,cp.operator_id asc,cp.size asc')
        						->limit($Page->firstRow.','.$Page->listRows)
        						->select();
        //加载模板
        $this->assign('channelproduct_list',get_sort_no($channelproduct_list,$Page->firstRow));  //数据列表
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
        //$channel = D("Channel")->channelall();
        $channel = D('Channel')->select();
        $this->assign('operator',$operator);
        $this->assign('province',$province);
        $this->assign('channel',$channel);
        
        $this->display('index');         //模板
    }

    /**
     * 导出excel
     */
    public function export_excel() {
        $product_name = I('product_name');
        $number = I('number');
        $channel_name = I('channel_name');
        $operator_id = I('operator_id');
        $province_id = I('province_id');
        $data_status = I('status');
        $channel_code = I('channel_code');
        $city_id=I('city_id');
        if(!empty($product_name))$map['cp.product_name'] = array("like","%{$product_name}%");
        if(!empty($number))$map['cp.number'] = array("like","%{$number}%");
        if(!empty($channel_name))$map['c.channel_name'] = array("like","%{$channel_name}%");
        if(!empty($channel_code))$map['c.channel_code'] = $channel_code;
        if(!empty($operator_id))$map['cp.operator_id'] = $operator_id;
        if(!empty($city_id)){
            $map['cp.city_id'] = $city_id;
            if($province_id==1){
                $map['cp.province_id'] = $province_id;
            }else{
                $map['cd.province_id']=0;
            }
        }else{
            if(!empty($province_id))$map['cp.province_id'] = $province_id;
            if($province_id==1){
                $map['cp.city_id'] = 0;
            }
        }
        //列表出状态和全部
        if($data_status == 9){
            $map['cp.status'] = array('neq',2);
        }else{
            $map['cp.status'] = $data_status=== '0' ? $data_status : 1;
        }
        //联表关系
        $join = array(
            C('DB_PREFIX').'channel as c ON cp.channel_id=c.channel_id',
            C('DB_PREFIX').'sys_operator as o ON cp.operator_id=o.operator_id',
            C('DB_PREFIX').'sys_province as ps ON cp.province_id=ps.province_id',
            C('DB_PREFIX').'sys_city as sc ON cp.city_id=sc.city_id'
        );
        //获取所有角色列表
        $channelproduct_list = M('ChannelProduct as cp')
            ->where($map)
            ->join($join,"left")
            ->order('cp.channel_id asc,cp.operator_id asc,cp.size asc')
            ->field("cp.*,c.channel_code,c.channel_name,o.operator_name,ps.province_name,sc.city_name")
            ->limit(3000)
            ->select();
        //加载模板
        $title='通道产品';
        $list=array();
        //$headArr=array("产品名称","产品编号","通道编码","通道名称","流量包大小(M)","运营商","省份","产品类型");
        $headArr=array("产品名称","产品编号","标准价格(元)","通道编码","通道名称","流量包大小(M)","运营商","省份","市");
        foreach($channelproduct_list as $k=>$v){
                $list[$k]['product_name'] =$v['product_name'];
                $list[$k]['number'] =$v['number'];
                $list[$k]['price']=$v['price'];
                $list[$k]['channel_code'] =$v['channel_code'];
                $list[$k]['channel_name'] =$v['channel_name'];
                $list[$k]['size'] =$v['size'];
                $list[$k]['operator_name'] =$v['operator_name'];
                $list[$k]['province_name'] = get_city_province_name($v['city_id'],$v['province_id']);
                $list[$k]['city_name'] =$v['city_name'];
            /*if($v['product_type']==0){
                $list[$k]['product_type'] ='全国流量';
            }else{
                $list[$k]['product_type'] ='省流量';
            }*/
        }
        ExportEexcel($title,$headArr,$list);

    }



    /**
     * 通道产品添加模板
     */
    public function add(){
        //读取运营商
        $operator = D("ChannelProduct")->operatorall();
        //读取省份
        $province = D("ChannelProduct")->provinceall();
        //读取通道
        $channel = D("Channel")->channelall();
        $citys=M("sys_city")->where()->select();
        $this->assign("citys",$citys);
        $this->assign('operator',$operator);
        $this->assign('province',$province);
        $this->assign('channel',$channel);
        $this->display('add');
    }

    /**
     * 通道添加功能
     */
    public function insert(){
        $msg = '系统错误！';
        $status = 'error';
        if(IS_POST){
            $post = I('post.');
            if(!empty($post['channel_id'])){
                if(!empty($post['product_name'])){
                    if(!empty($post['number'])){
                        if(!empty($post['size'])){
                            if(!empty($post['price'])){
                                if(!empty($post['province_id'])|| !empty($post['city_id'])) {
                                    $map['channel_id'] = array('eq', $post['channel_id']);
                                    $map['operator_id'] = array('eq', $post['operator_id']);
                                    $map['size'] = array('eq', $post['size']);
                                    if ($post['city_id']) {
                                        $map['city_id'] = array('eq', $post['city_id']);
                                        if ($post['province_id'] != 1) {
                                            $post['province_id'] = 0;
                                        } else {
                                            $map['province_id'] = array('eq', $post['province_id']);
                                        }
                                        $sc = '省市【' . get_city_name($post["city_id"]) . '】';
                                    } else {
                                        $map['province_id'] = array('eq', $post['province_id']);
                                        $post['city'] = 0;
                                        $sc = '省市【' . get_province_name($post["province_id"]) . '】';
                                    }
                                    $channelproductinfo = D('ChannelProduct')->where($map)->find();
                                    if (!$channelproductinfo) {
                                        $post['product_type'] = I('product_type');
                                        $post['create_user_id'] = D('SysUser')->self_id();
                                        $post['create_date'] = date("Y-m-d H:i:s", time());
                                        $post['modify_user_id'] = D('SysUser')->self_id();
                                        $post['modify_date'] = date("Y-m-d H:i:s", time());
                                        $add_id = M('ChannelProduct')->add($post);
                                        if ($add_id) {
                                            $msg = '新增通道产品成功！';
                                            $status = 'success';
                                            $n_msg = '成功';
                                        } else {
                                            $msg = '新增通道产品失败!';
                                            $n_msg = '失败';
                                        }
                                        $product_type = $post['product_type'] == 1 ? '全国全国流量' : '省全国流量';
                                        $note = '用户【' . get_user_name(D('SysUser')->self_id()) . '】，ID【' . $add_id . '】，新增通道产品，通道名称【' . channel_info($post['channel_id']) . '】，产品名称【' . $post['product_name'] . '】，产品编号【' . $post['number'] . '】，流量大小【' . $post['size'] . '】，标准价格【' . money_format2($post['price']) . '】元，运营商【' . get_operator_name($post['operator_id']) . '】,' . $sc . '，产品类型【' . $product_type . '】' . $n_msg;
                                        $this->sys_log('新增通道产品', $note);

                                    } else {
                                        $msg = '通道产品重复,请仔细检查！';
                                    }
                                   
                                }else{
                                    $msg = '省份或者市至少选择一个！';
                                }
                            }else{
                                $msg = '请输入产品售价！';
                            }
                        }else{
                            $msg = '请输入流量包大小！';
                        }
                    }else{
                        $msg = '请输入产品编号！';
                    }
                }else{
                    $msg = '请输入产品名称！';
                }
            }else{
                $msg = '请选择所属通道！';
            }
        }
        if(IS_AJAX) $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }
    
    /**
 * 修改通道产品信息
 */
    public function edit(){
        $info = D('ChannelProduct')->channelproductinfo(I('get.product_id',0,'int'));
        //当菜单不存在时
        if($info){
            if($info['city_id']!=0 && $info['province_id']!=1){
                $city=M("Sys_city")->where(array("city_id"=>$info['city_id']))->find();
                $info['province_id']=$city['province_id'];
                $citys=M("Sys_city")->where(array("province_id"=>$city['province_id']))->select();
                $this->assign("citys",$citys);
            }elseif($info['province_id']!=1){
                $citys=M("Sys_city")->where(array("province_id"=>$info['province_id']))->select();
                $this->assign("citys",$citys);
            }elseif($info['province_id']==1){
                $citys=M("Sys_city")->where()->select();
                $this->assign("citys",$citys);
            }
           
            $this->assign('info',$info);
            //读取运营商
            $operator = D("ChannelProduct")->operatorall();
            //读取省份
            $province = D("ChannelProduct")->provinceall();
            //读取通道
            //$channel = D("Channel")->channelall();
            $self_channel=D("Channel")->channelinfo($info['channel_id']);
            $this->assign('operator',$operator);
            $this->assign('province',$province);
            //$this->assign('channel',$channel);
            $this->assign('self_channel',$self_channel);
            $this->assign('info',$info);
            $this->display('edit');
        }else{
            $this->error('通道产品不存在！');
        }
    }


    /**
     * 修改通道产品信息
     */
    public function show(){
        $where['cp.product_id']=I('get.product_id');
        $info = D('ChannelProduct')->show($where);
        //当菜单不存在时
        if($info){
            $this->assign($info);
            $this->display('show');
        }else{
            $this->error('通道产品不存在！');
        }
    }

    public function update(){
        $msg = '系统错误！';
        $status = 'error';
        if(IS_POST){
            $post = I('post.');
            if(!empty($post['channel_id'])){
                if(!empty($post['product_name'])){
                    if(!empty($post['number'])){
                        if(!empty($post['size'])){
                            if(!empty($post['price'])){
                               
                                if(empty($post['province_id'])&& empty($post['city_id'])){
                                    $this->ajaxReturn(array('msg'=>'省份或者市至少选择一个！','status'=>'error'));
                                }
                                    $map['channel_id'] = array('eq',$post['channel_id']);
                                    $map['number'] = array('eq',$post['number']);
                                    $map['operator_id'] = array('eq',$post['operator_id']);
                                    $map['product_id'] = array('neq',$post['product_id']);
                                    if($post['city_id']){
                                        $map['city_id'] = array('eq',$post['city_id']);
                                        if($post['province_id']!=1){
                                            $post['province_id']=0;
                                        }else{
                                            $map['province_id'] = array('eq',$post['province_id']);
                                        }
                                    }else{
                                        $map['province_id'] = array('eq',$post['province_id']);
                                        $post['city']=0;
                                    }
                                    $channelproductinfo = D('ChannelProduct')->where($map)->find();
                                    if(!$channelproductinfo){
                                        $post['modify_user_id'] = D('SysUser')->self_id();
                                        $post['modify_date'] =date("Y-m-d H:i:s",time());
                                        if(M('ChannelProduct')->save($post)){
                                            $msg = '编辑通道产品成功！';
                                            $status = 'success';
                                            $n_msg='成功';
                                        }else{
                                            $msg = '编辑通道产品失败!';
                                            $n_msg='失败';
                                        }
                                        $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$post['product_id'].'】，编辑通道【'.channel_info($post['channel_id']).'】产品【'.$post['product_name'].'】'.$n_msg;
                                        $this->sys_log('编辑通道产品',$note);
                                    }else{
                                        $msg = '通道产品重复,请仔细检查！';
                                    }

                            }else{
                                $msg = '请输入产品售价！';
                            }
                        }else{
                            $msg = '请输入流量包大小！';
                        }
                    }else{
                        $msg = '请输入产品编号！';
                    }
                }else{
                    $msg = '请输入产品名称！';
                }
            }else{
                $msg = '请选择所属通道！';
            }
        }
        if(IS_AJAX) $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }
    
    /**
     * 删除通道产品
     */
    public function delete(){
        $msg = '系统错误！';
        $status = 'error';
        
        if(IS_POST && IS_AJAX){
            $product_id = I('post.product_id',0,'int');
            if(!empty($product_id)){
                $channelproductinfo = D('ChannelProduct')->channelproductinfo($product_id);
                if($channelproductinfo){
                    //将需要删除的节点修改为已删除状态
                    $edit = array(
                        'product_id'=>$product_id,
                        'status'=> 2,
                        );
                    $edit = M('ChannelProduct')->save($edit);
                    if($edit){
                        $status = 'success';
                        //$msg = '删除通道产品【'.$channelproductinfo['product_name'].'】成功！';
						$msg = '删除通道产品成功！';
                        $n_msg='成功';
                    }else{
                        $msg = '删除通道产品失败!';
                        $n_msg='失败';
                    }
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$product_id.'】，删除通道【'.channel_info($channelproductinfo['channel_id']).'】产品【'.$channelproductinfo['product_name'].'】'.$n_msg;
                    $this->sys_log('删除通道产品',$note);
                }
            }
        }
        if(IS_AJAX){
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }
    /**
     * 修改通道产品状态
     */
    public function toggle_status(){
        $msg = '系统错误！';
        $status = 'error';
        
        if(IS_POST && IS_AJAX){
            $product_id = I('post.product_id',0,'int');
            if(!empty($product_id)){
                $channelproductinfo = D('ChannelProduct')->channelproductinfo($product_id);
                if($channelproductinfo){
                    $status = $channelproductinfo['status'] == 1 ? "0" : "1";
                    $edit = array(
                        'product_id'=>$product_id,
                        'status'=> $status,
                    );
                    $edit = M('ChannelProduct')->save($edit);
                    $status_name = $status == 1 ? "启用" : "禁用";
                    if($edit){
                        $status = 'success';
                        $msg = "通道产品".$status_name.'成功!';
                        $n_msg='成功';
                    }else{
                        $msg = "通道产品".$status_name.'失败!';
                        $n_msg='失败';
                    }
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$product_id.'】，'.$status_name.'通道【'.channel_info($channelproductinfo['channel_id']).'】产品【'.$channelproductinfo['product_name'].'】'.$n_msg;
                    $this->sys_log($status_name.'通道产品',$note);
                }else{
                    $msg = '数据读取失败!';
                }
            }else{
                $msg = '传入ID错误!';
            }
        }
        if(IS_AJAX){
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }

}