<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
class EnterpriseController extends CommonController {

    public $os_enterprise_ids;
    public $os_proxy_ids;

    public function start(){
        $this->os_enterprise_ids = D('Enterprise')->enterprise_child_ids();
        $this->os_proxy_ids = D('Proxy')->proxy_child_ids();
    }


    /**
     *  企业档案
     */
    public function index(){
        /*
        $type = trim(I('get.type'));

        if($type == 'table'){
        */      D("SysUser")->sessionwriteclose();
                $model = M('');
                $enterprise_code = trim(I('get.enterprise_code'));
                $enterprise_name = trim(I('get.enterprise_name'));
                $top_proxy_name = trim(I('get.top_proxy_name'));
                $top_proxy_code = trim(I('get.top_proxy_code'));
                //$user_name = trim(I('get.user_name'));
                $status = trim(I('get.status'));
                $approve_status = trim(I('get.approve_status'));
                $top_proxy_id = trim(I('get.top_proxy_id'));
                $user_name=trim(I('get.user_name'));//客户经理

                if($user_name){
                    $map['user_name'] = array('like','%'.$user_name.'%');
                }
                if($enterprise_code){
                    $map['enterprise.enterprise_code'] = array('like','%'.$enterprise_code.'%');
                }
                if($enterprise_name){
                    $map['enterprise.enterprise_name'] = array('like','%'.$enterprise_name.'%');
                }
                if($top_proxy_name){
                    $map['top_proxy.proxy_name'] = array('like','%'.$top_proxy_name.'%');
                }
                if($top_proxy_code){
                    $map['top_proxy.proxy_code'] = array('like','%'.$top_proxy_code.'%');
                }
                $map['enterprise.status'] = array('neq',2);
                if(in_array($status,array('0','1'))){
                    $map['enterprise.status'] = array('eq',$status);
                }

               /* if(in_array($approve_status,array('0','1','2'))){
                    $map['enterprise.approve_status'] = array('eq',$approve_status);
                }*/
                if(D('SysUser')->is_top_proxy_admin()){
                    if(!isset($_GET['status']) && !isset($_GET['istree']) ){
                        $_GET['status'] = 1;
                        $map['enterprise.status'] = array('eq',1);
                    }
                    $map['enterprise.approve_status'] = array('eq',1);
                }else{
                    if(!isset($_GET['status']) && !isset($_GET['istree']) ){
                        $_GET['status'] = 1;
                        $map['enterprise.status'] = array('eq',1);
                    }
                    if($approve_status ==""){
                        $map['enterprise.approve_status'] =1;
                    }else{
                        if($approve_status!=9){
                            $map['enterprise.approve_status'] = array('eq',$approve_status);
                        }
                    }
                }

                $where['enterprise.enterprise_id'] = array(array('in',$this->os_enterprise_ids),array('in',D('Enterprise')->enterprise_ids2()),'or') ;

                $map['_complex'] = $where;

                if($top_proxy_id && (in_array($top_proxy_id,explode(',',$this->os_proxy_ids)) or ($top_proxy_id == D('SysUser')->self_proxy_id() ) ) ){
                    $ids = M('')->query("select getProxyChildList('$top_proxy_id') as ids");
                    $map['enterprise.top_proxy_id'] = array('in',$ids[0]['ids']);
                }

                if(D('SysUser')->is_top_proxy_admin() == false){
                    $map['enterprise.top_proxy_id'] = D('SysUser')->self_proxy_id();
                }

                $count = $model
                ->table('t_flow_enterprise as enterprise')
                ->field('enterprise.enterprise_id,enterprise.enterprise_code,enterprise.enterprise_name,enterprise.message_status,top_proxy.proxy_name as top_name,enterprise.approve_status,enterprise.status,user.user_name')
                ->join('left join t_flow_sys_user as user on user.user_id = enterprise.sale_id and user.status = 1')
                ->join('left join t_flow_proxy as top_proxy on enterprise.top_proxy_id = top_proxy.proxy_id')
                ->where($map)
                ->count();

                $Page       = new Page($count,20);
                $show       = $Page->show();

                $enterprise_list = $model
                ->table('t_flow_enterprise as enterprise')
                ->field('enterprise.enterprise_id,enterprise.enterprise_code,enterprise.message_status,enterprise.contact_name,
                enterprise.contact_tel,enterprise.enterprise_name,top_proxy.proxy_name as top_name,
                top_proxy.proxy_code as top_code,enterprise.approve_status,enterprise.status,
                user.user_name,enterprise.top_proxy_id,enterprise.refund_status')
                ->join('left join t_flow_sys_user as user on user.user_id = enterprise.sale_id and user.status = 1')
                ->join('left join t_flow_proxy as top_proxy on enterprise.top_proxy_id = top_proxy.proxy_id')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('enterprise.modify_date Desc,top_proxy.proxy_level')
                ->where($map)
                ->select();

                //判断是否需要显示设置客户经理
                $self_proxy_id = D('SysUser')->self_proxy_id();
                foreach( $enterprise_list as $k => $v){
                    if($v['top_proxy_id'] == $self_proxy_id){
                        $enterprise_list[$k]['is_os'] = true;
                    }
                }

                $this->assign('is_top_proxy_admin',D('SysUser')->is_top_proxy_admin());
                $this->assign('page',$show);
                $this->assign('enterprise_list', get_sort_no($enterprise_list, $Page->firstRow));
                $this->display();
            

        /*
        
        }else{


           //获取树形结构
            $self_proxy_id = D('SysUser')->self_proxy_id();

            $map = array();
            $map['approve_status'] = array('eq',1);

            $map['status'] = array('neq',2);
            $map['proxy_id'] = array('in',D('SysUser')->self_proxy_id().','.$this->os_proxy_ids);
            $proxy_tree_list = M('Proxy')
            ->field('proxy_id,proxy_name,proxy_code,top_proxy_id')
            ->order('proxy_code')
            ->where($map)
            ->select();

            foreach($proxy_tree_list as  $v){
               if($v['proxy_id'] == $self_proxy_id){
                    $self = $v;
               }
            }
            if(D('SysUser')->is_top_proxy_admin()){
                $self['proxy_name'] = '尚通科技';
            }
            $proxy_tree = D('proxy')->proxy_tree($self,$proxy_tree_list);
            $this->assign('tree_html',D('Enterprise')->tree_html($proxy_tree));
            $this->display('tree');

        }
        */

    }



    /**
     *  新增企业模板
     */
    public function add(){
        //获取旗下所有管理员
        $map['status'] = array('eq',1);
        $map['is_manager'] = array('eq',0);
        $map['proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
        $user_list = M('SysUser')->where($map)->select();
        foreach($user_list as $k=>$v){
            if($v['user_id'] == D('SysUser')->self_id()){
                $user_list[$k]['selected'] = 'selected';
            }
        }

        //获取支持的运营商

        $root_user = D('SysUser')->userinfo(D('SysUser')->root_user_id());
        $map = array();
        $map['operator_id'] = array('in',$root_user['operator']);
        $operator_list = M('Sys_operator')->where($map)->select();

        $map = array();
        $map['province_id'] = array('neq',1);
        $province_list = M('Sys_province')->where($map)->select();
        $this->assign('province_list',$province_list);

        $city_list = M('Sys_city')->select();
        $this->assign('city_list',$city_list);


        $this->assign('is_top_proxy_admin',D('SysUser')->is_top_proxy_admin());
        $this->assign('operator_list',$operator_list);
        $this->assign('user_list',$user_list);
        $this->display();

    }

    /**
     *  新增企业
     */
    public function insert(){

        $msg = '系统错误!';
        $status = 'error';
        $data = array();



        $self_id = D('SysUser')->self_id();
        $time = date('Y-m-d H:i:s',time());

        //获取基础数据
        $contact_name           =   trim(I('post.contact_name'));            //姓名   
        $tel                    =   trim(I('post.tel'));                  //联系电话
        $contact_tel            =   trim(I('post.contact_tel'));               //手机号码
        $email                  =   trim(I('post.email'));                //邮箱
        $enterprise_name        =   trim(I('post.enterprise_name'));      //企业名称
        $operator_list          =   I('post.operator_list');   //支持运营商
        $address                =   trim(I('post.address'));              //企业地址
        $province               =   trim(I('post.province_id'));
        $city                   =   trim(I('post.city_id'));

        $operator = implode(',',$operator_list);
        if(!D('SysUser')->is_top_proxy_admin()){
            if(!empty($contact_name) ){
                if(empty($email) or isEmail($email)){
                    if(empty($tel) or isTel($tel)){
                            if(!empty($contact_tel) && isTel($contact_tel)){
                                    if(!empty($enterprise_name)){
                                        if(!D('Enterprise')->check_enterprise_name($enterprise_name)){
                                            if(!empty($operator) ){
                                                $model = M();
                                                $model->startTrans();
                                                $max_enterprise_code =  M('Enterprise')->max('enterprise_code');
                                                $enterprise_code = ($max_enterprise_code < 1) ?  C("DES_ENTERPRISE_ID") : $max_enterprise_code + 1;

                                                $addinfo = array(

                                                    'enterprise_code'   =>  $enterprise_code,
                                                    'enterprise_name'   =>  $enterprise_name,
                                                    'tel'               =>  $tel,
                                                    'contact_name'      =>  $contact_name,
                                                    'contact_tel'       =>  $contact_tel,
                                                    'email'             =>  $email,
                                                    'top_proxy_id'      =>  D('SysUser')->self_proxy_id(),
                                                    'operator'          =>  $operator,
                                                    'address'           =>  $address,
                                                    'province'          =>  $province,
                                                    'city'              =>  $city,
                                                    'sale_id'           =>  $self_id,
                                                    'create_user_id'    =>  $self_id,
                                                    'create_date'       =>  $time,
                                                    'modify_user_id'    =>  $self_id,
                                                    'modify_date'       =>  $time,
                                                    'status'            =>  1,
                                                    'approve_status'    =>  0,
                                                    'approve_user_id'   =>  '',
                                                    'approve_date'      =>  '',
                                                    'approve_remark'    =>  '',
                                                    );
                                                   

                                                $enterprise_id = M('Enterprise')->add($addinfo);

                                                //添加基础数据
                                                $addbase = array(
                                                    'user_name'         =>  $contact_name,                      //联系人
                                                    'login_name'        =>  'admin',                     //登录部分名
                                                    'login_name_full'   =>  'admin@'.$enterprise_code,     //登录全名
                                                    'login_pass'        =>  md5('123456'),                //密码
                                                    'user_type'         =>  3,      //用户类型
                                                    'is_manager'        =>  1,      //是否是管理员
                                                    'proxy_id'          =>  '',     //代理商ID
                                                    'enterprise_id'     =>  $enterprise_id, //企业ID
                                                    'mobile'            =>  $contact_tel,   //手机号码
                                                    'email'             =>  $email,   //邮箱
                                                    'status'            =>  1,    //状态 0已禁用 1正常
                                                    'create_user_id'    =>  $self_id,    //创建人
                                                    'create_date'       =>  $time,   //创建时间
                                                    'modify_user_id'    =>  $self_id,     //最后修改人
                                                    'modify_date'       =>  $time,   //最后修改时间
                                                    );

                                                
                                                $user_id = M('Sys_user')->add($addbase);

                                                

                                                                                                    
                                                if(!D('SysUser')->is_admin() or D('SysUser')->is_all_enterprise($user_id) == '0'){
                                                    $addcorrelation = array(
                                                        'user_id'           =>      D('SysUser')->self_id(),
                                                        'enterprise_id'     =>      $enterprise_id,
                                                        'create_user_id'    =>      $self_id,    //创建人
                                                        'create_date'       =>      $time,   //创建时间
                                                        );

                                                    $correlation = M('Enterprise_user')->add($addcorrelation);

                                                }else{
                                                    $correlation = true;
                                                }
                                                    
                                                //添加账户信息
                                                $account_add = array(
                                                    'enterprise_id'         =>          $enterprise_id,
                                                    'account_balance'       =>          0.00,
                                                    'freeze_money'          =>          0.00,
                                                    'credit_money'          =>          0.00,
                                                    'credit_freeze_money'   =>          0.00,
                                                    'create_user_id'        =>          $self_id,
                                                    'create_date'           =>          $time,
                                                    'modify_user_id'        =>          $self_id,                        //最后修改人
                                                    'modify_date'           =>          $time 
                                                    );

                                                $account = M('Enterprise_account')->add( $account_add );
                                                
                                                $apiadd = array(
                                                    'user_type'                 =>         2,
                                                    'proxy_id'                  =>         0,
                                                    'enterprise_id'             =>         $enterprise_id,
                                                    'api_account'               =>         randomY(8,'QWERTYUIOPASDFGHJKLZXCVBNM'),
                                                    'api_key'                   =>         getrandstr(32),
                                                    'api_callback_address'      =>         '',
                                                    'api_callback_ip'           =>         '',
                                                    );
                                                
                                                $api_add = M('Sys_api')->add($apiadd);


                                                if($correlation  && $enterprise_id && $user_id && $account && $api_add){
                                                    $msg = '新增企业成功，请上传企业证件！';
                                                    $status = 'success';
                                                    $model->commit();
                                                    $n_msg='成功';
                                                    $proxy_name=M('proxy')->where('proxy_id='.D('SysUser')->self_proxy_id())->field('proxy_name')->find();
                                                    $remind_content='代理商【'.$proxy_name['proxy_name'].'】新增的企业【'.$enterprise_name.'】已提交审核，请进行审核！';
                                                    R('ObjectRemind/send_user',array(3,$remind_content));

                                                }else{
                                                    $msg = '新增企业失败！';
                                                    $model->rollback();
                                                    $n_msg='失败';
                                                }
                                                 $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$enterprise_id.'】，新增企业【'.$enterprise_name.'('.$enterprise_code.')'.'】'.$n_msg;
                                                $this->sys_log('新增企业',$note);
                                            }else{
                                                $msg = '请选择企业支持运营商!';
                                            }
                                        }else{
                                            $msg = '企业名称已存在！';
                                        }
                                    }else{
                                        $msg = '请填写企业名称！';
                                    }
                            }else{
                                $msg = '请填写联系人电话！';
                            }
                    }else{
                        $msg = '请输入正确的公司电话！';
                    }
                }else{
                    $msg = '请输入正确的邮箱！';
                }
            }else{
                $msg = '请输入联系人!';
            }

        }
 
         $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
    }



        public function edit(){
        $msg ="系统错误";
        $status = 'error';

        $enterprise_id = I('get.enterprise_id');
        $where['enterprise.enterprise_id'] = array(array('in',D('Enterprise')->enterprise_child_ids()),array('in',D('Enterprise')->enterprise_ids()),'or');
        $map['_complex'] = $where;  
        $map['enterprise.status'] =array('neq',2);
        $map['enterprise.enterprise_id'] = array('eq',$enterprise_id);

        $enterprise = M('Enterprise as enterprise')
        ->field('enterprise.*,user.user_id,user.login_name')
        ->join('left join t_flow_sys_user as user on user.enterprise_id = enterprise.enterprise_id and user.is_manager = 1')
        ->where($map)
        ->find();

        if($enterprise){
            //获取旗下所有管理员
            $map = array();
            $map['status'] = array('eq',1);
            $map['is_manager'] = array('eq',0);
            $map['proxy_id'] = array('eq',$enterprise['top_proxy_id']);
            $user_list = M('SysUser')->where($map)->select();
            foreach($user_list as $k=>$v){
                if($v['user_id'] == $enterprise['sale_id']){

                    $user_list[$k]['selected'] = 'selected';
                }
            }

            //获取支持的运营商
            $map = array();
            $map['status'] = array('neq',2);
            $map['proxy_id'] = array('eq',$enterprise['top_proxy_id']);
            $top_proxy = M('Proxy')->field('operator')->where($map)->find();

            $map = array();
            $map['operator_id'] = array('in',$top_proxy['operator']);
            $operator_list = M('Sys_operator')->where($map)->select();

            if($enterprise['operator']){
                foreach($operator_list as $k=>$v){
                    if(in_array($v['operator_id'],explode(',',$enterprise['operator']))){
                        $operator_list[$k]['checked'] = 'checked';
                    }
                }
            }

            $map = array();
            $map['province_id'] = array('neq',1);
            $province_list = M('Sys_province')->where($map)->select();
            $this->assign('province_list',$province_list);

            $city_list = M('Sys_city')->select();
            $this->assign('city_list',$city_list);


            $this->assign('enterprise',$enterprise);
            $this->assign('is_top_proxy_admin',D('SysUser')->is_top_proxy_admin());
            $this->assign('operator_list',$operator_list);
            $this->assign('user_list',$user_list);
            $this->display();

        }else{
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }


    /**
     *  修改企业
     */
    public function update(){

        $msg = '系统错误!';
        $status = 'error';
        $data = array();



        $self_id = D('SysUser')->self_id();
        $time = date('Y-m-d H:i:s',time());

        //获取基础数据
        $enterprise_id      =   trim(I('post.enterprise_id'));
        $login_name         =   trim(I('post.login_name'));
        $contact_name       =   trim(I('post.contact_name'));           
        $tel                =   trim(I('post.tel'));                 
        $contact_tel        =   trim(I('post.contact_tel'));    
        $enterprise_name    =   trim(I('post.enterprise_name'));    
        $operator_list      =   I('post.operator_list');    
        $address            =   trim(I('post.address'));
        $email              =   trim(I('post.email'));
        $province           =   trim(I('post.province_id'));
        $city               =   trim(I('post.city_id'));
        $operator           =   implode(',',$operator_list);

        $where['enterprise.enterprise_id'] = array(array('in',D('Enterprise')->enterprise_child_ids()),array('in',D('Enterprise')->enterprise_ids()),'or');
        $map['_complex'] = $where;  
        $map['enterprise.status'] = array('neq',2);
        $map['enterprise.enterprise_id'] = array('eq',$enterprise_id);
 
        $enterprise = M('Enterprise as enterprise')
        ->field('enterprise.enterprise_id,enterprise.enterprise_code,enterprise.enterprise_name,enterprise.tel,enterprise.sale_id,enterprise.operator,user.user_id,user.user_name,user.mobile,enterprise.top_proxy_id,enterprise.sale_id as old_sale_id,user.login_name,enterprise.contact_name,enterprise.contact_tel,enterprise.province,enterprise.city,enterprise.email,enterprise.address')
        ->join('left join t_flow_sys_user as user on user.enterprise_id = enterprise.enterprise_id and user.is_manager = 1')
        ->where($map)
        ->find();

        if($enterprise){
            if(!empty($contact_name)){
                if(empty($email) or isEmail($email) ){
                    if(!empty($login_name)){
                        if(empty($tel) or isTel($tel)){
                                if(!empty($contact_tel)){
                                    if(isTel($contact_tel)){
                                        if(!empty($enterprise_name)){
                                            if(!D('Enterprise')->check_enterprise_name($enterprise_name,$enterprise['enterprise_id'])){
                                                if(!empty($operator)){

                                                    $model = M();
                                                    $model->startTrans();

                                                    $enterpriseedit = array(
                                                        'enterprise_id'         =>  $enterprise_id,
                                                        'enterprise_name'       =>  $enterprise_name,
                                                        'tel'                   =>  $tel,
                                                        'contact_tel'           =>  $contact_tel,
                                                        'contact_name'          =>  $contact_name,
                                                        'operator'              =>  $operator,
                                                        'province'              =>  $province,
                                                        'city'                  =>  $city,
                                                        'address'               =>  $address,
                                                        'email'                 =>  $email,
                                                        'modify_user_id'        =>  $self_id,
                                                        'modify_date'           =>  $time,
                                                        );
                                                
                                                    $enterprise_id = M('Enterprise')->save($enterpriseedit);

                                                    //添加基础数据
                                                    $editbase = array(
                                                        'user_id'           =>  $enterprise['user_id'],
                                                        'login_name'        =>  $login_name,
                                                        'login_name_full'   =>  $login_name.'@'.$enterprise['enterprise_code'],
                                                        'modify_user_id'    =>  $self_id,                       
                                                        'modify_date'       =>  $time,                  
                                                        );
                                                    $user_id = M('Sys_user')->save($editbase);
                                                    if($enterprise_id && $user_id){
                                                        $msg = '编辑企业成功！';
                                                        $status = 'success';
                                                        $model->commit();
                                                        $n_msg='成功';
                                                    }else{
                                                        $msg = '编辑企业失败！';
                                                        $model->rollback();
                                                        $n_msg='失败';
                                                    }
                                                    $c_item='';
                                                    $c_item.=$enterprise_name===$enterprise['enterprise_name']?'':'企业名称【'. $enterprise_name.'】';
                                                    $fg=!empty($c_item)?'，':'';
                                                    $c_item.=$login_name===$enterprise['login_name']?'':$fg.'登陆名称【'. $login_name.'】';
                                                    $fg=!empty($c_item)?'，':'';
                                                    $ops='';
                                                    foreach($operator_list as $v){
                                                        $ops.=get_operator_name($v).'，';
                                                    }
                                                    $ops= substr($ops,0,-1);
                                                    $c_item.=$operator===$enterprise['operator']?'':$fg.'支持运营商【'. $ops.'】';
                                                    $fg=!empty($c_item)?'，':'';
                                                    $c_item.=$contact_name===$enterprise['contact_name']?'':$fg.'联系人【'. $contact_name.'】';
                                                    $fg=!empty($c_item)?'，':'';
                                                    $c_item.=$tel===$enterprise['tel']?'':$fg.'联系电话【'. $tel.'】';
                                                    if($contact_tel!==$enterprise['contact_tel']){
                                                        $fg=!empty($c_item)?'，':'';
                                                        $c_item.=empty($contact_tel)?$fg.'清除公司电话':$fg.'公司电话【'. $contact_tel.'】';
                                                    }
                                                    if($email!==$enterprise['email']){
                                                        $fg=!empty($c_item)?'，':'';
                                                        $c_item.=empty($email)?$fg.'清除邮箱':$fg.'邮箱【'. $email.'】';
                                                    }
                                                    if($province!==$enterprise['province']){
                                                        $fg=!empty($c_item)?'，':'';
                                                        if($province!='' || $province!=='0' ){
                                                            $c_item.=$fg.'所属省【'. get_province_name($province).'】';
                                                        }else{
                                                            $c_item.=$fg.'清除所属省';
                                                        }
                                                    }
                                                    if($city!==$enterprise['city']){
                                                        $fg=!empty($c_item)?'，':'';
                                                        if($city!='' || $city!=='0'){
                                                            $c_item.=$fg.'所属市【'. get_city_name($city).'】';
                                                        }else{
                                                            $c_item.=$fg.'清除所属市';
                                                        }
                                                    }
                                                    if($address!==$enterprise['address']){
                                                        $fg=!empty($c_item)?'，':'';
                                                        $c_item.=empty($address)?$fg.'清除地址':$fg.'地址【'. $address.'】';
                                                    }
                                                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$enterprise_id.'】，编辑企业【'.$enterprise['enterprise_name'].'('.$enterprise['enterprise_code'].')】:'.$c_item.$n_msg;
                                                    $this->sys_log('编辑企业',$note);
                                                }else{
                                                    $msg = '代理商支持运营商不能为空!'; 
                                                }

                                            }else{
                                                $msg = '代理商名称已存在！'; 
                                            }
                                        }else{
                                            $msg = '请输入代理商名称！';
                                        }
                                    }else{  
                                        $msg = '请输入正确联系人电话！';
                                    }
                                }else{
                                    $msg = '请输入联系人电话！'; 
                                }
                        }else{
                            $msg = '请输入正确公司电话!';
                        }
                    }else{
                        $msg = '请输入登录名称！'; 
                    }
                }else{
                    $msg = '请填写正确的邮箱！';
                }  
            }else{
                $msg = '请输入联系人!'; 
            }
        }

        return $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
    }




    /**
     *  代理商审核
     */
    public function approve(){

        $msg = '系统错误！';
        $status = 'error';
        $data = '';

        //$where['enterprise.enterprise_id'] = array(array('in',$this->os_enterprise_ids),array('in',D('Enterprise')->enterprise_ids()),'or') ;
        //$map['_complex'] = $where;


        if(IS_POST){

            $self_id = D('SysUser')->self_id();
            $time = date("Y-m-d H:i:s",time());
            $enterprise_id = intval(I('post.enterprise_id'));
            $approve_status = intval(I('post.approve_status'));
            $approve_remark = trim(I('post.approve_remark'));

            if(in_array($approve_status,array('1','2'))){
                
                $map['enterprise.enterprise_id'][] = array('eq',$enterprise_id);
                //$map['enterprise.approve_status'] = array('eq',0);
                $enterprise = M('')
                ->table('t_flow_enterprise as enterprise')
                ->where($map)
                ->find();
                //判断当前企业的审核状态

                if($enterprise){
                    if($enterprise['approve_status'] == 0){
                    //判断自身是否为尚通管理员下面的管理员
                        if(D('SysUser')->is_top_proxy_admin()){
                            if($approve_status == 1){
                                if(!$enterprise['icense_img']){
                                    $msg = '营业执照附件不能为空！';
                                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                                }
                                if(!$enterprise['icense_img_num']){
                                    $msg = '营业执照编号不能为空！';
                                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                                }
                                /*
                                if(!$enterprise['identity_img']){
                                    $msg = '身份证图片附件不能为空！';
                                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                                }
                                if(!$enterprise['identity_img_num']){
                                    $msg = '法人身份证不能为空！';
                                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                                }
                                */
                                
                            }
                                $model = M('');
                                $model->startTrans();
                                $enterpriseedit = array(
                                    'approve_status' => $approve_status,
                                    'approve_remark' => $approve_remark,
                                    'enterprise_id' => $enterprise_id,
                                    'approve_user_id' => $self_id,
                                    'approve_date' => $time,

                                );
                                $enterprise_edit = M('Enterprise')->save($enterpriseedit);

                                $map = array();
                                $map['status'] = array('neq', 2);
                                $map['is_manager'] = array('eq', 1);
                                $map['enterprise_id'] = array('eq', $enterprise_id);
                                $user = M('Sys_user')->where($map)->find();

                                if ($enterprise_edit) {
                                    if ($approve_status == 1) {
                                        $r_msg = '审核通过';
                                        $n_msg='通过';
                                        $msg = '企业审核通过成功！';
                                        //D("Enterprise")->set_enterprise_role($enterprise_id);初始化企业权限的 
                                        $data = '企业【' . $enterprise['enterprise_name'] . '】已审核通过！<br>登录名称【' . $user['login_name_full'] . '】<br>登录密码【123456】';
                                    } else {
                                        $r_msg = '审核驳回';
                                        $msg = '企业审核驳回成功！';
                                        $n_msg='驳回';
                                        $data = '';
                                    }
                                    $status = 'success';
                                    $model->commit();
                                    $remind_content = '您提交审核的企业【' . $enterprise['enterprise_name'] . '】已经【' . $r_msg . '】，请知晓！';
                                    R('ObjectRemind/send_user', array(4, $remind_content, array($user['create_user_id'])));

                                } else {
                                    $msg = '审核失败！';
                                    $model->rollback();
                                    $n_msg='失败';
                                }
                            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$enterprise_id.'】，审核企业【'.$enterprise['enterprise_name'].'('.$enterprise['enterprise_code'].')】'.$n_msg;
                            $this->sys_log('审核企业',$note);
                        }
                    }else{
                        $msg = '对不起，不可重复审核！';
                    }

                }
            }
            return $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
        }else{
                
            
                $map['enterprise.status'] = array('neq',2);
                $map['enterprise.enterprise_id'][] = array('eq',intval(I('get.enterprise_id')));

                $enterprise = M('')
                ->table('t_flow_enterprise as enterprise')
                ->field('enterprise.enterprise_name,enterprise.enterprise_code,enterprise.email,enterprise.enterprise_id,enterprise.tel,enterprise.contact_name,enterprise.contact_tel,enterprise.operator,user.user_name,root_user.login_name_full,enterprise.approve_status,enterprise.approve_remark,enterprise.approve_date,approve_user.user_name as approve_name,enterprise.address,enterprise.icense_img,enterprise.icense_img_num,enterprise.identity_img_num,enterprise.identity_img,province.province_name,city.city_name,enterprise.create_user_id')
                ->join('left join t_flow_sys_user as user on enterprise.sale_id = user.user_id and user.status = 1')
                ->join('left join t_flow_sys_user as root_user on root_user.enterprise_id = enterprise.enterprise_id and root_user.is_manager = 1')
                ->join('left join t_flow_sys_user as approve_user on approve_user.user_id = enterprise.approve_user_id')
                ->join('t_flow_sys_province as province on province.province_id = enterprise.province','left')
                ->join('t_flow_sys_city as city on city.city_id = enterprise.city','left')
                ->where($map)
                ->find();

                if($enterprise){
                    $type = I('get.download');
                    if(in_array($type,array('icense_img','identity_img'))){
                        parent::download('.'.$enterprise[$type]);
                    }else{
                        $map = array();
                        $map['status'] = array('eq',1);
                        $map['operator_id'] = array('in',$enterprise['operator']);
                        $enterprise['operator'] = '';
                        foreach(M('Sys_operator')->where($map)->select() as $v){
                            $enterprise['operator'] .= ','.$v['operator_name'];
                        }
                        $enterprise['operator'] = substr($enterprise['operator'],'1',strlen($enterprise['operator'])-1);
                
                        $this->assign('is_top_proxy_admin',D('SysUser')->is_top_proxy_admin());
                        $this->assign('is_approve',$is_approve);

                        $this->assign('enterprise',$enterprise);
                        $this->display();
                    }

                }else{
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                }

        }
    }


    public function set_sale(){

        $msg = '系统错误！';
        $status = 'error';

        $enterprise_ids = D('Enterprise')->enterprise_ids();
                
        $where['enterprise.enterprise_id'] = array(array('in',$this->os_enterprise_ids),array('in',D('Enterprise')->enterprise_ids()),'or') ;
        $map['_complex'] = $where;
        $map['enterprise.top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
        $map['enterprise.enterprise_id'] = array('eq',intval(I('enterprise_id')));
        $map['enterprise.status'] = array('neq',2);
        $map['enterprise.approve_status'] = array('eq',1);
        $enterprise = M('')
        ->table('t_flow_enterprise as enterprise')
        ->where($map)
        ->find();

        if(IS_POST){
            if($enterprise){
                $sale_id = intval(I('post.sale_id'));
                //当设置了值的时候

                    if((D('SysUser')->is_balance($sale_id) && $sale_id ) or !$sale_id ){
                        
                            $model = M('');
                            $model->startTrans();

                        if($sale_id == $enterprise['sale_id']){
                            $is_edit = true;
                            $is_add = true;
                            $is_delete = true;
                        }else{
                            $edit = array(
                                'sale_id'           =>      $sale_id,
                                'enterprise_id'     =>      $enterprise['enterprise_id'],
                                );
                            $is_edit = M('Enterprise')->save($edit);

                            if($enterprise['sale_id']){
                                //删除原有权限表中客户经理与企业的关系
                                $map = array();
                                $map['user_id'] = array('eq',$enterprise['sale_id']);
                                $map['enterprise_id'] = array('eq',$enterprise['enterprise_id']);
                                $Enterprise_user_count = M('Enterprise_user')->where($map)->count();
                                $Enterprise_user_delete = M('Enterprise_user')->where($map)->delete();
                                if($Enterprise_user_count == $Enterprise_user_delete){
                                    $is_delete = true;
                                }else{
                                    $is_delete = false;
                                }
                            }else{

                                $is_delete = true;
                            }
                            //添加一条新的记录
                            if($sale_id){

                                $user = M('SysUser')->field('user_name,is_all_enterprise')->find($sale_id);
                                
                                if(!$user['is_all_enterprise']){
                                    $add = array(
                                        'user_id'               =>  $sale_id,
                                        'enterprise_id'         =>  $enterprise['enterprise_id'],
                                        'create_user_id'        =>  D('SysUser')->self_id(),
                                        'create_date'           =>  date("Y-m-d H:i:s",time()),
                                    );

                                    $is_add = M('Enterprise_user')->add($add);

                                }else{
                                    $is_add = true;
                                }
                                
                            }else{

                                $is_add = true;
                            }
                        }
                        if($is_edit && $is_delete && $is_add){
                            $msg = '设置客户经理成功!';
                            $status = 'success';
                            $model->commit();
                            $n_msg='成功';
                        }else{
                            $msg = '设置客户经理失败!';
                            $model->rollback();
                            $n_msg='失败';
                        }
                        $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，给企业【'.obj_name($enterprise['enterprise_id'],2).'】设置客户经理'.$n_msg;
                        $this->sys_log('企业设置客户经理',$note);
                    }

            }
             $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));

        }else{

            if($enterprise){
                $map = array();
                $map['user.is_manager'] = array('eq',0);
                $map['user.proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
                $map['user.status'] = array('eq',1);
                $user_list = M('Sys_user as user')
                ->field('user.user_name,user.user_id,depart.depart_id')
                ->join('t_flow_sys_depart as depart on depart.depart_id = user.depart_id','left')
                ->where($map)
                ->select();

                

                $map = array();
                $map['user_id'] = array('eq',D('SysUser')->root_user_id());
                $map['status'] = array('eq',1);
                $depart_list = M('Sys_depart')->where($map)->select();

                $map = array();
                $map['user_id'] = $enterprise['sale_id'];
                $map['status'] = array('neq',2);
                $user = M('Sys_user')->where($map)->find();


                $this->assign('depart_list',$depart_list);
                $this->assign('user_list',$user_list);
                $this->assign('enterprise',$enterprise);
                $this->display();

            }else{
                 $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
            
        }
   

    }
    

    public function toggle_status(){
        $msg = '系统错误!';
        $status = 'error';
        $data = array();

       
        $where['enterprise.enterprise_id'] = array(array('in',$this->os_enterprise_ids),array('in',D('Enterprise')->enterprise_ids()),'or') ;
        $map['_complex'] = $where;
        $enterprise_id = intval(I('post.enterprise_id'));

        $map['enterprise.status'] = array('neq',2);
        $map['enterprise.enterprise_id'][] = array('eq',$enterprise_id);
        $map['enterprise.approve_status'] = array('eq',1);
        if(!D('SysUser')->is_top_proxy_admin()){
            $map['enterprise.top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
        }
        //var_dump($map);exit;
        $enterprise = M('')
        ->table('t_flow_enterprise as enterprise')
        ->where($map)
        ->find();

        if($enterprise && $enterprise['approve_status'] == 1){
            //判断企业是否是自己代理商的下级  并且在自己的权限以内
            $edit = array();
            if($enterprise['status'] == 1){
                $edit['status'] = 0;
            }else{
                $edit['status'] = 1;
            }
            $edit['modify_user_id'] =   D('SysUser')->self_id();
            $edit['modify_date']    =   date("Y-m-d H:i:s",time());
            $edit['enterprise_id'] = $enterprise['enterprise_id'];

            if(M('Enterprise')->save($edit)){

                if($enterprise['status'] == 1){
                    $msg = '禁用成功!';
                }else{
                    $msg = '启用成功!';
                }
                $status = 'success';
                $data['status'] = 0;
                $n_msg='成功';
            }else{
                if($enterprise['status'] == 1){
                    $msg = '禁用失败!';
                }else{
                    $msg = '启用失败!';
                }
                $n_msg='失败';
            }
            $title = $enterprise['status'] == 1 ? "禁用" : "启用";
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$enterprise_id.'】，'.$title.'企业【'.$enterprise['enterprise_name'].'('.$enterprise['enterprise_code'].')】'.$n_msg;
            $this->sys_log($title.'企业',$note);
        }

        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
    }


    /**
     *  企业个人设置
     */
    public function set_enterprise(){
        $msg = '系统错误!';
        $status = 'error';
        if(!D('SysUser')->is_admin() && D('SysUser')->self_user_type() != 3 ){
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));    
        }
        if(IS_POST){

            $tel = trim(I('post.tel'));
            $email = trim(I('post.email'));
            $address = trim(I('post.address'));
            $contact_name = trim(I('post.contact_name'));
            $contact_tel = trim(I('post.contact_tel'));
            $province = trim(I('post.province_id'));
            $city = trim(I('post.city_id'));
            $enterprise_id=D('SysUser')->self_enterprise_id();
            $e_info=M('enterprise')->where('enterprise_id='.$enterprise_id)->find();
            //if(empty($tel) && isTel($tel)){
                //if($tel && !D('Enterprise')->check_tel($tel,D('SysUser')->self_enterprise_id())){
                    if(empty($email) or isEmail($email)){
                        if(!empty($contact_name)){
                            if(!empty($contact_tel) && isTel($contact_tel)){
                                $edit = array(
                                    'enterprise_id'     =>      $enterprise_id,
                                    'tel'               =>      $tel,
                                    'email'             =>      $email,
                                    'contact_name'      =>      $contact_name,
                                    'contact_tel'       =>      $contact_tel,
                                    'address'           =>      $address,
                                    'province'          =>      $province,
                                    'city'              =>      $city,
                                    'modify_user_id'    =>      D('SysUser')->self_id(),
                                    'modify_date'       =>      date("Y-m-d H:i:s",time())
                                    );
                                if(M('Enterprise')->save($edit)){
                                    $msg = '企业信息设置成功！';
                                    $status = 'success';
                                    $n_msg='成功';
                                }else{
                                    $msg = '企业信息设置失败!';
                                    $n_msg='失败';
                                }
                                $c_item='';
                                $c_item.=$contact_name===$e_info['contact_name']?'':'联系人【'. $contact_name.'】';
                                $fg=!empty($c_item)?'，':'';
                                $c_item.=$tel===$e_info['tel']?'':$fg.'联系电话【'. $tel.'】';
                                if($contact_tel!==$e_info['contact_tel']){
                                    $fg=!empty($c_item)?'，':'';
                                    $c_item.=empty($contact_tel)?$fg.'':$fg.'公司电话【'. $contact_tel.'】';
                                }

                                if($email!==$e_info['email']){
                                    $fg=!empty($c_item)?'，':'';
                                    $c_item.=empty($email)?$fg.'清除邮箱':$fg.'邮箱【'. $email.'】';
                                }
                                if($province!==$e_info['province']){
                                    $fg=!empty($c_item)?'，':'';
                                    if($province!='' || $province!=='0' ){
                                        $c_item.=$fg.'所属【'. get_province_name($province).'】';
                                    }else{
                                        $c_item.=$fg.'清除所属省';
                                    }
                                }
                                if($city!==$e_info['city']){
                                    $fg=!empty($c_item)?'，':'';
                                    if($city!='' || $city!=='0'){
                                        $c_item.=$fg.'所属市【'. get_city_name($city).'】';
                                    }else{
                                        $c_item.=$fg.'清除所属市';
                                    }
                                }
                                if($address!==$e_info['address']){
                                    $c_item.=empty($address)?$fg.'清除地址':$fg.'地址【'. $address.'】';
                                }
                                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$enterprise_id.'】，企业信息设置，企业【'.obj_name(D('SysUser')->self_enterprise_id(),2).'】：'.$c_item.$n_msg;
                                $this->sys_log('企业信息设置',$note);
                            }else{
                                $msg = '请输入正确的联系人电话！';
                            }
                        }else{
                            $msg = '请输入联系人！';
                        }
                    }else{
                        $msg = '请输入正确邮箱!';
                    }
                /*}else{
                    $msg = '公司电话已存在！';
                }*/
            /*}else{
                $msg = '请输入正确公司电话!';
            }*/

            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }else{

            $map['approve_status'] = array('eq',1);
            $map['status'] = array('eq',1);
            $map['enterprise_id']   = array('eq',D('SysUser')->self_enterprise_id());
            $enterprise = M('Enterprise')->where($map)->find();
            if($enterprise){
                $map = array();
                $map['province_id'] = array('neq',1);
                $province_list = M('Sys_province')->where($map)->select();
                $this->assign('province_list',$province_list);

                $city_list = M('Sys_city')->select();
                $this->assign('city_list',$city_list);


                $this->assign('enterprise',$enterprise);
                $this->display();
            }else{
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
            
        }
    }



    /**
     *  审核证件修改模板
     */
    public function approve_credentials_edit(){

        $msg = '系统错误！';
        $status = 'error';
        //$where['enterprise_id'] = array(array('in',$this->os_enterprise_ids),array('in',D('Enterprise')->enterprise_ids()),'or') ;
        //$map['_complex'] = $where;
        $enterprise_id = trim(intval(I('get.enterprise_id')));
        $map['enterprise_id'] = array('eq',$enterprise_id);
        $map['status'] = array('neq',2);
        $map['approve_status'] = array('neq',1);
        $enterprise = M('Enterprise')->where($map)->find();
        if($enterprise){

            $type = I('get.download');
            if(in_array($type,array('icense_img','identity_img'))){
                 parent::download('.'.$enterprise[$type]);
            }else{
                $this->assign('enterprise',$enterprise);
                $this->display();
            }
        }else{
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }



    /**
     *  证件修改模板
     */
    public function credentials_edit(){

        $msg = '系统错误！';
        $status = 'error';
        $where['enterprise_id'] = array(array('in',$this->os_enterprise_ids),array('in',D('Enterprise')->enterprise_ids()),'or') ;
        $map['_complex'] = $where;
        
        $enterprise_id = intval(I('get.enterprise_id'));
        $map['enterprise_id'] = array('eq',$enterprise_id);
        $map['status'] = array('neq',2);

        if(D('SysUser')->is_top_proxy_admin()){
            $map['approve_status'] = array('eq',1);
        }else{
            $map['approve_status'] = array('neq',1);
        }
        $enterprise = M('Enterprise')->where($map)->find();

        if($enterprise){
            $type = I('get.download');
            if(in_array($type,array('icense_img','identity_img'))){
                parent::download('.'.$enterprise[$type]);
            }else{
                $this->assign('enterprise',$enterprise);
                $this->display();
            }

        }else{
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }


    /**
     *  审核证件修改
     */
    public function approve_credentials_update(){
        $msg = '系统错误！';
        $status = 'error';
        $icense_img_num = trim(I('post.icense_img_num'));
        $identity_img_num = trim(I('post.identity_img_num'));
        $enterprise_id = I('post.enterprise_id');
        //$where['enterprise_id'] = array(array('in',$this->os_enterprise_ids),array('in',D('Enterprise')->enterprise_ids()),'or') ;
        //$map['_complex'] = $where;
        $map['enterprise_id'] = array('eq',$enterprise_id);
        $map['approve_status'] = array('neq',1);
        $enterprise = M('Enterprise')->where($map)->find();
        if(!$enterprise){
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
        if( empty($icense_img_num)){
            $msg = '请输入营业执照编号！';
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }

        $fileinfo = $this->business_licence_upload(C('PROXY_UPLOAD_DIR'));
        $error = $this->business_licence_upload_Error;
        if($error){
            if($error['icense_img'] && $error['icense_img'] != '没有文件被上传！'){
                $msg = '营业执照附件'.$error['icense_img'];
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
            if($error['identity_img'] && $error['identity_img'] != '没有文件被上传！'){
                $msg = '身份证附件'.$error['identity_img'];
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
        }

        if($fileinfo['icense_img']){
            $icense_img = substr(C('UPLOAD_DIR').$fileinfo['icense_img']['savepath'].$fileinfo['icense_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['icense_img']['savepath'].$fileinfo['icense_img']['savename'])-1);
        }else{
            $icense_img = '';
        }
        if($fileinfo['identity_img']){
            $identity_img = substr(C('UPLOAD_DIR').$fileinfo['identity_img']['savepath'].$fileinfo['identity_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['identity_img']['savepath'].$fileinfo['identity_img']['savename'])-1);
        }else{
            $identity_img = '';
        }

        if( $enterprise['icense_img'] == '' && !$fileinfo['icense_img']){
            $msg = '请上传营业执照附件！';
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }

        $edit = array(
            'icense_img_num'        =>      $icense_img_num,
            'identity_img_num'      =>      $identity_img_num,
            'enterprise_id'         =>      $enterprise_id,
            'modify_user_id'        =>      D('SysUser')->self_id(),
            'modify_date'           =>      date("Y-m-d H:i:s",time()),
            );

        if($fileinfo['icense_img']){
            $edit['icense_img'] = $icense_img;
        }
        if($fileinfo['identity_img']){
            $edit['identity_img'] = $identity_img;
        }

        if(M('Enterprise')->save($edit)){
            $msg = '企业证件保存成功！';
            $status = 'success';
            $n_msg='成功';
        }else{
            $msg = '企业证件保存失败！';
            $n_msg='失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$enterprise_id.'】，企业【'.obj_name($enterprise_id,2).'】证件保存'.$n_msg;
        $this->sys_log('保存企业证件',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }




    /**
     *  审核证件修改
     */
    public function credentials_update(){
        $msg = '系统错误！';
        $status = 'error';
        $icense_img_num = trim(I('post.icense_img_num'));
        $identity_img_num = trim(I('post.identity_img_num'));
        $enterprise_id = I('post.enterprise_id');
        $where['enterprise_id'] = array(array('in',$this->os_enterprise_ids),array('in',D('Enterprise')->enterprise_ids()),'or') ;
        $map['_complex'] = $where;
        $map['enterprise_id'] = array('eq',$enterprise_id);
        if(D('SysUser')->is_top_proxy_admin()){
            $map['approve_status'] = array('eq',1);
        }else{
            $map['approve_status'] = array('neq',1);
        }
        $enterprise = M('Enterprise')->where($map)->find();
        if(!$enterprise){
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
        if( empty($icense_img_num)){
            $msg = '请输入营业执照编号！';
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }


        $fileinfo = $this->business_licence_upload(C('PROXY_UPLOAD_DIR'));
        $error = $this->business_licence_upload_Error;
        if($error){
            if($error['icense_img'] && $error['icense_img'] != '没有文件被上传！'){
                $msg = '营业执照附件'.$error['icense_img'];
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
            if($error['identity_img'] && $error['identity_img'] != '没有文件被上传！'){
                $msg = '身份证附件'.$error['identity_img'];
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
        }

        if($fileinfo['icense_img']){
            $icense_img = substr(C('UPLOAD_DIR').$fileinfo['icense_img']['savepath'].$fileinfo['icense_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['icense_img']['savepath'].$fileinfo['icense_img']['savename'])-1);
        }else{
            $icense_img = '';
        }
        if($fileinfo['identity_img']){
            $identity_img = substr(C('UPLOAD_DIR').$fileinfo['identity_img']['savepath'].$fileinfo['identity_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['identity_img']['savepath'].$fileinfo['identity_img']['savename'])-1);
        }else{
            $identity_img = '';
        }

        if( $enterprise['icense_img'] == '' && !$fileinfo['icense_img']){
            $msg = '请上传营业执照附件！';
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }

        $edit = array(
            'icense_img_num'        =>      $icense_img_num,
            'identity_img_num'      =>      $identity_img_num,
            'enterprise_id'         =>      $enterprise_id,
            'modify_user_id'        =>      D('SysUser')->self_id(),
            'modify_date'           =>      date("Y-m-d H:i:s",time()),
            );

        if($fileinfo['icense_img']){
            $edit['icense_img'] = $icense_img;
        }
        if($fileinfo['identity_img']){
            $edit['identity_img'] = $identity_img;
        }

        if(M('Enterprise')->save($edit)){
            $msg = '企业证件保存成功！';
            $status = 'success';
            $n_msg='成功';
        }else{
            $msg = '企业证件保存失败！';
            $n_msg='失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$enterprise_id.'】，企业【'.obj_name($enterprise_id,2).'】证件修改'.$n_msg;
        $this->sys_log('企业证件修改',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }


    /**
     *  审核证件查看
     */
    public function approve_credentials_index(){
        $msg = '系统错误！';
        $status = 'error';
        
        $enterprise_id = intval(I('enterprise_id'));
        $where['enterprise_id'] = array(array('in',$this->os_enterprise_ids),array('in',D('Enterprise')->enterprise_ids()),'or') ;
        $map['_complex'] = $where;

        $map['enterprise_id'] = array('eq',$enterprise_id);
        $map['status'] = array('neq',2);

        $enterprise = M('Enterprise')->where($map)->find();
        if($enterprise){
            $type = I('get.download');
            if(in_array($type,array('icense_img','identity_img'))){
                 parent::download('.'.$enterprise[$type]);
            }else{
                $this->assign('enterprise',$enterprise);
                $this->display();
            }
        }

    }



    /**
     *  企业权限分配
     */
    public function set_enterprise_user(){
        $msg = '系统错误!';
        $status = 'error';
        $data = array();
        if(IS_POST){
            $user_id = I('post.user_id');
            $enterprise_ids = I('post.enterprise_ids');
            $type = I('post.type');
            if(in_array($type,array('add','delete'))){

                if($enterprise_ids){

                    if(!is_array($enterprise_ids)){
                        $enterprise_array = explode(',',$enterprise_ids);

                    }else{
                        $enterprise_array = $enterprise_ids;
                        $enterprise_ids = implode(',',$enterprise_ids);
                    }
                    foreach($enterprise_array as $k=>$v){
                        if(!intval($v)){
                            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                        }else{
                            $enterprise_array[$k] = intval($v);
                        }
                    }
                    $user = M('Sys_user')->find($user_id);

                    if($user['user_id'] && $user['is_all_enterprise'] == '0' && D('SysUser')->is_balance($user_id)){
                        //判断id序列是否在都在自己的管辖内
                        $map['enterprise_id'] = array('in',$enterprise_ids);
                        $map['status'] = array('neq',2);
                        $map['top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());

                        if(intval(M('Enterprise')->where($map)->count()) === count($enterprise_array) ){

                            $model = M('');
                            $model->startTrans();
                            if($type == 'add'){
                                //删除部分数据 防止数据重复
                                $delete = D('Enterprise')->delete_section($user_id,$enterprise_ids);

                                //执行添加
                                $add_array = array();
                                foreach($enterprise_array as $k=>$v){
                                    $add_array[] = array(
                                        'user_id'       =>      $user_id,
                                        'enterprise_id' =>      $v,
                                        'create_user_id'=>      D('SysUser')->self_id(),
                                        'create_date'   =>      date("Y-m-d H:i:s",time())
                                        );
                                }
                                $add = M('Enterprise_user')->addAll($add_array);

                                $edit = true;
                                
                            }elseif($type == 'delete'){
                                //删除部分数据
                                $delete = D('Enterprise')->delete_section($user_id,$enterprise_ids);

                                $add = true;

                                //判断删除的数据中是否有自己是客户
                                $map = array();
                                $map['status'] = array('neq',2);
                                $map['enterprise_id'] = array('in',$enterprise_ids);
                                $map['sale_id'] = array('eq',$user_id);
                                $edit_count = M('Enterprise')->where($map)->count();
                                if($edit_count){
                                    $edit_array = array(
                                    'sale_id'           => '',
                                    'modify_user_id'    =>  D('SysUser')->self_id(),
                                    'modify_date'       =>   date("Y-m-d H:i:s",time())
                                    );
                                    $edit = M('Enterprise')->where($map)->save($edit_array);
                                }else{
                                    $edit = true;
                                }

                            }

                            if($delete && $add  && $edit){
                                if($type == 'add'){
                                    $msg = '分配成功！';
                                }else{
                                    $msg = '解除成功！';
                                }
                                $n_msg='成功';
                                $status = 'success';
                                $model->commit();
                            }else{
                                if($type == 'add'){
                                    $msg = '分配失败！';
                                }else{
                                    $msg = '解除失败！';
                                }
                                $model->rollback();
                                $n_msg='失败';
                            }
                            $status_name = $type == 'add' ? "分配" : "解除";
                            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，为员工【'.$user['user_name'].'】'.$status_name.'企业权限'.$n_msg;
                            $this->sys_log($status_name.'企业权限',$note);
                        }
                    }
                }
            }else{
                if(in_array($type,array('alldelete','alladd'))){
                    $user = M('Sys_user')->find($user_id);
                    if($type == 'alldelete'){
                        if($user['user_id'] && $user['is_all_enterprise'] == '1' && D('SysUser')->is_balance($user['user_id'])){
                            $edit = array(
                                'user_id'               =>      $user_id,
                                'is_all_enterprise'     =>      0, 
                                );
                            if(M('Sys_user')->save($edit)){
                                $msg = '为员工【'.$user['user_name'].'】解除所有企业权限成功！';
                                $status ='success';
                                $n_msg='成功';
                            }else{
                                $msg = '解除所有企业权限失败！';
                                $n_msg='失败';
                            }
                            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，为员工【'.$user['user_name'].'】解除所有企业权限'.$n_msg;
                            $this->sys_log('解除所有企业权限',$note);
                        }
                    }elseif($type == 'alladd'){

                        if($user['user_id'] && $user['is_all_enterprise'] == '0' && D('SysUser')->is_balance($user['user_id'])){
                            $model = M('');
                            $model->startTrans();

                            $edit = array(
                                'user_id'               =>      $user_id,
                                'is_all_enterprise'     =>      1, 
                                );
                            $edit = M('Sys_user')->save($edit);
                            
                            $map = array();
                            $map['user_id'] = array('eq',$user['user_id']);
                            $count = M('Enterprise_user')->where($map)->count();

                            if($count){
                                $deletecount = M('Enterprise_user')->where($map)->delete();

                                if($count ==  $deletecount){
                                    $delete = true;
                                }else{
                                    $delete = false;
                                }
                            }else{
                                $delete = true;
                            }
                            
                            if($edit && $delete){
                                $msg = '为员工【'.$user['user_name'].'】分配所有企业权限成功！';
                                $status ='success';
                                $model->commit();
                                $n_msg='成功';
                            }else{
                                $msg = '分配所有企业权限失败！';
                                $model->rollback();
                                $n_msg='失败';
                            }
                            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，为员工【'.$user['user_name'].'】分配所有企业权限'.$n_msg;
                            $this->sys_log('分配所有企业权限',$note);
                        }
                    }  
                }
            }
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }else{
            $type = I('get.type');
            $user_id = I('get.user_id');
            if($type !== 'getlist'){

                $map['user.proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
                $map['user.is_manager'] = array('eq',0);
                $map['user.status'] = array('eq',1);
                $user_list = M('Sys_user as user')
                ->field("user.*,depart.depart_name")
                ->join('t_flow_sys_depart as depart on user.depart_id = depart.depart_id','left')
                ->where($map)
                ->select();
                
                if($user_list){
                    foreach($user_list as $k=>$v){
                        if($user_id == $v['user_id']){
                            $user = $v;
                        }
                    }
                    /*
                    if(!$user){
                        $user = $user_list[0];
                    }
                    */
                }

                if($user['user_id'] && D('SysUser')->is_balance($user['user_id'])){
                    if($user['is_all_enterprise'] == '1' ){
                         $this->assign('user_list', get_sort_no($user_list,0));
                         $data['msg'] = '用户【'.$user['user_name'].'】拥有所有企业权限！';
                         $data['status'] = 'success';
                         $data['is_all_enterprise'] = 1;
                         $this->assign('jsondata',json_encode($data));
                         $this->assign('data',$data);
                         $this->assign('user',$user);
                         $this->display();
                         exit;
                    }
                    $map = array();
                    $map['enterprise.status'] = array('neq',2);
                    $map['enterprise.top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
                    $enterpriselist = M('')
                    ->table('t_flow_enterprise as enterprise')
                    ->field("enterprise.enterprise_id,enterprise.enterprise_name,enterprise.enterprise_code")
                    ->where($map)
                    ->select();

                    if($enterpriselist){
                        $map = array();
                        $map['user_id'] = array('eq',$user['user_id']);
                        $enterprise_ids = M('Enterprise_user')->field('enterprise_id')->where($map)->select();
                        if($enterprise_ids){
                            foreach($enterprise_ids as $k=>$v){
                                $ids[] = $v['enterprise_id'];
                            }
                            foreach($enterpriselist as $k=>$v){
                                if( in_array($v['enterprise_id'],$ids) ){
                                    $enterprise_list['have'][] = $v;
                                }else{
                                    $enterprise_list['no'][] = $v;
                                }
                            }
                            if(!$enterprise_list['no']){
                                $enterprise_list['no'] = array();
                            }

                        }else{
                            $enterprise_list['no'] = $enterpriselist;
                            $enterprise_list['have'] = array();
                        }
                        
                    }else{
                        $enterprise_list['have'] = array();
                        $enterprise_list['no'] = array();
                    }

                    $enterprise_list['have'] = get_sort_no($enterprise_list['have'],0);
                    $enterprise_list['no'] = get_sort_no($enterprise_list['no'],0);
                    $data = $enterprise_list;
                    $data['is_all_enterprise'] = '0';
                    $this->assign('data',$data);
                    $this->assign('user',$user);
                    $this->assign('user_list', get_sort_no($user_list,0));
                    $this->display();
                }else{
                    $this->assign('user_list', get_sort_no($user_list,0));
                    $this->display();
                }
                
            }else{

                $user_id = intval(I('get.user_id'));
                $user = M('SysUser')->find($user_id);
                if($user['is_all_enterprise'] == 1){
                     $msg = '员工【'.$user['user_name'].'】拥有所有企业权限！';
                     $status = 'success';
                     $proxy_list['have'] = array();
                     $proxy_list['no'] = array();
                     $data = $proxy_list;
                     $data['is_all_enterprise'] = 1;
                     $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                     exit;
                }
                if($user['user_id']  && $user['is_all_enterprise'] == '0' && D('SysUser')->is_balance($user_id)){
                    $map = array();
                    $map['enterprise.status'] = array('neq',2);
                    $map['enterprise.top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
                    $enterpriselist = M('')
                    ->table('t_flow_enterprise as enterprise')
                    ->field("enterprise.enterprise_id,enterprise.enterprise_name,enterprise.enterprise_code")
                    ->where($map)
                    ->select();

                    if($enterpriselist){
                        $map = array();
                        $map['user_id'] = array('eq',$user_id);
                        $enterprise_ids = M('Enterprise_user')->field('enterprise_id')->where($map)->select();
                        if($enterprise_ids){
                            foreach($enterprise_ids as $k=>$v){
                                $ids[] = $v['enterprise_id'];
                            }
                            foreach($enterpriselist as $k=>$v){
                                if( in_array($v['enterprise_id'],$ids) ){
                                    $enterprise_list['have'][] = $v;
                                }else{
                                    $enterprise_list['no'][] = $v;
                                }
                            }
                            if(!$enterprise_list['no']){
                                $enterprise_list['no'] = array();
                            }

                        }else{
                            $enterprise_list['no'] = $enterpriselist;
                            $enterprise_list['have'] = array();
                        }
                        
                    }else{
                        $enterprise_list['have'] = array();
                        $enterprise_list['no'] = array();
                    }

                    $msg = '';
                    $status = 'success';
                    $enterprise_list['have'] = get_sort_no($enterprise_list['have'],0);
                    $enterprise_list['no'] = get_sort_no($enterprise_list['no'],0);
                    $data = $enterprise_list;
                    $data['is_all_enterprise'] = $user['is_all_enterprise'];
                }
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
            }
            
        }
        
    }



        /**
     *  重置下级代理商的密码
     */
    public function reset_password(){
        $msg= '系统错误!';
        $status = 'error';
        $data = '';

        //判断用户的id是否在管辖内
        $enterprise_id  = I('post.enterprise_id');
        $map['status'] = array('neq',2);
        //获取企业数据的权限
        $where['enterprise_id'] = array(array('in',D('Enterprise')->enterprise_child_ids()),array('in',D('Enterprise')->enterprise_ids()),'or');
        $map['_complex'] = $where;
        $map['enterprise_id'] = array('eq',$enterprise_id);
        $map['approve_status'] = array('eq',1);
        $enterprise = M('Enterprise')->where($map)->find();

        if($enterprise){
            $map = array();
            $map['is_manager'] = array('eq',1);
            $map['enterprise_id'] = array('eq',$enterprise['enterprise_id']);
            $user = M('Sys_user')->where($map)->find();
            if($user){
                $pass = rand(100000,999999);
                $edit = array(
                    'user_id'           =>      $user['user_id'],
                    'login_pass'        =>      md5($pass),
                    'modify_user_id'    =>      D('SysUser')->self_id(),
                    'modify_date'       =>      date("Y-m-d H:i:s",time())
                    );
                if(M('Sys_user')->save($edit)){
                    $msg = '密码重置成功';
                    $status = 'success';
                    $data = '重置企业【'.$enterprise['enterprise_name'].'】登录密码成功！<br>登录名【'.$user['login_name_full'].'】<br>新密码【'.$pass.'】';
                    $n_msg='成功';
                }else{
                    $msg = '重置失败！';
                    $n_msg='失败';
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$enterprise_id.'】，重置企业【'.$enterprise['enterprise_name'].'】登录密码'.$n_msg;
                $this->sys_log('重置企业登录密码',$note);
            }
        }
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
    }




  
    /**
     *  企业详情
     */
    public function show(){
        $msg = '系统错误!';
        $status = 'error';
     /*   $where['enterprise.enterprise_id'] = array(array('in',D('Enterprise')->enterprise_child_ids()),array('in',D('Enterprise')->enterprise_ids()),'or');
        $map['_complex'] = $where;*/
        $enterprise_id = intval(I('get.enterprise_id'));
        $map['enterprise.status'] = array('neq',2);
        $map['enterprise.enterprise_id'][] = array('eq',$enterprise_id);
        $enterprise = M('Enterprise as enterprise')
        ->field('enterprise.enterprise_id,enterprise.enterprise_code,enterprise.enterprise_name,enterprise.tel,enterprise.contact_name,enterprise.contact_tel,enterprise.email,enterprise.operator,enterprise.address,enterprise.status,enterprise.icense_img,enterprise.approve_status,enterprise.approve_date,enterprise.approve_remark,approve_user.user_name as approve_name,sale_user.user_name as sale_name,sale_user.mobile as sale_mobile,top_proxy.proxy_name as top_name,top_proxy.proxy_code as top_code,top_proxy.tel as top_tel,top_proxy.contact_name as top_contact_name,top_proxy.address as top_address,account.account_balance,account.freeze_money,admin_user.user_name as admin_name,admin_user.login_name_full as admin_login_name,admin_user.mobile as admin_mobile,admin_user.email as admin_email,enterprise.icense_img,enterprise.icense_img_num,enterprise.identity_img_num,enterprise.identity_img,city.city_name,province.province_name')
        ->join('left join t_flow_sys_user as sale_user on sale_user.user_id = enterprise.sale_id and sale_user.status = 1')
        ->join('left join t_flow_sys_user as approve_user on approve_user.user_id = enterprise.approve_user_id and approve_user.status = 1')
        ->join('left join t_flow_proxy as top_proxy on top_proxy.proxy_id = enterprise.top_proxy_id and top_proxy.status = 1')
        ->join('left join t_flow_enterprise_account as account on account.enterprise_id = enterprise.enterprise_id')
        ->join('left join t_flow_sys_user as admin_user on admin_user.enterprise_id = enterprise.enterprise_id and admin_user.is_manager = 1')
        ->join('t_flow_sys_province as province on province.province_id = enterprise.province','left')
        ->join('t_flow_sys_city as city on city.city_id = enterprise.city','left')
        ->where($map)->find();
        $m=M('Enterprise as enterprise')->getLastSql();
        if($enterprise){
            $type = I('get.download');
            if(in_array($type,array('icense_img','identity_img'))){
                parent::download('.'.$enterprise[$type]);
            }else{
                $map = array();
                $map['status'] = array('eq',1);
                $map['operator_id'] = array('in',$enterprise['operator']);
                $enterprise['operator'] = '';
                foreach(M('Sys_operator')->where($map)->select() as $v){
                    $enterprise['operator'] .= ','.$v['operator_name'];
                }
                $enterprise['operator'] = substr($enterprise['operator'],'1',strlen($enterprise['operator'])-1);
                $this->assign('enterprise',$enterprise);
                $this->display();
            }
        }else{
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }

    public function approve_index(){
        D("SysUser")->sessionwriteclose();
        $enterprise_code = trim(I('get.enterprise_code'));    
        $enterprise_name = trim(I('get.enterprise_name'));
        $approve_status = trim(I('get.approve_status'));
        $create_start_datetime = trim(I('get.create_start_datetime'));
        $create_end_datetime = trim(I('get.create_end_datetime'));
        $approve_start_datetime = trim(I('get.approve_start_datetime'));
        $approve_end_datetime = trim(I('get.approve_end_datetime'));
        if(trim(I('get.approve_user'))){
            $map['approve_user.user_name'] =  array('like','%'.trim(I('get.approve_user')).'%');
        }
        if(trim(I('get.create_user'))){
            $map['create_user.user_name'] =   array('like','%'.trim(I('get.create_user')).'%');
        }
        //var_dump($create_user);exit;
        if($enterprise_code){
            $map['enterprise.enterprise_code'] = array('like','%'.$enterprise_code.'%');
        }
        if($enterprise_name){
            $map['enterprise.enterprise_name'] = array('like','%'.$enterprise_name.'%');
        }
        
        if($approve_status == '0' or $approve_status == '1' or $approve_status=='2'){
            $map['enterprise.approve_status'] = array('eq',$approve_status);
        }elseif($approve_status == ''){
            $_GET['approve_status'] = 0;
            $map['enterprise.approve_status'] = array('eq',0);
        }

        if($create_start_datetime or $create_end_datetime){
            if($create_start_datetime && $create_end_datetime){
                $map['enterprise.create_date']  = array('between',array($create_start_datetime,$create_end_datetime));
            }elseif($create_start_datetime){
                $map['enterprise.create_date'] = array('EGT',$create_start_datetime);
            }elseif($create_end_datetime){
                $map['enterprise.create_date'] = array('ELT',$create_end_datetime);
            }
        }

        if($approve_start_datetime or $approve_end_datetime){
            if($approve_start_datetime && $approve_end_datetime){
                $map['enterprise.approve_date']  = array('between',array($approve_start_datetime,$approve_end_datetime));
            }elseif($approve_start_datetime){
                $map['enterprise.approve_date'] = array('EGT',$approve_start_datetime);
            }elseif($approve_end_datetime){
                $map['enterprise.approve_date'] = array('ELT',$approve_end_datetime);
            }
        }

        $map['enterprise.status'] = array('neq',2);


        $map_status['enterprise.enterprise_id'] = array(array('in',D('Enterprise')->enterprise_child_ids()),array('in',D('Enterprise')->enterprise_ids()),'or');
        $map_status_or['_complex'] = $map_status;
        $map_status_or['_logic'] = 'and';
        $map_status_or['enterprise.approve_status'] = array('eq',1);
        $where['_complex'] = $map_status_or;
        $where['_logic'] = 'or';
        $where['enterprise.approve_status'] = array('neq',1);
        $map['_complex'] = $where;
        //var_dump($map);exit;
        $model = M('');

        $count = $model
            ->table('t_flow_enterprise as enterprise')
            ->field('enterprise.*,create_user.user_name as create_name,approve_user.user_name as approve_name')
            ->join('t_flow_sys_user as create_user on create_user.user_id = enterprise.create_user_id','left')
            ->join('t_flow_sys_user as approve_user on approve_user.user_id = enterprise.approve_user_id','left')
            ->where($map)
            ->count();

        $Page       = new Page($count,20);
        $show       = $Page->show();


        $enterprise_list = $model
        ->table('t_flow_enterprise as enterprise')
        ->field('enterprise.*,create_user.user_name as create_name,approve_user.user_name as approve_name')
        ->join('t_flow_sys_user as create_user on create_user.user_id = enterprise.create_user_id','left')
        ->join('t_flow_sys_user as approve_user on approve_user.user_id = enterprise.approve_user_id','left')
        ->limit($Page->firstRow.','.$Page->listRows)
        ->order('enterprise.modify_date Desc')
        ->where($map)
        ->select();



        $this->assign('page',$show);
        $this->assign('enterprise_list', get_sort_no($enterprise_list, $Page->firstRow));
        $this->display();

    }

    public function approve_edit(){
        $msg = '系统错误!';
        $status = 'error';

        $map['enterprise.status'] = array('neq',2);
        $map['enterprise.enterprise_id'] = array('eq',intval(I('get.enterprise_id')));
        $map['enterprise.approve_status'] = array('neq',1);
        $enterprise = M('')
        ->table('t_flow_enterprise as enterprise')
        ->where($map)
        ->find();

        if($enterprise){

            //计算自身的支持运营商
            $me_proxy = M('Proxy')->find($enterprise['top_proxy_id']);
            $map = array();
            $map['operator_id'] = array('in',$me_proxy['operator']);
            $operator_list = M('Sys_operator')->where($map)->select();

            $map = array();
            $map['province_id'] = array('neq',1);
            $province_list = M('Sys_province')->where($map)->select();
            $this->assign('province_list',$province_list);

            $city_list = M('Sys_city')->select();
            $this->assign('city_list',$city_list);

            $this->assign('is_top_proxy_admin',D('SysUser')->is_top_proxy_admin());
            $this->assign('is_approve',true);
            $this->assign('operator_list',$operator_list);
            $this->assign('enterprise',$enterprise);
            $this->display();

        }else{

            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }

    public function approve_update(){
        $msg = '系统错误!';
        $status = 'error';

        if(IS_POST){
            $tel = trim(I('post.tel'));
            $contact_tel = trim(I('post.contact_tel'));
            $contact_name = trim(I('post.contact_name'));
            $enterprise_name = trim(I('post.enterprise_name'));
            $operator_list = I('post.operator_list');
            $address = trim(I('post.address'));
            $enterprise_id = I('post.enterprise_id');
            $email = trim(I('post.email'));
            $operator = implode(',',$operator_list);
            $province = trim(I('post.province_id'));
            $city = trim(I('post.city_id'));
            if(empty($tel) or isTel($tel)){
                if(empty($email) or isEmail($email)){
                    if(!empty($operator)){
                        if(!empty($enterprise_name)){
                            if(!empty($contact_name)){
                                if(!empty($contact_tel)){
                                    if(isTel($contact_tel)){
                                        if(!D('Enterprise')->check_enterprise_name($enterprise_name,$enterprise_id)){
                                            $map['enterprise.status'] = array('neq',2);
                                            $map['enterprise.enterprise_id'][] = array('eq',$enterprise_id);
                                            $map['enterprise.approve_status'] = array('neq',1);
                                            $enterprise = M('Enterprise as enterprise')->where($map)->find();
                                            if($enterprise){
                                                $edit = array(
                                                    'tel'               =>      $tel,
                                                    'enterprise_name'   =>      $enterprise_name,
                                                    'address'           =>      $address,
                                                    'province'          =>      $province,
                                                    'city'              =>      $city,
                                                    'enterprise_id'     =>      $enterprise_id,
                                                    'operator'          =>      $operator,
                                                    'email'             =>      $email,
                                                    'contact_tel'       =>      $contact_tel,
                                                    'contact_name'      =>      $contact_name,
                                                    'modify_user_id'    =>      D('SysUser')->self_id(),
                                                    'modify_date'       =>      date("Y-m-d H:i:s",time()),
                                                    );

                                                if(M('Enterprise')->save($edit)){
                                                    $msg = '编辑企业成功！';
                                                    $status = 'success';
                                                    $n_msg='成功';
                                                }else{
                                                    $msg = '编辑企业失败!';
                                                    $n_msg='失败';
                                                }
                                                $c_item='';
                                                $c_item.=$enterprise_name===$enterprise['enterprise_name']?'':'企业名称【'. $enterprise_name.'】';
                                                $fg=!empty($c_item)?'，':'';
                                                $c_item.=$contact_name===$enterprise['contact_name']?'':$fg.'联系人【'. $contact_name.'】';
                                                $fg=!empty($c_item)?'，':'';
                                                $c_item.=$tel===$enterprise['tel']?'':$fg.'联系电话【'. $tel.'】';
                                                if($contact_tel!==$enterprise['contact_tel']){
                                                    $fg=!empty($c_item)?'，':'';
                                                    $c_item.=empty($contact_tel)?$fg.'清除公司电话':$fg.'公司电话【'. $contact_tel.'】';
                                                }
                                                if($email!==$enterprise['email']){
                                                    $fg=!empty($c_item)?'，':'';
                                                    $c_item.=empty($email)?$fg.'清除邮箱':$fg.'邮箱【'. $email.'】';
                                                }
                                                if($province!==$enterprise['province']){
                                                    $fg=!empty($c_item)?'，':'';
                                                    if($province!='' || $province!=='0' ){
                                                        $c_item.=$fg.'所属省【'. get_province_name($province).'】';
                                                    }else{
                                                        $c_item.=$fg.'清除所属省';
                                                    }
                                                }
                                                if($city!==$enterprise['city']){
                                                    $fg=!empty($c_item)?'，':'';
                                                    if($city!='' || $city!=='0'){
                                                        $c_item.=$fg.'所属市【'. get_city_name($city).'】';
                                                    }else{
                                                        $c_item.=$fg.'清除所属市';
                                                    }
                                                }
                                                if($address!==$enterprise['address']){
                                                    $fg=!empty($c_item)?'，':'';
                                                    $c_item.=empty($address)?$fg.'清除地址':$fg.'地址【'. $address.'】';
                                                }

                                                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$enterprise_id.'】，编辑企业【'.$enterprise['enterprise_name'].'('.$enterprise['enterprise_code'].')】：'.$c_item.$n_msg;
                                                $this->sys_log('编辑企业',$note);
                                            }
                                        }else{
                                            $msg = '企业名称已存在！';
                                        }
                                    }else{
                                        $msg = '请填写正确的联系人电话';
                                    }
                                }else{
                                    $msg = '请填写联系人电话！';
                                }
                            }else{
                                $msg = '请填写联系人！';
                            }
                        }else{
                            $msg = '请输入企业名称!';
                        }
                    }else{
                        $msg = '请勾选支持运营商！';
                    }
                }else{
                    $msg = '请输入正确的邮箱！';
                }
            }else{
                $msg = '请输入正确公司电话！';
            }
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }
    /**
     *  重新提交企业申请审核
     */
    public function approve_again(){
        $msg = '系统错误!';
        $status = 'error';

        if(IS_POST){
            $enterprise_id = intval(I('post.enterprise_id'));
            $map['enterprise_id']= array('eq',$enterprise_id);
            $map['status'] = array('neq',2);
            $map['approve_status'] = array('eq',2);
            $enterprise = M('Enterprise')->where($map)->find();
            if($enterprise){
                $edit = array(
                    'enterprise_id'         =>      $enterprise_id,
                    'approve_status'        =>      0,
                    'approve_user_id'       =>      '',
                    'approve_date'          =>      '',
                    'approve_remark'        =>      '',
                    'modify_user_id'        =>      D('SysUser')->self_id(),
                    'modify_date'           =>      date("Y-m-d H:i:s",time()),
                    );

                if(M('Enterprise')->save($edit)){
                    $msg = '重新提交审核申请成功！请耐心等待审核...';
                    $status = 'success';
                    //R('ObjectRemind/send_user',array(6,'新增【'.$enterprise['enterprise_name'].'】企业信息已重新送审，请注意审核！',array($enterprise['approve_user_id'])));
                    $n_msg='成功';
                }else{
                    $msg = '重新提交失败！';
                    $n_msg='失败';
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$enterprise_id.'】，企业【'.obj_name($enterprise_id,2).'】重新提交审核申请'.$n_msg;
                $this->sys_log('企业重新提交审核申请',$note);
            }
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }

        /** 
     *  删除驳回的企业
     */

    public function delete(){
        $msg = '系统错误!';
        $status = 'error';
        $model = M('');
        $model->startTrans();
        $enterprise_id=intval(I('post.enterprise_id'));
        $map['enterprise_id'] = $enterprise_id;
        $map['approve_status'] = array('neq',1);
        $enterprise_edit = array(
            'status'            =>      2,
            'modify_user_id'    =>      D('SysUser')->self_id(),
            'modify_date'       =>      date("Y-m-d H:i:s",time()),
            );
        $enterpriseinfo = M('Enterprise')->where($map)->find();
        $enterpriseedit = M('Enterprise')->where($map)->save($enterprise_edit);
        $map = array();
        $map['enterprise_id'] = array('eq',intval(I('post.enterprise_id')));
        $user_edit = array(
            'status'        =>      2
            );
        $useredit = M('Sys_user')->where($map)->save($user_edit);
        if($enterpriseedit && $useredit){
            $msg = '删除企业成功！';
            $status = 'success';
            $model->commit();
            $n_msg='成功';
        }else{
            $msg = '删除企业失败！';
            $model->rollback();
            $n_msg='失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$enterprise_id.'】，删除企业【'.$enterpriseinfo['enterprise_name'].'('.$enterpriseinfo['enterprise_code'].')】'.$n_msg;
        $this->sys_log('删除企业',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));

    }


        /** 
     *  删除驳回的企业
     */

    public function approve_delete(){
        $msg = '系统错误!';
        $status = 'error';
        $model = M('');
        $model->startTrans();
        $enterprise_id=intval(I('post.enterprise_id'));
        $map['enterprise_id'] = $enterprise_id;
        $map['approve_status'] = array('neq',1);
        $enterprise_edit = array(
            'status'            =>      2,
            'modify_user_id'    =>      D('SysUser')->self_id(),
            'modify_date'       =>      date("Y-m-d H:i:s",time()),
            );
        $enterpriseinfo = M('Enterprise')->where($map)->find();
        $enterpriseedit = M('Enterprise')->where($map)->save($enterprise_edit);
        $map = array();
        $map['enterprise_id'] = array('eq',intval(I('post.enterprise_id')));
        $user_edit = array(
            'status'        =>      2
            );
        $useredit = M('Sys_user')->where($map)->save($user_edit);
        if($enterpriseedit && $useredit){
            $msg = '删除企业成功！';
            $status = 'success';
            $model->commit();
            $n_msg='成功';
        }else{
            $msg = '删除企业失败！';
            $model->rollback();
            $n_msg='失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$enterprise_id.'】，删除企业【'.$enterpriseinfo['enterprise_name'].'('.$enterpriseinfo['enterprise_code'].')】'.$n_msg;
        $this->sys_log('删除企业',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));

    }



        /**
     *  代理商详情
     */
    public function approve_show(){

        $msg = '系统错误!';
        $status = 'error';

        $enterprise_id = intval(I('get.enterprise_id'));

        $map['enterprise.status'] = array('neq',2);
        $map['enterprise.enterprise_id'] = array('eq',$enterprise_id);
        $enterprise = M('Enterprise as enterprise')
        ->field('enterprise.*,approve_user.user_name as approve_name,province.province_name,city.city_name')
        ->join('left join t_flow_sys_user as approve_user on approve_user.user_id = enterprise.approve_user_id and approve_user.status = 1')
        ->join('t_flow_sys_province as province on province.province_id = enterprise.province','left')
        ->join('t_flow_sys_city as city on city.city_id = enterprise.city','left')
        ->where($map)->find();

        if($enterprise){
            $type = I('get.download');
            if(in_array($type,array('icense_img','identity_img')) ){
                parent::download('.'.$enterprise[$type]);
            }else{
                $map = array();
                $map['status'] = array('eq',1);
                $map['operator_id'] = array('in',$enterprise['operator']);
                $enterprise['operator'] = '';
                foreach(M('Sys_operator')->where($map)->select() as $v){
                    $enterprise['operator'] .= ','.$v['operator_name'];
                }
                $enterprise['operator'] = substr($enterprise['operator'],'1',strlen($enterprise['operator'])-1);
                $this->assign('enterprise',$enterprise);
                $this->display();
            }
        }else{
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }

    }

    public function approve_excel(){
        $enterprise_code = trim(I('get.enterprise_code'));
        $enterprise_name = trim(I('get.enterprise_name'));
        $approve_status = trim(I('get.approve_status'));
        $create_start_datetime = trim(I('get.create_start_datetime'));
        $create_end_datetime = trim(I('get.create_end_datetime'));
        $approve_start_datetime = trim(I('get.approve_start_datetime'));
        $approve_end_datetime = trim(I('get.approve_end_datetime'));
        if(trim(I('get.approve_user'))){
            $map['approve_user.user_name'] =  array('like','%'.trim(I('get.approve_user')).'%');
        }
        if(trim(I('get.create_user'))){
            $map['create_user.user_name'] =   array('like','%'.trim(I('get.create_user')).'%');
        }
        //var_dump($create_user);exit;
        if($enterprise_code){
            $map['enterprise.enterprise_code'] = array('like','%'.$enterprise_code.'%');
        }
        if($enterprise_name){
            $map['enterprise.enterprise_name'] = array('like','%'.$enterprise_name.'%');
        }

        if($approve_status == '0' or $approve_status == '1' or $approve_status=='2'){
            $map['enterprise.approve_status'] = array('eq',$approve_status);
        }elseif($approve_status == ''){
            $_GET['approve_status'] = 0;
            $map['enterprise.approve_status'] = array('eq',0);
        }

        if($create_start_datetime or $create_end_datetime){
            if($create_start_datetime && $create_end_datetime){
                $map['enterprise.create_date']  = array('between',array($create_start_datetime,$create_end_datetime));
            }elseif($create_start_datetime){
                $map['enterprise.create_date'] = array('EGT',$create_start_datetime);
            }elseif($create_end_datetime){
                $map['enterprise.create_date'] = array('ELT',$create_end_datetime);
            }
        }

        if($approve_start_datetime or $approve_end_datetime){
            if($approve_start_datetime && $approve_end_datetime){
                $map['enterprise.approve_date']  = array('between',array($approve_start_datetime,$approve_end_datetime));
            }elseif($approve_start_datetime){
                $map['enterprise.approve_date'] = array('EGT',$approve_start_datetime);
            }elseif($approve_end_datetime){
                $map['enterprise.approve_date'] = array('ELT',$approve_end_datetime);
            }
        }

        $map['enterprise.status'] = array('neq',2);


        $map_status['enterprise.enterprise_id'] = array(array('in',D('Enterprise')->enterprise_child_ids()),array('in',D('Enterprise')->enterprise_ids()),'or');
        $map_status_or['_complex'] = $map_status;
        $map_status_or['_logic'] = 'and';
        $map_status_or['enterprise.approve_status'] = array('eq',1);
        $where['_complex'] = $map_status_or;
        $where['_logic'] = 'or';
        $where['enterprise.approve_status'] = array('neq',1);
        $map['_complex'] = $where;
        //var_dump($map);exit;
        $model = M('');
        $enterprise_list = $model
            ->table('t_flow_enterprise as enterprise')
            ->field('enterprise.*,create_user.user_name as create_name,approve_user.user_name as approve_name')
            ->join('t_flow_sys_user as create_user on create_user.user_id = enterprise.create_user_id','left')
            ->join('t_flow_sys_user as approve_user on approve_user.user_id = enterprise.approve_user_id','left')
            ->limit(3000)
            ->field('enterprise.enterprise_code,enterprise.enterprise_name,enterprise.tel,enterprise.contact_name,enterprise.approve_date,approve_user.user_name as approve_name,enterprise.approve_status')
            ->order('enterprise.modify_date Desc')
            ->where($map)
            ->select();


        $title='企业审核';
        $headArr=array("企业编号","企业名称","公司电话","联系人","审核状态","审核人","审核时间");
        $data=array();
        foreach ($enterprise_list as $v) {
            $enter=array();
            $enter['enterprise_code']=$v['enterprise_code'];
            $enter['enterprise_name']=$v['enterprise_name'];
            $enter['tel']=$v['tel'];
            $enter['contact_name']=" ".$v['contact_name'];
            if($v['approve_status'] == 1){
                $enter['approve_status']="审核通过";
            }elseif($v['approve_status'] == 2){
                $enter['approve_status']="审核驳回";
            }else{
                $enter['approve_status']="待审核";
            }
            $enter['approve_name']=$v['approve_name'];
            if($v['approve_date']=='0000-00-00 00:00:00'){
                $enter['approve_date']=" ";
            }else{
                $enter['approve_date']=$v['approve_date'];
            }

            array_push($data,$enter);
        }
        ExportEexcel($title,$headArr,$data);
    }

    public function export_excel(){
                 $model = M('');

                $enterprise_code = trim(I('get.enterprise_code'));
                $enterprise_name = trim(I('get.enterprise_name'));
                $top_proxy_name = trim(I('get.top_proxy_name'));
                $top_proxy_code = trim(I('get.top_proxy_code'));
                $status = trim(I('get.status'));
                $approve_status = trim(I('get.approve_status'));
                $top_proxy_id = trim(I('get.top_proxy_id'));
                $user_name=trim(I('get.user_name'));//客户经理
                $message_status=I('message_status');
                if($message_status!= 9 && $message_status !=''){
                    $map['enterprise.message_status'] = $message_status=== '0' ? $message_status : 1;
                }
                if($user_name){
                    $map['user_name'] = array('like','%'.$user_name.'%');
                }
                if($enterprise_code){
                    $map['enterprise.enterprise_code'] = array('like','%'.$enterprise_code.'%');
                }
                if($enterprise_name){
                    $map['enterprise.enterprise_name'] = array('like','%'.$enterprise_name.'%');
                }
                if($top_proxy_name){
                    $map['top_proxy.proxy_name'] = array('like','%'.$top_proxy_name.'%');
                }
                if($top_proxy_code){
                    $map['top_proxy.proxy_code'] = array('like','%'.$top_proxy_code.'%');;
                }
                $map['enterprise.status'] = array('neq',2);
                if(in_array($status,array('0','1'))){
                    $map['enterprise.status'] = array('eq',$status);
                }

                /*if(in_array($approve_status,array('0','1','2'))){
                    $map['enterprise.approve_status'] = array('eq',$approve_status);
                }*/
                if(D('SysUser')->is_top_proxy_admin()){
                    if(!isset($_GET['status']) && !isset($_GET['istree']) ){
                        $_GET['status'] = 1;
                        $map['enterprise.status'] = array('eq',1);
                    }
                    $map['enterprise.approve_status'] = array('eq',1);
                }else{
                    if(!isset($_GET['status']) && !isset($_GET['istree']) ){
                        $_GET['status'] = 1;
                        $map['enterprise.status'] = array('eq',1);
                    }

                    if($approve_status ==""){
                        $map['enterprise.approve_status'] =1;
                    }else{
                        if($approve_status!=9){
                            $map['enterprise.approve_status'] = array('eq',$approve_status);
                        }
                    }
                }

                $where['enterprise.enterprise_id'] = array(array('in',$this->os_enterprise_ids),array('in',D('Enterprise')->enterprise_ids2()),'or') ;

                $map['_complex'] = $where;

                if($top_proxy_id && (in_array($top_proxy_id,explode(',',$this->os_proxy_ids)) or ($top_proxy_id == D('SysUser')->self_proxy_id() ) ) ){
                    $ids = M('')->query("select getProxyChildList('$top_proxy_id') as ids");
                    $map['enterprise.top_proxy_id'] = array('in',$ids[0]['ids']);
                }

                if(D('SysUser')->is_top_proxy_admin() == false){
                    $map['enterprise.top_proxy_id'] = D('SysUser')->self_proxy_id();
                }
            $enterprise_list = $model
                ->table('t_flow_enterprise as enterprise')
                ->field('enterprise.icense_img_num,enterprise.enterprise_id,enterprise.enterprise_code,enterprise.contact_name,enterprise.contact_tel,enterprise.enterprise_name,top_proxy.proxy_name as top_name,top_proxy.proxy_code as top_code,enterprise.approve_status,enterprise.status,user.user_name,enterprise.top_proxy_id')
                ->join('left join t_flow_sys_user as user on user.user_id = enterprise.sale_id and user.status = 1')
                ->join('left join t_flow_proxy as top_proxy on enterprise.top_proxy_id = top_proxy.proxy_id')
                ->limit('0,3000')
                ->order('enterprise.modify_date Desc,top_proxy.proxy_level')
                ->where($map)
                ->select();

            //判断是否需要显示设置客户经理
            $self_proxy_id = D('SysUser')->self_proxy_id();
            foreach( $enterprise_list as $k => $v){
                if($v['top_proxy_id'] == $self_proxy_id){
                    $enterprise_list[$k]['is_os'] = true;
                }
            }
            $data=array();

            if(D('SysUser')->is_top_proxy_admin()){
                $headArr=array("企业编号","企业名称","联系人","联系电话",'上级代理商编号',"上级代理商名称","营业执照编号");
                foreach ($enterprise_list as $v) {
                    $enter=array();
                    $enter['enterprise_code']=$v['enterprise_code'];
                    $enter['enterprise_name']=$v['enterprise_name'];
                    $enter['contact_name']=$v['contact_name'];
                    $enter['contact_tel']=" ".$v['contact_tel'];
                    if(D('SysUser')->is_top_proxy_admin()){
                        $enter['user_code']=$v['top_code'];
                        $enter['user_name']=$v['top_name'];
                    }else{
                        if($v['is_os']){
                            $enter['user_name']=$v['user_name'];
                        }else{
                            $enter['user_name']="";
                        }
                    }
                    $enter['icense_img_num']=" ".$v['icense_img_num'];
                    array_push($data,$enter);
                }
            }else{
                $headArr=array("企业编号","企业名称","联系人","联系电话","客户经理","审核状态");
                foreach ($enterprise_list as $v) {
                    $enter=array();
                    $enter['enterprise_code']=$v['enterprise_code'];
                    $enter['enterprise_name']=$v['enterprise_name'];
                    $enter['contact_name']=$v['contact_name'];
                    $enter['contact_tel']=" ".$v['contact_tel'];
                    if(D('SysUser')->is_top_proxy_admin()){
                        $enter['user_name']=$v['top_name'];
                    }else{
                        if($v['is_os']){
                            $enter['user_name']=$v['user_name'];
                        }else{
                            $enter['user_name']="";
                        }
                    }
                    if($v['approve_status']==2){
                        $enter['approve_status']="审核驳回";
                    }elseif($v['approve_status']==1){
                        $enter['approve_status']="审核通过";
                    }else{
                        $enter['approve_status']="等待审核";
                    }
                    array_push($data,$enter);
                }
            }
            $title='企业信息';

        ExportEexcel($title,$headArr,$data);
    }


    public function set_refund_status(){
        $msg = '系统错误!';
        $status = 'error';
        $data = array();
        $where['enterprise.enterprise_id'] = array(array('in',$this->os_enterprise_ids),array('in',D('Enterprise')->enterprise_ids()),'or') ;
        $map['_complex'] = $where;
        $enterprise_id = intval(I('post.enterprise_id'));

        $map['enterprise.status'] = array('neq',2);
        $map['enterprise.enterprise_id'][] = array('eq',$enterprise_id);
        $map['enterprise.approve_status'] = array('eq',1);
        if(!D('SysUser')->is_top_proxy_admin()){
            $map['enterprise.top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
        }
        //var_dump($map);exit;
        $enterprise = M('')
            ->table('t_flow_enterprise as enterprise')
            ->where($map)
            ->find();

        if($enterprise && $enterprise['approve_status'] == 1){
            //判断企业是否是自己代理商的下级  并且在自己的权限以内
            $edit = array();
            if($enterprise['refund_status'] == 1){
                $edit['refund_status'] = 0;
            }else{
                $edit['refund_status'] = 1;
            }
            $edit['modify_user_id'] =   D('SysUser')->self_id();
            $edit['modify_date']    =   date("Y-m-d H:i:s",time());
            $edit['enterprise_id'] = $enterprise['enterprise_id'];

            if(M('Enterprise')->save($edit)){

                if($enterprise['refund_status'] == 1){
                    $msg = '退款状态禁用成功!';
                }else{
                    $msg = '退款状态启用成功!';
                }
                $status = 'success';
                $data['status'] = 0;
                $n_msg='成功';
            }else{
                if($enterprise['refund_status'] == 1){
                    $msg = '退款状态禁用失败!';
                }else{
                    $msg = '退款状态启用失败!';
                }
                $n_msg='失败';
            }
            $title = $enterprise['refund_status'] == 1 ? "禁用退款状态" : "启用退款状态";
            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$enterprise_id.'】，'.$title.'企业【'.$enterprise['enterprise_name'].'('.$enterprise['enterprise_code'].')】'.$n_msg;
            $this->sys_log($title.'企业',$note);
        }

        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
    }

    public function toggle_message(){
        $msg = '系统错误！';
        $status = 'error';
        if(IS_POST && IS_AJAX){
            $enterprise_id = I('post.enterprise_id',0,'int');
            if(!empty($enterprise_id)){
                $enterpriseinfo = M('enterprise')->where(array("enterprise_id"=>$enterprise_id))->find();
                if($enterpriseinfo){
                    $message_status = $enterpriseinfo['message_status'] == 1 ? "0" : "1";
                    $edit = array(
                        'message_status'=> $message_status
                    );
                    $edit = M('enterprise')->where(array( 'enterprise_id'=>$enterprise_id))->save($edit);
                    $status_name = $enterpriseinfo['message_status'] == 1 ? "禁用短信" : "启用短信";
                    if($edit){
                        $status = 'success';
                        $msg = $status_name.'功能成功!';
                        $n_msg='成功';
                    }else{
                        $msg = $status_name.'功能失败!';
                        $n_msg='失败';
                    }
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【' . $enterprise_id . '】，企业【'.$enterpriseinfo['enterprise_name'].'('.$enterpriseinfo['enterprise_code'].')'.'】'.$status_name.$n_msg;
                    $this->sys_log('企业'.$status_name,$note);
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


