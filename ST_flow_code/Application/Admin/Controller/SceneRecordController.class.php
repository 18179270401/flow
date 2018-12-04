<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class SceneRecordController extends CommonController{
	/*
	 *领取流量记录
	 */
	public function  index(){
        D("SysUser")->sessionwriteclose();
        $use_t=D("SysUser")->self_user_type();
        $user_type = D('SysUser')->self_user_type()-1;
        $user_id = D('SysUser')->self_id();
        $self_proxy_id = D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        $mobile = trim(I('mobile'));
        $user_name=trim(I('user_name'));
        $product_name = trim(I('product_name'));
        $activity_name = trim(I('activity_name'));
        $start_datetime = trim(I('start_datetime'));
        $end_datetime = trim(I('end_datetime')) ;
        //状态 0全部；1成功；2失败
        $status = trim(I('status'));
        /*if($status == ''){
            $status = 1;
        }*/

        $where = array();

        if($use_t!=3){
            if(!empty($user_name)) {
                $where1['proxy_name'] = array("like","%".$user_name."%");
                $where1['enterprise_name'] = array("like","%".$user_name."%");
                $where1["_logic"] = "or";
                $where[] = $where1;
            }
        }
        if($use_t==3) {
            $where['sr.user_type'] = $user_type;
            if ($user_type == '1') {
                //代理商
                $where['sr.proxy_id'] = $self_proxy_id;
            } else if ($user_type == '2') {
                //企业
                $where['sr.enterprise_id'] = $self_enterprise_id;
            }
        }
        if($status!=9){
            if($status == 1){
                $where['sr.order_id'] = array('neq','');
            }

            if($status == 2){
                $where['sr.order_id'] = array(array('eq',''),array('exp','is null'), 'or');
            }
        }

        if($mobile){
            $where['sr.mobile'] = $mobile;
        }
        if($activity_name){
            $where1['sua.user_activity_name'] = array("like","%".$activity_name."%");
            $where1['sa.activity_name']=array("like","%".$activity_name."%");
            $where1['_logic']="or";
            $where[]=$where1;
        }

        if($product_name){
            $where['sr.product_name']=array('like','%'.$product_name.'%');
        }

        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['sr.receive_date'] = array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['sr.receive_date'] = array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['sr.receive_date'] = array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            //$end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $end_datetime= strtotime($start_datetime)-2592000;
            $e_time=start_time(date('Y-m-d',$end_datetime));
            $where['sr.receive_date']= array('between',array($e_time,$start_datetime));
        }
        $list=D('SceneInfo')->get_scene_record_lists($where);
        $this->assign("use",$use_t);
        //获取活动列表
        $scene_activitys = D('SceneInfo')->get_scene_activity_all();
        $this->assign('scene_activitys',$scene_activitys);  //数据列表
        //加载模板
        $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
        $end_datetime= strtotime($start_datetime)-2592000;
        $e_time=start_time(date('Y-m-d',$end_datetime));
        $this->assign('default_end',$start_datetime);
        $this->assign('default_start',$e_time);
        $this->assign('list',$list['list']);  //数据列表
        $this->assign('page',$list['page']);  //分页
        $this->display();        //模板
    }

    public function show(){
        $model=M('operator_record as ro');
        $id = trim(I('get.record_id'));
        $info = D('SceneInfo')->get_scene_record_detail($id);
        $use_t=D('SysUser')->self_user_type();
        $this->assign("usr",$use_t);
        $this->assign('info',$info);
        $this->display();
    }

    /**
    *导出EXCEL
    **/
    public function export_excel(){
        $use_t=D("SysUser")->self_user_type();
        $user_type = D('SysUser')->self_user_type()-1;
        $user_id = D('SysUser')->self_id();
        $self_proxy_id = D('SysUser')->self_proxy_id();
        $self_enterprise_id = D('SysUser')->self_enterprise_id();
        $mobile = trim(I('mobile'));
        $user_name=trim(I('user_name'));
        $product_name = trim(I('product_name'));
        $activity_id = trim(I('activity_id'));
        $start_datetime = trim(I('start_datetime'));
        $end_datetime = trim(I('end_datetime')) ;
        //状态 0全部；1成功；2失败
        $status = trim(I('status'));
        /*if($status == ''){
            $status = 1;
        }*/

        $where = array();

        if($use_t!=3){
            if(!empty($user_name)) {
                $where1['proxy_name'] = array("like","%".$user_name."%");
                $where1['enterprise_name'] = array("like","%".$user_name."%");
                $where1["_logic"] = "or";
                $where[] = $where1;
            }
        }
        if($use_t==3) {
            $where['sr.user_type'] = $user_type;
            if ($user_type == '1') {
                //代理商
                $where['sr.proxy_id'] = $self_proxy_id;
            } else if ($user_type == '2') {
                //企业
                $where['sr.enterprise_id'] = $self_enterprise_id;
            }
        }
        if($status!=9){
            if($status == 1){
                $where['sr.order_id'] = array('neq','');
            }

            if($status == 2){
                $where['sr.order_id'] = array(array('eq',''),array('exp','is null'), 'or');
            }
        }

        if($mobile){
            $where['sr.mobile'] = $mobile;
        }

        if($activity_id){
            $where['sua.activity_id'] = $activity_id;
        }

        if($product_name){
            $where['sr.product_name']=array('like','%'.$product_name.'%');
        }

        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['sr.receive_date'] = array('between',array(start_time($start_datetime),end_time($end_datetime)));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['sr.receive_date'] = array('between',array(start_time($start_datetime),$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['sr.receive_date'] = array('between',array($start_datetime,end_time($end_datetime)));
            }
        }else{
            $start_datetime =date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
            //$end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $end_datetime= strtotime($start_datetime)-2592000;
            $e_time=start_time(date('Y-m-d',$end_datetime));
            $where['sr.receive_date']= array('between',array($e_time,$start_datetime));
        }
        
        if($use_t==1){
            $proxys=D('Proxy')->proxy_child_ids();
            if($proxys){
                $where1['p.proxy_id']= array('in',$proxys);
            }else{
                $where1['p.proxy_id']=-1;
            }
            $enterprises=D("Enterprise")->enterprise_child_ids();
            if($enterprises){
                $where1['e.enterprise_id']=array('in',$enterprises);
            }else{
                $where1['e.enterprise_id']=-1;
            }
            $where1['_logic']="or";
            $where[]=$where1;
        }
        if($use_t==2) {
            $proxys=D('Proxy')->proxy_child_ids();
            $self_proxy_id=D('SysUser')->self_proxy_id();
            if($proxys){
                $stat['p.proxy_id'] = array('in',$proxys);
                $stat['p.top_proxy_id'] = array('eq',$self_proxy_id);
                $stat['_logic'] = 'and';
                $map['_complex'] = $stat;
                $map['p.proxy_id'] = array('eq',$self_proxy_id);
                $map['_logic'] = 'or';
                $where1[]=$map;
            }else{
                $where1['p.proxy_id']=$self_proxy_id;
            }
            $enterprises=D("Enterprise")->enterprise_ids();//获取该用户可操作的企业号();
            if($enterprises){
                $where1['e.enterprise_id']=array("in",$enterprises);
            }else{
                $where1['e.enterprise_id']=-1;
            }
            $where1['_logic']= "or";
            $where[]=$where1;
        }
        $model=M('scene_record  sr');
        $list =$model
            ->join('left join t_flow_scene_user_activity as sua on sua.user_activity_id=sr.user_activity_id ')
            ->join('left join t_flow_scene_activity as sa on sa.activity_id = sua.activity_id')
            ->join('left join t_flow_proxy as p on p.proxy_id = sr.proxy_id and sr.user_type=1 and p.status=1 and p.approve_status=1')
            ->join('left join t_flow_enterprise as e on e.enterprise_id= sr.enterprise_id and sr.user_type=2 and e.status=1 and e.approve_status=1')
            ->join('left join t_flow_order as o on o.order_code= sr.order_id')
            ->field('sr.record_id,sr.user_type,sr.proxy_id,p.proxy_name,e.enterprise_name,sr.enterprise_id,sr.order_id,sr.user_activity_id,sr.openid,sr.wx_photo,sr.wx_name,sr.mobile,sr.product_name,sr.receive_date,sa.activity_name,o.discount_price,o.order_status')
            ->where($where)
            ->order('sr.record_id desc')
            ->limit(3000)
            ->select();

        $datas = array();
        $headArr=array();
        if($use_t != 3){
            $headArr = array_merge($headArr,array("用户类型","用户名称"));
        }
        $headArr=array_merge($headArr,array("微信昵称","手机号","参与活动","流量包","领取状态","领取时间"));
        foreach ($list as $v) {
            $data=array();if($use_t != 3){
                $data['user_type'] = $v['user_type'] == 1?"代理商":"企业";
                $data['user_name'] = $v['user_type'] == 1?$v['proxy_name']:$v['enterprise_name'];
            }
            $data['wx_name'] = $v['wx_name'];
            $data['mobile'] = $v['mobile'];
            $data['activity_name'] = $v['activity_name'];
            $data['product_name'] = $v['product_name'];
            /*$data['dicount_price']=$v['discount_price'];*/
            $data["data_status"]=$v["order_id"]?"成功":"失败";
            $data['receive_date'] = $v['receive_date'];
            array_push($datas,$data);
        }
            
        $title='领取记录';

        ExportEexcel($title,$headArr,$datas);
    }



}
?>