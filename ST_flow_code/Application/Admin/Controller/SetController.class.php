<?php

/*
 * 系统设置控制器
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class SetController extends CommonController {
    /*
     * 系统设置
     */
    public function index(){
        $type = I('get.type');
        $sysset = M("SysSet");
        if($type == "operation"){
            //保存系统设置
            $post = I("post.");
            $status = "error";
            $msg = "系统设置保存失败!";
            //将大写逗号转为小写
            $enterprise_money=$post['enterprise_quota_remind'];
            $proxy_money=$post['proxy_quota_remind'];
            //通道关停提醒人电话
            $post['channel_shut_down_remind'] = str_replace("，", ",", $post['channel_shut_down_remind']);
            //额度、余额不足提醒人
            $post['channel_quota_remind'] = str_replace("，", ",", $post['channel_quota_remind']);
            //备注
            $post['channel_quota_remark'] = str_replace("，", ",", $post['channel_quota_remark']);
            //通道预警提醒人
            $post['early_warning_people'] = str_replace("，", ",", $post['early_warning_people']);
            //通道预警提醒人备注
            //$post['early_warning_people_remark'] = str_replace("，", ",", $post['early_warning_people_remark']);
            //卡单提醒人
            $post['card_warning_people'] = str_replace("，", ",", $post['card_warning_people']);

            if($list = $sysset->order("set_id asc")->find()){
                $result=$sysset->where(array('set_id'=>$list['set_id']))->save($post);
                if($result >=0){
                    $status = "success";
                    $msg = "系统设置保存成功!";
                    $n_msg='成功';
                }else{
                    $msg = "系统设置保存失败!";
                    $n_msg='失败';
                }
                $c_item='';
                $c_item.=$proxy_money*100===$list['proxy_quota_remind']*100?'':'代理商提醒金额【'.$proxy_money.'】元';
                $fg=!empty($c_item)?'，':'';
                $c_item.=$enterprise_money*100===$list['enterprise_quota_remind']*100?'':$fg.'企业提醒金额【'.$enterprise_money.'】元';
                $fg=!empty($c_item)?'，':'';
                $c_item.= $post['channel_quota_remind']===$list['channel_quota_remind']?'':$fg.'额度、余额不足提醒人【'. $post['channel_quota_remind'].'】';
                $note_info='ID【'.$list['set_id'].'】，系统设置：'.$c_item;
            }else{
                if($id=$sysset->add($post)){
                    $status = "success";
                    $msg = "系统设置保存成功!";
                    $n_msg='成功';
                }else{
                    $msg = "系统设置保存失败!";
                    $n_msg='失败';
                }
                $note_info='ID【'.$id.'】，系统设置，代理商提醒金额【'.$proxy_money.'】元，企业提醒金额【'.$enterprise_money.'】元，额度、余额不足提醒人【'.$post['channel_quota_remind'].'】';

            }
            $note = "用户【".get_user_name(D('SysUser')->self_id())."】，".$note_info.$n_msg;
            $this->sys_log('系统设置',$note);
            if(IS_AJAX) $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
        }else{
            //读取系统设置
            $list = M("SysSet")->order("set_id asc")->find();
            $this->assign("list",$list);
            $this->display();
        }
    }

}