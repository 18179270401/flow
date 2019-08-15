/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:12:32
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_wxs_attention
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_wxs_attention`;
CREATE TABLE `t_flow_wxs_attention` (
  `attention_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '状态表id',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业id',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商id',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型(1.代理商，2企业)',
  `attention_new` bigint(18) DEFAULT NULL COMMENT '新增关注人数',
  `attention_cancel` bigint(18) DEFAULT NULL COMMENT '取消关注人数',
  `attention_grow` bigint(18) DEFAULT NULL COMMENT '净增关注人数',
  `attention_total` bigint(18) DEFAULT NULL COMMENT '累计关注人数',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人id',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人id',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`attention_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_flow_wxs_attention
-- ----------------------------
