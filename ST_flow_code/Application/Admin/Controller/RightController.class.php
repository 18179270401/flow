<?php

/*
 * RightController.class.php
 * 节点操作控制器
 *
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class RightController extends CommonController {

    /*
     * 节点列表
     */
    public function index(){
        D("SysUser")->sessionwriteclose();
        $menu_name=trim(I('menu_name'));
        $sys_type = I('sys_type');
        $data_status = I('status');
        if(!empty($menu_name))$map['b.menu_name']=array("like","%{$menu_name}%");
        if(!empty($sys_type))$map['b.sys_type'] = $sys_type;
        //列表出状态和全部
        if($data_status == 9){
            $map['a.status'] = array('neq',2);
        }else{
            $map['a.status'] = $data_status=== '0' ? $data_status : 1;
        }
        //调用分页类
        $count      = M('Sys_function as a')->join("t_flow_sys_menu as b on a.menu_id=b.menu_id","left")->where($map)->count();
        $Page       = new Page($count,20);
        $show   = $Page->show();
         
        //获取所有角色列表
        $function_list = M('Sys_function as a')
        ->join("t_flow_sys_menu as b on a.menu_id=b.menu_id","left")
        ->where($map)
        ->field("a.*,b.menu_name,b.sys_type")
        ->order("a.order_num")
        ->limit($Page->firstRow.','.$Page->listRows)
        ->select();

        //加载模板
        $this->assign('function_list',get_sort_no($function_list,$Page->firstRow));  //数据列表
        $this->assign('page',$show);            //分页
        $this->assign("systype",get_sys_type());
        $this->display('index');         //模板
    }

    /**
     * 导出excel
     */
    public function export_excel() {
        $sys_type = I('sys_type');
        $data_status = I('status');
        if(!empty($menu_name))$map['b.menu_name']=array("like","%{$menu_name}%");
        if(!empty($sys_type))$map['b.sys_type'] = $sys_type;
        //列表出状态和全部
        if($data_status == 9){
            $map['a.status'] = array('neq',2);
        }else{
            $map['a.status'] = $data_status=== '0' ? $data_status : 1;
        }

        //获取所有角色列表
        $function_list = M('Sys_function as a')
            ->join("t_flow_sys_menu as b on a.menu_id=b.menu_id","left")
            ->where($map)
            ->field("a.*,b.menu_name,b.sys_type")
            ->order("a.order_num")
            ->limit(3000)
            ->select();
        $title='功能管理';
        $list=array();
        $headArr=array("功能名称","功能地址","所属菜单","所属平台");
        foreach($function_list as $k=>$v){
            $list[$k]['function_name'] =$v['function_name'];
            $list[$k]['action_url'] =$v['action_url'];
            $list[$k]['menu_name'] =$v['menu_name'];
            $list[$k]['sys_type'] =get_sys_type($v['sys_type']);
        }
        ExportEexcel($title,$headArr,$list);
    }

    public function show(){
        $where['a.function_id']=trim(I('function_id'));
        $info = D('SysMenu')->show($where);
        if($info){
            $this->assign($info);
            $this->display();
        }else{

            $this->error('系统错误！');

        }

    }

    /*
     * 节点添加模板
     */
    public function add(){
        $menu_id = I("menu_id");
        $order_num = M('SysFunction')->where(array('menu_id'=>$menu_id))->order("order_num desc")->field("order_num")->find();
        $order_num = $order_num['order_num'] > 0 ? $order_num['order_num']+1 : 1;
        $this->assign('order_num',$order_num);
        $this->display('add');
    }


    /*
     * 节点添加功能
     */
    public function insert(){
        $msg = '系统错误！';
        $status = 'error';

        if(IS_POST){

            $menu_id = I('post.menu_id');
            $function_name = I('post.function_name');
            $action_url = I('post.action_url');
            $remake = I('post.remake');
            $icon_path = I('post.icon_path');
            $order_num = I('post.order_num',1,'int');
            $data_status = I('post.status',1,'int'); 

            $menuinfo = D('SysMenu')->menuinfo($menu_id);

            //判断菜单是否存在
            if($menuinfo){

                //节点名称不能为空
                if(!empty($function_name) && !empty($action_url)){

                    //查询是否有重复节点名称或者节点地址
                    $map['function_name'] = array('eq',$function_name);
                    $map['action_url'] = array('eq',$action_url);
                    //$where['_logic'] = 'or';

                    //$map['_complex'] = $where;
                    $map['menu_id'] = array('eq',$menu_id);
                    $map['status'] = array('neq',2);

                    $functioninfo = M('Sys_function')->where($map)->find();

                    if(!$functioninfo){

                        //添加数组
                        $add = array(
                            'menu_id'           =>  $menu_id,
                            'function_name'     =>  $function_name,
                            'action_url'        =>  $action_url,
                            'icon_path'         =>  $icon_path,
                            'status'            =>  $data_status,
                            'remake'            =>  $remake,
                            'order_num'         =>  $order_num,
                            'create_user_id'    =>  D('SysUser')->self_id(),
                            'create_date'       =>  date('Y-m-d H:i:s',time()),
                            'modify_user_id'    =>  D('SysUser')->self_id(),
                            'modify_date'       =>  date('Y-m-d H:i:s',time()),
                            );

                        //执行添加
                        $id=M('Sys_function')->add($add);
                        if($id){
                            $msg = '新增功能完成！';
                            $status = 'success';
                            $n_msg='完成';
                        }else{
                            $msg = '新增功能失败！';
                            $n_msg='失败';
                        }
                        $note='用户【' . get_user_name(D('SysUser')->self_id()) . '】，ID【'.$id.'】，新增功能，功能名称【'.$function_name.'】，功能地址【'.$action_url.'】，排序号【'.$order_num.'】，所属菜单【'.$this->menu($menu_id,'name').'】，所属平台【'.$this->menu($menu_id).'】'.$n_msg;
                        $this->sys_log('新增功能',$note);
                    }else{
                        $msg = '功能名称或者地址已存在,请勿重复添加';
                    }

                }else{

                    $msg = '功能名称或者地址不能为空';

                }
            }


            if(IS_AJAX){
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
            
        }

    }


    /*
     * 节点修改模板
     */
    public function edit(){

        $info = D('SysFunction')->functioninfo(I('get.function_id',0,'int'));
        //当角色不存在时
        if($info){
            $this->assign('info',$info);
            $this->display('edit');
        }else{

            $this->error('系统错误！');

        }
    }


    /*
     * 节点修改功能
     */
    public function update(){

        $msg = '系统错误！';
        $status = 'error';

        if(IS_POST){

            $function_id = I('post.function_id',0,'int');
            $function_name = I('post.function_name');
            $action_url = I('post.action_url');
            $remark = I('post.remark');
            $order_num = I('post.order_num',1,'int');
            $data_status = I('post.status',1,'int'); 


            //id不能为0
            if(!empty($function_id)){
                //判断如果没有数据的话
                $functioninfo = D('SysFunction')->find($function_id);
                if($functioninfo){
                    //角色名称不能为空
                    $user_id=D("SysUser")->self_id();
                    if(!empty($function_name) && !empty($action_url)){

                        //查询是否有重复节点名称
                        $map['function_name'] = array('eq',$function_name);
                        $map['action_url'] = array('eq',$action_url);
                        //$where['_logic'] = 'or';

                        //$map['_complex'] = $where;
                        $map['status'] = array('neq',2);
                        $map['menu_id'] = array('eq',$functioninfo['menu_id']);
                        $map['function_id'] = array('neq',$function_id);
                        $functioninfo1 = M('Sys_function')->where($map)->find();
                        if(!$functioninfo1){

                            //修改数组
                            $edit = array(
                                'function_id'       =>  $function_id,
                                'function_name'     =>  $function_name,
                                'action_url'        =>  $action_url,
                                'order_num'         =>  $order_num,
                                'status'            =>  $data_status,
                                'remark'            =>  $remark,
                                'modify_user_id'    =>  D('SysUser')->self_id(),
                                'modify_date'       =>  date('Y-m-d H:i:s',time()),
                                );

                            //执行修改
                            if(M('Sys_function')->save($edit)){
                                $msg = '编辑功能完成！';
                                $status = 'success';
                                $n_msg='完成';
                            }else{
                                $msg = '编辑功能失败！';
                                $n_msg='失败';
                            }
                            $c_item='';
                            $c_item.=$function_name===$functioninfo['function_name']?'':'，功能名称【'. $function_name.'】';
                            $fg=!empty($c_item)?'，':'';
                            $c_item.=$action_url===$functioninfo['action_url']?'':$fg.'功能地址【'. $action_url.'】';
                            $fg=!empty($c_item)?'，':'';
                            $c_item.=$order_num===$functioninfo['order_num']?'':$fg.'排序号【'. $order_num.'】';
                            $c_item.='所属菜单【'.$this->menu($functioninfo['menu_id'],'name').'】，所属平台【'.$this->menu($functioninfo['menu_id']).'】';
                            $note='用户【' . get_user_name(D('SysUser')->self_id()) . '】，ID【'.$function_id.'】，编辑功能【'.$functioninfo['function_name'].'】，所属菜单【'.$this->menu($functioninfo['menu_id'],'name').'】，所属平台【'.$this->menu($functioninfo['menu_id']).'】：'.$c_item.$n_msg;
                            $this->sys_log('编辑功能',$note);
                        }else{
                            $msg = '功能名称或者地址已存在,请勿重复添加';
                        }
                    }else{

                        $msg = '功能名称或者地址不能为空';

                    }
                    
                }
                
            }

            if(IS_AJAX)$this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                
        }

    }


    /*
     * 节点删除功能
     */
    public function delete(){

        $msg = '系统错误！';
        $status = 'error';

        if(IS_POST && IS_AJAX){

            $function_id = I('post.function_id',0,'int');

            if(!empty($function_id)){

                $functioninfo = D('SysFunction')->functioninfo($function_id);

                if($functioninfo){

                    //将需要删除的节点修改为已删除状态
                    $edit = array(
                        'function_id'=>$function_id,
                        'status'=> 2,
                        );

                    $edit = M('Sys_function')->save($edit);

                    //删除包含该节点的所有角色节点信息
                    $delete_role = D('SysFunction')->delete_role($function_id);

                    if($edit && $delete_role){

                        $status = 'success';
                        //$msg = '删除节点（'.$functioninfo['function_name'].'）成功！';
						$msg = '删除功能成功！';
                        $n_msg='成功';

                    }else{

                        $msg = '删除功能失败!';
                        $n_msg='失败';
                    } 
                }else{
                    $msg = '删除功能失败!';
                    $n_msg='失败';
                }          
            }else{
                 $msg = '删除功能失败!';
                $n_msg='失败';
            }

            if(IS_AJAX){
                $functioninfo = D('SysFunction')->functioninfo($function_id);
                $note='用户【' . get_user_name(D('SysUser')->self_id()) . '】，ID【'.$function_id.'】，删除功能【'.$functioninfo['function_name'].'】，所属菜单【'.$this->menu($functioninfo['menu_id'],'name').'】，所属平台【'.$this->menu($functioninfo['menu_id']).'】'.$n_msg;
                $this->sys_log('删除功能',$note);
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
            
        }
    }

    /*
     * 节点禁用功能
     */
    public function toggle_status(){

        $msg = '系统错误！';
        $status = 'error';

        if(IS_POST && IS_AJAX){

            $function_id = I('post.function_id',0,'int');

            if(!empty($function_id)){

                $functioninfo = D('SysFunction')->functioninfo($function_id);
                if($functioninfo['status']<1){
                    $status=1;
                    $m="启用";
                }else{
                    $status=0;
                    $m="禁用";
                }

                if($functioninfo){

                    //将需要删除的节点修改为已删除状态
                    $edit = array(
                        'function_id'=>$function_id,
                        'status'=> $status,
                        'modify_user_id'=>D('SysUser')->self_id(),
                        'modify_date'=>date("Y-m-d H:i:s",time()),
                        );

                    $edit = M('Sys_function')->save($edit);

                    //删除包含该节点的所有角色节点信息
                    $delete_role = D('SysFunction')->delete_role($function_id);

                    if($edit && $delete_role){

                        $status = 'success';
                        $msg = "功能".$m.'成功！';
                        $n_msg=$m.'成功';
                    }else{

                        $msg = "功能".$m.'失败!';
                        $n_msg=$m.'失败';
                    } 
                }else{
                    $msg = "功能".$m.'失败!';
                    $n_msg=$m.'失败';
                }
                $note='用户【' . get_user_name(D('SysUser')->self_id()) . '】，ID【'.$function_id.'】，'.$m.'功能，功能名称【'.$functioninfo['function_name'].'】，所属菜单【'.$this->menu($functioninfo['menu_id'],'name').'】，所属平台【'.$this->menu($functioninfo['menu_id']).'】'.$n_msg;
                $this->sys_log($m.'功能',$note);
            }else{
                 $msg = '对不起没有找到相关信息!';
                 $n_msg='对不起没有找到功能的相关信息';
            }

            if(IS_AJAX){
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
            
        }
    }

    public function menu($id,$name=''){
      $info=M('sys_menu')->where('menu_id='.$id)->field('menu_name,sys_type')->find();
        $res='';
        if($name){
            $res=$info['menu_name'];
        }else{
            if($info['sys_type']==1){
                $res='尚通端';
            }else if($info['sys_type']==2){
                $res='代理商';
            }else {
                $res='企业';
            }
        }
        return $res;
    }

}