<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class StatController extends CommonController{

    /*企业利润汇总 */
    public function company_profit(){
        $company_name = trim(I('get.company_name'));    //用户名称
        $operator_id = trim(I('get.operator_id'));    //运营商
        $start_time = trim(I('get.start_time'));    //开始时间
        $end_time = trim(I('get.end_time'));    //结束时间
        if($start_time!="" && $end_time!=""){
            if(strtotime($end_time) - strtotime($start_time) > 2678400*3){
                $this->display();
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }

        if(empty($start_time) || empty($end_time)){
            if(empty($start_time) && empty($end_time)){
                $start_time = date('Ym01');
                $end_time = date('Ymd');
            }elseif(empty($start_time)){
                $start_time = date('Ym01',strtotime($end_time));
            }else{
                $end_time = date('Ymd',strtotime(date('Ym01',strtotime($start_time)) . ' +1 month -1 day'));
            }
        }
        $start_time = date('Ymd',strtotime($start_time));
        $end_time = date('Ymd',strtotime($end_time));

        //$where = 'a.stat_type = 4 and a.stat_status = 205 and a.top_id=a.company_id and a.top_type=a.company_type';
        $where = '1=1';
        if(!empty($company_name)){
            $where .= " and a.top_name like '%$company_name%'";
        }
        $where .= " and a.stat_day between $start_time and $end_time";
        if(!empty($operator_id)){
            $where .= " and a.operator_id = $operator_id";
        }


        /**
        $table = '(select operator_id,stat_day,stat_type,stat_status,proxy_id as company_id,proxy_code as company_code,proxy_name as company_name,channel_id,channel_code,channel_name,' .
            'sale_discount,top_discount,stat_count,stat_price,discount_price,top_price,profit_price,' .
            '1 as company_type from t_flow_stat_proxy union all ' .
            'select operator_id,stat_day,stat_type,stat_status,enterprise_id as company_id,enterprise_code as company_code,enterprise_name as company_name,channel_id,channel_code,' .
            'channel_name,sale_discount,top_discount,stat_count,stat_price,discount_price,top_price,' .
            'profit_price,2 as company_type from  t_flow_stat_enterprise) as b';

        $table = "( (select bb.*,tp.proxy_code as top_code,tp.proxy_name as top_name from (select b.*,f_get_top_proxy(b.company_id,b.company_type) as top_id,1 as top_type ".
            " from $table) as bb ".
            "left join t_flow_proxy as tp on top_id=tp.proxy_id)".
            " union all (SELECT se.operator_id,se.stat_day,se.stat_type,se.stat_status,".
            "se.enterprise_id as company_id,se.enterprise_code as company_code,se.enterprise_name as company_name,".
            "se.channel_id,se.channel_code,se.channel_name,se.sale_discount,se.top_discount,se.stat_count,".
            "se.stat_price,se.discount_price,se.top_price,se.profit_price,2 as company_type,se.enterprise_id as top_id".
            ",2 as top_type,ze.enterprise_code as top_code,ze.enterprise_name as top_name ".
            " FROM `t_flow_stat_enterprise` se left join t_flow_enterprise ze ".
            "on se.enterprise_id=ze.enterprise_id where ze.enterprise_type=2)) as a";
        **/
        //直营企业ID
        $direct_enterprise_ids = '(select dir_e.enterprise_id from t_flow_enterprise dir_e '.
            'left join t_flow_proxy dir_p on dir_e.top_proxy_id=dir_p.proxy_id '.
            'where dir_p.proxy_type=1 and dir_e.status=1 and dir_e.approve_status=1)';
        //直营代理商ID
        $direct_proxy_ids = '(select proxy_id from t_flow_proxy where proxy_type=1 and status=1 '.
            'and approve_status=1 and proxy_id>1)';

        $direct_top_ent_tab = "(select ws2.top_id,ws2.channel_id,ws2.operator_id,province_id,stat_day,ifnull(sum(stat_size),0) stat_size
                ,ifnull(sum(stat_price),0) stat_price,ifnull(sum(stat_count),0) stat_count from (select ws1.*,
                ws_e.top_proxy_id as top_id 
                from t_flow_stat_enterprise as ws1 left join t_flow_enterprise ws_e on ws1.enterprise_id=ws_e.enterprise_id where ws1.stat_type=3 and ws1.stat_status=205 and ws1.enterprise_id 
                in $direct_enterprise_ids and ws1.stat_day between $start_time and $end_time ) as ws2 group by ws2.top_id,ws2.channel_id,
                ws2.operator_id,ws2.province_id,ws2.stat_day)";

        $where .= ' and a.stat_count>0';
        $table = '(select province_id,province_name,rebate_price,operator_id,stat_day,stat_type,stat_status,proxy_id as company_id,'.
            'proxy_code as company_code,proxy_name as company_name,channel_id,channel_code,channel_name,' .
            'sale_discount,top_discount,stat_count,stat_price,discount_price,top_price,profit_price,' .
            '1 as company_type,proxy_id as top_id,1 as top_type,proxy_code as top_code,'.
            'proxy_name as top_name from t_flow_stat_proxy  where stat_type=4 and stat_status=205 '.
            " and proxy_id not in $direct_proxy_ids and stat_day between  $start_time and $end_time ".
            'union all ' .
            "(SELECT se.province_id,se.province_name,se.rebate_price,se.operator_id,se.stat_day,se.stat_type,se.stat_status,".
            "se.enterprise_id as company_id,se.enterprise_code as company_code,".
            "se.enterprise_name as company_name,".
            "se.channel_id,se.channel_code,se.channel_name,se.sale_discount,se.top_discount,se.stat_count,".
            "se.stat_price,se.discount_price,se.top_price,se.profit_price,2 as company_type,".
            "se.enterprise_id as top_id".
            ",2 as top_type,ze.enterprise_code as top_code,ze.enterprise_name as top_name ".
            " FROM `t_flow_stat_enterprise` se left join t_flow_enterprise ze ".
            "on se.enterprise_id=ze.enterprise_id where ze.enterprise_id in ($direct_enterprise_ids) and se.stat_type=3 ".
            "and se.stat_status=205 and se.stat_day between $start_time and $end_time ) ".
            "union all ".
            'select w1.province_id,w1.province_name,w1.rebate_price,w1.operator_id,w1.stat_day,w1.stat_type,w1.stat_status,w1.proxy_id as company_id,'.
            'w1.proxy_code as company_code,w1.proxy_name as company_name,w1.channel_id,w1.channel_code,w1.channel_name,' .
            'w1.sale_discount,w1.top_discount,'.
            '(w1.stat_count - w2.stat_count) as stat_count,'.
            '(w1.stat_price - w2.stat_price) as stat_price,'.
            '((w1.stat_price - w2.stat_price)*w1.sale_discount) as discount_price,'.
            '((w1.stat_price - w2.stat_price)*w1.top_discount) as top_price,'.
            '(((w1.stat_price - w2.stat_price)*w1.sale_discount)-'.
            '((w1.stat_price - w2.stat_price)*w1.top_discount)) as profit_price,' .
            '1 as company_type,w1.proxy_id as top_id,1 as top_type,w1.proxy_code as top_code,'.
            'w1.proxy_name as top_name from t_flow_stat_proxy as w1  '.
            "left join $direct_top_ent_tab as w2 on w1.proxy_id=w2.top_id and w1.channel_id=w2.channel_id
            and w1.operator_id=w2.operator_id and w1.province_id=w2.province_id and w1.stat_day=w2.stat_day
            where w1.stat_type=4 and w1.stat_status=205 and w1.proxy_id in $direct_proxy_ids and w1.stat_day between $start_time and $end_time group by w1.stat_id".
            ") as a";

        $sql_count = "select a.top_id from $table inner join t_flow_channel cd on a.channel_id=cd.channel_id
        where $where group by a.top_id,a.top_type,a.channel_id";

        $count      = M()->query($sql_count);
        $count = count($count);
        $Page       = new Page($count,20);
        $show       = $Page->show();

        //分页显示所有的数据

        $list_sql = "select a.top_id,a.channel_id,a.top_type as company_type,a.top_code as company_code,a.top_name as company_name,a.channel_code,a.channel_name," .
            "cd.province_id c_province_id,".
            "round(sum(a.stat_count),0) stat_count,round(sum(a.stat_price),3) stat_price,round(sum(a.discount_price),3) discount_price,".
            "round(sum(a.top_price-ifnull(a.rebate_price,0)),3) top_price,round(sum(a.profit_price+ifnull(a.rebate_price,0)),3) profit_price,a.company_type " .
            ",0 as country_p,0 as split_p,0 as province_p "
            ."from $table inner join t_flow_channel cd on a.channel_id=cd.channel_id "
            ." where $where group by a.top_id,a.top_type,a.channel_id ".
            "order by a.discount_price desc limit " .
            $Page->firstRow . ',' . $Page->listRows;

        $list = M()->query($list_sql);

        //获取总的金额开始
        $all_sql = "select round(sum(a.stat_count),0) stat_count,round(sum(a.stat_price),3) stat_price,round(sum(a.discount_price),3) discount_price,".
            "round(sum(a.top_price-ifnull(a.rebate_price,0)),3) top_price,round(sum(a.profit_price+ifnull(a.rebate_price,0)),3) profit_price " .
            " from $table where $where";

        $all_list = M()->query($all_sql);

        $all_stat_price = $all_list[0]['stat_price']; //原价总额
        $all_discount_price = $all_list[0]['discount_price'];//销售总额
        $all_top_price = $all_list[0]['top_price'];//成本总额
        $all_profit_price = $all_list[0]['profit_price'];//利润总额

        $this->assign("all_stat_price",sprintf("%.3f", $all_stat_price));
        $this->assign("all_discount_price",sprintf("%.3f", $all_discount_price));
        $this->assign("all_top_price",sprintf("%.3f", $all_top_price));
        $this->assign("all_profit_price",sprintf("%.3f", $all_profit_price));
        //计算总额结束

        //通道为全国，则为全国，通道为省，用户省，则为省，用户全国，则分流
        $list_sp_sql = "select a.top_id,a.operator_id,a.channel_id,a.top_type as company_type,a.top_code as company_code,a.top_name as company_name,a.channel_code,a.channel_name," .
            "cd.province_id c_province_id,u_province_id,".
            "round(sum(a.stat_count),0) stat_count,round(sum(a.stat_price),3) stat_price,round(sum(a.discount_price),3) discount_price,".
            "round(sum(a.top_price-ifnull(a.rebate_price,0)),3) top_price,round(sum(a.profit_price+ifnull(a.rebate_price,0)),3) profit_price,a.company_type " .
            ",0 as country_p,0 as split_p,0 as province_p "
            ."from $table inner join t_flow_channel cd on a.channel_id=cd.channel_id left join ".
            "(select if(user_type=1,proxy_id,enterprise_id) user_id,user_type,operator_id,".
            "province_id as u_province_id from t_flow_discount where city_id=0 ) as ud ".
            "on a.company_id=ud.user_id and a.company_type=ud.user_type and a.operator_id=ud.operator_id and a.province_id=ud.u_province_id"
            ." where $where group by a.top_id,a.top_type,a.channel_id,a.province_id,a.operator_id ".
            "order by a.discount_price desc ";

        $list_sp = M()->query($list_sp_sql);

        foreach($list as &$v){
            $v['sale_discount'] = '--';
            $v['top_discount'] = '--';
            if(!empty($v['stat_price'])){
                $v['sale_discount'] = sprintf("%.3f", $v['discount_price']/$v['stat_price']*10);

                $v['top_discount'] = sprintf("%.3f", $v['top_price']/$v['stat_price']*10);
            }
            $v['country_p'] = 0;
            $v['province_p'] = 0;
            $v['split_p'] = 0;

            foreach($list_sp as $v2){
                if($v2['top_id'] == $v['top_id']
                    && $v2['company_type'] == $v['company_type']
                    && $v2['channel_id'] == $v['channel_id']
                ){
                    if($v2['c_province_id'] == 1){
                        $v['country_p'] += $v2['profit_price'];
                    }else{
                        if($v2['u_province_id'] == $v2['c_province_id']){
                            $v['province_p'] += $v2['profit_price'];
                        }else{
                            $v['split_p'] += $v2['profit_price'];
                        }
                    }
                }
            }

            $v['country_p'] = sprintf("%.3f", $v['country_p']);
            $v['province_p'] = sprintf("%.3f", $v['province_p']);
            $v['split_p'] = sprintf("%.3f", $v['split_p']);
        }
        //计算三种通道利润开始
        /**
        $all_country_p = 0;
        $all_province_p = 0;
        $all_split_p = 0;
        foreach($all_list as &$v){
            foreach($list_sp as $k2 => $v2){
                if($v2['top_id'] == $v['top_id']
                    && $v2['company_type'] == $v['company_type']
                    && $v2['channel_id'] == $v['channel_id']
                ){
                    if($v2['c_province_id'] == 1){
                        $all_country_p += $v2['profit_price'];
                    }else{
                        if($v2['u_province_id'] == $v2['c_province_id']){
                            $all_province_p += $v2['profit_price'];
                        }else{
                            $all_split_p += $v2['profit_price'];
                        }
                    }
                    unset($list_sp[$k2]);
                }
            }
        }

        $this->assign("all_country_p",$all_country_p);
        $this->assign("all_province_p",$all_province_p);
        $this->assign("all_split_p",$all_split_p);
         **/
        //计算三种通道利润结束


        $this->assign('list',get_sort_no($list,$Page->firstRow));// 赋值数据集  //get_sort_no用序列号
        $this->assign('operator',D("ChannelProduct")->operatorall());
        $this->assign('page',$show);// 赋值分页输出

        $start_time = date('Y-m-d',strtotime($start_time));
        $end_time = date('Y-m-d',strtotime($end_time));
        $this->assign('start_time',$start_time);
        $this->assign('end_time',$end_time);
        $this->display();
    }


    /*企业利润汇总到处excel*/
    public function company_profit_export_excel(){
        $company_name = trim(I('get.company_name'));    //用户名称
        $operator_id = trim(I('get.operator_id'));    //运营商
        $start_time = trim(I('get.start_time'));    //开始时间
        $end_time = trim(I('get.end_time'));    //结束时间
        if($start_time!="" && $end_time!=""){
            if(strtotime($end_time) - strtotime($start_time) > 2678400*3){
                $this->display("company_profit");
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }
        if(empty($start_time) || empty($end_time)){
            if(empty($start_time) && empty($end_time)){
                $start_time = date('Ym01');
                $end_time = date('Ymd');
            }elseif(empty($start_time)){
                $start_time = date('Ym01',strtotime($end_time));
            }else{
                $end_time = date('Ymd',strtotime(date('Ym01',strtotime($start_time)) . ' +1 month -1 day'));
            }
        }
        $start_time = date('Ymd',strtotime($start_time));
        $end_time = date('Ymd',strtotime($end_time));

        //$where = 'a.stat_type = 4 and a.stat_status = 205 and a.top_id=a.company_id and a.top_type=a.company_type';
        $where = '1=1';
        if(!empty($company_name)){
            $where .= " and a.top_name like '%$company_name%'";
        }
        $where .= " and a.stat_day between '$start_time' and '$end_time'";
        if(!empty($operator_id)){
            $where .= " and a.operator_id = $operator_id";
        }


        /**
        $table = '(select operator_id,stat_day,stat_type,stat_status,proxy_id as company_id,proxy_code as company_code,proxy_name as company_name,channel_id,channel_code,channel_name,' .
        'sale_discount,top_discount,stat_count,stat_price,discount_price,top_price,profit_price,' .
        '1 as company_type from t_flow_stat_proxy union all ' .
        'select operator_id,stat_day,stat_type,stat_status,enterprise_id as company_id,enterprise_code as company_code,enterprise_name as company_name,channel_id,channel_code,' .
        'channel_name,sale_discount,top_discount,stat_count,stat_price,discount_price,top_price,' .
        'profit_price,2 as company_type from  t_flow_stat_enterprise) as b';

        $table = "( (select bb.*,tp.proxy_code as top_code,tp.proxy_name as top_name from (select b.*,f_get_top_proxy(b.company_id,b.company_type) as top_id,1 as top_type ".
        " from $table) as bb ".
        "left join t_flow_proxy as tp on top_id=tp.proxy_id)".
        " union all (SELECT se.operator_id,se.stat_day,se.stat_type,se.stat_status,".
        "se.enterprise_id as company_id,se.enterprise_code as company_code,se.enterprise_name as company_name,".
        "se.channel_id,se.channel_code,se.channel_name,se.sale_discount,se.top_discount,se.stat_count,".
        "se.stat_price,se.discount_price,se.top_price,se.profit_price,2 as company_type,se.enterprise_id as top_id".
        ",2 as top_type,ze.enterprise_code as top_code,ze.enterprise_name as top_name ".
        " FROM `t_flow_stat_enterprise` se left join t_flow_enterprise ze ".
        "on se.enterprise_id=ze.enterprise_id where ze.enterprise_type=2)) as a";
         **/
        //直营企业ID
        $direct_enterprise_ids = '(select dir_e.enterprise_id from t_flow_enterprise dir_e '.
            'left join t_flow_proxy dir_p on dir_e.top_proxy_id=dir_p.proxy_id '.
            'where dir_p.proxy_type=1 and dir_e.status=1 and dir_e.approve_status=1)';
        //直营代理商ID
        $direct_proxy_ids = '(select proxy_id from t_flow_proxy where proxy_type=1 and status=1 '.
            'and approve_status=1 and proxy_id>1)';

        $direct_top_ent_tab = "(select ws2.top_id,ws2.channel_id,ws2.operator_id,province_id,stat_day,ifnull(sum(stat_size),0) stat_size
                ,ifnull(sum(stat_price),0) stat_price,ifnull(sum(stat_count),0) stat_count from (select ws1.*,
                ws_e.top_proxy_id as top_id 
                from t_flow_stat_enterprise as ws1 left join t_flow_enterprise ws_e on ws1.enterprise_id=ws_e.enterprise_id where ws1.stat_type=3 and ws1.stat_status=205 and ws1.enterprise_id 
                in $direct_enterprise_ids and ws1.stat_day between $start_time and $end_time ) as ws2 group by ws2.top_id,ws2.channel_id,
                ws2.operator_id,ws2.province_id,ws2.stat_day)";

        $where .= ' and a.stat_count>0';
        $table = '(select province_id,province_name,rebate_price,operator_id,stat_day,stat_type,stat_status,proxy_id as company_id,'.
            'proxy_code as company_code,proxy_name as company_name,channel_id,channel_code,channel_name,' .
            'sale_discount,top_discount,stat_count,stat_price,discount_price,top_price,profit_price,' .
            '1 as company_type,proxy_id as top_id,1 as top_type,proxy_code as top_code,'.
            'proxy_name as top_name from t_flow_stat_proxy  where stat_type=4 and stat_status=205 '.
            " and proxy_id not in $direct_proxy_ids and stat_day between  $start_time and $end_time ".
            'union all ' .
            "(SELECT se.province_id,se.province_name,se.rebate_price,se.operator_id,se.stat_day,se.stat_type,se.stat_status,".
            "se.enterprise_id as company_id,se.enterprise_code as company_code,".
            "se.enterprise_name as company_name,".
            "se.channel_id,se.channel_code,se.channel_name,se.sale_discount,se.top_discount,se.stat_count,".
            "se.stat_price,se.discount_price,se.top_price,se.profit_price,2 as company_type,".
            "se.enterprise_id as top_id".
            ",2 as top_type,ze.enterprise_code as top_code,ze.enterprise_name as top_name ".
            " FROM `t_flow_stat_enterprise` se left join t_flow_enterprise ze ".
            "on se.enterprise_id=ze.enterprise_id where ze.enterprise_id in ($direct_enterprise_ids) and se.stat_type=3 ".
            "and se.stat_status=205 and se.stat_day between $start_time and $end_time ) ".
            "union all ".
            'select w1.province_id,w1.province_name,w1.rebate_price,w1.operator_id,w1.stat_day,w1.stat_type,w1.stat_status,w1.proxy_id as company_id,'.
            'w1.proxy_code as company_code,w1.proxy_name as company_name,w1.channel_id,w1.channel_code,w1.channel_name,' .
            'w1.sale_discount,w1.top_discount,'.
            '(w1.stat_count - w2.stat_count) as stat_count,'.
            '(w1.stat_price - w2.stat_price) as stat_price,'.
            '((w1.stat_price - w2.stat_price)*w1.sale_discount) as discount_price,'.
            '((w1.stat_price - w2.stat_price)*w1.top_discount) as top_price,'.
            '(((w1.stat_price - w2.stat_price)*w1.sale_discount)-'.
            '((w1.stat_price - w2.stat_price)*w1.top_discount)) as profit_price,' .
            '1 as company_type,w1.proxy_id as top_id,1 as top_type,w1.proxy_code as top_code,'.
            'w1.proxy_name as top_name from t_flow_stat_proxy as w1  '.
            "left join $direct_top_ent_tab as w2 on w1.proxy_id=w2.top_id and w1.channel_id=w2.channel_id
            and w1.operator_id=w2.operator_id and w1.province_id=w2.province_id and w1.stat_day=w2.stat_day
            where w1.stat_type=4 and w1.stat_status=205 and w1.proxy_id in $direct_proxy_ids and w1.stat_day between $start_time and $end_time group by w1.stat_id".
            ") as a";

        //分页显示所有的数据

        $list_sql = "select a.top_id,a.channel_id,a.top_type as company_type,a.top_code as company_code,a.top_name as company_name,a.channel_code,a.channel_name," .
            "cd.province_id c_province_id,".
            "round(sum(a.stat_count),0) stat_count,round(sum(a.stat_price),3) stat_price,round(sum(a.discount_price),3) discount_price,".
            "round(sum(a.top_price-ifnull(a.rebate_price,0)),3) top_price,round(sum(a.profit_price+ifnull(a.rebate_price,0)),3) profit_price,a.company_type " .
            ",0 as country_p,0 as split_p,0 as province_p "
            ."from $table inner join t_flow_channel cd on a.channel_id=cd.channel_id "
            ." where $where group by a.top_id,a.top_type,a.channel_id ".
            "order by a.discount_price desc limit 3000";

        $list = M()->query($list_sql);

        //通道为全国，则为全国，通道为省，用户省，则为省，用户全国，则分流
        $list_sp_sql = "select a.top_id,a.operator_id,a.channel_id,a.top_type as company_type,a.top_code as company_code,a.top_name as company_name,a.channel_code,a.channel_name," .
            "cd.province_id c_province_id,u_province_id,".
            "round(sum(a.stat_count),0) stat_count,round(sum(a.stat_price),3) stat_price,round(sum(a.discount_price),3) discount_price,".
            "round(sum(a.top_price-ifnull(a.rebate_price,0)),3) top_price,round(sum(a.profit_price+ifnull(a.rebate_price,0)),3) profit_price,a.company_type " .
            ",0 as country_p,0 as split_p,0 as province_p "
            ."from $table inner join t_flow_channel cd on a.channel_id=cd.channel_id left join ".
            "(select if(user_type=1,proxy_id,enterprise_id) user_id,user_type,operator_id,".
            "province_id as u_province_id from t_flow_discount where city_id=0 ) as ud ".
            "on a.company_id=ud.user_id and a.company_type=ud.user_type and a.operator_id=ud.operator_id and a.province_id=ud.u_province_id"
            ." where $where group by a.top_id,a.top_type,a.channel_id,a.province_id,a.operator_id ".
            "order by a.discount_price desc ";

        $list_sp = M()->query($list_sp_sql);

        foreach($list as &$v){
            $v['sale_discount'] = '--';
            $v['top_discount'] = '--';
            if(!empty($v['stat_price'])){
                $v['sale_discount'] = sprintf("%.3f", $v['discount_price']/$v['stat_price']*10);

                $v['top_discount'] = sprintf("%.3f", $v['top_price']/$v['stat_price']*10);
            }
            $v['country_p'] = 0;
            $v['province_p'] = 0;
            $v['split_p'] = 0;

            foreach($list_sp as $v2){
                if($v2['top_id'] == $v['top_id']
                    && $v2['company_type'] == $v['company_type']
                    && $v2['channel_id'] == $v['channel_id']
                ){
                    if($v2['c_province_id'] == 1){
                        $v['country_p'] += $v2['profit_price'];
                    }else{
                        if($v2['u_province_id'] == $v2['c_province_id']){
                            $v['province_p'] += $v2['profit_price'];
                        }else{
                            $v['split_p'] += $v2['profit_price'];
                        }
                    }
                }
            }

            $v['country_p'] = sprintf("%.3f", $v['country_p']);
            $v['province_p'] = sprintf("%.3f", $v['province_p']);
            $v['split_p'] = sprintf("%.3f", $v['split_p']);
        }

        $datas = array();
        $headArr=array("用户名称","通道编码","通道名称","销售折扣(折)","成本折扣(折)","订单总数","原价总额(元)",
            "销售总额(元)","成本总额(元)"
        //,"利润总额(元)"
        ,"全国通道利润","分流通道利润","省网通道利润","利润汇总");
        foreach ($list as $vv) {
            $data=array();
            $data['company_name'] = $vv['company_name'];
            $data['channel_code'] = $vv['channel_code'];
            $data['channel_name'] = $vv['channel_name'];
            $data['sale_discount'] = $vv['sale_discount'];
            $data['top_discount'] = $vv['top_discount'];
            $data['stat_count'] = $vv['stat_count'];
            $data['stat_price'] = $vv['stat_price'];
            $data['discount_price'] = $vv['discount_price'];
            $data['top_price'] = $vv['top_price'];
            //$data['profit_price'] = $vv['profit_price'];
            $data['country_p'] = $vv['country_p'];
            $data['split_p'] = $vv['split_p'];
            $data['province_p'] = $vv['province_p'];
            $data['profit_price2'] = $vv['profit_price'];
            array_push($datas,$data);
        }

        $title='企业利润汇总表';
        ExportEexcel($title,$headArr,$datas);

    }

    public function down_account_info_proxy(){
        $this->assign('url_excel','/index.php/Admin/Stat/down_account_info_proxy');
        $this->assign('url_excel','Stat/down_account_info_proxy_export_excel');
        $this->down_account_info(1);    //user_type:1代理商、2企业
    }

    public function down_account_info_enterprise(){
        $this->assign('url','/index.php/Admin/Stat/down_account_info_enterprise');
        $this->assign('url_excel','Stat/down_account_info_enterprise_export_excel');
        $this->down_account_info(2);    //user_type:1代理商、2企业
    }

    public function down_account_info($user_type=0){
        $top_proxy_name = trim(I('get.top_proxy_name'));    //顶级代理商名称
        $user_name = trim(I('get.user_name'));    //下级客户名称
        $start_time = trim(I('get.start_time'));    //开始时间
        $end_time = trim(I('get.end_time'));    //结束时间
        if(empty($user_type)){
            $user_type = trim(I('get.user_type'));
        }

        if($start_time!="" && $end_time!=""){
            if(strtotime($end_time) - strtotime($start_time) > 2678400*3){
                $this->display("down_account_info");
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }
        if(empty($start_time) || empty($end_time)){
            if(empty($start_time) && empty($end_time)){
                $start_time = date('Ym01');
                $end_time = date('Ymd');
            }elseif(empty($start_time)){
                $start_time = date('Ym01',strtotime($end_time));
            }else{
                $end_time = date('Ymd',strtotime(date('Ym01',strtotime($start_time)) . ' +1 month -1 day'));
            }
        }
        $start_time = date('Ymd',strtotime($start_time));
        $end_time = date('Ymd',strtotime($end_time));

        $where = 'stat_status = 205 and stat_type='.$user_type;

        $where .= " and stat_day between '$start_time' and '$end_time'";
        if(!empty($user_name)){
            $where .= " and user_name like '%$user_name%'";
        }
        $operator_id = trim(I('get.operator_id'));  //获取运营商
        if(!empty($operator_id)) {
            $where .=" and operator_id = $operator_id";
        }

        if(!empty($top_proxy_name)){
            //获取所有1级代理商
            $cond = array(
                'proxy_level' => array('eq', 1), //一级代理商
                'approve_status' => array('eq', 1), //1：审核通过
                'status' => array('eq', 1), //1：正常
                'proxy_name' => array('like',"%$top_proxy_name%")
            );
            $top_proxy_list = $rt = M('proxy')->where($cond)->field('proxy_id,proxy_name')->select();
            //获取代理商下的所以子代理商和企业
            $all_child_pid[0] = -1;
            $all_child_eid[0] = -1;

            foreach($top_proxy_list as $tv){
                $tp_id = $tv['proxy_id'];

                //获取所有代理商
                $tmp_cid = D('Proxy')->get_proxy_child_list($tp_id);
                $tmp_cid[] = $tp_id;

                if(!empty($tmp_cid)){
                    $all_child_pid = array_merge($all_child_pid,$tmp_cid);
                }


                //获取所有企业
                $map['status'] = array('neq',2);
                $map['top_proxy_id'] = array('in',$tmp_cid);
                $map['approve_status']=1;
                $tme_id = M('Enterprise')->field('enterprise_id')->where($map)->select();
                $tme_id = get_array_column($tme_id, 'enterprise_id');

                if(!empty($tme_id)){
                    $all_child_eid = array_merge($all_child_eid,$tme_id);
                }

            }
            if($user_type == 1){
                $all_child_pid = implode(',', $all_child_pid);
                $where .= " and ((stat_type=1 and user_id in ($all_child_pid)) )";
            }else{
                $all_child_eid = implode(',', $all_child_eid);
                $where .= " and ( (stat_type=2 and user_id in ($all_child_eid)))";
            }




        }

        $count = M('stat_product')
            ->where($where)
            ->field("user_id,stat_type")
            ->group("stat_type,user_id,product_name")
            ->select();

        $count = count($count);
        $Page       = new Page($count,20);
        $show       = $Page->show();

        $direct_enterprise_ids = '(select dir_e.enterprise_id from t_flow_enterprise dir_e '.
            'left join t_flow_proxy dir_p on dir_e.top_proxy_id=dir_p.proxy_id '.
            'where dir_p.proxy_type=1 and dir_e.status=1 and dir_e.approve_status=1)';

        $table = "((select user_id,stat_type,user_name,product_name,sum(stat_count) as stat_count,".
            "one_proxy_id as top_proxy_id,one_proxy_name as top_proxy_name,sum(stat_price) as stat_price,".
            "sum(discount_price) as discount_price,round(sum(discount_price - stat_price * top_discount+ifnull(rebate_price,0)),3) as profit_price from t_flow_stat_product sp ".
            "where $where and (stat_type=2 and user_id in $direct_enterprise_ids ) GROUP BY stat_type,user_id,product_name) union all " .
            "(select user_id,stat_type,user_name,product_name,sum(stat_count) as stat_count,".
            "one_proxy_id as top_proxy_id,one_proxy_name as top_proxy_name,sum(stat_price) as stat_price,".
            "round(sum(stat_price * sale_discount),3) as discount_price,round(sum(stat_price *(sale_discount-top_discount)+ifnull(rebate_price,0)),3) as profit_price from t_flow_stat_product sp ".
            "where $where and (stat_type=1  or (stat_type=2 and sp.user_id not in $direct_enterprise_ids) ) GROUP BY stat_type,user_id,product_name)) as a ";

        $list_sql = "select a.user_id,a.stat_type,a.user_name,a.product_name,a.stat_count,a.stat_price,ROUND(a.profit_price,3) as profit_price,".
            "a.discount_price,a.top_proxy_name  from ".
            "$table ".
            " order by a.stat_type,a.user_id limit " .
            $Page->firstRow . ',' . $Page->listRows;

        $list = M()->query($list_sql);
        //计算下游对账信息总额开始
        $list_sql = "select a.user_id,a.stat_type,a.user_name,a.product_name,a.stat_count,a.stat_price,ROUND(a.profit_price,3) as profit_price,".
            "a.discount_price,a.top_proxy_name from ".
            "$table ".
            "order by a.user_id ";

        $all_list = M()->query($list_sql);
        $all_stat_price=0;//原价总额
        $all_discount_price=0;//折后价
        $all_stat_count=0;//订单总数
        $all_profit_price=0;//利润
        foreach($all_list as $al){
            $all_stat_price+=$al['stat_price'];
            $all_discount_price+=$al['discount_price'];
            $all_stat_count+=$al['stat_count'];
            $all_profit_price+=$al['profit_price'];
        }
        $this->assign('all_stat_price',sprintf('%.3f',$all_stat_price));
        $this->assign('all_discount_price',sprintf('%.3f',$all_discount_price));
        $this->assign('all_profit_price',sprintf('%.3f',$all_profit_price));
        $this->assign('all_stat_count',$all_stat_count);
        //计算下游对账信息总额结束

        $this->assign('list',get_sort_no($list,$Page->firstRow));// 赋值数据集  //get_sort_no用序列号
        $this->assign('page',$show);// 赋值分页输出

        $start_time = date('Y-m-d',strtotime($start_time));
        $end_time = date('Y-m-d',strtotime($end_time));
        $operator = D("ChannelProduct")->operatorall();//读取运营商
        $this->assign("operator",$operator);
        $this->assign('start_time',$start_time);
        $this->assign('end_time',$end_time);
        $this->assign('user_type',$user_type);
        if($user_type==1){
            $this->display('down_account_info1');
        }else{
            $this->display('down_account_info');
        }
    }


    public function down_account_info_proxy_export_excel(){
        $this->down_account_info_export_excel();
    }

    public function down_account_info_enterprise_export_excel(){
        $this->down_account_info_export_excel();
    }



    /*下游对账信息表导出excel*/
    public function down_account_info_export_excel(){
        $top_proxy_name = trim(I('get.top_proxy_name'));    //顶级代理商名称
        $user_name = trim(I('get.user_name'));    //下级客户名称
        $start_time = trim(I('get.start_time'));    //开始时间
        $end_time = trim(I('get.end_time'));    //结束时间
        $user_type = trim(I('get.user_type'));

        if($start_time!="" && $end_time!=""){
            if(strtotime($end_time) - strtotime($start_time) > 2678400*3){
                $this->display("down_account_info");
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }
        if(empty($start_time) || empty($end_time)){
            if(empty($start_time) && empty($end_time)){
                $start_time = date('Ym01');
                $end_time = date('Ymd');
            }elseif(empty($start_time)){
                $start_time = date('Ym01',strtotime($end_time));
            }else{
                $end_time = date('Ymd',strtotime(date('Ym01',strtotime($start_time)) . ' +1 month -1 day'));
            }
        }
        $start_time = date('Ymd',strtotime($start_time));
        $end_time = date('Ymd',strtotime($end_time));

        $where = 'stat_status = 205 and stat_type='.$user_type;

        $where .= " and stat_day between '$start_time' and '$end_time'";
        if(!empty($user_name)){
            $where .= " and user_name like '%$user_name%'";
        }
        $operator_id = trim(I('get.operator_id'));  //获取运营商
        if(!empty($operator_id)) {
            $where .=" and operator_id = $operator_id";
        }

        if(!empty($top_proxy_name)){
            //获取所有1级代理商
            $cond = array(
                'proxy_level' => array('eq', 1), //一级代理商
                'approve_status' => array('eq', 1), //1：审核通过
                'status' => array('eq', 1), //1：正常
                'proxy_name' => array('like',"%$top_proxy_name%")
            );
            $top_proxy_list = $rt = M('proxy')->where($cond)->field('proxy_id,proxy_name')->select();
            //获取代理商下的所以子代理商和企业
            $all_child_pid[0] = -1;
            $all_child_eid[0] = -1;

            foreach($top_proxy_list as $tv){
                $tp_id = $tv['proxy_id'];

                //获取所有代理商
                $tmp_cid = D('Proxy')->get_proxy_child_list($tp_id);
                $tmp_cid[] = $tp_id;

                if(!empty($tmp_cid)){
                    $all_child_pid = array_merge($all_child_pid,$tmp_cid);
                }


                //获取所有企业
                $map['status'] = array('neq',2);
                $map['top_proxy_id'] = array('in',$tmp_cid);
                $map['approve_status']=1;
                $tme_id = M('Enterprise')->field('enterprise_id')->where($map)->select();
                $tme_id = get_array_column($tme_id, 'enterprise_id');

                if(!empty($tme_id)){
                    $all_child_eid = array_merge($all_child_eid,$tme_id);
                }

            }
            if($user_type == 1){
                $all_child_pid = implode(',', $all_child_pid);
                $where .= " and ((stat_type=1 and user_id in ($all_child_pid)) )";
            }else{
                $all_child_eid = implode(',', $all_child_eid);
                $where .= " and ( (stat_type=2 and user_id in ($all_child_eid)))";
            }


        }

        $direct_enterprise_ids = '(select dir_e.enterprise_id from t_flow_enterprise dir_e '.
            'left join t_flow_proxy dir_p on dir_e.top_proxy_id=dir_p.proxy_id '.
            'where dir_p.proxy_type=1 and dir_e.status=1 and dir_e.approve_status=1)';

        $table = "((select user_id,stat_type,user_name,product_name,sum(stat_count) as stat_count,".
            "one_proxy_id as top_proxy_id,one_proxy_name as top_proxy_name,sum(stat_price) as stat_price,".
            "sum(discount_price) as discount_price,round(sum(discount_price - stat_price * top_discount+ifnull(rebate_price,0)),3) as profit_price from t_flow_stat_product sp ".
            "where $where and (stat_type=2 and user_id in $direct_enterprise_ids ) GROUP BY stat_type,user_id,product_name) union all " .
            "(select user_id,stat_type,user_name,product_name,sum(stat_count) as stat_count,".
            "one_proxy_id as top_proxy_id,one_proxy_name as top_proxy_name,sum(stat_price) as stat_price,".
            "round(sum(stat_price * sale_discount),3) as discount_price,round(sum(stat_price *(sale_discount-top_discount)+ifnull(rebate_price,0)),3) as profit_price from t_flow_stat_product sp ".
            "where $where and (stat_type=1  or (stat_type=2 and sp.user_id not in $direct_enterprise_ids) ) GROUP BY stat_type,user_id,product_name)) as a ";

        $list_sql = "select a.user_id,a.stat_type,a.user_name,a.product_name,a.stat_count,a.stat_price,ROUND(a.profit_price,3) as profit_price,".
            "a.discount_price,a.top_proxy_name  from ".
            "$table ".
            " order by a.stat_type,a.user_id limit 3000 ";

        $list = M()->query($list_sql);

        $datas = array();
        if($user_type==1){
            $headArr=array("代理商名称","产品规格","订单总数","原价总额","销售金额","利润金额");
        }else{
            $headArr=array("代理商名称","企业名称","产品规格","订单总数","原价总额","销售金额","利润金额");
        }
        foreach ($list as $v) {
            $data=array();
            if($user_type==2){
                $data['top_proxy_name'] = $v['top_proxy_name'];
            }
            $data['user_name'] = $v['user_name'];
            $data['product_name'] = $v['product_name'];
            $data['stat_count'] = $v['stat_count'];
            $data['stat_price'] = $v['stat_price'];
            $data['discount_price'] = $v['discount_price'];
            $data['profit_price']=$v['profit_price'];
            array_push($datas,$data);
        }

        $title='下游对账信息表';
        ExportEexcel($title,$headArr,$datas);

    }



     /**
     * 上游对账信息表
     */
    public function up_account_info(){

        $channel_name = trim(I('get.channel_name'));    //通道名称
        $channel_code = trim(I('get.channel_code'));    //通道编码
        $start_time = trim(I('get.start_time'));    //开始时间
        $end_time = trim(I('get.end_time'));    //结束时间
        if($start_time!="" && $end_time!=""){
            if(strtotime($end_time) - strtotime($start_time) > 2678400*3){
                $this->display();
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }
        if(empty($start_time) || empty($end_time)){
            if(empty($start_time) && empty($end_time)){
                $start_time = date('Ym01');
                $end_time = date('Ymd');
            }elseif(empty($start_time)){
                $start_time = date('Ym01',strtotime($end_time));
            }else{
                $end_time = date('Ymd',strtotime(date('Ym01',strtotime($start_time)) . ' +1 month -1 day'));
            }
        }
        $start_time = date('Ymd',strtotime($start_time));
        $end_time = date('Ymd',strtotime($end_time));

        $where = 'a.stat_status = 205';

        $where .= " and a.stat_day between '$start_time' and '$end_time'";

        if(!empty($channel_name)){
            $where .= " and a.channel_name like '%$channel_name%'";
        }
        if(!empty($channel_code)){
            $where .= " and a.channel_code like '%$channel_code%'";
        }
        
        $model = M('stat_product a');
        $count = $model
            ->join(C('DB_PREFIX')."channel as c on c.channel_id=a.channel_id","inner")
            ->join(C('DB_PREFIX')."channel_account as ca on c.account_id=ca.account_id","left")
            ->field('c.channel_code,c.channel_name,a.product_name')
            ->where($where)->group('a.product_name,a.channel_id')->select();

        $count = count($count);
        $Page       = new Page($count,20);
        $show       = $Page->show();

        $list = $model
            ->join(C('DB_PREFIX')."channel as c on c.channel_id=a.channel_id","inner")
            ->field('c.channel_code,c.channel_name,a.product_name,'.
                'sum(a.stat_count) as stat_count,sum(a.stat_price) as stat_price,'.
                'sum(a.top_price-a.rebate_price) as top_price,sum(a.discount_price) discount_price')
            ->where($where)->group('a.product_name,a.channel_id')
            ->order('a.channel_id')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

        //上游对账信息表计算总额开始
        $all_list= $model
            ->join(C('DB_PREFIX')."channel as c on c.channel_id=a.channel_id","inner")
            ->field('c.channel_code,c.channel_name,a.product_name,'.
                'sum(a.stat_count) as stat_count,sum(a.stat_price) as stat_price,'.
                'sum(a.discount_price) as discount_price,sum(a.top_price-a.rebate_price) as top_price')
            ->where($where)->group('a.product_name,a.channel_id')
            ->select();

        $all_stat_price=0;
        $all_top_price=0;
        $all_stat_count=0;
        $all_discount_price=0;
        $all_product_size=0;
        foreach ($all_list as $vo){
            $all_stat_price+=$vo['stat_price'];
            $all_top_price+=$vo['top_price'];
            $all_stat_count+=$vo['stat_count'];
            $all_discount_price += $vo['discount_price'];
            if(strstr($vo['product_name'], 'G') || strstr($vo['product_name'], 'g')){
                $vo['product_size'] = $vo['stat_count'] * trim($vo['product_name'],'G') * 1024;
            }else if(strstr($vo['product_name'], 'g')){
                $vo['product_size'] = $vo['stat_count'] * trim($vo['product_name'],'g') * 1024;
            }else{
                $vo['product_size'] = $vo['stat_count'] * trim($vo['product_name'],'M');
            }
            $all_product_size+=$vo['product_size'];
        }
        $all_stat_price=empty($all_stat_price)?0:sprintf('%.3f',$all_stat_price);//成本总额
        $all_top_price=empty($all_top_price)?0:sprintf('%.3f',$all_top_price);//原价总额
        $all_discount_price=empty($all_discount_price)?0:sprintf('%.3f',$all_discount_price);//销售总额
        $this->assign("all_stat_price",$all_stat_price);
        $this->assign("all_top_price",$all_top_price);
        $this->assign("all_stat_count",$all_stat_count);
        $this->assign("all_product_size",$all_product_size);
        $this->assign("all_discount_price",$all_discount_price);
        //上游对账信息表计算总额结束

        foreach($list as &$v){
            if(empty($v['top_price']) || $v['top_price'] == 0){
                $v['discount'] = '--';
            }else{
                $v['discount'] = sprintf('%.3f',$v['top_price']/$v['stat_price']*10);
            }
            if(strstr($v['product_name'], 'G') || strstr($v['product_name'], 'g')){
                $v['product_size'] = $v['stat_count'] * trim($v['product_name'],'G') * 1024;
            }else if(strstr($v['product_name'], 'g')){
                $v['product_size'] = $v['stat_count'] * trim($v['product_name'],'g') * 1024;
            }else{
                $v['product_size'] = $v['stat_count'] * trim($v['product_name'],'M');
            }

        }

        $this->assign('list',get_sort_no($list,$Page->firstRow));// 赋值数据集  //get_sort_no用序列号
        $this->assign('page',$show);// 赋值分页输出

        $start_time = date('Y-m-d',strtotime($start_time));
        $end_time = date('Y-m-d',strtotime($end_time));
        $this->assign('start_time',$start_time);
        $this->assign('end_time',$end_time);
        $this->display();
    }



       /*上游对账信息表导出excel*/
    public function up_account_info_export_excel(){
        $channel_name = trim(I('get.channel_name'));    //通道名称
        $channel_code = trim(I('get.channel_code'));    //通道编码
        $start_time = trim(I('get.start_time'));    //开始时间
        $end_time = trim(I('get.end_time'));    //结束时间
        if($start_time!="" && $end_time!=""){
            if(strtotime($end_time) - strtotime($start_time) > 2678400*3){
                $this->display("up_account_info");
                echo "<script>alertbox({'status':'error','msg':'查询时间间隔请勿超过93天！'});history.back(); </script>";exit;
            }
        }
        if(empty($start_time) || empty($end_time)){
            if(empty($start_time) && empty($end_time)){
                $start_time = date('Ym01');
                $end_time = date('Ymd');
            }elseif(empty($start_time)){
                $start_time = date('Ym01',strtotime($end_time));
            }else{
                $end_time = date('Ymd',strtotime(date('Ym01',strtotime($start_time)) . ' +1 month -1 day'));
            }
        }
        $start_time = date('Ymd',strtotime($start_time));
        $end_time = date('Ymd',strtotime($end_time));

        $where = 'a.stat_status = 205';

        $where .= " and a.stat_day between '$start_time' and '$end_time'";
        if(!empty($account_name)){
            $where .= " and ca.account_name like '%$account_name%'";
        }
        if(!empty($channel_name)){
            $where .= " and a.channel_name like '%$channel_name%'";
        }
        if(!empty($channel_code)){
            $where .= " and a.channel_code like '%$channel_code%'";
        }

        $model = M('stat_product a');

        $list = $model
            ->join(C('DB_PREFIX')."channel as c on c.channel_id=a.channel_id","inner")
            ->field('c.channel_code,c.channel_name,a.product_name,'.
                'sum(a.stat_count) as stat_count,sum(a.stat_price) as stat_price,'.
                'sum(a.discount_price) as discount_price,sum(a.top_price-a.rebate_price) as top_price')
            ->where($where)->group('a.product_name,a.channel_id')
            ->order('a.channel_id')
            ->limit(3000)
            ->select();

        foreach($list as &$vv){
            if(empty($vv['top_price']) || $vv['top_price'] == 0){
                $vv['discount'] = '--';
            }else{
                $vv['discount'] = sprintf('%.3f',$vv['top_price']/$vv['stat_price']*10);
            }
            if(strstr($vv['product_name'], 'G') || strstr($vv['product_name'], 'g')){
                $vv['product_size'] = $vv['stat_count'] * trim($vv['product_name'],'G') * 1024;
            }else if(strstr($vv['product_name'], 'g')){
                $vv['product_size'] = $vv['stat_count'] * trim($vv['product_name'],'g') * 1024;
            }else{
                $vv['product_size'] = $vv['stat_count'] * trim($vv['product_name'],'M');
            }
        }

        $datas = array();
        $headArr=array("通道编码","通道名称","产品规格","订单总数","流量总计（M）","成本总额","原价总额","销售总额","折扣(折)");
        foreach ($list as $v) {
            $data=array();
            $data['channel_code'] = $v['channel_code'];
            $data['channel_name'] = $v['channel_name'];
            $data['product_name'] = $v['product_name'];
            $data['stat_count'] = $v['stat_count'];
            $data['product_size'] = $v['product_size'];
            $data['top_price'] = $v['top_price'];
            $data['stat_price'] = $v['stat_price'];
            $data['discount_price'] = $v['discount_price'];
            $data['discount'] = $v['discount'];
            array_push($datas,$data);
        }

        $title='上游对账信息表';
        ExportEexcel($title,$headArr,$datas);

    }



}
?>