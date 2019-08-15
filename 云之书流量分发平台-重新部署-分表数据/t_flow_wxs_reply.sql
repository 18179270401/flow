/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:12:50
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_wxs_reply
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_wxs_reply`;
CREATE TABLE `t_flow_wxs_reply` (
  `reply_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '回复id',
  `reply_type` tinyint(1) DEFAULT NULL COMMENT '回复类型(1文字，2图文。3多图文。4活动))',
  `reply_keyword` varchar(50) DEFAULT NULL COMMENT '回复所需关键字',
  `reply_keywordid` bigint(18) DEFAULT NULL COMMENT '回复反馈id',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业id',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商id',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1.代理商，2.企业）',
  `reply_concern` tinyint(1) DEFAULT NULL COMMENT '关注回复（1.是，2.否）',
  PRIMARY KEY (`reply_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_flow_wxs_reply
-- ----------------------------
