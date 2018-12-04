<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Think;

class Page{
    public $firstRow; // 起始行数
    public $listRows; // 列表每页显示行数
    public $parameter; // 分页跳转时要带的参数
    public $totalRows; // 总行数
    public $totalPages; // 分页总页面数
    public $rollPage   = 10;// 分页栏每页显示的页数
    public $lastSuffix = true; // 最后一页是否显示总页数
    public $routing    =false;// 是否开启了路由功能，false没有，true开启了路由

    private $p       = 'p'; //分页参数名
    public  $url     = ''; //当前链接URL
    private $nowPage = 1;
    public $jump_url='';
    // 分页显示定制
    private $config  = array(
        //'header' => ' <span class="totalnum">总计<em> %TOTAL_ROW%</em>条，分<em>%TOTAL_PAGE%</em>页显示，当前第<em>%NOW_PAGE%</em>页</span> ',
        'header' => ' <span class="totalnum">共<em> %TOTAL_ROW% </em>条记录，第<em> %NOW_PAGE%/%TOTAL_PAGE% </em>页</span>',
        'prev'   => '',
        'next'   => '',
        'first'  => '首页',
        'last'   => '尾页',
        'theme'  => '%HEADER%  %DIV_H%  %FIRST% %UP_PAGE% %LINK_PAGE% %END% %DOWN_PAGE% %LINKPAGE% %DIV_B%',
    );

