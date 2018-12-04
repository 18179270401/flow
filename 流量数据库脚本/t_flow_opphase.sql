/*
Navicat MySQL Data Transfer

Source Server         : 流量平台-开发数据库
Source Server Version : 50634
Source Host           : stflowdevout01.mysql.rds.aliyuncs.com:3306
Source Database       : samton_flow_basic

Target Server Type    : MYSQL
Target Server Version : 50634
File Encoding         : 65001

Date: 2017-02-20 13:34:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `t_flow_opphase`
-- ----------------------------
DROP TABLE IF EXISTS `t_flow_opphase`;
CREATE TABLE `t_flow_opphase` (
  `id` int(11) NOT NULL,
  `phone_section` varchar(15) DEFAULT NULL COMMENT '号段',
  `operaid` varchar(5) DEFAULT NULL COMMENT '区ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='运营商解析表';

-- ----------------------------
-- Records of t_flow_opphase
-- ----------------------------
INSERT INTO `t_flow_opphase` VALUES ('1', '134', '1');
INSERT INTO `t_flow_opphase` VALUES ('2', '135', '1');
INSERT INTO `t_flow_opphase` VALUES ('3', '136', '1');
INSERT INTO `t_flow_opphase` VALUES ('4', '137', '1');
INSERT INTO `t_flow_opphase` VALUES ('5', '138', '1');
INSERT INTO `t_flow_opphase` VALUES ('6', '139', '1');
INSERT INTO `t_flow_opphase` VALUES ('7', '147', '1');
INSERT INTO `t_flow_opphase` VALUES ('8', '150', '1');
INSERT INTO `t_flow_opphase` VALUES ('9', '151', '1');
INSERT INTO `t_flow_opphase` VALUES ('10', '152', '1');
INSERT INTO `t_flow_opphase` VALUES ('11', '157', '1');
INSERT INTO `t_flow_opphase` VALUES ('12', '159', '1');
INSERT INTO `t_flow_opphase` VALUES ('13', '182', '1');
INSERT INTO `t_flow_opphase` VALUES ('14', '183', '1');
INSERT INTO `t_flow_opphase` VALUES ('15', '187', '1');
INSERT INTO `t_flow_opphase` VALUES ('16', '188', '1');
INSERT INTO `t_flow_opphase` VALUES ('17', '130', '2');
INSERT INTO `t_flow_opphase` VALUES ('18', '131', '2');
INSERT INTO `t_flow_opphase` VALUES ('19', '132', '2');
INSERT INTO `t_flow_opphase` VALUES ('20', '145', '2');
INSERT INTO `t_flow_opphase` VALUES ('22', '155', '2');
INSERT INTO `t_flow_opphase` VALUES ('23', '156', '2');
INSERT INTO `t_flow_opphase` VALUES ('24', '185', '2');
INSERT INTO `t_flow_opphase` VALUES ('25', '186', '2');
INSERT INTO `t_flow_opphase` VALUES ('26', '133', '3');
INSERT INTO `t_flow_opphase` VALUES ('27', '153', '3');
INSERT INTO `t_flow_opphase` VALUES ('28', '180', '3');
INSERT INTO `t_flow_opphase` VALUES ('29', '189', '3');
INSERT INTO `t_flow_opphase` VALUES ('30', '158', '1');
INSERT INTO `t_flow_opphase` VALUES ('31', '181', '3');
INSERT INTO `t_flow_opphase` VALUES ('32', '184', '1');
INSERT INTO `t_flow_opphase` VALUES ('33', '178', '1');
INSERT INTO `t_flow_opphase` VALUES ('34', '177', '3');
INSERT INTO `t_flow_opphase` VALUES ('40', '1700', '3');
INSERT INTO `t_flow_opphase` VALUES ('41', '1701', '3');
INSERT INTO `t_flow_opphase` VALUES ('42', '1702', '3');
INSERT INTO `t_flow_opphase` VALUES ('43', '1709', '2');
INSERT INTO `t_flow_opphase` VALUES ('44', '1708', '2');
INSERT INTO `t_flow_opphase` VALUES ('45', '1707', '2');
INSERT INTO `t_flow_opphase` VALUES ('46', '1705', '1');
INSERT INTO `t_flow_opphase` VALUES ('47', '1706', '1');
INSERT INTO `t_flow_opphase` VALUES ('48', '172', '1');
INSERT INTO `t_flow_opphase` VALUES ('49', '173', '3');
INSERT INTO `t_flow_opphase` VALUES ('50', '149', '3');
INSERT INTO `t_flow_opphase` VALUES ('51', '145', '2');
INSERT INTO `t_flow_opphase` VALUES ('52', '175', '2');
INSERT INTO `t_flow_opphase` VALUES ('54', '176', '2');
