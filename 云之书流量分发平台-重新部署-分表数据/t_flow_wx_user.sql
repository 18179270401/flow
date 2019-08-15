/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:12:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_wx_user
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_wx_user`;
CREATE TABLE `t_flow_wx_user` (
  `wx_user_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '用户微信id',
  `wx_openid` varchar(50) DEFAULT NULL COMMENT '微信用户openid',
  `wx_photo_url` varchar(200) DEFAULT NULL COMMENT '微信用户头像',
  `wx_name` varchar(50) DEFAULT NULL COMMENT '微信用户昵称',
  `user_flow_score` bigint(18) DEFAULT NULL COMMENT '用户积分',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业类型',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商id',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1.代理商，2.企业）',
  `last_flow_date` datetime DEFAULT NULL COMMENT '最后签到时间',
  `mobile` varchar(11) DEFAULT '-1' COMMENT '手机号',
  PRIMARY KEY (`wx_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_flow_wx_user
-- ----------------------------
