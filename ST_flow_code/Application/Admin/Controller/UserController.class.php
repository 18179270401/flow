<?php

/*
 * UserController.class.php
 * 用户操作控制器
 *
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class UserController extends CommonController {

    /*
     * 用户列表
     */
    public function index(){
        D("SysUser")->sessionwriteclose();
        $map['u.status'] = array('neq',2);
        $map['u.proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
        if(!D('SysUser')->is_admin()){
            $map['u.is_manager'] = array('neq',1);
        }
        $user_name = trim(I('get.user_name'));
        $mobile = trim(I('get.mobile'));
        $status = trim(I('get.status'));
        $depart_id = trim(I('get.depart_id','-1','intval'));

        $login_name_full = trim(I('get.login_name_full'));
        if($user_name){
            $map['u.user_name'] = array('like','%'.$user_name.'%');
        }
        if($mobile){
            $map['u.mobile'] = array('like','%'.$mobile.'%');
        }
        if($login_name_full){
            $map['u.login_name_full'] = array('like','%'.$login_name_full.'%');
        }
        if('-1' != $depart_id) {
            $map['u.depart_id'] = $depart_id;
        }

        if($status == '0'){
           $map['u.status'] = array('eq',0);
        }elseif($status == '1' or $status == ''){
           $map['u.status'] = array('eq',1);
           if($status == ''){
                $status = 1;
           }
        }

        $root_user_id = D('SysUser')->root_user_id();
        $arr_depart = D('SysDepart')->get_user_depart($root_user_id);

        //调用分页类
        $count      = M('')
        ->field('u.*,d.depart_name')
        ->table('t_flow_sys_user as u')
        ->where($map)
        ->count();

        $Page       = new Page($count,20);
        $show       = $Page->show();

        $user_list = M('')
        ->field('u.*,d.depart_name')
        ->table('t_flow_sys_user as u')
        ->join('left join t_flow_sys_depart as d on u.depart_id = d.depart_id and d.status = 1')
        ->where($map)
        ->order('u.is_manager desc,u.modify_date desc,u.user_id desc')
        ->limit($Page->firstRow.','.$Page->listRows)
        ->select();
        foreach($user_list as $k=>$v){
            $map = array();
            $map['center.user_id'] = array('eq',$v['user_id']);
            $role_list = M('Sys_user_role as center')
            ->field('role.role_name')
            ->join('t_flow_sys_role as role on role.role_id = center.role_id and role.status = 1','right')
            ->where($map)
            ->select();
            if($role_list){
                $roles = '';
                foreach($role_list as $lk=>$lv){
                    $roles .= ','.$lv['role_name'];
                }
                $role_name = substr($roles,1,strlen($roles)-1);
                $user_list[$k]['role_name'] = $role_name; 
            }else{
                $user_list[$k]['role_name'] = '';
            }
            
        }

        //exit;
        $this->assign('is_admin',D('SysUser')->is_admin());
        $this->assign('status',$status);
        $this->assign('user_list', get_sort_no($user_list, $Page->firstRow));
        $this->assign('page',$show);
        $this->assign('arr_depart', $arr_depart);
        $this->assign('depart_id', $depart_id);
        $this->display();

    }

    /**
     * 导出excel
     */
    public function export_excel() {
        $map['u.status'] = array('neq',2);
        $map['u.proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
        if(!D('SysUser')->is_admin()){
            $map['u.is_manager'] = array('neq',1);
        }
        $user_name = trim(I('get.user_name'));
        $mobile = trim(I('get.mobile'));
        $status = trim(I('get.status'));
        $depart_id = trim(I('get.depart_id','-1','intval'));

        $login_name_full = trim(I('get.login_name_full'));
        if($user_name){
            $map['u.user_name'] = array('like','%'.$user_name.'%');
        }
        if($mobile){
            $map['u.mobile'] = array('like','%'.$mobile.'%');
        }
        if($login_name_full){
            $map['u.login_name_full'] = array('like','%'.$login_name_full.'%');
        }
        if('-1' != $depart_id) {
            $map['u.depart_id'] = $depart_id;
        }

        if($status == '0'){
           $map['u.status'] = array('eq',0);
        }elseif($status == '1' or $status == ''){
           $map['u.status'] = array('eq',1);
           if($status == ''){
                $status = 1;
           }
        }
        
        $user_list = M('')
            ->field('u.*,d.depart_name')
            ->table('t_flow_sys_user as u')
            ->join('left join t_flow_sys_depart as d on u.depart_id = d.depart_id and d.status = 1')
            ->where($map)
            ->order('u.is_manager desc,u.modify_date desc,u.user_id desc')
            ->limit(3000)
            ->select();
        $title='用户管理';
        $list=array();
        $headArr=array("用户姓名","登录名称","性别","所属部门","职务","角色","联系电话");
        foreach($user_list as $k=>$v){
            $list[$k]['user_name'] =$v['user_name'];
            $list[$k]['login_name_full'] =$v['login_name_full'];
            if($v['sex']==1){
                $list[$k]['sex']='男';
            }else {
                $list[$k]['sex']='女';
            }
            $list[$k]['depart_name'] =$v['depart_name'];
            $list[$k]['posts'] =$v['posts'];
            $list[$k]['role_name'] =$this->role($v['user_id']);
            $list[$k]['mobile'] =" ".$v['mobile'];
        }
        ExportEexcel($title,$headArr,$list);
    }
    public function role($user_id){
        $map = array();
        $map['center.user_id'] = array('eq',$user_id);
        $role_list = M('Sys_user_role as center')
            ->field('role.role_name')
            ->join('t_flow_sys_role as role on role.role_id = center.role_id and role.status = 1','right')
            ->where($map)
            ->select();
        if($role_list){
            $roles = '';
            foreach($role_list as $lk=>$lv){
                $roles .= ','.$lv['role_name'];
            }
            $role_name = substr($roles,1,strlen($roles)-1);
            return $user_list['role_name'] = $role_name;
        }else{
            return $user_list['role_name'] = '';
        }
    }

    /**
     * 添加管理员模板显示
     */
    public function add(){

        //部门列表
        $map = array();
        $map['user_id'] = array('eq',D('SysUser')->root_user_id());
        $map['status'] = array('eq',1);
        $depart_list = M('Sys_depart')->where($map)->select();

        //获取当前自身的编号
        $this->assign('is_top_proxy_admin',D('SysUser')->is_top_proxy_admin());
        $this->assign('code',D('Proxy')->user_proxy_code(D('SysUser')->self_id()));
        $this->assign('depart_list',$depart_list);

        $this->display();
        
    }


    /**
     *  添加管理员
     */

    public function insert(){

        //当前操作者属于哪个平台添加的管理员也属于哪个平台
        $user_type = D('SysUser')->self_user_type();

        $msg = '系统错误!';
        $status = 'error';
        if(IS_POST && IS_AJAX){

            $self_id = D('SysUser')->self_id();
            $time = date('Y-m-d H:i:s',time());

            //获取基础数据
            $user_name      =   I('post.user_name');             //联系人   *
            $login_name     =   I('post.login_name');            //登录部分名 *
            $mobile         =   I('post.mobile');                //手机号码    *
            $depart_id      =   I('post.depart_id');             //部门ID
            $email          =   I('post.email');                 //邮箱
            $sex            =   I('post.sex');
            $posts          =   I('post.posts');

            if(!empty($user_name)){
                //登录名称
                if(!empty($login_name)){
                    if(in_array($sex,array('0','1','2'))){

                        //获取编号
                        $login_name_full = $login_name.'@'.D('Proxy')->user_proxy_code(D('SysUser')->self_id());

                        if(!D('SysUser')->check_login_name_repeat($login_name_full)){
                        
                            //部门是否在自己的范围内
                            if($depart_id &&  !D('SysDepart')->is_me_depart($depart_id)){
                                $msg = '部门选择错误！';
                                return $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                            }
                            if(empty($email) or (!empty($email) && isEmail($email)) ){
                                //手机号码不能为空
                                if(!empty($mobile) && isTel($mobile)){


                                        $add = array(
                                            'user_name'         =>  $user_name,                 //联系人
                                            'login_name'        =>  $login_name,                //登录部分名
                                            'login_name_full'   =>  $login_name_full,           //登录全名
                                            'login_pass'        =>  md5('123456'),                //密码
                                            'user_type'         =>  $user_type,                 //用户类型
                                            'is_manager'        =>  0,                //是否是管理员
                                            'proxy_id'          =>  D('SysUser')->self_proxy_id(),                  //代理商ID
                                            'enterprise_id'     =>  '',             //企业ID
                                            'sex'               =>  $sex,
                                            'depart_id'         =>  $depart_id,
                                            'posts'             =>  $posts,
                                            'mobile'            =>  $mobile,                    //手机号码
                                            'email'             =>  $email,                     //邮箱
                                            'status'            =>  1,                    //状态 0已禁用 1正常
                                            'create_user_id'    =>  $self_id,                   //创建人
                                            'create_date'       =>  $time,                      //创建时间
                                            'modify_user_id'    =>  $self_id,                   //最后修改人
                                            'modify_date'       =>  $time,                      //最后修改时间
                                            );
                                        $su=M('Sys_user')->add($add);
                                        if($su){
                                            $msg = '新增用户成功！';
                                            $status = 'success';
                                            $n_msg='成功';
                                        }else{
                                            $msg = '新增用户失败！';
                                            $n_msg='失败';
                                        }
                                    $c_item='';
                                    if(!empty($depart_id)){
                                        $c_item.='，部门【'.get_depart_name($depart_id).'】';
                                    }
                                    $c_item.=empty($posts)?'':'，职务【'.$posts.'】';
                                    $c_item.=empty($email)?'':'，邮箱【'.$email.'】';
                                    $sex1=$sex==1?"男":"女";
                                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$su.'】，新增用户，登录用户名【'.$login_name.'】，用户姓名【'.$user_name.'】,姓别【'.$sex1.'】，联系电话【'.$mobile.'】'.$c_item.$n_msg;
                                    $this->sys_log('新增用户',$note);
                                }else{
                                    $msg = '手机号码格式错误';
                                }
                            }else{
                                $msg = '邮箱格式错误!';
                            }
                        }else{
                            $msg = '当前登录名已存在';
                        }
                    }else{
                        $msg = '请选择性别！';
                    }
                }else{
                    $msg = '请输入登录名称';
                } 
            }else{
                $msg = '联系人不能为空';
            }

            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
        
    }


    public function edit(){
        $msg = '系统错误';
        $status = 'error';

        $user_id = intval(I('get.user_id'));
        if($user_id){
        
            $map= array();
            $map['user.user_id'][] = array('eq',$user_id);     //ID符合
            $map['user.status'] = array('neq',2);            //未被删除
            $map['user.proxy_id'] = array('eq',D('SysUser')->self_proxy_id());   //归属自己平台
            if(!D('SysUser')->is_admin()){
                $map['is_manager'] = array('neq',1);
            }
            $user = M('Sys_user as user')
            ->field('user.*,proxy.proxy_code')
            ->join('t_flow_proxy as proxy on proxy.proxy_id = user.proxy_id','left')
            ->where($map)->find();
            //判断用户是否存在
            if($user){

                //部门列表
                $map = array();
                $map['user_id'] = array('eq',D('SysUser')->root_user_id());
                $map['status'] = array('eq',1);
                $depart_list = M('Sys_depart')->where($map)->select();


                foreach($depart_list as $k=>$v){
                    if($user['depart_id'] == $v['depart_id']){
                        $depart_list[$k]['ischecked'] = 'selected';
                    }
                }

                $this->assign('depart_list',$depart_list);
                $this->assign('is_manager',$user['is_manager']);
                $this->assign('is_top_proxy_admin',D('SysUser')->is_top_proxy_admin());
                $this->assign('user',$user);
                $this->display();
                exit;
            }else{
                $msg = '用户不存在!';
            }
            
        }

        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        
    }

    public function update(){

        $msg = '系统错误!';
        $status = 'error';

        if(IS_POST && IS_AJAX){
            $self_id = D('SysUser')->self_id();
            $time = date('Y-m-d H:i:s',time());

            //获取基础数据
            $login_name     =   I('post.login_name');
            $user_id        =   intval(I('post.user_id'));       //ID
            $user_name      =   I('post.user_name');             //联系人
            $mobile         =   I('post.mobile');                //手机号码
            $depart_id      =   intval(I('post.depart_id'));     //部门ID
            $email          =   I('post.email');                 //邮箱
            $sex            =   I('post.sex');
            $posts          =   I('post.posts');
            //判断用户是否在自己平台
            $map= array();
            $map['user_id'][] = array('eq',$user_id);     //ID符合
            $map['status'] = array('neq',2);            //未被删除
            $map['proxy_id'] = array('eq',D('SysUser')->self_proxy_id());   //归属自己平台
            if(!D('SysUser')->is_admin()){
                $map['is_manager'] = array('neq',1);
            }
            $user = M('Sys_user')->where($map)->find();

            if($user){

                
                //用户名不能为空
                if(!empty($user_name)){
                    //部门是否在自己的范围内
                    if($depart_id && !D('SysDepart')->is_me_depart($depart_id)){
                        $msg = '部门选择错误！';
                        return $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                    }
                    if(in_array($sex,array('1','2'))){
                        if(empty($email) or (!empty($email) && isEmail($email)) ){
                            //手机号码不能为空
                            if(!empty($mobile) && isTel($mobile)){
                                    if(!empty($login_name)){
                                        $login_name_full = $login_name.'@'.D('Proxy')->user_proxy_code(D('SysUser')->self_id());

                                        if(!D('SysUser')->check_login_name_repeat($login_name_full,$user_id)){

                                            $edit = array(
                                                'user_id'           =>  $user_id,
                                                'user_name'         =>  $user_name, 
                                                'login_name'        =>  $login_name,
                                                'login_name_full'   =>  $login_name_full,    
                                                'depart_id'         =>  $depart_id,
                                                'posts'             =>  $posts,    
                                                'mobile'            =>  $mobile,      
                                                'email'             =>  $email,
                                                'sex'               =>  $sex,      
                                                'modify_user_id'    =>  $self_id,   
                                                'modify_date'       =>  $time,     
                                                );

                                            $edit = M('Sys_user')->save($edit);

                                            if($edit){
                                                $msg = '编辑用户成功！';
                                                $status = 'success';
                                                $n_msg='成功';
                                            }else{
                                                $msg = '编辑用户失败！';
                                                $n_msg='失败';
                                            }
                                            $c_item='';
                                            $c_item.=$user_name===$user['user_name']?'':'用户姓名【'. $user_name.'】';
                                            $fg=!empty($c_item)?'，':'';
                                            $c_item.=$login_name===$user['login_name']?'':$fg.'登陆名称【'. $login_name.'】，登陆全称【'.$login_name_full.'】';
                                            $fg=!empty($c_item)?'，':'';
                                            $c_item.=$mobile===$user['mobile']?'':$fg.'联系电话【'. $mobile.'】';
                                            if($depart_id!==$user['depart_id']){
                                                if(empty($depart_id)){
                                                    $c_item.='，部门【'.get_depart_name($depart_id).'】';
                                                }else{
                                                    $c_item.='';
                                                }
                                            }
                                            if($posts!==$user['posts']){
                                                $fg=!empty($c_item)?'，':'';
                                                $c_item.=empty($posts)?$fg.'清除职务':$fg.'职【'. $posts.'】';
                                            }
                                            if($email!==$user['email']){
                                                $fg=!empty($c_item)?'，':'';
                                                $c_item.=empty($email)?$fg.'清除邮箱':$fg.'邮箱【'. $email.'】';
                                            }
                                            if($sex!==$user['sex']){
                                                $fg=!empty($c_item)?'，':'';
                                                $c_item.=$sex==1?$fg."性别【男】":$fg."性别【女】";
                                            }

                                            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$user_id.'】，编辑用户，用户姓名【'.$user['user_name'].'】：'.$c_item.$n_msg;
                                            $this->sys_log('编辑用户',$note);
                                        }else{
                                            $msg = '登录名称已存在！';
                                        }
                                    }else{
                                        $msg = '请输入登录名称！';
                                    }
                            }else{
                                $msg = '手机号码格式错误';
                            }
                        }else{
                            $msg = '邮箱格式错误!';
                        }
                    }else{
                        $msg = '请选择性别！';
                    }
                }else{
                    $msg = '联系人不能为空';
                }
            }
             $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }

    /**
     *  删除管理员
     */
    public function delete(){
        $msg = '系统错误';
        $status = 'error';
        $user_id = intval(I('post.user_id'));
        $map['status'] = array('neq',2);
        $map['user_id'] = array('eq',$user_id);
        $user = M('Sys_user')->where($map)->find();
        //用户必须存在
        if($user){
            //用户必须与自身同为一个平台
            if($user['proxy_id'] == D('SysUser')->self_proxy_id()){
                //判断用户是否是超级管理员或者是修改自身不允许被修改
                if(!D('SysUser')->is_root_admin($user['user_id']) or (D('SysUser')->self_id() == $user['user_id']) ){
                    //执行删除
                    $model = M('');
                    $model->startTrans();
                    $delete_array = array(
                        'user_id' => $user['user_id'],
                        'status' => 2,
                        );
                    $delete = M('Sys_user')->save($delete_array);
                    //删除与用户有关的用户角色信息
                    $role = D('SysRole')->delete_role($user['user_id']);
                    if($delete && $role){
                        $msg = '删除用户【'.$user['user_name'].'】成功!';
                        $status = 'success';
                        $model->commit();
                        $n_msg='成功';
                    }else{
                        $msg = '删除用户失败!';
                        $model->rollback();
                        $n_msg='失败';
                    }
                    //$sex=$user['sex']==1?"男":"女";
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$user_id.'】，删除用户，登录用户名【'.$user['login_name'].'】，用户姓名【'.$user['user_name'].'】'.$n_msg;
                    $this->sys_log('删除用户',$note);
                }
            }
        }
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }


    public function toggle_status(){
        $msg = '系统错误!';
        $status = 'error';
        $data = array();
        $user_id = intval(I('post.user_id'));
        if($user_id){
            $map = array();
            $map['status'] = array('neq',2);
            $map['user_id'] = array('eq',$user_id);
            $map['proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
            $user = M('Sys_user')->where($map)->find();
            if($user){
                 //判断如果修改的是超级管理员或者是修改自身不允许被修改
                if(D('SysUser')->is_root_admin($user['user_id']) or  (D('SysUser')->self_id() == $user['user_id']) ){
                    $msg = '权限不足';
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                }
                $edit = array();
                if($user['status'] == 1){
                    $edit['status'] = 0;
                }else{
                    $edit['status'] = 1;
                }
                $edit['modify_user_id'] = D('SysUser')->self_id();
                $edit['modify_date'] = date("Y-m-d H:i:s",time());
                $edit['user_id'] = $user_id;
                if(M('Sys_user')->save($edit)){
                    if($user['status'] == 1){
                        $msg = '禁用成功!';
                        $n_msg='成功';
                        $title='禁用';
                    }else{
                        $msg = '启用成功!';
                        $n_msg='成功';
                        $title='启用';
                    }
                    $status = 'success';
                    $data['status'] = 0;
                }else{
                    if($user['status'] == 1){
                        $msg = '禁用失败!';
                        $n_msg='失败';
                        $title='禁用';
                    }else{
                        $msg = '启用失败!';
                        $n_msg='失败';
                        $title='启用';
                    }
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$user_id.'】，'.$title.'登录用户名【'.$user['user_name'].'】'.$n_msg;
                $this->sys_log($title.'用户',$note);
            }
        }
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
    }
    


    /**
     *  个人设置
     */
    public function set(){
        $msg = '系统错误!';
        $status = 'error';

        if(IS_POST){
            $login_name = I('post.login_name');
            $user_name = I('post.user_name');
            $mobile = I('post.mobile');
            $email = I('post.email');
            $sex = I('post.sex');
            if(!empty($user_name)){
                if(empty($email) or (!empty($email) && isEmail($email)) ){
                    if(!empty($mobile) && isTel($mobile)){
                        if(!empty($login_name)){
                        	$user_type = D('SysUser')->self_user_type();
                        	$proxy_id = D('SysUser')->self_proxy_id();
                        	$enterprise_id = D('SysUser')->self_enterprise_id();
                        	$peinfo = D('SysUser')->get_pe_info($user_type, $proxy_id, $enterprise_id);
                        	$pecode = empty($peinfo['proxy_code']) ? $peinfo['enterprise_code'] : $peinfo['proxy_code'];
                            $login_name_full = $login_name.'@'.$pecode;
                            if(!D('SysUser')->check_login_name_repeat($login_name_full,D('SysUser')->self_id())){
                                $edit =array(
                                    'user_name'         =>          $user_name,
                                    'login_name'        =>          $login_name,
                                    'login_name_full'   =>          $login_name_full,
                                    'mobile'            =>          $mobile,
                                    'email'             =>          $email,
                                    'sex'               =>          $sex,
                                    'user_id'           =>          D('SysUser')->self_id(),
                                    'modify_user_id'    =>          D('SysUser')->self_id(),
                                    'modify_date'       =>          date("Y-m-d H:i:s",time()),
                                    );
                                if(M('Sys_user')->save($edit)){
                                    $msg = '个人信息设置成功!';
                                    $status = 'success';
                                    $n_msg='成功';
                                    //更新session
                                    $update = array(
                                        'user_name'     =>      $edit['user_name'],
                                        );
                                    D('SysUser')->update_session($update);
                                }else{
                                    $msg = '个人信息设置失败!';
                                    $n_msg='失败';
                                }
                                $sex1=$sex==1?"男":"女";
                                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.D('SysUser')->self_id().'】,个人信息设置，登录用户名【'.$login_name.'】，用户姓名【'.$user_name.'】,姓别【'.$sex1.'】，联系电话【'.$mobile.'】'.$n_msg;
                                $this->sys_log('个人信息设置',$note);
                            }else{
                                $msg = '登录名称已存在！';
                            }
                        }else{
                            $msg = '请输入正确登录名称！';
                        }
                    }else{
                        $msg = '请输入正确手机号码!';
                    }
                }else{
                    $msg = '请输入正确邮箱!';
                }
            }else{
                $msg = '请输入用户姓名！';
            }
            
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));


        }else{

            //自身用户
            $map = array();
            $map['user.user_id'] = array('eq',D('SysUser')->self_id());
            $user = M('SysUser as user')
            ->field('user.*,depart.depart_name')
            ->join('t_flow_sys_depart as depart on depart.depart_id = user.depart_id','left')
            ->where($map)->find();
            if($user['proxy_id']){
               $proxycode = M('Proxy')->field('proxy_code')->find($user['proxy_id']); 
               $user['code'] = $proxycode['proxy_code'];
            }else{
                $enterprisecode = M('Enterprise')->field('enterprise_code')->find($user['enterprise_id']); 
                $user['code'] = $enterprisecode['enterprise_code'];
            }

            $this->assign('is_top_proxy_admin',D('SysUser')->is_top_proxy_admin());
            $this->assign('is_admin',D('SysUser')->is_admin());
            $this->assign('user',$user);
            $this->display();
        }
    }



    /**
     *  设置密码
     */
    public function set_password(){

        $msg = '系统错误！';
        $status = 'error';
        $data = '';

        if(IS_POST){
            $old_password = I('post.old_password');
            $new_password = I('post.new_password');

            if(!empty($old_password) ){
                if(IsPassword($old_password)){
                    if(!empty($new_password) ){
                        if(IsPassword($new_password)){
                            $map['user_id']     =   array('eq',D('SysUser')->self_id());
                            $map['login_pass']    =   array('eq',md5($old_password));
                            $user = M('Sys_user')->where($map)->find();
                            if($user){    
                                $edit = array(
                                    'user_id'       =>      D('SysUser')->self_id(),
                                    'login_pass'    =>      md5($new_password),
                                    'modify_user_id'=>      D('SysUser')->self_id(),
                                    'modify_date'   =>      date("Y-m-d H:i:s",time())
                                    );
                                if(M('Sys_user')->save($edit)){
                                    $msg = '新密码将在下次登录生效！';
                                    $data = '修改登录密码成功！<br>登录名【'.$user['login_name_full'].'】<br>新密码【'.$new_password.'】';
                                    $n_msg='成功';
                                    $status = 'success';
                                }else{
                                    $msg = '密码更新失败！';
                                    $n_msg='失败';
                                }
                                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.D('SysUser')->self_id().'】，更新登录名【'.$user['login_name_full'].'】密码'.$n_msg;
                                $this->sys_log('更新密码',$note);
                            }else{
                                $msg = '旧密码错误！';
                            }
                        }else{
                            $msg = '旧密码核对失败!';
                        }
                    }else{
                        $msg = '旧密码长度错误！';
                    }
                }else{
                    $msg = '新密码格式错误!';
                }
            }else{
                $msg = '旧密码格式错误!';
            }
            
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
        
        }else{
            $this->display();
        }
        
    }


    /**
     *  重置管理员密码
     */
    public function reset_password(){
        $msg= '系统错误！';
        $status = 'error';
        $data = '';
        $user_id = I('post.user_id');
        $map['user_id'][] = array('eq',$user_id);
        $map['status'] = array('neq',2);
        if(!D('SysUser')->is_admin()){
            $map['is_manager'] = array('neq',1);
        }
        $map['proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
        $user = M('Sys_user')->where($map)->find();
        if($user){
            $pass = rand(100000,999999);
            $edit = array(
                'user_id'       =>      $user_id,
                'login_pass'    =>      md5($pass),
                'modify_user_id'=>      D('SysUser')->self_id(),
                'modify_date'   =>      date("Y-m-d H:i:s",time())
                );
            if(M('Sys_user')->save($edit)){
                $data = '重置用户【'.$user['user_name'].'】登录密码成功！<br>登录名【'.$user['login_name_full'].'】<br>新密码【'.$pass.'】';
                $msg = '重置密码成功！';
                $status = 'success';
                $n_msg='成功';
            }else{
                $msg = '重置失败！';
                $n_msg='失败';
            }

            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$user_id.'】，重置用户【'.$user['user_name'].'】登录密码'.$n_msg;
            $this->sys_log('重置密码',$note);
        }
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
    }


    public function show(){
        $msg = '系统错误！';
        $status = 'error';
        $user_id = I('get.user_id');
        $map['user.status'] = array('neq',2);
        $map['user.user_id'] = array('eq',$user_id);
        $map['user.proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
        if(!D('SysUser')->is_admin()){
            $map['user.is_manager'] = array('neq',1);
        }
        $user = M('Sys_user as user')
        ->field('user.*,depart.depart_name')
        ->join('t_flow_sys_depart as depart on depart.depart_id = user.depart_id','left')
        ->where($map)->find();
        if($user){
            $this->assign('user',$user);
            $this->display();
        }else{
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }
}