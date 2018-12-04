<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
class ProxyController extends CommonController {


    public $os_proxy_ids;

    public $level = array(
        '1' =>  '一级',
        '2' =>  '二级',
        '3' =>  '三级',
        '4' =>  '四级',
        '5' =>  '五级',
        '6' =>  '六级',
        '7' =>  '七级',
        '8' =>  '八级',
        '9' =>  '九级',
        '10' =>  '十级'
        );
    public function start(){
       //计算操作者的数据权限ID序列
        $this->os_proxy_ids = D('Proxy')->proxy_child_ids();
    }


    /**
     *  代理商档案
     */
    public function index(){
        D("SysUser")->sessionwriteclose();
        $type = trim(I('get.type'));
        if($type == 'table'){

             $model = M('');

            $proxy_code = trim(I('get.proxy_code'));
            $proxy_name = trim(I('get.proxy_name'));
            $top_proxy_id = trim(I('get.top_proxy_id'));

            /*
            $user_name = trim(I('get.user_name'));

            if($user_name){
                $map['user.user_name'] = array('like','%'.$user_name.'%');
            }
            */
            $status = trim(I('get.status'));
            $approve_status = trim(I('get.approve_status'));
            
            if($proxy_code){
                $map['proxy.proxy_code'] = array('like','%'.$proxy_code.'%');
            }
            if($proxy_name){
                $map['proxy.proxy_name'] = array('like','%'.$proxy_name.'%');
            }

            $map['proxy.status'] = array('neq',2);
            if(in_array($status,array('0','1'))){
               $map['proxy.status'] = array('eq',$status);
            }elseif($status == ''){
               // $_GET['status'] = 1;
                //$map['proxy.status'] = array('eq',1);
            }

            if($approve_status ==""){
                $map['proxy.approve_status'] =1;
            }else{
                if($approve_status!=9){
                    $map['proxy.approve_status'] = $approve_status;
                }
            }
            if(D("SysUser")->self_user_type()==2){
                $proxy_ids=D("proxy")->proxy_approve_child_ids();
            }else{
                $proxy_ids=$this->os_proxy_ids;
            }
            $self_proxy_id=D('SysUser')->self_proxy_id();//当前代理商

            /*if(D("SysUser")->self_user_type()==1 ){
                $where['proxy.top_proxy_id'] = D('SysUser')->self_proxy_id();
                $where['proxy.proxy_id'] =  array(array('in',$proxy_ids),array('eq',D('SysUser')->self_proxy_id()),'or');
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
            }else{
                $map['proxy.proxy_id'] = array(array('in',$proxy_ids),array('eq',D('SysUser')->self_proxy_id()),'or');
            }*/
            $map['proxy.proxy_id'] = array('in',$proxy_ids);
            if($top_proxy_id && (in_array($top_proxy_id,explode(',',$proxy_ids)) or ($top_proxy_id == D('SysUser')->self_proxy_id() ) ) ){
                $ids = M('')->query("select getProxyChildList('$top_proxy_id') as ids");
                unset($map['proxy.proxy_id']);
                //$map['proxy.proxy_id'][] = array('neq',D('SysUser')->self_proxy_id());

                $where['proxy.proxy_id'][] = array(array('in',$proxy_ids),array('eq',D('SysUser')->self_proxy_id()),'or');
                $map['_complex'] = $where;
                $map['proxy.proxy_id'][] = array('in',$ids[0]['ids']);
                $map['proxy.proxy_id'][] = array('in',$self_proxy_id.','.$proxy_ids);
                //var_dump($where);

            }
            /*if(D('SysUser')->is_top_proxy_admin() == false ){
                $where['proxy.top_proxy_id'] = D('SysUser')->self_proxy_id();
                $where['proxy.proxy_id'] = D('SysUser')->self_proxy_id();
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
            }*/
            $count = $model
                ->table('t_flow_proxy as proxy')
                ->field('proxy.*,top_proxy.proxy_name as top_name,user.user_name')
                ->join('left join t_flow_sys_user as user on user.user_id = proxy.sale_id and user.status = 1')
                ->join('left join t_flow_proxy as top_proxy on proxy.top_proxy_id = top_proxy.proxy_id')
                ->where($map)
                ->count();
            $Page       = new Page($count,20);
            $show       = $Page->show();
            $proxy_list = $model
            ->table('t_flow_proxy as proxy')
            ->field('proxy.*,top_proxy.proxy_name as top_name,user.user_name')            
            ->join('left join t_flow_sys_user as user on user.user_id = proxy.sale_id and user.status = 1')
            ->join('left join t_flow_proxy as top_proxy on proxy.top_proxy_id = top_proxy.proxy_id')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('proxy.proxy_level,approve_status,proxy.modify_date Desc')
            ->where($map)
            ->select();
            //判断是否需要显示设置客户经理
            $self_proxy_id = D('SysUser')->self_proxy_id();
            foreach( $proxy_list as $k => $v){
                if($v['top_proxy_id'] == $self_proxy_id){
                    $proxy_list[$k]['is_os'] = true;
                }
            }
            $this->assign('page',$show);
            $this->assign('user_type',D("SysUser")->self_user_type());
            $this->assign('is_top_proxy_admin',D('SysUser')->is_top_proxy_admin());
            $this->assign('proxy_list', get_sort_no($proxy_list, $Page->firstRow));
            $this->display();
            

        }else{

            //获取树形结构
            $self_proxy_id = D('SysUser')->self_proxy_id();
            //$ids = M('')->query("select getProxyChildList('$self_proxy_id') as ids");
            $map = array();
            //$map['approve_status'] = array('eq',1);

            $map['status'] = array('eq',1);
            //$map['proxy_id'][] = array('in',$ids[0]['ids']);
           // $map['proxy_id'][] = array('in',D('SysUser')->self_proxy_id().','.$this->os_proxy_ids);
            $map['proxy_id'][] = array('in',D('SysUser')->self_proxy_id().','.$this->os_proxy_ids);
           /* if(D('SysUser')->is_top_proxy_admin() == false ){
                $where['top_proxy_id'] = D('SysUser')->self_proxy_id();
                $where['proxy_id'] = D('SysUser')->self_proxy_id();
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
            }*/

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
                $self['proxy_name'] = C('PROXY_NAME');
            }
            $proxy_tree = D('proxy')->proxy_tree($self,$proxy_tree_list);
            $this->assign('tree_html',D('Proxy')->tree_html($proxy_tree));
            $this->display('tree');
           

        }
    }
    public function approve_excel(){

        $proxy_code = trim(I('get.proxy_code'));
        $proxy_name = trim(I('get.proxy_name'));
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
        if($proxy_code){
            $map['proxy.proxy_code'] = array('like','%'.$proxy_code.'%');
        }
        if($proxy_name){
            $map['proxy.proxy_name'] = array('like','%'.$proxy_name.'%');
        }


        if($approve_status ==""){
            $map['proxy.approve_status'] =0;
        }else{
            if($approve_status!=9){
                $map['proxy.approve_status'] = array('eq',$approve_status);
            }
        }

        if($create_start_datetime or $create_end_datetime){
            if($create_start_datetime && $create_end_datetime){
                $map['proxy.create_date']  = array('between',array($create_start_datetime,$create_end_datetime));
            }elseif($create_start_datetime){
                $map['proxy.create_date'] = array('EGT',$create_start_datetime);
            }elseif($create_end_datetime){
                $map['proxy.create_date'] = array('ELT',$create_end_datetime);
            }
        }

        if($approve_start_datetime or $approve_end_datetime){
            if($approve_start_datetime && $approve_end_datetime){
                $map['proxy.approve_date']  = array('between',array($approve_start_datetime,$approve_end_datetime));
            }elseif($approve_start_datetime){
                $map['proxy.approve_date'] = array('EGT',$approve_start_datetime);
            }elseif($approve_end_datetime){
                $map['proxy.approve_date'] = array('ELT',$approve_end_datetime);
            }
        }

        $map['proxy.status'] = array('neq',2);

        /*$map['proxy.top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
        //将已审核通过的做数据权限
        $map_status['proxy.proxy_id'] = array('in',$this->os_proxy_ids);
        $map_status['proxy.approve_status'] = array('eq',1);
        $map_status['_logic'] = 'and';
        $where['_complex'] = $map_status;
        $where['proxy.approve_status'] = array('neq',1);
        $where['_logic'] = 'or';
        $map['_complex'] = $where;*/
        if(D("SysUser")->self_user_type()==2){
            $proxy_ids=D("proxy")->proxy_approve_child_ids();
        }else{
            $proxy_ids=$this->os_proxy_ids;
        }
        //$map['proxy.proxy_id'] = array(array('in',$proxy_ids),array('eq',D('SysUser')->self_proxy_id()),'or');
        $map['proxy.proxy_id'] =array('in',$proxy_ids);

     /*   if(D('SysUser')->is_top_proxy_admin() == false ){
            $where['proxy.top_proxy_id'] = D('SysUser')->self_proxy_id();
            $where['proxy.proxy_id'] = D('SysUser')->self_proxy_id();
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
        }*/

        $model = M('');
        $proxy_list = $model
            ->table('t_flow_proxy as proxy')
            ->field('proxy.*,create_user.user_name as create_name,approve_user.user_name as approve_name')
            ->join('t_flow_sys_user as create_user on create_user.user_id = proxy.create_user_id','left')
            ->join('t_flow_sys_user as approve_user on approve_user.user_id = proxy.approve_user_id','left')
            ->limit(3000)
            ->order('proxy.modify_date Desc')
            ->where($map)
            ->select();

        $title='代理商审核';
        $headArr=array("代理商编号","代理商名称","联系人","联系人电话","审核状态","审核人","审核时间");
        foreach($proxy_list as $k=>$v){
            $list[$k]['proxy_code'] =$v['proxy_code'];
            $list[$k]['proxy_name'] =$v['proxy_name'];
           /* $list[$k]['tel'] =" ".$v['tel'];*/
            $list[$k]['contact_name'] =$v['contact_name'];
            $list[$k]['contact_tel'] =$v['contact_tel'];
            //$list[$k]['create_name'] =$v['create_name'];
            //$list[$k]['create_date'] =$v['create_date'];
            if($v['approve_status']==0){
                $list[$k]['approve_status'] ='待审核';
            }else if($v['approve_status']==1){
                $list[$k]['approve_status'] ='审核通过';
            }else{
                $list[$k]['approve_status'] ='审核驳回';
            }
            $list[$k]['approve_name'] =$v['approve_name'];
            if($v['approve_date']=='0000-00-00 00:00:00'){
                $list[$k]['approve_date'] ='';
            }else{
                $list[$k]['approve_date'] =$v['approve_date'];
            }

        }
        ExportEexcel($title,$headArr,$list);
    }

    public function export_excel(){
        $model = M('');

        $proxy_code = trim(I('get.proxy_code'));
        $proxy_name = trim(I('get.proxy_name'));
        $top_proxy_id = trim(I('get.top_proxy_id'));
        $message_status=I('message_status');
        if($message_status!= 9 && $message_status !=''){
            $map['proxy.message_status'] = $message_status=== '0' ? $message_status : 1;
        }
        /*
        $user_name = trim(I('get.user_name'));
        $top_proxy_name = trim(I('get.top_proxy_name'));
        if($user_name){
            $map['user.user_name'] = array('like','%'.$user_name.'%');
        }
        if($top_proxy_name){
            $map['top_proxy.proxy_name'] = array('like','%'.$top_proxy_name.'%');
        }
        */

        $status = trim(I('get.status'));
        $approve_status = trim(I('get.approve_status'));

        if($proxy_code){
            $map['proxy.proxy_code'] = array('like','%'.$proxy_code.'%');
        }
        if($proxy_name){
            $map['proxy.proxy_name'] = array('like','%'.$proxy_name.'%');
        }

        $map['proxy.status'] = array('neq',2);
        if(in_array($status,array('0','1'))){
            $map['proxy.status'] = array('eq',$status);
        }elseif($status == ''){
            $_GET['status'] = 1;
            $map['proxy.status'] = array('eq',1);
        }

        if($approve_status ==""){
            $map['proxy.approve_status'] =1;
        }else{
            if($approve_status!=9){
                $map['proxy.approve_status'] = array('eq',$approve_status);
            }
        }

        if(D("SysUser")->self_user_type()==2){
            $proxy_ids=D("proxy")->proxy_approve_child_ids();
        }else{
            $proxy_ids=$this->os_proxy_ids;
        }
        $self_proxy_id=D('SysUser')->self_proxy_id();//当前代理商
        $map['proxy.proxy_id'] =array('in',$proxy_ids);
        if($top_proxy_id && (in_array($top_proxy_id,explode(',',$proxy_ids)) or ($top_proxy_id == D('SysUser')->self_proxy_id() ) ) ){
            $ids = M('')->query("select getProxyChildList('$top_proxy_id') as ids");
            unset($map['proxy.proxy_id']);
            //$map['proxy.proxy_id'][] = array('neq',D('SysUser')->self_proxy_id());

            $where['proxy.proxy_id'][] = array(array('in',$proxy_ids),array('eq',D('SysUser')->self_proxy_id()),'or');
            $map['_complex'] = $where;
            $map['proxy.proxy_id'][] = array('in',$ids[0]['ids']);
            $map['proxy.proxy_id'][] = array('in',$self_proxy_id.','.$proxy_ids);
            //var_dump($where);
        }
        $proxy_list = $model
            ->table('t_flow_proxy as proxy')
            ->field('proxy.*,top_proxy.proxy_name as top_name,user.user_name')
            ->join('left join t_flow_sys_user as user on user.user_id = proxy.sale_id and user.status = 1')
            ->join('left join t_flow_proxy as top_proxy on proxy.top_proxy_id = top_proxy.proxy_id')
            ->limit(3000)
            ->order('proxy.proxy_level,proxy.modify_date Desc')
            ->where($map)
            ->select();
        $title='代理商信息';
            $user_type=D("SysUser")->self_user_type();
            $headArr=array("代理商编号","代理商名称","联系人","联系电话","客户经理","审核状态","营业执照编号");
        $self_proxy_id = D('SysUser')->self_proxy_id();
            foreach($proxy_list as $k=>$v){
                $list[$k]['proxy_code'] =$v['proxy_code'];
                $list[$k]['proxy_name'] =$v['proxy_name'];
                $list[$k]['contact_name'] =$v['contact_name'];
                $list[$k]['contact_tel'] =" ".$v['contact_tel'];
                if($v['top_proxy_id'] == $self_proxy_id){
                    $list[$k]['user_name'] =$v['user_name'];
                }else{
                    $list[$k]['user_name'] ='';
                }
                if($v['approve_status']==0){
                    $list[$k]['approve_status'] ='待审核';
                }else if($v['approve_status']==1){
                    $list[$k]['approve_status'] ='审核通过';
                }else{
                    $list[$k]['approve_status'] ='审核驳回';
                }
                $list[$k]['icense_img_num'] = $v['icense_img_num'];
            }
        ExportEexcel($title,$headArr,$list);
    }


    public function add(){

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
        $this->display();
    }


    public function insert(){

        $msg = '系统错误!';
        $status = 'error';
        $data = array();
        $layer = array();



        $self_id = D('SysUser')->self_id();
        $time = date('Y-m-d H:i:s',time());

        //获取基础数据
        $contact_name   =   trim(I('post.contact_name'));            //姓名   
        $tel            =   trim(I('post.tel'));                  //联系电话
        $contact_tel    =   trim(I('post.contact_tel'));               //手机号码
        $email          =   trim(I('post.email'));                //邮箱
        $proxy_name     =   trim(I('post.proxy_name'));           //企业名称
        $operator_list  =   I('post.operator_list');   //支持运营商
        $address        =   trim(I('post.address'));              //企业地址
        $operator = implode(',',$operator_list);
        $province = trim(I('post.province_id'));
        $city = trim(I('post.city_id'));

        if(!empty($contact_name) ){
            if(empty($email) or isEmail($email)){
                if(empty($tel) or isTel($tel)){
                    if(!empty($contact_tel) && isTel($contact_tel)){
                        if(!empty($proxy_name)){
                            if(!D('Proxy')->check_proxy_name($proxy_name)){
                                if(!empty($operator)){
                                    if(D('Proxy')->checkoperator($operator_list,D('SysUser')->self_proxy_id())){
                                        $max_proxy_code =  M('Proxy')->max('proxy_code');
                                        $proxy_code = ($max_proxy_code < 1) ?  C("DES_PROXY_ID") : $max_proxy_code + 1;
                                        $map = array();
                                        $map['proxy_id'] =  D('SysUser')->self_proxy_id();
                                        $proxy_info = M('Proxy')->where($map)->find();
                                        
                                        $model = M();
                                        $model->startTrans();

                                        $addinfo = array(

                                            'proxy_code'        =>  $proxy_code,
                                            'proxy_name'        =>  $proxy_name,
                                            'tel'               =>  $tel,
                                            'contact_name'      =>  $contact_name,
                                            'contact_tel'       =>  $contact_tel,
                                            'top_proxy_id'      =>  D('SysUser')->self_proxy_id(),
                                            'operator'          =>  $operator,
                                            'address'           =>  $address,
                                            'email'             =>  $email,
                                            'sale_id'           =>  $self_id,
                                            'proxy_level'       =>  $proxy_info['proxy_level'] + 1,
                                            'city'              =>  $city,
                                            'province'          =>  $province,
                                            'create_user_id'    =>  $self_id,
                                            'create_date'       =>  $time,
                                            'modify_user_id'    =>  $self_id,
                                            'modify_date'       =>  $time,

                                            );

                                        //如果是顶级代理商 需要审核
                                        //if($proxy_info['top_proxy_id'] == 0){
                                            $addinfo['status']          = 1 ;
                                            $addinfo['approve_status']  = 0 ;
                                            $addinfo['approve_user_id'] = '';
                                            $addinfo['approve_date']    = '';
                                            $addinfo['approve_remark']  = '';
                                        /*}else{

                                            $addinfo['status'] = 1;
                                            $addinfo['approve_status']  = 1 ;
                                            $addinfo['approve_user_id'] = $self_id;
                                            $addinfo['approve_date']    = $time;
                                            $addinfo['approve_remark']  = '';

                                        }*/

                                        $proxy_id = M('Proxy')->add($addinfo);

                                        //添加基础数据
                                        $addbase = array(
                                            'user_name'         =>  $contact_name,                      //联系人
                                            'login_name'        =>  'admin',                         //登录部分名
                                            'login_name_full'   =>  'admin@'.$proxy_code,            //登录全名
                                            'login_pass'        =>  md5('123456'),                   //密码
                                            'user_type'         =>  2,                               //用户类型
                                            'is_manager'        =>  1,                               //是否是管理员
                                            'proxy_id'          =>  $proxy_id,                       //代理商ID
                                            'enterprise_id'     =>  '',                              //企业ID
                                            'mobile'            =>  $contact_tel,                         //手机号码
                                            'email'             =>  $email,                          //邮箱
                                            'status'            =>  1,                              //状态 0已禁用 1正常
                                            'create_user_id'    =>  $self_id,                        //创建人
                                            'create_date'       =>  $time,                           //创建时间
                                            'modify_user_id'    =>  $self_id,                        //最后修改人
                                            'modify_date'       =>  $time,                           //最后修改时间
                                            );

                                        
                                        $user_id = M('Sys_user')->add($addbase);

                                        if(!D('SysUser')->is_admin() or D('SysUser')->is_all_proxy($user_id) == '0'){
                                            $addcorrelation = array(
                                                'user_id'           =>      D('SysUser')->self_id(),
                                                'proxy_id'          =>      $proxy_id,
                                                'create_user_id'    =>      $self_id,    //创建人
                                                'create_date'       =>      $time,   //创建时间
                                                );

                                            $correlation = M('Proxy_user')->add($addcorrelation);

                                        }else{
                                            $correlation = true;
                                        }


                                        //添加账户信息
                                        $account_add = array(
                                            'proxy_id'              =>          $proxy_id,
                                            'account_balance'       =>          0.00,
                                            'freeze_money'          =>          0.00,
                                            'credit_money'          =>          0.00,
                                            'credit_freeze_money'   =>          0.00,
                                            'create_user_id'        =>          $self_id,
                                            'create_date'           =>          $time,
                                            'modify_user_id'        =>          $self_id,                        //最后修改人
                                            'modify_date'           =>          $time 
                                            );
                                        $account = M('Proxy_account')->add( $account_add );


                                        $apiadd = array(
                                            'user_type'                 =>         1,
                                            'proxy_id'                  =>         $proxy_id,
                                            'enterprise_id'             =>         0,
                                            'api_account'               =>         randomY(8,'QWERTYUIOPASDFGHJKLZXCVBNM'),
                                            'api_key'                   =>         getrandstr(32),
                                            'api_callback_address'      =>         '',
                                            'api_callback_ip'           =>         '',
                                            );
                                        
                                        $api_add = M('Sys_api')->add($apiadd);

                                        if($correlation && $proxy_id && $user_id && $account && $api_add){
                                            //if(D('SysUser')->is_top_proxy_admin()){
                                                $msg = '新增代理商成功，请上传代理商证件！';
                                                
                                           /* }else{
                                                $msg = '新增代理商成功';
                                                $layer['is_layer_msg'] = '代理商【'.$proxy_name.'】已审核通过！<br/>登录名称【'.$addbase['login_name_full'].'】 <br/>登录密码【123456】';
                                                $layer['is_layer_status'] = 'success';
                                            }*/
                                            $data = $proxy_id;
                                            $status = 'success';
                                            $model->commit();
                                            //if($proxy_info['top_proxy_id'] == 0){
                                                $remind_content="用户【".get_user_name($self_id)."】新增的代理商【".$proxy_name."】已提交审核，请进行审核！";
                                                R('ObjectRemind/send_user',array(1,$remind_content));
                                            //}
                                            $n_msg='成功';
                                        }else{
                                            $msg = '新增代理商失败！';
                                            $model->rollback();
                                             $data = '';
                                            $n_msg='失败';
                                        }
                                        $note='用户【'. get_user_name(D('SysUser')->self_id()).'】，ID【'.$proxy_id.'】，新增代理商【'.$proxy_name.'('.$proxy_code.')】'.$n_msg;
                                        $this->sys_log('新增代理商',$note);
                                    }
                                }else{
                                    $msg = '请勾选代理商支持的运营商!'; 
                                }
                            }else{
                                $msg = '代理商名称已存在！';
                            }
                        }else{
                            $msg = '请输入代理商名称！';  
                        }
                    }else{
                        $msg = '联系电话格式错误！'; 
                    }
                }else{
                    $msg = '请输入正确公司电话!'; 
                }
            }else{
                $msg = '请输入正确邮箱！';
            }
        }else{
            $msg = '请输入联系人!'; 
        }

        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data,'layer'=>$layer));
    }



    public function edit(){
        $msg ="系统错误";
        $status = 'error';

        $proxy_id = trim(I('get.proxy_id'));
        if(D('SysUser')->is_top_proxy_admin()){
            $where['proxy.proxy_id'] = array(array('in',$this->os_proxy_ids),array('eq',D('SysUser')->self_proxy_id()),'or');
            $map['_complex'] = $where;
            $map['proxy.proxy_id'] = array('eq',$proxy_id);
        }else{
            $map['proxy.proxy_id'][] = array('in',$this->os_proxy_ids);
            $map['proxy.proxy_id'][] = array('eq',$proxy_id);
        }
        
        $map['proxy.status'] =array('neq',2);

        //$map['proxy.approve_status'] = array('neq',2);
        $proxy = M('Proxy as proxy')
        ->field('proxy.*,user.user_id,user.login_name')
        ->join('left join t_flow_sys_user as user on user.proxy_id = proxy.proxy_id and user.is_manager = 1')
        ->where($map)
        ->find();

        if($proxy){
            //获取旗下所有管理员
            $map = array();
            $map['status'] = array('eq',1);
            $map['is_manager'] = array('eq',0);
            $map['proxy_id'] = array('eq',$proxy['top_proxy_id']);
            $user_list = M('SysUser')->where($map)->select();
            foreach($user_list as $k=>$v){
                if($v['user_id'] == $proxy['sale_id']){

                    $user_list[$k]['selected'] = 'selected';
                }
            }

            //获取支持的运营商
            if($proxy['top_proxy_id'] != 0){
               
                $map = array();
                $map['status'] = array('neq',2);
                $map['proxy_id'] = array('eq',$proxy['top_proxy_id']);
                $top_proxy = M('Proxy')->field('operator')->where($map)->find();

                $map = array();
                $map['operator_id'] = array('in',$top_proxy['operator']);
                $operator_list = M('Sys_operator')->where($map)->select();

                if($proxy['operator']){
                    foreach($operator_list as $k=>$v){
                        if(in_array($v['operator_id'],explode(',',$proxy['operator']))){
                            $operator_list[$k]['checked'] = 'checked';
                        }
                    }
                }
                $this->assign('operator_list',$operator_list);

            }
           

            $map = array();
            $map['province_id'] = array('neq',1);
            $province_list = M('Sys_province')->where($map)->select();
            $this->assign('province_list',$province_list);
            $city_list = M('Sys_city')->select();
            $this->assign('city_list',$city_list);


            $this->assign('proxy',$proxy);
            $this->assign('is_top_proxy_admin',D('SysUser')->is_top_proxy_admin());
           
            $this->assign('user_list',$user_list);
            $this->display();

        }else{
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
        
    }




    /**
     *  修改代理商
     */
    public function update(){

        $msg = '系统错误!';
        $status = 'error';
        $data = array();



        $self_id = D('SysUser')->self_id();
        $time = date('Y-m-d H:i:s',time());

        //获取基础数据
        $proxy_id       =   trim(I('post.proxy_id'));             //ID
        $login_name     =   trim(I('post.login_name'));
        $contact_name   =   trim(I('post.contact_name'));            //姓名   
        $tel            =   trim(I('post.tel'));                  //联系电话
        $contact_tel    =   trim(I('post.contact_tel'));               //手机号码
        $proxy_name     =   trim(I('post.proxy_name'));           //企业名称
        $operator_list  =   I('post.operator_list');        //支持运营商
        $address        =   trim(I('post.address'));              //企业地址
        $email          =   trim(I('post.email'));
        $province       =   trim(I('post.province_id'));
        $city           =   trim(I('post.city_id'));

        $operator = implode(',',$operator_list);
        $map['proxy.status'] = array('neq',2);
        if(D('SysUser')->is_top_proxy_admin()){
            $where['proxy.proxy_id'] = array(array('in',$this->os_proxy_ids),array('eq',D('SysUser')->self_proxy_id()),'or');
            $map['_complex'] = $where;
            $map['proxy.proxy_id'] = array('eq',$proxy_id);
        }else{
            $map['proxy.proxy_id'][] = array('in',$this->os_proxy_ids);
            $map['proxy.proxy_id'][] = array('eq',$proxy_id);
        }

        //$map['proxy.approve_status'] = array('neq',2);
        $proxy = M('Proxy as proxy')
        ->field('proxy.proxy_id,proxy.proxy_code,proxy.proxy_name,proxy.tel,proxy.sale_id,proxy.operator,user.user_id,user.user_name,user.mobile,user.login_name,proxy.top_proxy_id,proxy.sale_id as old_sale_id,proxy.province,proxy.city,proxy.address,proxy.contact_tel,proxy.contact_name,proxy.email')
        ->join('left join t_flow_sys_user as user on user.proxy_id = proxy.proxy_id and user.is_manager = 1')
        ->where($map)
        ->find();
        if($proxy){
            if(!empty($contact_name)){
                if(empty($email) or isEmail($email)){
                    if(!empty($login_name)){
                        if(empty($tel) or isTel($tel)){
                                if(!empty($contact_tel)){
                                    if(isTel($contact_tel)){
                                        if(!empty($proxy_name)){
                                            if(!D('Proxy')->check_proxy_name($proxy_name,$proxy['proxy_id'])){
                                                if( ($proxy['top_proxy_id'] != 0 ) && (empty($operator) || !D('Proxy')->checkoperator($operator_list,$proxy['top_proxy_id']) )  ){
                                                    $msg = '代理商支持运营商不能为空!'; 
                                                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                                                }
                                                $model = M();
                                                $model->startTrans();
                                                $proxyedit = array(
                                                    'proxy_id'      =>  $proxy_id,
                                                    'proxy_name'    =>  $proxy_name,
                                                    'tel'           =>  $tel,
                                                    'contact_name'  =>  $contact_name,
                                                    'contact_tel'   =>  $contact_tel,
                                                    //'operator'      =>  $operator,
                                                    'address'       =>  $address,
                                                    'email'         =>  $email,
                                                    'province'      =>  $province,
                                                    'city'          =>  $city,
                                                    'modify_user_id'=>  $self_id,
                                                    'modify_date'   =>  $time,
                                                    );
                                                if($proxy['top_proxy_id'] != 0 ){
                                                    $proxyedit['operator'] = $operator;
                                                }
                                                $proxy_id = M('Proxy')->save($proxyedit);

                                                //添加基础数据
                                                $editbase = array(
                                                    'user_id'           =>  $proxy['user_id'],
                                                    'login_name'        =>  $login_name,
                                                    'login_name_full'   =>  $login_name.'@'.$proxy['proxy_code'],
                                                    'modify_user_id'    =>  $self_id,                       
                                                    'modify_date'       =>  $time,                    
                                                    );

                                                
                                                $user_id = M('Sys_user')->save($editbase);

                                                if($proxy_id && $user_id){
                                                    $msg = '编辑代理商成功！';
                                                    $status = 'success';
                                                    $model->commit();
                                                    $n_msg='成功';
                                                }else{
                                                    $msg = '编辑代理商失败！';
                                                    $model->rollback();
                                                    $n_msg='失败';
                                                }
                                                $c_item='';
                                                $c_item.=$proxy_name===$proxy['proxy_name']?'':'代理商名称【'. $proxy_name.'】';
                                                $fg=!empty($c_item)?'，':'';
                                                $c_item.=$login_name===$proxy['login_name']?'':$fg.'登陆名称【'. $login_name.'】';
                                                $fg=!empty($c_item)?'，':'';
                                                $ops='';
                                                foreach($operator_list as $v){
                                                    $ops.=get_operator_name($v).'，';
                                                }
                                                $ops= substr($ops,0,-1);
                                                $c_item.=$operator===$proxy['operator']?'':$fg.'支持运营商【'. $ops.'】';
                                                $fg=!empty($c_item)?'，':'';
                                                $c_item.=$contact_name===$proxy['contact_name']?'':$fg.'联系人【'. $contact_name.'】';
                                                $fg=!empty($c_item)?'，':'';
                                                $c_item.=$tel===$proxy['tel']?'':$fg.'联系电话【'. $tel.'】';
                                                if($contact_tel!==$proxy['contact_tel']){
                                                    $fg=!empty($c_item)?'，':'';
                                                    $c_item.=empty($contact_tel)?$fg.'清除公司电话':$fg.'公司电话【'. $contact_tel.'】';
                                                }
                                                if($email!==$proxy['email']){
                                                    $fg=!empty($c_item)?'，':'';
                                                    $c_item.=empty($email)?$fg.'清除邮箱':$fg.'邮箱【'. $email.'】';
                                                }
                                                if($province!==$proxy['province']){
                                                    $fg=!empty($c_item)?'，':'';
                                                    if($province!='' || $province!=='0' ){
                                                        $c_item.=$fg.'所属省【'. get_province_name($province).'】';
                                                    }else{
                                                        $c_item.=$fg.'清除所属省';
                                                    }
                                                }
                                                if($city!==$proxy['city']){
                                                    $fg=!empty($c_item)?'，':'';
                                                    if($city!='' || $city!=='0'){
                                                        $c_item.=$fg.'所属市【'. get_city_name($city).'】';
                                                    }else{
                                                        $c_item.=$fg.'清除所属市';
                                                    }
                                                }
                                                if($address!==$proxy['address']){
                                                    $fg=!empty($c_item)?'，':'';
                                                    $c_item.=empty($address)?$fg.'清除地址':$fg.'地址【'. $address.'】';
                                                }
                                                $note='用户【'. get_user_name(D('SysUser')->self_id()).'】，ID【'.$proxy_id.'】，编辑代理商【'.$proxy['proxy_name'].'('.$proxy['proxy_code'].')】：'.$c_item.$n_msg;
                                                $this->sys_log('编辑代理商',$note);
                                                
                                            }else{
                                                $msg = '代理商名称已存在！';
                                            }
                                        }else{
                                            $msg = '代理商名称不能为空！'; 
                                        }
                                    }else{  
                                        $msg = '请输入正确联系电话！';
                                    }
                                }else{
                                    $msg = '请输入联系电话！'; 
                                }
                        }else{
                            $msg = '请输入正确公司电话!';
                        }
                    }else{
                        $msg = '请输入登录名称！';
                    }
                }else{
                    $msg = '请输入正确的邮箱！';
                }
            }else{
                $msg = '请输入联系人!'; 
            }
        }
        

        return $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
    }




    /**
     *  审核证件修改模板
     */
    public function approve_credentials_edit(){

        $msg = '系统错误！';
        $status = 'error';
        //$map['proxy_id'][] = array('in',$this->os_proxy_ids);
        $proxy_id = trim(intval(I('get.proxy_id')));
        $map['proxy_id'][] = array('eq',$proxy_id);
        $map['status'] = array('neq',2);
        $map['approve_status'] = array('neq',1);
        $proxy = M('Proxy')->where($map)->find();
        if($proxy){
            $type = trim(I('get.download'));
            if(in_array($type,array('icense_img','identity_img'))){
                 parent::download('.'.$proxy[$type]);
            }else{
                $this->assign('is_top_proxy_admin',D('SysUser')->is_top_proxy_admin());
                $this->assign('proxy',$proxy);
                $this->assign("user_type",D("SysUser")->self_user_type());
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
        
        $proxy_id = trim(intval(I('get.proxy_id')));
        if(D('SysUser')->is_top_proxy_admin()){
            $where['proxy_id'] = array(array('in',$this->os_proxy_ids),array('eq',D('SysUser')->self_proxy_id()),'or');
            $map['_complex'] = $where;
            $map['proxy_id'] = array('eq',$proxy_id);
        }else{
            $map['proxy_id'][] = array('in',$this->os_proxy_ids);
            $map['proxy_id'][] = array('eq',$proxy_id);
        }

        $map['status'] = array('neq',2);

        $proxy = M('Proxy')->where($map)->find();
        if($proxy){
            $type = trim(I('get.download'));
            if(in_array($type,array('icense_img','identity_img'))){
                 parent::download('.'.$proxy[$type]);
            }else{
                $this->assign('is_top_proxy_admin',D('SysUser')->is_top_proxy_admin());
                $this->assign('proxy',$proxy);
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
        $proxy_id = I('post.proxy_id');
        //$map['proxy_id'][] = array('in',$this->os_proxy_ids);
        $map['proxy_id'][] = array('eq',$proxy_id);
        $map['approve_status'] = array('neq',1);
        $proxy = M('Proxy')->where($map)->find();
        if(!$proxy){
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
        if(empty($icense_img_num)){
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

        if( $proxy['icense_img'] == '' && !$fileinfo['icense_img']){
            $msg = '请上传营业执照附件！';
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }

        
        $edit = array(
            'icense_img_num'        =>      $icense_img_num,
            'identity_img_num'      =>      $identity_img_num,
            'proxy_id'              =>      $proxy_id,
            'modify_user_id'        =>      D('SysUser')->self_id(),
            'modify_date'           =>      date("Y-m-d H:i:s",time()),
            );

        if($fileinfo['icense_img']){
            $edit['icense_img'] = $icense_img;
        }
        if($fileinfo['identity_img']){
            $edit['identity_img'] = $identity_img;
        }

        if(M('Proxy')->save($edit)){
            $msg = '代理商证件保存成功！';
            $status = 'success';
            $n_msg='成功';
        }else{
            $msg = '代理商证件保存失败！';
            $n_msg='失败';
        }
        $note='用户【'. get_user_name(D('SysUser')->self_id()).'】，ID【'.$proxy_id.'】，代理商【'.$proxy['proxy_name'].'('.$proxy['proxy_code'].')】证件保存'.$n_msg;
        $this->sys_log('代理商证件保存',$note);
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
        $proxy_id = trim(I('post.proxy_id'));

        if(D('SysUser')->is_top_proxy_admin()){
            $where['proxy_id'] = array(array('in',$this->os_proxy_ids),array('eq',D('SysUser')->self_proxy_id()),'or');
            $map['_complex'] = $where;
            $map['proxy_id'] = array('eq',$proxy_id);
        }else{
            $map['proxy_id'][] = array('in',$this->os_proxy_ids);
            $map['proxy_id'][] = array('eq',$proxy_id);
        }

        $map['status'] = array('neq',2);

        $proxy = M('Proxy')->where($map)->find();

        if(!$proxy){
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }

        if( empty($icense_img_num)){
            $msg = '营业执照编号格式错误！';
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

        if( $proxy['icense_img'] == '' && !$fileinfo['icense_img']){
            $msg = '请上传营业执照附件！';
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }

        $edit = array(
            'icense_img_num'        =>      $icense_img_num,
            'identity_img_num'      =>      $identity_img_num,
            'proxy_id'              =>      $proxy_id,
            'modify_user_id'        =>      D('SysUser')->self_id(),
            'modify_date'           =>      date("Y-m-d H:i:s",time()),
            );

        if($fileinfo['icense_img']){
            $edit['icense_img'] = $icense_img;
        }
        if($fileinfo['identity_img']){
            $edit['identity_img'] = $identity_img;
        }

        if(M('Proxy')->save($edit)){
            $msg = '代理商证件保存成功！';
            $status = 'success';
            $n_msg='保存成功';
        }else{
            $msg = '代理商证件保存失败！';
            $n_msg='保存失败';
        }
        $note='用户【'. get_user_name(D('SysUser')->self_id()).'】，ID【'.$proxy_id.'】，编辑代理商【'.$proxy['proxy_name'].'('.$proxy['proxy_code'].')】证件'.$n_msg;
        $this->sys_log('编辑代理商证件',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }




    /**
     *  代理商审核
     */
    public function approve(){

        $msg = '系统错误！';
        $status = 'error';
        $data = '';
        //$map['proxy.proxy_id'][] = array('in',$this->os_proxy_ids);

        if(IS_POST){

            $self_id = D('SysUser')->self_id();
            $time = date("Y-m-d H:i:s",time());
            $proxy_id = trim(intval(I('proxy_id')));
            $approve_status = trim(intval(I('post.approve_status')));
            $approve_remark = trim(I('post.approve_remark'));

            if(in_array($approve_status,array('1','2'))){
                
                $map['proxy.proxy_id'] = array('eq',$proxy_id);
                //$map['proxy.approve_status'] = array('eq',0);
                $proxy = M('')
                ->table('t_flow_proxy as proxy')
                ->where($map)
                ->find();
                //判断当前代理商的审核状态
                if($proxy){
                    if($proxy['approve_status'] == 0){
                        //判断自身是否为尚通管理员下面的管理员
                        if(D('SysUser')->is_top_proxy_admin()){
                            if($approve_status == 1){

                                if(!$proxy['icense_img']){
                                    $msg = '请上传营业执照附件！';
                                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                                }
                                if(!$proxy['icense_img_num']){
                                    $msg = '请输入营业执照编号！';
                                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                                }
                                /*
                                if(!$proxy['identity_img']){
                                    $msg = '请上传身份证附件！';
                                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                                }
                                
                                if(!$proxy['identity_img_num']){
                                    $msg = '请输入法人身份证！';
                                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                                }
                                */
                            }
                                $model = M('');
                                $model->startTrans();
                                $proxyedit = array(
                                    'approve_status'    =>      $approve_status,
                                    'approve_remark'    =>      $approve_remark,
                                    'proxy_id'          =>      $proxy_id,
                                    'approve_user_id'   =>      $self_id,
                                    'approve_date'      =>      $time,
                                    'modify_user_id'    =>      D('SysUser')->self_id(),
                                    'modify_date'       =>      date("Y-m-d H:i:s",time())
                                );

                                $proxy_edit = M('Proxy')->save($proxyedit);
                                $map = array();
                                $map['status'] = array('neq',2);
                                $map['is_manager'] = array('eq',1);
                                $map['proxy_id'] = array('eq',$proxy_id);
                                $user = M('Sys_user')->where($map)->find();

                                if($proxy_edit){
                                    if($approve_status == 1){
                                        $msg = '代理商审核通过成功！';
                                        $approve_status_msg="审核通过";
                                        $data = '代理商【'.$proxy['proxy_name'].'】已审核通过！<br>登录名称【'.$user['login_name_full'].'】<br>登录密码【123456】';
                                    }else{
                                        $msg = '代理商审核驳回成功！';
                                        $data = '';
                                        $approve_status_msg="审核驳回";
                                    }
                                    // $proxy_info=M('proxy')->where('proxy_id='.$proxy_id)->field('create_user_id,proxy_level')->find();
                                    $status = 'success';
                                    $model->commit();
                                    if($proxy['proxy_level']==1){
                                        $remind_content='您提交审核的代理商【'.$proxy['proxy_name'].'】已经【'.$approve_status_msg.'】，请知晓！';
                                        R('ObjectRemind/send_user',array(2,$remind_content,array($proxy['create_user_id'])));
                                    }

                                }else{
                                    $msg = '审核失败！';
                                    $model->rollback();
                                    $data = array();
                                    $approve_status_msg='审核失败';
                                }
                            $note='用户【'. get_user_name(D('SysUser')->self_id()).'】,ID【'.$proxy_id.'】，审核代理商【'.$proxy['proxy_name'].'('.$proxy['proxy_code'].')】'.$approve_status_msg;
                            $this->sys_log('代理商审核',$note);
                        }
                    }else{
                        $msg = '对不起，不可重复审核！';
                    }
                }
            }


            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));

        }else{
            
                $map['proxy.status'] = array('neq',2);
                $map['proxy.proxy_id'][] = array('eq',trim(intval(I('get.proxy_id'))));
                $proxy = M('')
                ->table('t_flow_proxy as proxy')
                ->field('proxy.proxy_name,proxy.email,proxy.proxy_code,proxy.proxy_id,proxy.tel,proxy.contact_name,proxy.contact_tel,proxy.operator,user.user_name,root_user.login_name_full,proxy.approve_status,proxy.approve_remark,proxy.approve_date,approve_user.user_name as approve_name,proxy.address,proxy.icense_img,proxy.icense_img_num,proxy.identity_img,proxy.identity_img_num,province.province_name,city.city_name')
                ->join('left join t_flow_sys_user as user on proxy.sale_id = user.user_id and user.status = 1')
                ->join('left join t_flow_sys_user as root_user on root_user.proxy_id = proxy.proxy_id and root_user.is_manager = 1')
                ->join('left join t_flow_sys_user as approve_user on approve_user.user_id = proxy.approve_user_id')
                ->join('t_flow_sys_province as province on province.province_id = proxy.province','left')
                ->join('t_flow_sys_city as city on city.city_id = proxy.city','left')
                ->where($map)
                ->find();
                if($proxy){
                    $type = trim(I('get.download'));
                    if(in_array($type,array('icense_img','identity_img'))){
                        parent::download('.'.$proxy[$type]);
                    }else{
                        $map = array();
                        $map['status'] = array('eq',1);
                        $map['operator_id'] = array('in',$proxy['operator']);
                        $proxy['operator'] = '';
                        foreach(M('Sys_operator')->where($map)->select() as $v){
                            $proxy['operator'] .= ','.$v['operator_name'];
                        }
                        $proxy['operator'] = substr($proxy['operator'],'1',strlen($proxy['operator'])-1);

                        $this->assign('is_top_proxy_admin',D('SysUser')->is_top_proxy_admin());
                        $this->assign('is_approve',$is_approve);
                        $this->assign('proxy',$proxy);
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

        $map['proxy.proxy_id'][] = array('in',$this->os_proxy_ids);
        $map['proxy.top_proxy_id'] = D('SysUser')->self_proxy_id();
        $map['proxy.proxy_id'][] = array('eq',trim(intval(I('proxy_id'))));
        $map['proxy.status'] = array('neq',2);
        $map['proxy.approve_status'] = array('eq',1);
        $proxy = M('')
                ->table('t_flow_proxy as proxy')
                ->where($map)
                ->find();

        if($proxy){

            if(IS_POST){
                $sale_id = trim(intval(I('post.sale_id')));
                //当设置了值的时候

                if((D('SysUser')->is_balance($sale_id) && $sale_id ) or !$sale_id ){
                    
                        $model = M('');
                        $model->startTrans();

                    if($sale_id == $proxy['sale_id']){
                        $is_edit = true;
                        $is_add = true;
                        $is_delete = true;
                    }else{
                        $edit = array(
                            'sale_id'       =>      $sale_id,
                            'proxy_id'      =>      $proxy['proxy_id'],
                            );
                        $is_edit = M('Proxy')->save($edit);

                        if($proxy['sale_id']){
                            //删除原有权限表中客户经理与企业的关系
                            $map = array();
                            $map['user_id'] = array('eq',$proxy['sale_id']);
                            $map['proxy_id'] = array('eq',$proxy['proxy_id']);
                            $Proxy_user_count = M('Proxy_user')->where($map)->count();
                            $Proxy_user_delete = M('Proxy_user')->where($map)->delete();
                            if($Proxy_user_count == $Proxy_user_delete){
                                $is_delete = true;
                            }else{
                                $is_delete = false;
                            }
                        }else{

                            $is_delete = true;
                        }
                        

                        //添加一条新的记录
                        if($sale_id){
                            //判断客户经理的ID是否是有查看所有的数据的权限 如果有 则不需要添加
                            $user = M('SysUser')->field('user_name,is_all_proxy')->find($sale_id);
                            if(!$user['is_all_proxy']){
                                $add = array(
                                    'user_id'               =>  $sale_id,
                                    'proxy_id'              =>  $proxy['proxy_id'],
                                    'create_user_id'        =>  D('SysUser')->self_id(),
                                    'create_date'           =>  date("Y-m-d H:i:s",time()),
                                );
                                $is_add = M('Proxy_user')->add($add);
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
                    $note='用户【'. get_user_name(D('SysUser')->self_id()).'】，ID【'.$proxy['proxy_id'].'】，给代理商【'.$proxy['proxy_name'].'('.$proxy['proxy_code'].')】设置客户经理'.$n_msg;
                    $this->sys_log('代理商设置客户经理',$note);
                }
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));

            }else{

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
                    $map['user_id'] = $proxy['sale_id'];
                    $map['status'] = array('neq',2);
                    $user = M('Sys_user')->where($map)->find();

                    $this->assign('user',$user);
                    $this->assign('depart_list',$depart_list);
                    $this->assign('user_list',$user_list);
                    $this->assign('proxy',$proxy);
                    $this->display();
                }
        }else{
             $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }

    }


    public function toggle_status(){
        $msg = '系统错误!';
        $status = 'error';
        $data = array();

        $proxy_id = trim(intval(I('post.proxy_id')));

        if($proxy_id){
            $map['proxy.status'] = array('neq',2);
            $map['proxy.proxy_id'][] = array('eq',$proxy_id);
            $map['proxy.proxy_id'][] = array('in',$this->os_proxy_ids);
            //$map['top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
            $map['proxy.approve_status'] = array('eq',1);
            $proxy = M('')
                ->table('t_flow_proxy as proxy')
                ->where($map)
                ->find();
            if($proxy && $proxy['approve_status'] == 1){

                //判断是否有权限操作
                $edit = array();
                if($proxy['status'] == 1){
                    $edit['status'] = 0;
                }else{
                    $edit['status'] = 1;
                }
                $edit['modify_user_id'] =   D('SysUser')->self_id();
                $edit['modify_date']    =   date("Y-m-d H:i:s",time());
                $edit['proxy_id'] = $proxy['proxy_id'];

                if(M('Proxy')->save($edit)){

                    if($proxy['status'] == 1){
                        $msg = '禁用成功!';
                    }else{
                        $msg = '启用成功!';
                    }
                    $n_msg='成功';
                    $status = 'success';
                    $data['status'] = 0;
                }else{
                    if($proxy['status'] == 1){
                        $msg = '禁用失败!';
                    }else{
                        $msg = '启用失败!';
                    }
                    $n_msg='失败';
                }
                $title=$proxy['status'] == 1?'禁用':'启用';
                $note='用户【'. get_user_name(D('SysUser')->self_id()).'】，ID【'.$proxy_id.'】，'.$title.'代理商【'.$proxy['proxy_name'].'('.$proxy['proxy_code'].')】'.$n_msg;
                $this->sys_log($title.'代理商',$note);
            }
        }
        return $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
    }



    /**
     *  超级管理员设置自己的代理商信息
     */
    public function set_proxy(){
        $msg = '系统错误';
        $status = 'error';

        if(!D('SysUser')->is_admin() && (D('SysUser')->self_user_type() == 2 or D('SysUser')->self_user_type() == 3) ){
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
            $proxy_id=D('SysUser')->self_proxy_id();
            $e_info=M('proxy')->where('proxy_id='.$proxy_id)->find();
           /* if(empty($tel) or isTel($tel)){*/
                //if($tel!='' && !D('Proxy')->check_tel($tel,D('SysUser')->self_proxy_id())){
                    if(!empty($contact_name) ){
                        if(!empty($contact_tel)){
                            if(isTel($contact_tel)){
                                if(empty($email) or isEmail($email)){
                                $edit = array(
                                    'proxy_id'      =>      $proxy_id,
                                    'tel'           =>      $tel,
                                    'email'         =>      $email,
                                    'address'       =>      $address,
                                    'province'      =>      $province,
                                    'city'          =>      $city,
                                    'contact_name'  =>      $contact_name,
                                    'contact_tel'   =>      $contact_tel,
                                    'modify_user_id'=>      D('SysUser')->self_id(),
                                    'modify_date'   =>      date("Y-m-d H:i:s",time())
                                    );
                                    if(M('proxy')->save($edit)){
                                        $msg = '企业信息设置成功！';
                                        $status = 'success';
                                        $n_msg='成功';
                                    }else{
                                        $msg = '企业信息设置失败！';
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
                                            $c_item.=$fg.'所属省【'. get_province_name($province).'】';
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
                                    $note='用户【'. get_user_name(D('SysUser')->self_id()).'】，ID【'.$proxy_id.'】，企业信息设置，代理商代理商【'.$e_info['proxy_name'].'('.$e_info['proxy_code'].')】'.$c_item.$n_msg;
                                    $this->sys_log('企业信息设置',$note);
                                }else{
                                    $msg = '请输入正确联系电话！';
                                }
                            }else{
                                $msg = '请输入联系电话！';
                            }
                        }else{
                            $msg = '请输入联系人';
                        }
                    }else{
                        $msg = '请输入正确邮箱!';
                    }
                /*}else{
                    $msg = '公司电话已存在!';
                }*/
            /*}else{
                $msg = '请输入正确公司电话!';
            }*/
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }else{

            $map['status'] = array('eq',1);
            $map['proxy_id']  = D('SysUser')->self_proxy_id();
            $proxy = M('Proxy')->where($map)->find();
            if($proxy){

                $map = array();
                $map['province_id'] = array('neq',1);
                $province_list = M('Sys_province')->where($map)->select();
                $this->assign('province_list',$province_list);

                $city_list = M('Sys_city')->select();
                $this->assign('city_list',$city_list);


                $this->assign('user_type',D('SysUser')->self_user_type());
                $this->assign('proxy',$proxy);
                $this->display();
            }else{
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
            }
            
        }
    }


    /**
     *  代理商权限分配
     */
    public function set_proxy_user(){
        $msg = '系统错误!';
        $status = 'error';
        $data = array();
        D("SysUser")->sessionwriteclose();
        if(IS_POST){

            $user_id = trim(I('post.user_id'));
            $proxy_ids = trim(I('post.proxy_ids'));
            $type = trim(I('post.type'));
            if(in_array($type,array('add','delete'))){

                if($proxy_ids){
                    if(!is_array($proxy_ids)){
                        $proxy_array = explode(',',$proxy_ids);

                    }else{
                        $proxy_array = $proxy_ids;
                        $proxy_ids = implode(',',$proxy_ids);
                    }
                    foreach($proxy_array as $k=>$v){
                        if(!intval($v)){
                            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                        }else{
                            $proxy_array[$k] = intval($v);
                        }
                    }
                    $user = M('Sys_user')->find($user_id);
                    if($user['user_id'] && $user['is_all_proxy'] == '0' && D('SysUser')->is_balance($user['user_id'])){
                        //判断id序列是否在都在自己的管辖内
                        $map['proxy_id'] = array('in',$proxy_ids);
                        $map['status'] = array('neq',2);
                        $map['top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
                        if(intval(M('Proxy')->where($map)->count()) === count($proxy_array)){
                            $model = M('');
                            $model->startTrans();
                            if($type == 'add'){

                                //删除部分数据 防止数据重复
                                $delete = D('Proxy')->delete_section($user_id,$proxy_ids);

                                //执行添加
                                $add_array = array();
                                foreach($proxy_array as $k=>$v){
                                    $add_array[] = array(
                                        'user_id'       =>      $user_id,
                                        'proxy_id'      =>      $v,
                                        'create_user_id'=>      D('SysUser')->self_id(),
                                        'create_date'   =>      date("Y-m-d H:i:s",time())
                                        );
                                }
                                $add = M('Proxy_user')->addAll($add_array);

                                $edit = true;
                                
                            }elseif($type == 'delete'){
                                //删除部分数据
                                $delete = D('Proxy')->delete_section($user_id,$proxy_ids);

                                $add = true;

                                //判断删除的数据中是否有自己是客户经理
                                $map = array();
                                $map['status'] = array('neq',2);
                                $map['proxy_id'] = array('in',$proxy_ids);
                                $map['sale_id'] = array('eq',$user_id);
                                $edit_count = M('Proxy')->where($map)->count();
                                if($edit_count){
                                    $edit_array = array(
                                    'sale_id'           => '',
                                    'modify_user_id'    =>  D('SysUser')->self_id(),
                                    'modify_date'       =>   date("Y-m-d H:i:s",time())
                                    );
                                    $edit = M('Proxy')->where($map)->save($edit_array);
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
                            $title=$type == 'add'?'分配':'解除';
                            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，为员工【'.get_user_name($user_id).'】'.$title.'代理商权限'.$n_msg;
                            $this->sys_log('为员工'.$title.'代理商权限',$note);
                        }
                    }
                }

            }else{

                if(in_array($type,array('alldelete','alladd'))){
                    $user = M('Sys_user')->find($user_id);
                    if($type == 'alldelete'){
                        if($user['user_id'] && $user['is_all_proxy'] == '1' && D('SysUser')->is_balance($user['user_id'])){
                            $edit = array(
                                'user_id'       =>      $user_id,
                                'is_all_proxy'  =>      0, 
                                );
                            if(M('Sys_user')->save($edit)){
                                $msg = '为员工【'.$user['user_name'].'】解除所有代理商权限成功！';
                                $status ='success';
                                $n_msg='成功';
                            }else{
                                $msg = '解除所有代理权限失败！';
                                $n_msg='失败';
                            }
                            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，为员工【'.$user['user_name'].'】解除所有代理商权限'.$n_msg;
                            $this->sys_log('解除所有代理权限',$note);
                        }
                    }elseif($type == 'alladd'){

                        if($user['user_id'] && $user['is_all_proxy'] == '0' && D('SysUser')->is_balance($user['user_id'])){
                            $model = M('');
                            $model->startTrans();

                            $edit = array(
                                'user_id'       =>      $user_id,
                                'is_all_proxy'  =>      1, 
                                );
                            $edit = M('Sys_user')->save($edit);
                            
                            $map = array();
                            $map['user_id'] = array('eq',$user['user_id']);
                            $count = M('Proxy_user')->where($map)->count();

                            if($count){
                                $deletecount = M('Proxy_user')->where($map)->delete();

                                if($count ==  $deletecount){
                                    $delete = true;
                                }else{
                                    $delete = false;
                                }
                            }else{
                                $delete = true;
                            }
                            
                            if($edit && $delete){
                                $msg = '为员工【'.$user['user_name'].'】分配所有代理商权限成功！';
                                $status ='success';
                                $model->commit();
                                $n_msg='成功';
                            }else{
                                $msg = '分配所有代理权限失败！';
                                $model->rollback();
                                $n_msg='失败';
                            }
                            $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，为员工【'.$user['user_name'].'】分配所有代理商权限'.$n_msg;
                            $this->sys_log('分配所有代理权限',$note);
                        }
                    }  
                }
            }
            

            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));

        }else{
            $type = trim(I('get.type'));
            $user_id = trim(I('get.user_id'));

            if($type !== 'getlist'){

                $map['user.proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
                $map['user.is_manager'] = array('eq',0);
                $map['user.status'] = array('eq',1);

                $user_list = M('Sys_user as user')
                ->field('user.*,depart.depart_name')
                ->join('t_flow_sys_depart as depart on user.depart_id = depart.depart_id','left')
                ->where($map)
                ->order('depart.depart_id desc')
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

                    if($user['is_all_proxy'] == '1' ){
                         $this->assign('user_list', get_sort_no($user_list,0));
                         $data['msg'] = '用户【'.$user['user_name'].'】拥有所有代理商权限！';
                         $data['status'] = 'success';
                         $data['is_all_proxy'] = 1;
                         $this->assign('jsondata',json_encode($data));
                         $this->assign('data',$data);
                         $this->assign('user',$user);
                         $this->display();
                         exit;
                    }

                    $map = array();
                    $map['proxy.status'] = array('neq',2);
                    $map['proxy.top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
                    $map['proxy.approve_status'] = array('eq',1);

                    $proxylist = M('')
                    ->table('t_flow_proxy as proxy')
                    ->field("proxy.proxy_id,proxy.proxy_name,proxy.proxy_code")
                    ->where($map)
                    ->select();

                    if($proxylist){
                        $map = array();
                        $map['user_id'] = array('eq',$user['user_id']);
                        $proxy_ids = M('Proxy_user')->field('proxy_id')->where($map)->select();
                        if($proxy_ids){
                            foreach($proxy_ids as $k=>$v){
                                $ids[] = $v['proxy_id'];
                            }
                            foreach($proxylist as $k=>$v){
                                if( in_array($v['proxy_id'],$ids) ){
                                    $proxy_list['have'][] = $v;
                                }else{
                                    $proxy_list['no'][] = $v;
                                }
                            }
                            if(!$proxy_list['no']){
                                $proxy_list['no'] = array();
                            }

                        }else{
                            $proxy_list['no'] = $proxylist;
                            $proxy_list['have'] = array();
                        }
                        
                    }else{
                        $proxy_list['have'] = array();
                        $proxy_list['no'] = array();
                    }

                    $proxy_list['have'] = get_sort_no($proxy_list['have'],0);
                    $proxy_list['no'] = get_sort_no($proxy_list['no'],0);
                    $data = $proxy_list;
                    $data['is_all_proxy'] = '0';
                    $this->assign('data',$data);

                    $this->assign('user',$user);
                    $this->assign('user_list', get_sort_no($user_list,0));
                    $this->display();
                    
                }else{
                    $this->assign('user_list', get_sort_no($user_list,0));
                    $this->display();
                }

            }else{

                $user_id = trim(intval(I('get.user_id')));
                $user = M('SysUser')->find($user_id);
                if($user['user_id'] && D('SysUser')->is_balance($user['user_id'])){
                    if($user['is_all_proxy'] == '1' ){

                         $msg = '员工【'.$user['user_name'].'】拥有所有代理商权限！';
                         $status = 'success';
                         $proxy_list['have'] = array();
                         $proxy_list['no'] = array();
                         $data = $proxy_list;
                         $data['is_all_proxy'] = 1;
                         $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                         exit;
                    }
                        $map = array();
                        $map['proxy.status'] = array('neq',2);
                        $map['proxy.top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
                        $map['proxy.approve_status'] = array('eq',1);

                        $proxylist = M('')
                        ->table('t_flow_proxy as proxy')
                        ->field("proxy.proxy_id,proxy.proxy_name,proxy.proxy_code")
                        ->where($map)
                        ->select();

                        if($proxylist){
                            $map = array();
                            $map['user_id'] = array('eq',$user_id);
                            $proxy_ids = M('ProxyUser')->field('proxy_id')->where($map)->select();
                            if($proxy_ids){
                                foreach($proxy_ids as $k=>$v){
                                    $ids[] = $v['proxy_id'];
                                }
                                foreach($proxylist as $k=>$v){
                                    if( in_array($v['proxy_id'],$ids) ){
                                        $proxy_list['have'][] = $v;
                                    }else{
                                        $proxy_list['no'][] = $v;
                                    }
                                }
                                if(!$proxy_list['no']){
                                    $proxy_list['no'] = array();
                                }

                            }else{
                                $proxy_list['no'] = $proxylist;
                                $proxy_list['have'] = array();
                            }
                            
                        }else{
                            $proxy_list['have'] = array();
                            $proxy_list['no'] = array();
                        }

                        $msg = '';
                        $status = 'success';
                        $proxy_list['have'] = get_sort_no($proxy_list['have'],0);
                        $proxy_list['no'] = get_sort_no($proxy_list['no'],0);
                        $data = $proxy_list;
                        $data['is_all_proxy'] = $user['is_all_proxy'];
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
        $proxy_id  = trim(I('post.proxy_id'));
        $map['status'] = array('neq',2);
        if(D('SysUser')->is_top_proxy_admin()){
            $where['proxy_id'] = array(array('in',$this->os_proxy_ids),array('eq',D('SysUser')->self_proxy_id()),'or');
            $map['_complex'] = $where;
            $map['proxy_id'] = array('eq',$proxy_id);
        }else{
            $map['proxy_id'][] = array('in',$this->os_proxy_ids);
            $map['proxy_id'][] = array('eq',$proxy_id);
        }
        $map['approve_status'] = array('eq',1);
        $proxy = M('Proxy')->where($map)->find();

        if($proxy){
            $map = array();
            $map['is_manager'] = array('eq',1);
            $map['proxy_id'] = array('eq',$proxy['proxy_id']);
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
                    $data = '重置代理商【'.$proxy['proxy_name'].'】登录密码成功！<br>登录名【'.$user['login_name_full'].'】<br>新密码【'.$pass.'】';
                    $status = 'success';
                    $n_msg='成功';
                }else{
                    $msg = '重置失败！';
                    $n_msg='失败';
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$proxy['proxy_id'].'】，重置代理商【'.$proxy['proxy_name'].'('.$proxy['proxy_code'].')】登录密码'.$n_msg;
                $this->sys_log('重置代理商登录密码',$note);
            }

        }
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
    }


    


    /**
     *  代理商详情
     */
    public function show(){
        $msg = '系统错误!';
        $status = 'error';
        if( D('SysUser')->self_user_type()!=1 ){
            $proxy_ids=D("Proxy")->proxy_approve_child_ids();
            $where['proxy.proxy_id'][] = array(array('in',$proxy_ids),array('eq',D('SysUser')->self_proxy_id()),'or');
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
        }
        $proxy_id = trim(intval(I('get.proxy_id')));
        $map['proxy.status'] = array('neq',2);
        $map['proxy.proxy_id'] = array('eq',$proxy_id);

        $proxy = M('Proxy as proxy')
        ->field('proxy.proxy_id,proxy.contact_tel,proxy.proxy_code,proxy.proxy_name,proxy.tel,proxy.contact_name,
        proxy.email,proxy.operator,proxy.address,proxy.status,proxy.proxy_level,proxy.icense_img,
        proxy.approve_status,proxy.approve_date,proxy.approve_remark,approve_user.user_name as approve_name,
        sale_user.user_name as sale_name,sale_user.mobile as sale_mobile,top_proxy.proxy_name as top_name,
        top_proxy.proxy_code as top_code,top_proxy.tel as top_tel,top_proxy.contact_name as top_contact_name,
        top_proxy.address as top_address,account.account_balance,account.freeze_money,admin_user.user_name as admin_name,
        admin_user.login_name_full as admin_login_name,admin_user.mobile as admin_mobile,admin_user.email as admin_email,
        proxy.icense_img_num,proxy.icense_img,proxy.identity_img_num,proxy.identity_img,city.city_name,
        province.province_name,proxy.refund_status')
        ->join('t_flow_sys_user as sale_user on sale_user.user_id = proxy.sale_id and sale_user.status = 1','left')
        ->join('t_flow_sys_user as approve_user on approve_user.user_id = proxy.approve_user_id and approve_user.status = 1','left')
        ->join('t_flow_proxy as top_proxy on top_proxy.proxy_id = proxy.top_proxy_id and top_proxy.status = 1','left')
        ->join('t_flow_proxy_account as account on account.proxy_id = proxy.proxy_id','left')
        ->join('t_flow_sys_user as admin_user on admin_user.proxy_id = proxy.proxy_id and admin_user.is_manager = 1','left')
        ->join('t_flow_sys_province as province on province.province_id = proxy.province','left')
        ->join('t_flow_sys_city as city on city.city_id = proxy.city','left')
        ->where($map)->find();

        if($proxy){
            $type = I('get.download');
            if(in_array($type,array('icense_img','identity_img'))){
                parent::download('.'.$proxy[$type]);
            }else{
                $i = $proxy['proxy_level'] - D('SysUser')->self_proxy_level();
                if(isset($this->level[$i])){
                    $proxy['proxy_level'] = $this->level[$i];
                }

                $map = array();
                $map['status'] = array('eq',1);
                $map['operator_id'] = array('in',$proxy['operator']);
                $proxy['operator'] = '';
                foreach(M('Sys_operator')->where($map)->select() as $v){
                    $proxy['operator'] .= ','.$v['operator_name'];
                }
                $proxy['operator'] = substr($proxy['operator'],'1',strlen($proxy['operator'])-1);
                $this->assign('proxy',$proxy);
                $this->display();
            }
            
        }else{
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }

    }


    public function approve_index(){
        D("SysUser")->sessionwriteclose();
        $proxy_code = trim(I('get.proxy_code'));    
        $proxy_name = trim(I('get.proxy_name'));
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
        if($proxy_code){
            $map['proxy.proxy_code'] = array('like','%'.$proxy_code.'%');
        }
        if($proxy_name){
            $map['proxy.proxy_name'] = array('like','%'.$proxy_name.'%');
        }
        
        
       /* if($approve_status == '0' or $approve_status == '1' or $approve_status=='2'){
            $map['proxy.approve_status'] = array('eq',$approve_status);
        }elseif($approve_status == ''){
            $_GET['approve_status'] = 0;
            $map['proxy.approve_status'] = array('eq',0);
        }*/
        if($approve_status ==""){
            $map['proxy.approve_status'] =0;
        }else{
            if($approve_status!=9){
                $map['proxy.approve_status'] = $approve_status;
            }
        }


        if($create_start_datetime or $create_end_datetime){
            if($create_start_datetime && $create_end_datetime){
                $map['proxy.create_date']  = array('between',array($create_start_datetime,$create_end_datetime));
            }elseif($create_start_datetime){
                $map['proxy.create_date'] = array('EGT',$create_start_datetime);
            }elseif($create_end_datetime){
                $map['proxy.create_date'] = array('ELT',$create_end_datetime);
            }
        }

        if($approve_start_datetime or $approve_end_datetime){
            if($approve_start_datetime && $approve_end_datetime){
                $map['proxy.approve_date']  = array('between',array($approve_start_datetime,$approve_end_datetime));
            }elseif($approve_start_datetime){
                $map['proxy.approve_date'] = array('EGT',$approve_start_datetime);
            }elseif($approve_end_datetime){
                $map['proxy.approve_date'] = array('ELT',$approve_end_datetime);
            }
        }

        $map['proxy.status'] = array('neq',2);

        //$map['proxy.top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
        //将已审核通过的做数据权限
        /*$map_status['proxy.proxy_id'] = array('in',$this->os_proxy_ids);
        $map_status['proxy.approve_status'] = array('eq',1);
        $map_status['_logic'] = 'and';
        $where['_complex'] = $map_status;
        $where['proxy.approve_status'] = array('neq',1);
        $where['_logic'] = 'or';
        $map['_complex'] = $where;*/
        if(D("SysUser")->self_user_type()==2){
            $proxy_ids=D("proxy")->proxy_approve_child_ids();
        }else{
            $proxy_ids=$this->os_proxy_ids;
        }
        $map['proxy.proxy_id'] =array('in',$proxy_ids);
      /*  if(D('SysUser')->is_top_proxy_admin() == false ){
            $where['proxy.top_proxy_id'] = D('SysUser')->self_proxy_id();
            $where['proxy.proxy_id'] = D('SysUser')->self_proxy_id();
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
        }*/
        //var_dump($map);exit;
        $model = M('');

        $count = $model
            ->table('t_flow_proxy as proxy')
            ->field('proxy.*,create_user.user_name as create_name,approve_user.user_name as approve_name')
            ->join('t_flow_sys_user as create_user on create_user.user_id = proxy.create_user_id','left')
            ->join('t_flow_sys_user as approve_user on approve_user.user_id = proxy.approve_user_id','left')
            ->where($map)
            ->count();

        $Page       = new Page($count,20);
        $show       = $Page->show();

        $proxy_list = $model
        ->table('t_flow_proxy as proxy')
        ->field('proxy.*,create_user.user_name as create_name,approve_user.user_name as approve_name')
        ->join('t_flow_sys_user as create_user on create_user.user_id = proxy.create_user_id','left')
        ->join('t_flow_sys_user as approve_user on approve_user.user_id = proxy.approve_user_id','left')
        ->limit($Page->firstRow.','.$Page->listRows)
        ->order('proxy.modify_date Desc')
        ->where($map)
        ->select();

        

        $this->assign('page',$show);
        $this->assign('proxy_list', get_sort_no($proxy_list, $Page->firstRow));
        $this->display();

    }



    /**
     *  审核编辑功能
     */
    public function approve_edit(){
        $msg = '系统错误!';
        $status = 'error';

        $map['proxy.status'] = array('neq',2);
        $map['proxy.proxy_id'] = array('eq',intval(I('get.proxy_id')));
        $map['proxy.approve_status'] = array('neq',1);
        $proxy = M('')
        ->table('t_flow_proxy as proxy')
        ->where($map)
        ->find();
        if($proxy){

            //计算上级的支持运营商
            $me_proxy = M('Proxy')->find($proxy['top_proxy_id']);
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
            $this->assign('operator_list',$operator_list);
            $this->assign('proxy',$proxy);
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
            $proxy_name = trim(I('post.proxy_name'));
            $operator_list = I('post.operator_list');
            $address = trim(I('post.address'));
            $contact_tel = trim(I('post.contact_tel'));
            $contact_name = trim(I('post.contact_name'));
            $email = trim(I('post.email'));
            $proxy_id = I('post.proxy_id');
            $province = trim(I('post.province_id'));
            $city = trim(I('post.city_id'));
            $operator = implode(',',$operator_list);

            if(empty($tel) or isTel($tel)){
                if(empty($email) or isEmail($email)){
                    if(!empty($operator)){
                            if(!empty($proxy_name)){
                                if(!empty($contact_name)){
                                    if(!empty($contact_tel)){
                                        if(isTel($contact_tel)){
                                            if(!D('Proxy')->check_proxy_name($proxy_name,$proxy_id)){
                                                $map['proxy.status'] = array('neq',2);
                                                $map['proxy.proxy_id'][] = array('eq',$proxy_id);
                                                $map['proxy.approve_status'] = array('neq',1);
                                                $proxy = M('Proxy as proxy')->where($map)->find();
                                                if($proxy){
                                                    if(D('Proxy')->checkoperator($operator_list,$proxy['top_proxy_id'])){
                                                        $edit = array(
                                                            'tel'               =>      $tel,
                                                            'proxy_name'        =>      $proxy_name,
                                                            'contact_tel'       =>      $contact_tel,
                                                            'contact_name'      =>      $contact_name,
                                                            'address'           =>      $address,
                                                            'email'             =>      $email,
                                                            'proxy_id'          =>      $proxy_id,
                                                            'operator'          =>      $operator,
                                                            'province'          =>      $province,
                                                            'city'              =>      $city,
                                                            'modify_user_id'    =>      D('SysUser')->self_id(),
                                                            'modify_date'       =>      date("Y-m-d H:i:s",time()),
                                                            );
                                                        if(M('Proxy')->save($edit)){
                                                            $msg = '编辑代理商成功！';
                                                            $status = 'success';
                                                            $n_msg='成功';
                                                        }else{
                                                            $msg = '编辑代理商失败！';
                                                            $n_msg='失败';
                                                        }
                                                        $c_item='';
                                                        $c_item.=$proxy_name===$proxy['proxy_name']?'':'代理商名称【'. $proxy_name.'】';
                                                        $fg=!empty($c_item)?'，':'';
                                                        $c_item.=$contact_name===$proxy['contact_name']?'':$fg.'联系人【'. $contact_name.'】';
                                                        $fg=!empty($c_item)?'，':'';
                                                        $c_item.=$tel===$proxy['tel']?'':$fg.'联系电话【'. $tel.'】';
                                                        if($contact_tel!==$proxy['contact_tel']){
                                                            $fg=!empty($c_item)?'，':'';
                                                            $c_item.=empty($contact_tel)?$fg.'清除公司电话':$fg.'公司电话【'. $contact_tel.'】';
                                                        }
                                                        if($email!==$proxy['email']){
                                                            $fg=!empty($c_item)?'，':'';
                                                            $c_item.=empty($email)?$fg.'清除邮箱':$fg.'邮箱【'. $email.'】';
                                                        }
                                                        if($province!==$proxy['province']){
                                                            $fg=!empty($c_item)?'，':'';
                                                            if($province!='' || $province!=='0' ){
                                                                $c_item.=$fg.'所属省【'. get_province_name($province).'】';
                                                            }else{
                                                                $c_item.=$fg.'清除所属省';
                                                            }
                                                        }
                                                        if($city!==$proxy['city']){
                                                            $fg=!empty($c_item)?'，':'';
                                                            if($city!='' || $city!=='0'){
                                                                $c_item.=$fg.'所属市【'. get_city_name($city).'】';
                                                            }else{
                                                                $c_item.=$fg.'清除所属市';
                                                            }
                                                        }
                                                        if($address!==$proxy['address']){
                                                            $fg=!empty($c_item)?'，':'';
                                                            $c_item.=empty($address)?$fg.'清除地址':$fg.'地址【'. $address.'】';
                                                        }

                                                        $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$proxy_id.'】，编辑代理商【'.$proxy['proxy_name'].'('.$proxy['proxy_code'].')】：'.$c_item.$n_msg;
                                                        $this->sys_log('编辑代理商',$note);
                                                    }
                                                }
                                            }else{
                                                $msg = '代理商名称已存在！';
                                            }
                                        }else{
                                            $msg = '请输入正确联系电话！';
                                        }
                                    }else{
                                        $msg = '请输入联系人电话！';
                                    }
                                }else{
                                    $msg = '请输入联系人！';
                                }
                            }else{
                                $msg = '请输入代理商名称!';
                            }

                    }else{
                        $msg = '请勾选支持运营商！';
                    }
                }else{
                    $msg = '请输入正确的邮箱！';
                }
            }else{
                $msg = '请输入正确的公司电话！';
            }
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }
    
    /** 
     * 重新提交审核申请
     */
    public function approve_again(){
        $msg = '系统错误!';
        $status = 'error';
        if(IS_POST){
            $proxy_id = intval(I('post.proxy_id'));

            $map['proxy_id']= array('eq',$proxy_id);
            $map['status'] = array('neq',2);
            $map['approve_status'] = array('eq',2);
            $proxy = M('Proxy')->where($map)->find();
            if($proxy){
                $edit = array(
                    'proxy_id'              =>      $proxy_id,
                    'approve_status'        =>      0,
                    'approve_user_id'       =>      '',
                    'approve_date'          =>      '',
                    'approve_remark'        =>      '',
                    'modify_user_id'        =>      D('SysUser')->self_id(),
                    'modify_date'           =>      date("Y-m-d H:i:s",time()),
                    );

                if(M('Proxy')->save($edit)){
                    $msg = '重新提交审核申请成功！请耐心等待审核...';
                    $status = 'success';
                    $n_msg='成功';
                    //R('ObjectRemind/send_user',array(5,'新增【'.$proxy['proxy_name'].'】代理商信息已重新送审，请注意审核！',array($proxy['approve_user_id'])));
                }else{
                    $msg = '重新提交失败！';
                    $n_msg='失败';
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$proxy_id.'】，重新提交代理商【'.$proxy['proxy_name'].'('.$proxy['proxy_code'].')】审核申请'.$n_msg;
                $this->sys_log('代理商重新提交审核',$note);
            }
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }

    /** 
     *  删除代理商
     */

    public function delete(){
        $msg = '系统错误!';
        $status = 'error';
        $model = M('');
        $model->startTrans();
        $proxy_id=intval(I('post.proxy_id'));
        $map['proxy_id'] = array('eq',intval(I('post.proxy_id')));
        $map['approve_status'] = array('neq',1);
        $proxy_edit = array(
            'status'            =>      2,
            'modify_user_id'    =>      D('SysUser')->self_id(),
            'modify_date'       =>      date("Y-m-d H:i:s",time()),
            );
        $proxy_info = M('Proxy')->where($map)->find();
        $proxyedit = M('Proxy')->where($map)->save($proxy_edit);
        $map = array();
        $map['proxy_id'] = array('eq',intval(I('post.proxy_id')));
        $user_edit = array(
            'status'        =>      2
            );
        $useredit = M('Sys_user')->where($map)->save($user_edit);
        if($proxyedit && $useredit){
            $msg = '删除代理商成功！';
            $status = 'success';
            $model->commit();
            $n_msg='成功';
        }else{
            $msg = '删除失败！';
            $model->rollback();
            $n_msg='失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$proxy_id.'】，删除代理商【'.$proxy_info['proxy_name'].'('.$proxy_info['proxy_code'].')】'.$n_msg;
        $this->sys_log('删除代理商',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));

    }


/** 
     *  删除代理商
     */

    public function approve_delete(){
        $msg = '系统错误!';
        $status = 'error';
        $model = M('');
        $model->startTrans();
        $proxy_id=intval(I('post.proxy_id'));
        $map['proxy_id'] = array('eq',intval(I('post.proxy_id')));
        $map['approve_status'] = array('neq',1);
        $proxy_edit = array(
            'status'            =>      2,
            'modify_user_id'    =>      D('SysUser')->self_id(),
            'modify_date'       =>      date("Y-m-d H:i:s",time()),
            );
        $proxy_info = M('Proxy')->where($map)->find();
        $proxyedit = M('Proxy')->where($map)->save($proxy_edit);
        $map = array();
        $map['proxy_id'] = array('eq',intval(I('post.proxy_id')));
        $user_edit = array(
            'status'        =>      2
            );
        $useredit = M('Sys_user')->where($map)->save($user_edit);
        if($proxyedit && $useredit){
            $msg = '删除代理商成功！';
            $status = 'success';
            $model->commit();
            $n_msg='成功';
        }else{
            $msg = '删除失败！';
            $model->rollback();
            $n_msg='失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$proxy_id.'】，删除代理商【'.$proxy_info['proxy_name'].'('.$proxy_info['proxy_code'].')】'.$n_msg;
        $this->sys_log('删除代理商',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));

    }




    /**
     *  代理商详情
     */
    public function approve_show(){

        $msg = '系统错误!';
        $status = 'error';

        $proxy_id = intval(I('get.proxy_id'));
        $map['proxy.status'] = array('neq',2);
        $map['proxy.proxy_id'][] = array('eq',$proxy_id);
        $proxy = M('Proxy as proxy')
        ->field('proxy.*,approve_user.user_name as approve_name,province.province_name,city.city_name')
        ->join('left join t_flow_sys_user as approve_user on approve_user.user_id = proxy.approve_user_id and approve_user.status = 1')
        ->join('t_flow_sys_province as province on province.province_id = proxy.province','left')
        ->join('t_flow_sys_city as city on city.city_id = proxy.city','left')
        ->where($map)->find();
        if($proxy){
            $type = I('get.download');
            if(in_array($type,array('icense_img','identity_img'))){
                parent::download('.'.$proxy[$type]);
            }else{
                $map = array();
                $map['status'] = array('eq',1);
                $map['operator_id'] = array('in',$proxy['operator']);
                $proxy['operator'] = '';
                foreach(M('Sys_operator')->where($map)->select() as $v){
                    $proxy['operator'] .= ','.$v['operator_name'];
                }
                $proxy['operator'] = substr($proxy['operator'],'1',strlen($proxy['operator'])-1);
                $this->assign('proxy',$proxy);
                $this->display();
            }   
        }else{
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }

    //切换退款状态
    public function set_refund_status(){
        $msg = '系统错误!';
        $status = 'error';
        $data = array();

        $proxy_id = trim(intval(I('post.proxy_id')));

        if($proxy_id){
            $map['proxy.status'] = array('neq',2);
            $map['proxy.proxy_id'][] = array('eq',$proxy_id);
            $map['proxy.proxy_id'][] = array('in',$this->os_proxy_ids);
            //$map['top_proxy_id'] = array('eq',D('SysUser')->self_proxy_id());
            $map['proxy.approve_status'] = array('eq',1);
            $proxy = M('')
                ->table('t_flow_proxy as proxy')
                ->where($map)
                ->find();
            if($proxy && $proxy['approve_status'] == 1){

                //判断是否有权限操作
                $edit = array();
                if($proxy['refund_status'] == 1){
                    $edit['refund_status'] = 0;
                }else{
                    $edit['refund_status'] = 1;
                }
                $edit['modify_user_id'] =   D('SysUser')->self_id();
                $edit['modify_date']    =   date("Y-m-d H:i:s",time());
                $edit['proxy_id'] = $proxy['proxy_id'];

                if(M('Proxy')->save($edit)){

                    if($proxy['refund_status'] == 1){
                        $msg = '退款状态禁用成功!';
                    }else{
                        $msg = '退款状态启用成功!';
                    }
                    $n_msg='成功';
                    $status = 'success';
                    $data['status'] = 0;
                }else{
                    if($proxy['refund_status'] == 1){
                        $msg = '退款状态禁用失败!';
                    }else{
                        $msg = '退款状态启用失败!';
                    }
                    $n_msg='失败';
                }
                $title=$proxy['refund_status'] == 1?'禁用退款状态':'启用退款状态';
                $note='用户【'. get_user_name(D('SysUser')->self_id()).'】，ID【'.$proxy_id.'】，'.$title.'代理商【'.$proxy['proxy_name'].'('.$proxy['proxy_code'].')】'.$n_msg;
                $this->sys_log($title.'代理商',$note);
            }
        }
        return $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
    }

    public function toggle_message(){
        $msg = '系统错误！';
        $status = 'error';
        if(IS_POST && IS_AJAX){
            $proxy_id = I('post.proxy_id',0,'int');
            if(!empty($proxy_id)){
                $proxyinfo = M('proxy')->where(array("proxy_id"=>$proxy_id))->find();
                if($proxyinfo){
                    $message_status = $proxyinfo['message_status'] == 1 ? "0" : "1";
                    $edit = array(
                        'message_status'=> $message_status
                    );
                    $edit = M('Proxy')->where(array( 'proxy_id'=>$proxy_id))->save($edit);
                    $status_name = $proxyinfo['message_status'] == 1 ? "禁用短信" : "启用短信";
                    if($edit){
                        $status = 'success';
                        $msg = $status_name.'功能成功!';
                        $n_msg='成功';
                    }else{
                        $msg = $status_name.'功能失败!';
                        $n_msg='失败';
                    }
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【' . $proxy_id . '】，代理商【'.$proxyinfo['proxy_name'].'('.$proxyinfo['proxy_code'].')'.'】'.$status_name.$n_msg;
                    $this->sys_log('代理商'.$status_name,$note);
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
