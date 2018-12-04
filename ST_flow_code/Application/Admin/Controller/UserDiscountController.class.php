<?php

/*
 * UserController.class.php
 * 用户操作控制器
 *
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class UserDiscountController extends CommonController {

    /*
     * 用户产品折扣列表
     */
    public function index(){
        $user_type = D(SysUser)->self_user_type();
        $user_id = 0;
        if($user_type == 2){
            $user_id = D(SysUser)->self_proxy_id();
        }elseif($user_type == 3){
            $user_id = D(SysUser)->self_enterprise_id();
        }else{
            $this->error('没有权限！');
        }
        $user_type--;

        $table = '(select a.user_type,a.user_id,a.operator_id,a.province_id,a.city_id,min(a.discount_number) as discount_number,
a.operator_name,concat(ifnull(a.province_name,""),ifnull(a.city_name,"")) place,a.product_name,a.product_size,a.base_price
 from(
(
SELECT
	d.user_type,
IF (
	d.proxy_id=0,
	d.enterprise_id,
	d.proxy_id
) user_id,
 d.operator_id,
 d.province_id,
 d.city_id,
 d.discount_number,
 so.operator_name,
 sp.province_name,
 sc.city_name,
 p.product_name,p.size product_size,p.base_price
FROM
	t_flow_discount d
LEFT JOIN t_flow_sys_operator so ON d.operator_id = so.operator_id
LEFT JOIN t_flow_sys_province sp ON d.province_id = sp.province_id
LEFT JOIN t_flow_sys_city sc ON d.city_id = sc.city_id
inner JOIN t_flow_product p on d.operator_id = p.operator_id and p.`status`=1
)
union all
(
SELECT
	dp.user_type,

IF (
	dp.proxy_id=0,
	dp.enterprise_id,
	dp.proxy_id
) user_id,
 dp.operator_id,
 dp.province_id,
 dp.city_id,
 dp.discount_number,
 so2.operator_name,
 sp2.province_name,
 sc2.city_name,
 p2.product_name,
 p2.size product_size,p2.base_price
FROM
	t_flow_discount_product dp
LEFT JOIN t_flow_sys_operator so2 ON dp.operator_id = so2.operator_id
LEFT JOIN t_flow_sys_province sp2 ON dp.province_id = sp2.province_id
LEFT JOIN t_flow_sys_city sc2 ON dp.city_id = sc2.city_id
INNER JOIN t_flow_product p2 ON dp.operator_id = p2.operator_id
AND dp.size = p2.size
AND p2.`status` = 1
)
) a GROUP BY a.user_type,a.user_id,a.operator_id,a.province_id,a.city_id,a.product_size)';


        //调用分页类
        $count = M()->query("select count(b.user_id) as num from $table b where b.user_type=$user_type and b.user_id=$user_id");
        $count = $count[0]['num'];
        $Page       = new Page($count,20);
        $show       = $Page->show();

        $list = M()->query("select b.*,round(b.base_price*b.discount_number,3) discount_price from $table b where b.user_type=$user_type and b.user_id=$user_id
        order by b.operator_id,b.product_size limit $Page->firstRow,$Page->listRows");
        $this->assign('list',get_sort_no($list, $Page->firstRow));
        $this->assign('page',$show);
        $this->display();
    }

    /**
     * 导出excel
     */
    public function export_excel() {
        $user_type = D(SysUser)->self_user_type();
        $user_id = 0;
        if($user_type == 2){
            $user_id = D(SysUser)->self_proxy_id();
        }elseif($user_type == 3){
            $user_id = D(SysUser)->self_enterprise_id();
        }else{
            $this->error('没有权限！');
        }
        $user_type--;

        $table = '(select a.user_type,a.user_id,a.operator_id,a.province_id,a.city_id,min(a.discount_number) as discount_number,
a.operator_name,concat(ifnull(a.province_name,""),ifnull(a.city_name,"")) place,a.product_name,a.product_size,a.base_price
 from(
(
SELECT
	d.user_type,
IF (
	d.proxy_id = 0,
	d.enterprise_id,
	d.proxy_id
) user_id,
 d.operator_id,
 d.province_id,
 d.city_id,
 d.discount_number,
 so.operator_name,
 sp.province_name,
 sc.city_name,
 p.product_name,p.size product_size,p.base_price
FROM
	t_flow_discount d
LEFT JOIN t_flow_sys_operator so ON d.operator_id = so.operator_id
LEFT JOIN t_flow_sys_province sp ON d.province_id = sp.province_id
LEFT JOIN t_flow_sys_city sc ON d.city_id = sc.city_id
inner JOIN t_flow_product p on d.operator_id = p.operator_id and p.`status`=1
)
union all
(
SELECT
	dp.user_type,

IF (
	dp.proxy_id,
	dp.enterprise_id,
	dp.proxy_id
) user_id,
 dp.operator_id,
 dp.province_id,
 dp.city_id,
 dp.discount_number,
 so2.operator_name,
 sp2.province_name,
 sc2.city_name,
 p2.product_name,
 p2.size product_size,p2.base_price
FROM
	t_flow_discount_product dp
LEFT JOIN t_flow_sys_operator so2 ON dp.operator_id = so2.operator_id
LEFT JOIN t_flow_sys_province sp2 ON dp.province_id = sp2.province_id
LEFT JOIN t_flow_sys_city sc2 ON dp.city_id = sc2.city_id
INNER JOIN t_flow_product p2 ON dp.operator_id = p2.operator_id
AND dp.size = p2.size
AND p2.`status` = 1
)
) a GROUP BY a.user_type,a.user_id,a.operator_id,a.province_id,a.city_id,a.product_size)';


        $list_o = M()->query("select b.*,round(b.base_price*b.discount_number,3) discount_price from $table b where b.user_type=$user_type and b.user_id=$user_id
        order by b.operator_id,b.product_size limit 3000");
        $title='订单产品查询';
        $list=array();

        $headArr=array("产品名称","运营商","适用区域","产品大小","产品定价","产品结算价格");
        foreach($list_o as $k=>$v){
            $list[$k]['product_name'] =$v['product_name'];
            $list[$k]['operator_name'] =$v['operator_name'];
            $list[$k]['place'] =$v['place'];
            $list[$k]['product_size'] =$v['product_size'];
            $list[$k]['base_price'] = " ".$v['base_price'];
            $list[$k]['discount_price'] =" ".$v['discount_price'];
        }
        ExportEexcel($title,$headArr,$list);
    }

}