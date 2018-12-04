<?php 
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;
class CustomreplyController extends CommonController{
	/*
	 * 自定义回复设置
	 */
	public function  index(){
        D("SysUser")->sessionwriteclose();
        $reply_keyword=I("reply_keyword");
        if(!empty($reply_keyword)){
            $where['w.reply_keyword']=array("like","%".$reply_keyword."%");
        }
        $where['w.enterprise_id']=D('SysUser')->self_enterprise_id();//获取自身的企业ID
        /*$count=M("wxs_reply as w")
            ->join("t_flow_wxs_replytext as r on r.replytext_id=w.reply_keywordid and w.reply_type=1","left")
            ->join("t_flow_wxs_replyimg as ri on ri.replyimg_id=w.reply_keywordid and w.reply_type=2","left")
            ->join("t_flow_wxs_replymoreimg as rmi on rmi.replymoreimg_id=w.reply_keywordid and w.reply_type=3","left")
            ->join("t_flow_wxs_replyactivity as rt on rt.replyactivity_id=w.reply_keywordid and w.reply_type=4","left")
            ->where($where)
            ->count();
        $Page = new \Think\Page($count,12);
        $show = $Page->show();*/
        $list = M("wxs_reply as w")
            ->join("t_flow_wxs_replytext as r on r.replytext_id=w.reply_keywordid and w.reply_type=1","left")
            ->join("t_flow_wxs_replymoreimg as rmi on rmi.replymoreimg_id=w.reply_keywordid and (w.reply_type=3 or w.reply_type=2)","left")
            ->join("t_flow_wxs_replyactivity as rt on rt.replyactivity_id=w.reply_keywordid and w.reply_type=4","left")
            ->where($where)
            ->field("w.*,r.*,rmi.*,rt.*")
            ->order("w.reply_id desc")
            ->select();
            $newlist = array();
            foreach($list as $listinfo)
            {
                $translistinfo = $this->transinfo($listinfo);
			    array_push($newlist, $translistinfo);
            }
        $this->assign('list',get_sort_no($newlist));  //数据列表
        $this->display();        //模板
    }

    private function transinfo($listinfo)
    {
                $reply_type = $listinfo["reply_type"];
                if($reply_type == 2)
                {
                    //标示为单图文模式
                    //查询到相关的id。并且修改相关的id
                    $replymoreimg_material =  $listinfo["replymoreimg_material"];
                    $materialarray = explode(",",$replymoreimg_material);
                    foreach($materialarray as $materialid)
                    {
                        $replyimg['replyimg_id'] = $materialid;
                        $info=M("wxs_replyimg")->where($replyimg)->find();
                        $listinfo["replyimg_img"] = $info["replyimg_img"];
                        $listinfo["replyimg_url"] = $info["replyimg_url"];
                        $listinfo["replyimg_description"] = $info["replyimg_description"];
                        $listinfo["replyimg_title"] = $info["replyimg_title"];
                    }
                }
                else if ($reply_type == 3) 
                {
                    //标示为单图文模式
                    //查询到相关的id。并且修改相关的id
                    $count = 1;
                    $replymoreimg_material =  $listinfo["replymoreimg_material"];
                    $materialarray = explode(",",$replymoreimg_material);
                    foreach($materialarray as $materialid)
                    {
                        $replyimg['replyimg_id'] = $materialid;
                        $info=M("wxs_replyimg")->where($replyimg)->find();
                        $keyreplymoreimg_img = "replymoreimg_img".$count;
                        $keyreplymoreimg_url = "replymoreimg_url".$count;
                        $keyreplymoreimg_title = "replymoreimg_title".$count;

                        $listinfo[$keyreplymoreimg_img] = $info["replyimg_img"];
                        $listinfo[$keyreplymoreimg_url] = $info["replyimg_url"];
                        $listinfo[$keyreplymoreimg_title] = $info["replyimg_title"];
                        $count++;
                    }
                }
        return $listinfo;
    }
    //新增自定义回复
    public function add(){
        $where['sua.enterprise_id']=D('SysUser')->self_enterprise_id();//获取自身的企业ID
        $activity=M("scene_user_activity  as sua")
            ->join("t_flow_scene_activity as sa on sa.activity_id=sua.activity_id","left")
            ->where($where)
            ->order("sua.user_activity_type asc,sua.modify_date desc")
            ->field("sua.user_activity_id,sa.activity_name,sua.user_activity_name,sua.user_activity_type")
            ->select();
        $this->assign("activity",$activity);
        $this->display();
    }

