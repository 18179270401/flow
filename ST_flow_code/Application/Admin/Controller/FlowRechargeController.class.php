<?php
namespace Admin\Controller;
use Think\Controller;
use \Think\Page;

/**
 * 流量充值
 */
class FlowRechargeController extends CommonController {

    public function index(){
        //读取所有运营商
    	$operator = D("ChannelProduct")->operatorall();
    	if($operator){
    		//读取省份
    		$province = D("ChannelProduct")->provinceall();
    		foreach($operator as $k=>$v){
    			$list[$k]['operator_id'] = $v['operator_id'];
    			$list[$k]['operator_name'] = $v['operator_name'];
    			//读取省份
    			$list[$k]['Province'] = $province;
    			//读取运营商下所属全国的产品包
    			$wherec['operator_id'] = $v['operator_id'];
    			$wherec['province_id'] = 1;
    			$wherec['status']      = 1;
    			$product = D("Product")->where($wherec)->order('base_price asc,product_id asc')->select();
    			$list[$k]['product'] = $product;
    		}
    	}else{
    		$list = '';
    	}
    	$this->assign('list',$list);
        //下载excel文件
        $this->assign('excel_url',gethostwithhttp().'/Public/ExcelDemo/mobile_view.xlsx');
    	$this->display();
    }
    
