<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;

class PaySourcesRecordController extends CommonController{
	/*
	 *领取流量记录
	 */
	public function  index(){
        $sources_name=trim(I("sources_name"));
        $user_type = D('SysUser')->self_user_type()-1;
        //模板
        if(!empty($sources_name))$where['sources_name']=array("like","%".$sources_name."%");
        if($user_type==1){
            $where['proxy_id'] = D('SysUser')->self_proxy_id();
        }else{
            $where['enterprise_id'] = D('SysUser')->self_enterprise_id();
        }
        $count=M("pay_sources_record")->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        $list=M("pay_sources_record")
            ->where($where)
            ->order('create_date desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        $this->assign('list',get_sort_no($list,$Page->firstRow));
        $this->assign('page',$show);            //分页
        $this->display();
    }
    public function show(){
        $id = trim(I('get.sources_id'));
        $info = M("pay_sources_record")->where(array("sources_id"=>$id))->find();
        $this->assign('info',$info);
        $this->display();
     }

    public function delete(){
        $msg="系统错误！";
        $status="error";
        $id=trim(I("sources_id"));
        if(empty($id)){
            $msg="参数错误！";
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }
        $info=M("pay_sources_record")->where(array("sources_id"=>$id))->find();
        if($info){
            $rt=M("pay_sources_record")->where(array("sources_id"=>$id))->delete();
            if($rt){
                $msg='删除成功！';
                $status="success";
                $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
            }else{
                $msg='删除失败！';
                $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
            }
        }else{
            $msg="信息错误！";
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }
    }
    //导出
    public function  export_excel(){
        $sources_name=trim(I("sources_name"));
        $user_type = D('SysUser')->self_user_type()-1;
        //模板
        if(!empty($sources_name))$where['sources_name']=array("like","%".$sources_name."%");
        if($user_type==1){
            $where['proxy_id'] = D('SysUser')->self_proxy_id();
        }else{
            $where['enterprise_id'] = D('SysUser')->self_enterprise_id();
        }
        $list=M("pay_sources_record")
            ->where($where)
            ->order('create_date desc')
            ->limit(3000)
            ->select();
        $datas = array();
        $headArr=array('渠道名称','充值链接','创建时间');
        foreach ($list as $v) {
            $data=array();
            $data['sources_name']=$v['sources_name'];
            $data['sources_url']=$v['sources_url'];
            $data['create_date']=$v['create_date'];
            array_push($datas,$data);
        }
        $title='充值来源管理';
        ExportEexcel($title,$headArr,$datas);
    }
}
?>