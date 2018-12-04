<?php

namespace Common\Model;
use Think\Model;

class SysLogModel extends Model{

	public function getlog(){
$where['login_date']=date(Y-m-d);

	
$log_info=M('SysLoginLog')->where($where)->find();


var_dump($log_info);

}
}