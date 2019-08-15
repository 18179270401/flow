/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:12:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_wxs_auth
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_wxs_auth`;
CREATE TABLE `t_flow_wxs_auth` (
  `auth_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '授权id',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业id',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商id',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1.代理商,2企业）',
  `auth_status` tinyint(1) DEFAULT NULL COMMENT '是否绑定（1.已绑,2未绑）',
  `auth_nickname` varchar(50) DEFAULT NULL COMMENT '授权方昵称',
  `auth_headimg` varchar(255) DEFAULT NULL COMMENT '授权方头像',
  `auth_service_type` tinyint(1) DEFAULT NULL COMMENT '授权公众号类型（1.服务号，2.订阅号）',
  `auth_wxname` varchar(50) DEFAULT NULL COMMENT '授权方微信号',
  `auth_businesspay` tinyint(1) DEFAULT NULL COMMENT '是否开通微信支付功能(1.开通，2，未开通)',
  `auth_appid` varchar(50) DEFAULT NULL COMMENT '授权方appid',
  `auth_code` varchar(75) DEFAULT NULL COMMENT '授权码',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人id',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人id',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`auth_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_flow_wxs_auth
-- ----------------------------
