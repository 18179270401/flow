/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:12:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_wx_enterprise
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_wx_enterprise`;
CREATE TABLE `t_flow_wx_enterprise` (
  `flowscore_basic_id` bigint(18) NOT NULL AUTO_INCREMENT COMMENT '企业积分基础信息id',
  `flowscore_exchange_rate` int(10) DEFAULT NULL COMMENT '积分兑换比率',
  `daily_score` int(10) DEFAULT NULL COMMENT ' 没日签到积分',
  `flowscore_basic_logo` varchar(100) DEFAULT NULL COMMENT '每日签到logo图路径',
  `flowscore_basic_background` varchar(100) DEFAULT NULL COMMENT '每日签到背景图路径',
  `flowscore_basic_photo` varchar(100) DEFAULT NULL COMMENT '每日签到宣传图路径',
  `enterprise_id` bigint(18) DEFAULT NULL COMMENT '企业id',
  `proxy_id` bigint(18) DEFAULT NULL COMMENT '代理商id',
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型（1.代理商，2.企业）',
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人id',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人id',
  `modify_date` datetime DEFAULT NULL COMMENT '需改时间',
  `start_time` varchar(20) DEFAULT NULL COMMENT '开始时间',
  `end_time` varchar(20) DEFAULT NULL COMMENT '结束时间',
  `start_date` datetime DEFAULT NULL COMMENT '活动开始时间',
  `end_date` datetime DEFAULT NULL COMMENT '活动结束时间',
  PRIMARY KEY (`flowscore_basic_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_flow_wx_enterprise
-- ----------------------------
INSERT INTO `t_flow_wx_enterprise` VALUES ('1', null, null, '/Public/Uploads/./Enterprise_scene/2016-05-10/570312323eab9.png', '/Public/Uploads/./Enterprise_scene/2016-05-10/2323120aceab9.png', '/Public/Uploads/./Enterprise_scene/2016-05-10/2313130aceab9.png', '814', '0', '2', '1318', '2018-01-16 09:47:41', '1318', '2018-01-16 09:47:41', null, null, null, null);
INSERT INTO `t_flow_wx_enterprise` VALUES ('2', null, null, '/Public/Uploads/./Enterprise_scene/2016-05-10/570312323eab9.png', '/Public/Uploads/./Enterprise_scene/2016-05-10/2323120aceab9.png', '/Public/Uploads/./Enterprise_scene/2016-05-10/2313130aceab9.png', '0', '0', '-1', null, '2018-01-16 09:48:41', null, '2018-01-16 09:48:41', null, null, null, null);
INSERT INTO `t_flow_wx_enterprise` VALUES ('3', null, null, '/Public/Uploads/./Enterprise_scene/2016-05-10/570312323eab9.png', '/Public/Uploads/./Enterprise_scene/2016-05-10/2323120aceab9.png', '/Public/Uploads/./Enterprise_scene/2016-05-10/2313130aceab9.png', '813', '0', '2', '1317', '2018-03-16 13:43:11', '1317', '2018-03-16 13:43:11', null, null, null, null);
INSERT INTO `t_flow_wx_enterprise` VALUES ('4', null, null, '/Public/Uploads/./Enterprise_scene/2016-05-10/570312323eab9.png', '/Public/Uploads/./Enterprise_scene/2016-05-10/2323120aceab9.png', '/Public/Uploads/./Enterprise_scene/2016-05-10/2313130aceab9.png', '816', '0', '2', '1320', '2018-05-08 16:33:55', '1320', '2018-05-08 16:33:55', null, null, null, null);
INSERT INTO `t_flow_wx_enterprise` VALUES ('5', null, null, '/Public/Uploads/./Enterprise_scene/2016-05-10/570312323eab9.png', '/Public/Uploads/./Enterprise_scene/2016-05-10/2323120aceab9.png', '/Public/Uploads/./Enterprise_scene/2016-05-10/2313130aceab9.png', '834', '0', '2', '1339', '2018-07-05 10:58:03', '1339', '2018-07-05 10:58:03', null, null, null, null);
INSERT INTO `t_flow_wx_enterprise` VALUES ('6', null, null, '/Public/Uploads/./Enterprise_scene/2016-05-10/570312323eab9.png', '/Public/Uploads/./Enterprise_scene/2016-05-10/2323120aceab9.png', '/Public/Uploads/./Enterprise_scene/2016-05-10/2313130aceab9.png', '837', '0', '2', '1343', '2018-11-28 09:46:57', '1343', '2018-11-28 09:46:57', null, null, null, null);
