<?php

namespace Common\Model;
use Think\Model;

class EnterpriseAccountModel extends Model{

    //尚通运营端和代理商端
    public function enterprise_account_list($where){
        $user_type=D('SysUser')->self_user_type();
        //尚通运营端
        $list = array();
        $user_id=D("SysUser")->self_id;
        $model=M('Enterprise as e');
        $where['e.status']=1;
        $where['e.approve_status'] = 1;
        if($user_type==1){
           // $where['up.status'] = array('neq',2);
            if(!D('SysUser')->is_admin()){
                $ids=D("Enterprise")->enterprise_child_ids();//获取所有可操作企业号
                $is=M("EnterpriseUser")->where(array("user_id"=>$user_id))->distinct(true)->field("enterprise_id")->select();
                if($is){
                    if($ids){
                        $ids=$ids.",";
                    }
                    foreach ($is as $v) {
                        $ids.=$v['enterprise_id'];
                    }
                }else{
                    if(!$ids){
                        return;
                    }
                }
                $where["e.enterprise_id"]=array("in",$ids);
            }
            $count=$model
                ->join('left join t_flow_proxy as up on up.proxy_id = e.top_proxy_id')
                ->join('left join t_flow_enterprise_account as ea on ea.enterprise_id = e.enterprise_id')
                ->where($where)
                ->count();

        $Page       = new \Think\Page($count,20);
        $show       = $Page->show();

            $list =$model
                ->join('left join t_flow_proxy as up on up.proxy_id = e.top_proxy_id ')
                ->join('left join t_flow_enterprise_account as ea on ea.enterprise_id = e.enterprise_id ')
                ->where($where)
                ->order('e.modify_date desc')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->field("e.*,up.proxy_code,up.proxy_name,ea.account_balance,ea.freeze_money,ea.new_quota_remind")
                ->select();
                $sum_results = $model
                    ->join('left join t_flow_proxy as up on up.proxy_id = e.top_proxy_id ')
                    ->join('left join t_flow_enterprise_account as ea on ea.enterprise_id = e.enterprise_id ')
                    ->where($where)
                    ->field('sum(ea.account_balance) as sum_money_one ,sum(ea.freeze_money) as sum_money_tow')
                    ->find();

                return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show,'sum_results'=>$sum_results);
        }
        //代理商端
        if($user_type==2){
            $proxy_id=D('SysUser')->self_proxy_id;

            $ids=D("Enterprise")->enterprise_ids();//获取该用户可操作的企业号
            if(!$ids){
                return;
            }
            $where['e.enterprise_id']=array("in",$ids);
            $count = $model
                ->join('left join t_flow_enterprise_account as ea on ea.enterprise_id = e.enterprise_id ')
                ->where($where)
                ->count();
            $Page = new \Think\Page($count,20);
            $show = $Page->show();
            $list =$model
                ->join('left join t_flow_enterprise_account as ea on ea.enterprise_id = e.enterprise_id')
                ->where($where)
                ->order('e.modify_date desc')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->field("e.*,ea.account_balance,ea.freeze_money")
                ->select();
            $sum_results = $model
                ->join('left join t_flow_enterprise_account as ea on ea.enterprise_id = e.enterprise_id ')
                ->where($where)
                ->field('sum(ea.account_balance) as sum_money_one ,sum(ea.freeze_money) as sum_money_tow')
                ->find();
                return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show,'sum_results'=>$sum_results);
        }
        if($user_type==3){
            $map['e.enterprise_id']=D('SysUser')->self_enterprise_id();
            $count=$model
                ->join('left join t_flow_enterprise_account as ea on ea.enterprise_id = e.enterprise_id ')
                ->where($where)
                ->count();

            $Page       = new \Think\Page($count,20);
            $show       = $Page->show();
            /*企业端*/
            $list =$model
            ->join('left join t_flow_enterprise_account as ea on ea.enterprise_id = e.enterprise_id and e.status = 1')
            ->field("e.*,ea.account_balance,ea.freeze_money")
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
            return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>'');
        }
    }

    //尚通运营端和代理商端
    public function enterprise_account_excel($where){
        $user_type=D('SysUser')->self_user_type();
        //尚通运营端
        $list = array();
        $user_id=D("SysUser")->self_id;
        $model=M('Enterprise as e');
        $where['e.status']=1;
        if($user_type==1){
            //$where['up.status'] = array('neq',2);
            if(!D('SysUser')->is_admin()){
                $ids=D("Enterprise")->enterprise_child_ids();//获取所有可操作企业号
                $is=M("EnterpriseUser")->where(array("user_id"=>$user_id))->distinct(true)->field("enterprise_id")->select();
                if($is){
                    if($ids){
                        $ids=$ids.",";
                    }
                    foreach ($is as $v) {
                        $ids.=$v['enterprise_id'];
                    }
                }else{
                    if(!$ids){
                        return;
                    }
                }
                $where["e.enterprise_id"]=array("in",$ids);
            }
            $list =$model
                ->join('left join t_flow_proxy as up on up.proxy_id = e.top_proxy_id')
                ->join('left join t_flow_enterprise_account as ea on ea.enterprise_id = e.enterprise_id ')
                ->where($where)
                ->order('e.modify_date desc')
                ->limit('0,30000')
                ->field("e.enterprise_code,e.enterprise_name,up.proxy_code,up.proxy_name,ea.account_balance,ea.freeze_money")//,ea.new_quota_remind
                ->select();
                return $list;
        }
        //代理商端
        if($user_type==2){
            $proxy_id=D('SysUser')->self_proxy_id;

            $ids=D("Enterprise")->enterprise_ids();//获取该用户可操作的企业号
            if(!$ids){
                return;
            }
            $where['e.enterprise_id']=array("in",$ids);
            $count = $model
                ->join('left join t_flow_enterprise_account as ea on ea.enterprise_id = e.enterprise_id ')
                ->where($where)
                ->count();
            $Page = new \Think\Page($count,20);
            $show = $Page->show();
            $list =$model
                ->join('left join t_flow_enterprise_account as ea on ea.enterprise_id = e.enterprise_id ')
                ->where($where)
                ->order('e.modify_date desc')
                ->limit('0,3000')
                ->field("e.enterprise_code,e.enterprise_name,ea.account_balance,ea.freeze_money")
                ->select();
                return $list;
        }
        if($user_type==3){
            $map['e.enterprise_id']=D('SysUser')->self_enterprise_id();
            $list =$model
            ->join('left join t_flow_enterprise_account as ea on ea.enterprise_id = e.enterprise_id ')
            ->field("e.enterprise_code,e.enterprise_name,ea.account_balance,ea.freeze_money")
            ->order('e.enterprise_id asc')
            ->where($map)
            ->limit('0,3000')
            ->select();
            return $list;
        }
    }


    public function account($where){
        $res=M('enterprise_account')->where($where)->find();
        return $res;
    }

    public function all_apply(){
        $where['enterprise_id']=D('SysUser')->self_enterprise_id();
        $res=M('enterprise_withdraw_apply')->where($where)->field('apply_id')->select();
        return $res;
    }

    /*该代理商下的所有二级代理商*/
    public function allEnterprise($where){
        $model=M('proxy p');
        $list=$model
            ->join('left join t_flow_enterprise as e on e.top_proxy_id = p.proxy_id and p.status = 1')
            ->field('e.enterprise_id,e.enterprise_name,e.enterprise_code')
            ->where($where)->select();
        return $list;
    }

    /*代理商信息*/
    public function  proxy($where){
        $model=M('enterprise_account  ep');
        $info=$model
            ->join('left join t_flow_enterprise as e on p.enterprise_id = ep.enterprise_id and p.status = 1')
            ->where($where)
            ->field('ep.*,e.enterprise_id,e.enterprise_name')
            ->find();
        return $info;
    }

    public function account_detailed(){
        $where['e.enterprise_id']=trim(I('get.enterprise_id'));
        $list =M('enterprise_account ea')
            ->join('left join t_flow_enterprise as e on e.enterprise_id = ea.enterprise_id and e.status = 1')
            ->join('left join t_flow_sys_user as a on a.user_id = ea.create_user_id and a.status = 1')
            ->join('left join t_flow_sys_user as am on a.user_id = ea.modify_user_id and am.status = 1')
            ->field('ea.*,e.enterprise_name,e.enterprise_code,a.user_name as create_name,am.user_name as modify_name')
            ->where($where)
            ->find();
        return $list;
    }

    public function all_recharge_apply(){
        $where['enterprise_id']=D('SysUser')->self_enterprise_id();
        $res=M('enterprise_recharge_apply')->where($where)->field('apply_id,apply_code')->select();
        return $res;
    }

    public function detailed(){
        $where['ea.apply_id']=trim(I('apply_id'));
        $where['ea.approve_status']=array("neq","1");
        $list =M('enterprise_recharge_apply ea')
            ->join('left join t_flow_enterprise as e on e.enterprise_id = ea.enterprise_id and e.status = 1')
            ->field('ea.*,e.enterprise_name,e.enterprise_code')
            ->where($where)
            ->find();
        return $list;
    }

    public function recharge_apply_detailed($where){
        $list =M('enterprise_recharge_apply ea')
            ->join('left join t_flow_enterprise as e on e.enterprise_id = ea.enterprise_id and e.status = 1')
            ->join('left join t_flow_proxy as p on e.top_proxy_id = p.proxy_id ')
            ->field('ea.*,e.enterprise_name,e.enterprise_code,p.proxy_type,p.proxy_code,p.proxy_name')
            ->where($where)
            ->find();
        return $list;
    }


