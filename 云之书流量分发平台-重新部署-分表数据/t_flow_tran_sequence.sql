/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:11:41
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_tran_sequence
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_tran_sequence`;
CREATE TABLE `t_flow_tran_sequence` (
  `id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `tran_type` int(11) DEFAULT NULL COMMENT '交易类型',
  `current_seq` bigint(18) DEFAULT NULL COMMENT '当前序列值',
  `next_seq` bigint(18) DEFAULT '1' COMMENT '下一序列值',
  `get_count` int(11) DEFAULT NULL COMMENT '取值数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='事务提交序列表';

-- ----------------------------
-- Records of t_flow_tran_sequence
-- ----------------------------
