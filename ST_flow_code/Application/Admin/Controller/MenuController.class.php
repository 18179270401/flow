<?php

/*
 * MenuController.class.php
 * 菜单操作控制器
 *
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class MenuController extends CommonController {

    /*
     * 菜单列表
     */
    public function index(){
        D("SysUser")->sessionwriteclose();
        $menu_name = I('menu_name');
        $menu_type = I('menu_type');
        $sys_type = I('sys_type');
        $data_status = I('status');
        if(!empty($menu_name))$map['a.menu_name'] = array("like","%{$menu_name}%");
        if(!empty($menu_type))$map['a.menu_type'] = $menu_type;
        if(!empty($sys_type))$map['a.sys_type'] = $sys_type;
        //列表出状态和全部
        if($data_status == 9){
            $map['a.status'] = array('neq',2);
        }else{
            $map['a.status'] = $data_status=== '0' ? $data_status : 1;
        }
        
        //调用分页类
        $count      = M('Sys_menu as a')->where($map)->join(C('DB_PREFIX').'sys_menu as b ON a.top_menu_id=b.menu_id',"left")->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        
        //获取所有角色列表
        $menu_list = M('Sys_menu as a')
        ->where($map)
        ->join(C('DB_PREFIX').'sys_menu as b ON a.top_menu_id=b.menu_id',"left")
        ->order("a.order_num asc,a.menu_id asc")
        ->field("a.*,b.menu_name as bmenu_name")
        ->order('a.modify_date desc')
        ->limit($Page->firstRow.','.$Page->listRows)
        ->select();

        //加载模板
        $this->assign('menu_list',get_sort_no($menu_list,$Page->firstRow));  //数据列表
        $this->assign('page',$show);            //分页
        $this->assign("systype",get_sys_type());
        $this->display('index');         //模板
    }

   /**
 * 导出excel
 */
    public function export_excel() {
        $menu_name = I('menu_name');
        $menu_type = I('menu_type');
        $sys_type = I('sys_type');
        $data_status = I('status');
        if(!empty($menu_name))$map['a.menu_name'] = array("like","%{$menu_name}%");
        if(!empty($menu_type))$map['a.menu_type'] = $menu_type;
        if(!empty($sys_type))$map['a.sys_type'] = $sys_type;
        //列表出状态和全部
        if($data_status == 9){
            $map['a.status'] = array('neq',2);
        }else{
            $map['a.status'] = $data_status=== '0' ? $data_status : 1;
        }

        //获取所有角色列表
        $menu_list = M('Sys_menu as a')
            ->where($map)
            ->join(C('DB_PREFIX').'sys_menu as b ON a.top_menu_id=b.menu_id',"left")
            ->order("a.order_num asc,a.menu_id asc")
            ->field("a.*,b.menu_name as bmenu_name")
            ->order('a.modify_date desc')
            ->limit(3000)
            ->select();
        $title='菜单管理';
        $list=array();
        $headArr=array("菜单名称","分组名称","上级菜单","菜单类型","所属平台");
        foreach($menu_list as $k=>$v){
            $list[$k]['menu_name'] =$v['menu_name'];
            $list[$k]['group_name'] = empty($v['group_name'])?'--':$v['group_name'];
            if($v['bmenu_name']){
                $list[$k]['bmenu_name'] =$v['bmenu_name'];
            }else{
                $list[$k]['bmenu_name']='顶级菜单';
            }

            if($v['menu_type']==1){
                $list[$k]['menu_type']='分级菜单';
            }else {
                $list[$k]['menu_type']='功能菜单';
            }
            $list[$k]['sys_type'] =get_sys_type($v['sys_type']);
        }
        ExportEexcel($title,$headArr,$list);
    }


    public function show(){
        $where['a.menu_id'] =trim(I('get.menu_id'));
        $info = M('Sys_menu as a')
            ->where($where)
            ->join(C('DB_PREFIX').'sys_menu as b ON a.top_menu_id=b.menu_id',"left")
            ->field("a.*,b.menu_name as bmenu_name")
            ->find();
        //当菜单不存在时
        if($info){
            $this->assign($info);
            $this->display();
        }else{
            $this->error('菜单不存在！');
        }
    }

    /*
     * 菜单添加模板
     */
    public function add(){
        //读取最后一个菜单ID值+1做为排序号
        $order_num = M('SysMenu')->order("menu_id desc")->field("menu_id")->find();
        $order_num = $order_num['menu_id']+1;
        $this->assign('order_num',$order_num);
        //读取顶级菜单
        $topmenu = D("SysMenu")->topmenu();
        $this->assign('topmenu',$topmenu);  
        //读取所属平台
        $this->assign('list_sys_type',get_sys_type());
        $this->display('add');
    }

    /*
     * 菜单添加功能
     */
    public function insert(){
        $msg = '系统错误！';
        $status = 'error';
        if(IS_POST){
            $menu_name = I('post.menu_name');       //菜单名称
            $group_name = I('post.group_name');       //菜单名称
            $sys_type = I('post.sys_type');         //菜单所属用户  尚通1/代理商2/企业3
            $top_menu_id = I('post.top_menu_id');   //上级菜单ID
            $page_url = I('post.page_url');         //菜单路由地址
            $icon_path = I('post.icon_path');       //菜单图标
            $order_num = I('post.order_num');       //菜单排序
            $data_status = I('post.status');             //菜单状态
            $remark = I('remark');                  //菜单备注
            //菜单名称不能为空
            if(!empty($menu_name) ){
                if($top_menu_id == 0){
                    $menu_type == 1;
                }else{
                    $menu_type == 2;
                }
                //判断如果是功能菜单路由地址不能为空
                if(!($menu_type == 2 && empty($page_url)) ){
                    //判断菜单所属类型只能1,2,3
                    if($sys_type == 1 or $sys_type == 2 or $sys_type == 3){
                        //同所属下 同级别下不能名称不能相同
                        $map['menu_name'] = array('eq',$menu_name);
                        $map['sys_type'] = array('eq',$sys_type);
                        $map['status'] = array('neq',2);
                        $map['top_menu_id'] = array('eq',$top_menu_id);
                        
                        $menuinfo = D('SysMenu')->where($map)->find();
                        if(!$menuinfo){
                            //如果不是顶级菜单  
                            if($top_menu_id != 0 && $top_menu_id != ''){
                                //判断菜单的上级ID是否所属类型是否一致
                                $menuinfo = D('SysMenu')->menuinfo($top_menu_id);
                                if($menuinfo['sys_type'] != $sys_type){
                                    if(IS_AJAX) $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                                }
                            }

                            if($top_menu_id == '0' or $top_menu_id == ''){
                                $top_menu_id = '0';
                            }
                            if($top_menu_id == '0'){
                                $page_url = '';
                                $menu_type = 1;
                                $menu_type_m='分级菜单';
                            }else{
                                $menu_type = 2;
                                $menu_type_m='功能菜单';
                            }
                            $add = array(
                                'menu_name'     =>  $menu_name,
                                'group_name'     =>  $group_name,
                                'sys_type'      =>  $sys_type,    
                                'top_menu_id'   =>  $top_menu_id,
                                'menu_type'     =>  $menu_type,
                                'page_url'      =>  $page_url,
                                'icon_path'     =>  $icon_path,
                                'order_num'     =>  $order_num,
                                'status'        =>  $data_status,
                                'remark'        =>  $remark,
                                'create_user_id'=>  D('SysUser')->self_id(),
                                'create_date'   =>  date('Y-m-d H:i:s',time()),
                                'modify_user_id'=>  D('SysUser')->self_id(),
                                'modify_date'   =>  date('Y-m-d H:i:s',time()),
                                );
                            $id=M('Sys_menu')->add($add);
                            if($id){
                                $msg = '新增菜单成功！';
                                $status = 'success';
                                $n_msg='成功';
                            }else{
                                $msg = '新增菜单失败!';
                                $n_msg='失败';
                            }
                            $c_item='';
                            $c_item.=empty($page_url)?'':'，菜单地址【'. $page_url.'】';
                            $c_item.=empty($icon_path)?'':'，图标 【'. $icon_path.'】';
                            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$id.'】，新增菜单，菜单名称【'.$menu_name.'】，上级菜单【'.$this->menu_name($top_menu_id).'】，菜单类型【'.$menu_type_m.'】，所属平台【'.get_sys_type($sys_type).'】'.$c_item.$n_msg;
                            $this->sys_log('新增菜单',$note);
                        }else{
                            $msg = '菜单名称重复,请仔细检查！';
                        }
                    }
                }else{
                    $msg = '功能菜单路由地址不能为空';
                }
            }else{
                $msg = '菜单名称不能为空';
            }
            if(IS_AJAX) $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }


    public function edit(){
        $info = D('SysMenu')->menuinfo(I('get.menu_id',0,'int'));
        //当菜单不存在时
        if($info){
            $this->assign('info',$info);
            $this->display('edit');
        }else{
            $this->error('菜单不存在！');
        }
    }

    public function update(){
        $msg = '系统错误！';
        $status = 'error';

        if(IS_POST){
            $menu_id = I('post.menu_id');           //菜单ID
            $menu_name = I('post.menu_name');       //菜单名称
            $group_name = I('post.group_name');       //菜单名称
            $page_url = I('post.page_url');         //菜单路由地址
            $icon_path = I('post.icon_path');       //菜单图标
            $order_num = I('post.order_num');       //菜单排序
            $data_status = I('post.status');             //菜单状态
            $remark = I('remark');                  //菜单备注
            //获取菜单详情
            $info = D('SysMenu')->menuinfo($menu_id);
            //菜单名称不能为空
            if(!empty($menu_name) ){
                //判断如果是功能菜单路由地址不能为空
                if(!($info['menu_type'] == 2 && empty($page_url)) ){
                        //同所属下 同级别下不能重名
                        $map['menu_name'] = array('eq',$menu_name);
                        $map['sys_type'] = array('eq',$info['sys_type']);
                        $map['status'] = array('neq',2);
                        $map['menu_id'] = array('neq',$menu_id);
                        $map['top_menu_id'] = array('eq',$info['top_menu_id']);

                        $menuinfo = D('SysMenu')->where($map)->find();
                        if(!$menuinfo){
                            //如果是分组菜单 将地址清空
                            if($info['menu_type'] == 1){
                                $page_url = '';
                            }
                            $edit = array(
                                'menu_id'       =>  $menu_id,
                                'menu_name'     =>  $menu_name,
                                'group_name'     =>  $group_name,
                                'page_url'      =>  $page_url,
                                'icon_path'     =>  $icon_path,
                                'order_num'     =>  $order_num,
                                'status'        =>  $data_status,
                                'remark'        =>  $remark,
                                'modify_user_id'=>  D('SysUser')->self_id(),
                                'modify_date'   =>  date('Y-m-d H:i:s',time()),
                                );
                            if(M('Sys_menu')->save($edit)){
                                $msg = '编辑菜单成功！';
                                $status = 'success';
                                $n_msg='成功';
                            }else{
                                $msg = '编辑菜单失败!';
                                $n_msg='失败';
                            }
                            $c_item='';
                            $c_item.=$menu_name===$info['menu_name']?'':'菜单名称【'. $menu_name.'】';
                            if($page_url!==$info['page_url'] && $info['menu_type'] != 1){
                                $fg=!empty($c_item)?'，':'';
                                $c_item.=empty($page_url)?$fg.'清空菜单地址':$fg.'菜单地址【'. $page_url.'】';
                            }
                            if($icon_path!==$info['icon_path']){
                                $fg=!empty($c_item)?'，':'';
                                $c_item.=$icon_path==''?$fg.'清空图标':$fg.'图标【'. $icon_path.'】';
                            }
                            if($order_num!==$info['order_num']){
                                $fg=!empty($c_item)?'，':'';
                                $c_item.=$order_num==''?$fg.'清空排序号':$fg.'排序号【'. $order_num.'】';
                            }
                            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$menu_id.'】，编辑菜单【'.$info['menu_name'].'】：'.$c_item.$n_msg;
                            $this->sys_log('编辑菜单',$note);
                        }else{
                            $msg = '菜单名称重复,请仔细检查！';
                        }
                }else{
                    $msg = '功能菜单路由地址不能为空';
                }
            }else{
                $msg = '菜单名称不能为空';
            }
            if(IS_AJAX) $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }


    public function delete(){
        $msg = '系统错误！';
        $status = 'error';
        
        if(IS_POST && IS_AJAX){
            $menu_id = I('post.menu_id',0,'int');
            if(!empty($menu_id)){
                $menuinfo = D('SysMenu')->menuinfo($menu_id);
                if($menuinfo){
                    $model = M('');
                    $model->startTrans();
                    //将需要删除的节点修改为已删除状态
                    $edit = array(
                        'menu_id'=>$menu_id,
                        'status'=> 2,
                        );
                    $edit = M('Sys_menu')->save($edit);
                    //删除菜单下的所有节点
                    $menu_delete_function = D('SysFunction')->menu_delete_function($menu_id);
                    if($edit && $menu_delete_function){
                        $model->commit();
                        $status = 'success';
                        $msg = '删除菜单【'.$menuinfo['menu_name'].'】成功！';
                        $n_msg='成功';
                    }else{
                        $model->rollback();
                        $msg = '删除菜单失败!';
                        $n_msg='失败';
                    }
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$menu_id.'】，删除菜单【'.$menuinfo['menu_name'].'】'.$n_msg;
                    $this->sys_log('删除菜单',$note);
                }
            }
        }
        if(IS_AJAX){
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }
    
    function toggle_status(){
        $msg = '系统错误！';
        $status = 'error';
        
        if(IS_POST && IS_AJAX){
            $menu_id = I('post.menu_id',0,'int');
            if(!empty($menu_id)){
                $menuinfo = D('SysMenu')->menuinfo($menu_id);
                if($menuinfo){
                    $status = $menuinfo['status'] == 1 ? "0" : "1";
                    $edit = array(
                        'menu_id'=>$menu_id,
                        'status'=> $status,
                        'modify_user_id'=>  D('SysUser')->self_id(),
                        'modify_date'   =>  date('Y-m-d H:i:s',time()),
                    );
                    $edit = M('Sys_menu')->save($edit);
                    $status_name = $status == 1 ? "启用" : "禁用";
                    if($edit){
                        $status = 'success';
                        $msg = "状态".$status_name.'成功!';
                        $n_msg='成功';
                    }else{
                        $msg = "状态".$status_name.'失败!';
                        $n_msg='失败';
                    }
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$menu_id.'】，'.$status_name.'菜单【'.$menuinfo['menu_name'].'】'.$n_msg;
                    $this->sys_log($status_name.'菜单',$note);
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

    function menu_name($id){
        $list=M('sys_menu')->where('menu_id='.$id)->find();
        return $list['menu_name'];
    }

}