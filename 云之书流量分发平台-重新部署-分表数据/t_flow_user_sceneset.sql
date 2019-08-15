/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:11:56
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_user_sceneset
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_user_sceneset`;
CREATE TABLE `t_flow_user_sceneset` (
  `user_sceneid` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '设置ID',
  `user_set_id` bigint(18) DEFAULT NULL COMMENT '用户账户设置ID',
  `user_province_type` tinyint(1) DEFAULT NULL COMMENT '从折扣类型',
  `user_headpics` text COMMENT '图片轮播路径(json格式)',
  `follow_type` tinyint(1) DEFAULT NULL COMMENT '是否需要关注',
  PRIMARY KEY (`user_sceneid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信账号设置';

-- ----------------------------
-- Records of t_flow_user_sceneset
-- ----------------------------
