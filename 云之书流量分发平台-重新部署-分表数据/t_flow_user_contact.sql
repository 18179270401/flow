/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:11:49
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_user_contact
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_user_contact`;
CREATE TABLE `t_flow_user_contact` (
  `id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '联系人ID',
  `company_id` bigint(18) DEFAULT NULL COMMENT '代理商、企业的ID',
  `company_type` tinyint(1) DEFAULT NULL COMMENT '1、代理商、2、企业',
  `contact_name` varchar(50) DEFAULT NULL COMMENT '联系人姓名',
  `tel` varchar(13) DEFAULT NULL COMMENT '联系电话',
  `job` varchar(50) DEFAULT NULL COMMENT '职位',
  `qq` bigint(12) DEFAULT NULL COMMENT 'QQ号',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `remark` text COMMENT '备注',
  `status` tinyint(1) DEFAULT NULL COMMENT '启用禁用状态，0：已禁用、1：正常、2：已删除',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='企业、代理商联系人表';

-- ----------------------------
-- Records of t_flow_user_contact
-- ----------------------------
