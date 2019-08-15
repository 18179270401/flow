/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:11:01
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_ticket_receive
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_ticket_receive`;
CREATE TABLE `t_flow_ticket_receive` (
  `flowticket_receive_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '领取Id',
  `redeem_code` varchar(50) DEFAULT NULL COMMENT '兑换码',
  `operator_id` bigint(18) DEFAULT NULL COMMENT '三大运营商',
  `product_id` bigint(18) DEFAULT NULL COMMENT '流量包Id',
  `mobile` varchar(15) DEFAULT NULL COMMENT '手机号',
  `receive_time` datetime DEFAULT NULL COMMENT '领取时间',
  `flowticket_status` tinyint(1) DEFAULT NULL COMMENT '流量券状态（已兑换、已过期、已失效）',
  `effective_duration` varchar(50) DEFAULT NULL COMMENT '流量券有效时长',
  `user_activity_id` bigint(18) DEFAULT NULL COMMENT '流量券活动Id',
  PRIMARY KEY (`flowticket_receive_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_flow_ticket_receive
-- ----------------------------
