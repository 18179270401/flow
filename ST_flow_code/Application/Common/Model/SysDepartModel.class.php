<?php

namespace Common\Model;
use Think\Model;

class SysDepartModel extends Model{

	/*
     * 获取节点详细信息
	 */
	public function departinfo($depart_id){
		$info = M('SysDepart a')
            ->where(array('a.depart_id' => $depart_id))
            ->join('t_flow_sys_user b ON a.depart_manager=b.user_id','left')
            ->field("a.*,b.user_name as depart_manager_name")
            ->find();
		if($info){
            return $info;
		}else{
			return '';
		}
	}

    public function show($where){
        $depart_list = M('Sys_depart a')
            ->where($where)
            ->join('t_flow_sys_depart as b ON a.top_depart_id=b.depart_id',"left")
            ->join('t_flow_sys_user c ON a.depart_manager=c.user_id','left')
            ->field("a.*,b.depart_name as bdepart_name,c.user_name as depart_manager_name")
            ->find();
        return $depart_list;
    }



	/*
	 * 判断是否是自己的管理平台的部门
	 */
	public function is_me_depart($depart_id){

		$map = array();

        $map['depart_id'] = array('eq',$depart_id);
        //所属属于自己的超级管理员的额
        $map['user_id'] = array('eq',D('SysUser')->root_user_id());

        $depart = M('Sys_depart')->where($map)->find();

        if($depart){
        	return true;
        }else{
        	return false;
        }

	}

	/*
	 * 获取等级部门
	 */
	public function topdepart(){
        $root_user_id=D("SysUser")->root_user_id();
        $where['top_depart_id'] = array("eq",0);
        $where['user_id']=$root_user_id;
        $where['status']=array('neq',2);
        $info = M("Sys_depart")->where($where)->select();
        if(!$info){
            return '';
        }
        return $info;
    }

    /*
     *  查询部门是否用户 返回 1.该部门下有子部门，2.该部门有用户，3.可以删除该部门
     */
    public function depart_user($depart_id){
        $map['top_depart_id'] = array('eq',$depart_id);
        $where['depart_id']=array('eq',$depart_id);
        //用来修改用户部门
        $con = M('Sys_depart')->where($map)->count();
        $count=M('Sys_user')->where($where)->count();
        if($con>0){
            return 1;
        }
        if($count>0){
            return 2;
        }
        return 3;
    }
	/*
	 * 逻辑删除部门(旧)
	 */
	public function depart_delete($depart_id){

		$map['top_depart_id'] = array('eq',$depart_id);
        $where['depart_id']=array('eq',$depart_id);
        $count = M('Sys_depart')->where($map)->count();
        M('Sys_user')->where($where)->delete();
        if($count){
            $list = M('Sys_depart')->field('depart_id')->where($map)->select();
            $ids = '';
            foreach($list as $k=>$v){
                $ids .= $v['depart_id'].',';
            }
            $ids = substr($ids,0,-1);
            $map = array();

            $map['top_depart_id'] = array('in',$ids);
            $con=M('Sys_user')->where($map)->count();
            if($con){
                if(M('Sys_user')->where($map)->delete()==$con){
                }else{
                    return false;
                }
            }
            if(M('Sys_depart')->where($map)->delete() == $count){
                return true;
            }else{
                return false;
            }

        }else{
            return true;
        }

	}

    /**
     * 根据user_id 获取所有部门
     */
    public function get_user_depart($user_id) {
        $cond = array(
            'user_id'   => $user_id
        );
        $arr_depart = M('sys_depart')->where($cond)->field('depart_id,depart_name')->select();
        return $arr_depart;
    }


}