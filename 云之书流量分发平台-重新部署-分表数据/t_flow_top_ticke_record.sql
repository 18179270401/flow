/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:11:33
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_top_ticke_record
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_top_ticke_record`;
CREATE TABLE `t_flow_top_ticke_record` (
  `record_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '记录ID',
  `ticke_id` bigint(18) DEFAULT NULL COMMENT '开票ID号',
  `ticket_money` decimal(11,3) DEFAULT NULL,
  `operater_before_money` decimal(11,3) DEFAULT NULL,
  `operater_after_money` decimal(11,3) DEFAULT NULL,
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='上游开票记录表';

-- ----------------------------
-- Records of t_flow_top_ticke_record
-- ----------------------------
