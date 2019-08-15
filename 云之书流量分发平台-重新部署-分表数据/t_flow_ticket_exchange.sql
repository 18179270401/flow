/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:10:45
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_ticket_exchange
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_ticket_exchange`;
CREATE TABLE `t_flow_ticket_exchange` (
  `redeem_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '兑换Id',
  `redeem_code` varchar(50) DEFAULT NULL COMMENT '兑换码',
  `operator_id` bigint(18) DEFAULT NULL COMMENT '三大运营商',
  `order_id` varchar(50) DEFAULT NULL COMMENT '订单id',
  `product_id` bigint(18) DEFAULT NULL COMMENT '流量包Id',
  `mobile` varchar(15) DEFAULT NULL COMMENT '手机号',
  `exchange_time` datetime DEFAULT NULL COMMENT '兑换时间',
  `order_date` datetime DEFAULT NULL COMMENT '下单时间',
  `complete_time` datetime DEFAULT NULL COMMENT '下单完成时间',
  `order_status` tinyint(1) DEFAULT NULL COMMENT '充值状态',
  `user_activity_id` int(10) DEFAULT NULL COMMENT '流量券活动Id',
  `platform_openid` varchar(50) DEFAULT NULL COMMENT '平台用户id',
  `wx_photo` varchar(150) DEFAULT NULL COMMENT '微信头像',
  `wx_name` varchar(150) DEFAULT NULL COMMENT '微信昵称',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商id',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业id',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1.代理商,2企业）',
  PRIMARY KEY (`redeem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_flow_ticket_exchange
-- ----------------------------
