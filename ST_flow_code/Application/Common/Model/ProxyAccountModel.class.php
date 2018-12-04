<?php

namespace Common\Model;
use Think\Model;

class ProxyAccountModel extends Model{
    public function proxyAccountList($where){
        $model=M('Proxy as p');   
        $join=array(
           C('DB_PREFIX').'proxy_account as pa on p.proxy_id=pa.proxy_id',
            C('DB_PREFIX').'proxy as up on p.top_proxy_id =up.proxy_id',
            C('DB_PREFIX').'proxy_loan as pl on p.proxy_id =pl.proxy_id and pl.is_pay_off=0 and pl.approve_status=5'
            );
        $count = $model
            ->join($join,'left')
            ->where($where)
            ->count();
        $Page = new \Think\Page($count,20);
        $show = $Page->show();
        
        $list =$model
            ->join($join,'left')
            ->where($where)
            ->order('p.proxy_id asc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->field('pa.*,p.proxy_name as proxy_name,p.proxy_code as proxy_code,
            up.proxy_name as top_proxy_name,up.proxy_code  as top_proxy_code,
            up.proxy_level  as top_proxy_level,up.proxy_id  as top_proxy_id,
            sum(ifnull(pl.loan_money,0)-ifnull(pl.repayment_money,0)) as loan_money')
            ->group('p.proxy_id')
            ->select();

            $sum_results = $model
                ->join($join,'left')->where($where)
                ->field('sum(pa.account_balance) as sum_money_one ,
                sum(pa.freeze_money) as sum_money_tow,
                sum(ifnull(pl.loan_money,0)-ifnull(pl.repayment_money,0)) as loan_money_one')
                ->find();


            return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show,'sum_results'=>$sum_results);
    }
    public function export_excel($where){
        $model=M('Proxy as p');
        if(D('SysUser')->self_user_type()==1){
            $field='p.proxy_code as proxy_code,p.proxy_name as proxy_name,up.proxy_code  as top_proxy_code,up.proxy_name as top_proxy_name,(pa.account_balance-(sum(ifnull(pl.loan_money,0)-ifnull(pl.repayment_money,0)))) account_balance,(sum(ifnull(pl.loan_money,0)-ifnull(pl.repayment_money,0))) credit_money,pa.freeze_money';//,pa.new_quota_remind
        }else{
            $field= 'p.proxy_code as proxy_code,p.proxy_name as proxy_name,up.proxy_code  as top_proxy_code,up.proxy_name as top_proxy_name,(pa.account_balance-(sum(ifnull(pl.loan_money,0)-ifnull(pl.repayment_money,0)))) account_balance,(sum(ifnull(pl.loan_money,0)-ifnull(pl.repayment_money,0))) credit_money,pa.freeze_money';
        }
        $list =$model
            ->join(C('DB_PREFIX').'proxy as up on up.proxy_id = p.top_proxy_id ','left')
            ->join(C('DB_PREFIX').'proxy_account as pa on pa.proxy_id = p.proxy_id','left')
            ->join(C('DB_PREFIX').'proxy_loan as pl on p.proxy_id =pl.proxy_id and pl.is_pay_off=0 and pl.approve_status=5','left')
            ->where($where)
            ->order('p.proxy_id asc')
            ->group('p.proxy_id')
            ->limit(3000)
            ->field($field)
            ->select();
        return $list;
    }

    /*该代理商下的所有二级代理商*/
    public function allProxy($where){
        $model=M('proxy p');
        $list=$model
            ->join(C('DB_PREFIX').'proxy as up on p.top_proxy_id = up.proxy_id','left')
            ->field('p.proxy_id,p.proxy_name,p.proxy_code')
            ->where($where)->select();
        return $list;
    }

    /*代理商信息*/
    public function  proxy($where){
        $model=M('proxy_account  ap');
        $info=$model
            ->join(C('DB_PREFIX').'proxy as p on p.proxy_id = ap.proxy_id and p.status = 1','left')
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
            C('DB_PREFIX')."proxy as p on p.proxy_id = ap.proxy_id",
            C('DB_PREFIX')."proxy as up on up.proxy_id = ap.top_proxy_id"
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
            ->order('ap.modify_date desc,ap.approve_status asc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
            $sum_results = $model
                ->join($join,'left')->where($where)
                ->field('sum(ap.apply_money) as sum_money_one')
                ->find();
        return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show,'sum_results'=>$sum_results);
    }

    public function proxy_export_excel($where){
        $model=M('proxy_recharge_apply ap');
        $join = array(
            C('DB_PREFIX')."proxy as p on p.proxy_id = ap.proxy_id",
            C('DB_PREFIX')."proxy as up on up.proxy_id = ap.top_proxy_id"
        );
        $list =$model
            ->join($join)
            ->field('p.proxy_code,p.proxy_name,up.proxy_code as proxy_code2,up.proxy_name as proxy_name2,p.proxy_type,ap.*')
            ->where($where)
            ->order('ap.create_date desc,ap.approve_status asc')
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
            ->join(C('DB_PREFIX').'proxy as p on p.proxy_id = pa.proxy_id and p.status = 1','left')
            ->join(C('DB_PREFIX').'proxy as tp on tp.proxy_id=p.top_proxy_id','left')
            ->join(C('DB_PREFIX').'sys_user as a on a.user_id = pa.create_user_id and a.status = 1','left')
            ->join(C('DB_PREFIX').'sys_user as am on a.user_id = pa.modify_user_id and a.status = 1','left')
            ->field('pa.*,p.proxy_name,p.proxy_code,p.tel,a.user_name as create_name,am.user_name as modify_name,tp.proxy_name as top_proxy_name')
            ->where($where)
            ->find();
        return $list;
    }
    public function proxy_account_detailed(){
        $where['pa.account_id']=trim(I('account_id'));
        $list =M('proxy as p')
            ->join(C('DB_PREFIX').'proxy_account as pa on pa.proxy_id = p.proxy_id and p.status = 1','left')
            ->join(C('DB_PREFIX').'proxy as tp on tp.proxy_id=p.top_proxy_id','left')
            ->join(C('DB_PREFIX').'proxy_loan as pl on p.proxy_id =pl.proxy_id and pl.is_pay_off=0 and pl.approve_status=5','left')
            ->field('pa.*,p.proxy_name,p.proxy_code,p.tel,tp.proxy_name as top_proxy_name,
            tp.proxy_code as top_proxy_code,
            sum(ifnull(pl.loan_money,0)-ifnull(pl.repayment_money,0)) as loan_money')
            ->where($where)
            ->group('p.proxy_id')
            ->find();
        return $list;
    }
    public function account_detailed_self(){
        $proxy_id=D("SysUser")->self_proxy_id();
        $where['p.proxy_id']=$proxy_id;
        $list =M('proxy_account pa')
            ->join(C('DB_PREFIX').'proxy as p on p.proxy_id = pa.proxy_id and p.status = 1','left')
            ->field('pa.*,p.proxy_name,p.proxy_code,p.proxy_id')
            ->where($where)
            ->find();
        return $list;
    }



    public function detailed(){
        $where['ap.apply_id']=trim(I('apply_id'));
        $list =M('proxy_recharge_apply ap')
            ->join(C('DB_PREFIX').'proxy as p on p.proxy_id = ap.proxy_id','left')
            ->field('ap.*,p.proxy_name,p.proxy_code')
            ->where($where)
            ->find();
        return $list;
    }


    public function proxy_detailed($where){
        $list =M('proxy_recharge_apply ap')
            ->join(C('DB_PREFIX').'proxy as p on p.proxy_id = ap.proxy_id','left')
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
            $record[0]['device_name']       = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));
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
            $record[1]['device_name']       = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));
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
        //上级代理商的流水
        if($res && $res2){
            if(D('SysUser')->self_user_type()==1){
                $record[0]['operater_before_balance']=0;  //操作前金额，如果是尚通端 则为0
                $record[0]['operater_after_balance']=0; //操作后金额，如果是尚通端 则为0
            }else{
                $record[0]['operater_before_balance']= $condition['top_account_balance'];  //操作前金额，其他按照正常金额显示
                $record[0]['operater_after_balance']=$self_account['account_balance']; //操作后金额，其他按照正常金额显示
            }
            $record[0]['operater_price']        = $condition['apply_money'];  //操作金额
            $record[0]['operate_type']          = $condition['top_operate_type']; //操作类型：退款
            $record[0]['balance_type']          = $condition['top_balance_type'];//收支类型：支出
            $record[0]['record_date']           = date('Y-m-d H:i:s',time());
            $record[0]['user_id']               = D('SysUser')->self_id();  //用户id
            $record[0]['operation_date']        = date('Y-m-d H:i:s',time());
            $record[0]['user_type']             = $condition['operate_user_type'];  //操作者类型：1代理商  2企业
            $record[0]['proxy_id']              = D('SysUser')->self_proxy_id();  //操作者id
            $record[0]['obj_user_type']         = $condition['top_user_type']; //操作对象类型
            $record[0]['obj_proxy_id']          = $condition['operate_proxy_id']; //操作对象id
            $record[0]['device_name']           = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));  //设备号

            /*下级代理商或企业的流水收入*/
            $record[1]['operater_before_balance']   = $condition['operate_account_balance'];  //操作前金额
            $record[1]['operater_after_balance']    = $money['account_balance']; //操作后金额
            $record[1]['operater_price']            = $condition['apply_money'];  //操作金额
            $record[1]['operate_type']              = $condition['operate_operate_type'];  //操作类型：充值
            $record[1]['balance_type']              = $condition['operate_balance_type']; //收支类型：收入
            $record[1]['record_date']               = date('Y-m-d H:i:s',time());
            $record[1]['user_id']                   = D('SysUser')->self_id();  //用户id
            $record[1]['operation_date']            = date('Y-m-d H:i:s',time());
            $record[1]['user_type']                 = $condition['operate_user_type'];   //操作者类型：1代理商  2企业
            $record[1]['proxy_id']                  = $condition['operate_proxy_id']; //操作者id
            $record[1]['obj_user_type']             = $condition['top_user_type'];   //操作对象类型
            $record[1]['obj_proxy_id']              = D('SysUser')->self_proxy_id(); //操作对象id
            $record[1]['device_name']               = get_client_ip2().'+'.iconv("gb2312","utf-8",gethostbyaddr($_SERVER['REMOTE_ADDR']));//设备号
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