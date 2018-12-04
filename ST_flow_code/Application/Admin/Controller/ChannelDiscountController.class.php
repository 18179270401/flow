<?php

/*
 * ChannelController.class.php
 * 通道折扣操作控制器
 *
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class ChannelDiscountController extends CommonController {

    /*
     * 通道折扣列表
     */
    public function index(){
        D("SysUser")->sessionwriteclose();
        $channel_code = trim(I('channel_code'));
        $channel_name = trim(I('channel_name'));
        $operator_id = I('operator_id');
        $province_id = I('province_id');
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
        
        $join = array(
            C('DB_PREFIX').'channel as c ON cd.channel_id=c.channel_id',
            C('DB_PREFIX').'sys_operator as o ON cd.operator_id=o.operator_id',
            C('DB_PREFIX').'sys_province as ps ON cd.province_id=ps.province_id',
            C('DB_PREFIX').'sys_city as sc ON cd.city_id=sc.city_id'
        );
        //调用分页类
        $count      = M('ChannelDiscount as cd')->where($map)->join($join,"left")->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $channeldiscount_list = M('ChannelDiscount as cd')->where($map)
		->join($join,"left")->field("cd.*,c.channel_code,c.channel_name,o.operator_name,ps.province_name,sc.city_name")
		->order('cd.modify_date desc')
		->limit($Page->firstRow.','.$Page->listRows)
		->select();
        //加载模板
        $this->assign('channeldiscount_list',get_sort_no($channeldiscount_list,$Page->firstRow));  //数据列表
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

    public function show(){
        $discount_id = I("get.discount_id");
        $join = array(
            C('DB_PREFIX').'channel as c ON cd.channel_id=c.channel_id',
            C('DB_PREFIX').'sys_operator as o ON cd.operator_id=o.operator_id',
            C('DB_PREFIX').'sys_province as ps ON cd.province_id=ps.province_id',
            C('DB_PREFIX').'sys_city as sc ON cd.city_id=sc.city_id'
        );
        //调用分页类
        $channeldiscount = M('ChannelDiscount as cd')
        ->where(array('cd.discount_id'=>$discount_id))
        ->join($join,"left")
        ->field("cd.*,c.channel_code,c.channel_name,o.operator_name,ps.province_name,sc.city_name")
        ->find();
        $this->assign("info",$channeldiscount);
        $this->display();
    }

    /*
     * 通道折扣添加模板
     */
    public function add(){
        //读取通道
        $channel = M("Channel")->where(array('status'=>1))->field("channel_id,channel_name")->select();
        //读取运营商
        $sysoperator = M("SysOperator")->select();
        //读取省
        $sysprovince = M("SysProvince")->order("order_num asc")->select();
        $citys=M("SysCity")->select();
        $this->assign("citys",$citys);
        $this->assign("channel",$channel);
        $this->assign("operator",$sysoperator);
        $this->assign("province",$sysprovince);
        $this->display();
    }

    /*
     * 通道折扣添加功能
     */
    public function insert(){
        $msg = '系统错误！';
        $status = 'error';
        if(IS_POST){
            $post = I("post.");
            if($post['channel_id']==""){
                $msg = '请选择通道名称！';
            }elseif($post['operator_id']==""){
                $msg = '请选择运营商！';
            //}elseif(empty($post['province_id']) && empty($post['city_id'])){
                //$msg = '省份或者市至少选择一个！';
            }elseif($post['discount_number']==""){
                $msg = '请输入折扣数！';
            }elseif($post['discount_number'] <= 0 || $post['discount_number'] > 10){
                $msg = '折扣数请输入1-10之间的数值！';
            //}elseif($post['rebate_discoun']==""){
                //$msg = '请输入返利折扣数！';
            //}elseif($post['rebate_discoun'] <= 0 || $post['rebate_discoun'] > 10){
                //$msg = '返利折扣数请输入1-10之间的数值！';
            }else{
                $discount_number = floatval($post['discount_number'] / 10);
                $rebate_discoun = floatval($post['rebate_discoun'] / 10);
                $where['channel_id'] = $post['channel_id'];
                $where['operator_id'] = $post['operator_id'];

                if($post['province_id']=='' && $post['city_id']==''){
                    $channel = M("Channel")->where(array('channel_id'=>$post['channel_id']))->field('province_id,city_id')->find();
                    $where['province_id'] = $post['province_id'] = $channel['province_id'];
                    $where['city_id'] = $post['city_id'] = $channel['city_id'];
                    $sc='省市【'.get_province_name($post["province_id"]).get_city_name($post["city_id"]).'】';
                }else{
                    if($post['city_id']){
                        $where['city_id'] = array('eq',$post['city_id']);
                        $post['province_id'] = $post['province_id']==1 ? 1 : 0;
                        $where['province_id'] = array('eq',$post['province_id']);
                        $sc='省市【'.get_city_name($post["city_id"]).'】';
                    }else{
                        $where['province_id'] = array('eq',$post['province_id']);
                        $post['city_id']=0;
                        $sc='省市【'.get_province_name($post["province_id"]).'】';
                    }
                }

                if(!M("ChannelDiscount")->where($where)->find()){
                    $add['channel_id'] = $post['channel_id'];
                    $add['operator_id'] = $post['operator_id'];
                    $add['province_id'] = $post['province_id'];
                    $add['city_id'] =$post['city_id'];
                    $add['discount_number'] = $discount_number;
                    $add['rebate_discoun'] = $rebate_discoun;
                    $add['create_user_id'] = D('SysUser')->self_id();
                    $add['create_date'] = date("Y-m-d H:i:s",time());
                    $add['modify_user_id'] = D('SysUser')->self_id();
                    $add['modify_date'] = date("Y-m-d H:i:s",time());
                    $cd=M("ChannelDiscount")->add($add);
                    if($cd){
                        $msg = '新增通道折扣成功！';
                        $status = 'success';
                        $n_msg='成功';
                        //将普通折扣记入变动表
                        $this->discount_record($post['channel_id'],$post['operator_id'],$post['province_id'],$post['city_id'],1,1,$discount_number);
                        //将返利折扣记入变动表
                        $this->discount_record($post['channel_id'],$post['operator_id'],$post['province_id'],$post['city_id'],2,0,$rebate_discoun);
                    }else{
                        $msg = '新增通道折扣失败！';
                        $n_msg='失败';
                    }
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$cd.'】，新增通道折扣，通道【'.channel_info($post['channel_id']).'】，运营商【'.get_operator_name($post['operator_id']).'】,'.$sc.',折扣【'.$post['discount_number'].'】折'.$n_msg;
                    $this->sys_log('新增通道折扣',$note);
                }else{
                    $msg = '通道折扣重复,请仔细检查！';
                }
            }
        }
        if(IS_AJAX) $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    
    public function edit(){
        $discount_id = I("get.discount_id");
        $join = array(
            C('DB_PREFIX').'channel as c ON cd.channel_id=c.channel_id',
            C('DB_PREFIX').'sys_operator as o ON cd.operator_id=o.operator_id',
            C('DB_PREFIX').'sys_province as ps ON cd.province_id=ps.province_id',
            C('DB_PREFIX').'sys_city as sc ON cd.city_id=sc.city_id'
        );
        $channeldiscount = M('ChannelDiscount as cd')
        ->where(array('cd.discount_id'=>$discount_id))
        ->join($join,"left")
        ->field("cd.*,c.channel_code,c.channel_name,o.operator_name,ps.province_name,sc.city_name")
        ->find();
        $this->assign("info",$channeldiscount);
        $this->display();
    }

    public function update(){
        $msg = '系统错误！';
        $status = 'error';
        if(IS_POST){
            $post = I("post.");
            if($post['discount_number']==""){
                $msg = '请输入折扣数！';
            }elseif($post['discount_number'] <= 0 || $post['discount_number'] > 10) {
                $msg = '折扣数请输入1-10之间的数值！';
            //}else if($post['rebate_discoun']==""){
                //$msg = '请输入返利折扣数！';
            //}elseif($post['rebate_discoun'] <= 0 || $post['rebate_discoun'] > 10){
                //$msg = '返利折扣数请输入1-10之间的数值！';
            }else{
                $info=M("ChannelDiscount")->where(array('discount_id'=>$post['discount_id']))->find();
                if($info){
                    $edit['discount_number'] = floatval($post['discount_number'] / 10);
                    $edit['rebate_discoun'] = floatval($post['rebate_discoun'] / 10);
                    $edit['modify_user_id'] = D('SysUser')->self_id();
                    $edit['modify_date'] = date("Y-m-d H:i:s",time());
                    $cd=M("ChannelDiscount")->where(array('discount_id'=>$post['discount_id']))->save($edit);
                    if($cd){
                        $msg = '编辑通道折扣成功！';
                        $status = 'success';
                        $n_msg='成功';
                        //将普通折扣记入变动表
                        $this->discount_record($info['channel_id'],$info['operator_id'],$info['province_id'],$info['city_id'],1,$info['discount_number'],sprintf("%1.3f", $edit['discount_number']));
                        //将返利折扣记入变动表
                        $this->discount_record($info['channel_id'],$info['operator_id'],$info['province_id'],$info['city_id'],2,$info['rebate_discoun'],sprintf("%1.3f", $edit['rebate_discoun']));
                    }else{
                        $msg = '编辑通道折扣失败！';
                        $n_msg='失败';
                    }
                    $c_item='';
                    $c_item.=$post['discount_number']*100===$info['discount_number']*100?'':'，折扣数【'.$post['discount_number'].'】折';
                    $fg=!empty($c_item)?'，':'';
                    $c_item.=$post['rebate_discoun']*100===$info['rebate_discoun']*100?'':$fg.'返利折扣数【'.$post['rebate_discoun'].'】折';
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$cd.'】，编辑通道折扣，通道【'.channel_info($info['channel_id']).'】，运营商【'.get_operator_name($info['operator_id']).'】,省份【'.get_province_name($info['province_id']).'】'.$c_item.$n_msg;
                    $this->sys_log('编辑通道折扣',$note);
                }else{
                    $msg = '数据读取失败！';
                }
            }
        }
        if(IS_AJAX) $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }
    
    public function delete(){
        $msg = '系统错误！';
        $status = 'error';
        if(IS_POST && IS_AJAX){
            $id = I('post.id');
            if(!empty($id)){
                $info=M("ChannelDiscount")->where(array('discount_id'=>$id))->find();
                if($info){
                    $cd=M("ChannelDiscount")->where(array('discount_id'=>$id))->delete();
                    if($cd){
                        $status = 'success';
                        $msg = '删除通道折扣成功！';
                        $n_msg='成功';
                        //将普通折扣记入变动表
                        $this->discount_record($info['channel_id'],$info['operator_id'],$info['province_id'],$info['city_id'],1,sprintf("%1.3f", $info['discount_number']),1);
                        //将返利折扣记入变动表
                        $this->discount_record($info['channel_id'],$info['operator_id'],$info['province_id'],$info['city_id'],2,sprintf("%1.3f", $info['rebate_discoun']),0);
                    }else{
                        $msg = '删除通道折扣失败！';
                        $n_msg='失败';
                    }
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$id.'】，删除通道折扣，通道【'.channel_info($info['channel_id']).'】，运营商【'.get_operator_name($info['operator_id']).'】,省份【'.get_province_name($info['province_id']).'】,折扣【'.($info['discount_number']*10).'】折'.$n_msg;
                    $this->sys_log('删除通道折扣',$note);
                }else{
                    $msg = '数据读取失败！';
                }
            }
        }
        if(IS_AJAX)$this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    /**
    *导出EXCEL
    **/
    public function export_excel(){
        $channel_code = trim(I('channel_code'));
        $channel_name = trim(I('channel_name'));
        $operator_id = I('operator_id');
        $province_id = I('province_id');
        $city_id = I('city_id');

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
        
        $join = array(
            C('DB_PREFIX').'channel as c ON cd.channel_id=c.channel_id',
            C('DB_PREFIX').'sys_operator as o ON cd.operator_id=o.operator_id',
            C('DB_PREFIX').'sys_province as ps ON cd.province_id=ps.province_id',
            C('DB_PREFIX').'sys_city as sc ON cd.city_id=sc.city_id'
        );
        
        $channeldiscount_list = M('ChannelDiscount as cd')->where($map)
        ->join($join,"left")->field("cd.*,c.channel_code,c.channel_name,o.operator_name,ps.province_name,sc.city_name")
        ->order('cd.modify_date desc')
        ->limit(3000)
        ->select();

        $datas = array();
        $headArr=array("通道编码","通道名称","运营商","省份","市","折扣数","返利折扣数");

        foreach ($channeldiscount_list as $v) {
            $data=array();
            $data['channel_code'] = $v['channel_code'];
            $data['channel_name'] = $v['channel_name'];
            $data['operator_name'] = $v['operator_name'];
            $data['province_name'] = get_city_province_name($v['city_id'],$v['province_id']);
            $data['city_name']=$v['city_name'];
            $data['discount_number'] = show_discount_ten($v['discount_number']) . '折';
            $data['rebate_discoun'] = show_discount_ten($v['rebate_discoun']) . '折';
            array_push($datas,$data);
        }
            
        $title='通道折扣';

        ExportEexcel($title,$headArr,$datas);
    }

    /**
     * 将变动折扣加入变动记录表里
     * @$channel_id         =>通道ID
     * @$operator_id        =>所属运营商
     * @$province_id        =>省ID
     * @$discount_type      =>类型（1:普通折扣、2：返利折扣）
     * @$discount_before    =>操作前折扣
     * @$discount_after     =>操作后折扣
     */
    private function discount_record($channel_id,$operator_id,$province_id,$city_id,$discount_type,$discount_before,$discount_after){
        if($discount_type==1) {
            //操作前折扣
            $discount_before = $discount_before > 0 ? $discount_before : 1;
            //操作后折扣
            $discount_after = $discount_after > 0 ? $discount_after : 1;
        }
        //折扣相等不做记录
        if($discount_before-$discount_after!=0){
            $add = array(
                'channel_id'=>$channel_id,
                'operator_id'=>$operator_id,
                'province_id'=>$province_id,
                'city_id'=>$city_id,
                'discount_type'=>$discount_type,
                'discount_before'=>$discount_before,
                'discount_after'=>$discount_after,
                'create_user_id'=>D('SysUser')->self_id(),
                'create_date'=>date("Y-m-d H:i:s",time())
            );
            M("channel_discount_record")->add($add);
        }
        return true;
    }
    

}