/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:10:18
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_sys_user_order_set
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_user_order_set`;
CREATE TABLE `t_flow_sys_user_order_set` (
  `user_order_set_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '订单数据设置ID',
  `set_orle_id` varchar(255) DEFAULT NULL COMMENT '角色ID（多个用,号分开）',
  `set_user_id` varchar(255) DEFAULT NULL COMMENT '用户ID（多个用,号分开）',
  PRIMARY KEY (`user_order_set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_flow_sys_user_order_set
-- ----------------------------
