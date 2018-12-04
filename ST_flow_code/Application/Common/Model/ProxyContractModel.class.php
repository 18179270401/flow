<?php

namespace Common\Model;
use Think\Model;

class ProxyContractModel extends Model{
    //显示列表数据
    public function proxy_contract($where){
        $list = array();
        $model=M('ProxyContract as pc');
        $count = $model
            ->join('left join t_flow_proxy as p on p.proxy_id = pc.proxy_id')
            ->where($where)
            ->count();
        $Page = new \Think\Page($count,20);
        $show = $Page->show();
        
        $list =$model
            ->join('left join t_flow_proxy as p on p.proxy_id = pc.proxy_id')
            ->where($where)
            ->order('pc.modify_date desc,pc.approve_status asc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->field('pc.*')
            ->select();
        return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show);
    }

    public function export_excel($where){
        $list = array();
        $model=M('Proxy as p');
      /*  $ids=D("Proxy")->proxy_child_ids();//获取该用户可操作的企业号
        $ids=$ids.",".D("SysUser")->self_proxy_id();
        $where['p.proxy_id']=array("in",$ids);*/
        $list =$model
            ->join('left join t_flow_proxy as up on up.proxy_id = p.top_proxy_id  and up.status = 1')
            ->join('left join t_flow_proxy_account as pa on pa.proxy_id = p.proxy_id and p.status = 1')
            ->where($where)
            ->order('p.proxy_id asc')
            ->limit(3000)
            ->field('p.proxy_code as proxy_code,p.proxy_name as proxy_name,up.proxy_code  as top_proxy_code,up.proxy_name as top_proxy_name,pa.account_balance,pa.freeze_money')
            ->select();
        return $list;
    }

    /*该代理商下的所有二级代理商*/
    public function allProxy($where){
        $model=M('proxy p');
        $list=$model
            ->join('left join t_flow_proxy as up on p.top_proxy_id = up.proxy_id and up.status = 1')
            ->field('p.proxy_id,p.proxy_name,p.proxy_code')
            ->where($where)->select();
        return $list;
    }

    /*代理商信息*/
    public function  proxy($where){
        $model=M('proxy_account  ap');
        $info=$model
            ->join('left join t_flow_proxy as p on p.proxy_id = ap.proxy_id and p.status = 1')
            ->where($where)
            ->field('ap.*,p.proxy_id,p.proxy_name')
            ->find();
        return $info;
    }

