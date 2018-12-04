<?php
$prize_arr = array(
	'0' => array('id'=>1,'prize'=>'100M流量','v'=>10),
	'1' => array('id'=>2,'prize'=>'40M流量','v'=>10),
	'2' => array('id'=>3,'prize'=>'30M流量','v'=>10),
	'3' => array('id'=>4,'prize'=>'20M流量','v'=>30),
	'4' => array('id'=>5,'prize'=>'10M流量','v'=>30),
	'5' => array('id'=>6,'prize'=>'下次没准就能中哦','v'=>10),//概率为50%
);


foreach ($prize_arr as $key => $val) {
	$arr[$val['id']] = $val['v'];
}
//print_r($arr);

$rid = getRand($arr); //根据概率获取奖项id
$res['msg'] = ($rid==6)?0:1; 

$res['about'] = 1;
$res['prize'] = $prize_arr[$rid-1]['prize']; //中奖项
echo json_encode($res);exit;


//计算概率
function getRand($proArr) {
	$result = '';

	//概率数组的总概率精度
	$proSum = array_sum($proArr);

	//概率数组循环
	foreach ($proArr as $key => $proCur) {
		$randNum = mt_rand(1, $proSum);
		if ($randNum <= $proCur) {
			$result = $key;
			break;
		} else {
			$proSum -= $proCur;
		}
	}
	unset ($proArr);

	return $result;
}
?>