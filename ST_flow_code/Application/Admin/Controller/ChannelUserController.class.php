<?php

/*
 * ChannelUserController.class.php
 * 通道操作控制器
 *
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class ChannelUserController extends CommonController {

    /*
     * 通道列表
     */
    public function index(){
        D("SysUser")->sessionwriteclose();
        $name = I('name');
        $code = I('code');
        $channel_name = I('channel_name');
        $channel_code = I('channel_code');
        
        $map_1['p.proxy_code'] = array("like","%".$code."%");
        $map_1['e.enterprise_code'] = array("like","%".$code."%");
        $map_1['_logic'] = 'or';
        $map[] = $map_1;
        
        $map_2['p.proxy_name'] = array("like","%".$name."%");
        $map_2['e.enterprise_name'] = array("like","%".$name."%");
        $map_2['_logic'] = 'or';
        $map[] = $map_2;
        
        if(!empty($channel_name))$map['c.channel_name'] = array("like","%".$channel_name."%");

        if(!empty($channel_code))$map['c.channel_code'] = array("like","%".$channel_code."%");

        //调用分页类
        $count      = M('ChannelUser as cu')
        ->join("LEFT JOIN ".C('DB_PREFIX').'channel as c on c.channel_id = cu.channel_id')
        ->join("LEFT JOIN ".C('DB_PREFIX').'proxy as p on p.proxy_id = cu.proxy_id')
        ->join("LEFT JOIN ".C('DB_PREFIX').'enterprise as e on e.enterprise_id = cu.enterprise_id')
        ->where($map)
        ->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        //获取所有通道列表
        $channel_user = M('ChannelUser as cu')
        ->join("LEFT JOIN ".C('DB_PREFIX').'channel as c on c.channel_id = cu.channel_id')
        ->join("LEFT JOIN ".C('DB_PREFIX').'proxy as p on p.proxy_id = cu.proxy_id')
        ->join("LEFT JOIN ".C('DB_PREFIX').'enterprise as e on e.enterprise_id = cu.enterprise_id')
        ->where($map)
        ->order("cu.create_date desc")
        ->field("cu.*,c.channel_name,c.channel_code,p.proxy_name,p.proxy_code,e.enterprise_name,e.enterprise_code")
        ->limit($Page->firstRow.','.$Page->listRows)
        ->select();
        if($channel_user){
            foreach($channel_user as $k=>$v){
                if($v['user_type']==1){
                    $channel_user[$k]['code'] = obj_data($v['proxy_id'],$v['user_type'],"code");
                    $channel_user[$k]['name'] = obj_data($v['proxy_id'],$v['user_type'],"name");
                }else{
                    $channel_user[$k]['code'] = obj_data($v['enterprise_id'],$v['user_type'],'code');
                    $channel_user[$k]['name'] = obj_data($v['enterprise_id'],$v['user_type'],'name');
                }
            }
        }
        //加载模板
        $this->assign('channel_user',get_sort_no($channel_user,$Page->firstRow));  //数据列表
        $this->assign('page',$show);            //分页
        //读取所有通道 
        //$Channel = M("Channel")->where(array('status'=>1))->field("channel_id,channel_name,channel_code")->select();
       // $this->assign('channel',$Channel);
       // $this->assign('channel_id',$channel_id);
        $this->display('index');         //模板
    }

    /**
     * 导出excel
     */
    public function export_excel() {
        $name = I('name');
        $code = I('code');
        $channel_code = trim(I('channel_code'));
        $channel_name = trim(I('channel_name'));

        $map_1['p.proxy_code'] = array("like","%".$code."%");
        $map_1['e.enterprise_code'] = array("like","%".$code."%");
        $map_1['_logic'] = 'or';
        $map[] = $map_1;

        $map_2['p.proxy_name'] = array("like","%".$name."%");
        $map_2['e.enterprise_name'] = array("like","%".$name."%");
        $map_2['_logic'] = 'or';
        $map[] = $map_2;

        if(!empty($channel_name))$map['c.channel_name'] = array("like","%".$channel_name."%");

        if(!empty($channel_code))$map['c.channel_code'] = array("like","%".$channel_code."%");

        //获取所有通道列表
        $channel_user = M('ChannelUser as cu')
            ->join("LEFT JOIN ".C('DB_PREFIX').'channel as c on c.channel_id = cu.channel_id')
            ->join("LEFT JOIN ".C('DB_PREFIX').'proxy as p on p.proxy_id = cu.proxy_id')
            ->join("LEFT JOIN ".C('DB_PREFIX').'enterprise as e on e.enterprise_id = cu.enterprise_id')
            ->join("LEFT JOIN ".C('DB_PREFIX').'sys_user as u on u.user_id = cu.modify_user_id')
            ->where($map)
            ->order("cu.create_date desc")
            ->field("cu.*,c.channel_name,c.channel_code,p.proxy_name,p.proxy_code,e.enterprise_name,e.enterprise_code,u.user_name")
            ->limit(3000)
            ->select();
        $title='用户通道管理';
        $list=array();
        $headArr=array("用户编号","用户名称","用户类型","通道编码","通道名称","操作人","操作时间");
        foreach($channel_user as $k=>$v){
            if($v['user_type']==1){
                $list[$k]['code'] = obj_data($v['proxy_id'],$v['user_type'],"code");
                $list[$k]['name'] = obj_data($v['proxy_id'],$v['user_type'],"name");
            }else{
                $list[$k]['code'] = obj_data($v['enterprise_id'],$v['user_type'],'code');
                $list[$k]['name'] = obj_data($v['enterprise_id'],$v['user_type'],'name');
            }
            if($v['user_type']==1){
                $list[$k]['user_type'] ='代理商';
            }else{
                $list[$k]['user_type'] ='企业';
            }
            $list[$k]['channel_code'] =$v['channel_code'];
            $list[$k]['channel_name'] =$v['channel_name'];
            $list[$k]['user_name'] = $v['user_name'];
            $list[$k]['modify_date'] = $v['modify_date'];
        }
        ExportEexcel($title,$headArr,$list);
    }



    /*
     * 通道添加模板
     */
    public function add(){
        //读取所有通道 
        $Channel = M("Channel")->where(array('status'=>1))->field("channel_id,channel_name,channel_code")->select();
        //读取代理商
        $proxy = M("proxy")->where(array('status'=>1,'proxy_level'=>1,'approve_status'=>1))->field("proxy_id,proxy_name")->select();
        if($proxy){
            $id = array();
            foreach($proxy as $v){
                $id[] = $v['proxy_id'];
            }
            $idall = implode(",",$id);
        }else{
            $proxy=NULL;
        }
        //读企业
        if($idall!=""){
            $where['top_proxy_id'] = array("in",$idall);
            $where['status'] = 1;
            $where['approve_status'] = 1;
            $enterprise = M("enterprise")->where($where)->field("enterprise_id,enterprise_name")->select();
            if(!$enterprise){
                $enterprise=NULL;
            }
        }else{
            $enterprise=NULL;
        }
        $this->assign('proxy',$proxy);
        $this->assign('enterprise',$enterprise);
        $this->assign('channel',$Channel);
        $this->display('add');
    }

    /*
     * 通道用户添加功能
     */
    public function insert(){
        $msg = '系统错误！';
        $status = 'error';
        if(IS_POST){
            $user_type = I('post.user_type');
            $id = I('post.id');
            $channel_id=I('post.channel_id');
            if(empty($id) || $id<1){
                $msg="用户名称输入有误！";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                exit();
            }
            if(empty($channel_id) || $channel_id<1){
                $msg="通道名称输入有误！";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                exit();
            }
            $channelinfo = array();
            if($user_type == 1) {
                $channelinfo = D('ChannelUser')->where(array('proxy_id'=>$id,'channel_id'=>$channel_id))->find();
            } else {
                $channelinfo = D('ChannelUser')->where(array('enterprise_id'=>$id,'channel_id'=>$channel_id))->find();
            }
            if(!$channelinfo){
                if(true){
                    $add['user_type'] = $user_type;
                    if($user_type==1){
                        $add['proxy_id'] = $id;
                        $name=obj_name($id,1);
                        $name_type='代理商';
                    }else{
                        $add['enterprise_id'] = $id;
                        $name=obj_name($id,2);
                        $name_type='企业';
                    }
                    $add['channel_id'] = $channel_id;
                    $add['create_user_id'] = D('SysUser')->self_id();
                    $add['create_date'] = date("Y-m-d H:i:s",time());
                    $add['modify_user_id'] = D('SysUser')->self_id();
                    $add['modify_date'] = date("Y-m-d H:i:s",time());
                    $add_id = M('ChannelUser')->add($add);
                    if($add_id){
                        $msg = '新增用户通道成功！';
                        $status = 'success';
                        $n_msg='成功';
                    }else{
                        $msg = '新增用户通道失败!';
                        $n_msg='失败';
                    }
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$add_id.'】，新增用户通道，用户类型【'.$name_type.'】，用户名称【'.$name.'】，通道名称【'.channel_info($channel_id).'】'.$n_msg;
                    $this->sys_log('新增用户通道',$note);
                }else{
                    $msg = '用户通道信息重复,请仔细检查！';
                }
            }else{
                $msg = '用户通道信息重复,请仔细检查！';
            }
        }
        if(IS_AJAX) $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    
    public function edit(){
        $channel_user_id = I('get.channel_user_id');
        $info = D('ChannelUser')->where(array('channel_user_id'=>$channel_user_id))->find();
        //当菜单不存在时
        if($info){
            //读取所有通道 
            $Channel = M("Channel")->where(array('status'=>1))->field("channel_id,channel_name,channel_code")->select();
            if($info['user_type']==1){
                //读取代理商
                $proxy = M("proxy")->where(array('proxy_id'=>$info['proxy_id']))->field("proxy_name,proxy_code")->find();
                $this->assign('proxy',$proxy);
            }else{
                $enterprise = M("enterprise")->where(array('enterprise_id'=>$info['enterprise_id']))->find();
                $this->assign('enterprise',$enterprise);
            }
            $this->assign('channel',$Channel);
            $this->assign('info',$info);
            $this->display('edit');
        }else{
            $this->error('用户通道不存在！');
        }
    }

    public function show(){
        $channel_user_id = I('get.channel_user_id');
        $info = D('ChannelUser')->where(array('channel_user_id'=>$channel_user_id))->find();
        //当菜单不存在时
        if($info){
            //读取所有通道 
            $Channel = M("Channel")->where(array('channel_id'=>$info['channel_id']))->find();
            if($info['user_type']==1){
                //读取代理商
                $proxy = M("proxy")->where(array('proxy_id'=>$info['proxy_id']))->field("proxy_name,proxy_code")->find();
                $this->assign('proxy',$proxy);
            }else{
                $enterprise = M("enterprise")->where(array('enterprise_id'=>$info['enterprise_id']))->field("enterprise_name,enterprise_code")->find();
                $this->assign('enterprise',$enterprise);
            }
            $this->assign('channel',$Channel);
            $this->assign('info',$info);
            $this->assign("info",$info);
            $this->display('show');
        }else{
            $this->error('用户通道不存在！');
        }
    }

    public function update() {
        $msg = '系统错误！';
        $status = 'error';
        if(IS_POST){
            $channel_user_id = I('post.channel_user_id');
            $channel_id = I('post.channel_id');
            if(empty($channel_id) || $channel_id<1){
                $msg="通道名称输入有误！";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                exit();
            }
            if(empty($channel_user_id) || $channel_user_id<1){
                $msg="用户名称输入有误！";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                exit();
            }
            $channel_user_info = M('channel_user')->where("channel_user_id={$channel_user_id}")->find();
            if($channel_user_info) {
                $cond['user_type'] = $channel_user_info['user_type'];
                if($channel_user_info['user_type']==1){
                    $cond['proxy_id'] = $channel_user_info['proxy_id'];
                    $name=obj_name($channel_user_info['proxy_id'],1);
                    $name_type='代理商';
                }else{
                    $cond['enterprise_id'] = $channel_user_info['enterprise_id'];
                    $name=obj_name($channel_user_info['enterprise_id'],2);
                    $name_type='企业';
                }
                $cond['channel_id'] = $channel_id;
                $old = M('channel_user')->where($cond)->find();
                if(!$old) {
                    $edit['channel_id'] = $channel_id;
                    $edit['modify_user_id'] = D('SysUser')->self_id();
                    $edit['modify_date'] = date("Y-m-d H:i:s",time());
                    if(M('ChannelUser')->where(array('channel_user_id'=>$channel_user_id))->save($edit)){
                        $msg = '编辑用户通道成功！';
                        $status = 'success';
                        $n_msg='成功';
                    }else{
                        $msg = '编辑用户通道失败！';
                        $n_msg='失败';
                    }
                    $c_item=$channel_id===$channel_user_info['channel_id']?'没有修改':'，通道名称【'. channel_info($channel_id).'】'.$n_msg;
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$channel_user_id.'】，编辑用户通道，用户类型【'.$name_type.'】，用户名称【'.$name.'】'.$c_item;
                    $this->sys_log('编辑用户通道',$note);
                } else {
                    $msg = '用户通道信息重复,请仔细检查！';
                }

            } else {
                $msg = '用户通道数据错误！';
            }
        }
        if(IS_AJAX) $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    function delete(){

        $msg = '系统错误！';
        $status = 'error';
        
        if(IS_POST && IS_AJAX){
            $channel_user_id = I('post.channel_user_id',0,'int');
            if(!empty($channel_user_id)){
                $info = D('ChannelUser')->where(array('channel_user_id'=>$channel_user_id))->find();
                if($info){
                   $name=$info['user_type']==1?'代理商'.obj_name($info['proxy_id'],1):'企业'.obj_name($info['enterprise_id'],2);
                    if(M('ChannelUser')->where(array('channel_user_id'=>$channel_user_id))->delete()){
                        $status = 'success';
                        $msg = '删除用户通道成功!';
                        $n_msg='成功';
                    }else{
                        $msg = '删除用户通道失败!';
                        $n_msg='失败';
                    }
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$channel_user_id.'】，删除用户【'.$name.'】通道名称【'.channel_info($info['channel_id']).'】'.$n_msg;
                    $this->sys_log('删除用户通道',$note);
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

    /**
     * 通道权限管理
     */
    public function set_channel_info_user(){
        if(IS_POST){
            $original_channel_id = I('post.original_channel_id');
            $user_str = I('post.user_str');
            $channel_str = I('post.channel_str');
            if(!$original_channel_id || !$user_str || !$channel_str){
                $this->ajaxReturn(array('msg'=>'提交信息有误，请认真核查！'));
            }else{
                //添加新通道
                $add = $this->add_some_rights($channel_str,$user_str);
                $user_codes = trim($user_str,',');
                $n_msg="";
                if(!empty($user_codes)){
                    $proxys=M("proxy")->where("proxy_code in ($user_codes)")->field("proxy_name")->select();
                    $enterprises=M("enterprise")->where("enterprise_code in ($user_codes)")->field("enterprise_name")->select();
                    $channels=M("channel")->where("channel_id in ($channel_str)")->field("channel_name")->select();
                    $n_msg.="通道";
                    $channel_old=M("channel")->where("channel_id = $original_channel_id")->find();
                    $n_msg.="【".$channel_old['channel_name']."】";
                    $n_msg.="中用户";
                    foreach ($proxys as $v){
                        $n_msg.="【".$v['proxy_name']."】";
                    }
                    foreach ($enterprises as $v){
                        $n_msg.="【".$v['enterprise_name']."】";
                    }
                    $n_msg.="设置到通道";
                    foreach ($channels as $v){
                        $n_msg.="【".$v['channel_name']."】";
                    }
                }
                $status = 'error';
                $msg = '系统错误';
                if($add){
                    $status = 'success';
                    $msg = '分配成功!';
                    $n_msg.="分配成功";
                }else{
                    $msg = '分配失败!';
                    $n_msg.='分配失败';
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，'.$n_msg;
                $this->sys_log('批量设置通道',$note);
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
        }else{
            $channel_list = $this->get_channel_list();
            $this->assign('channel_list',$channel_list);
            $this->display();
        }
    }

    /**
     * 批量切换用户通道
     */
    public function exchange_channel_batch(){
        if(IS_POST){
            $original_channel_id = I('post.original_channel_id');
            $user_str = I('post.user_str');
            $channel_str = I('post.channel_str');
            if(!$original_channel_id || !$user_str || !$channel_str){
                $this->ajaxReturn(array('msg'=>'提交信息有误，请认真核查！'));
            }else{
                $user_arr = explode(',', $user_str);
                //删除原有通道
                $user_msg=""; //操作日志记录企业和代理商
                $channle_info=M("channel")->where("channel_id=$original_channel_id")->find();
                $user_msg.="通道【".$channle_info['channel_name']."】中的用户";
                foreach($user_arr as $user){
                    if($user_info=M('enterprise')->where(array('enterprise_code'=>$user))->find()){
                        $user_msg.="【".$user_info['enterprise_name']."】";
                        M('channel_user')->where(array('enterprise_id'=>$user_info['enterprise_id'],'channel_id'=>$original_channel_id))->delete();
                    }elseif($user_info=M('proxy')->where(array('proxy_code'=>$user))->find()){
                        $user_msg.="【".$user_info['proxy_name']."】";
                        M('channel_user')->where(array('proxy_id'=>$user_info['proxy_id'],'channel_id'=>$original_channel_id))->delete();
                    }
                }

                //添加新通道
                $add = $this->add_some_rights($channel_str,$user_str);
                $channel_str = trim($channel_str,',');
                $channel_info=M("channel")->where("channel_id in ($channel_str)")->field("channel_name")->select();
                $user_msg.="切换到通道";
                foreach ($channel_info as $v){
                    $user_msg.= "【".$v['channel_name']."】";
                }
                $status = 'error';
                $msg = '系统错误';
                if($add){
                    $status = 'success';
                    $msg = '分配成功!';
                    $user_msg.="分配成功";
                }else{
                    $msg = '分配失败！';
                    $user_msg.="分配失败";
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，'.$user_msg;
                $this->sys_log('批量切换通道',$note);
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
        }else{
            $channel_list = $this->get_channel_list();
            $this->assign('channel_list',$channel_list);
            $this->display();
        }
    }

    /**
     * 批量设置用户
     */
     public function set_user_info_channel(){
        $channel_list = $this->get_channel_list();
        $this->assign('channel_list',$channel_list);
        $this->display();
     }
    /*某个通道的用户情况*/
    public function set_user_info_channel_btn(){
        $channel_list = $this->get_channel_list();
        $this->assign('channel_list',$channel_list);
        $channel_id = trim(I('channel_id'));
        $result = $this->get_channel_rights($channel_id);
        $this->assign('channel_id',$channel_id);
        $this->assign('data',$result);
        $this->display('set_user_info_channel');

    }

    /**
     * 点击通道获取该通道的用户
     */
    public function set_channel_info_allot_user_rights_list_ajax(){
        $channel_id = trim(I('channel_id'));
        $result = $this->get_channel_rights($channel_id);
        $msg = '';
        $status = 'success';
        $data = $result;
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
    }

    /**
     * ajax点击通道获取该通道的用户
     */
    public function ajax_get_users_by_channel(){
        $channel_id = trim(I('channel_id'));
        $result = $this->get_channel_rights($channel_id);
        $msg = '';
        $status = 'success';
        $data = $result;
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
    }

    /*某个通道的用户情况*/
    public function set_channel_info_allot_user_rights_list(){
        $channel_list = $this->get_channel_list();
        $this->assign('channel_list',$channel_list);

        $channel_id = trim(I('channel_id'));
        $result = $this->get_channel_rights($channel_id);
        $this->assign('channel_id',$channel_id);
        $this->assign('data',$result);
        $this->display('set_channel_info_user');

    }

    /**
     * @return array
     * 获取所有已有折扣的通道
     */
    private function get_channel_list(){
        $where['c.status'] = 1;
        $join = array(
            C('DB_PREFIX').'channel_discount as cd ON cd.channel_id=c.channel_id',
            //C('DB_PREFIX').'channel_account as ca ON ca.account_id=c.account_id'
        );
        $where_a['c.attribute_id'] = 1;
        $where_a = array_merge($where_a,$where);
        $list_a = M('channel as c')->where($where_a)
            ->join($join,"inner")->field("c.channel_id as channel_id,c.channel_name as channel_name,c.channel_code as channel_code")
            ->order('c.modify_date desc')
            ->group("c.channel_id")
            ->select();
        $where_b['c.attribute_id'] = 2;
        $where_b = array_merge($where_b,$where);
        $list_b = M('channel as c')->where($where_b)
            ->field("c.channel_id as channel_id,c.channel_name as channel_name,c.channel_code as channel_code")
            ->order('c.modify_date desc')
            ->select();
        $list = array_merge($list_a,$list_b);
        return get_sort_no($list,0);
    }

    /**
     * @param $channel_id
     * @return array
     * 通道分配信息
     */
    private function get_channel_rights($channel_id){
        if(empty($channel_id)){
            $result['have'] = array();
            $result['no'] = array();
            return $result;
        }
        //获取一级代理商和自营代理商及其所有子集、自营企业
        //1,获取所有直营企业
        $sql_enterprise = "SELECT e.`enterprise_id` as user_id,".
            "e.`enterprise_code` as user_code,".
            "e.`enterprise_name` as user_name,".
            "'企业' as user_type ".
            "FROM t_flow_enterprise e ".
            //"LEFT JOIN t_flow_proxy p ON e.`top_proxy_id`=p.`proxy_id`".
            //p.proxy_type = 1 and
            "WHERE e.status=1 and e.approve_status=1";
        $list_enterprise = M('')->query($sql_enterprise);

        $list_proxy = M('proxy')
            // and ((proxy_type=0 and proxy_level=1) or (proxy_type=1 and proxy_level>0))
            ->where("approve_status=1 and status=1")
            ->field('proxy_id as user_id,proxy_code as user_code,proxy_name as user_name,"代理商" as user_type')
            ->select();

        //所有代理商和企业
        $list = array_merge($list_enterprise,$list_proxy);

        //获取该通道所拥有的企业和代理商
        $model = M('channel_user as cu');

        $channel_id_arr = explode(',', $channel_id);
        $all_user_set_list = array();
        foreach($channel_id_arr as $ca){
            $user_enterprise = $model
                ->join("t_flow_enterprise as e on e.enterprise_id=cu.enterprise_id",'left')
                //->join("t_flow_proxy as pe on pe.proxy_id=e.top_proxy_id",'left')
                //and pe.proxy_type = 1
                ->where("cu.channel_id=$ca and e.status=1 and e.approve_status=1")
                ->field("e.`enterprise_id` as user_id,
              e.`enterprise_code` as user_code,
              e.`enterprise_name` as user_name,
              2 as user_type")
                ->select();

            $user_proxy = $model
                ->join("t_flow_proxy as p on p.proxy_id=cu.proxy_id")
                //and ((p.proxy_type=0 and p.proxy_level=1) or (p.proxy_type=1 and p.proxy_level>0))
                ->where("cu.channel_id=$ca and p.approve_status=1 and p.status=1")
                ->field("p.proxy_id as user_id,p.proxy_code as user_code,p.proxy_name as user_name,1 as user_type")
                ->select();
            $user_set_list = array_merge($user_enterprise,$user_proxy);
            $temp = array();
            foreach($user_set_list as $usl){
                $temp[] = json_encode($usl);
            }
            $all_user_set_list[] = $temp;
        }

        $user_set_list = array();
        $user_set_list_json = $all_user_set_list[0];
        $all_user_set_list_len = count($all_user_set_list);
        if( $all_user_set_list_len >= 2 ){
            for($i=1;$i<$all_user_set_list_len;$i++){
                $user_set_list_json = array_intersect($user_set_list_json,$all_user_set_list[$i]);
            }
        }
        foreach($user_set_list_json as $ulj){
            $user_set_list[] = json_decode($ulj,true);
        }


        $user_set_list_tidy = array();
        foreach($user_set_list as $v){
            $user_code = $v['user_code'];
            $user_set_list_tidy[$user_code] = 1;
        }

        $have = array();
        $no = array();
        if(empty($user_set_list)){
            $no = $list;
        }elseif(count($user_set_list) == count($list)){
            $have = $list;
        }else{
            foreach($list as $vv){
                $the_user_code = $vv['user_code'];
                if(isset($user_set_list_tidy[$the_user_code])){
                    $have[] = $vv;
                }else{
                    $no[] = $vv;
                }
            }
        }

        $result = array();
        $result['have'] = get_sort_no($have,0);
        $result['no'] = get_sort_no($no,0);
        return $result;
    }

    //给通道添加用户
    public function set_channel_info_allot_add_some_rights(){
        $channel_id = trim(I('channel_id'));
        $user_codes = trim(I('user_codes'));
        $add = $this->add_some_rights($channel_id,$user_codes);
        $user_codes = trim($user_codes,',');
        $n_msg="";
        if(!empty($user_codes)){
            $proxys=M("proxy")->where("proxy_code in ($user_codes)")->field("proxy_name")->select();
            $enterprises=M("enterprise")->where("enterprise_code in ($user_codes)")->field("enterprise_name")->select();
            $channels=M("channel")->where("channel_id in ($channel_id)")->field("channel_name")->select();
            foreach ($channels as $v){
                $n_msg.="通道【".$v['channel_name']."】";
            }
            $n_msg.="分配用户";
            foreach ($proxys as $v){
                $n_msg.="【".$v['proxy_name']."】";
            }
            foreach ($enterprises as $v){
                $n_msg.="【".$v['enterprise_name']."】";
            }
        }
        $status = 'error';
        $msg = '系统错误';
        if($add){
            $status = 'success';
            $msg = '分配成功!';
            $n_msg.="分配成功";
        }else{
            $msg = '分配失败!';
            $n_msg.='分配失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，'.$n_msg;
        $this->sys_log('批量设置用户',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    //给通道减少用户
    public function set_channel_info_allot_del_some_rights(){
        $channel_id = trim(I('channel_id'));
        $user_codes = trim(I('user_codes'));
        $add = $this->del_some_rights($channel_id,$user_codes);
        //获取用户的代理商名称
        $user_codes = trim($user_codes,',');
        $n_msg="";//记录日志内容
        if(!empty($user_codes)){
            $proxys=M("proxy")->where("proxy_code in ($user_codes)")->field("proxy_name")->select();
            $enterprises=M("enterprise")->where("enterprise_code in ($user_codes)")->field("enterprise_name")->select();
            $channels=M("channel")->where("channel_id in ($channel_id)")->field("channel_name")->select();
            foreach ($channels as $v){
                $n_msg.="通道【".$v['channel_name']."】";
            }
            $n_msg.="解除用户";
            foreach ($proxys as $v){
                $n_msg.="【".$v['proxy_name']."】";
            }
            foreach ($enterprises as $v){
                $n_msg.="【".$v['enterprise_name']."】";
            }
        }
        $status = 'error';
        $msg = '系统错误';
        if($add){
            $status = 'success';
            $msg = '解除成功！';
            $n_msg.="解除成功";
        }else{
            $msg = '解除失败!';
            $n_msg.="解除失败";
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，'.$n_msg;
        $this->sys_log('批量设置用户',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    private function add_some_rights($channel_id,$user_codes){
        $user_codes = trim($user_codes,',');
        if(empty($user_codes)){
            return false;
        }
        $model = M('channel_user');

        $exists_ids = $this->user_codes2channel_user_ids($channel_id,$user_codes);
        $add_user_info = $this->user_codes2add_channel_user_info($channel_id,$user_codes);
        $model->startTrans();
        $del = 0;
        $add = 0;

        if(!empty($exists_ids)){
            $map_del = array(
                'channel_user_id' => array('in',$exists_ids)
            );
            $del = $model->where($map_del)->delete();
        }
        if(!empty($add_user_info)){
            $add = $model->addAll($add_user_info);
        }

        if($del !== false && $add !== false){
            $model->commit();
        }else{
            $add = false;
            $model->rollback();
        }
        return $add;
    }

    private function del_some_rights($channel_id,$user_codes){
        $user_codes = trim($user_codes,',');
        if(empty($user_codes)){
            return false;
        }
        $model = M('channel_user');

        $exists_ids = $this->user_codes2channel_user_ids($channel_id,$user_codes);

        $del = 0;
        if(!empty($exists_ids)){
            $map_del = array(
                'channel_user_id' => array('in',$exists_ids)
            );
            $del = $model->where($map_del)->delete();
        }

        return $del;
    }

    private function user_codes2channel_user_ids($channel_id,$user_codes){
        $model = M('channel_user as cu');

        $exists_ids_arr = $model
            ->join("t_flow_enterprise as e on e.enterprise_id=cu.enterprise_id and cu.user_type=2",'left')
            ->join("t_flow_proxy as p on p.proxy_id=cu.proxy_id",'left')
            ->field("cu.channel_user_id")
            ->where("cu.channel_id in ($channel_id) and (e.enterprise_code in ($user_codes) or p.proxy_code in ($user_codes))")
            ->select();
        $exists_ids = array();
        foreach($exists_ids_arr as $eia){
            $exists_ids[] = $eia['channel_user_id'];
        }
        return $exists_ids;
    }

    private function user_codes2add_channel_user_info($channel_id,$user_codes){
        $user_id = D('SysUser')->self_id();
        $date_time = date('Y-m-d H:i:s');

        $channel_id_arr = explode(',', $channel_id);
        $user_info = array();
        foreach($channel_id_arr as $ca){
            $user_enterprise = M('enterprise')
                ->where("enterprise_code in ($user_codes)")
                ->field("enterprise_id,
              2 as user_type,
              0 as proxy_id,
              '$ca' as channel_id,
              '$user_id' as create_user_id,
              '$date_time' as create_date,
              '$user_id' as modify_user_id,
              '$date_time' as modify_date
              ")
                ->select();

            $user_proxy = M('proxy')
                ->where("proxy_code in ($user_codes)")
                ->field("0 as enterprise_id,
              1 as user_type,
              proxy_id,
              '$ca' as channel_id,
              '$user_id' as create_user_id,
              '$date_time' as create_date,
              '$user_id' as modify_user_id,
              '$date_time' as modify_date
              ")
                ->select();

            $user_info = array_merge($user_info,$user_enterprise,$user_proxy);
        }

        return $user_info;
    }



}