    /**
     * 上传手机号、验证手机号码和读取流量包的操作 
     */
    public function operation(){
        //上传excel列出手机号
        if($_POST['op_type']=='upfile'){
            if ($_FILES["file"] == null) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '文件上传失败'));
            }
            $filetype = array(
                'application/vnd.ms-excel',
				'application/octet-stream',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            );
            if (!in_array($_FILES["file"]['type'],$filetype)) {
                $this->ajaxReturn(array('status' => 'error', 'msg' => '请上传 Excel 文档！'));
            }
            $list = readExcel($_FILES["file"]["tmp_name"]);
            foreach ($list as $key => $value) {
                $mobile[] = $value[0];
            }
            $this->ajaxReturn(array('status' => 'success', 'msg' => '读取成功','mobile' => implode(' ',$mobile)));
        //验证手机号
        }else if($_POST['op_type']=='check_mobile'){
            if($_POST['mobileall']!=""){
            	$self_user_type = D('SysUser')->self_user_type(); //user表的
            	$self_proxy_id 	= D('SysUser')->self_proxy_id();
            	$self_enterprise_id 	= D('SysUser')->self_enterprise_id();
            	$pe_info = D('SysUser')->get_pe_info($self_user_type, $self_proxy_id, $self_enterprise_id);
            	$data[] = array('operator' => $pe_info['operator']);
                $mobileall = explode(" ",$_POST['mobileall']);
                foreach($mobileall as $v){
					$rs = CheckMobile($v);
					if($rs['status'] == 'success') {
						$data[] = $rs;
					}
                }
                $this->ajaxReturn(array('status' => 'success', 'info' => $data));
            }else{
                $this->ajaxReturn(array('status' => 'error', 'msg' => '请输入或上传需要校验的手机号！'));
            }
        }
    }
    
    /**
     * 根据运营商ID和省份ID获取相应销售产品
     */
    public function get_op_product() {
    	if(IS_POST) {
    		$data = array();
    		$post = I('post.');
    		$wherec['operator_id'] = $post['operator_id'];
    		$wherec['province_id'] = $post['province_id'];
    		$wherec['status']      = 1;
    		$product = D("Product")->where($wherec)->order('base_price asc,product_id asc')->select();
    		if(!empty($product)) {
    			$data = $product;
    		}
    		$this->ajaxReturn(array('status' => 'success', 'info' => $data));
    	} else {
    		$this->ajaxReturn(array('status' => 'error', 'msg' => '错误，非POST请求数据！'));
    	}
    }
    
    /**
     * 根据运营商ID和省份ID获取相应的折扣
     */
    public function get_p_discount() {
    	if(IS_POST) {
   			$dc = 1;
    		
   			$operator_id = I('post.operator_id');
   			$province_id = I('post.province_id');
   			$self_user_type = D('SysUser')->self_user_type(); //user表的
   			$arrtype = array(1=>0, 2=>1, 3=>2);
   			$cond1 = array(
   					'user_type'		=> $arrtype[$self_user_type], //折扣表的
   					'proxy_id'		=> intval(D('SysUser')->self_proxy_id()),
   					'enterprise_id'	=> intval(D('SysUser')->self_enterprise_id()),
   			);
   			
    		$arrdiscount = D('Discount')->discountall();
    		if(!empty($arrdiscount) && is_array($arrdiscount)) {
    			foreach ($arrdiscount as $k => $v) {
    				if($v['user_type']==$cond1['user_type'] && $v['proxy_id']==$cond1['proxy_id'] && $v['enterprise_id']==$cond1['enterprise_id'] 
    						&& $v['operator_id']==$operator_id && $v['province_id']==$province_id) {
    					$dc = $v['discount_number'];
    					break;
    				}
    			}
    		}
    		$data = array('dc' => $dc);
    		
    		$this->ajaxReturn(array('status' => 'success', 'info' => $data));
    	} else {
    		$this->ajaxReturn(array('status' => 'error', 'msg' => '错误，非POST请求数据！'));
    	}
    }

    /**
     * 检查并提交充值数据
     */
    public function commit_flow_recharge() {
    	if(IS_POST) {
    		$data = $pid = $arr_product = array();
    		$post = I('post.');
    
    		$mobile_content	= $post['mobile_content'];
    		$take_effect_time = $post['take_effect_time']; //生效时间  1立即 2下月
    		$productco1 	= $post['productco1'];
    		$productco2 	= $post['productco2'];
    		$productco3 	= $post['productco3'];
    
    		if(!in_array($take_effect_time, array(1,2))) {
    			$this->ajaxReturn(array('status' => 'error', 'msg' => '请勿提交非法生效时间！'));
    		}
    
    		!empty($productco1) && $pid[] = $productco1;
    		!empty($productco2) && $pid[] = $productco2;
    		!empty($productco3) && $pid[] = $productco3;
    		if(!empty($mobile_content)) {
    			$arr_mobile = explode(" ", $mobile_content);
    			if(!empty($arr_mobile) && is_array($arr_mobile)) {
					$arr_mobile_info = array();
    				foreach ($arr_mobile as $k4 => $v4) {
    					if(!isMobile2($v4)) {
    						$this->ajaxReturn(array('status' => 'error', 'msg' => '填的手机号码【'.$v4.'】格式错误！'));
    					} else {
                            //$arr_mobile_info[] = CheckMobile($v4);
                        }
    				}
    				
    				$cond = array(
    						'mobile'	=> array('in', $arr_mobile),
    				);
    				$arr_mobile_info = M('sys_mobile_dict')->where($cond)->select();
    				if(empty($arr_mobile_info) || !is_array($arr_mobile_info)) {
    					$this->ajaxReturn(array('status' => 'error', 'msg' => '填的手机号码并未经过合法性验证！'));
    				}
    			} else {
    				$this->ajaxReturn(array('status' => 'error', 'msg' => '填的手机号码格式错误！'));
    			}
    		} else {
    			$this->ajaxReturn(array('status' => 'error', 'msg' => '请填写欲充值的手机号码！'));
    		}
    
    		if(!empty($pid)) {
    			$cond = array(
    					'p.product_id'	=> array('in', $pid),
    					'p.status'		=> array('eq', 1),
    					'cp.status'		=> array('eq', 1),
    			);
    			$arr_productT = M('product p')
    			->join('left join '.C('DB_PREFIX').'channel_product cp on p.size=cp.size and p.operator_id=cp.operator_id and p.province_id=cp.province_id')
    			->where($cond)
    			->field('p.*,p.size as size2,cp.size')
    			->select();
    			if(!empty($arr_productT) && is_array($arr_productT)) {
    				$self_user_type = D('SysUser')->self_user_type(); //user表的
    				$arrtype = array(1=>0, 2=>1, 3=>2);
    				$cond1 = array(
    						'user_type'		=> $arrtype[$self_user_type], //API表的
    						'proxy_id'		=> intval(D('SysUser')->self_proxy_id()),
    						'enterprise_id'	=> intval(D('SysUser')->self_enterprise_id()),
    				);
    				$api_info = D('SysUser')->get_sys_api_by_id($cond1);
    				//write_debug_log(array(__METHOD__,'api_info==', $api_info));
    				if(empty($api_info) || !is_array($api_info)) {
    					write_error_log(array(__METHOD__.'：'.__LINE__, '请先进行接口配置', $cond1));
    					$this->ajaxReturn(array('status' => 'error', 'msg' => '请先进行接口配置！'));
    				}
    				$api_key = $api_info['api_key'];
    				/* $client_ip = get_client_ip2();
    				 if(!empty($api_info['api_callback_ip']) && '255.255.255.255'!=$api_info['api_callback_ip'] && !in_array($client_ip, explode(',', $api_info['api_callback_ip']))) {
    				 write_error_log(array(__METHOD__.'：'.__LINE__, '请使用您接口配置的IP地址进行充值'));
    				 $this->ajaxReturn(array('status' => 'error', 'msg' => '请使用您接口配置的IP地址进行充值！'));
    				} */
    				//print_r($arr_productT );
    				foreach ($arr_productT as $k => $v) {
    					$arr_product[$v['operator_id']] = $v;
    				}
    
    				$pe_info = D('SysUser')->get_pe_info($self_user_type, $cond1['proxy_id'], $cond1['enterprise_id']);
    				$cost_total = 0; //花费金额
    				foreach ($arr_mobile_info as $k3 => $v3) {
    					if(false === strpos($pe_info['operator'], $v3['operator_id'])) {
							write_error_log(array(__METHOD__.'：'.__LINE__,'此号码非三大运营商，拒绝充值：'.$v3['mobile']));
    						//$this->ajaxReturn(array('status' => 'error', 'msg' => '您不能给该运营商手机号充值流量：'.$v3['mobile']));
    					}else{
							$cost_total += $arr_product[$v3['operator_id']]['base_price'];
						}

    				}
    				$account_info = D('SysUser')->get_account_info($self_user_type, $cond1['proxy_id'], $cond1['enterprise_id']);
					/*
    				if($account_info['account_balance'] < $cost_total) {
    					//write_debug_log(array(__METHOD__,'您账户余额不足', $cost_total, $account_info, $cond1));
    					$this->ajaxReturn(array('status' => 'error', 'msg' => '您账户余额不足，请先充值余额！'));
    				}*/

					$count_ret = array(); //下单结果描述
					$count_succ = 0; //成功条数
					$count_fail = 0; //失败条数
    				foreach ($arr_mobile_info as $k2 => $v2) {
    					$post_data = array(
    							'account'			=> $api_info['api_account'],
    							'action'			=> 'Charge',
    							'phone'				=> $v2['mobile'],
    							'range'				=> (1 == $arr_product[$v2['operator_id']]['province_id']) ? 0 : 1,
    							'size'				=> $arr_product[$v2['operator_id']]['size'],
    							'timeStamp'			=> NOW_TIME,
    							'take_effect_time'	=> $take_effect_time,
    							'source_type'		=> 2,
    					);
    					$pre_str = "{$api_key}account={$post_data['account']}&action=Charge&phone={$post_data['phone']}&range={$post_data['range']}&size={$post_data['size']}&timeStamp={$post_data['timeStamp']}{$api_key}";
    					$post_data['sign'] = md5($pre_str);
    					$rt = https_request(C('COMMIT_URL'), $post_data);
    					//write_debug_log(array(__METHOD__,'curl充值返回结果==',$rt));
    					if(false !== $rt) {
    						$arr_rt = json_decode($rt, true);
                            if('0000' != $arr_rt['respCode']) {
                                ('' != $arr_rt['respMsg']) && $count_ret[] = $arr_rt['respMsg'];
                                $count_fail += 1;
    							write_error_log(array(__METHOD__.'：'.__LINE__, '订单提交失败，原因：'.$arr_rt['respMsg'].'，编码：'.$arr_rt['respCode'].'，提交地址：'.C('COMMIT_URL'), $post_data));
    						} else {
                                $count_succ += 1;
                            }
    					} else {
    						write_error_log(array(__METHOD__.'：'.__LINE__, 'CURL执行失败', $post_data));
    					}
    				}
                    if($count_fail > 0) {
                        $sts = 'error';
                        //$fx = array_count_values($count_ret);
                        //$fail_desc = '';
                        //foreach($fx as $k1 => $v1) {
                        //    $fail_desc .= $k1.'：'.$v1.' 条；';
                        //}
                        //$ress = "<br />流量充值已提交，结果如下：成功 {$count_succ} 条，<br />失败 {$count_fail} 条。失败原因如下：<br />{$fail_desc}";
                        //$ress = "<br />流量充值已提交，结果如下：成功 {$count_succ} 条，失败 {$count_fail} 条。";
						$ress = "流量充值已提交，部分用户号码状态异常，充值失败！";
                    } else {
                        $sts = 'success';
                        //$ress = "流量充值已提交成功，数量 {$count_succ} 条！";
						$ress = "流量充值已全部提交成功！";
                    }
    				$this->ajaxReturn(array('status' => $sts, 'info' => $ress, 'msg'=>$ress));
    			} else {
    				$this->ajaxReturn(array('status' => 'error', 'msg' => '您选择的流量包不存在或已禁用！'));
    			}
    		} else {
    			$this->ajaxReturn(array('status' => 'error', 'msg' => '请选择流量包！'));
    		}
    	} else {
    		$this->ajaxReturn(array('status' => 'error', 'msg' => '错误，非POST请求数据！'));
    	}
    }
    

}