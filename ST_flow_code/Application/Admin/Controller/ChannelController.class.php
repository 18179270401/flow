<?php

/*
 * ChannelController.class.php
 * 通道操作控制器
 *
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class ChannelController extends CommonController {

    /*
     * 通道列表
     */
    public function index(){
        D("SysUser")->sessionwriteclose();
        $channel_code = trim(I('channel_code'));
        $channel_name = trim(I('channel_name'));
        $data_status = I('status');
        if(!empty($channel_code))$map['c.channel_code'] = array("like","%{$channel_code}%");
        if(!empty($channel_name))$map['c.channel_name'] = array("like","%{$channel_name}%");
        //列表出状态和全部
        if($data_status == 9){
            $map['c.status'] = array('neq',2);
        }else{
            $map['c.status'] = $data_status=== '0' ? $data_status : 1;
        }

        $join = array(
            "left join ".C('DB_PREFIX').'sys_province as ps ON c.province_id=ps.province_id',
            "left join ".C('DB_PREFIX').'sys_city as sc on c.city_id = sc.city_id'
        );
        //调用分页类
        $count      = M('Channel as c')->join($join)->where($map)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        //获取所有通道列表
        $channel_list = M('Channel as c')->join($join)->where($map)->order("c.modify_date desc")->field("c.*,ps.province_name,sc.city_name")->limit($Page->firstRow.','.$Page->listRows)->select();
        //加载模板
        $this->assign('channel_list',get_sort_no($channel_list,$Page->firstRow));  //数据列表
        $this->assign('page',$show);            //分页
        $this->display('index');         //模板
    }

    /**
     * 导出excel
     */
    public function export_excel() {
        //$map['c.platform_id'] = 1;
        $channel_code = I('channel_code');
        $channel_name = I('channel_name');
        $data_status = I('status');
        if(!empty($channel_code))$map['channel_code'] = array("like","%{$channel_code}%");
        if(!empty($channel_name))$map['channel_name'] = array("like","%{$channel_name}%");
        //列表出状态和全部
        if($data_status == 9){
            $map['status'] = array('neq',2);
        }else{
            $map['status'] = $data_status=== '0' ? $data_status : 1;
        }

        //获取所有通道列表
        $join = array(
            "left join ".C('DB_PREFIX').'sys_province as ps ON c.province_id=ps.province_id',
            "left join ".C('DB_PREFIX').'sys_city as sc ON c.city_id=sc.city_id'
        );
        $list = M('Channel as c')->where($map)
            ->join($join)
            ->field("c.*,ps.province_name,sc.city_name")
            ->order("c.modify_date desc")
            ->limit(3000)
            ->select();
        $title='通道配置';
        $data=array();
        $da=array();
        foreach ($list as $v) {
            $da['channel_code']=$v['channel_code'];
            $da['channel_name']=$v['channel_name'];
            $da['channel_file_name']=$v['channel_file_name'];
            $da['province_name']=get_city_province_name($v['city_id'],$v['province_id']);
            $da['city_name']=$v['city_name'];
            array_push($data,$da);
        }
        $headArr=array("通道编码","通道名称","通道文件名称","省份","市");
        ExportEexcel($title,$headArr,$data);
    }

    /*
     * 通道添加模板
     */
    public function add(){
        //读取省份
        $province = D("ChannelProduct")->provinceall();
        $this->assign("province",$province);
        $citys=M("sys_city")->where()->select();
        $this->assign("citys",$citys);
        $this->assign("attribute_lists",get_attribute_lists());
        $this->display('add');
    }

    /*
     * 通道添加功能
     */
    public function insert(){
        $msg = '系统错误！';
        $status = 'error';
        if(IS_POST){
            $channel_name = I('post.channel_name');       //通道名称
            $channel_code = I('post.channel_code');       //通道编码
            $province_id = I('post.province_id');
            $city_id  = I('post.city_id');
            $channel_file_name=I('post.channel_file_name');
            $discount_mobile=I('post.discount_mobile');
            $discount_unicom=I('post.discount_unicom');
            $discount_telecom=I('post.discount_telecom');

            if(!empty($city_id)){
                if($province_id!=1){
                    $province_id=0;
                }
            }else{
                $city_id=0;
            }
            if($discount_mobile <= 0 || $discount_mobile > 10) {
                $this->ajaxReturn(array('msg'=>'请填写正确的移动折扣，数值要处于0到10之间！', 'status'=>$status));
            }
            if($discount_unicom <= 0 || $discount_unicom > 10) {
                $this->ajaxReturn(array('msg'=>'请填写正确的联通折扣，数值要处于0到10之间！', 'status'=>$status));
            }
            if($discount_telecom <= 0 || $discount_telecom > 10) {
                $this->ajaxReturn(array('msg'=>'请填写正确的电信折扣，数值要处于0到10之间！', 'status'=>$status));
            }
            if(!($province_id || $city_id)){
                $this->ajaxReturn(array('msg'=>'省份或者市至少选择一个！', 'status'=>$status));
            }

            if(!empty($channel_name)){
                if(!empty($channel_file_name)){
                    if(!empty($channel_code)){
                        //$map['channel_name'] = array('eq',$channel_name);
                        $map['channel_code'] = array('eq',$channel_code);
                        $map['province_id'] = array('eq',$province_id);

                        $channelinfo = D('Channel')->where($map)->find();
                        if(!$channelinfo){

                            $model=M('Channel');
                            $model->startTrans();
                            $add = array(
                                'platform_id'  => $platform_id,
                                'channel_name'  => $channel_name,
                                'channel_code'  => $channel_code,
                                'channel_file_name' => $channel_file_name,
                                'discount_unicom' => round($discount_unicom / 10.00, 3),
                                'discount_mobile' => round($discount_mobile / 10.00, 3),
                                'discount_telecom' => round($discount_telecom / 10.00, 3),
                                'province_id'   => $province_id,
                                'city_id'       => $city_id,
                                'create_user_id'  => D('SysUser')->self_id(),
                                'create_date'  => date("Y-m-d H:i:s",time()),
                                'modify_user_id'  => D('SysUser')->self_id(),
                                'modify_date'  => date("Y-m-d H:i:s",time()),
                            );
                            $id = $model->add($add);
                            if($id){
                                M()->execute("CALL p_monitor_init_channel_stat(".$id.",@p_out_flag);");
                                $flag = M()->query("SELECT @p_out_flag;");
                                if($flag[0]['@p_out_flag']==1){
                                    $model->commit();
                                    $msg = '新增通道成功！';
                                    $status = 'success';
                                    $n_msg='成功';
                                }else{
                                    $model->rollback();
                                    $msg = '新增通道失败!';
                                    $n_msg='失败';
                                }
                            }else{
                                $model->rollback();
                                $msg = '新增通道失败!';
                                $n_msg='失败';
                            }
                            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【' . $id . '】，新增通道【'.$channel_name.'('.$channel_code.')'.'】'.$n_msg;
                            $this->sys_log('新增通道',$note);
                        }else{
                            $msg = '通道编码重复,请仔细检查！';
                        }
                    }else{
                        $msg = '通道编码不能为空';
                    }
                }else{
                    $msg="通道文件名称不能为空";
                }
            }else{
                $msg = '通道名称不能为空';
            }
        }
        if(IS_AJAX) $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }


    public function edit(){
        $info = D('Channel')->channelinfo(I('get.channel_id',0,'int'));
        //当菜单不存在时
        if($info){
            //读取省份
            if($info['city_id']!=0){
                $city=M("Sys_city")->where(array("city_id"=>$info['city_id']))->find();
                if($info['province_id']!=1){
                    $info['province_id']=$city['province_id'];
                }
                $citys=M("Sys_city")->where(array("province_id"=>$city['province_id']))->select();
                $this->assign("citys",$citys);
            }elseif($info['province_id']!=1){
                $citys=M("Sys_city")->where(array("province_id"=>$info['province_id']))->select();
                $this->assign("citys",$citys);
            }
            $province = D("ChannelProduct")->provinceall();
            $this->assign("province",$province);
            $this->assign('info',$info);
            $this->assign("attribute_lists",get_attribute_lists());
            $this->display('edit');
        }else{
            $this->error('通道不存在！');
        }
    }

    public function update(){
        $msg = '系统错误！';
        $status = 'error';
        if(IS_POST){
            $channel_name = I('post.channel_name');         //通道名称
            $channel_code = I('post.channel_code');         //通道编码
            $channel_file_name=I('post.channel_file_name'); //通道文件名称
            $channel_id = I('post.channel_id');             //通道ID号
            $discount_mobile=I('post.discount_mobile');
            $discount_unicom=I('post.discount_unicom');
            $discount_telecom=I('post.discount_telecom');
            $province_id = I('post.province_id');
            $city_id=I('post.city_id');
            if(empty($city_id)){
                $city_id=0;
            }else{
                if($province_id!=1){
                    $province_id=0;
                }
            }

            if($discount_mobile <= 0 || $discount_mobile > 10) {
                $this->ajaxReturn(array('msg'=>'请填写正确的移动折扣，数值要处于0到10之间！', 'status'=>$status));
            }
            if($discount_unicom <= 0 || $discount_unicom > 10) {
                $this->ajaxReturn(array('msg'=>'请填写正确的联通折扣，数值要处于0到10之间！', 'status'=>$status));
            }
            if($discount_telecom <= 0 || $discount_telecom > 10) {
                $this->ajaxReturn(array('msg'=>'请填写正确的电信折扣，数值要处于0到10之间！', 'status'=>$status));
            }

            if(!($province_id || $city_id)){
                $this->ajaxReturn(array('msg'=>'省份或者市至少选择一个！', 'status'=>$status));
            }

            if(!empty($channel_name)){
                if(!empty($channel_code)){
                    if(!empty($channel_file_name)){
                        $map['channel_code'] = array('eq',$channel_code);
                        $map['channel_id'] = array('neq',$channel_id);
                        $map['province_id'] = array('eq',$province_id);
                        $channelinfo = D('Channel')->where($map)->find();

                        if(!$channelinfo){
                            $edit = array(
                                'channel_name'  => $channel_name,
                                'channel_code'  => $channel_code,
                                'channel_file_name' => $channel_file_name,
                                'discount_unicom' => round($discount_unicom / 10.00, 3),
                                'discount_mobile' => round($discount_mobile / 10.00, 3),
                                'discount_telecom' => round($discount_telecom / 10.00, 3),
                                'province_id'   => $province_id,
                                'city_id'       => $city_id,
                                'modify_user_id'  => D('SysUser')->self_id(),
                                'modify_date'  => date("Y-m-d H:i:s",time()),
                            );
                            if(M('Channel')->where(array('channel_id'=>$channel_id))->save($edit)){
                                $msg = '编辑通道成功！';
                                $status = 'success';
                                $n_msg='成功';
                            }else{
                                $msg = '编辑通道失败!';
                                $n_msg='失败';
                            }
                            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$channel_id.'】编辑通道【'.$channel_name.'('.$channel_code.')】'.$n_msg;
                            $this->sys_log('编辑通道',$note);
                        }else{
                            $msg = '通道编码重复,请仔细检查！';
                        }
                    }else{
                        $msg="通道文件名称不能为空";
                    }
                }else{
                    $msg = '通道编码不能为空';
                }
            }else{
                $msg = '通道名称不能为空';
            }
        }
        if(IS_AJAX) $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    public function show(){
        $info = D('Channel')->channelinfo(I('get.channel_id',0,'int'));
        //读取省ID
        if($info['city_id']!=0){
            $splist = M('SysCity')->where(array('city_id'=>$info['city_id']))->find();
            $info['city_name'] = $splist['city_name'];
        }else{
            $splist = M('SysProvince')->where(array('province_id'=>$info['province_id']))->find();
            $info['province_name'] = $splist['province_name'];
        }

        //当菜单不存在时
        if($info){
            $this->assign($info);
            $this->display('show');
        }else{
            $this->error('通道不存在！');
        }
    }

    function toggle_status(){
        $msg = '系统错误！';
        $status = 'error';

        if(IS_POST && IS_AJAX){
            $channel_id = I('post.channel_id',0,'int');
            if(!empty($channel_id)){
                $channelinfo = D('Channel')->channelinfo($channel_id);
                if($channelinfo){
                    $status = $channelinfo['status'] == 1 ? "0" : "1";
                    $edit = array(
                        'status'=> $status
                    );
                    $edit = M('Channel')->where(array( 'channel_id'=>$channel_id))->save($edit);
                    $status_name = $status == 1 ? "启用" : "禁用";
                    if($edit){
                        $status = 'success';
                        $msg = "通道".$status_name.'成功!';
                        $n_msg='成功';
                    }else{
                        $msg = "通道".$status_name.'失败!';
                        $n_msg='失败';
                    }
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【' . $channel_id . '】，'.$status_name.'通道【'.$channelinfo['channel_name'].'('.$channelinfo['channel_code'].')'.'】'.$n_msg;
                    $this->sys_log($status_name.'通道',$note);
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

    //启用禁用缓存，
    function toggle_cache(){
        $msg = '系统错误！';
        $status = 'error';

        if(IS_POST && IS_AJAX){
            $channel_id = I('post.channel_id',0,'int');
            if(!empty($channel_id)){
                $channelinfo = D('Channel')->channelinfo($channel_id);
                if($channelinfo){
                    $is_cache = $channelinfo['is_cache'] == 1 ? "0" : "1";
                    $edit = array(
                        'is_cache'=> $is_cache
                    );
                    $edit = M('Channel')->where(array( 'channel_id'=>$channel_id))->save($edit);
                    $status_name = $channelinfo['is_cache'] == 1 ? "启用缓存" : "禁用缓存";
                    if($edit){
                        $status = 'success';
                        $msg = "通道".$status_name.'功能成功!';
                        $n_msg='成功';
                    }else{
                        $msg = "通道".$status_name.'功能失败!';
                        $n_msg='失败';
                    }
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【' . $channel_id . '】，'.$status_name.'通道【'.$channelinfo['channel_name'].'('.$channelinfo['channel_code'].')'.'】'.$n_msg;
                    $this->sys_log($status_name.'通道',$note);
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

    public function toggle_message(){
        $msg = '系统错误！';
        $status = 'error';

        if(IS_POST && IS_AJAX){
            $channel_id = I('post.channel_id',0,'int');
            if(!empty($channel_id)){
                $channelinfo = D('Channel')->channelinfo($channel_id);
                if($channelinfo){
                    $is_message = $channelinfo['is_message'] == 1 ? "0" : "1";
                    $edit = array(
                        'is_message'=> $is_message
                    );
                    $edit = M('Channel')->where(array( 'channel_id'=>$channel_id))->save($edit);
                    $status_name = $channelinfo['is_message'] == 1 ? "禁用短信" : "启用短信";
                    if($edit){
                        $status = 'success';
                        $msg = "通道".$status_name.'功能成功!';
                        $n_msg='成功';
                    }else{
                        $msg = "通道".$status_name.'功能失败!';
                        $n_msg='失败';
                    }
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【' . $channel_id . '】，通道【'.$channelinfo['channel_name'].'('.$channelinfo['channel_code'].')'.'】'.$status_name.$n_msg;
                    $this->sys_log('通道'.$status_name,$note);
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

    //绑定通道账号
    public function account(){
        $type = I("type");
        if($type=="page"){
            //读取通道信息
            $channel_id = I("channel_id");
            $join = array("left join ".C('DB_PREFIX').'sys_province as ps ON c.province_id=ps.province_id',
                "left join ".C('DB_PREFIX').'sys_city as sc on c.city_id = sc.city_id',);
            $channel_list = M('Channel as c')->join($join)->where(array('c.channel_id'=>$channel_id))->field("c.*,ps.province_name,sc.city_name")->find();
            if($channel_list){
                //读取所有账户信息
                $account = M("channel_account")->field("account_id,account_name")->select();
                $this->assign("list",$channel_list);
                $this->assign("account",$account);
                $this->display();
            }else{
                $this->error('通道不存在！');
            }
        }else{
            $msg = '系统错误！';
            $status = 'error';
            $post = I("post.");
            $channel_id = $post['channel_id'];
            //if($post['account_id']==""){
            //$msg = '请选择通道账户名称！';
            //}else{
            $info=M("channel")->where(array('channel_id'=>$channel_id))->find();
            if($info){
                $edit['modify_user_id'] = D('SysUser')->self_id();
                $edit['modify_date'] = date("Y-m-d H:i:s",time());
                $edit['account_id'] = $post['account_id'];
                if(M("channel")->where(array('channel_id'=>$channel_id))->save($edit)){
                    $msg = '通道账户绑定成功！';
                    if($post['account_id']==""){
                        $msg = '通道账户解除成功！';
                    }
                    $status = 'success';
                    $n_msg='成功';
                }else{
                    $msg = '通道账户绑定失败！';
                    if($post['account_id']==""){
                        $msg = '通道账户解除失败！';
                    }
                    $n_msg='失败';
                }
                $status_name=$post['account_id']=="" ? "通道账户解除" : "通道账户绑定";
                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【' . $channel_id . '】，'.$status_name.'【'.$info['channel_name'].'('.$info['channel_code'].')'.'】'.$n_msg;
                $this->sys_log($status_name,$note);
            }else{
                $msg = '通道信息读取失败！';
            }
            //}
            if(IS_AJAX)$this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }


    public function set_threshold(){
        $type = I("type");
        if($type=="page"){
            //读取通道信息
            $channel_id = I("channel_id");
            $join = array("left join ".C('DB_PREFIX').'sys_province as ps ON c.province_id=ps.province_id',
                "left join ".C('DB_PREFIX').'sys_city as sc on c.city_id = sc.city_id',);
            $channel_list = M('Channel as c')->join($join)->where(array('c.channel_id'=>$channel_id))->field("c.*,ps.province_name,sc.city_name")->find();
            if($channel_list){
                //读取所有账户信息
                $account = M("channel_account")->field("account_id,account_name")->select();
                $this->assign("list",$channel_list);
                $this->assign("account",$account);
                $this->display();
            }else{
                $this->error('通道不存在！');
            }
        }else{
            $msg = '系统错误！';
            $status = 'error';
            $post = I("post.");
            $channel_id = $post['channel_id'];
            //if($post['account_id']==""){
            //$msg = '请选择通道账户名称！';
            //}else{
            $info=M("channel")->where(array('channel_id'=>$channel_id))->find();
            if($info){
                $edit['modify_user_id'] = D('SysUser')->self_id();
                $edit['modify_date'] = date("Y-m-d H:i:s",time());

                $fail_threshold = $post['fail_threshold'];
                $block_card_num = $post['block_card_num'];
                if(empty($fail_threshold)){
                    $edit['fail_threshold'] = null;
                }elseif(is_numeric($fail_threshold) && ($fail_threshold < 0 || $fail_threshold > 100) ) {
                    $this->ajaxReturn(array('msg'=>'请填写正确的失败率阈值，数值要处于0到100之间！', 'status'=>$status));
                }elseif(!is_numeric($fail_threshold)){
                    $this->ajaxReturn(array('msg'=>'请填写正确的失败率阈值，必须为数字！', 'status'=>$status));
                }else{
                    $edit['fail_threshold'] = $fail_threshold/100;
                }

                if(empty($block_card_num)){
                    $edit['block_card_num'] = null;
                }elseif(is_numeric($block_card_num) && $block_card_num < 0 ) {
                    $this->ajaxReturn(array('msg'=>'请填写正确的卡单数目阈值！', 'status'=>$status));
                }elseif(!is_numeric($block_card_num)){
                    $this->ajaxReturn(array('msg'=>'请填写正确的卡单数目阈值，必须为数字！', 'status'=>$status));
                }else{
                    $edit['block_card_num'] = $block_card_num;
                }

                if( (empty($block_card_num) && !empty($fail_threshold)) || (!empty($block_card_num) && empty($fail_threshold)) ){
                    $this->ajaxReturn(array('msg'=>'失败率阈值和卡单数目不能只填写其中一个！', 'status'=>$status));
                }

                if(M("channel")->where(array('channel_id'=>$channel_id))->save($edit)){
                    $msg = '通道设置阈值成功！';
                    $status = 'success';
                    $n_msg='成功';
                }else{
                    $msg = '通道设置阈值失败！';
                    $n_msg='失败';
                }
                $status_name = "通道设置阈值";
                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【' . $channel_id . '】，'.$status_name.
                    '【'.$info['channel_name'].'('.$info['channel_code'].')'.'】'.$n_msg.',失败率阈值【'.$fail_threshold.'】，'.
                    '卡单数目阈值【'.$block_card_num.'】';
                $this->sys_log($status_name,$note);
            }else{
                $msg = '通道信息读取失败！';
            }
            //}
            if(IS_AJAX)$this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }
}