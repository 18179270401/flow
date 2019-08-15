/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:11:25
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_top_ticke
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_top_ticke`;
CREATE TABLE `t_flow_top_ticke` (
  `ticke_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '开票ID号',
  `ticke_code` varchar(50) DEFAULT NULL COMMENT '发票申请单号（自动生成字母+年月日+4位排序号）',
  `top_name` varchar(150) DEFAULT NULL COMMENT '上游名称',
  `taxpayer_name` varchar(150) DEFAULT NULL COMMENT '纳税人名称',
  `taxpayer_number` varchar(150) DEFAULT NULL COMMENT '纳税人识别号',
  `ticket_money` decimal(16,3) DEFAULT NULL,
  `cumulative` decimal(16,3) DEFAULT NULL,
  `actual_ticket_money` decimal(16,3) DEFAULT NULL,
  `property` tinyint(1) DEFAULT NULL COMMENT '上游属性：1直连；2第三方',
  `is_record` tinyint(1) DEFAULT NULL COMMENT '是否录入开票信息：1是；0否',
  `charge_name` varchar(150) DEFAULT NULL COMMENT '负责人',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`ticke_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='上游开票信息表';

-- ----------------------------
-- Records of t_flow_top_ticke
-- ----------------------------
