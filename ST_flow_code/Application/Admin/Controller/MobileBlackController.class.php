<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class MobileBlackController extends CommonController{
    /*号码黑名单*/
    public function index(){
        D("SysUser")->sessionwriteclose();
        $mobile = trim(I('get.mobile'));    //联系电话
        $where = array();
        if($mobile){
            $where['mb.mobile'] = array('like','%'.$mobile.'%');
        }

        $Model = M('Mobile_blacklist as mb');
        $count      = $Model->where($where)->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        //分页显示所有的数据

        $mb_list=$Model
            ->join("LEFT JOIN ".C('DB_PREFIX').'sys_user as u on u.user_id = mb.create_user_id')
            ->where($where)
            ->order("mb.modify_date desc,mb.mobile_id desc")
            ->field('mb.mobile_id,mb.mobile,mb.remark,mb.modify_date,u.user_name')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

        $this->assign('list',get_sort_no($mb_list,$Page->firstRow));// 赋值数据集  //get_sort_no用序列号
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    /*
       增加号码黑名单，显示页面
    */
    public function add(){
        $this->display();
    }

    /*
      处理增加号码黑名单的逻辑
    */
    public function insert(){
        $msg = '系统错误!';
        $status = 'error';
        if(IS_POST && IS_AJAX){
            $post = I("post."); 
            if($post['mobile']==""){
                $msg = '请输入手机号码!';
            }elseif(!isMobile2($post['mobile'])){
                $msg = '手机号码格式错误!';
            }else{
                $model = M('Mobile_blacklist');

                //是否已经存在该号码
                $where = array('mobile' => $post['mobile']);
                $info = $model->where($where)->find();
                if(!empty($info)){
                    $this->ajaxReturn(array('msg'=>'对不起，号码黑名单中已经存在该号码！','status'=>'error'));
                }

                $add=array(
                    'mobile'=>$post['mobile'],
                    'remark'=>  $post['remark'],
                    'create_user_id'=>  D('SysUser')->self_id(),
                    'create_date'   =>  date('Y-m-d H:i:s',time()),
                    'modify_user_id'=>  D('SysUser')->self_id(),
                    'modify_date'   =>  date('Y-m-d H:i:s',time())
                );

                $id = $model->add($add);
                if($id){
                    $status = 'success';
                    $msg = '新增号码黑名单成功！';
                    $n_msg='成功';
                }else{
                    $msg = '新增号码黑名单失败！';
                    $n_msg='失败';
                }
                $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$id.'】，新增号码【'.$post['mobile'].'】黑名单'.$n_msg;
                $this->sys_log('新增号码黑名单',$note);
            }         
        }
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));

    }

    /*
        显示当前号码黑名单的信息
    */
    public function show(){
        $where['mb.mobile_id'] =trim(I('get.mobile_id'));
        $info = M('Mobile_blacklist as mb')
              ->field('mb.mobile_id,mb.mobile,mb.remark,mb.create_user_id,mb.create_date,mb.modify_user_id,mb.modify_date')
              ->where($where)
              ->find();
        $this->assign('info',$info);
        $this->display();
    }
    
    /**
        删除号码黑名单
    **/
    public function delete(){
        $msg = '系统错误';
        $status = 'error';
        $mobile_id=I('mobile_id');
        $where = array('mobile_id' => $mobile_id);
        $info = M('Mobile_blacklist')->where($where)->find();
        if(empty($info)){
            $this->ajaxReturn(array('msg'=>'对不起没有找到相关信息，请重试！','status'=>'error'));
        }
        $res=M('Mobile_blacklist')->where($where)->delete();
        if($res){
            $msg='删除号码黑名单成功！';
            $status = 'success';
            $n_msg='成功';
        }else{
            $msg='删除号码黑名单失败！';
            $n_msg='失败';
        }
        $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$mobile_id.'】，删除号码【'.$info['mobile'].'】黑名单'.$n_msg;
        $this->sys_log('删除号码黑名单',$note);
        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }

    /**
     * Excel导入新增
     */
    public function excel_add(){
        //上传excel列出手机号
        if ($_FILES["file"] == null) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '文件上传失败'));
        }
        $filetype = array(
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        );
        if (!in_array($_FILES["file"]['type'],$filetype)) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '请上传 Excel 文档！'));
        }
        $list = readExcel($_FILES["file"]["tmp_name"]);
        $new_mobile = array();
        foreach($list as $key => $value){
            //过滤手机号码
            if(isMobile2($value[0])){
                $new_mobile[] = $value[0];
            }
        }

        //过滤掉已经存在的手机号码
        $all_mobile = M('Mobile_blacklist')->field('mobile')->select();
        $new_all_mobile = get_array_column($all_mobile,'mobile');
        if(empty($new_all_mobile)){
            $new_all_mobile = $new_mobile;
        }else{
            $new_all_mobile = array_diff($new_mobile,$new_all_mobile);
        }
        $add_array = array();
        foreach($new_all_mobile as $v){
            $add = array(
                'mobile'    =>  $v,
                'remark'    =>  'Excel导入',
                'create_user_id'=>  D('SysUser')->self_id(),
                'create_date'   =>  date('Y-m-d H:i:s',time()),
                'modify_user_id'=>  D('SysUser')->self_id(),
                'modify_date'   =>  date('Y-m-d H:i:s',time())
            );
            $add_array[] = $add;
        }

        $lastid = M('Mobile_blacklist')->addAll($add_array);

        if(!empty($lastid)){
            $this->ajaxReturn(array('status' => 'success', 'msg' => '上传成功'));
        }else{
            $msg = '上传失败!';
            if(empty($add_array)){
                $msg .= '没有合适的手机号!';
            }
            $this->ajaxReturn(array('status' => 'error', 'msg' => $msg));
        }
    }

    /**
     * Excel导入删除
     */
    public function excel_delete(){
        //上传excel列出手机号
        if ($_FILES["file"] == null) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '文件上传失败'));
        }
        $filetype = array(
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        );
        if (!in_array($_FILES["file"]['type'],$filetype)) {
            $this->ajaxReturn(array('status' => 'error', 'msg' => '请上传 Excel 文档！'));
        }
        $list = readExcel($_FILES["file"]["tmp_name"]);
        $new_mobile = array();
        foreach($list as $key => $value){
            //过滤手机号码
            if(isMobile2($value[0])){
                $new_mobile[] = $value[0];
            }
        }

        //获取都存在的手机号码
        $all_mobile = M('Mobile_blacklist')->field('mobile')->select();
        $new_all_mobile = get_array_column($all_mobile,'mobile');
        $new_all_mobile = array_intersect($new_mobile,$new_all_mobile);

        $where = array('mobile' => array('in',implode(',',$new_all_mobile)));
        $res = M('Mobile_blacklist')->where($where)->delete();

        if($res){
            $this->ajaxReturn(array('status' => 'success', 'msg' => '删除成功'));
        }else{
            $msg = '删除失败!';
            if(empty($new_all_mobile)){
                $msg .= '没有合适的手机号!';
            }
            $this->ajaxReturn(array('status' => 'error', 'msg' => $msg));
        }
    }


    /**
    *导出EXCEL
    **/
    public function export_excel(){
        $mobile = trim(I('get.mobile'));    //联系电话
        $where = array();

        if($mobile){
            $where['mb.mobile'] = array('like','%'.$mobile.'%');
        }

        $Model = M('Mobile_blacklist as mb');

        $mb_list=$Model
            ->where($where)
            ->order("mb.modify_date desc,mb.mobile_id desc")
            ->field('mb.mobile_id,mb.mobile,mb.remark,mb.modify_date,mb.create_user_id')
            ->limit(3000)
            ->select();

        $datas = array();
        $headArr=array("手机号码","备注","操作人","操作时间");

        foreach ($mb_list as $v) {
            $data=array();
            $data['mobile'] = ' ' . $v['mobile'];
            $data['remark'] = $v['remark'];
            $data['create_user_id'] = get_user_name($v['create_user_id']);
            $data['modify_date'] = $v['modify_date'];
            array_push($datas,$data);
        }
            
        $title='号码黑名单';

        ExportEexcel($title,$headArr,$datas);
    }


    
    
}
?>