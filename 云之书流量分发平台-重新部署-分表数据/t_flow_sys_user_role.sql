/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:10:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_sys_user_role
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_user_role`;
CREATE TABLE `t_flow_sys_user_role` (
  `user_id` bigint(18) NOT NULL COMMENT '用户ID',
  `role_id` bigint(18) NOT NULL COMMENT '角色ID',
  `create_user_id` bigint(18) NOT NULL COMMENT '创建人',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  KEY `FK_Reference_3` (`user_id`),
  KEY `FK_Reference_4` (`role_id`),
  KEY `ix_t_flow_sys_user_role_role_id` (`role_id`),
  KEY `ix_t_flow_sys_user_role_user_id` (`user_id`),
  CONSTRAINT `FK_Reference_3` FOREIGN KEY (`user_id`) REFERENCES `t_flow_sys_user` (`user_id`),
  CONSTRAINT `FK_Reference_4` FOREIGN KEY (`role_id`) REFERENCES `t_flow_sys_role` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户角色表';

-- ----------------------------
-- Records of t_flow_sys_user_role
-- ----------------------------
