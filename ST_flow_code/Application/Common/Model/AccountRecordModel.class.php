<?php

namespace Common\Model;
use Think\Model;

class AccountRecordModel extends Model{

    public function index($where){
        $model=M('enterprise  e');
        $list =$model
            ->join('left join t_flow_proxy as up on up.proxy_id = e.top_proxy_id and up.status = 1')
            ->join('left join t_flow_enterprise_account as ea on ea.enterprise_id = e.enterprise_id and e.status = 1')
            ->field('ea.*,e.enterprise_name,e.enterprise_code,up.proxy_name  as top_proxy_name')
            ->where($where)
            ->order('ea.modify_date desc')
            ->select();

    }

    /*充值和提现记录*/
    public function account_record($where){
        $model=M('account_record ar');
        $user_type=D('SysUser')->self_user_type();
        $list='';
        $show='';
        $where['p.status']=array('neq',2);
       // $where['op.status']=1;
       // $where['op.status'] = array('neq',2);
        $where['u.status']=1;
        if($user_type=='1'){
            /*运营端充值明细*/
            $join=array('t_flow_proxy as p on  ar.proxy_id=p.proxy_id',
                        't_flow_proxy as op on ar.obj_proxy_id =op.proxy_id',
                        't_flow_sys_user  as u on u.user_id=ar.user_id');
            $count=$model
                ->join($join)

                ->where($where)
                ->count();
            $Page       = new \Think\Page($count,20);
            $show   	= $Page->show();

            $list=$model
                ->join($join)
                ->field('ar.*,p.proxy_name,p.proxy_code,p.top_proxy_id,op.proxy_code as top_proxy_code ,op.proxy_name  as obj_proxy_name,u.user_name')
                ->where($where)
                ->order('ar.record_id desc')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
        }else if($user_type=='2'){
            /*代理商端充值明细*/
            $join=array('t_flow_proxy as p on  ar.proxy_id=p.proxy_id',
                        't_flow_proxy as up on  ar.proxy_id=up.top_proxy_id',
                        't_flow_proxy  as op on op.proxy_id=ar.obj_proxy_id',
                        't_flow_sys_user  as u on u.user_id=ar.user_id');
            $where['p.top_proxy_id']=D('SysUser')->self_proxy_id();
            $count=$model
                ->join($join)
                ->where($where)
                ->count();
            $Page       = new \Think\Page($count,20);
            $show   	= $Page->show();
            $list=$model
                ->join($join)
                ->field('ar.*,p.proxy_name,p.proxy_code,p.top_proxy_id,op.proxy_name  as obj_proxy_name,u.user_name,up.proxy_name as top_proxy_name')
                ->where($where)
                ->order('ar.record_id desc')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
        }
        return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show);
    }

    public function detailed($where){
        $model=M('account_record ar');
        $list=$model
            ->join('left join t_flow_proxy as p on  ar.proxy_id=p.proxy_id')
            ->join('left join t_flow_proxy as up on  p.top_proxy_id=up.proxy_id ')
            ->join('left join t_flow_proxy  as op on ar.obj_proxy_id=op.proxy_id ')
            ->join('left join t_flow_sys_user  as u on u.user_id=ar.user_id and u.status=1')
            ->field('ar.*,p.proxy_name,p.proxy_code,p.top_proxy_id,op.proxy_code as top_proxy_code ,op.proxy_name  as obj_proxy_name,u.user_name')
            ->where($where)
            ->find();
        return $list;
    }

    public function cashRecord_detailed($where){
        $model=M('account_record ar');
        $list=M('account_record')->where($where)->find();
        return $list;
    }


   /*现金记录*/
    public function  cash_record($where){
        $model=M('account_record ar');
        $user_type=D('SysUser')->self_user_type();
        if($user_type==2){
            /*代理商端现金记录*/
            $join=array('t_flow_proxy as p on  p.proxy_id=ar.proxy_id',
                        't_flow_enterprise as oe on  oe.enterprise_id=ar.obj_enterprise_id and ar.obj_enterprise_id is not null',
                        't_flow_proxy  as op on op.proxy_id = ar.obj_proxy_id and ar.obj_proxy_id is not null');
            /*$sum_results = $model
                ->join($join,'left')->where($where)
                ->field('sum(ar.operater_price) as sum_apply_money')
                ->find();*/
            $count=$model
                ->join($join,'left')
                ->where($where)
                ->count();
            $Page       = new \Think\Page($count,20);
            $show   	= $Page->show();
            $list=$model
                ->join($join,'left')
                ->field('ar.*,p.proxy_name ,p.proxy_code')
                ->where($where)
                ->order('ar.record_id desc')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
        }
            if($user_type==3){
            /*企业端现金记录*/
            $join=array('t_flow_enterprise as e on  e.enterprise_id=ar.enterprise_id');
          /*  $sum_results = $model
                    ->join($join,'left')->where($where)
                    ->field('sum(ar.operater_price) as sum_apply_money')
                    ->find();*/
            $count=$model
                ->join($join)
                ->where($where)
                ->count();
            $Page       = new \Think\Page($count,20);
            $show   	= $Page->show();
            $list=$model
                ->join($join)
                ->field('ar.*,e.enterprise_name,e.enterprise_code')
                ->where($where)
                ->order('ar.record_id desc')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
        }

        return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show/*,'sum_results'=>$sum_results['sum_apply_money']*/);
    }

    /*现金记录导出execl*/
    public function  cash_excel($where){
        $model=M('account_record ar');
        $user_type=D('SysUser')->self_user_type();
        if($user_type==2){
            /*代理商端现金记录*/
            $join=array('t_flow_proxy as p on  p.proxy_id=ar.proxy_id',
                        't_flow_enterprise as oe on  oe.enterprise_id=ar.obj_enterprise_id and ar.obj_enterprise_id is not null',
                        't_flow_proxy  as op on op.proxy_id = ar.obj_proxy_id and ar.obj_proxy_id is not null');
            
            $list=$model
                ->join($join,'left')
                ->field('ar.*,p.proxy_name ,p.proxy_code')
                ->where($where)
                ->order('ar.record_id desc')
                ->limit("0,3000")
                ->select();
        }else  if($user_type==3){
            /*企业端现金记录*/
            $join=array('t_flow_enterprise as e on  e.enterprise_id=ar.enterprise_id');
                        //'t_flow_proxy  as op on op.proxy_id = ar.obj_proxy_id');
            $list=$model
                ->join($join)
                ->field('ar.*,e.enterprise_name,e.enterprise_code')
                ->where($where)
                ->order('ar.record_id desc')
                ->limit("0,3000")
                ->select();
        }

        return $list;
    }


    public function all_proxy(){
        $where['top_proxy_id']=D('SysUser')->self_proxy_id();
        $where['approve_status']=1;
        $res=M('proxy')->where($where)->field('proxy_id,proxy_name')->select();
        return $res;
    }
    public function all_enterprise(){
        $where['top_proxy_id']=D('SysUser')->self_proxy_id();
        $where['approve_status']=1;
        $res=M('enterprise')->where($where)->field('enterprise_id,enterprise_name')->select();
        return $res;
    }
    
    /**
     * 获取某代理商所有返利数据
     * @return array 
     */
    public function rebateinfo($obj_proxy_id, $keyw, $start_datetime, $end_datetime) {
    	
    	$where = array(
    			'ar.operate_type'	=> 6, //分红
    			'ar.user_type'		=> 1,
    			'ar.enterprise_id'	=> 0,
    	);
        (!empty($obj_proxy_id) && is_array($obj_proxy_id)) && $where['ar.proxy_id'] = array('in', $obj_proxy_id);
    	('' != $keyw) && $where['pr.proxy_code|pr.proxy_name|e.enterprise_code|e.enterprise_name'] = array('like', "%{$keyw}%");
    	($start_datetime || $end_datetime) && $where['ar.operation_date'] = array('between',array($start_datetime,$end_datetime));

        if(2 == D('SysUser')->self_user_type()) {
            $child_proxy_ids = D('Proxy')->proxy_child_ids(); //所有管辖的代理商ID
            $child_enterprise_ids = D('Enterprise')->enterprise_ids(); //所有管辖的企业ID

            if(!empty($child_proxy_ids) && !empty($child_enterprise_ids)) {
                $where['_string'] = "ar.obj_proxy_id in ({$child_proxy_ids}) or ar.obj_enterprise_id in ({$child_enterprise_ids})";
            } else if(!empty($child_proxy_ids)) {
                $where['_string'] = "ar.obj_proxy_id in ({$child_proxy_ids}) or ar.obj_enterprise_id in ('-1')";
            } else if(!empty($child_enterprise_ids)) {
                $where['_string'] = "ar.obj_enterprise_id in ({$child_enterprise_ids}) or ar.obj_proxy_id in ('-1')";
            } else {
                $where['_string'] = "ar.obj_proxy_id in ('-1') or ar.obj_enterprise_id in ('-1')";
            }
        }

    	$model = M('account_record ar');
    	
    	$count = $model->join('left join '.C('DB_PREFIX').'order o on ar.order_id = o.order_id')
		    			->join('left join '.C('DB_PREFIX').'proxy pr on ar.obj_proxy_id = pr.proxy_id')
    					->join('left join '.C('DB_PREFIX').'enterprise e on ar.obj_enterprise_id = e.enterprise_id')
    					->join('left join '.C('DB_PREFIX').'sys_province sp on o.province_id = sp.province_id')
    					->join('left join '.C('DB_PREFIX').'channel_product cp on o.channel_product_id = cp.product_id')
		    			->where($where)
		    			->count();

        //返利总额
        $operater_price_sum = $model
            ->join('left join '.C('DB_PREFIX').'proxy pr on ar.obj_proxy_id = pr.proxy_id')
            ->join('left join '.C('DB_PREFIX').'enterprise e on ar.obj_enterprise_id = e.enterprise_id')
            ->where($where)
            ->sum('ar.operater_price');

        //echo "count== {$count}--- sql== ".$model->getLastSql();
    	$Page       = new \Think\Page($count,20);
    	$show   	= $Page->show();
    	
    	$list = $model->join('left join '.C('DB_PREFIX').'order o on ar.order_id = o.order_id')
    					->join('left join '.C('DB_PREFIX').'proxy pr on ar.obj_proxy_id = pr.proxy_id')
    					->join('left join '.C('DB_PREFIX').'enterprise e on ar.obj_enterprise_id = e.enterprise_id')
    					->join('left join '.C('DB_PREFIX').'sys_province sp on o.province_id = sp.province_id')
    					->join('left join '.C('DB_PREFIX').'channel_product cp on o.channel_product_id = cp.product_id')
		    			->where($where)->field('ar.*,o.order_id,o.operator_id,o.province_id,o.price,o.order_code,o.profit_case,cp.size,sp.province_name,pr.proxy_name,e.enterprise_name')
		    			->order('ar.record_id desc')
		    			->limit($Page->firstRow.','.$Page->listRows)
		    			->select();
        //echo "<hr />count== ".count($list).' sql== '.$model->getLastSql();
    	if(!empty($list) && is_array($list)) {
    		foreach ($list as $k => &$v) {
                $v['proxy_id_name'] = D('Proxy')->getproxy_name_byid($v['proxy_id']);
    			$arr_profit_case = json_decode($v['profit_case'], true);
    			foreach ($arr_profit_case as $k2 => $v2) {
    				if(in_array($v2[1], $obj_proxy_id)){
    					$v['self_dc'] = $v2[3];
    					$v['down_dc'] = $v2[4];
    					break;
    				}
    			}
    		}
    		
    	}
    	
    	return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show,'operater_price_sum'=>$operater_price_sum);
    }
    
    public function allRecordList($where){
        $model=M('account_record ar');
        $count=$model
                ->join('left join t_flow_proxy as p on  p.proxy_id=ar.proxy_id')
                ->join('left join '.C('DB_PREFIX').'proxy pr on ar.obj_proxy_id = pr.proxy_id and ar.user_type=1')
                ->join('left join '.C('DB_PREFIX').'enterprise e on ar.obj_enterprise_id = e.enterprise_id and ar.user_type=2')
                ->where($where)
                ->count();

        $Page       = new \Think\Page($count,20);
        $show   	= $Page->show();

        $list=$model
            ->join('left join t_flow_proxy as p on  p.proxy_id=ar.proxy_id')
            ->join('left join '.C('DB_PREFIX').'proxy pr on ar.obj_proxy_id = pr.proxy_id and ar.obj_user_type=1')
            ->join('left join '.C('DB_PREFIX').'enterprise e on ar.obj_enterprise_id = e.enterprise_id and ar.obj_user_type=2')
            ->field('ar.*,p.proxy_name,p.proxy_code')
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('ar.record_id desc')
            ->select();
        return array('list'=>get_sort_no($list,$Page->firstRow),'page'=>$show);
    }

    public function all_record_excel($where){
        $model=M('account_record ar');
        $list=$model
            ->join('left join t_flow_proxy as p on  p.proxy_id=ar.proxy_id')
            ->field('ar.*,p.proxy_name,p.proxy_code')
            ->where($where)
            ->limit(3000)
            ->order('ar.record_id desc')
            ->select();
        return $list;
    }



    public function record_excel($where){
            $model=M('account_record ar');
            $user_type=D('SysUser')->self_user_type();
            $list='';
            $show='';
            $where['p.status']=array('neq',2);
            //$where['op.status']=array('neq',2);
            if($user_type=='1'){
                /*运营端充值明细*/
                $join=array('t_flow_proxy as p on  ar.proxy_id=p.proxy_id',
                    't_flow_proxy  as op on op.proxy_id=ar.obj_proxy_id');
                $list=$model
                    ->join($join)
                    //->field("p.proxy_code,p.proxy_name,op.proxy_code as top_proxy_code ,op.proxy_name  as obj_proxy_name,ar.operater_price,ar.operater_before_balance,ar.operater_after_balance,ar.record_date")
                    ->field("p.proxy_code,p.proxy_name,ar.operater_price,ar.record_date")
                    ->where($where)
                    ->order('ar.record_id desc')
                    ->limit(3000)
                    ->select();
            }else if($user_type=='2'){
                /*代理商端充值明细*/
                $join=array('t_flow_proxy as p on  ar.proxy_id=p.proxy_id',
                    't_flow_proxy as up on  ar.proxy_id=up.top_proxy_id',
                    't_flow_proxy  as op on op.proxy_id=ar.obj_proxy_id');
                $where['p.top_proxy_id']=D('SysUser')->self_proxy_id();
                $list=$model
                    ->join($join)
                    //->field("p.proxy_code,p.proxy_name,op.proxy_code as top_proxy_code ,op.proxy_name  as obj_proxy_name,ar.operate_type,ar.operater_price,ar.operater_before_balance,ar.operater_after_balance,ar.record_date")
                    ->field("p.proxy_code,p.proxy_name,ar.operate_type,ar.operater_price,ar.record_date")
                    ->where($where)
                    ->order('ar.record_id desc')
                    ->limit(3000)
                    ->select();
            }

            return $list;
        }

    /**
     * 导出某代理商所有返利数据
     * @return array
     */
    public function rebate_excel2($obj_proxy_id, $keyw, $start_datetime, $end_datetime) {
        $ret = array();

        $where = array(
            'ar.operate_type'	=> 6, //分红
            'ar.user_type'		=> 1,
            'ar.proxy_id'		=> intval($obj_proxy_id),
            'ar.enterprise_id'	=> 0,
        );
        ('' != $keyw) && $where['pr.proxy_code|pr.proxy_name|e.enterprise_code|e.enterprise_name'] = array('like', "%{$keyw}%");
        ($start_datetime || $end_datetime) && $where['ar.operation_date'] = array('between',array($start_datetime,$end_datetime));

        $child_proxy_ids = D('Proxy')->proxy_child_ids(); //所有管辖的代理商ID
        $child_enterprise_ids = D('Enterprise')->enterprise_ids(); //所有管辖的企业ID
        //dump($child_proxy_ids);echo '<hr />';
        //dump($child_enterprise_ids);exit;

        if(!empty($child_proxy_ids) && !empty($child_enterprise_ids)) {
            $where['_string'] = "ar.obj_proxy_id in ({$child_proxy_ids}) or ar.obj_enterprise_id in ({$child_enterprise_ids})";
        } else if(!empty($child_proxy_ids)) {
            $where['_string'] = "ar.obj_proxy_id in ({$child_proxy_ids}) or ar.obj_enterprise_id in ('-1')";
        } else if(!empty($child_enterprise_ids)) {
            $where['_string'] = "ar.obj_enterprise_id in ({$child_enterprise_ids}) or ar.obj_proxy_id in ('-1')";
        } else {
            $where['_string'] = "ar.obj_proxy_id in ('-1') or ar.obj_enterprise_id in ('-1')";
        }

        $model = M('account_record ar');
        $list = $model->join('left join '.C('DB_PREFIX').'order o on ar.order_id = o.order_id')
            ->join('left join '.C('DB_PREFIX').'proxy pr on ar.obj_proxy_id = pr.proxy_id')
            ->join('left join '.C('DB_PREFIX').'enterprise e on ar.obj_enterprise_id = e.enterprise_id')
            ->join('left join '.C('DB_PREFIX').'sys_province sp on o.province_id = sp.province_id')
            ->join('left join '.C('DB_PREFIX').'channel_product cp on o.channel_product_id = cp.product_id')
            ->where($where)->field('ar.*,o.order_id,o.operator_id,o.province_id,o.price,o.order_code,o.profit_case,cp.size,sp.province_name,pr.proxy_name,e.enterprise_name')
            ->order('ar.record_id desc')
            ->limit(3000)
            ->select();

        if(!empty($list) && is_array($list)) {
            foreach ($list as $k => &$v) {
                $arr_profit_case = json_decode($v['profit_case'], true);
                foreach ($arr_profit_case as $k2 => $v2) {
                    if($obj_proxy_id==$v2[1]){
                        $v['self_dc'] = $v2[3];
                        $v['down_dc'] = $v2[4];
                        break;
                    }
                }

            }
        }

        return $list;
    }

    public function rebate_excel($obj_proxy_id, $keyw, $start_datetime, $end_datetime) {

        $where = array(
            'ar.operate_type'	=> 6, //分红
            'ar.user_type'		=> 1,
            'ar.enterprise_id'	=> 0,
        );
        (!empty($obj_proxy_id) && is_array($obj_proxy_id)) && $where['ar.proxy_id'] = array('in', $obj_proxy_id);
        ('' != $keyw) && $where['pr.proxy_code|pr.proxy_name|e.enterprise_code|e.enterprise_name'] = array('like', "%{$keyw}%");
        ($start_datetime || $end_datetime) && $where['ar.operation_date'] = array('between',array($start_datetime,$end_datetime));

        if(2 == D('SysUser')->self_user_type()) {
            $child_proxy_ids = D('Proxy')->proxy_child_ids(); //所有管辖的代理商ID
            $child_enterprise_ids = D('Enterprise')->enterprise_ids(); //所有管辖的企业ID

            if(!empty($child_proxy_ids) && !empty($child_enterprise_ids)) {
                $where['_string'] = "ar.obj_proxy_id in ({$child_proxy_ids}) or ar.obj_enterprise_id in ({$child_enterprise_ids})";
            } else if(!empty($child_proxy_ids)) {
                $where['_string'] = "ar.obj_proxy_id in ({$child_proxy_ids}) or ar.obj_enterprise_id in ('-1')";
            } else if(!empty($child_enterprise_ids)) {
                $where['_string'] = "ar.obj_enterprise_id in ({$child_enterprise_ids}) or ar.obj_proxy_id in ('-1')";
            } else {
                $where['_string'] = "ar.obj_proxy_id in ('-1') or ar.obj_enterprise_id in ('-1')";
            }
        }

        $model = M('account_record ar');
        $list = $model->join('left join '.C('DB_PREFIX').'order o on ar.order_id = o.order_id')
            ->join('left join '.C('DB_PREFIX').'proxy pr on ar.obj_proxy_id = pr.proxy_id')
            ->join('left join '.C('DB_PREFIX').'enterprise e on ar.obj_enterprise_id = e.enterprise_id')
            ->join('left join '.C('DB_PREFIX').'sys_province sp on o.province_id = sp.province_id')
            ->join('left join '.C('DB_PREFIX').'channel_product cp on o.channel_product_id = cp.product_id')
            ->where($where)->field('ar.*,o.order_id,o.operator_id,o.province_id,o.price,o.order_code,o.profit_case,cp.size,sp.province_name,pr.proxy_name,e.enterprise_name')
            ->order('ar.record_id desc')
            ->limit(3000)
            ->select();
        //echo "<hr />count== ".count($list).' sql== '.$model->getLastSql();
        if(!empty($list) && is_array($list)) {
            foreach ($list as $k => &$v) {
                $v['proxy_id_name'] = D('Proxy')->getproxy_name_byid($v['proxy_id']);
                $arr_profit_case = json_decode($v['profit_case'], true);
                foreach ($arr_profit_case as $k2 => $v2) {
                    if(in_array($v2[1], $obj_proxy_id)){
                        $v['self_dc'] = $v2[3];
                        $v['down_dc'] = $v2[4];
                        break;
                    }
                }
            }

        }
        return $list;
    }




}