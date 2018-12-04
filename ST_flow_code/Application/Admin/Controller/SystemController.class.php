<?php

/*
 * 参数管理控制器
 */
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class SystemController extends CommonController {

    /*
     * 参数列表
     */
    public function index(){

        $type = trim(I('get.download'));

        if( $type !== '' )
        {
            $imgurl =  C('DOMAIN_LOGIN_DEFAULT_INFO.'.$type);

            if ( file_exists( '.'.$imgurl ) )
            {
                parent::download('.' . $imgurl);
            }
            exit;
        }
        

        if(!empty($_FILES))
        {
            $fileinfo = $this->scene_base_upload(C('USER_LOGO_UPLOAD_DIR'));
            $error = $this->business_licence_upload_Error;
            if($error){
                if($error['logo_img'] && $error['logo_img'] != '没有文件被上传！'){
                    $msg = 'LOGO图片'.$error['logo_img'];
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                }
            }
            if($fileinfo['logo_img']){
                $logo_img = substr(C('UPLOAD_DIR').$fileinfo['logo_img']['savepath'].$fileinfo['logo_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['logo_img']['savepath'].$fileinfo['logo_img']['savename'])-1);
            }

            exit;
        }

        #统计代理商数量
        $proxycount =M("proxy")->count();

        $p_status = $proxycount > 1 ? 2 : 1 ;

        #统计企业数量
        $enterprisecount =M("enterprise")->count();

        $e_status = $enterprisecount > 0 ? 2 : 1 ;

        $this->assign("p_status",$p_status);

        $this->assign("e_status",$e_status);

        $this->display();

    }


    /**
     * 获取参数
     */
    public function Save()
    {   

        $title = array(
            'display_icon'      =>  '网站标题LOGO',
            'display_picture'   =>  '登录页面LOGO',
            'logo'              =>  '登录后LOGO',
            );

        $status = 'error';
        $msg = '配置保存失败';

        $array['COMMIT_URL'] = $_POST['COMMIT_URL'];
        $array['PROXY_NAME'] = $_POST['PROXY_NAME'];
        $array['DES_PROXY_ID'] = $_POST['DES_PROXY_ID'];
        $array['DES_ENTERPRISE_ID'] = $_POST['DES_ENTERPRISE_ID'];
        $array['API_SUBMIT'] = $_POST['API_SUBMIT'];
        $array['API_QUERY'] = $_POST['API_QUERY'];
        $array['API_BALANCE'] = $_POST['API_BALANCE'];
        $array['DOMAIN_LOGIN_DEFAULT_INFO']['display_title'] = $_POST['display_title'];
        $array['DOMAIN_LOGIN_DEFAULT_INFO']['display_end'] = C('DOMAIN_LOGIN_DEFAULT_INFO.display_end');
        $array['DOMAIN_LOGIN_DEFAULT_INFO']['display_back'] = C('DOMAIN_LOGIN_DEFAULT_INFO.display_back');

        if( $_FILES['display_icon']['error'] !== 0 )
        {
            $array['DOMAIN_LOGIN_DEFAULT_INFO']['display_icon'] = C('DOMAIN_LOGIN_DEFAULT_INFO.display_icon');
            unset($_FILES['display_icon']);
        }

        if( $_FILES['display_picture']['error'] !== 0  )
        {
            $array['DOMAIN_LOGIN_DEFAULT_INFO']['display_picture'] = C('DOMAIN_LOGIN_DEFAULT_INFO.display_picture');
            unset($_FILES['display_picture']);
        }

        if( $_FILES['logo']['error'] !== 0  )
        {
            $array['DOMAIN_LOGIN_DEFAULT_INFO']['logo'] = C('DOMAIN_LOGIN_DEFAULT_INFO.logo');
            unset($_FILES['logo']);
        }

        $fileinfo = $this->scene_base_upload( C('USER_LOGO_UPLOAD_DIR') );
        $error = $this->business_licence_upload_Error;

        #判断上传错误
        if(!empty( $error ) ){
            foreach( $error  as $key => $value )
            {
                $msg = $title[$key].$value;
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
                exit;
            }
            
        }

        #如果没有错误则赋值
        foreach( $fileinfo  as $key => $value )
        {
            $array['DOMAIN_LOGIN_DEFAULT_INFO'][$key] = substr(C('UPLOAD_DIR').$fileinfo[$key]['savepath'].$fileinfo[$key]['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo[$key]['savepath'].$fileinfo[$key]['savename'])-1);
        }

        $array['API_ACCOUNT']   =  C('API_ACCOUNT');

        $array['API_KEY']       =  C('API_KEY');

        try
        {
            #写入文件
            $result = $this->arraytofile( $array  );

        }catch(\Exception $e)
        {
            $result = array('status'=>'error' , 'msg' => '配置保存失败,'.$e->getMessage() );
        }

        if( $result['status'] === 'success' )
        {
            $msg = '配置保存成功';
            $status = 'success';
        }else
        {
            $msg = $result['msg'] == '' ? '配置保存失败' : $result['msg']  ;
        }

        $this->ajaxReturn(array('msg'=>$msg,'status'=>$status));
    }


    /**
     * 将参数写入配置文件
     */
    private function arraytofile( $array = array() , $filepath = '' )
    {   

        if( $filepath == '')  $filepath = C('SYSTEM_CONFIG_FILE_PATH');

        $str = '<?php'.PHP_EOL.PHP_EOL;
        $str .= "\t".'return array( '.PHP_EOL.PHP_EOL;
        $str .= $this->arraytostr( $array , 1 );
        $str .= PHP_EOL.PHP_EOL."\t".');'.PHP_EOL.PHP_EOL;
        $file = fopen( $filepath , 'w' );

        if( !fwrite( $file , $str ) )
        {
            throw new \Exception('没有配置文件写入权限');
        }

        fclose($file);

        return array('status'=> 'success' , 'msg' => '' );
    }


    private  function arraytostr( $value , $num )
    { 
        if( empty( $value ) ) return '';

        $str = '';

        $tnum = '';

        for($i = 0 ; $i < $num+1 ; $i ++){
            $tnum .= "\t";
        }
         
        foreach( $value as $k => $v)
        {   
            if( is_array($v) )
            {   
                
                $str .= $tnum.'"'.$k.'"  => array( '.PHP_EOL.PHP_EOL;

                $str .= $this->arraytostr( $v , ++$num );

                $str .= $tnum.'),'.PHP_EOL.PHP_EOL;
            }else
            {   

                $str .= $tnum.'"'.$k.'"   =>  "'.$v.'",'.PHP_EOL.PHP_EOL;
                
            }

            
        }

        return $str;

    }

}