    public function insert(){
        $msg="系统错误！";
        $status="error";
        $enterprise_id=D('SysUser')->self_enterprise_id();//获取自身的企业ID
        $reply_type=trim(I("reply_type")); //回复类型（1.文本，2.单图片，3.多图片，4.活动）
        $reply_keyword=trim(I("reply_keyword"));
        $reply_concern=trim(I("reply_concern"));
        if(empty($reply_keyword)){
            $msg="请输入关键字！";
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }
        $words=explode(",",$reply_keyword);
        if(is_array($words)){
            foreach ($words as $w){
                $wxr=M("wxs_reply")->where(array("reply_keyword"=>$w,"enterprise_id"=>$enterprise_id))->find();
                if($wxr){
                    $msg="关键字已被使用!";
                    $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
                }
            }
        }
        if($reply_concern==1){
            $wx=M("wxs_reply")->where(array("reply_concern"=>1,"enterprise_id"=>$enterprise_id))->find();
            if($wx){
                $msg="设置失败，关注回复只能设置一个！";
                $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
            }
        }
        $reply=array();//自定义回复表信息
        $data=array();//回复内容
        $data['create_date']=date('Y-m-d H:i:s',time());
        $data['create_user_id']=D('SysUser')->self_id();
        $data['modify_date']=date('Y-m-d H:i:s',time());
        $data['modify_user_id']=D('SysUser')->self_id();
        $id=0;//之定义具体内容id;
        M("wxs_reply")->startTrans();

        //文本回复
        if($reply_type==1) {
            $replytext_contact = trim(I("replytext_contact"));//回复内容
            if (empty($replytext_contact)) {
                $msg = "请输入回复内容";
                $this->ajaxReturn(array("msg" => $msg, "status" => $status));
            }
            $data['replytext_contact'] = $replytext_contact;
            $id = M("wxs_replytext")->add($data);
        }

        //单图片回复
        if($reply_type==2){
            $replyimg_title=trim(I('replyimg_title'));
            $replyimg_url=trim(I('replyimg_url'));
            $replyimg_description=trim(I('replyimg_description'));
            if(empty($replyimg_title)){
                $msg = "请输入回复标题";
                $this->ajaxReturn(array("msg" => $msg, "status" => $status));
            }
            if(empty($replyimg_description)){
                $msg = "请输入回复内容";
                $this->ajaxReturn(array("msg" => $msg, "status" => $status));
            }
            if($_FILES['replyimg_img']['name']==null || $_FILES['replyimg_img']['name']==""){
                $icense_img = "";
            }else {
                $fileinfo = $this->business_licence_upload(C('PROXY_UPLOAD_DIR'));
                if($fileinfo['replyimg_img']){
                    $icense_img = substr(C('UPLOAD_DIR').$fileinfo['replyimg_img']['savepath'].$fileinfo['replyimg_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['replyimg_img']['savepath'].$fileinfo['replyimg_img']['savename'])-1);
                }else{
                    $msg = $this->business_licence_upload_Error['replyimg_img'];
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                }
            }
            $data['replyimg_title']=$replyimg_title;
            $data['replyimg_img']=$icense_img;
            $data['replyimg_url']=$replyimg_url;
            $data['replyimg_description']=$replyimg_description;
            $id=M("wxs_replyimg")->add($data);

            //而后在多图文模式下再增加一条素材模式

            $data['create_date']=date('Y-m-d H:i:s',time());
            $data['create_user_id']=D('SysUser')->self_id();
            $data['modify_date']=date('Y-m-d H:i:s',time());
            $data['modify_user_id']=D('SysUser')->self_id();
            $moreimgdata["replymoreimg_material"] = $id;
            $id=M("wxs_replymoreimg")->add($moreimgdata);
        }

        //多图片回复
        if($reply_type==3){
            $replymoreimg_title1=trim(I("replymoreimg_title1"));
            $replymoreimg_url1=trim(I("replymoreimg_url1"));
            $replymoreimg_title2=trim(I("replymoreimg_title2"));
            $replymoreimg_url2=trim(I("replymoreimg_url2"));
            $replymoreimg_title3=trim(I("replymoreimg_title3"));
            $replymoreimg_url3=trim(I("replymoreimg_url3"));
            $replymoreimg_title4=trim(I("replymoreimg_title4"));
            $replymoreimg_url4=trim(I("replymoreimg_url4"));
            if(empty($replymoreimg_title1)){
                $msg="请输入主题1";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
            }
            $fileinfo = $this->business_licence_upload(C('PROXY_UPLOAD_DIR'));
           if($_FILES['replymoreimg_img1']['name']==null || $_FILES['replymoreimg_img1']['name']==""){
                $replymoreimg_img1 = "";
            }else {
               if($fileinfo['replymoreimg_img1']){
                   $replymoreimg_img1 = substr(C('UPLOAD_DIR').$fileinfo['replymoreimg_img1']['savepath'].$fileinfo['replymoreimg_img1']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['replymoreimg_img1']['savepath'].$fileinfo['replymoreimg_img1']['savename'])-1);
               }else{
                   $msg = $this->business_licence_upload_Error['replymoreimg_img1'];
                   $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
               }
            }
            if($_FILES['replymoreimg_img2']['name']==null || $_FILES['replymoreimg_img2']['name']==""){
                $replymoreimg_img2 = "";
            }else {
                if($fileinfo['replymoreimg_img2']){
                    $replymoreimg_img2 = substr(C('UPLOAD_DIR').$fileinfo['replymoreimg_img2']['savepath'].$fileinfo['replymoreimg_img2']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['replymoreimg_img2']['savepath'].$fileinfo['replymoreimg_img2']['savename'])-1);
                }else{
                    $msg = $this->business_licence_upload_Error['replymoreimg_img2'];
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                }
            }
            if($_FILES['replymoreimg_img3']['name']==null || $_FILES['replymoreimg_img3']['name']==""){
                $replymoreimg_img3 = "";
            }else {
                if($fileinfo['replymoreimg_img3']){
                    $replymoreimg_img3 = substr(C('UPLOAD_DIR').$fileinfo['replymoreimg_img3']['savepath'].$fileinfo['replymoreimg_img3']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['replymoreimg_img3']['savepath'].$fileinfo['replymoreimg_img3']['savename'])-1);
                }else{
                    $msg = $this->business_licence_upload_Error['replymoreimg_img3'];
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                }
            }
            if($_FILES['replymoreimg_img4']['name']==null || $_FILES['replymoreimg_img4']['name']==""){
                $replymoreimg_img4 = "";
            }else {
                if($fileinfo['replymoreimg_img4']){
                    $replymoreimg_img4 = substr(C('UPLOAD_DIR').$fileinfo['replymoreimg_img4']['savepath'].$fileinfo['replymoreimg_img4']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['replymoreimg_img4']['savepath'].$fileinfo['replymoreimg_img4']['savename'])-1);
                }else{
                    $msg = $this->business_licence_upload_Error['replymoreimg_img4'];
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                }
            }
            // $data['replymoreimg_url1']=$replymoreimg_url1;
            // $data['replymoreimg_title1']=$replymoreimg_title1;
            // $data['replymoreimg_img1']=$replymoreimg_img1;

