/*
Navicat MySQL Data Transfer

Source Server         : 流量平台-开发数据库
Source Server Version : 50634
Source Host           : stflowdevout01.mysql.rds.aliyuncs.com:3306
Source Database       : samton_flow_basic

Target Server Type    : MYSQL
Target Server Version : 50634
File Encoding         : 65001

Date: 2017-02-20 11:07:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `t_flow_sys_menu`
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_menu`;
CREATE TABLE `t_flow_sys_menu` (
  `menu_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '菜单ID',
  `menu_name` varchar(50) NOT NULL COMMENT '菜单名称',
  `sys_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '菜单所属(1：尚通，2：代理商，3：企业)',
  `top_menu_id` bigint(18) DEFAULT NULL COMMENT '上级菜单',
  `menu_type` tinyint(1) DEFAULT '1' COMMENT '菜单类型(1：分级菜单，2：功能菜单)',
  `page_url` varchar(200) DEFAULT NULL COMMENT '页面URL',
  `icon_path` varchar(200) DEFAULT NULL COMMENT '图标',
  `order_num` int(4) DEFAULT '1' COMMENT '排序号',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态（0：已禁用，1：正常，2：已删除）',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `group_name` varchar(255) DEFAULT NULL COMMENT '分组名称',
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人ID',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) NOT NULL COMMENT '修改人ID',
  `modify_date` datetime NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=241 DEFAULT CHARSET=utf8 COMMENT='系统菜单表';

-- ----------------------------
-- Records of t_flow_sys_menu
-- ----------------------------
INSERT INTO `t_flow_sys_menu` VALUES ('1', '代理商管理', '1', '0', '1', '', 'agents_icon', '1', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-03-30 14:36:27');
INSERT INTO `t_flow_sys_menu` VALUES ('2', '代理商信息', '1', '1', '2', 'Proxy/index', '', '1', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-04-01 13:34:51');
INSERT INTO `t_flow_sys_menu` VALUES ('3', '企业管理', '1', '0', '1', '', 'busimanage_icon', '2', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-03-30 14:36:42');
INSERT INTO `t_flow_sys_menu` VALUES ('4', '企业信息', '1', '3', '2', 'Enterprise/index', '', '1', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-04-01 13:35:09');
INSERT INTO `t_flow_sys_menu` VALUES ('5', '资金管理', '1', '0', '1', '', 'agentsfunds_icon', '3', '1', null, null, '1', '2016-03-14 17:35:40', '1', '2016-03-14 17:35:40');
INSERT INTO `t_flow_sys_menu` VALUES ('6', '代理账户管理', '1', '5', '2', 'ProxyAccount/index', '', '1', '1', '', '代理资金管理', '1', '2016-03-14 17:35:40', '191', '2016-07-07 18:07:52');
INSERT INTO `t_flow_sys_menu` VALUES ('7', '代理提现管理', '1', '5', '2', 'ProxyWithdrawals/index', '', '4', '0', '', '代理资金管理', '1', '2016-03-14 17:35:40', '188', '2016-12-12 16:33:45');
INSERT INTO `t_flow_sys_menu` VALUES ('8', '企业账户管理', '1', '5', '2', 'EnterpriseAccount/index', '', '7', '1', '', '企业资金管理', '1', '2016-03-14 17:35:40', '188', '2016-07-07 18:33:03');
INSERT INTO `t_flow_sys_menu` VALUES ('9', '产品管理', '1', '0', '1', '', 'products_icon', '6', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-04-24 08:45:48');
INSERT INTO `t_flow_sys_menu` VALUES ('10', '流量包管理', '1', '9', '2', 'Flow/index', '', '6', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-04-13 10:21:02');
INSERT INTO `t_flow_sys_menu` VALUES ('11', '用户折扣管理', '1', '5', '2', 'Discount/index', '', '200', '1', '用户折扣管理', null, '1', '2016-03-14 17:35:40', '188', '2016-06-29 10:49:45');
INSERT INTO `t_flow_sys_menu` VALUES ('12', '通道产品', '1', '9', '2', 'ChannelProduct/index', '', '3', '1', null, null, '1', '2016-03-14 17:35:40', '1', '2016-03-14 17:35:40');
INSERT INTO `t_flow_sys_menu` VALUES ('13', '通道配置', '1', '97', '2', 'Channel/index', '', '1', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-04-21 14:53:05');
INSERT INTO `t_flow_sys_menu` VALUES ('14', '流量中心', '1', '0', '1', '', 'trafficcenter_icon', '5', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-03-17 17:00:42');
INSERT INTO `t_flow_sys_menu` VALUES ('15', '已完成充值记录', '1', '14', '2', 'RechargeRecord/index', '', '2', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-05-13 17:34:21');
INSERT INTO `t_flow_sys_menu` VALUES ('16', '统计报表', '1', '0', '1', '', 'finance_icon', '8', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-04-24 08:46:13');
INSERT INTO `t_flow_sys_menu` VALUES ('17', '代理返利记录', '1', '226', '2', 'RebateInfor/index', '', '50', '1', '', '代理账单', '1', '2016-03-14 17:35:40', '188', '2016-12-01 10:19:14');
INSERT INTO `t_flow_sys_menu` VALUES ('18', '代理充值明细表', '1', '16', '2', 'ProxyDetails/recharge_record', '', '2', '1', '', '代理报表', '1', '2016-03-14 17:35:40', '191', '2016-07-07 18:20:53');
INSERT INTO `t_flow_sys_menu` VALUES ('19', '代理提现明细表', '1', '16', '2', 'ProxyDetails/withdraw_record', '', '3', '0', '', '代理报表', '1', '2016-03-14 17:35:40', '188', '2016-12-12 16:33:42');
INSERT INTO `t_flow_sys_menu` VALUES ('20', '企业充值明细表', '1', '16', '2', 'EnterpriseDetails/recharge_record', '', '5', '1', '', '企业报表', '1', '2016-03-14 17:35:40', '191', '2016-07-07 18:22:02');
INSERT INTO `t_flow_sys_menu` VALUES ('21', '日志查询', '1', '0', '1', '', 'logquery_icon', '10', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-04-24 08:46:33');
INSERT INTO `t_flow_sys_menu` VALUES ('22', '操作日志', '1', '21', '2', 'Syslog/index', '', '1', '1', null, null, '1', '2016-03-14 17:35:40', '1', '2016-03-14 17:35:40');
INSERT INTO `t_flow_sys_menu` VALUES ('23', '登录日志', '1', '21', '2', 'Loginlog/index', '', '2', '1', null, null, '1', '2016-03-14 17:35:40', '1', '2016-03-14 17:35:40');
INSERT INTO `t_flow_sys_menu` VALUES ('24', '系统管理', '1', '0', '1', '', 'systemset_icon', '9', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-04-22 15:08:02');
INSERT INTO `t_flow_sys_menu` VALUES ('25', '用户管理', '1', '24', '2', 'User/index', '', '1', '1', null, null, '1', '2016-03-14 17:35:40', '1', '2016-03-14 17:35:40');
INSERT INTO `t_flow_sys_menu` VALUES ('26', '角色权限', '1', '24', '2', 'Role/index', '', '2', '1', null, null, '1', '2016-03-14 17:35:40', '1', '2016-03-14 17:35:40');
INSERT INTO `t_flow_sys_menu` VALUES ('27', '公告管理', '1', '24', '2', 'Sysnotices/index', '', '8', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-04-26 10:06:13');
INSERT INTO `t_flow_sys_menu` VALUES ('28', '部门管理', '1', '24', '2', 'Depart/index', '', '4', '1', null, null, '1', '2016-03-14 17:35:40', '1', '2016-03-14 17:35:40');
INSERT INTO `t_flow_sys_menu` VALUES ('29', '功能管理', '1', '24', '2', 'Right/index', '', '5', '1', null, null, '1', '2016-03-14 17:35:40', '1', '2016-03-14 17:35:40');
INSERT INTO `t_flow_sys_menu` VALUES ('30', '菜单管理', '1', '24', '2', 'Menu/index', '', '6', '1', null, null, '1', '2016-03-14 17:35:40', '1', '2016-03-14 17:35:40');
INSERT INTO `t_flow_sys_menu` VALUES ('34', '企业管理', '2', '0', '1', '', 'busimanage_icon', '2', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-03-30 15:53:47');
INSERT INTO `t_flow_sys_menu` VALUES ('35', '企业信息', '2', '34', '2', 'Enterprise/index', '', '1', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-04-01 16:30:22');
INSERT INTO `t_flow_sys_menu` VALUES ('36', '资金管理', '2', '0', '1', '', 'agentsfunds_icon', '3', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-03-18 14:22:55');
INSERT INTO `t_flow_sys_menu` VALUES ('37', '代理账户管理', '2', '36', '2', 'ProxyAccount/index', '', '1', '1', '', '代理商资金管理', '1', '2016-03-14 17:35:40', '188', '2016-07-19 23:07:40');
INSERT INTO `t_flow_sys_menu` VALUES ('38', '代理提现申请', '2', '36', '2', 'ProxyWithdrawals/index', '', '3', '0', '', '代理商资金管理', '1', '2016-03-14 17:35:40', '188', '2016-12-12 16:33:26');
INSERT INTO `t_flow_sys_menu` VALUES ('39', '企业账户管理', '2', '36', '2', 'EnterpriseAccount/index', '', '1', '1', '', '企业资金管理', '1', '2016-03-14 17:35:40', '188', '2016-07-19 23:14:24');
INSERT INTO `t_flow_sys_menu` VALUES ('40', '企业提现管理', '2', '36', '2', 'EnterpriseWithdrawals/index', '', '3', '0', '', '企业资金管理', '1', '2016-03-14 17:35:40', '188', '2016-12-12 16:33:20');
INSERT INTO `t_flow_sys_menu` VALUES ('41', '流量中心', '2', '0', '1', '', 'trafficcenter_icon', '4', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-03-18 14:23:48');
INSERT INTO `t_flow_sys_menu` VALUES ('42', '流量充值', '2', '41', '2', 'FlowRecharge/index', '', '1', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-03-19 10:44:39');
INSERT INTO `t_flow_sys_menu` VALUES ('43', '开发者中心', '2', '41', '2', 'ApiConfiguration/index', '', '5', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-05-18 17:50:58');
INSERT INTO `t_flow_sys_menu` VALUES ('44', '已完成充值记录', '2', '41', '2', 'RechargeRecord/index', '', '3', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-05-14 10:00:47');
INSERT INTO `t_flow_sys_menu` VALUES ('45', '折扣管理', '2', '0', '1', '', 'discount_icon', '5', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-03-30 15:54:33');
INSERT INTO `t_flow_sys_menu` VALUES ('46', '用户折扣管理', '2', '45', '2', 'Discount/index', '', '1', '1', '用户折扣管理', null, '1', '2016-03-14 17:35:40', '1', '2016-05-20 18:20:14');
INSERT INTO `t_flow_sys_menu` VALUES ('47', '现金流水记录	', '2', '0', '1', '', 'cashflowrecord_icon', '6', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-03-30 15:55:40');
INSERT INTO `t_flow_sys_menu` VALUES ('48', '未支付订单', '2', '47', '2', 'Orders/unpaid', '', '1', '0', null, null, '1', '2016-03-14 17:35:40', '188', '2016-07-28 18:19:10');
INSERT INTO `t_flow_sys_menu` VALUES ('49', '已完成订单', '2', '47', '2', 'Orders/completed', '', '2', '1', null, null, '1', '2016-03-14 17:35:40', '1', '2016-03-14 17:35:40');
INSERT INTO `t_flow_sys_menu` VALUES ('50', '已取消订单', '2', '47', '2', 'Orders/canceled', '', '3', '1', null, null, '1', '2016-03-14 17:35:40', '1', '2016-03-14 17:35:40');
INSERT INTO `t_flow_sys_menu` VALUES ('51', '现金收入记录', '2', '47', '2', 'CashRecord/income', '', '4', '1', null, null, '1', '2016-03-14 17:35:40', '1', '2016-03-14 17:35:40');
INSERT INTO `t_flow_sys_menu` VALUES ('52', '现金支出记录', '2', '47', '2', 'CashRecord/payout', '', '5', '1', null, null, '1', '2016-03-14 17:35:40', '1', '2016-03-14 17:35:40');
INSERT INTO `t_flow_sys_menu` VALUES ('53', '系统管理', '2', '0', '1', '', 'systemset_icon', '8', '1', null, null, '1', '2016-03-14 17:35:40', '1', '2016-03-14 17:35:40');
INSERT INTO `t_flow_sys_menu` VALUES ('54', '用户管理', '2', '53', '2', 'User/index', '', '1', '1', null, null, '1', '2016-03-14 17:35:40', '1', '2016-03-14 17:35:40');
INSERT INTO `t_flow_sys_menu` VALUES ('55', '企业设置', '2', '53', '2', 'Center/index', '', '2', '0', null, null, '1', '2016-03-14 17:35:40', '1', '2016-04-05 12:14:01');
INSERT INTO `t_flow_sys_menu` VALUES ('56', '账户中心', '3', '0', '1', '', 'cashcenter_icon', '1', '1', '', '', '1', '2016-03-14 17:35:40', '188', '2016-11-30 17:52:47');
INSERT INTO `t_flow_sys_menu` VALUES ('57', '账户管理', '3', '56', '2', 'EnterpriseAccount/index', '', '1', '1', null, null, '1', '2016-03-14 17:35:40', '1', '2016-03-14 17:35:40');
INSERT INTO `t_flow_sys_menu` VALUES ('58', '账户提现申请', '3', '56', '2', 'EnterpriseApply/index', '', '3', '0', '', null, '1', '2016-03-14 17:35:40', '188', '2016-12-12 16:33:49');
INSERT INTO `t_flow_sys_menu` VALUES ('59', '流量中心', '3', '0', '1', '', 'trafficcenter_icon', '2', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-03-18 14:26:36');
INSERT INTO `t_flow_sys_menu` VALUES ('60', '流量充值', '3', '59', '2', 'FlowRecharge/index', '', '1', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-05-14 11:07:53');
INSERT INTO `t_flow_sys_menu` VALUES ('61', '开发者中心', '3', '59', '2', 'ApiConfiguration/index', '', '5', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-05-18 17:45:16');
INSERT INTO `t_flow_sys_menu` VALUES ('62', '历史订单_备注', '3', '59', '2', 'RechargeRecord/index', '', '3', '0', '', null, '1', '2016-03-14 17:35:40', '1', '2016-05-12 19:36:47');
INSERT INTO `t_flow_sys_menu` VALUES ('63', '现金流水记录', '3', '0', '1', '', 'cashflowrecord_icon', '3', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-03-30 16:05:45');
INSERT INTO `t_flow_sys_menu` VALUES ('64', '现金收入记录', '3', '63', '2', 'CashRecord/income', '', '1', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-03-25 18:02:35');
INSERT INTO `t_flow_sys_menu` VALUES ('65', '现金支出记录', '3', '63', '2', 'CashRecord/payout', '', '2', '1', '', null, '1', '2016-03-14 17:35:40', '1', '2016-03-25 18:04:00');
INSERT INTO `t_flow_sys_menu` VALUES ('66', '系统设置', '3', '0', '1', '', 'systemset_icon', '4', '1', null, null, '1', '2016-03-14 17:35:40', '1', '2016-03-14 17:35:40');
INSERT INTO `t_flow_sys_menu` VALUES ('67', '企业设置', '3', '66', '2', 'Center/index', '', '1', '0', null, null, '1', '2016-03-14 17:35:40', '1', '2016-04-05 12:14:04');
INSERT INTO `t_flow_sys_menu` VALUES ('68', '角色权限', '2', '53', '2', 'Role/index', '', '3', '1', '', null, '1', '2016-03-15 20:41:52', '1', '2016-04-05 11:25:10');
INSERT INTO `t_flow_sys_menu` VALUES ('69', '功能管理', '2', '53', '2', 'Right/index', '', '4', '0', '', null, '1', '2016-03-15 20:44:47', '1', '2016-03-15 20:44:47');
INSERT INTO `t_flow_sys_menu` VALUES ('70', '部门管理', '2', '53', '2', 'Depart/index', '', '5', '1', '', null, '1', '2016-03-15 20:45:50', '1', '2016-03-15 20:45:50');
INSERT INTO `t_flow_sys_menu` VALUES ('71', '代理商权限分配', '1', '1', '2', 'proxy/set_proxy_user', '', '3', '1', '', null, '1', '2016-03-18 10:59:45', '1', '2016-04-01 20:13:00');
INSERT INTO `t_flow_sys_menu` VALUES ('73', '代理充值管理', '1', '5', '2', 'ProxyRecharge/index', '', '3', '1', '', '代理资金管理', '1', '2016-03-18 18:27:08', '191', '2016-07-07 18:08:43');
INSERT INTO `t_flow_sys_menu` VALUES ('74', '企业提现管理', '1', '5', '2', 'EnterpriseWithdrawals/index', '', '9', '0', '', '企业资金管理', '1', '2016-03-19 10:13:57', '188', '2016-12-12 16:33:34');
INSERT INTO `t_flow_sys_menu` VALUES ('75', '企业充值管理', '1', '5', '2', 'EnterpriseRecharge/index', '', '10', '0', '', '企业资金管理', '1', '2016-03-19 12:18:44', '188', '2016-07-15 10:02:27');
INSERT INTO `t_flow_sys_menu` VALUES ('76', '代理充值申请', '2', '36', '2', 'ProxyRecharge/index', '', '2', '1', '', '代理商资金管理', '1', '2016-03-19 18:33:12', '188', '2016-07-19 23:11:11');
INSERT INTO `t_flow_sys_menu` VALUES ('77', '企业充值管理', '2', '36', '2', 'EnterpriseRecharge/index', '', '2', '1', '', '企业资金管理', '1', '0000-00-00 00:00:00', '188', '2016-07-19 23:17:02');
INSERT INTO `t_flow_sys_menu` VALUES ('78', '企业权限分配', '2', '34', '2', 'Enterprise/set_enterprise_user', '', '2', '1', '', null, '1', '2016-03-21 09:30:03', '1', '2016-03-23 14:28:10');
INSERT INTO `t_flow_sys_menu` VALUES ('79', '账户充值申请', '3', '56', '2', 'EnterpriseRecharge/index', '', '2', '1', '', null, '1', '2016-03-22 14:23:03', '1', '2016-04-02 14:50:34');
INSERT INTO `t_flow_sys_menu` VALUES ('80', '代理商充值明细', '2', '36', '2', 'ProxyRechargeDetailed/index', '', '4', '2', '', null, '1', '2016-03-22 16:59:56', '1', '2016-03-22 16:59:56');
INSERT INTO `t_flow_sys_menu` VALUES ('81', '代理商提现明细', '2', '36', '2', 'ProxyWithdrawDetailed/index', '', '5', '2', '', null, '1', '2016-03-22 17:00:44', '1', '2016-03-22 17:00:44');
INSERT INTO `t_flow_sys_menu` VALUES ('82', '代理商审核', '1', '1', '2', 'Proxy/approve_index', '', '2', '1', '', null, '1', '2016-03-23 17:47:10', '1', '2016-04-01 13:33:41');
INSERT INTO `t_flow_sys_menu` VALUES ('83', '企业审核', '1', '3', '2', 'Enterprise/approve_index', '', '2', '1', '', null, '1', '2016-03-24 16:43:22', '1', '2016-03-24 16:43:22');
INSERT INTO `t_flow_sys_menu` VALUES ('85', '统计报表', '2', '0', '1', '', 'finance_icon', '7', '1', '统计报表', null, '1', '2016-03-25 20:47:58', '1', '2016-03-26 09:19:46');
INSERT INTO `t_flow_sys_menu` VALUES ('86', '代理返利记录', '2', '85', '2', 'RebateInfor/index', '', '1', '1', '代理返利记录', '', '1', '2016-03-25 20:52:10', '191', '2016-07-07 18:13:26');
INSERT INTO `t_flow_sys_menu` VALUES ('87', '企业提现明细表', '1', '16', '2', 'EnterpriseWithdrawDetails/widthdraw_record', '', '6', '0', '', '企业报表', '1', '2016-03-28 11:21:28', '188', '2016-12-12 16:33:38');
INSERT INTO `t_flow_sys_menu` VALUES ('88', '代理收支明细表', '1', '16', '2', 'ProxyDetails/all_record', '', '4', '1', '', '代理报表', '1', '2016-03-28 15:02:41', '188', '2016-11-25 09:46:13');
INSERT INTO `t_flow_sys_menu` VALUES ('90', '用户通道管理', '1', '97', '2', 'ChannelUser/index', '', '5', '1', '', null, '1', '2016-04-09 00:21:21', '1', '2016-04-13 10:21:39');
INSERT INTO `t_flow_sys_menu` VALUES ('91', '流量退款管理', '1', '14', '2', 'OrderRefund/index', '', '89', '1', '', null, '1', '2016-04-08 22:03:44', '1', '2016-04-08 22:03:44');
INSERT INTO `t_flow_sys_menu` VALUES ('92', '流量退款管理', '2', '41', '2', 'OrderRefund/index', '', '4', '1', '', null, '1', '2016-04-09 01:42:33', '1', '2016-05-18 17:47:40');
INSERT INTO `t_flow_sys_menu` VALUES ('93', '流量退款管理', '3', '59', '2', 'OrderRefund/index', '', '4', '1', '', null, '1', '2016-04-10 12:26:24', '1', '2016-05-18 17:45:03');
INSERT INTO `t_flow_sys_menu` VALUES ('94', '企业收支明细表', '1', '16', '2', 'EnterpriseRecord/index', '', '94', '1', '', '企业报表', '1', '2016-04-11 14:27:10', '188', '2016-11-25 14:59:21');
INSERT INTO `t_flow_sys_menu` VALUES ('97', '通道管理', '1', '0', '1', '', 'channel_icon', '7', '1', '', null, '1', '2016-04-21 14:41:08', '1', '2016-04-24 08:45:55');
INSERT INTO `t_flow_sys_menu` VALUES ('104', '通道折扣', '1', '97', '2', 'ChannelDiscount/index', '', '104', '1', '', null, '1', '2016-04-22 17:32:26', '1', '2016-04-22 17:32:26');
INSERT INTO `t_flow_sys_menu` VALUES ('106', '企业授信管理', '1', '5', '2', 'EnterpriseBorrow/index', '', '11', '0', '', '企业资金管理', '1', '2016-05-03 11:42:20', '1', '2017-01-05 11:08:10');
INSERT INTO `t_flow_sys_menu` VALUES ('107', '企业授信管理2', '1', '5', '2', 'EnterpriseBorrowManagement/index', '', '107', '0', '', '', '1', '2016-05-03 11:54:45', '1', '2017-01-05 11:08:06');
INSERT INTO `t_flow_sys_menu` VALUES ('108', '企业还款管理', '1', '5', '2', 'EnterprisePayBack/index', '', '108', '0', '', null, '1', '2016-05-04 20:19:41', '1', '2016-05-06 15:59:32');
INSERT INTO `t_flow_sys_menu` VALUES ('109', '流量充值测试', '1', '14', '2', 'FlowRechargetest/index', '', '109', '0', '', null, '1', '2016-05-05 17:07:02', '1', '2017-01-05 15:01:21');
INSERT INTO `t_flow_sys_menu` VALUES ('116', '营销场景', '3', '0', '1', '', 'trafficscene_icon', '116', '1', '流量场景菜单', null, '1', '2016-05-07 14:37:25', '1', '2016-05-25 10:15:36');
INSERT INTO `t_flow_sys_menu` VALUES ('117', '微信设置', '3', '116', '2', 'SceneBase/index', '', '117', '1', '流量场景-基础设置', '', '1', '2016-05-07 14:38:16', '192', '2016-07-14 17:48:43');
INSERT INTO `t_flow_sys_menu` VALUES ('118', '收款设置', '3', '116', '2', 'SceneAccount/index', '', '118', '1', '流量场景--收款设置', '流量充值', '1', '2016-05-07 14:39:05', '192', '2016-07-14 17:48:22');
INSERT INTO `t_flow_sys_menu` VALUES ('119', '活动管理', '3', '116', '2', 'SceneActivity/index', '', '119', '1', '流量场景，活动管理', '流量活动', '1', '2016-05-07 14:48:22', '192', '2016-07-14 17:46:19');
INSERT INTO `t_flow_sys_menu` VALUES ('120', '领取记录', '3', '116', '2', 'SceneRecord/index', '', '120', '1', '流量场景--领取记录', '流量活动', '1', '2016-05-07 14:49:27', '192', '2016-07-14 17:46:31');
INSERT INTO `t_flow_sys_menu` VALUES ('121', '未完成充值记录', '1', '14', '2', 'OrderPending/index', '', '1', '1', '', null, '1', '2016-05-11 22:14:04', '1', '2016-05-13 17:34:36');
INSERT INTO `t_flow_sys_menu` VALUES ('122', '历史订单_备注', '1', '14', '2', 'OrderHistory/index', '', '122', '0', '', null, '1', '2016-05-11 22:14:51', '1', '2016-05-12 19:00:40');
INSERT INTO `t_flow_sys_menu` VALUES ('123', '营销场景', '1', '0', '1', '', 'marketscena_icon', '123', '1', '', null, '1', '2016-05-12 09:28:48', '1', '2016-05-12 09:28:48');
INSERT INTO `t_flow_sys_menu` VALUES ('124', '场景管理', '1', '123', '2', 'Marketing/index', '', '124', '1', '', null, '1', '2016-05-12 09:29:33', '1', '2016-05-12 09:37:48');
INSERT INTO `t_flow_sys_menu` VALUES ('125', '流量消费统计', '2', '85', '2', 'FlowConsume/index', '', '5', '1', '流量消费统计', null, '1', '2016-05-12 10:51:08', '1', '2016-05-18 17:47:24');
INSERT INTO `t_flow_sys_menu` VALUES ('126', '未完成充值记录', '2', '41', '2', 'OrderPending/index', '', '2', '1', '', null, '1', '2016-05-12 16:12:41', '1', '2016-05-14 10:01:09');
INSERT INTO `t_flow_sys_menu` VALUES ('127', '历史订单_备份', '2', '41', '2', 'OrderHistory/index', '', '127', '0', '', null, '1', '2016-05-12 16:13:20', '1', '2016-05-12 19:00:51');
INSERT INTO `t_flow_sys_menu` VALUES ('128', '未完成充值记录', '3', '59', '2', 'OrderPending/index', '', '2', '1', '', null, '1', '2016-05-12 18:46:12', '1', '2016-05-14 11:08:09');
INSERT INTO `t_flow_sys_menu` VALUES ('129', '代理流量消费统计', '1', '16', '2', 'ProxyFlowConsume/index', '', '129', '1', '代理商流量消费统计', '代理报表', '1', '2016-05-13 10:11:58', '188', '2016-11-25 14:57:51');
INSERT INTO `t_flow_sys_menu` VALUES ('130', '已完成充值记录', '3', '59', '2', 'RechargeRecord/index', '', '3', '1', '', null, '1', '2016-05-13 17:41:15', '1', '2016-05-14 11:08:23');
INSERT INTO `t_flow_sys_menu` VALUES ('133', '号码黑名单', '1', '14', '2', 'MobileBlack/index', '', '133', '1', '', null, '1', '2016-05-18 16:25:46', '1', '2016-05-23 08:55:44');
INSERT INTO `t_flow_sys_menu` VALUES ('134', '领取记录', '1', '123', '2', 'SceneRecord/index', '', '134', '1', '', null, '1', '2016-05-19 15:14:46', '1', '2016-05-19 15:14:46');
INSERT INTO `t_flow_sys_menu` VALUES ('135', '流量购买记录', '1', '123', '2', 'PayOrderRecord/index', '', '135', '1', '', null, '1', '2016-05-23 16:36:05', '1', '2016-06-15 09:38:52');
INSERT INTO `t_flow_sys_menu` VALUES ('136', '流量购买记录', '3', '116', '2', 'PayOrderRecord/index', '', '136', '1', '', '流量充值', '1', '2016-05-23 16:37:17', '192', '2016-07-14 17:45:34');
INSERT INTO `t_flow_sys_menu` VALUES ('137', '营销场景', '2', '0', '1', '', 'marketscena_icon', '137', '1', '', null, '1', '2016-05-24 09:15:41', '1', '2016-05-24 09:16:19');
INSERT INTO `t_flow_sys_menu` VALUES ('138', '领取记录', '2', '137', '2', 'SceneRecord/index', '', '138', '1', '', null, '1', '2016-05-24 09:17:16', '1', '2016-05-24 09:17:16');
INSERT INTO `t_flow_sys_menu` VALUES ('139', '流量购买记录', '2', '137', '2', 'PayOrderRecord/index', '', '139', '1', '', null, '1', '2016-05-24 09:17:56', '1', '2016-06-15 09:38:37');
INSERT INTO `t_flow_sys_menu` VALUES ('143', '基础设置', '2', '137', '2', 'SceneBase/index', '', '137', '0', '', null, '1', '2016-05-26 16:57:38', '1', '2016-05-26 18:16:00');
INSERT INTO `t_flow_sys_menu` VALUES ('144', '收款设置', '2', '137', '2', 'SceneAccount/index', '', '137', '0', '', null, '1', '2016-05-26 17:18:16', '1', '2016-05-26 18:15:57');
INSERT INTO `t_flow_sys_menu` VALUES ('145', '活动管理', '2', '137', '2', 'SceneActivity/index', '', '137', '0', '', null, '1', '2016-05-26 17:19:06', '1', '2016-05-26 18:15:53');
INSERT INTO `t_flow_sys_menu` VALUES ('146', '系统设置', '2', '53', '2', 'UserSet/index', '', '146', '1', '', null, '1', '2016-05-30 17:59:57', '1', '2016-05-31 10:45:03');
INSERT INTO `t_flow_sys_menu` VALUES ('147', '企业收款设置', '2', '137', '2', 'CollectionSet/index', '', '127', '1', '', null, '1', '2016-05-31 08:55:19', '1', '2016-05-31 18:20:05');
INSERT INTO `t_flow_sys_menu` VALUES ('148', '系统管理', '3', '0', '1', '', 'systemset_icon', '115', '1', '', null, '1', '2016-05-31 10:39:44', '1', '2016-06-06 10:09:36');
INSERT INTO `t_flow_sys_menu` VALUES ('149', '系统设置', '3', '148', '2', 'UserSet/index', '', '149', '1', '', null, '1', '2016-05-31 10:40:17', '1', '2016-05-31 10:40:17');
INSERT INTO `t_flow_sys_menu` VALUES ('151', '开发者设置', '3', '116', '2', 'SceneDeveloper/index', '', '118', '1', '', '流量充值', '1', '2016-05-31 17:01:22', '192', '2016-07-21 18:14:09');
INSERT INTO `t_flow_sys_menu` VALUES ('155', '折扣设置', '3', '116', '2', 'PersonDiscount/index', '', '119', '1', '', '流量充值', '1', '2016-06-02 14:41:09', '192', '2016-07-14 17:46:56');
INSERT INTO `t_flow_sys_menu` VALUES ('156', '红包购买记录', '1', '123', '2', 'PayRedRecord/index', '', '156', '1', '', null, '1', '2016-06-12 14:41:04', '1', '2016-06-12 14:58:51');
INSERT INTO `t_flow_sys_menu` VALUES ('157', '红包购买记录', '3', '116', '2', 'PayRedRecord/index', '', '157', '1', '', '流量充值', '1', '2016-06-12 14:42:13', '192', '2016-07-14 17:45:45');
INSERT INTO `t_flow_sys_menu` VALUES ('158', '红包购买记录', '2', '137', '2', 'PayRedRecord/index', '', '158', '1', '', null, '1', '2016-06-15 09:34:30', '1', '2016-06-15 09:34:30');
INSERT INTO `t_flow_sys_menu` VALUES ('159', '账户收支明细', '2', '85', '2', 'ChangeAccount/index', '', '159', '0', '', null, '1', '2016-06-15 15:28:42', '188', '2016-06-21 19:26:03');
INSERT INTO `t_flow_sys_menu` VALUES ('160', '账户收支明细', '3', '63', '2', 'ChangeAccount/index', '', '160', '0', '', null, '1', '2016-06-15 15:29:22', '188', '2016-06-21 19:12:06');
INSERT INTO `t_flow_sys_menu` VALUES ('161', '签到设置', '3', '116', '2', 'FlowscoreBase/index', '', '161', '1', '', '积分管理', '1', '2016-06-16 16:03:05', '192', '2016-07-14 17:44:15');
INSERT INTO `t_flow_sys_menu` VALUES ('162', '兑换记录', '3', '116', '2', 'ExchangeRecord/index', '', '162', '1', '', '积分管理', '1', '2016-06-17 11:08:25', '192', '2016-07-14 17:43:54');
INSERT INTO `t_flow_sys_menu` VALUES ('166', '用户折扣变动记录', '1', '21', '2', 'DiscountRecord/index', '', '166', '1', '', null, '1', '2016-06-27 10:13:50', '1', '2016-06-27 10:13:50');
INSERT INTO `t_flow_sys_menu` VALUES ('167', '日志查询', '2', '0', '1', '', 'logquery_icon', '136', '1', '', null, '1', '2016-06-27 10:15:26', '1', '2016-06-27 10:19:41');
INSERT INTO `t_flow_sys_menu` VALUES ('168', '用户折扣变动记录', '2', '167', '2', 'DiscountRecord/index', '', '168', '1', '', null, '1', '2016-06-27 10:16:04', '1', '2016-06-27 10:16:04');
INSERT INTO `t_flow_sys_menu` VALUES ('169', '通道折扣变动记录', '1', '21', '2', 'ChannelDiscountRecord/index', '', '169', '1', '', null, '1', '2016-06-27 10:26:19', '1', '2016-06-27 10:26:19');
INSERT INTO `t_flow_sys_menu` VALUES ('176', '授权平台', '3', '116', '2', 'Authorize/index', '', '176', '1', '', '公众号服务', '1', '2016-07-08 09:07:33', '192', '2016-08-04 17:58:08');
INSERT INTO `t_flow_sys_menu` VALUES ('177', '自定义回复设置', '3', '116', '2', 'Customreply/index', '', '177', '1', '', '公众号服务', '1', '2016-07-08 09:09:54', '192', '2016-08-04 17:58:29');
INSERT INTO `t_flow_sys_menu` VALUES ('180', '券活动管理', '3', '116', '2', 'Flowticket/index', '', '180', '1', '', '流量券活动', '1', '2016-07-15 11:58:39', '192', '2016-08-04 18:15:40');
INSERT INTO `t_flow_sys_menu` VALUES ('181', '券兑换记录', '3', '116', '2', 'FlowTicketExchangeRecord/index', '', '181', '1', '', '流量券活动', '1', '2016-07-15 12:03:00', '192', '2016-08-04 18:15:42');
INSERT INTO `t_flow_sys_menu` VALUES ('183', '代理收入统计', '2', '85', '2', 'ProxyIncome/index', '', '2', '1', '', '', '1', '2016-07-22 10:08:01', '1', '2016-07-22 14:55:55');
INSERT INTO `t_flow_sys_menu` VALUES ('184', '操作日志', '2', '167', '2', 'Syslog/index', '', '1', '1', '', '', '1', '2016-07-22 14:34:48', '1', '2016-07-22 14:54:39');
INSERT INTO `t_flow_sys_menu` VALUES ('188', '代理授信管理', '1', '5', '2', 'ProxyBorrow/index', '', '188', '1', '', '代理资金管理', '1', '2016-08-01 17:11:34', '1', '2017-01-05 11:08:01');
INSERT INTO `t_flow_sys_menu` VALUES ('189', '代理还款管理', '1', '5', '2', 'ProxyPayBack/index', '', '189', '1', '', '代理资金管理', '1', '2016-08-02 11:12:13', '1', '2016-08-02 11:37:56');
INSERT INTO `t_flow_sys_menu` VALUES ('190', '历史充值记录', '1', '14', '2', 'RechargeRecord/index_history', '', '3', '1', '', '', '1', '2016-08-02 11:32:45', '188', '2016-10-31 21:41:50');
INSERT INTO `t_flow_sys_menu` VALUES ('191', '历史充值记录', '2', '41', '2', 'RechargeRecord/index_history', '', '3', '1', '', '', '1', '2016-08-02 14:51:52', '1', '2016-08-02 14:52:05');
INSERT INTO `t_flow_sys_menu` VALUES ('192', '历史充值记录', '3', '0', '1', '', '', '3', '0', '', '', '1', '2016-08-02 14:53:38', '1', '2016-08-02 14:55:32');
INSERT INTO `t_flow_sys_menu` VALUES ('193', '历史充值记录', '3', '59', '2', 'RechargeRecord/index_history', '', '3', '1', '', '', '1', '2016-08-02 14:55:48', '1', '2016-08-02 14:55:54');
INSERT INTO `t_flow_sys_menu` VALUES ('196', '充值来源管理', '3', '116', '2', 'PaySourcesRecord/index', '', '196', '1', '', '流量充值', '1', '2016-08-17 16:11:58', '1', '2016-08-17 16:11:58');
INSERT INTO `t_flow_sys_menu` VALUES ('204', '上游对账信息表', '1', '226', '2', 'Stat/up_account_info', '', '47', '1', '', '上游账单', '1', '2016-08-23 14:32:01', '1', '2016-08-23 14:32:01');
INSERT INTO `t_flow_sys_menu` VALUES ('205', '下游对账信息表', '1', '16', '2', 'Stat/down_account_info', '', '205', '2', '', '财务对账报表', '1', '2016-08-24 16:13:59', '1', '2016-12-01 14:20:27');
INSERT INTO `t_flow_sys_menu` VALUES ('206', '产品折扣管理', '1', '5', '2', 'ProductDiscount/index', '', '206', '1', '', '', '1', '2016-09-08 14:58:04', '1', '2016-09-08 14:59:02');
INSERT INTO `t_flow_sys_menu` VALUES ('207', '产品折扣管理', '2', '45', '2', 'ProductDiscount/index', '', '207', '1', '', '', '1', '2016-09-09 15:55:34', '1', '2016-09-09 15:55:34');
INSERT INTO `t_flow_sys_menu` VALUES ('208', '产品折扣变动记录', '1', '21', '2', 'ProductDiscountRecord/index', '', '208', '1', '', '', '1', '2016-09-09 17:14:13', '1', '2016-09-09 17:14:13');
INSERT INTO `t_flow_sys_menu` VALUES ('209', '产品折扣变动记录', '2', '167', '2', 'ProductDiscountRecord/index', '', '209', '1', '', '', '1', '2016-09-09 17:20:10', '1', '2016-09-09 17:20:10');
INSERT INTO `t_flow_sys_menu` VALUES ('221', '流量码生成管理', '3', '116', '2', 'Flowcode/index', '', '221', '1', '', '流量码活动', '1', '2016-11-14 16:16:18', '1', '2016-11-14 16:16:18');
INSERT INTO `t_flow_sys_menu` VALUES ('222', '流量码活动设置', '3', '116', '2', 'FlowcodeSet/index', '', '220', '1', '', '流量码活动', '1', '2016-11-14 16:19:54', '1', '2016-11-14 16:25:19');
INSERT INTO `t_flow_sys_menu` VALUES ('223', '流量码兑换记录', '3', '116', '2', 'FlowcodeRecord/index', '', '223', '1', '', '流量码活动', '1', '2016-11-14 16:22:11', '1', '2016-11-14 16:22:11');
INSERT INTO `t_flow_sys_menu` VALUES ('224', '用户折扣记录', '3', '56', '2', 'DiscountRecord/index_record', '', '224', '1', '', '', '1', '2016-11-23 14:05:27', '1', '2016-11-23 14:17:52');
INSERT INTO `t_flow_sys_menu` VALUES ('225', '产品折扣记录', '3', '56', '2', 'ProductDiscountRecord/index_record', '', '225', '1', '', '', '1', '2016-11-23 14:08:36', '1', '2016-11-23 14:18:00');
INSERT INTO `t_flow_sys_menu` VALUES ('226', '对账管理', '1', '0', '1', '', 'account_icon', '3', '1', '', '', '1', '2016-11-24 09:28:35', '188', '2016-12-01 13:54:51');
INSERT INTO `t_flow_sys_menu` VALUES ('227', '代理结算单', '1', '226', '2', 'ProxyStatements/index', '', '49', '1', '', '代理账单', '1', '2016-11-24 09:31:26', '1', '2017-01-07 16:35:12');
INSERT INTO `t_flow_sys_menu` VALUES ('228', '企业结算单', '1', '226', '2', 'EnterpriseStatements/index', '', '228', '1', '', '企业账单', '1', '2016-11-24 09:32:20', '1', '2016-11-24 09:32:20');
INSERT INTO `t_flow_sys_menu` VALUES ('229', '订单产品查询', '2', '41', '2', 'UserDiscount/index', '', '229', '1', '', '', '1', '2016-11-24 11:30:55', '1', '2016-11-24 11:30:55');
INSERT INTO `t_flow_sys_menu` VALUES ('230', '订单产品查询', '3', '59', '2', 'UserDiscount/index', '', '230', '1', '', '', '1', '2016-11-24 11:31:15', '1', '2016-11-24 11:31:15');
INSERT INTO `t_flow_sys_menu` VALUES ('231', '业务对账表', '3', '56', '2', 'EnterpriseStatements/index', '', '231', '1', '', '', '1', '2016-11-24 17:23:24', '1', '2016-11-24 17:23:24');
INSERT INTO `t_flow_sys_menu` VALUES ('232', '对账管理', '2', '0', '1', '', 'account_icon', '3', '1', '', '', '1', '2016-11-25 09:02:11', '188', '2016-12-01 13:54:39');
INSERT INTO `t_flow_sys_menu` VALUES ('233', '代理结算单', '2', '232', '2', 'ProxyStatements/index', '', '49', '1', '', '', '1', '2016-11-25 09:05:49', '1', '2017-01-07 16:34:19');
INSERT INTO `t_flow_sys_menu` VALUES ('234', '企业结算单', '2', '232', '2', 'EnterpriseStatements/index', '', '234', '1', '', '', '1', '2016-11-25 09:06:20', '1', '2016-11-25 09:06:20');
INSERT INTO `t_flow_sys_menu` VALUES ('235', '代理对账信息表', '1', '226', '2', 'Stat/down_account_info_proxy', '', '49', '1', '', '代理账单', '1', '2016-12-01 09:32:33', '1', '2017-01-07 16:35:54');
INSERT INTO `t_flow_sys_menu` VALUES ('236', '企业对账信息表', '1', '226', '2', 'Stat/down_account_info_enterprise', '', '236', '1', '', '企业账单', '1', '2016-12-01 09:33:36', '1', '2016-12-01 09:56:15');
INSERT INTO `t_flow_sys_menu` VALUES ('237', '上游结算单', '1', '226', '2', 'TopStatements/index', '', '237', '1', '', '上游账单', '1', '2016-08-23 14:32:01', '1', '2016-08-23 14:32:01');
INSERT INTO `t_flow_sys_menu` VALUES ('238', '系统设置', '1', '24', '2', 'System/index', '', '237', '1', '', '', '1', '2017-01-08 13:24:16', '1', '2017-01-10 19:46:43');
INSERT INTO `t_flow_sys_menu` VALUES ('239', '尚通资源管理', '1', '97', '2', 'Samtonchannel/index', '', '239', '1', '', '', '1', '2017-01-08 16:59:10', '1', '2017-01-08 16:59:10');
INSERT INTO `t_flow_sys_menu` VALUES ('240', '1', '1', '1', '2', '321312', '', '240', '0', '', '1', '1', '2017-01-09 15:38:39', '1', '2017-01-09 15:39:00');
INSERT INTO `t_flow_sys_menu` VALUES ('247', '顶级代理账单', '1', '226', '2', 'DownStatements/index', '', '48', '1', '', '下游结算单', '1', '2016-08-23 14:32:01', '1', '2016-08-23 14:32:01');


INSERT INTO `t_flow_sys_menu` VALUES ('110', '企业授信管理', '2', '36', '2', 'EnterpriseBorrow/index', '', '4', '1', '', '企业资金管理', '1', '2016-08-23 14:32:01', '1', '2016-08-23 14:32:01');
INSERT INTO `t_flow_sys_menu` VALUES ('111', '企业还款管理', '2', '36', '2', 'EnterprisePayBack/index', '', '111', '1', '', '企业资金管理', '1', '2016-08-23 14:32:01', '1', '2016-08-23 14:32:01');

INSERT INTO `t_flow_sys_menu` VALUES ('194', '代理授信管理', '2', '36', '2', 'ProxyBorrow/index', '', '194', '0', '', '代理商资金管理', '1', '2016-08-23 14:32:01', '1', '2016-08-23 14:32:01');
INSERT INTO `t_flow_sys_menu` VALUES ('195', '代理还款管理', '2', '36', '2', 'ProxyPayBack/index', '', '195', '0', '', '代理商资金管理', '1', '2016-08-23 14:32:01', '1', '2016-08-23 14:32:01');

INSERT INTO `t_flow_sys_menu` VALUES ('199', '企业利润汇总表', '1', '16', '2', 'Stat/company_profit', '', '199', '1', '', '用户报表', '1', '2016-08-23 14:32:01', '1', '2016-08-23 14:32:01');

INSERT INTO `t_flow_sys_menu` VALUES ('242', '企业授信管理', '3', '56', '2', 'EnterpriseBorrow/index', '', '242', '1', '', '企业资金管理', '1', '2016-08-23 14:32:01', '1', '2016-08-23 14:32:01');
INSERT INTO `t_flow_sys_menu` VALUES ('243', '企业还款管理', '3', '56', '2', 'EnterprisePayBack/index', '', '243', '1', '', '企业资金管理', '1', '2016-08-23 14:32:01', '1', '2016-08-23 14:32:01');

