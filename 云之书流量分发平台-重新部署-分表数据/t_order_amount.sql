/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:13:29
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_order_amount
-- ----------------------------
DROP TABLE IF EXISTS `t_order_amount`;
CREATE TABLE `t_order_amount` (
  `order_id` bigint(20) DEFAULT NULL,
  `discount_price` decimal(11,3) DEFAULT NULL,
  `price` decimal(11,3) DEFAULT NULL,
  `dt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_order_amount
-- ----------------------------