            // $data['replymoreimg_title2']=$replymoreimg_title2;
            // $data['replymoreimg_url2']=$replymoreimg_url2;
            // $data['replymoreimg_img2']=$replymoreimg_img2;

            // $data['replymoreimg_title3']=$replymoreimg_title3;
            // $data['replymoreimg_url3']=$replymoreimg_url3;
            // $data['replymoreimg_img3']=$replymoreimg_img3;

            // $data['replymoreimg_title3']=$replymoreimg_title3;
            // $data['replymoreimg_url3']=$replymoreimg_url3;
            // $data['replymoreimg_img3']=$replymoreimg_img3;
            //将新增的几个素材。添加入素材表。然后在将数据插入表
            $materialstr="";
            $data['create_date']=date('Y-m-d H:i:s',time());
            $data['create_user_id']=D('SysUser')->self_id();
            $data['modify_date']=date('Y-m-d H:i:s',time());
            $data['modify_user_id']=D('SysUser')->self_id();
      
            $data['replyimg_title']=$replymoreimg_title1;
            $data['replyimg_img']=$replymoreimg_img1;
            $data['replyimg_url']=$replymoreimg_url1;
            $id=M("wxs_replyimg")->add($data);
            $materialstr = $materialstr.$id;
            $data['replyimg_title']=$replymoreimg_title2;
            $data['replyimg_img']=$replymoreimg_img2;
            $data['replyimg_url']=$replymoreimg_url2;
            $id=M("wxs_replyimg")->add($data);
            $materialstr = $materialstr.",".$id;
            $data['replyimg_title']=$replymoreimg_title3;
            $data['replyimg_img']=$replymoreimg_img3;
            $data['replyimg_url']=$replymoreimg_url3;
            $id=M("wxs_replyimg")->add($data);
            $materialstr = $materialstr.",".$id;
            $data['replyimg_title']=$replymoreimg_title4;
            $data['replyimg_img']=$replymoreimg_img4;
            $data['replyimg_url']=$replymoreimg_url4;
            $id=M("wxs_replyimg")->add($data);
            $materialstr = $materialstr.",".$id;
            
