/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:13:22
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_wxs_replytext
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_wxs_replytext`;
CREATE TABLE `t_flow_wxs_replytext` (
  `replytext_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '回复id',
  `replytext_contact` text COMMENT '回复文字',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人id',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人id',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`replytext_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_flow_wxs_replytext
-- ----------------------------