    /*
 * 获取节点详细信息
 */
    public function proxyRechargeList($where){
        $model=M('proxy_recharge_apply ap');
        $join = array(
            "t_flow_proxy as p on p.proxy_id = ap.proxy_id",
            "t_flow_proxy as up on up.proxy_id = ap.top_proxy_id"
            );
        $count=$model
            ->join($join)
            ->where($where)
            ->count();
        $Page       = new \Think\Page($count,20);
        $show   	= $Page->show();
        $list =$model
            ->join($join)
            ->field('ap.*,p.proxy_name,p.proxy_code,up.proxy_code as proxy_code2,up.proxy_name as proxy_name2')
            ->where($where)
            ->order('ap.approve_status asc,ap.create_date desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show);
    }

    public function proxy_export_excel($where){
        $model=M('proxy_recharge_apply ap');
        $join = array(
            "t_flow_proxy as p on p.proxy_id = ap.proxy_id",
            "t_flow_proxy as up on up.proxy_id = ap.top_proxy_id"
        );
        $list =$model
            ->join($join)
            ->field('p.proxy_code,p.proxy_name,up.proxy_code as proxy_code2,up.proxy_name as proxy_name2,ap.*')
            ->where($where)
            ->order('ap.approve_status asc,ap.last_approve_date desc,ap.create_date desc')
            ->limit(3000)
            ->select();
        return $list;
    }


    public function account($where){
        $res=M('proxy_account')->where($where)->find();
        return $res;
    }

    public function account_detailed(){
        $where['pa.account_id']=trim(I('account_id'));
        $list =M('proxy_account pa')
            ->join('left join t_flow_proxy as p on p.proxy_id = pa.proxy_id and p.status = 1')
            ->join('left join t_flow_proxy as tp on tp.proxy_id=p.top_proxy_id')
            ->join('left join t_flow_sys_user as a on a.user_id = pa.create_user_id and a.status = 1')
            ->join('left join t_flow_sys_user as am on a.user_id = pa.modify_user_id and a.status = 1')
            ->field('pa.*,p.proxy_name,p.proxy_code,p.tel,a.user_name as create_name,am.user_name as modify_name,tp.proxy_name as top_proxy_name')
            ->where($where)
            ->find();
        return $list;
    }
    public function proxy_account_detailed(){
        $where['pa.account_id']=trim(I('account_id'));
        $list =M('proxy as p')
            ->join('left join t_flow_proxy_account as pa on pa.proxy_id = p.proxy_id and p.status = 1')
            ->join('left join t_flow_proxy as tp on tp.proxy_id=p.top_proxy_id')
            ->field('pa.*,p.proxy_name,p.proxy_code,p.tel,tp.proxy_name as top_proxy_name,tp.proxy_code as top_proxy_code')
            ->where($where)
            ->find();
        return $list;
    }
    public function account_detailed_self(){
        $proxy_id=D("SysUser")->self_proxy_id();
        $where['p.proxy_id']=$proxy_id;
        $list =M('proxy_account pa')
            ->join('left join t_flow_proxy as p on p.proxy_id = pa.proxy_id and p.status = 1')
            ->field('pa.*,p.proxy_name,p.proxy_code,p.proxy_id')
            ->where($where)
            ->find();
        return $list;
    }



    public function detailed(){
        $where['contract_id']=trim(I('contract_id'));
        $list =M('proxy_contract')
            ->where($where)
            ->find();
        return $list;
    }
     public function approve_process(){
        $where['contract_id']=trim(I('contract_id'));
        $list =M('proxy_contract_process')
            ->where($where)
            ->select();
        return $list;
    }


    public function proxy_detailed($where){
        $list =M('proxy_recharge_apply ap')
            ->join('left join t_flow_proxy as p on p.proxy_id = ap.proxy_id')
            ->field('ap.*,p.proxy_name,p.proxy_code')
            ->where($where)
            ->find();
        return $list;
    }


    public function account_record($condition){
        $model=M('proxy_account');
        /*下级代理商账户余额加上操作金额*/
        $map['account_id']= $condition['operate_account_id'];
        $money['account_balance']=$condition['operate_account_balance']+$condition['apply_money'];
        $money['modify_user_id']=D('SysUser')->self_id();
        $money['modify_date']=date('Y-m-d H:i:s',time());
        $res=$model->where($map)->save($money);
        /*当前代理商账户余额减少操作金额*/
        $self_account_id['account_id']=$condition['top_account_id'];
        if(D('SysUser')->self_user_type()==1){
            $self_account['account_balance']= 0;
        }else{
            $self_account['account_balance']= $condition['top_account_balance']-$condition['apply_money'];
        }
        $self_account['modify_user_id']=D('SysUser')->self_id();
        $self_account['modify_date']=date('Y-m-d H:i:s',time());
        $res2=$model->where($self_account_id)->save($self_account);
        if($res && $res2){
            if(D('SysUser')->self_user_type()==1){
                $record[0]['operater_before_balance']=0;  //操作前金额
                $record[0]['operater_after_balance']=0; //操作后金额
            }else{
                $record[0]['operater_before_balance']= $condition['top_account_balance'];  //操作前金额
                $record[0]['operater_after_balance']=$self_account['account_balance']; //操作后金额
            }
            $record[0]['operater_price']        = $condition['apply_money'];  //划拨金额
            $record[0]['operate_type']          = $condition['top_operate_type']; //划拨
            $record[0]['balance_type']          = $condition['top_balance_type'];//支出
            $record[0]['record_date']           = date('Y-m-d H:i:s',time());
            $record[0]['user_id']               = D('SysUser')->self_id();
            $record[0]['operation_date']        = date('Y-m-d H:i:s',time());
            $record[0]['user_type']             = $condition['operate_user_type'];
            $record[0]['proxy_id']              = D('SysUser')->self_proxy_id();
            $record[0]['obj_user_type']         = $condition['top_user_type'];
            $record[0]['obj_proxy_id']          = $condition['operate_proxy_id'];
            $record[0]['remark']                = $condition['remark'];
            /*下级代理商为收入*/
            $record[1]['operater_before_balance']   = $condition['operate_account_balance'];  //操作前金额
            $record[1]['operater_after_balance']    = $money['account_balance']; //操作后金额
            $record[1]['operater_price']            = $condition['apply_money'];  //划拨金额
            $record[1]['operate_type']              = $condition['operate_operate_type']; //充值
            $record[1]['balance_type']              = $condition['operate_balance_type'];//收入
            $record[1]['record_date']               = date('Y-m-d H:i:s',time());
            $record[1]['user_id']                   = D('SysUser')->self_id();
            $record[1]['operation_date']            = date('Y-m-d H:i:s',time());
            $record[1]['user_type']                 = $condition['operate_user_type'];
            $record[1]['proxy_id']                  = $condition['operate_proxy_id'];
            $record[1]['obj_user_type']             = $condition['top_user_type'];
            $record[1]['obj_proxy_id']              = D('SysUser')->self_proxy_id();
            $record[1]['remark']                    = $condition['remark'];
            //添加流水记录
            $recordResult=M('account_record')->addAll($record);
            if($recordResult){
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }

    public function order_refund($condition){
        $model=M('proxy_account');
        /*下级代理商账户余额加上操作金额*/
        $map['account_id']= $condition['operate_account_id'];
        $money['account_balance']=$condition['operate_account_balance']+$condition['apply_money'];
        $money['modify_user_id']=D('SysUser')->self_id();
        $money['modify_date']=date('Y-m-d H:i:s',time());
        $res=$model->where($map)->save($money);
        /*当前代理商账户余额减少操作金额*/
        $self_account_id['account_id']=$condition['top_account_id'];
        if(D('SysUser')->self_user_type()==1){
            $self_account['account_balance']= 0;
        }else{
            $self_account['account_balance']= $condition['top_account_balance']-$condition['apply_money'];
        }
        $self_account['modify_user_id']=D('SysUser')->self_id();
        $self_account['modify_date']=date('Y-m-d H:i:s',time());
        $res2=$model->where($self_account_id)->save($self_account);
        if($res && $res2){
            if(D('SysUser')->self_user_type()==1){
                $record[0]['operater_before_balance']=0;  //操作前金额
                $record[0]['operater_after_balance']=0; //操作后金额
            }else{
                $record[0]['operater_before_balance']= $condition['top_account_balance'];  //操作前金额
                $record[0]['operater_after_balance']=$self_account['account_balance']; //操作后金额
            }
            $record[0]['operater_price']        = $condition['apply_money'];  //划拨金额
            $record[0]['operate_type']          = $condition['top_operate_type']; //划拨
            $record[0]['balance_type']          = $condition['top_balance_type'];//支出
            $record[0]['record_date']           = date('Y-m-d H:i:s',time());
            $record[0]['user_id']               = D('SysUser')->self_id();
            $record[0]['operation_date']        = date('Y-m-d H:i:s',time());
            $record[0]['user_type']             = $condition['operate_user_type'];
            $record[0]['proxy_id']              = D('SysUser')->self_proxy_id();
            $record[0]['obj_user_type']         = $condition['top_user_type'];
            $record[0]['obj_proxy_id']          = $condition['operate_proxy_id'];
            /*下级代理商为收入*/
            $record[1]['operater_before_balance']   = $condition['operate_account_balance'];  //操作前金额
            $record[1]['operater_after_balance']    = $money['account_balance']; //操作后金额
            $record[1]['operater_price']            = $condition['apply_money'];  //退款金额
            $record[1]['operate_type']              = $condition['operate_operate_type']; //充值
            $record[1]['balance_type']              = $condition['operate_balance_type'];//收入
            $record[1]['record_date']               = date('Y-m-d H:i:s',time());
            $record[1]['user_id']                   = D('SysUser')->self_id();
            $record[1]['operation_date']            = date('Y-m-d H:i:s',time());
            $record[1]['user_type']                 = $condition['operate_user_type'];
            $record[1]['proxy_id']                  = $condition['operate_proxy_id'];
            $record[1]['obj_user_type']             = $condition['top_user_type'];
            $record[1]['obj_proxy_id']              = D('SysUser')->self_proxy_id();
            //添加流水记录
            $recordResult=M('account_record')->addAll($record);
            if($recordResult){
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }





}