            $moreimgdata['create_date']=date('Y-m-d H:i:s',time());
            $moreimgdata['create_user_id']=D('SysUser')->self_id();
            $moreimgdata['modify_date']=date('Y-m-d H:i:s',time());
            $moreimgdata['modify_user_id']=D('SysUser')->self_id();
            $moreimgdata["replymoreimg_material"] = $materialstr;

            $id=M("wxs_replymoreimg")->add($moreimgdata);
        }

        //活动回复
        if($reply_type==4){
            $user_activity_id=trim(I("user_activity_id"));
            $replyactivity_title=trim(I("replyactivity_title"));
            $replyactivity_description=trim(I("replyactivity_description"));
            $replyactivity_contact=trim(I("replyactivity_contact"));
            if(empty($user_activity_id)){
                $msg="请选择活动！";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
            }
            if($_FILES['replyactivity_img']['name']==null || $_FILES['replyactivity_img']['name']==""){
                $icense_img = "";
            }else {
                $fileinfo = $this->business_licence_upload(C('PROXY_UPLOAD_DIR'));
                if($fileinfo['replyactivity_img']){
                    $icense_img = substr(C('UPLOAD_DIR').$fileinfo['replyactivity_img']['savepath'].$fileinfo['replyactivity_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['replyactivity_img']['savepath'].$fileinfo['replyactivity_img']['savename'])-1);
                }else{
                    $msg = $this->business_licence_upload_Error['replyactivity_img'];
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                }
            }
            $data['user_activity_id']=$user_activity_id;
            $data['replyactivity_title']=$replyactivity_title;
            $data['replyactivity_description']=$replyactivity_description;
            $data['replyactivity_contact']=$replyactivity_contact;
            $data['replyactivity_img']=$icense_img;
            $data['create_date']=date('Y-m-d H:i:s',time());
            $data['create_user_id']=D('SysUser')->self_id();
            $data['modify_date']=date('Y-m-d H:i:s',time());
            $data['modify_user_id']=D('SysUser')->self_id();
            $id=M('wxs_replyactivity')->add($data);
        }
        if($id==0){
            $msg="新增自定义活动失败！";
            M("wxs_reply")->rollback();
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }
        //自定回复表信息
        $reply['reply_type']=$reply_type;
        $reply['reply_keyword']=$reply_keyword;
        $reply['reply_keywordid']=$id;
        $reply['enterprise_id']=$enterprise_id;
        $reply['reply_concern']=$reply_concern;
        $reply['user_type']=2;
        $wr=M("wxs_reply")->add($reply);
        if($wr){
            $msg="新增自定义回复成功！";
            $status="success";
            M("wxs_reply")->commit();
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }else{
            $msg="新增自定义回复失败！";
            M("wxs_reply")->rollback();
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }
    }
    
    //删除自定义回复
    public function delete(){
        $msg="系统错误！";
        $status="error";
        $reply_id=trim(I("reply_id"));//自定义回复id
        if(empty($reply_id)){
            $msg="信息错误！";
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }
        M("wxs_reply")->startTrans();
        $info=M("wxs_reply")->where(array("reply_id"=>$reply_id))->find();
        if($info){
            switch ($info['reply_type']){
                case 1:
                    $rt=M("wxs_replytext")->where(array("replytext_id"=>$info['reply_keywordid']))->delete();
                    break;
                case 2:
                {
                    $moreimginfo=M("wxs_replymoreimg")->where(array("replymoreimg_id"=>$info['reply_keywordid']))->find();
                    //查询到相关的id。并且修改相关的id
                    $replymoreimg_material =  $moreimginfo["replymoreimg_material"];


                    $rt=M("wxs_replymoreimg")->where(array("replymoreimg_id"=>$info['reply_keywordid']))->delete();
                    $materialarray = explode(",",$replymoreimg_material);
                    foreach ($materialarray as $materialid) {
                       $rt=M("wxs_replyimg")->where(array("replyimg_id"=>$materialid))->delete();
                    }
                }
                    break;
                case 3:
                {
                    $moreimginfoinfo=M("wxs_replymoreimg")->where(array("replymoreimg_id"=>$info['reply_keywordid']))->find();
                    //查询到相关的id。并且修改相关的id
                    $replymoreimg_material =  $moreimginfoinfo["replymoreimg_material"];
                    $rt=M("wxs_replymoreimg")->where(array("replymoreimg_id"=>$info['reply_keywordid']))->delete();

                    $materialarray = explode(",",$replymoreimg_material);
                    foreach ($materialarray as $materialid) {
                       $rt=M("wxs_replyimg")->where(array("replyimg_id"=>$materialid))->delete();
                    }
                }
                    break;
                case 4:
                    $rt=M("wxs_replyactivity")->where(array("replyactivity_id"=>$info['reply_keywordid']))->delete();
                    break;
            }
            $wr=M("wxs_reply")->where(array("reply_id"=>$reply_id))->delete();
            if($wr && $rt){
                $msg="删除自定义回复成功！";
                $status="success";
                M("wxs_reply")->commit();
                $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
            }else{
                M("wxs_reply")->rollback();
                $msg="删除自定义回复失败！";
                $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
            }
        }else{
            M("wxs_reply")->rollback();
            $msg="信息错误！";
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }
    }
    public function edit(){
        if(I("download")){
            $reply_id =I('get.reply_id');
            $name=I('name');
            $info=M("wxs_reply as w")
                ->join("t_flow_wxs_replytext as r on r.replytext_id=w.reply_keywordid and w.reply_type=1","left")
                ->join("t_flow_wxs_replymoreimg as rmi on rmi.replymoreimg_id=w.reply_keywordid and (w.reply_type=3 or w.reply_type=2)","left")
                ->join("t_flow_wxs_replyactivity as rt on rt.replyactivity_id=w.reply_keywordid and w.reply_type=4","left")
                ->where(array("w.reply_id"=>$reply_id))
                ->find();
            $info = $this->transinfo($info);
            parent::download('.'.$info[$name]);
        }else {
            $msg = "系统错误！";
            $status = "error";
            $reply_id = trim(I("reply_id"));//自定义回复id
            if (empty($reply_id)) {
                $msg = "信息错误！";
                $this->ajaxReturn(array("msg" => $msg, "status" => $status));
            }
            M("wxs_reply")->startTrans();
            $info = M("wxs_reply as w")
                ->join("t_flow_wxs_replytext as r on r.replytext_id=w.reply_keywordid and w.reply_type=1", "left")
                ->join("t_flow_wxs_replymoreimg as rmi on rmi.replymoreimg_id=w.reply_keywordid and (w.reply_type=3 or w.reply_type=2)","left")
                ->join("t_flow_wxs_replyactivity as rt on rt.replyactivity_id=w.reply_keywordid and w.reply_type=4", "left")
                ->where(array("w.reply_id" => $reply_id))
                ->find();
            $info = $this->transinfo($info);
            
            if ($info) {
                $where['sua.enterprise_id'] = D('SysUser')->self_enterprise_id();//获取自身的企业ID
                $activity = M("scene_user_activity  as sua")
                    ->join("t_flow_scene_activity as sa on sa.activity_id=sua.activity_id", "left")
                    ->where($where)
                    ->order("sua.user_activity_type asc,sua.modify_date desc")
                    ->field("sua.user_activity_id,sa.activity_name,sua.user_activity_name,sua.user_activity_type")
                    ->select();

                
                $this->assign("activity", $activity);
                $this->assign("info", $info);
                $this->display();
            } else {
                $msg = "信息错误！";
                $this->ajaxReturn(array("msg" => $msg, "status" => $status));
            }
        }
    }
    public function update(){
        $msg="系统错误！";
        $status="error";
        $enterprise_id=D('SysUser')->self_enterprise_id();//获取自身的企业ID
        $reply_id=trim(I("reply_id"));
        $reply_type=trim(I("reply_type")); //回复类型（1.文本，2.单图片，3.多图片，4.活动）
        $reply_keyword=trim(I("reply_keyword"));
        $reply_concern=trim(I("reply_concern"));//关注回复标示符
        $hfid=trim(I("id"));//回复内容具体id号
        if(empty($reply_keyword)){
            $msg="请输入关键字！";
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }
        $words=explode(",",$reply_keyword);
        if(is_array($words)){
            foreach ($words as $w){
                $wxr=M("wxs_reply")->where(array("reply_keyword"=>$w,"enterprise_id"=>$enterprise_id))->find();
                if($wxr && $wxr['reply_id']!=$reply_id){
                    $msg="关键字已被使用!";
                    $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
                }
            }
        }
        if($reply_concern==1){
            $wx=M("wxs_reply")->where(array("reply_concern"=>1,"enterprise_id"=>$enterprise_id))->find();
            if($wx && $wx['reply_id']!=$reply_id){
                $msg="设置失败，关注回复只能设置一个！";
                $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
            }
        }
        $reply=array();//自定义回复表信息
        $data=array();//回复内容
        $data['modify_date']=date('Y-m-d H:i:s',time());
        $data['modify_user_id']=D('SysUser')->self_id();
        $id=0;//之定义具体内容id;
        M("wxs_reply")->startTrans();
        if(empty($hfid)){
            $msg = "信息错误！";
            M("wxs_reply")->rollback();
            $this->ajaxReturn(array("msg" => $msg, "status" => $status));
        }
        //文本回复
        if($reply_type==1) {
            $where=array();
            $where['replytext_id']=$hfid;
            $info=M("wxs_replytext")->where($where)->find();
            if(empty($info)){
                $msg = "信息错误！";
                M("wxs_reply")->rollback();
                $this->ajaxReturn(array("msg" => $msg, "status" => $status));
            }
            $replytext_contact = trim(I("replytext_contact"));//回复内容
            if (empty($replytext_contact)) {
                $msg = "请输入回复内容";
                $this->ajaxReturn(array("msg" => $msg, "status" => $status));
            }
            $data['replytext_contact'] = $replytext_contact;
            $id = M("wxs_replytext")->where($where)->save($data);
        }
        //单图片回复
        if($reply_type==2){
            $replyimg_title=trim(I('replyimg_title'));
            $replyimg_url=trim(I('replyimg_url'));
            $replyimg_description=trim(I('replyimg_description'));

            if(empty($replyimg_title)){
                $msg = "请输入回复标题";
                $this->ajaxReturn(array("msg" => $msg, "status" => $status));
            }
            if(empty($replyimg_description)){
                $msg = "请输入回复内容";
                $this->ajaxReturn(array("msg" => $msg, "status" => $status));
            }
           if($_FILES['replyimg_img']['name']==null || $_FILES['replyimg_img']['name']==""){
                $icense_img ="";
            }else {
                $fileinfo = $this->business_licence_upload(C('PROXY_UPLOAD_DIR'));
                if($fileinfo['replyimg_img']){
                    $icense_img = substr(C('UPLOAD_DIR').$fileinfo['replyimg_img']['savepath'].$fileinfo['replyimg_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['replyimg_img']['savepath'].$fileinfo['replyimg_img']['savename'])-1);
                }else{
                    $msg = $this->business_licence_upload_Error['replyimg_img'];
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                }
            }
            $where=array();
            $where['replymoreimg_id']=$hfid;
            $info=M("wxs_replymoreimg")->where($where)->find();
            //找到图片资源路径找到对应的产品id

            if(empty($info)){
                $msg="信息有误！";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
            }
            $replymoreimg_material = $info["replymoreimg_material"];

	        $materialArray=explode(',',$replymoreimg_material);
		    //去素材库里面查找
		    $replymoreimg_materialid = $materialArray[0];
            $replyimg["replyimg_id"] = $replymoreimg_materialid;
            $replyimginfo=M("wxs_replyimg")->where($replyimg)->find();


            $data['replyimg_title']=$replyimg_title;
            if($icense_img !=""){
                $data['replyimg_img']=$icense_img;
            }
            $data['replyimg_url'] = $replyimg_url;
            $data['replyimg_description']=$replyimg_description;
            
            $id = M("wxs_replyimg")->where($replyimg)->save($data);
        }

        //多图片回复
        if($reply_type==3){
            $replymoreimg_title1=trim(I("replymoreimg_title1"));
            $replymoreimg_url1=trim(I("replymoreimg_url1"));
            $replymoreimg_title2=trim(I("replymoreimg_title2"));
            $replymoreimg_url2=trim(I("replymoreimg_url2"));
            $replymoreimg_title3=trim(I("replymoreimg_title3"));
            $replymoreimg_url3=trim(I("replymoreimg_url3"));
            $replymoreimg_title4=trim(I("replymoreimg_title4"));
            $replymoreimg_url4=trim(I("replymoreimg_url4"));
            $fileinfo = $this->business_licence_upload(C('PROXY_UPLOAD_DIR'));
            if(empty($replymoreimg_title1)){
                $msg="请输入主题1";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
            }
            if($_FILES['replymoreimg_img1']['name']==null || $_FILES['replymoreimg_img1']['name']==""){
                $replymoreimg_img1 = "";
            }else {
                if ($fileinfo['replymoreimg_img1']) {
                    $replymoreimg_img1 = substr(C('UPLOAD_DIR') . $fileinfo['replymoreimg_img1']['savepath'] . $fileinfo['replymoreimg_img1']['savename'], 1, strlen(C('UPLOAD_DIR') . $fileinfo['replymoreimg_img1']['savepath'] . $fileinfo['replymoreimg_img1']['savename']) - 1);
                } else {
                    $msg = $this->business_licence_upload_Error['replymoreimg_img1'];
                    $this->ajaxReturn(array('msg' => $msg, 'status' => $status, 'data' => $data));
                }
            }
            if($_FILES['replymoreimg_img2']['name']==null || $_FILES['replymoreimg_img2']['name']==""){
                $replymoreimg_img2 = "";
            }else {
                if($fileinfo['replymoreimg_img2']){
                    $replymoreimg_img2 = substr(C('UPLOAD_DIR').$fileinfo['replymoreimg_img2']['savepath'].$fileinfo['replymoreimg_img2']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['replymoreimg_img2']['savepath'].$fileinfo['replymoreimg_img2']['savename'])-1);
                }else{
                    $msg = $this->business_licence_upload_Error['replymoreimg_img2'];
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                }
            }
            if($_FILES['replymoreimg_img3']['name']==null || $_FILES['replymoreimg_img3']['name']==""){
                $replymoreimg_img3 = "";
            }else {
                if($fileinfo['replymoreimg_img3']){
                    $replymoreimg_img3 = substr(C('UPLOAD_DIR').$fileinfo['replymoreimg_img3']['savepath'].$fileinfo['replymoreimg_img3']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['replymoreimg_img3']['savepath'].$fileinfo['replymoreimg_img3']['savename'])-1);
                }else{
                    $msg = $this->business_licence_upload_Error['replymoreimg_img3'];
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                }
            }
            if($_FILES['replymoreimg_img4']['name']==null || $_FILES['replymoreimg_img4']['name']==""){
                $replymoreimg_img4 = "";
            }else {
                if($fileinfo['replymoreimg_img4']){
                    $replymoreimg_img4 = substr(C('UPLOAD_DIR').$fileinfo['replymoreimg_img4']['savepath'].$fileinfo['replymoreimg_img4']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['replymoreimg_img4']['savepath'].$fileinfo['replymoreimg_img4']['savename'])-1);
                }else{
                    $msg = $this->business_licence_upload_Error['replymoreimg_img4'];
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                }
            }

            $where=array();
            $where['replymoreimg_id']=$hfid;
            $info=M("wxs_replymoreimg")->where($where)->find();
            if(empty($info)){
                $msg="信息有误！";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
            }
            //查询到相关的id。并且修改相关的id
            $replymoreimg_material =  $info["replymoreimg_material"];

            $materialarray = explode(",",$replymoreimg_material);
            $count = 0;
            $infouser_id = D('SysUser')->self_id();
            $time = date('Y-m-d H:i:s',time());
            foreach ($materialarray as $materialid) {
                $where['replyimg_id'] = $materialid;
                $info=M("wxs_replyimg")->where($where)->find();
                $info['modify_date']= $time;
                $info['modify_user_id']= $infouser_id;
                switch ($count) {
                    case 0:
                        {
                            $info['replyimg_title']=$replymoreimg_title1;
                            if(!empty($replymoreimg_img1))
                            {
                                $info['replyimg_img']=$replymoreimg_img1;
                            }
                            $info['replyimg_url']=$replymoreimg_url1;
                        }
                        break;
                    case 1:
                        {
                            $info['replyimg_title']=$replymoreimg_title2;
                            if(!empty($replymoreimg_img2))
                            {
                                $info['replyimg_img']=$replymoreimg_img2;
                            }
                            $info['replyimg_url']=$replymoreimg_url2;
                        }
                        break;
                    case 2:
                        {
                            $info['replyimg_title']=$replymoreimg_title3;
                            if(!empty($replymoreimg_img3))
                            {
                                $info['replyimg_img']=$replymoreimg_img3;
                            }
                            $info['replyimg_url']=$replymoreimg_url3;
                        }
                        break;
                    case 3:
                        {
                            $info['replyimg_title']=$replymoreimg_title4;
                            if(!empty($replymoreimg_img4))
                            {
                                $info['replyimg_img']=$replymoreimg_img4;
                            }
                            $info['replyimg_url']=$replymoreimg_url4;
                        }
                        break;
                    
                    default:
                        # code...
                        break;
                }
                $id=M("wxs_replyimg")->where($where)->save($info);
                $count++;
            }
        }

        //活动回复
        if($reply_type==4){
            $user_activity_id=trim(I("user_activity_id"));
            $replyactivity_title=trim(I("replyactivity_title"));
            $replyactivity_description=trim(I("replyactivity_description"));
            $replyactivity_contact=trim(I("replyactivity_contact"));
            if(empty($user_activity_id)){
                $msg="请选择活动！";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
            }
            if($_FILES['replyactivity_img']['name']==null || $_FILES['replyactivity_img']['name']==""){
                $icense_img = "";
            }else {
                $fileinfo = $this->business_licence_upload(C('PROXY_UPLOAD_DIR'));
                if($fileinfo['replyactivity_img']){
                    $icense_img = substr(C('UPLOAD_DIR').$fileinfo['replyactivity_img']['savepath'].$fileinfo['replyactivity_img']['savename'],1,strlen(C('UPLOAD_DIR').$fileinfo['replyactivity_img']['savepath'].$fileinfo['replyactivity_img']['savename'])-1);
                }else{
                    $msg = $this->business_licence_upload_Error['replyactivity_img'];
                    $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
                }
            }
            $data['user_activity_id']=$user_activity_id;
            $data['replyactivity_title']=$replyactivity_title;
            $data['replyactivity_description']=$replyactivity_description;
            $data['replyactivity_contact']=$replyactivity_contact;
            if($icense_img !=""){
                $data['replyactivity_img']=$icense_img;
            }
            $where=array();
            $where['replyactivity_id']=$hfid;
            $info=M("wxs_replyactivity")->where($where)->find();
            if(empty($info)){
                $msg="信息有误！1111";
                $this->ajaxReturn(array('msg'=>$msg,'status'=>$status,'data'=>$data));
            }
            $id=M('wxs_replyactivity')->where($where)->save($data);
        }
        if($id==0){
            $msg="编辑自定义活动失败！";
            M("wxs_reply")->rollback();
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }
        //自定回复表信息
        $where=array();
        $reply['reply_keyword']=$reply_keyword;
        $reply['reply_concern']=$reply_concern;
        $where['reply_id']=$reply_id;
        $info=M("wxs_reply")->where($where)->find();
        if($info['reply_keyword']==$reply_keyword && $info['reply_concern']==$reply_concern){
            $wr=1;
        }else{
            $wr=M("wxs_reply")->where($where)->save($reply);
        }
        if($wr){
            $msg="编辑自定义回复成功！";
            $status="success";
            M("wxs_reply")->commit();
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }else{
            $msg="编辑自定义回复失败！";
            M("wxs_reply")->rollback();
            $this->ajaxReturn(array("msg"=>$msg,"status"=>$status));
        }
    }
}
?>