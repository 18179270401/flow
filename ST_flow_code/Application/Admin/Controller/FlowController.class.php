<?php
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class FlowController extends CommonController {

    public function index(){
        D("SysUser")->sessionwriteclose();
        $product_name = I('product_name');
        $operator_id = I('operator_id');
        $data_status = I('status');
        if(!empty($product_name))$map['p.product_name'] = array("like","%{$product_name}%");
        if(!empty($operator_id))$map['p.operator_id'] = $operator_id;
        //列表出状态和全部
        if($data_status == 9){
            $map['p.status'] = array('neq',2);
        }else{
            $map['p.status'] = $data_status=== '0' ? $data_status : 1;
        }
        //联表关系
        $join = array(
            C('DB_PREFIX').'channel as c ON p.channel_id=c.channel_id',
            C('DB_PREFIX').'sys_operator as o ON p.operator_id=o.operator_id',
            C('DB_PREFIX').'sys_province as ps ON p.province_id=ps.province_id',
            C('DB_PREFIX').'channel as ca ON p.back_channel_id=ca.channel_id',
            //C('DB_PREFIX').'sys_city as sc ON p.city_id=sc.city_id'
            );
        //调用分页类
        $count      = M('Product as p')->where($map)->join($join,"left")->count();
        $Page       = new Page($count,20);
        $show       = $Page->show();
        //获取所有角色列表
        $product_list = M('Product as p')
            ->where($map)
            ->join($join,"left")
            ->field("p.*,c.channel_code,c.channel_name,ca.channel_code as channel_code2,ca.channel_name as channel_name2,o.operator_name,ps.province_name")
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('operator_id ASC,base_price ASC,p.modify_date DESC')
            ->select();
        //加载模板
        $this->assign('product_list',get_sort_no($product_list,$Page->firstRow));  //数据列表
        $this->assign('page',$show);            //分页
        
        //读取运营商
        $operator = D("ChannelProduct")->operatorall();
        $this->assign('operator',$operator);
    	$this->display();
        
    }


    /**
     * 导出excel
     */
    public function export_excel() {
        $product_name = I('product_name');
        $operator_id = I('operator_id');
        $data_status = I('status');
        if(!empty($product_name))$map['p.product_name'] = array("like","%{$product_name}%");
        if(!empty($operator_id))$map['p.operator_id'] = $operator_id;
        //列表出状态和全部
        if($data_status == 9){
            $map['p.status'] = array('neq',2);
        }else{
            $map['p.status'] = $data_status=== '0' ? $data_status : 1;
        }
        //联表关系
        $join = array(
            C('DB_PREFIX').'channel as c ON p.channel_id=c.channel_id',
            C('DB_PREFIX').'sys_operator as o ON p.operator_id=o.operator_id',
            C('DB_PREFIX').'sys_province as ps ON p.province_id=ps.province_id',
            C('DB_PREFIX').'channel as ca ON p.back_channel_id=ca.channel_id'
        );
        //获取所有角色列表
        $list = M('Product as p')
            ->where($map)
            ->join($join,"left")
            //->field('o.operator_name,ps.province_name,p.product_name,p.base_price,c.channel_name,ca.channel_name as channel_name2')
            ->field('o.operator_name,ps.province_name,p.product_name,p.base_price,p.size')
            ->limit(3000)
            ->order('o.operator_id ASC,base_price ASC,p.modify_date DESC')
            ->select();
        $title='流量包管理';
        //$headArr=array("运营商","省份","流量包名称","基础售价（元）","主通道","备用通道");
        $headArr=array("运营商","省份","流量包名称","标准价格(元)","流量包大小(M)");
        ExportEexcel($title,$headArr,$list);

    }

    public function add(){
        //读取运营商
        $operator = D("ChannelProduct")->operatorall();
        //读取省份
        $province = D("ChannelProduct")->provinceall();
        //$list = D("Product")->distinct(true)->field("product_name")->order("size")->select();
        //$this->assign('list',$list);
        $this->assign('operator',$operator);
        $this->assign('province',$province);
        $this->display();
    }
    
    public function insert(){
        $msg = '系统错误！';
        $status = 'error';
        if(IS_POST){
            $post = I('post.');
            if(!empty($post['operator_id'])){
                if(!empty($post['province_id'])){
                    if(!empty($post['product_name'])){
                        if(!empty($post['base_price'])){
                            if(!empty($post['size'])){
                                //if(!empty($post['channel_id'])){
                                $map['operator_id'] = $post['operator_id'];
                                $map['province_id'] = $post['province_id'];
                                $map['product_name'] = $post['product_name'];
                                $product = D('Product')->where($map)->find();
                                if(!$product){
                                    $post['create_user_id'] = D('SysUser')->self_id();
                                    $post['create_date'] = date("Y-m-d H:i:s",time());
                                    $post['modify_user_id'] = D('SysUser')->self_id();
                                    $post['modify_date'] =date("Y-m-d H:i:s",time());
                                    $add_id = M('Product')->add($post);
                                    if($add_id){
                                        $msg = '新增流量包成功！';
                                        $status = 'success';
                                        $n_msg='成功';
                                    }else{
                                        $msg = '新增流量包失败！';
                                        $n_msg='失败';
                                    }
                                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$add_id.'】，新增运营商【'.get_operator_name($post['operator_id']).'】，省份【'.get_province_name($post['province_id']).'】，流量包【'.$post['product_name'].'】'.$n_msg;
                                    $this->sys_log('新增流量包',$note);
                                }else{
                                    $msg = '流量包重复,请仔细检查！';
                                }
                                //}else{
                                //$msg = '请选择主通道！';
                                //}
                            }

                        }else{
                            $msg = '请输入产品售价！';
                        }
                    }else{
                        $msg = '请选择产品名称！';
                    }
                }else{
                    $msg = '请选择所属省份！';
                }
            }else{
                $msg = '请选择所属运营商！';
            }
        }
        if(IS_AJAX) $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }
    
    public function edit(){
        $product_id = I("product_id");
        $where['product_id'] = $product_id;
        $info = M("Product")->where($where)->find();
        if($info){
            //读取运营商
            $operator = D("ChannelProduct")->operatorall();
            //读取省份
            $province = D("ChannelProduct")->provinceall();
            //读取产品
            $where1['operator_id'] = $info['operator_id'];
            $where1['province_id'] = $info['province_id'];
            //$list = D("Product")->distinct(true)->where($where1)->field("product_name")->order("size")->select();
            $list = D("ChannelProduct")->distinct(true)->where($where1)->field("product_name")->select();
            //读取产品下的通道
            $where2['cp.operator_id'] = $info['operator_id'];
            $where2['cp.province_id'] = $info['province_id'];
            $where2['cp.product_name'] = $info['product_name'];
            $list2 = M("ChannelProduct as cp")->where($where2)->join(C('DB_PREFIX').'channel as c ON c.channel_id=cp.channel_id',"left")->field("cp.channel_id,c.channel_name,c.channel_code")->select();
            
            $this->assign('info',$info);
            $this->assign('operator',$operator);
            $this->assign('province',$province);
            $this->assign('list',$list);
            $this->assign('list2',$list2);
            $this->display();
        }else{
            $this->error('产品不存在！');
        }
    }

    public function show(){
        $where['p.product_id']=trim(I('product_id'));
        $info = D("ChannelProduct")->flowShow($where);
        $this->assign($info);
        $this->display();
    }
    
    public function update(){
        $msg = '系统错误！';
        $status = 'error';
        if(IS_POST){
            $post = I('post.');
            if(!empty($post['operator_id'])){
                if(!empty($post['province_id'])){
                    if(!empty($post['product_name'])){
                        if(!empty($post['base_price'])){
                            //if(!empty($post['channel_id'])){
                            if(!empty($post['size'])){
                                $map['operator_id'] = $post['operator_id'];
                                $map['province_id'] = $post['province_id'];
                                $map['product_name'] = $post['product_name'];
                                $map['product_id'] = array("neq",$post['product_id']);
                                $product = D('Product')->where($map)->find();
                                if(!$product){
                                    $post['modify_user_id'] = D('SysUser')->self_id();
                                    $post['modify_date'] =date("Y-m-d H:i:s");
                                    if(M('Product')->save($post)){
                                        $msg = '编辑流量包成功！';
                                        $status = 'success';
                                        $n_msg='成功';
                                    }else{
                                        $msg = '编辑流量包失败！';
                                        $n_msg='失败';
                                    }
                                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，编辑流量包【'.$post['product_name'].'】'.$n_msg;
                                    $this->sys_log('编辑流量包',$note);
                                }else{
                                    $msg = '流量包重复,请仔细检查！';
                                }
                                //}else{
                                //$msg = '请选择主通道！';
                                //}
                            }

                        }else{
                            $msg = '请输入产品售价！';
                        }
                    }else{
                        $msg = '请选择产品名称！';
                    }
                }else{
                    $msg = '请选择所属省份！';
                }
            }else{
                $msg = '请选择所属运营商！';
            }
        }
        if(IS_AJAX) $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }
    
    /**
     * 修改通道产品状态
     */
    public function toggle_status(){
        $msg = '系统错误！';
        $status = 'error';
        if(IS_POST && IS_AJAX){
            $product_id = I('post.product_id',0,'int');
            if(!empty($product_id)){
                $where['product_id'] = $product_id;
                $product = M('Product')->where($where)->find();
                if($product){
                    $status = $product['status'] == 1 ? "0" : "1";
                    $edit = array(
                        'product_id'=>$product_id,
                        'status'=> $status,
                    );
                    $edit = M('Product')->save($edit);
                    $status_name = $status == 1 ? "启用" : "禁用";
                    if($edit){
                        $status = 'success';
                        $msg = "流量包".$status_name.'成功！';
                        $n_msg='成功';
                    }else{
                        $msg = "流量包".$status_name.'失败！';
                        $n_msg='失败';
                    }
                    $note='用户【'.get_user_name(D('SysUser')->self_id()).'】，ID【'.$product_id.'】，'.$status_name.'运营商【'.get_operator_name($product['operator_id']).'】，省份【'.get_province_name($product['province_id']).'】，流量包【'.$product['product_name'].'】'.$n_msg;
                    $this->sys_log($status_name.'流量包',$note);
                }else{
                    $msg = '数据读取失败！';
                }
            }else{
                $msg = '传入ID错误！';
            }
        }
        if(IS_AJAX){
            $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }
    }
    
    //通过选择运营商和地区列出产品、通过产品列出通道
    public function read_operation(){
        $post = I('post.');
        if($post['type']=="channel_product_name"){
            if(!empty($post['operator_id']) && !empty($post['province_id'])){
                $where['operator_id'] = $post['operator_id'];
                $where['province_id'] = $post['province_id'];
                $list = D("ChannelProduct")->distinct(true)->where($where)->field("product_name")->select();
                $status = 'success';
            }else{
                $list = '';
                $status = 'error';
            }
        }elseif($post['type']=="channel"){
            if(!empty($post['operator_id']) && !empty($post['province_id']) && !empty($post['product_name'])){
                $where['cp.operator_id'] = $post['operator_id'];
                $where['cp.province_id'] = $post['province_id'];
                $where['cp.product_name'] = $post['product_name'];
                $list = M("ChannelProduct as cp")->where($where)->join(C('DB_PREFIX').'channel as c ON c.channel_id=cp.channel_id',"left")->field("cp.channel_id,channel_name,c.channel_code")->select();
                $status = 'success';
            }else{
                $list = '';
                $status = 'error';
            }
        }
        if(IS_AJAX) $this->ajaxReturn(array('list'=>$list,'status'=>$status));
    }

}