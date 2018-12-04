<?php
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;

/**
 * 下游一级代理商结算单
 * @package Admin\Controller
 */
class DownStatementsController extends CommonController
{
    public function index(  )
    {

        #--------获取参数
        $Paramters['proxy_code']  =   intval( trim( I('get.proxy_code') ) );
        $Paramters['proxy_name']  =   strval( trim( I('get.proxy_name') ) );

        #------拼装查询数据
        $Field = 'p.proxy_name,p.proxy_code,sp.stat_month,SUM(sp.sale_price) as sale_price';

        #-----拼装查询条件

        $Where = array();
        if($Paramters['proxy_name'] !== '' ) $Where['p.proxy_name']  =   array('like', '%'.$Paramters['proxy_name'].'%' );
        if($Paramters['proxy_code'] > 0)     $Where['p.proxy_code']  =   $Paramters['proxy_code'];

        #------拼装连表条件
        $Join = array(

            'left join '.C('DB_PREFIX').'proxy as p on p.proxy_id = sp.one_proxy_id',
            );

        #拼装分组条件
        $Group = 'sp.one_proxy_id,sp.stat_month';

        #拼装排序条件
        $Order = 'sp.stat_month desc,p.proxy_code asc';

        #统计数据条数
        $count = M('stat_product as sp')->join( $Join )->where( $Where )->count( "DISTINCT $Group" );

        #初始化分页类
        $Page       = new Page( array_keys( $count )[0]  ,20);

        #执行数据库查询
        $StatProductList = M('stat_product as sp')->field( $Field )->where( $Where )->join( $Join )->group( $Group )->order( $Order )->limit($Page->firstRow.','.$Page->listRows)->select();

        #放置View层
        $this->assign('StatProductList', get_sort_no($StatProductList) );
        $this->assign('page', $Page->show() );

        #显示页面
        $this->display();
    }

    public function export_excel(  )
    {

        #------拼装查询数据
        $Field = 'p.proxy_name,p.proxy_code,sp.stat_month,SUM(sp.sale_price) as sale_price';

        #------拼装连表条件
        $Join = array(

            'left join '.C('DB_PREFIX').'proxy as p on p.proxy_id = sp.one_proxy_id',
            );

        #拼装分组条件
        $Group = 'sp.one_proxy_id,sp.stat_month';

        #拼装排序条件
        $Order = 'sp.stat_month desc,p.proxy_code asc';

        #执行数据库查询
        $StatProductList = M('stat_product as sp')->field( $Field )->where( $Where )->join( $Join )->group( $Group )->order( $Order )->select();

        $Title = '顶级代理商账单';
        
        $HeadArr = array(
            '代理商名称',
            '代理商编号',
            '对账月份',
            '金额'
            );

        ExportEexcel($Title,$HeadArr,$StatProductList);


    }
}
?>