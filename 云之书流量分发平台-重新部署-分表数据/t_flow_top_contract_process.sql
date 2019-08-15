/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:11:17
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_top_contract_process
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_top_contract_process`;
CREATE TABLE `t_flow_top_contract_process` (
  `process_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '审核记录ID',
  `top_contract_id` bigint(18) NOT NULL COMMENT '上游合同ID',
  `approve_status` tinyint(1) DEFAULT NULL COMMENT '审核状态（1：审核通过、2：审核驳回）',
  `approve_user_id` bigint(18) DEFAULT NULL COMMENT '审核人ID',
  `approve_date` datetime DEFAULT NULL COMMENT '审核时间',
  `approve_remark` varchar(255) DEFAULT NULL COMMENT '审核说明',
  `approve_stage` tinyint(1) DEFAULT '1' COMMENT '审核阶段（1：初审、2：复审、3：打款）',
  PRIMARY KEY (`process_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='上游合同审核记录表';

-- ----------------------------
-- Records of t_flow_top_contract_process
-- ----------------------------
