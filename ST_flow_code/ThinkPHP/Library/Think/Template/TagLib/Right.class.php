<?php

namespace Think\Template\TagLib;
use Think\Template\TagLib;

/**
 * RIGHT标签库解析类
 */
class Right extends TagLib {

    // 标签定义
    protected $tags   =  array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'isshow'   =>  array('attr'=>'url','close'=>1,'level'=>1),

        );

    /**
     * isshow标签解析
     * @access public
     * @param array $tag 标签属性
     * @param string $content  标签内容
     * @return string
     */
    public function _isshow($tag,$content) {

        $url = strtolower(trim($tag['url']));
        //获取权限的列表
        $rightlist = D('SysUser')->getfunctionlist();
        $is = substr($url,0,1);
        if($is === '$'){
            $rightstr = 'array(';
            foreach($rightlist as $v){
                $rightstr .= "'".$v."',";
            }
            $rightstr .= ')';
            $str = '<?php if(in_array(strtolower('.$url.'),'.$rightstr.')){ ?>'.$content.'<?php } ?>';
        }else{
             $str = "<?php ";
             $str .= "if(is_jurisdiction('".$url."')){";
             $str .= " ?>";
             $str .= $content;
             $str .= "<?php } ?>";
        }
        
        return $str;
    }
/*
	public function _isshow($tag,$content) {

        $url = strtolower(trim($tag['url']));
        //获取权限的列表
        $rightlist = D('SysUser')->getfunctionlist();
        $is = substr($url,0,1);
        if($is === '$'){
            $rightstr = 'array(';
            foreach($rightlist as $v){
                $rightstr .= "'".$v."',";
            }
            $rightstr .= ')';
            $str = '<?php if(in_array(strtolower('.$url.'),'.$rightstr.')){ ?>'.$content.'<?php } ?>';
        }else{
            if(in_array($url,$rightlist)){
                $isshow = 'true';
            }else{
                $isshow = 'false';
            }
             $str = "<?php ";
             $str .= "if($isshow){";
             $str .= " ?>";
             $str .= $content;
             $str .= "<?php } ?>";
        }
        
        return $str;
    }
*/
}