/*提现申请基本详情*/
    public function withdraw_detailed($where){
        $list =M('enterprise_withdraw_apply ew')
            ->join('left join t_flow_enterprise as e on e.enterprise_id = ew.enterprise_id')
            ->field('ew.*,e.enterprise_name,e.enterprise_code')
            ->where($where)
            ->find();
        return $list;
    }
    /*提现申请状态*/
        public function withdraw_process($where){
            $list =M('enterprise_withdraw_process')->where($where)->select();
            return $list;
        }
     /*充值申请状态 */
        public function recharge_process($where){
            $list =M('enterprise_recharge_process')->where($where)->select();
            return $list;
        }   
    public function enterprise_apply_list($where){
        $model=M("enterprise_recharge_apply ea");
        $user_type=D('SysUser')->self_user_type();
        //if($user_type==1){
            $join=array(
                't_flow_enterprise as e on e.enterprise_id=ea.enterprise_id',
                't_flow_proxy as up on up.proxy_id =e.top_proxy_id'
            );
            $count=$model
                ->join($join)
                ->where($where)
                ->count();
            $Page     = new \Think\Page($count,20);
            $show     = $Page->show();

            $list =$model
                ->join($join)
                ->field('ea.apply_id,ea.enterprise_id,ea.apply_code,ea.source,ea.apply_money,ea.transaction_number,ea.credential_one,ea.top_proxy_id,ea.approve_status,ea.last_approve_date,ea.transaction_date,ea.create_user_id,ea.create_date,ea.modify_date,ea.apply_type,e.enterprise_name,e.enterprise_code,up.proxy_type,up.proxy_name,up.proxy_code')
                ->where($where)
                ->order('ea.modify_date desc,ea.approve_status asc,ea.last_approve_date desc')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
        $sum_results=0;
        if($user_type!=3){
            $sum_results =$model
                ->join($join)
                ->where($where)
                ->field('sum(ea.apply_money) as sum_money_one' )
                ->find();
        }

        //}

/*        if($user_type==2){

            $join=array('t_flow_enterprise as e on e.enterprise_id=ea.enterprise_id','t_flow_proxy as up on up.proxy_id =e.top_proxy_id');
            $count=$model
               ->join($join)
               ->where($where)
               ->count();
            $Page     = new \Think\Page($count,20);
            $show     = $Page->show();

            $list =$model
             ->join($join)
                ->field('ea.apply_id,ea.enterprise_id,ea.apply_code,ea.source,ea.apply_money,ea.transaction_number,ea.credential_one,ea.top_proxy_id,ea.approve_status,ea.last_approve_date,ea.transaction_date,ea.create_user_id,ea.create_date,ea.modify_date,ea.apply_type,e.enterprise_name,e.enterprise_code,up.proxy_type,up.proxy_name,up.proxy_code')
             ->where($where)
             ->order('ea.modify_date desc,ea.approve_status asc,ea.last_approve_date desc')
             ->limit($Page->firstRow.','.$Page->listRows)
             ->select();
        }
        if($user_type==3){
            $join=array('t_flow_enterprise as e on e.enterprise_id=ea.enterprise_id','t_flow_proxy as up on up.proxy_id =e.top_proxy_id');
            $where['e.enterprise_id']=D('SysUser')->self_enterprise_id();
            $count=$model
                ->join($join)
                ->where($where)
                ->count();

            $Page       = new \Think\Page($count,20);
            $show   	= $Page->show();

            $list =$model
                ->join($join)
                ->field('ea.apply_id,ea.enterprise_id,ea.apply_code,ea.source,ea.apply_money,ea.transaction_number,ea.credential_one,ea.top_proxy_id,ea.approve_status,ea.last_approve_date,ea.transaction_date,ea.create_user_id,ea.create_date,ea.modify_date,ea.apply_type,e.enterprise_name,e.enterprise_code,up.proxy_type,up.proxy_name,up.proxy_code')
                ->where($where)
                ->order('ea.modify_date desc,ea.approve_status asc,ea.last_approve_date desc')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
        }*/


        return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show,'sum_results'=>$sum_results);
    }

    public function enterprise_apply_excel($where){
        $model=M("enterprise_recharge_apply ea");
       // $user_type=D('SysUser')->self_user_type();
        //if($user_type==1){
            $join = array(
                "t_flow_enterprise as e on e.enterprise_id = ea.enterprise_id",
                "t_flow_proxy as up on up.proxy_id =e.top_proxy_id"
            );
            $list =$model
                ->join($join)
                ->field('ea.apply_id,ea.enterprise_id,ea.apply_code,ea.source,ea.apply_money,ea.transaction_number,ea.credential_one,ea.top_proxy_id,ea.approve_status,ea.last_approve_date,ea.transaction_date,ea.create_user_id,ea.create_date,ea.modify_date,ea.apply_type,e.enterprise_name,e.enterprise_code,up.proxy_type,up.proxy_name,up.proxy_code,ea.apply_type')
                ->where($where)
                ->order('ea.modify_date desc,ea.approve_status asc')
                ->limit(3000)
                ->select();
       // }
/*
        if($user_type==2){
            $join= array("t_flow_enterprise as e on e.enterprise_id = ea.enterprise_id");
            $list =$model
                ->join($join)
                ->field('ea.apply_id,ea.enterprise_id,ea.apply_code,ea.source,ea.apply_money,ea.transaction_number,ea.credential_one,ea.top_proxy_id,ea.approve_status,ea.transaction_date,ea.last_approve_date,ea.create_user_id,ea.create_date,ea.modify_date,ea.apply_type,e.enterprise_name,e.enterprise_code')
                ->where($where)
                ->order('ea.modify_date desc,ea.approve_status asc,ea.last_approve_date desc')
                ->limit(3000)
                ->select();
        }
        if($user_type==3){
            $join='t_flow_enterprise as e on ea.enterprise_id = e.enterprise_id';
            $where['e.enterprise_id']=D('SysUser')->self_enterprise_id();
            $list =$model
                ->join($join)
                ->field('ea.apply_id,ea.enterprise_id,ea.apply_code,ea.source,ea.apply_money,ea.transaction_number,ea.credential_one,ea.top_proxy_id,ea.approve_status,ea.last_approve_date,ea.transaction_date,ea.create_user_id,ea.create_date,ea.modify_date,ea.apply_type,e.enterprise_name,e.enterprise_code')
                ->where($where)
                ->order('ea.modify_date desc,ea.approve_status asc,ea.last_approve_date desc')
                ->limit(3000)
                ->select();
        }*/
        return $list;
    }


    public function account_record($condition){
        $model=M('enterprise_account');
        /*下级代理商账户余额加上操作金额*/
        $map['account_id']= $condition['operate_account_id'];
        $money['account_balance']=$condition['operate_account_balance']+$condition['apply_money'];
        $money['modify_user_id']=D('SysUser')->self_id();
        $money['modify_date']=date('Y-m-d H:i:s',time());
        /*一级代理商减去账户余额*/
        $self_account['account_balance']= $condition['top_account_balance']-$condition['apply_money'];
        if($self_account['account_balance']<0){
            return 0;
        }
        $self_account['modify_user_id']=D('SysUser')->self_id();
        $self_account['modify_date']=date('Y-m-d H:i:s',time());
        $record[0]['operater_before_balance']= $condition['top_account_balance'];  //操作前金额
        $record[0]['operater_after_balance']=$self_account['account_balance']; //操作后金额
        $record[0]['operater_price']=$condition['apply_money'];  //划拨金额
        $record[0]['operate_type']=$condition['top_operate_type']; //划拨
        $record[0]['balance_type']= $condition['top_balance_type'];//支出
        $record[0]['record_date']=date('Y-m-d H:i:s',time());
        $record[0]['user_id']=D('SysUser')->self_id();;
        $record[0]['operation_date']=date('Y-m-d H:i:s',time());
        $record[0]['user_type']=$condition['top_user_type'];
        $record[0]['remark']=$condition['remark'];
        if($condition['top_proxy_id']){
            $record[0]['proxy_id']=$condition['top_proxy_id'];    
        }else{
            $record[0]['proxy_id']=D("SysUser")->self_proxy_id();
        }
        $record[0]['enterprise_id']=null;
        $record[0]['obj_user_type']=$condition['operate_user_type'];
        $record[0]['obj_proxy_id']=null;
        $record[0]['obj_enterprise_id']= $condition['operate_enterprise_id'];
        $record[0]['device_name']       = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));
        /*下级代理商为收入*/
        $record[1]['operater_before_balance']=$condition['operate_account_balance'];  //操作前金额
        $record[1]['operater_after_balance']= $money['account_balance']; //操作后金额
        $record[1]['operater_price']=$condition['apply_money'];  //划拨金额
        $record[1]['operate_type']=$condition['operate_operate_type']; //充值
        $record[1]['balance_type']=$condition['operate_balance_type'];//收入
        $record[1]['record_date']=date('Y-m-d H:i:s',time());
        $record[1]['user_id']=D('SysUser')->self_id();
        $record[1]['operation_date']=date('Y-m-d H:i:s',time());
        $record[1]['user_type']=$condition['operate_user_type'];
        $record[1]['remark']=$condition['remark'];
        $record[1]['proxy_id']=null;
        $record[1]['enterprise_id']= $condition['operate_enterprise_id'];
        $record[1]['obj_user_type']=$condition['top_user_type'];
        if($condition['top_proxy_id']){
            $record[1]['obj_proxy_id']=$condition['top_proxy_id'];    
        }else{
            $record[1]['obj_proxy_id']=D('SysUser')->self_proxy_id();
        }
        $record[1]['obj_enterprise_id']=null;
        $record[1]['device_name']       = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));
        $where['account_id']=$condition['top_account_id'];
        $res=$model->where($map)->save($money);
        $self_res='';
        $self_res=M('proxy_account')->where($where)->save($self_account);
        $recordResult=M('account_record')->addAll($record);
        if(D('SysUser')->self_user_type()==1) {
            if($res>0 && $recordResult>0){
                return 1;
            }else{
                return 0;
            }
        }else{
            if($res>0 && $self_res>0 && $recordResult>0){
                return 1;
            }else{
                return 0;
            }
        }

    }

    //资金划拨
    public function account_sh($condition){
        $model=M('enterprise_account');
        /*下级代理商账户余额加上操作金额*/
        $map['account_id']= $condition['operate_account_id'];
        $money['account_balance']=$condition['operate_account_balance']-$condition['apply_money'];
        $money['modify_user_id']=D('SysUser')->self_id();
        $money['modify_date']=date('Y-m-d H:i:s',time());
        /*一级代理商减去账户余额*/
        $self_account['account_balance']= $condition['top_account_balance']+$condition['apply_money'];
        if($self_account['account_balance']<0){
            return 0;
        }
        $self_account['modify_user_id']=D('SysUser')->self_id();
        $self_account['modify_date']=date('Y-m-d H:i:s',time());
        $record[0]['operater_before_balance']= $condition['top_account_balance'];  //操作前金额
        $record[0]['operater_after_balance']=$self_account['account_balance']; //操作后金额
        $record[0]['operater_price']=$condition['apply_money'];  //划拨金额
        $record[0]['operate_type']=$condition['top_operate_type']; //划拨
        $record[0]['balance_type']= $condition['top_balance_type'];//支出
        $record[0]['record_date']=date('Y-m-d H:i:s',time());
        $record[0]['user_id']=D('SysUser')->self_id();;
        $record[0]['operation_date']=date('Y-m-d H:i:s',time());
        $record[0]['user_type']=$condition['top_user_type'];
        $record[0]['proxy_id']=$condition['top_proxy_id'];
        $record[0]['enterprise_id']=null;
        $record[0]['obj_user_type']=$condition['operate_user_type'];
        $record[0]['obj_proxy_id']=null;
        $record[0]['obj_enterprise_id']= $condition['operate_enterprise_id'];
        $record[0]['remark']=$condition['remark'];
        $record[0]['device_name']       = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));
        /*下级代理商为收入*/
        $record[1]['operater_before_balance']=$condition['operate_account_balance'];  //操作前金额
        $record[1]['operater_after_balance']= $money['account_balance']; //操作后金额
        $record[1]['operater_price']=$condition['apply_money'];  //划拨金额
        $record[1]['operate_type']=$condition['operate_operate_type']; //充值
        $record[1]['balance_type']=$condition['operate_balance_type'];//收入
        $record[1]['record_date']=date('Y-m-d H:i:s',time());
        $record[1]['user_id']=D('SysUser')->self_id();
        $record[1]['operation_date']=date('Y-m-d H:i:s',time());
        $record[1]['user_type']=$condition['operate_user_type'];
        $record[1]['proxy_id']=null;
        $record[1]['enterprise_id']= $condition['operate_enterprise_id'];
        $record[1]['obj_user_type']=$condition['top_user_type'];
        $record[1]['obj_proxy_id']=$condition['top_proxy_id'];
        $record[1]['obj_enterprise_id']=null;
        $record[1]['remark']=$condition['remark'];
        $record[1]['device_name']       = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));
        $where['account_id']=$condition['top_account_id'];
        $res=$model->where($map)->save($money);
        $self_res='';
        $self_res=M('proxy_account')->where($where)->save($self_account);
        $recordResult=M('account_record')->addAll($record);
        if(D('SysUser')->self_user_type()==1) {
            if($res>0 && $recordResult>0){
                return 1;
            }else{
                return 0;
            }
        }else{
            if($res>0 && $self_res>0 && $recordResult>0){
                return 1;
            }else{
                return 0;
            }
        }

    }


    //资金提现
    public function account_back($condition){
        $model=M('enterprise_account');
        /*下级代理商账户余额操作金额*/
        $map['account_id']= $condition['operate_account_id'];
        $money['freeze_money']=$condition['freeze_money']-$condition['apply_money'];
        $money['modify_user_id']=D('SysUser')->self_id();
        $money['modify_date']=date('Y-m-d H:i:s',time());
        /*一级代理商账户余额*/
        $self_account['account_balance']= $condition['top_account_balance']+$condition['apply_money'];
        $self_account['modify_user_id']=D('SysUser')->self_id();
        $self_account['modify_date']=date('Y-m-d H:i:s',time());

        if(D('SysUser')->self_user_type()==1){
            $record[0]['operater_before_balance']=0;  //操作前金额
            $record[0]['operater_after_balance']=0; //操作后金额
        }else{
            $record[0]['operater_before_balance']= $condition['top_account_balance'];  //操作前金额
            $record[0]['operater_after_balance']=$self_account['account_balance']; //操作后金额

        }
        $record[0]['operater_price']=$condition['apply_money'];  //划拨金额
        $record[0]['operate_type']=$condition['top_operate_type']; //划拨
        $record[0]['balance_type']= $condition['top_balance_type'];//支出
        $record[0]['record_date']=date('Y-m-d H:i:s',time());
        $record[0]['user_id']=D('SysUser')->self_id();;
        $record[0]['operation_date']=date('Y-m-d H:i:s',time());
        $record[0]['user_type']=$condition['top_user_type'];
        $record[0]['proxy_id']=D('SysUser')->self_proxy_id();
        $record[0]['enterprise_id']=null;
        $record[0]['obj_user_type']=$condition['operate_user_type'];
        $record[0]['obj_proxy_id']=null;
        $record[0]['obj_enterprise_id']= $condition['operate_enterprise_id'];
        $record[0]['device_name']       = get_client_ip2().'+'.get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));
        /*下级代理商为收入*/
        /*$record[1]['operater_before_balance']=$condition['operate_account_balance']+$condition['apply_money'];  //操作前金额
        $record[1]['operater_after_balance']= $condition['operate_account_balance']; //操作后金额
        $record[1]['operater_price']=$condition['apply_money'];  //划拨金额
        $record[1]['operate_type']=$condition['operate_operate_type']; //充值
        $record[1]['balance_type']=$condition['operate_balance_type'];//收入
        $record[1]['record_date']=date('Y-m-d H:i:s',time());
        $record[1]['user_id']=D('SysUser')->self_id();
        $record[1]['operation_date']=date('Y-m-d H:i:s',time());
        $record[1]['user_type']=$condition['operate_user_type'];
        $record[1]['proxy_id']=null;
        $record[1]['enterprise_id']= $condition['operate_enterprise_id'];
        $record[1]['obj_user_type']=$condition['top_user_type'];
        $record[1]['obj_proxy_id']=D('SysUser')->self_proxy_id();
        $record[1]['obj_enterprise_id']=null;
        $record[1]['device_name']       = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));*/
        $where['account_id']=$condition['top_account_id'];
        $res=$model->where($map)->save($money);
        $self_res='';
        if(D('SysUser')->self_user_type()!=1){
            $self_res=M('proxy_account')->where($where)->save($self_account);
        }
        $recordResult=M('account_record')->addAll($record);
        if(D('SysUser')->self_user_type()==1) {
            if($res>0 && $recordResult>0){
                return 1;
            }else{
                return 0;
            }
        }else{
            if($res>0 && $self_res>0 && $recordResult>0){
                return 1;
            }else{
                return 0;
            }
        }
    }

    //企业充值明细
    public function recharge_record($where){
        $user_type=D('SysUser')->self_user_type();
        $list = array();
        $user_id=D("SysUser")->self_id;
        $model=M('account_record as ar');
        //固定的条件
       // $where['up.status']=array('neq',2); //代理商状态
        $where['e.status']=array('neq',2);//企业状态
        $where['ar.enterprise_id']=array("gt",0);
        //尚通运营端 
        if($user_type==1){
            if(!D('SysUser')->is_admin()){
                $ids=D("Enterprise")->enterprise_child_ids();//获取所有可操作企业号
                $is=M("EnterpriseUser")->where(array("user_id"=>$user_id))->distinct(true)->field("enterprise_id")->select();
                if($is){
                    if($ids){
                        $ids=$ids.",";
                    }
                    foreach ($is as $v) {
                        $ids.=$v['enterprise_id'];
                    }
                }else{
                    if(!$ids){
                        return;
                    }
                }
                $where["e.enterprise_id"]=array("in",$ids);
            }
            $join=array('t_flow_enterprise as e on e.enterprise_id = ar.enterprise_id',
                        't_flow_proxy as up on up.proxy_id = ar.obj_proxy_id');
            $count=$model
                ->join($join,'left')
                ->where($where)
                ->count();
        $Page       = new \Think\Page($count,20);
        $show       = $Page->show();
            $sum_results = $model
                ->join($join,'left')->where($where)
                ->field('sum(ar.operater_price) as sum_apply_money')
                ->find();
            $list =$model
                ->join($join,'left')
                ->where($where)
                ->order('ar.record_id desc')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->field("ar.*,e.enterprise_name,e.enterprise_code,up.proxy_code as top_proxy_code,up.proxy_name as obj_proxy_name")
                ->select();
                return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show,'sum_results'=>$sum_results['sum_apply_money']);
        }
    }


    //企业充值明细
    public function recharge_record_excel($where){
        $user_type=D('SysUser')->self_user_type();
        $list = array();
        $user_id=D("SysUser")->self_id;
        $model=M('account_record as ar');
        //固定的条件
        //$where['up.status']=array('neq',2); //代理商状态
        $where['e.status']=array('neq',2);//企业状态
        $where['ar.enterprise_id']=array("gt",0);
        //尚通运营端
        if($user_type==1){
            if(!D('SysUser')->is_admin()){
                $ids=D("Enterprise")->enterprise_child_ids();//获取所有可操作企业号
                $is=M("EnterpriseUser")->where(array("user_id"=>$user_id))->distinct(true)->field("enterprise_id")->select();
                if($is){
                    if($ids){
                        $ids=$ids.",";
                    }
                    foreach ($is as $v) {
                        $ids.=$v['enterprise_id'];
                    }
                }else{
                    if(!$ids){
                        return;
                    }
                }
                $where["e.enterprise_id"]=array("in",$ids);
            }
            $list =$model
                ->join('left join t_flow_enterprise as e on e.enterprise_id = ar.enterprise_id')
                ->join('left join t_flow_proxy as up on up.proxy_id = ar.obj_proxy_id')
                ->where($where)
                ->order('ar.record_id desc')
                ->limit(3000)
                //->field("e.enterprise_code,e.enterprise_name,up.proxy_code as top_proxy_code,up.proxy_name as obj_proxy_name,ar.operater_price,ar.operater_before_balance,ar.operater_after_balance,ar.record_date")
                ->field("e.enterprise_code,e.enterprise_name,up.proxy_code as top_proxy_code,up.proxy_name as obj_proxy_name,ar.operater_price,ar.record_date")
                ->select();
            return $list;
        }
    }

    //企业提现明细
    public function widthdraw_record($where){
        $user_type=D('SysUser')->self_user_type();
        $list = array();
        $user_id=D("SysUser")->self_id;
        $model=M('account_record as ar');
        //固定的条件
        //$where['up.status']=array('neq',2); //代理商状态
        $where['e.status']=array('neq',2);//企业状态
        $where['ar.operate_type']=3; //表示充值
        $where['ar.enterprise_id']=array("gt",0);
        //尚通运营端 
        if($user_type==1){
            if(!D('SysUser')->is_admin()){
                $ids=D("Enterprise")->enterprise_child_ids();//获取所有可操作企业号
                $is=M("EnterpriseUser")->where(array("user_id"=>$user_id))->distinct(true)->field("enterprise_id")->select();
                if($is){
                    if($ids){
                        $ids=$ids.",";
                    }
                    foreach ($is as $v) {
                        $ids.=$v['enterprise_id'];
                    }
                }else{
                    if(!$ids){
                        return;
                    }
                }
                $where["e.enterprise_id"]=array("in",$ids);
            }
            $join=array('t_flow_enterprise as e on e.enterprise_id = ar.enterprise_id',
                't_flow_proxy as up on up.proxy_id = ar.obj_proxy_id');
            $sum_results = $model
                ->join($join,'left')->where($where)
                ->field('sum(ar.operater_price) as sum_apply_money')
                ->find();

            $count=$model
                ->join($join,'left')
                ->where($where)
                ->count();
        $Page       = new \Think\Page($count,20);
        $show       = $Page->show();

            $list =$model
                ->join($join,'left')
                ->where($where)
                ->order('ar.record_id desc')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->field("ar.*,e.enterprise_name,e.enterprise_code,up.proxy_code as top_proxy_code,up.proxy_name as obj_proxy_name")
                ->select();   
                return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show,'sum_results'=>$sum_results['sum_apply_money']);
        }
    }


    //企业收支明细
    public function all_record($where){
        /*$user_type=D('SysUser')->self_user_type();
        $list = array();
        $user_id=D("SysUser")->self_id;*/
        $model=M('account_record as ar');
        //固定的条件
       // $where['up.status']=array('neq',2); //代理商状态
        $where['e.status']=array('neq',2);//企业状态

        $join=array('t_flow_enterprise as e on e.enterprise_id = ar.enterprise_id',
            't_flow_enterprise as oe on  oe.enterprise_id=ar.obj_enterprise_id and ar.obj_enterprise_id is not null',
            't_flow_proxy  as up on up.proxy_id = ar.obj_proxy_id and ar.obj_proxy_id is not null');
        $count=$model
            ->join($join,'left')
            ->where($where)
            ->count();
        $Page       = new \Think\Page($count,20);
        $show       = $Page->show();

        $list =$model
            ->join($join,'left')
            ->where($where)
            ->order('ar.record_id desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->field("ar.*,e.enterprise_name,e.enterprise_code,up.proxy_code as obj_proxy_code,up.proxy_name as obj_proxy_name")
            ->select();
        return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show);
    }

    //企业收支明细表导出
    public function all_record_excel($where){
     /*   $user_type=D('SysUser')->self_user_type();
        $list = array();
        $user_id=D("SysUser")->self_id;*/
        $model=M('account_record as ar');
        //固定的条件
       // $where['up.status']=array('neq',2); //代理商状态
        $where['e.status']=array('neq',2);//企业状态

        $join=array(
            't_flow_enterprise as e on e.enterprise_id = ar.enterprise_id',
            't_flow_proxy as up on up.proxy_id =e.top_proxy_id'
        );
        $list =$model
            ->join($join)
            ->where($where)
            ->order('ar.record_id desc')
            ->limit(3000)
            ->field("ar.*,e.enterprise_name,e.enterprise_code,up.proxy_code as obj_proxy_code,up.proxy_name as obj_proxy_name")
            ->select();
        return $list;
    }

    public function order_refund($condition){
        $model=M('enterprise_account');
        /*企业商账户余额加上操作金额*/
        $map['account_id']= $condition['operate_account_id'];
        $money['account_balance']=$condition['operate_account_balance']+$condition['apply_money'];
        $money['modify_user_id']=D('SysUser')->self_id();
        $money['modify_date']=date('Y-m-d H:i:s',time());
        $res=$model->where($map)->save($money);
        if($res){
            if(D('SysUser')->self_user_type()==1){
                $record[1]['operater_before_balance']=0;  //操作前金额
                $record[1]['operater_after_balance']=0; //操作后金额
            }
            $record[1]['operater_price']        = $condition['apply_money'];  //划拨金额
            $record[1]['operate_type']          = $condition['top_operate_type']; //返还
            $record[1]['balance_type']          = $condition['top_balance_type'];//支出
            $record[1]['record_date']           = date('Y-m-d H:i:s',time());
            $record[1]['user_id']               = D('SysUser')->self_id();
            $record[1]['operation_date']        = date('Y-m-d H:i:s',time());
            $record[1]['user_type']             = $condition['top_user_type'];
            $record[1]['proxy_id']              = D('SysUser')->self_proxy_id();
            $record[1]['enterprise_id']         =null;
            $record[1]['obj_user_type']         = $condition['operate_user_type'];
            $record[1]['obj_proxy_id']          =null;
            $record[1]['obj_enterprise_id']     = $condition['operate_enterprise_id'];
            $record[1]['device_name']           = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));
            /*下级企业为收入*/
            $record[0]['operater_before_balance']   = $condition['operate_account_balance'];  //操作前金额
            $record[0]['operater_after_balance']    = $money['account_balance']; //操作后金额
            $record[0]['operater_price']            = $condition['apply_money'];  //退款金额
            $record[0]['operate_type']              = $condition['operate_operate_type']; //返还
            $record[0]['balance_type']              = $condition['operate_balance_type'];//收入
            $record[0]['record_date']               = date('Y-m-d H:i:s',time());
            $record[0]['user_id']                   = D('SysUser')->self_id();
            $record[0]['operation_date']            = date('Y-m-d H:i:s',time());
            $record[0]['user_type']                 = $condition['operate_user_type'];
            $record[0]['proxy_id']                  =null;
            $record[0]['enterprise_id']             = $condition['operate_enterprise_id'];
            $record[0]['obj_user_type']             = $condition['top_user_type'];
            $record[0]['obj_proxy_id']              = D('SysUser')->self_proxy_id();
            $record[0]['obj_enterprise_id']         = null;
            $record[0]['device_name']               = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));
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



    public function proxy_type() {
        $where['proxy_id']=D('SysUser')->self_proxy_id();
        $res=M('proxy')->where($where)->field('proxy_type')->find();
        if($res['proxy_type']==1){
            return 1;
        }else{
            return 0;
        }
    }

    public function top_proxy_type(){
        $where['e.enterprise_id']=D('SysUser')->self_enterprise_id();
        $res=M('enterprise as e')
            ->join('left join t_flow_proxy  as p on e.top_proxy_id=p.proxy_id')
            ->where($where)
            ->field('p.proxy_type')
            ->find();
        if($res['proxy_type']==1){
            return 1;
        }else{
            return 0;
        }
    }

    /**
     *  根据企业ID获取对应的账户表主键ID
     */
    public function get_aid_by_eid($eid) {
        $account_id = 0;
        if(is_numeric($eid) && $eid > 0) {
            $account_id = M('enterprise_account')->where("enterprise_id={$eid}")->getField('account_id');
        }
        return $account_id;
    }



}