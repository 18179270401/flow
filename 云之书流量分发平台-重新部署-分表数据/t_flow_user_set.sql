/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:12:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_user_set
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_user_set`;
CREATE TABLE `t_flow_user_set` (
  `account_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '账户ID',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1：代理商、2：企业）',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商ID',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业ID',
  `wx_appid` varchar(100) DEFAULT NULL COMMENT '公众号',
  `wx_appsecret` varchar(100) DEFAULT NULL COMMENT 'APPSECRET',
  `wx_mchid` varchar(100) DEFAULT NULL COMMENT '商户号',
  `wx_key` varchar(50) DEFAULT NULL COMMENT '商户号密钥',
  `wx_pem_file_one` varchar(100) DEFAULT NULL COMMENT 'pem文件1',
  `wx_pem_file_two` varchar(100) DEFAULT NULL COMMENT 'pem文件2',
  `alipay_partner` varchar(100) DEFAULT NULL COMMENT '支付宝账号',
  `alipay_key` varchar(100) DEFAULT NULL COMMENT '支付宝密钥',
  `alipay_pem_file` varchar(100) DEFAULT NULL COMMENT '支付宝pem文件',
  `alipay_pem_file_two` varchar(100) DEFAULT NULL,
  `app_appid` varchar(100) DEFAULT NULL,
  `app_appsecret` varchar(100) DEFAULT NULL,
  `app_mchid` varchar(100) DEFAULT NULL,
  `app_key` varchar(50) DEFAULT NULL,
  `paykey` varchar(50) DEFAULT NULL COMMENT '密钥key 用于支付宝退款',
  `app_pem_file_one` varchar(100) DEFAULT NULL,
  `app_pem_file_two` varchar(100) DEFAULT NULL,
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `payment_type` tinyint(1) DEFAULT '2' COMMENT '收款方式（1.运营方收款，2企业收款，3代理商收款）',
  `third_app_key` varchar(32) DEFAULT NULL,
  `third_app_code` varchar(32) DEFAULT NULL,
  `wx_name` varchar(100) DEFAULT NULL COMMENT '微信号',
  `wx_type` tinyint(1) DEFAULT '1' COMMENT '公众号类型：1.服务号 2.订阅号',
  `explanation` text COMMENT '充值说明',
  `consumer_phone` varchar(15) DEFAULT NULL COMMENT '客服电话',
  `pc_alipay_account` varchar(100) DEFAULT NULL COMMENT '网页支付宝账号',
  `pc_alipay_partner` varchar(100) DEFAULT NULL COMMENT '网页支付宝商户号',
  `pc_alipay_key` varchar(100) DEFAULT NULL COMMENT '网页支付宝密钥',
  `pc_explanation` text COMMENT '网页充值说明',
  `pc_notice` text COMMENT '网页公告',
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='用户设置信息表';

-- ----------------------------
-- Records of t_flow_user_set
-- ----------------------------
INSERT INTO `t_flow_user_set` VALUES ('1', null, null, '798', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '2', null, null, null, '1', null, null, null, null, null, null, null);
INSERT INTO `t_flow_user_set` VALUES ('2', '2', '0', '800', '', '', '', '', '', '', '', '', '', null, null, null, null, null, null, null, null, '1304', '2018-01-03 10:33:34', '1304', '2018-01-03 10:33:34', '2', null, null, null, '1', null, null, null, null, null, null, null);
INSERT INTO `t_flow_user_set` VALUES ('3', '2', '0', '809', '', '', '', '', '', '', '', '', '', null, null, null, null, null, null, null, null, '1313', '2018-01-11 14:20:45', '1313', '2018-01-11 14:20:45', '2', null, null, null, '1', null, null, null, null, null, null, null);
INSERT INTO `t_flow_user_set` VALUES ('4', '-1', '0', '0', '', '', '', '', '', '', '', '', '', null, null, null, null, null, null, null, null, null, '2018-01-11 14:21:45', null, '2018-01-11 14:21:45', '2', null, null, null, '1', null, null, null, null, null, null, null);
INSERT INTO `t_flow_user_set` VALUES ('5', '2', '0', '808', '', '', '', '', '', '', '', '', '', null, null, null, null, null, null, null, null, '1312', '2018-01-11 14:25:04', '1312', '2018-01-11 14:25:04', '2', null, null, null, '1', null, null, null, null, null, null, null);
INSERT INTO `t_flow_user_set` VALUES ('6', '2', '0', '814', '', '', '', '', '', '', '', '', '', null, null, null, null, null, null, null, null, '1318', '2018-01-16 09:47:11', '1318', '2018-01-16 09:47:11', '2', null, null, null, '1', null, null, null, null, null, null, null);
INSERT INTO `t_flow_user_set` VALUES ('7', '2', '0', '813', '', '', '', '', '', '', '', '', '', null, null, null, null, null, null, null, null, '1317', '2018-03-16 13:42:46', '1317', '2018-03-16 13:42:46', '2', null, null, null, '1', null, null, null, null, null, null, null);
INSERT INTO `t_flow_user_set` VALUES ('8', '2', '0', '827', '', '', '', '', '', '', '', '', '', null, null, null, null, null, null, null, null, '1331', '2018-03-24 14:01:52', '1331', '2018-03-24 14:01:52', '2', null, null, null, '1', null, null, null, null, null, null, null);
INSERT INTO `t_flow_user_set` VALUES ('9', '2', '0', '825', '', '', '', '', '', '', '', '', '', null, null, null, null, null, null, null, null, '1329', '2018-03-24 14:02:39', '1329', '2018-03-24 14:02:39', '2', null, null, null, '1', null, null, null, null, null, null, null);
INSERT INTO `t_flow_user_set` VALUES ('10', '2', '0', '816', '', '', '', '', '', '', '', '', '', null, null, null, null, null, null, null, null, '1320', '2018-05-08 16:33:50', '1320', '2018-05-08 16:33:50', '2', null, null, null, '1', null, null, null, null, null, null, null);
INSERT INTO `t_flow_user_set` VALUES ('11', '2', '0', '834', '', '', '', '', '', '', '', '', '', null, null, null, null, null, null, null, null, '1339', '2018-07-05 10:51:21', '1339', '2018-07-05 10:51:21', '2', null, null, null, '1', null, null, null, null, null, null, null);
INSERT INTO `t_flow_user_set` VALUES ('12', '2', '0', '837', '', '', '', '', '', '', '', '', '', null, null, null, null, null, null, null, null, '1343', '2018-11-28 09:46:18', '1343', '2018-11-28 09:46:18', '2', null, null, null, '1', null, null, null, null, null, null, null);
INSERT INTO `t_flow_user_set` VALUES ('13', '2', '0', '838', '', '', '', '', '', '', '', '', '', null, null, null, null, null, null, null, null, '1344', '2018-12-06 20:43:19', '1344', '2018-12-06 20:43:19', '2', null, null, null, '1', null, null, null, null, null, null, null);
