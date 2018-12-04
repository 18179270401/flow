<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {


  public function index(){
        $this->redirect('Admin/Public/Login');
        
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
        
        //$where['sys_type'] = 1;
        $where['menu_type'] = 2;
        $where['status'] = 1;
        
        $list = M("SysMenu")->where($where)->order("sys_type asc,order_num asc")->select();
        foreach($list as $k=>$v){
            $where2['menu_id'] = $v['menu_id'];
            $where2['status'] = 1;
            $list2 = M("SysFunction")->where($where2)->order('order_num asc')->select();
            $list[$k]['function'] = $list2;
        }
     
        $this->assign("list",$list);
        $this->display();
        
        
    }
    
    function edit(){
        $post = I("post.");
        foreach($post['id'] as $k=>$v){
            $edit['function_name'] = $post['name'][$k];
            $edit['order_num'] = $post['order_num'][$k];
            M("SysFunction")->where(array('function_id'=>$v))->save($edit);
        }
        $this->ajaxReturn(array('msg'=>"已成功请比对",'status'=>"success"));
    }
}