    /**
     * 架构函数
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows, $listRows=20,$routing,$parameter = array()) {
        C('VAR_PAGE') && $this->p = C('VAR_PAGE'); //设置分页参数名称
        /* 基础设置 */
        $this->totalRows  = $totalRows; //设置总记录数
        $this->listRows   = $listRows;  //设置每页显示行数
        $this->parameter  = empty($parameter) ? $_GET :$parameter;
        $this->nowPage    = empty($_GET[$this->p]) ? 1 : intval($_GET[$this->p]);
        $this->nowPage    = $this->nowPage>0 ? $this->nowPage : 1;
        $this->firstRow   = $this->listRows * ($this->nowPage - 1);
        $this->routing   =$routing;
        //$this->jump_url   = $this->getUri();

    }


    public function strreplace($str){
        $arr=array();
        foreach($str as $k=>$v){
            $arr[$k]=$this->strreplaces($v);
        }
        return $arr;
    }

    public function strreplaces($str){
        $str = stripslashes($str);
        $str = str_replace('+'," ",$str);
        $str = str_replace('%3A',":",$str);
        return trim($str);
    }

    /**
     * 定制分页链接设置
     * @param string $name  设置名称
     * @param string $value 设置值
     */
    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }
    private function replace($str){
        $str1 = str_replace('=','/',$str);
        $str2 = str_replace('&','/',$str1);
        return $str2;
    }
    /*在对象内部使用，用于自动获取访问的当前url*/
    private function getUri(){
        $request_uri = $_SERVER['REQUEST_URI'];
        if ($this->routing) {
            $depr = C('URL_PATHINFO_DEPR');
            $url=explode('.',$request_uri);
            $eurl=explode($depr,$url[0]);
            $jump_url =$eurl[0].$depr.$eurl[1].$depr;
        }else {
            $this->parameter[$this->p] =$_GET['jump_url'];
            parse_str($this->parameter[$this->p], $arrs);

            $jump_url ='/'.MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME.'/'.http_build_query($this->parameter).'/'.$this->p.'/';
        }
        return $jump_url;
    }




    /**
     * 生成链接URL
     * @param  integer $page 页码
     * @return string
     */
    private function url($page){
        return urldecode(str_replace(urlencode('[PAGE]'), $page, $this->url));
    }
    /*分页算法*/

    private  function generatePageList(){
        $pageList = array();
        if($this->totalPages <= 9){
            for($i=0;$i<$this->totalPages;$i++){
                array_push($pageList,$i+1);
            }
        }else{
            if($this->nowPage <= 4){
                for($i=0;$i<6;$i++){
                    array_push($pageList,$i+1);
                }
                array_push($pageList,-1);
                array_push($pageList,$this->totalPages);

            }else if($this->nowPage > $this->totalPages - 4){
                array_push($pageList,1);

                array_push($pageList,-1);
                for($i=6;$i>0;$i--){
                    array_push($pageList,$this->totalPages - $i+1);
                }
            }else if($this->nowPage > 4 && $this->nowPage <= $this->totalPages - 4){
                array_push($pageList,1);
                array_push($pageList,-1);

                array_push($pageList,$this->nowPage -2);
                array_push($pageList,$this->nowPage -1);
                array_push($pageList,$this->nowPage);
                array_push($pageList,$this->nowPage + 1);
                array_push($pageList,$this->nowPage + 2);
                array_push($pageList,-1);
                array_push($pageList,$this->totalPages);
            }
        }
        return $pageList;
    }


    /**
     * 组装分页链接
     * @return string
     */
    public function show() {
        if(0 == $this->totalRows) return '';
        /* 生成URL */
        if (empty($this->url)) {
            $this->parameter[$this->p] = '[PAGE]';
            $this->url = U(ACTION_NAME, $this->parameter);
        }else {
            $depr = C('URL_PATHINFO_DEPR');

            $this->url = rtrim(U('/'.$this->url,'',false),$depr).$depr.urlencode('[PAGE]').'.html';
        }

        /* 计算分页信息 */
        $this->totalPages = ceil($this->totalRows / $this->listRows); //总页数
        if(!empty($this->totalPages) && $this->nowPage > $this->totalPages) {
            $this->nowPage = $this->totalPages;
        }

        /* 计算分页临时变量 */
        $now_cool_page      = $this->rollPage/2;
        $now_cool_page_ceil = ceil($now_cool_page);
        $this->lastSuffix && $this->config['last']=$this->totalPages;

        //上一页
        $up_page='';
        if($this->totalPages>1){
            $up_row  = $this->nowPage - 1;
            if($up_row==0){
                $up_page ='<li class="prev"><a class="prev" href="' . $this->url($this->nowPage) . '"><i></i></a></li>';
            }else{
                $up_page ='<li class="prev"><a class="prev" href="' . $this->url($up_row) . '">' . $this->config['prev'] . '<i></i></a></li>';
            }

        }


        //下一页
        $down_page='';
        if($this->totalPages>1){
            $down_row  = $this->nowPage + 1;
            if($down_row==$this->totalPages+1){
                $down_page = '<li class="next"><a  href="' . $this->url($this->nowPage) . '"><i></i></a></li>' ;
            }else{
                $down_page = '<li class="next"><a  href="' . $this->url($down_row) . '">' . $this->config['next'] . '<i></i></a></li>' ;
            }
        }

        //第一页
        $the_first = '';
        /* if($this->totalPages > $this->rollPage && ($this->nowPage - $now_cool_page) >= 1){
             $the_first = '<li ><a class="first" href="' . $this->url(1) . '">' . $this->config['first'] . '</a></li>';
         }*/

        //最后一页
        $the_end = '';

        /*if($this->totalPages > $this->rollPage && ($this->nowPage + $now_cool_page) < $this->totalPages){
             $the_end = '<li class="num" ><a  href="' . $this->url($this->totalPages) . '">' . $this->config['last'] . '</a></li>';
         }*/

        $link_page = "";
        $pageList = $this->generatePageList();
        if(!empty($pageList)){
            if($this->totalPages >1){
                foreach ($pageList as $k=>$p){
                    if($this->nowPage == $p){
                        $link_page = $link_page .'<li class="num active"><a href="javascript:;">' .$this->nowPage . '</a></li>';;
                        continue;
                    }
                    if($p == -1) {
                        $link_page = $link_page . ' <li class="num"><a href="javascript:;">...</a></li>';
                        continue;
                    }
                    $link_page = $link_page .'<li class="num"><a  href="' . $this->url($p) . '">' . $p . '</a></li>';
                }

            }
        }
        $linkPage='';
       /* if($this->totalPages>1){
            $linkPage .= '<li class="input">';
            $linkPage .='<input class="inputtext" type="text" id="inputtext_id" />';
            $linkPage.= '<button  class="page_btn"  onclick="javascript:var page=(this.previousSibling.value>'.$this->totalPages.')?'.$this->totalPages.':this.previousSibling.value;location=\''.$this->jump_url.'\'+page+\'.html\'" >go </button> ';
            $linkPage .= '</li>';
        }*/
        $div_h='<div class="page_nav">
                <ul>';
        $div_b='</ul></div>';
        //替换分页内容

        $page_str = str_replace(
            array('%HEADER%','%DIV_H%' , '%NOW_PAGE%', '%UP_PAGE%', '%FIRST%','%LINKPAGE%', '%END%', '%DOWN_PAGE%', '%TOTAL_ROW%', '%TOTAL_PAGE%', '%LINK_PAGE%', '%DIV_B%'),
            array($this->config['header'],$div_h, $this->nowPage, $up_page,  $the_first,$linkPage, $the_end, $down_page,$this->totalRows, $this->totalPages,$link_page,$div_b),
            $this->config['theme']);

        return "{$page_str}";
    }
}
