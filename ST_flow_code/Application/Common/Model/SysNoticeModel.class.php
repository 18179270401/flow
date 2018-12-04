<?php

namespace Common\Model;
use Think\Model;

class SysNoticeModel extends Model{

    /**
     * 获取信息
     */
    public function noticetinfo($notice_id){
        $info = M('Sys_notice')->find($notice_id);
        if($info['status'] == 2){
            return '';
        }else{
            return $info;
        }
    }

    /**
     * 获取某人已读公告ID数组 1D
     */
    public function get_sysnotice_read($user_id) {
        $rid = array();
        $snr = M('sys_notice_read')->where("user_id=".$user_id)->select();
        if(!empty($snr) && is_array($snr)) {
            foreach ($snr as $k => $v) {
                $rid[] = $v['notice_id'];
            }
        }
        return $rid;
    }

    /**
     * 获取某人未读紧急公告 2D
     */
	public function get_sysnotice($user_id, $user_type) {
		$rid = $this->get_sysnotice_read($user_id); //已读

		$cond = array(
				'status'			=> array('eq', 1),
				'notice_type'		=> 1, //紧急公告
				'valid_date_begin'	=> array('elt', date('Y-m-d')),
				'valid_date_end'	=> array('egt', date('Y-m-d')),
				'scope'				=> array('like', "%".$user_type."%"),
		);
		!empty($rid) && $cond['notice_id'] = array('not in', $rid);
		
		$model = M('sys_notice');
		$model->where($cond)->order('create_date asc')->limit(20);
		$ret = $model->select();

		return $ret;
	}

    /**
     * 获取某人一条未读公告 1D
     */
    public function get_sysnotice_one($user_id, $user_type) {
        $rid = $this->get_sysnotice_read($user_id); //已读

        $cond = array(
            'status'			=> array('eq', 1),
            'valid_date_begin'	=> array('elt', date('Y-m-d')),
            'valid_date_end'	=> array('egt', date('Y-m-d')),
            'scope'				=> array('like', "%".$user_type."%"),
        );
        !empty($rid) && $cond['notice_id'] = array('not in', $rid);

        $model = M('sys_notice');
        $model->where($cond)->order('create_date asc,notice_id asc ')->limit(1);
        $ret = $model->find();
        empty($ret) && $ret = array();
        S('noticepage'.$user_id, 1, 86400); //当前页码

        return $ret;
    }

    /**
     * 获取某人一条未读公告
     * @param int $sort 1下一页 2上一页
     * @return array 1D
     */
    public function get_sysnotice_one_sort($user_id, $user_type, $notice_id,$sort=1) {
        $rid = $this->get_sysnotice_read($user_id); //已读
        //$noticepage = S('noticepage'.$user_id); //原页码
        if($sort == 1) { //下一页
            //$startidx = $noticepage;
            $cond['notice_id']=array('gt', $notice_id);
            $order="notice_id asc";
        } else {
            //$startidx = $noticepage-2;
            $cond['notice_id']=array('lt', $notice_id);
            $order="notice_id desc";
        }
        $cond['status']=1;
        $cond['valid_date_begin']=array('elt', date('Y-m-d'));
        $cond['valid_date_end']=array('egt', date('Y-m-d'));
        $cond['scope']=array('like', "%".$user_type."%");
        if(!empty($rid)){
            $map['notice_id'] = array('not in', $rid);
            $map['_logic'] = 'and';
            $cond['_complex'] = $map;
        }

        $model = M('sys_notice');
        $ret = $model->where($cond)->order($order)->find();
        //write_debug_log(array(__METHOD__.'行数:'.__LINE__, 'sql==='.$model->getLastSql()));
        $list=array();
        if(!empty($ret)){
            $list['notice_id']=$ret['notice_id'];
            $list['notice_title']=msubstr($ret['notice_title'],0,20,'utf-8');
            $list['notice_content']=msubstr($ret['notice_content'],0,40,'utf-8');
        }
        return $list;
    }

    /**
     * 获取某人未读公告数量 int
     */
    public function get_sysnotice_sum($user_id, $user_type) {
        $rid = $this->get_sysnotice_read($user_id); //已读

        $cond = array(
            'status'			=> array('eq', 1),
            'valid_date_begin'	=> array('elt', date('Y-m-d')),
            'valid_date_end'	=> array('egt', date('Y-m-d')),
            'scope'				=> array('like', "%".$user_type."%"),
        );
        !empty($rid) && $cond['notice_id'] = array('not in', $rid);

        $model = M('sys_notice');
        $ret = $model->where($cond)->count();

        return $ret;
    }


}


