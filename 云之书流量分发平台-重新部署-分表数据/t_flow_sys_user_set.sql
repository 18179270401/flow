/*
Navicat MySQL Data Transfer

Source Server         : 云之书
Source Server Version : 50616
Source Host           : 47.92.93.60:3306
Source Database       : flow

Target Server Type    : MYSQL
Target Server Version : 50616
File Encoding         : 65001

Date: 2019-04-26 11:10:36
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_flow_sys_user_set
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_sys_user_set`;
CREATE TABLE `t_flow_sys_user_set` (
  `user_set_id` bigint(18) NOT NULL AUTO_INCREMENT,
  `user_type` tinyint(1) DEFAULT NULL COMMENT '用户类型(1-代理商，2-企业）',
  `proxy_id` bigint(18) DEFAULT NULL,
  `enterprise_id` bigint(18) DEFAULT NULL,
  `logo_img` varchar(150) DEFAULT NULL,
  `web_name` varchar(150) DEFAULT NULL,
  `create_user_id` bigint(18) DEFAULT NULL COMMENT '创建人ID',
  `create_date` datetime DEFAULT NULL COMMENT '创建时间',
  `modify_user_id` bigint(18) DEFAULT NULL COMMENT '修改人ID',
  `modify_date` datetime DEFAULT NULL COMMENT '修改时间',
  `is_sub_use` tinyint(1) DEFAULT '1' COMMENT '下级是否可见：1，可用；0，不可用',
  PRIMARY KEY (`user_set_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='用户系统设置信息表';

-- ----------------------------
-- Records of t_flow_sys_user_set
-- ----------------------------
INSERT INTO `t_flow_sys_user_set` VALUES ('1', '1', '182', '0', '/Public/Uploads/User_logo/2017-12-26/5a421ad905064.png', '云之书流量分发管理平台', '1299', '2017-12-26 17:48:09', null, null, '1');
INSERT INTO `t_flow_sys_user_set` VALUES ('2', '2', '0', '832', '/Public/Uploads/User_logo/2018-07-02/5b39d4c8b52bb.jpg', '云之书流量分发管理平台', '1337', '2018-07-02 15:31:20', null, null, '1');
INSERT INTO `t_flow_sys_user_set` VALUES ('3', '2', '0', '837', '/Public/Uploads/User_logo/2018-11-28/5bfdf65b2ce1e.png', '流量分发管理平台', '1343', '2018-11-28 09:45:49', '1343', '2018-11-28 09:58:54', '1');
INSERT INTO `t_flow_sys_user_set` VALUES ('4', '2', '0', '838', '/Public/Uploads/User_logo/2018-11-28/5bfdf610549b6.png', '流量分发管理平台', '1344', '2018-11-28 09:55:21', '1344', '2018-11-28 09:57:36', '1');
