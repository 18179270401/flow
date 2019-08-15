/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:11:09
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_top_contract
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_top_contract`;
CREATE TABLE `t_flow_top_contract` (
  `top_contract_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '上游合同ID',
  `contract_code` varchar(100) DEFAULT NULL COMMENT '合同编号',
  `top_name` varchar(100) DEFAULT NULL COMMENT '上游名称',
  `contract_money` decimal(11,3) DEFAULT NULL,
  `effect_date` datetime DEFAULT NULL COMMENT '生效时间',
  `expire_date` datetime DEFAULT NULL COMMENT '到期时间',
  `contract_desc` text COMMENT '条款说明',
  `enclosure` varchar(500) DEFAULT NULL COMMENT '附件',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `approve_status` tinyint(1) DEFAULT NULL COMMENT '审核状态（1：草稿、2：等待审核、3：初审通过、4：初审驳回、5：复审通过、6：复审驳回）',
  `last_approve_date` datetime DEFAULT NULL COMMENT '最后审核时间',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  `connect_type` tinyint(1) DEFAULT NULL COMMENT '连接类型：1,直连运营商；2，第三方公司',
  `discount` varchar(255) DEFAULT NULL,
  `quota` decimal(11,3) DEFAULT NULL,
  `is_enclosure` tinyint(1) DEFAULT NULL COMMENT '是否上传附件，1：是，0：否',
  `is_channel` tinyint(1) DEFAULT NULL COMMENT '是否设置通道账户，1：是，0：否',
  `is_discount` tinyint(1) DEFAULT NULL COMMENT '是否设置通道折扣，1：是，0：否',
  `sign_company` tinyint(1) DEFAULT NULL COMMENT '签约公司',
  PRIMARY KEY (`top_contract_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='上游合同信息表';

-- ----------------------------
-- Records of t_flow_top_contract
-- ----------------------------
