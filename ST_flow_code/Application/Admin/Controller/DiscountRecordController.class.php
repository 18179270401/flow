<?php
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
/*
 * 折扣记录类
 */
class DiscountRecordController extends CommonController{
    public function index(){
        D("SysUser")->sessionwriteclose();
        $user_code=trim(I("user_code"));
        $user_name=trim(I("user_name"));
        $top_proxy_code=trim(I("top_proxy_code"));
        $top_proxy_name=trim(I("top_proxy_name"));
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        $operator_id = trim(I('get.operator_id'));  //获取运营商
        if(!empty($operator_id)) {
            $where['dr.operator_id'] = array('eq', $operator_id);
        }
        if($user_code){
            $where_code['e.enterprise_code']=array("like","%".$user_code."%");
            $where_code['p.proxy_code']=array("like","%".$user_code."%");
            $where_code['_logic']="or";
            $where[]=$where_code;
        }
        if($user_name){
            $where_name['e.enterprise_name']=array("like","%".$user_name."%");
            $where_name['p.proxy_name']=array("like","%".$user_name."%");
            $where_name['_logic']="or";
            $where[]=$where_name;
        }
        if($top_proxy_code){
            $where['tp.proxy_code']=array("like","%".$top_proxy_code."%");
        }
        if($top_proxy_name){
            $where['tp.proxy_name']=array("like","%".$top_proxy_name."%");
        }
        $use_t=D("SysUser")->self_user_type();
       //$where['dr.top_proxy_id']=D('SysUser')->self_proxy_id();
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
                /*$map['_complex'] = $stat;
                $map['p.proxy_id'] = array('eq',$self_proxy_id);
                $map['_logic'] = 'or';*/
                $where1[]=$stat;
            }/*else{
                $where1['p.proxy_id']=$self_proxy_id;
            }*/
            $enterprises=D("Enterprise")->enterprise_ids();//获取该用户可操作的企业号();
            if($enterprises){
                $where1['e.enterprise_id']=array("in",$enterprises);
            }else{
                $where1['e.enterprise_id']=-1;
            }
            $where1['_logic']= "or";
            $where[]=$where1;
        }

        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['dr.create_date']=array('between',array($start_datetime,$end_datetime));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['dr.create_date']= array('between',array($start_datetime,$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['dr.create_date'] =array('between',array($start_datetime,$end_datetime));
            }
        }else{
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $where['dr.create_date'] = array('between',array($start_datetime,$end_datetime));
        }
        //var_dump($where);die();
        $count= M('discount_record as dr')
            ->join("left join ".C('DB_PREFIX')."enterprise e on e.enterprise_id = dr.enterprise_id and dr.user_type=2")
            ->join("left join ".C('DB_PREFIX')."proxy p on p.proxy_id = dr.proxy_id and dr.user_type=1")
            //->join("left join ".C('DB_PREFIX')."proxy tp on tp.proxy_id = dr.top_proxy_id")
            ->where($where)
            ->count();
        $Page       = new Page($count, 20);
        $show       = $Page->show();
        $list= M('discount_record as dr')
            ->join("left join ".C('DB_PREFIX')."enterprise e on e.enterprise_id = dr.enterprise_id and dr.user_type=2")
            ->join("left join ".C('DB_PREFIX')."proxy p on p.proxy_id = dr.proxy_id and dr.user_type=1")
            //->join("left join ".C('DB_PREFIX')."proxy tp on tp.proxy_id = dr.top_proxy_id")
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order("dr.create_date desc")
            ->field("dr.*,e.enterprise_name,e.enterprise_code,p.proxy_name,p.proxy_code")
            ->select();
        //var_dump($list);
        $operator = D("ChannelProduct")->operatorall();//读取运营商
        $this->assign("operator",$operator);
        $this->assign('list', get_sort_no($list, $Page->firstRow));  //数据列表
        $this->assign('page',$show);
        $this->assign('d_sdata',date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))));  //默认开始时间
        $this->assign('d_edata',date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))));  //默认结束时间
        $this->display();
    }

    //导出
    public function export_excel(){
        $user_code=trim(I("user_code"));
        $user_name=trim(I("user_name"));
        $top_proxy_code=trim(I("top_proxy_code"));
        $top_proxy_name=trim(I("top_proxy_name"));
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        $operator_id = trim(I('get.operator_id'));  //获取运营商
        if(!empty($operator_id)) {
            $where['dr.operator_id'] = array('eq', $operator_id);
        }
        if($user_code){
            $where_code['e.enterprise_code']=array("like","%".$user_code."%");
            $where_code['p.proxy_code']=array("like","%".$user_code."%");
            $where_code['_logic']="or";
            $where[]=$where_code;
        }
        if($user_name){
            $where_name['e.enterprise_name']=array("like","%".$user_name."%");
            $where_name['p.proxy_name']=array("like","%".$user_name."%");
            $where_name['_logic']="or";
            $where[]=$where_name;
        }
        if($top_proxy_code){
            $where['tp.proxy_code']=array("like","%".$top_proxy_code."%");
        }
        if($top_proxy_name){
            $where['tp.proxy_name']=array("like","%".$top_proxy_name."%");
        }

        $use_t=D("SysUser")->self_user_type();
        //$where['dr.top_proxy_id']=D('SysUser')->self_proxy_id();
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
                /*$map['_complex'] = $stat;
                $map['p.proxy_id'] = array('eq',$self_proxy_id);
                $map['_logic'] = 'or';*/
                $where1[]=$stat;
            }/*else{
                $where1['p.proxy_id']=$self_proxy_id;
            }*/
            $enterprises=D("Enterprise")->enterprise_ids();//获取该用户可操作的企业号();
            if($enterprises){
                $where1['e.enterprise_id']=array("in",$enterprises);
            }else{
                $where1['e.enterprise_id']=-1;
            }
            $where1['_logic']= "or";
            $where[]=$where1;
        }

        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['dr.create_date']=array('between',array($start_datetime,$end_datetime));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['dr.create_date']= array('between',array($start_datetime,$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['dr.create_date'] =array('between',array($start_datetime,$end_datetime));
            }
        }else{
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $where['dr.create_date'] = array('between',array($start_datetime,$end_datetime));
        }
        $list= M('discount_record as dr')
            ->join("left join ".C('DB_PREFIX')."enterprise e on e.enterprise_id = dr.enterprise_id and dr.user_type=2")
            ->join("left join ".C('DB_PREFIX')."proxy p on p.proxy_id = dr.proxy_id and dr.user_type=1")
            //->join("left join ".C('DB_PREFIX')."proxy tp on tp.proxy_id = dr.top_proxy_id")
            ->where($where)
            ->limit(3000)
            ->order("dr.create_date desc")
            ->field("dr.*,e.enterprise_name,e.enterprise_code,p.proxy_name,p.proxy_code")
            ->select();
        $data=array();
        foreach($list as $v){
            $cash=array();
            if($v['user_type']==1){
                $cash['user_code']=$v['proxy_code'];
                $cash['user_name']=$v['proxy_name'];
                $cash['user_type']="代理商";
            }else{
                $cash['user_code']=$v['enterprise_code'];
                $cash['user_name']=$v['enterprise_name'];
                $cash['user_type']="企业";
            }
            $cash['operator_name']=get_operator_name($v['operator_id']);
            $cash['province_name']=get_city_province_name($v['city_id'],$v['province_id']).get_city_name($v['city_id']);
            $cash['discount_before']=show_discount_ten($v['discount_before'])."折";
            $cash['discount_after']=show_discount_ten($v['discount_after'])."折";
            $cash['create_user_id']=get_user_name($v['create_user_id'],"proxy");
            $cash['create_date']=$v['create_date'];
            array_push($data,$cash);
        }
        $headArr=array("用户编号","用户名称","用户类型","运营商","地区","操作前折扣数","操作后折扣数","操作人","操作时间");
        $name="用户折扣变动记录";
        ExportEexcel($name,$headArr,$data);
    }

    /*企业端用户折扣记录*/
    public function index_record(){
        D("SysUser")->sessionwriteclose();
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        $operator_id = trim(I('get.operator_id'));  //获取运营商

        $enterprise_id = D("SysUser")->self_enterprise_id();
        $user_type = 2;
        $where = array(
            'dr.enterprise_id' => $enterprise_id,
            'dr.user_type' => $user_type
        );


        if(!empty($operator_id)) {
            $where['dr.operator_id'] = array('eq', $operator_id);
        }

        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['dr.create_date']=array('between',array($start_datetime,$end_datetime));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['dr.create_date']= array('between',array($start_datetime,$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['dr.create_date'] =array('between',array($start_datetime,$end_datetime));
            }
        }else{
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $where['dr.create_date'] = array('between',array($start_datetime,$end_datetime));
        }
        //var_dump($where);die();
        $count= M('discount_record as dr')
            ->join("left join ".C('DB_PREFIX')."enterprise e on e.enterprise_id = dr.enterprise_id and dr.user_type=2")
            ->where($where)
            ->count();
        $Page       = new Page($count, 20);
        $show       = $Page->show();
        $list= M('discount_record as dr')
            ->join("left join ".C('DB_PREFIX')."enterprise e on e.enterprise_id = dr.enterprise_id and dr.user_type=2")
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order("dr.create_date desc")
            ->field("dr.*,e.enterprise_name,e.enterprise_code")
            ->select();
        //var_dump($list);
        $operator = D("ChannelProduct")->operatorall();//读取运营商
        $this->assign("operator",$operator);
        $this->assign('list', get_sort_no($list, $Page->firstRow));  //数据列表
        $this->assign('page',$show);
        $this->assign('d_sdata',date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y'))));  //默认开始时间
        $this->assign('d_edata',date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y'))));  //默认结束时间
        $this->display();
    }

    //导出
    public function export_excel_record(){
        D("SysUser")->sessionwriteclose();
        $start_datetime = trim(I('get.start_datetime'));   //开始时间
        $end_datetime = trim(I('get.end_datetime'));   //结束时间
        $operator_id = trim(I('get.operator_id'));  //获取运营商

        $enterprise_id = D("SysUser")->self_enterprise_id();
        $user_type = 2;
        $where = array(
            'dr.enterprise_id' => $enterprise_id,
            'dr.user_type' => $user_type
        );


        if(!empty($operator_id)) {
            $where['dr.operator_id'] = array('eq', $operator_id);
        }

        if($start_datetime or $end_datetime){
            if($start_datetime && $end_datetime){
                $where['dr.create_date']=array('between',array($start_datetime,$end_datetime));
            }elseif($start_datetime){
                $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('d'),date('Y')));
                $where['dr.create_date']= array('between',array($start_datetime,$end_datetime));
            }elseif($end_datetime){
                $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d'),date('Y')));
                $where['dr.create_date'] =array('between',array($start_datetime,$end_datetime));
            }
        }else{
            $start_datetime = date("Y-m-d H:i:s",mktime(0,0,0,date('m'),1,date('Y')));
            $end_datetime = date("Y-m-d H:i:s",mktime(23,59,59,date('m'),date('t'),date('Y')));
            $where['dr.create_date'] = array('between',array($start_datetime,$end_datetime));
        }

        $list= M('discount_record as dr')
            ->join("left join ".C('DB_PREFIX')."enterprise e on e.enterprise_id = dr.enterprise_id and dr.user_type=2")
            ->where($where)
            ->limit(3000)
            ->order("dr.create_date desc")
            ->field("dr.*,e.enterprise_name,e.enterprise_code")
            ->select();
        $data=array();
        foreach($list as $v){
            $cash=array();
            $cash['user_code']=$v['enterprise_code'];
            $cash['user_name']=$v['enterprise_name'];
            $cash['operator_name']=get_operator_name($v['operator_id']);
            $cash['province_name']=get_city_province_name($v['city_id'],$v['province_id']).get_city_name($v['city_id']);
            $cash['discount_before']=show_discount_ten($v['discount_before'])."折";
            $cash['discount_after']=show_discount_ten($v['discount_after'])."折";
            $cash['create_user_id']=get_user_name($v['create_user_id'],"proxy");
            $cash['create_date']=$v['create_date'];
            array_push($data,$cash);
        }
        $headArr=array("用户编号","用户名称","运营商","地区","操作前折扣数","操作后折扣数","操作人","操作时间");
        $name="用户折扣变动记录";
        ExportEexcel($name,$headArr,$data);
    }
}
?>