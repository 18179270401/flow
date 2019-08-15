/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:12:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_wxs_replyactivity
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_wxs_replyactivity`;
CREATE TABLE `t_flow_wxs_replyactivity` (
  `replyactivity_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '回复id',
  `user_activity_id` bigint(18) DEFAULT NULL COMMENT '回复活动id',
  `replyactivity_title` varchar(100) DEFAULT NULL COMMENT '回复标题',
  `replyactivity_img` varchar(200) DEFAULT NULL COMMENT '回复图片',
  `replyactivity_description` text COMMENT '回复详细内容',
  `replyactivity_contact` text COMMENT '回复文字',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人id',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人id',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`replyactivity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_flow_wxs_replyactivity
-- ----------------------------
